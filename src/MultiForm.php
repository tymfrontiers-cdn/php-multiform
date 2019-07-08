<?php
namespace TymFrontiers;
class MultiForm{
  use Helper\MySQLDatabaseObject,
      Helper\Pagination;

  protected static $_primary_key='id';
  protected static $_db_name;
  protected static $_table_name;
	protected static $_db_fields = [];

  protected $_author;
  protected $_updated;
  protected $_created;
  public $errors = [];

  function __construct(string $db_name, string $table_name, string $primary_key=''){
    $this->_init($db_name,$table_name);
    if( !empty($primary_key) ){
      self::$_primary_key = $primary_key;
      $this->$primary_key = null;
    }else{
      $this->_getPKey();
    }
  }

  private function _init(string $dbn, string $tbl){
    if( !$dbn OR !$tbl ){
      throw new \Exception("Database name and table name must be parsed to create class instance", 1);
      return false;
    }
    self::$_db_name = $dbn;
    self::$_table_name = $tbl;
    $this->_getDbFields();
  }
  protected function _getPKey(){
    if( empty(self::$_db_name) OR empty(self::$_table_name) ){
      throw new \Exception("Database/table not set", 1);
      return false;
    }
    global $db,$database;
    $db = ($db instanceof \TymFrontiers\MySQLDatabase) ? $db : (
      ($database instanceof \TymFrontiers\MySQLDatabase) ? $database : false
    );
    if( !$db  ){
      $this->errors['_getPKey'][] = [3,256,'There must be an instance of TymFrontiers\MySQLDatabase in the name of \'$db\' or \'$databse\' on global scope',__FILE__,__LINE__];
      return false;
    }
    $found = $db->fetchAssocArray($db->query("SHOW INDEX FROM `".self::$_db_name."`.`".self::$_table_name."` where Key_name = 'PRIMARY'"));
    if( !$found ){
      throw new \Exception("Unable to find Database > Table's primary key", 1);
      return false;
    }
    $key_name = $found['Column_name'];
    self::$_primary_key = $key_name;
    $this->$key_name = null;
    return $key_name;
  }
  public function create(){ return $this->_create(); }
  public function update(){ return $this->_update(); }
}
