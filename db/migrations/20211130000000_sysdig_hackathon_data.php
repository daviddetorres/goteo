<?php
/**
 * Migration Task class.
 */
class SysdigHackathonData
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
      // add the post-migration code here
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
      INSERT INTO user(id,name,location,email,password,gender,birthyear,entity_type,legal_entity,about,keywords,active,avatar,contribution,twitter,facebook,instagram,identica,linkedin,amount,num_patron,num_patron_active,worth,created,modified,token,rememberme,hide,confirmed,lang,node,num_invested,num_owned) VALUES('sysdig','Sysdig User',NULL,'david.detorres+sysdig@sysdig.com','$2a$12$TJ7qHi0tVYKYre4wxN.ZN.fJQC7KDwS3IHADKhrUHSoY5eMoeBJ0S',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2021-11-30 23:46:09','2021-11-30 22:46:09',X'3832343430303131396565323236353735633030643764363334393662333431','',0,0,'en','goteo',NULL,NULL);
     ";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     // add the post-migration code here
  }

}