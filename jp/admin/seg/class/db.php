<?php
class db{
  public static $conn;
  public static  function getConn() {
    if (self::$conn == NULL){
      $newConn=new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
      if(mysqli_connect_errno()!==0)
        {
          $msg=mysqli_connect_error();
          throw new DatabaseErrorException($msg);
        }
      $newConn->query("set names 'utf8'");
      self::$conn=$newConn;
    }
    return self::$conn;

  }
}
