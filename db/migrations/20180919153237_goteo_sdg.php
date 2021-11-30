<?php

use Goteo\Model\Sdg;
use Goteo\Model\Footprint;
use Goteo\Model\Category;
use Goteo\Model\SocialCommitment;
use Goteo\Model\Sphere;
use Goteo\Application\Config;

/**
 * Migration Task class.
 */
class GoteoSdg
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
    $seed = [];
    // add the post-migration code here
    $seed['es'] = [
      '1' => ['Dig deeper', 'Dig deeper', 'https://www.un.org/sustainabledevelopment/es/poverty/'],
      '2' => ['Love your customers', 'Love your customers', 'https://www.un.org/sustainabledevelopment/es/hunger/'],
      '3' => ['Trust the team', 'Trust the team', 'https://www.un.org/sustainabledevelopment/es/health/']
    ];
    $seed['ca'] = [
      '1' => ['Dig deeper', 'Dig deeper', 'https://www.un.org/sustainabledevelopment/es/poverty/'],
      '2' => ['Love your customers', 'Love your customers', 'https://www.un.org/sustainabledevelopment/es/hunger/'],
      '3' => ['Trust the team', 'Trust the team', 'https://www.un.org/sustainabledevelopment/es/health/']
    ];
    $seed['en'] = [
      '1' => ['Dig deeper', 'Dig deeper', 'https://www.un.org/sustainabledevelopment/es/poverty/'],
      '2' => ['Love your customers', 'Love your customers', 'https://www.un.org/sustainabledevelopment/es/hunger/'],
      '3' => ['Trust the team', 'Trust the team', 'https://www.un.org/sustainabledevelopment/es/health/']
    ];

    $sql_lang = Config::get('sql_lang');
    if(!$seed[$sql_lang]) throw new \RunException("[$sql_lang] not in the seed data!");

    foreach($seed[$sql_lang] as $id => $line) {
      $sdg = new Sdg(['id' => (int)$id, 'name' => $line[0], 'description' => $line[1], 'link' => $line[2] ? $line[2] : '']);
      $errors = [];
      if(!$sdg->save($errors)) {
        throw new \RuntimeException("Error saving main Sdg object [$id] " . implode("\n", $errors));
      }
      foreach($seed as $lang => $trans) {
        if($lang == $sql_lang) continue;
        $errors = [];
        $l = $trans[$id];
        if(!$sdg->setLang($lang, ['name' => $l[0], 'description' => $l[1], 'link' => $l[2] ? $l[2] : ''], $errors)) {
          throw new \RuntimeException("Error saving sdg translation [$lang] for id [{$sdg->id}] ({$l[0]})" . implode("\n", $errors));
        }
      }
    }

    // Build footprint sdg relationships
    $footprints = [];
    $footprints['es'] = [
      ['A/ Improve product', [1]],
      ['B/ Growth of the company', [2]],
      ['C/ Make Sysdig a better place', [3]],
    ];
    $footprints['ca'] = [
      ['A/ Improve product'],
      ['B/ Growth of the company'],
      ['C/ Make Sysdig a better place'],
    ];
    $footprints['en'] = [
      ['A/ Improve product'],
      ['B/ Growth of the company'],
      ['C/ Make Sysdig a better place'],
    ];
    if(!$footprints[$sql_lang]) throw new \RunException("[$sql_lang] not in the footprints data!");
    foreach($footprints[$sql_lang] as $id => $line) {
      $foot = new Footprint(['name' => $line[0]]);
      $errors = [];
      $fields = ['name', 'icon', 'description'];

      try {
          $foot->dbInsertUpdate($fields);
      }
      catch(\PDOException $e) {
          $errors[] = 'Error saving footprint: ' . $e->getMessage();
      }
      if(!empty($errors)) {
        throw new \RuntimeException("Error saving main Footprint object [$id] " . implode("\n", $errors));
      }
      if($line[1]) $foot->addSdgs($line[1]);

      foreach($footprints as $lang => $trans) {
        if($lang == $sql_lang) continue;
        $errors = [];
        $l = $trans[$id];
        if(!$foot->setLang($lang, ['name' => $l[0]], $errors)) {
          throw new \RuntimeException("Error saving footprint translation [$lang] for id [{$foot->id}] ({$l[0]})" . implode("\n", $errors));
        }
      }
    }

    // SocialCommitment (feed data if empty)
    $socials = [];
    $socials['es'] = [
      '1' => ['Have fun', 'Have fun', 3, [3]], 
      '2' => ['Open Source', 'Open Source', 1, [1]], 
      '3' => ['Improve the product', 'Improve the product', 1, [1]],
      '4' => ['Company culture', 'Company culture', 3, [3]], 
      '5' => ['Work/Life balance', 'Work/Life balance', 3, [3]], 
      '6' => ['Sales enablement', 'Sales enablement', 2, [2]] 
    ];
    $socials['ca'] = [
      '1' => ['Have fun', 'Have fun'], 
      '2' => ['Open Source', 'Open Source'], 
      '3' => ['Improve the product', 'Improve the product'],
      '4' => ['Company culture', 'Company culture'], 
      '5' => ['Work/Life balance', 'Work/Life balance'], 
      '6' => ['Sales enablement', 'Sales enablement'] 
    ];
    $socials['en'] = [
      '1' => ['Have fun', 'Have fun'], 
      '2' => ['Open Source', 'Open Source'], 
      '3' => ['Improve the product', 'Improve the product'],
      '4' => ['Company culture', 'Company culture'], 
      '5' => ['Work/Life balance', 'Work/Life balance'], 
      '6' => ['Sales enablement', 'Sales enablement'] 
    ];
    if(!$socials[$sql_lang]) throw new \RunException("[$sql_lang] not in the socials data!");
    foreach($socials[$sql_lang] as $id => $line) {
      if(!$sc = SocialCommitment::get($id)) {
        $sc = new SocialCommitment(['name' => $line[0], 'description' => $line[1]]);
        $errors = [];
        if(!$sc->save($errors)) {
          throw new \RuntimeException("Error saving main SocialCommitment object [{$sc->id}] " . implode("\n", $errors));
        }
      }
      foreach($socials as $lang => $trans) {
        if($lang == $sql_lang || $sc->getLang($lang)) continue;
        $errors = [];
        $l = $trans[$id];
        if(!$sc->setLang($lang, ['name' => $l[0], 'description' => $l[1]], $errors)) {
          throw new \RuntimeException("Error saving social_commitment translation [$lang] for id [{$sc->id}] ({$l[0]})" . implode("\n", $errors));
        }
      }
      if($line[2]) {
        if($cat = Category::get($line[2])) {
          $cat->social_commitment = $sc->id;
          $errors = [];
          if(!$cat->save($errors)) {
            throw new \RuntimeException("Error saving main Category object [{$cat->id}] " . implode("\n", $errors));
          }
        }
      }
      if($line[3]) $sc->addSdgs($line[3]);
    }

    // Spheres content
    $spheres = [];
    $spheres['es'] = [
    '1' => ['Cultura', [5]],
    '2' => ['Innovación', [15]],
    '3' => ['Salud'],
    '4' => ['Emprendimiento', [11]],
    '5' => ['Tecnología'],
    '6' => ['Ciudad', [2]],
    '7' => ['Cooperación', [4]],
    '8' => ['Género', [13]],
    '9' => ['Integración Social', [16]],
    '10' => ['Datos Abiertos', [6]],
    '11' => ['Periodismo'],
    '12' => ['Ecología', [8]],
    '13' => ['Infancia', [14]],
    '14' => ['Colaboración', [3]],
    '15' => ['Patrimonio', [17]],
    '16' => ['Digital', [7]],
    '17' => ['Educación', [10]],
    '18' => ['Emprendimiento social', [12]],
    '19' => ['Economías colaborativas', [9]],
    ];

    $spheres['en'] = [
    '1' => 'Culture',
    '2' => 'Innovation',
    '3' => 'Health',
    '4' => 'Entrepreneurship',
    '5' => 'Technology',
    '6' => 'City',
    '7' => 'Cooperation',
    '8' => 'Genre',
    '9' => 'Social inclusion',
    '10' => 'Open Data',
    '11' => 'Journalism',
    '12' => 'Environment',
    '13' => 'Childhood',
    '14' => 'Collaboration',
    '15' => 'Heritage',
    '16' => 'Digital',
    '17' => 'Education',
    '18' => 'Social entrepreneurship',
    '19' => 'Collaborative economies',
    ];
    $spheres['ca'] = [
    '1' => 'Cultura',
    '2' => 'Innovació',
    '3' => 'Salut',
    '4' => 'Emprenedoria',
    '5' => 'Tecnologia',
    '6' => 'Ciutat',
    '7' => 'Cooperació',
    '8' => 'Gènere',
    '9' => 'Integració social',
    '10' => 'Dades obertes',
    '11' => 'Periodisme',
    '12' => 'Ecologia',
    '13' => 'Infància',
    '14' => 'Col·laboració',
    '15' => 'Patrimoni',
    '16' => 'Digital',
    '17' => 'Educació',
    '18' => 'Emprenedoria social',
    '19' => 'Economies col·laboratives',
    ];

    if(!$spheres[$sql_lang]) throw new \RunException("[$sql_lang] not in the spheres data!");
    foreach($spheres[$sql_lang] as $id => $line) {
      if(!$sph = Sphere::get($id)) {
        $sph = new Sphere(['name' => $line[0]]);
        $errors = [];
        if(!$sph->save($errors)) {
          throw new \RuntimeException("Error saving main Sphere object [{$sph->id}] " . implode("\n", $errors));
        }
      }
      foreach($spheres as $lang => $trans) {
        if($lang == $sql_lang || $sph->getLang($lang)) continue;
        $errors = [];
        $l = $trans[$id];
        if(!$sph->setLang($lang, ['name' => $l], $errors)) {
          throw new \RuntimeException("Error saving translation [$lang] for id [{$sph->id}] ({$l})" . implode("\n", $errors));
        }
      }
      if($line[1]) $sph->addSdgs($line[1]);

    }
    $rel_categories = [
      
    ];

    foreach($rel_categories as $id => $sdgs) {
      if($cat = Category::get($id)) {
        $cat->addSdgs($sdgs);
      }
    }

    // TODO: create/import sphere (same as socialcommitment)


  }

  public function preDown()
  {
      // add the pre-migration code here
  }

  public function postDown()
  {
      // add the post-migration code here
  }

  /**
   * Return the SQL statements for the Up migration
   *
   * @return string The SQL string to execute for the Up migration.
   */
  public function getUpSQL()
  {
    return "
    UPDATE category SET social_commitment=NULL WHERE social_commitment='';
    UPDATE category SET social_commitment=NULL WHERE social_commitment NOT IN(SELECT id FROM social_commitment);

    ALTER TABLE `category` CHANGE `social_commitment` `social_commitment` INT(10) UNSIGNED NULL COMMENT 'Social commitment',
        DROP INDEX `id`,
        ADD FOREIGN KEY (`social_commitment`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE ON DELETE SET NULL;

    ALTER TABLE `social_commitment` CHANGE `image` `icon` CHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL;
    ALTER TABLE `sphere` CHANGE `image` `icon` CHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL;

    ALTER TABLE `call_sphere`
        DROP INDEX `call_sphere`,
        DROP INDEX `sphere`,
        ADD PRIMARY KEY (`call`, `sphere`),
        DROP FOREIGN KEY `call_sphere_ibfk_1`,
        DROP FOREIGN KEY `call_sphere_ibfk_2`;
    ALTER TABLE `call_sphere`
        CHANGE `call` `call_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
        CHANGE `sphere` `sphere_id` BIGINT(20) UNSIGNED NOT NULL,
        ADD FOREIGN KEY (`call_id`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        ADD FOREIGN KEY (`sphere_id`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

    ALTER TABLE `matcher_sphere`
        DROP INDEX `matcher`,
        DROP INDEX `sphere`,
        ADD PRIMARY KEY (`matcher`, `sphere`),
        DROP FOREIGN KEY `matcher_sphere_ibfk_1`,
        DROP FOREIGN KEY `matcher_sphere_ibfk_2`;
    ALTER TABLE `matcher_sphere`
        CHANGE `matcher` `matcher_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
        CHANGE `sphere` `sphere_id` BIGINT(20) UNSIGNED NOT NULL,
        ADD FOREIGN KEY (`matcher_id`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        ADD FOREIGN KEY (`sphere_id`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;


    CREATE TABLE `sdg` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `icon` varchar(255) NULL,
      `description` text NOT NULL,
      `link` varchar(255) NOT NULL DEFAULT '',
      `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    );

    CREATE TABLE `sdg_lang` (
      `id` int(10) unsigned NOT NULL,
      `lang` varchar(2) NOT NULL,
      `name` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `link` varchar(255) NOT NULL,
      `pending` tinyint(1) DEFAULT 0,
      PRIMARY KEY (`id`,`lang`),
      CONSTRAINT `sdg_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `sdg` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE `footprint` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `icon` varchar(255) NULL,
      `description` text NOT NULL,
      `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    );

    CREATE TABLE `footprint_lang` (
      `id` int(10) unsigned NOT NULL,
      `lang` varchar(2) NOT NULL,
      `name` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `pending` tinyint(1) DEFAULT 0,
      PRIMARY KEY (`id`,`lang`),
      CONSTRAINT `footprint_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `footprint` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE `sdg_category`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `category_id` INT(10) UNSIGNED NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      PRIMARY KEY (`sdg_id`, `category_id`),
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    );

    CREATE TABLE `sdg_social_commitment`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `social_commitment_id` INT(10) UNSIGNED NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      PRIMARY KEY (`sdg_id`, `social_commitment_id`),
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`social_commitment_id`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    );

    CREATE TABLE `sdg_sphere`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `sphere_id` BIGINT(20) UNSIGNED NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      PRIMARY KEY (`sdg_id`, `sphere_id`),
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`sphere_id`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    );

    CREATE TABLE `sdg_footprint`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `footprint_id` INT(10) UNSIGNED NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      PRIMARY KEY (`sdg_id`, `footprint_id`),
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`footprint_id`) REFERENCES `footprint`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    );

     "
     ;
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
     ALTER TABLE `category`
        CHANGE `social_commitment` `social_commitment` CHAR(50) NULL COMMENT 'Social commitment',
        DROP INDEX `social_commitment`, ADD UNIQUE INDEX `id` (`id`), DROP FOREIGN KEY `category_ibfk_1`;

     ALTER TABLE `social_commitment`
        CHANGE `icon` `image` CHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL;
     ALTER TABLE `sphere`
        CHANGE `icon` `image` CHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL;

     ALTER TABLE `matcher_sphere`
        CHANGE `matcher_id` `matcher` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
        CHANGE `sphere_id` `sphere` BIGINT(20) UNSIGNED NOT NULL,
        DROP PRIMARY KEY,
        DROP INDEX `sphere_id`,
        ADD UNIQUE INDEX `matcher` (`matcher`, `sphere`),
        ADD INDEX `sphere` (`sphere`),
        DROP FOREIGN KEY `matcher_sphere_ibfk_1`,
        DROP FOREIGN KEY `matcher_sphere_ibfk_2`;
     ALTER TABLE `matcher_sphere`
        ADD FOREIGN KEY (`matcher`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        ADD FOREIGN KEY (`sphere`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

     ALTER TABLE `call_sphere`
        CHANGE `call_id` `call` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
        CHANGE `sphere_id` `sphere` BIGINT(20) UNSIGNED NOT NULL,
        DROP PRIMARY KEY,
        DROP INDEX `sphere_id`,
        ADD UNIQUE INDEX `call_sphere` (`call`, `sphere`),
        ADD INDEX `sphere` (`sphere`),
        DROP FOREIGN KEY `call_sphere_ibfk_1`,
        DROP FOREIGN KEY `call_sphere_ibfk_2`;
     ALTER TABLE `call_sphere`
        ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        ADD FOREIGN KEY (`sphere`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

     DROP TABLE sdg_category;
     DROP TABLE sdg_social_commitment;
     DROP TABLE sdg_sphere;
     DROP TABLE sdg_footprint;
     DROP TABLE footprint_lang;
     DROP TABLE footprint;
     DROP TABLE sdg_lang;
     DROP TABLE sdg;
     ";
  }

}
