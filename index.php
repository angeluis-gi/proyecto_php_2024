<?php
session_start();
define('FPAG', 10); // Número de filas por página

require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/Usuario.php';
require_once 'app/models/AccesoDatosPDO.php';
require_once 'app/controllers/crudclientes.php';
require_once 'app/helpers/fpdf/fpdf.php';

//---- PAGINACIÓN ----
$midb = AccesoDatos::getModelo();
$totalfilas = $midb->numClientes();
if ($totalfilas % FPAG == 0) {
  $posfin = $totalfilas - FPAG;
} else {
  $posfin = $totalfilas - $totalfilas % FPAG;
}

if (!isset($_SESSION['posini'])) {
  $_SESSION['posini'] = 0;
}

$posAux = $_SESSION['posini'];

if (!isset($_SESSION['ordenacion'])) {
  $_SESSION['ordenacion'] = "id";
}
//------------

// Borro cualquier mensaje "
$_SESSION['msg'] = "";

ob_start(); // La salida se guarda en el bufer

//---- CONTROL ACCESO ----
// numero maximo de intentos de iniciar sesion: 3
if (!isset($_SESSION['contador_intentos'])) {
  $_SESSION['contador_intentos'] = 0;
}

// sin identificar
if (!isset($_SESSION['usuario'])) {
  if ($_SESSION['contador_intentos'] < 3) {
    if (!isset($_REQUEST['orden']) || $_REQUEST['orden'] != "Entrar") {
      include_once 'app/views/formulariocontraseña.php';
    } else if ($_REQUEST['orden'] == "Entrar") {
      if (isset($_REQUEST['login']) && isset($_REQUEST['password']) && comprobarLogin($_REQUEST['login'], $_REQUEST['password'])) {
        // anoto en la sesión
        $_SESSION['usuario'] = $_REQUEST['login'];
        $_SESSION['contador_intentos'] = 0;
        header("Refresh:0");
      } else {
        $_SESSION['contador_intentos']++;
        header("Refresh:0");
      }
    }
  } else {
    include_once 'app/views/bloq.php';
  }
} else {
  // identificado
  if ($_SERVER['REQUEST_METHOD'] == "GET") {

    // Proceso las ordenes de navegación
    if (isset($_GET['nav'])) {
      switch ($_GET['nav']) {
        case "Primero":
          $posAux = 0;
          break;
        case "Siguiente":
          $posAux += FPAG;
          if ($posAux > $posfin) $posAux = $posfin;
          break;
        case "Anterior":
          $posAux -= FPAG;
          if ($posAux < 0) $posAux = 0;
          break;
        case "Ultimo":
          $posAux = $posfin;
      }
      $_SESSION['posini'] = $posAux;
    }

    // Proceso de ordenes de CRUD clientes
    if (isset($_GET['orden'])) {
      switch ($_GET['orden']) {
        case "Nuevo":
          if (comprobarRol($_SESSION['usuario']) == 1) {
            crudAlta();
          } else {
            include_once 'app/views/funcionbloq.php';
          }
          break;
        case "Borrar":
          if (comprobarRol($_SESSION['usuario']) == 1) {
            crudBorrar($_GET['id']);
          } else {
            include_once 'app/views/funcionbloq.php';
          }
          break;
        case "Modificar":
          if (comprobarRol($_SESSION['usuario']) == 1) {
            crudModificar($_GET['id']);
          } else {
            include_once 'app/views/funcionbloq.php';
          }
          break;
        case "Detalles":
          crudDetalles($_GET['id']);
          break;
        case "Imprimir":
          crearPDF($_GET['id']);
          break;
      }
    }

    // Proceso las ordenes de navegación en detalles
    if (isset($_GET['nav-detalles']) && isset($_GET['id'])) {
      switch ($_GET['nav-detalles']) {
        case "Siguiente":
          crudDetallesSiguiente($_GET['id']);
          break;
        case "Anterior":
          crudDetallesAnterior($_GET['id']);
          break;
      }
    }

    // Proceso la orden de imprimir desde detalles
    if (isset($_GET['imprimir']) && isset($_GET['id'])) {
      crearPDF($_GET['id']);
    }
  }

  // POST Formulario de alta o de modificación
  else {
    if (isset($_POST['orden'])) {
      switch ($_POST['orden']) {
        case "Nuevo":
          crudPostAlta();
          break;
        case "Modificar":
          crudPostModificar();
          break;
        case "Terminar":
          crudTerminar();
          header("Refresh:0");
          break;
      }
    }

    // Proceso las ordenes de navegación en modificar
    if (isset($_POST['nav-modificar']) && isset($_POST['id'])) {
      switch ($_POST['nav-modificar']) {
        case "Siguiente >>":
          crudModificarSiguiente($_POST['id']);
          break;
        case "<< Anterior":
          crudModificarAnterior($_POST['id']);
          break;
      }
    }
  }

  // Si no hay nada en la buffer 
  // Cargo genero la vista con la lista por defecto
  if (ob_get_length() == 0) {
    $db = AccesoDatos::getModelo();
    $posini = $_SESSION['posini'];
    $ordenacion = isset($_GET['ordenacion']) ? $_GET['ordenacion'] : $_SESSION['ordenacion'];

    if (isset($_GET['ordenacion'])) {
      $_SESSION['ordenacion'] = $_GET['ordenacion'];
    }

    $tvalores = $db->getClientes($posini, FPAG, $_SESSION['ordenacion']);
    require_once "app/views/list.php";
  }
  $contenido = ob_get_clean();
  $msg = $_SESSION['msg'];
  // Muestro la página principal con el contenido generado
  require_once "app/views/principal.php";
}
