<?php

class conexion
{
  private $server;
  private $user;
  private $password;
  private $database;
  private $port;
  private $conexion;

  function __construct()
  {
    $listadoDatos = $this->datosConexion();
    foreach ($listadoDatos as $key => $value) {
      $this->server = $value['server'];
      $this->user = $value['user'];
      $this->password = $value['password'];
      $this->database = $value['database'];
      $this->port = $value['port'];
    }

    $this->conexion = new mysqli(
      $this->server,
      $this->user,
      $this->password,
      $this->database,
      $this->port
    );

    if ($this->conexion->connect_errno) {
      echo "algo va mal con la conexion";
      die();
    }
  }

  private function datosConexion()
  {
    $direccion = dirname(__FILE__);
    $jsonData = file_get_contents($direccion . "/" . "config");
    return json_decode($jsonData, true);
  }

  private function convertirUTF8($array)
  {
    array_walk_recursive($array, function (&$item, $key) {
      if (!mb_detect_encoding($item, 'utf-8', true)) {
        $item = utf8_encode($item);
      }
    });
    return $array;
  }

  public function obtenerDatos($query)
  {
    $results = $this->conexion->query($query);
    $resultArray = array();
    foreach ($results as $key) {
      $resultArray[] = $key;
    }
    return $this->convertirUTF8($resultArray);
  }

  // Metodo para guaradar, editar o eliminar
  public function nonQuery($sqlstr)
  {
    $results = $this->conexion->query($sqlstr);
    return $this->conexion->affected_rows;
  }

  // Metodo para guardar y tambien nos devuelve el ID; se va utilizar unicamente para los INSSERT
  public function nonQueryId($sqlstr)
  {
    $results = $this->conexion->query($sqlstr);
    $filas = $this->conexion->affected_rows;
    if ($filas >= 1) {
      return $this->conexion->insert_id;
    } else {
      return 0;
    }
  }

  //Encriptar 
  protected function encriptar($string)
  {
    return md5($string);
  }
}
