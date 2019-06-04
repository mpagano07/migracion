<?php
class Cola
{
  private $_data = array();
  
  public function encolar($element)
  {
    $this->_data[] = $element;
  }
  public function desencolar()
  {
    return array_shift($this->_data);
  }
  public function estaVacia()
  {
    return count($this->_data) == 0;
  }
}

class Persona
{
  private $nombre;
  private $dni;

  public function __construct($nombre, $dni)
  {
    $this->nombre = $nombre;
    $this->dni = $dni;
  }
  public function dameNombre()
  {
    return $this->nombre;
  }
  public function dameDNI()
  {
    return $this->dni;
  }
}

class DB
{
  private $db = array();
  public function insert($id, $obj)
  {
    $this->db[$id] = $obj;
  }
  public function delete($id)
  { }
  public function get($id)
  { }
  public function getAll()
  { }
}


class Cluster
{
  private $dbs = array();
  private $cola;

  public function __construct($cantidadDBs)
  {
    for ($i = 0; $i < $cantidadDBs; $i++) {
      $this->dbs[] = array();
    }
  }
  public function guardar(Persona $persona)
  {
    $a_donde = $persona->dameDNI() % count($this->dbs);
    $this->dbs[$a_donde][$persona->dameDNI()] = $persona;
  }
  public function borrar(Persona $persona)
  {
    $a_donde = $persona->dameDNI() % count($this->dbs);
    unset($this->dbs[$a_donde][$persona->dameDNI()]);
  }
  public function agregarDB()
  {
    $this->dbs[] = array();
    foreach ($this->dbs as $dbKey => $db) {
      foreach ($db->getAll() as $keyUsuario => $usuario) {
        $a_donde = $usuario->dameDNI() % count($this->dbs);
        if ($a_donde != $dbKey) {
          $this->cola->encolar($usuario);
        }
      }
    }
  }
  public function migrar()
  {
    while (!$this->cola->estaVacia()) {
      $usuario = $this->cola->desencolar();
      $viejoLugar = $usuario->dameDNI() % (count($this->dbs) - 1);
      $nuevoLugar = $usuario->dameDNI() % count($this->dbs);
      unset($this->dbs[$viejoLugar][$usuario->dameDNI()]);
      $this->dbs[$nuevoLugar][$usuario->dameDNI()] = $usuario;
    }
  }
  public function mostarResumen()
  {
    foreach ($this->dbs as $dbKey => $db) {
      echo "DB: $dbKey - Cantidad: " . count($db) . "\n";
    }
  }
}


$db = new Cluster(3, new Cola());
$db->guardar(new Persona("Pepe", 32));
$db->guardar(new Persona("Matias", 10));
$db->guardar(new Persona("Julian", 9));
$db->guardar(new Persona("Jose", 44));
$db->guardar(new Persona("Adrian", 55));
$db->guardar(new Persona("KP", 60));
$db->guardar(new Persona("Tomy", 70));
// $db->agregarDB();
$db->migrar();
$db->mostarResumen();
