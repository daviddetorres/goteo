CREATE TABLE `sponsor` (`id` SERIAL NOT NULL AUTO_INCREMENT ,`name` TINYTEXT NOT NULL ,`url` TINYTEXT NULL ,`image` INT( 10 ) NULL ,`order` INT( 11 ) NOT NULL DEFAULT '1',PRIMARY KEY ( `id` )) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Patrocinadores';