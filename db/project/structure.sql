CREATE TABLE IF NOT EXISTS `project` (  `id` varchar(50) NOT NULL,  `name` tinytext DEFAULT NULL,  `status` int(1) NOT NULL,  `progress` int(3) NOT NULL,  `owner` varchar(50) NOT NULL COMMENT 'usuario que lo ha creado',  `node` varchar(50) NOT NULL COMMENT 'nodo en el que se ha creado',  `amount` int(6) DEFAULT NULL COMMENT 'acumulado actualmente',  `days` int(3) NOT NULL DEFAULT '0' COMMENT 'Dias restantes',  `created` date DEFAULT NULL,  `updated` date DEFAULT NULL,  `published` date DEFAULT NULL,  `success` date DEFAULT NULL,  `closed` date DEFAULT NULL,  `contract_name` varchar(255) DEFAULT NULL,  `contract_nif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',  `phone` varchar(9) DEFAULT NULL COMMENT 'guardar sin espacios ni puntos',  `address` tinytext,  `zipcode` varchar(10) DEFAULT NULL,  `location` varchar(255) DEFAULT NULL,  `country` varchar(50) DEFAULT NULL,  `image` varchar(256) DEFAULT NULL,  `description` text,  `motivation` text,  `about` text,  `goal` text,  `related` text,  `category` varchar(50) DEFAULT NULL,  `keywords` tinytext COMMENT 'Separadas por comas',  `media` varchar(256) DEFAULT NULL,  `currently` int(1) DEFAULT NULL,  `project_location` varchar(256) DEFAULT NULL,  `scope` int(1) DEFAULT NULL,  `resource` text,  `comment` text COMMENT 'Comentario para los admin',  PRIMARY KEY (`id`),  KEY `owner` (`owner`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos de la plataforma';-- Los alter por si no se puede recrear la tablaALTER TABLE `project` ADD `image` VARCHAR( 256 ) NULL ,ADD `description` TEXT NULL ,ADD `motivation` TEXT NULL ,ADD `about` TEXT NULL ,ADD `goal` TEXT NULL ,ADD `related` TEXT NULL ,ADD `category` VARCHAR( 50 ) NULL ,ADD `media` VARCHAR( 256 ) NULL ,ADD `currently` INT( 1 ) NULL ,ADD `project_location` VARCHAR( 256 ) NULL ,ADD `resource` TEXT NULL ;ALTER TABLE `project` ADD `updated` DATE NULL AFTER `created` ;ALTER TABLE `project` ADD `keywords` TINYTEXT NULL COMMENT 'Separadas por comas' AFTER `category` ;ALTER TABLE `project` ADD `comment` TEXT NULL COMMENT 'Comentario para los admin';ALTER TABLE `project` ADD `days` INT( 3 ) NOT NULL DEFAULT '0' COMMENT 'Dias restantes' AFTER `amount` ;ALTER TABLE `project` CHANGE `name` `name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;ALTER TABLE `project`  DROP `contract_surname`,  DROP `contract_email`;  ALTER TABLE `project` ADD `scope` INT( 1 ) NULL COMMENT 'Ambito de alcance' AFTER `project_location` ;