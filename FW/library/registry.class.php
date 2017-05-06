<?php

class Registry{
  protected $_variables = array();
  protected static $_registry = null;
  
  public static function getInstance(){
    if (self::$_registry === null)
      self::$_registry = new Registry();
    return self::$_registry;
  }

  public static function get($index)
  {
    $instance = self::getInstance();
    if (array_key_exists($index,$instance->_variables))
      return $instance->_variables[$index];
    else 
      return null;
  }
  
  public static function set($index, $value){
    $instance = self::getInstance();
    $instance->_variables[$index] = $value;
  }
}
