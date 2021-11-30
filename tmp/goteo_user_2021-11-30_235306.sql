/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET SQL_NOTES=0 */;
DROP TABLE IF EXISTS user;
CREATE TABLE `user` (
  `id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `location` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` char(1) CHARACTER SET utf8 DEFAULT NULL,
  `birthyear` year(4) DEFAULT NULL,
  `entity_type` tinyint(1) DEFAULT NULL,
  `legal_entity` tinyint(1) DEFAULT NULL,
  `about` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contribution` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` tinytext CHARACTER SET utf8 DEFAULT NULL,
  `facebook` tinytext CHARACTER SET utf8 DEFAULT NULL,
  `instagram` tinytext CHARACTER SET utf8 DEFAULT NULL,
  `identica` tinytext CHARACTER SET utf8 DEFAULT NULL,
  `linkedin` tinytext CHARACTER SET utf8 DEFAULT NULL,
  `amount` int(7) DEFAULT NULL COMMENT 'Cantidad total aportada',
  `num_patron` int(10) unsigned DEFAULT NULL COMMENT 'Num. proyectos patronizados',
  `num_patron_active` int(10) unsigned DEFAULT NULL COMMENT 'Num. proyectos patronizados activos',
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token` tinytext CHARACTER SET utf8 NOT NULL,
  `rememberme` varchar(255) CHARACTER SET utf8 NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'No se ve publicamente',
  `confirmed` int(1) NOT NULL DEFAULT 0,
  `lang` varchar(2) CHARACTER SET utf8 DEFAULT 'es',
  `node` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `num_invested` int(10) unsigned DEFAULT NULL COMMENT 'Num. proyectos cofinanciados',
  `num_owned` int(10) unsigned DEFAULT NULL COMMENT 'Num. proyectos publicados',
  PRIMARY KEY (`id`),
  KEY `nodo` (`node`),
  KEY `coordenadas` (`location`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO user(id,name,location,email,password,gender,birthyear,entity_type,legal_entity,about,keywords,active,avatar,contribution,twitter,facebook,instagram,identica,linkedin,amount,num_patron,num_patron_active,worth,created,modified,token,rememberme,hide,confirmed,lang,node,num_invested,num_owned) VALUES('backer-1-passing-project','Backer 1 passing project',NULL,'backer-1-passing-project@example.org','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',NULL,NULL,NULL,NULL,X'4261636b657220312070617373696e672070726f6a656374',NULL,1,'0',X'6d7563686f2061727465',X'406f776e6572',X'666569736275632e636f6d',NULL,NULL,X'65696e3f',NULL,NULL,NULL,NULL,'2017-11-18 01:39:14','2017-11-18 01:39:14',X'','',0,0,'es',NULL,NULL,NULL),('backer-2-passing-project','Backer 2 passing project',NULL,'backer-2-passing-project@example.org','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',NULL,NULL,NULL,NULL,X'4261636b657220322070617373696e672070726f6a656374',NULL,1,'0',X'6d7563686f2061727465',X'406f776e6572',X'666569736275632e636f6d',NULL,NULL,X'65696e3f',NULL,NULL,NULL,NULL,'2017-11-18 01:39:14','2017-11-18 01:39:14',X'','',0,0,'es',NULL,NULL,NULL),('backer-3-passing-project','Backer 3 passing project',NULL,'backer-3-passing-project@example.org','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',NULL,NULL,NULL,NULL,X'4261636b657220332070617373696e672070726f6a656374',NULL,1,'0',X'6d7563686f2061727465',X'406f776e6572',X'666569736275632e636f6d',NULL,NULL,X'65696e3f',NULL,NULL,NULL,NULL,'2017-11-18 01:39:14','2017-11-18 01:39:14',X'','',0,0,'es',NULL,NULL,NULL),('backer-4-passing-project','Backer 4 passing project',NULL,'backer-4-passing-project@example.org','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',NULL,NULL,NULL,NULL,X'4261636b657220342070617373696e672070726f6a656374',NULL,1,'0',X'6d7563686f2061727465',X'406f776e6572',X'666569736275632e636f6d',NULL,NULL,X'65696e3f',NULL,NULL,NULL,NULL,'2017-11-18 01:39:14','2017-11-18 01:39:14',X'','',0,0,'es',NULL,NULL,NULL),('owner-project-passing','Owner project passing',NULL,'owner-project-passing@example.org','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',NULL,NULL,NULL,NULL,X'4f776e65722070726f6a6563742070617373696e67',NULL,1,'0',X'6d7563686f2061727465',X'406f776e6572',X'666569736275632e636f6d',NULL,NULL,X'65696e3f',NULL,NULL,NULL,NULL,'2017-11-18 01:39:14','2017-11-18 01:39:14',X'','',0,0,'es',NULL,NULL,NULL),('root','Sysadmin',NULL,'','dc76e9f0c0006e8f919e0c515c66dbba3982f785',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2017-11-18 01:39:13','2017-11-18 01:39:13',X'','',1,1,'en','goteo',NULL,NULL),('sysdig','Sysdig User',NULL,'david.detorres+sysdig@sysdig.com','$2a$12$TJ7qHi0tVYKYre4wxN.ZN.fJQC7KDwS3IHADKhrUHSoY5eMoeBJ0S',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2021-11-30 23:46:09','2021-11-30 22:46:09',X'3832343430303131396565323236353735633030643764363334393662333431','',0,0,'en','goteo',NULL,NULL);