<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería PDO *******************
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatos
{
  private static $modelo = null;
  private $dbh = null;

  public static function getModelo()
  {
    if (self::$modelo == null) {
      self::$modelo = new AccesoDatos();
    }
    return self::$modelo;
  }

  // Constructor privado  Patron singleton
  private function __construct()
  {
    try {
      $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DATABASE . ";charset=utf8";
      $this->dbh = new PDO($dsn, DB_USER, DB_PASSWD);
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error de conexión " . $e->getMessage();
      exit();
    }
  }

  // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
  public static function closeModelo()
  {
    if (self::$modelo != null) {
      $obj = self::$modelo;
      // Cierro la base de datos
      $obj->dbh = null;
      self::$modelo = null; // Borro el objeto.
    }
  }


  // Devuelvo cuantos filas tiene la tabla

  public function numClientes(): int
  {
    $result = $this->dbh->query("SELECT id FROM Clientes");
    $num = $result->rowCount();
    return $num;
  }


  // SELECT Devuelvo la lista de clientes
  public function getClientes($primero, $cuantos): array
  {
    $tuser = [];
    $ordenacion = $_SESSION['ordenacion'];
    // Crea la sentencia preparada
    // echo "<h1> $primero : $cuantos  </h1>";
    $stmt_usuarios  = $this->dbh->prepare("SELECT * FROM Clientes ORDER BY $ordenacion LIMIT $primero,$cuantos");
    // Si falla termina el programa
    $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');

    if ($stmt_usuarios->execute()) {
      while ($user = $stmt_usuarios->fetch()) {
        $tuser[] = $user;
      }
    }
    // Devuelvo el array de objetos
    return $tuser;
  }

  // SELECT Devuelvo un cliente o false
  public function getCliente(int $id)
  {
    $cli = false;
    $stmt_cli   = $this->dbh->prepare("SELECT * FROM Clientes WHERE id=:id");
    $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    $stmt_cli->bindParam(':id', $id);
    if ($stmt_cli->execute()) {
      if ($obj = $stmt_cli->fetch()) {
        $cli = $obj;
      }
    }
    return $cli;
  }

  public function getClienteSiguiente($id)
  {
    $cli = false;
    $ordenacion = $_SESSION["ordenacion"];
    // intentamos obtener el cliente siguiente
    $stmt_cli   = $this->dbh->prepare("SELECT * FROM Clientes WHERE $ordenacion > (SELECT $ordenacion FROM Clientes WHERE id = ?) ORDER BY $ordenacion ASC LIMIT 1");
    $stmt_cli->bindParam(1, $id);
    $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    if ($stmt_cli->execute()) {
      if ($obj = $stmt_cli->fetch()) {
        $cli = $obj;
      }
    }
    // si no se encuentra buscamos el primero
    if (!$cli) {
      $stmt_cli = $this->dbh->prepare("SELECT * FROM Clientes ORDER BY $ordenacion ASC LIMIT 1");
      $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
      if ($stmt_cli->execute()) {
        if ($obj = $stmt_cli->fetch()) {
          $cli = $obj;
        }
      }
    }
    return $cli;
  }

  public function getClienteAnterior($id)
  {
    $cli = false;
    $ordenacion = $_SESSION["ordenacion"];
    // intentamos obtener el cliente anterior
    $stmt_cli   = $this->dbh->prepare("SELECT * FROM Clientes WHERE $ordenacion < (SELECT $ordenacion FROM Clientes WHERE id = ?) ORDER BY $ordenacion DESC LIMIT 1");
    $stmt_cli->bindParam(1, $id);
    $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    if ($stmt_cli->execute()) {
      if ($obj = $stmt_cli->fetch()) {
        $cli = $obj;
      }
    }
    // si no se encuentra buscamos el ultimo
    if (!$cli) {
      $stmt_cli = $this->dbh->prepare("SELECT * FROM Clientes ORDER BY $ordenacion DESC LIMIT 1");
      $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
      if ($stmt_cli->execute()) {
        if ($obj = $stmt_cli->fetch()) {
          $cli = $obj;
        }
      }
    }
    return $cli;
  }

  // UPDATE TODO
  public function modCliente($cli): bool
  {
    $resu = false;

    $stmt_moduser = $this->dbh->prepare("UPDATE Clientes SET first_name=:first_name,last_name=:last_name,email=:email,gender=:gender, ip_address=:ip_address,telefono=:telefono WHERE id=:id");
    $stmt_moduser->bindValue(':first_name', $cli->first_name);
    $stmt_moduser->bindValue(':last_name', $cli->last_name);
    $stmt_moduser->bindValue(':email', $cli->email);
    $stmt_moduser->bindValue(':gender', $cli->gender);
    $stmt_moduser->bindValue(':ip_address', $cli->ip_address);
    $stmt_moduser->bindValue(':telefono', $cli->telefono);
    $stmt_moduser->bindValue(':id', $cli->id);
    $regexIP = "/^((25[0-5]|(2[0-4]|1\d|[1-9]|)\d)\.?\b){4}$/";
    $regexpTel = "/^\d{3}-\d{3}-\d{4}$/";

    if (self::comprobarCorreo($cli, "mod") && preg_match($regexIP, $cli->ip_address) && preg_match($regexpTel, $cli->telefono)) {
      $stmt_moduser->execute();
      $resu = ($stmt_moduser->rowCount() == 1);
    }
    return $resu;
  }

  //INSERT
  public function addCliente($cli): bool
  {
    $resu = false;
    // El id se define automáticamente por autoincremento.
    $stmt_crearcli  = $this->dbh->prepare(
      "INSERT INTO `Clientes`( `first_name`, `last_name`, `email`, `gender`, `ip_address`, `telefono`)" .
        "Values(?,?,?,?,?,?)"
    );
    $stmt_crearcli->bindValue(1, $cli->first_name);
    $stmt_crearcli->bindValue(2, $cli->last_name);
    $stmt_crearcli->bindValue(3, $cli->email);
    $stmt_crearcli->bindValue(4, $cli->gender);
    $stmt_crearcli->bindValue(5, $cli->ip_address);
    $stmt_crearcli->bindValue(6, $cli->telefono);
    $stmt_crearcli->execute();
    $resu = ($stmt_crearcli->rowCount() == 1);
    return $resu;
  }

  //Comprobacion del correo, IP y telefono
  public function comprobarCorreo($cli, $tipo): bool
  {
    switch ($tipo) {
      case 'mod':
        $stmt_chckemail = $this->dbh->prepare("SELECT * FROM Clientes WHERE email =:email AND NOT(id =:id)");
        $stmt_chckemail->bindValue(':id', $cli->id);
        break;
      case 'nuevo':
        $stmt_chckemail = $this->dbh->prepare("SELECT * FROM Clientes WHERE email =:email");
        break;
    }
    $stmt_chckemail->bindValue(':email', $cli->email);
    $stmt_chckemail->execute();
    if ($stmt_chckemail->rowCount() == 0) {
      return true;
    } else {
      return false;
    }
  }

  //Funcion para sacar el siguiente ID que será creado
  public function obtenerSiguienteID()
  {
    $consulta = $this->dbh->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'clientes' AND TABLE_NAME = 'Clientes'");
    $auto_increment = $consulta->fetchColumn();
    return $auto_increment;
  }

  //DELETE 
  public function borrarCliente(int $id): bool
  {
    $stmt_boruser   = $this->dbh->prepare("DELETE FROM Clientes WHERE id =:id");
    $stmt_boruser->bindValue(':id', $id);
    $stmt_boruser->execute();
    $resu = ($stmt_boruser->rowCount() == 1);
    return $resu;
  }

  // Evito que se pueda clonar el objeto. (SINGLETON)
  public function __clone()
  {
    trigger_error('La clonación no permitida', E_USER_ERROR);
  }



  // SELECT Devuelvo un usuario o false
  public function getUsuario($login)
  {
    $user = false;
    $stmt_user = $this->dbh->prepare("SELECT * FROM user WHERE login=:login");
    $stmt_user->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
    $stmt_user->bindParam(':login', $login);
    if ($stmt_user->execute()) {
      if ($obj = $stmt_user->fetch()) {
        $user = $obj;
      }
    }
    return $user;
  }
}