<?php

namespace Goteo\Core {

	use Goteo\Core\Error;

    abstract class Model {

        /**
         * Constructor.
         */
        public function __construct () {
            if (\func_num_args() >= 1) {
                $data = \func_get_arg(0);
                if (is_array($data) || is_object($data)) {
                    foreach ($data as $k => $v) {
                        $this->$k = $v;
                    }
                }
            }
        }

        /**
         * Obtener.
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto
         */
        abstract static public function get ($id);

        /**
		 * Guardar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         abstract public function save (&$errors = array());

        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        abstract public function validate (&$errors = array());

        /**
         * Consulta.
         * Devuelve un objeto de la clase PDOStatement
         * http://www.php.net/manual/es/class.pdostatement.php
         *
         * @param   type string $query      Consulta SQL
         * @param   type array  $params     ParÃ¡metros
         * $return  type object PDOStatement
         */
        public static function query ($query, $params = null) {

            static $db = null;

            if ($db === null) {
                $db = new DB;
            }

            $params = func_num_args() === 2 && is_array($params) ? $params : array_slice(func_get_args(), 1);

            // ojo que el stripslashes jode el contenido blob al grabar las imagenes
            if (\get_magic_quotes_gpc ()) {
                foreach ($params as $key => $value) {
                    if ($key != ':content') {
                        $params[$key] = \stripslashes(\stripslashes($value));
                    }
                }
            }

            $result = $db->prepare($query);

            try {

                $result->execute($params);

                return $result;

            } catch (\PDOException $e) {
                throw new Exception("Error PDO: " . \trace($e));
            }

        }

        /**
         * Devuelve el id autoincremental generado en la Ãºltima consulta, si se
         * ha generado uno.
         *
         * @return  int Id de `AUTO_INCREMENT` o `0`, si la Ãºltima consulta no
         *          ha generado ninguna valor autoincremental.
         */
        public static function insertId() {

            try {
                return static::query("SELECT LAST_INSERT_ID();")->fetchColumn();
            } catch (\Exception $e) {
                return 0;
            }
        }

		/**
		 * Formato.
		 * Formatea una cadena para ser usada como id varchar(50)
		 *
		 * @param string $value
		 * @return string $id
		 */
		public static function idealiza ($value) {
			$id = trim(strtolower($value));
			// Acentos
			$id = strtr($id, "ÁÀÄÂáàâäÉÈËÊéèêëÍÌÏÎíìîïÓÒÖÔóòôöÚÙÛÜúùûüÇçÑñ", "aaaaaaaaeeeeeeeeiiiiiiiioooooooouuuuuuuuccnn");
			// Separadores
			$id = preg_replace("/[\s\,\;\_\/\-]+/i", "-", $id);
			$id = preg_replace("/[^a-z0-9\.\-\+]/", "", $id);
			$id = substr($id, 0, 50);

			return $id;
		}

    }

}