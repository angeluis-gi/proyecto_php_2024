<?php

function crudBorrar($id)
{
  $db = AccesoDatos::getModelo();
  $resu = $db->borrarCliente($id);
  if ($resu) {
    // comprobar si existe y eliminar una imagen asociado al cliente mediante su id
    $rutajpg = 'app/uploads/' . str_pad($id, 8, "0", STR_PAD_LEFT) . '.jpg';
    $rutapng = 'app/uploads/' . str_pad($id, 8, "0", STR_PAD_LEFT) . '.png';
    if (file_exists($rutajpg)) {
      unlink($rutajpg);
    }
    if (file_exists($rutapng)) {
      unlink($rutapng);
    }
    $_SESSION['msg'] = " El usuario " . $id . " ha sido eliminado.";
  } else {
    $_SESSION['msg'] = " Error al eliminar el usuario " . $id . ".";
  }
}

function crudTerminar()
{
  AccesoDatos::closeModelo();
  session_destroy();
}

function crudAlta()
{
  $cli = new Cliente();
  $orden = "Nuevo";
  $extrainfo = '';
  $extraimg["form"] = '';
  include_once "app/views/formulario.php";
}

function crudDetalles($id)
{
  $db = AccesoDatos::getModelo();
  $cli = $db->getCliente($id);
  $imagen = obtenerImagen($cli);
  $bandera = obtenerBandera($cli);
  include_once "app/views/detalles.php";
}

function obtenerImagen($cli): array
{
  // formato de imagen: 00000XXX.jpg para el cliente con id XXX
  $img_id = str_pad($cli->id, 8, "0", STR_PAD_LEFT); // rellena el string a la izquierda con ceros hasta alcanzar 8 caracteres
  $rutajpg = "app/uploads/" . $img_id . ".jpg";
  $rutapng = "app/uploads/" . $img_id . ".png";
  if (!file_exists($rutajpg) && !file_exists($rutapng)) {
    $imagen = array(
      "url"  => 'https://robohash.org/' . $img_id . '.PNG',
      "msg"  => '<img src="https://robohash.org/' . $img_id . '" alt="imagen del usuario ' . $cli->id . '">',
      "form" => '<tr><td>imagen actual:</td><td><img src="https://robohash.org/' . $img_id . '" alt="imagen del usuario ' . $cli->id . '"></td></tr>'
    );
  } else {
    if (file_exists($rutajpg)) {
      $imagen = array(
        "url" => $rutajpg,
        "msg" => '<img src="' . $rutajpg . '" alt="imagen del usuario ' . $cli->id . '">',
        "form" => '<tr><td>imagen actual:</td><td><img src="' . $rutajpg . '" alt="imagen del usuario ' . $cli->id . '"></td></tr>'
      );
    } else if (file_exists($rutapng)) {
      $imagen = array(
        "url" => $rutapng,
        "msg" => '<img src="' . $rutapng . '" alt="imagen del usuario ' . $cli->id . '">',
        "form" => '<tr><td>imagen actual:</td><td><img src="' . $rutapng . '" alt="imagen del usuario ' . $cli->id . '"></td></tr>'
      );
    }
    
  }
  return $imagen;
}

function obtenerBandera($cli): array
{
  $loc = file_get_contents('http://ip-api.com/json/' . $cli->ip_address);
  $obj = json_decode($loc);
  if (property_exists($obj, "countryCode")) {
    $pais = strtolower($obj->countryCode);
    $bandera = array(
      "pais" => $pais,
      "url" => 'https://flagcdn.com/h120/' . $pais . '.jpg',
      "msg" => '<img src="https://flagcdn.com/h120/' . $pais . '.jpg" alt="' . $obj->country . '">'
    );
  } else {
    $bandera = array(
      "url" => 'https://th.bing.com/th/id/OIP.ji6So4VCQzBtezTCdbL8lwAAAA?rs=1&pid=ImgDetMain',
      "msg" => '<img src="https://th.bing.com/th/id/OIP.ji6So4VCQzBtezTCdbL8lwAAAA?rs=1&pid=ImgDetMain" alt="sin pais">'

    );
  }
  return $bandera;
}

function crudDetallesSiguiente($id)
{
  $db = AccesoDatos::getModelo();
  $cli = $db->getClienteSiguiente($id);
  $imagen = obtenerImagen($cli);
  $bandera = obtenerBandera($cli);
  include_once "app/views/detalles.php";
}

function crudDetallesAnterior($id)
{
  $db = AccesoDatos::getModelo();
  $cli = $db->getClienteAnterior($id);
  $imagen = obtenerImagen($cli);
  $bandera = obtenerBandera($cli);
  include_once "app/views/detalles.php";
}

function crudModificar($id)
{
  $db = AccesoDatos::getModelo();
  $cli = $db->getCliente($id);
  $orden = "Modificar";
  $extrainfo = '<br><br><input type="submit" name="nav-modificar" value="<< Anterior"><input type="submit" name="nav-modificar" value="Siguiente >>">';
  $extraimg = obtenerImagen($cli);
  include_once "app/views/formulario.php";
}

function crudModificarSiguiente($id)
{
  $db = AccesoDatos::getModelo();
  $cli = $db->getClienteSiguiente($id);
  $orden = "Modificar";
  $extrainfo = '<br><br><input type="submit" name="nav-modificar" value="<< Anterior"><input type="submit" name="nav-modificar" value="Siguiente >>">';
  $extraimg = obtenerImagen($cli);
  include_once "app/views/formulario.php";
}

function crudModificarAnterior($id)
{
  $db = AccesoDatos::getModelo();
  $cli = $db->getClienteAnterior($id);
  $orden = "Modificar";
  $extrainfo = '<br><br><input type="submit" name="nav-modificar" value="<< Anterior"><input type="submit" name="nav-modificar" value="Siguiente >>">';
  $extraimg = obtenerImagen($cli);
  include_once "app/views/formulario.php";
}

function crudPostAlta()
{
  limpiarArrayEntrada($_POST); //Evito la posible inyección de código
  $cli = new Cliente();
  $cli->id            = $_POST['id'];
  $cli->first_name    = $_POST['first_name'];
  $cli->last_name     = $_POST['last_name'];
  $cli->email         = $_POST['email'];
  $cli->gender        = $_POST['gender'];
  $cli->ip_address    = $_POST['ip_address'];
  $cli->telefono      = $_POST['telefono'];
  $error = [];
  $db = AccesoDatos::getModelo();
  if (!$db->comprobarCorreo($cli, "nuevo")) {
    $error[] = "Correo ya existente.</br>";
  }
  /* forma alt. de validar correo
  if (!filter_var($cli->email, FILTER_VALIDATE_EMAIL)) {
  */
  $regexCorreo = "/^([a-zA-Z0-9\.]+@+[a-zA-Z]+(\.)+[a-zA-Z]{2,3})$/";
  if (preg_match($regexCorreo, $cli->email) == 0) {
    $error[] = "Formato de correo incorrecto.</br>";
  }
  /* forma alt. de validar ip
  $regexIP = "/^((25[0-5]|(2[0-4]|1\d|[1-9]|)\d)\.?\b){4}$/";
  if (preg_match($regexIP, $cli->ip_address) == 0) {
  */
  if (!filter_var($cli->ip_address, FILTER_VALIDATE_IP)) {
    $error[] = "Formato de IP incorrecto.</br>";
  }
  $regexpTel = "/^\d{3}-\d{3}-\d{4}$/";
  if (!preg_match($regexpTel, $cli->telefono)) {
    $error[] = "Formato de teléfono incorrecto.</br>";
  }
  if (count($error) === 0) {
    if ($db->addCliente($cli)) {
      $_SESSION['msg'] = "El usuario " . $cli->first_name . " se ha dado de alta.</br>";
    } else {
      $_SESSION['msg'] = "Error al dar de alta al usuario " . $cli->first_name . ".</br>";
    }
  } else {
    $_SESSION['msg'] = "Error al dar de alta al usuario " . $cli->first_name . ".</br>";
    foreach ($error as $valor) {
      $_SESSION['msg'] .= $valor;
    }
  }
  if (!verificarImagen()) {
    $_SESSION['msg'] .= "Error con el tamaño o formato de la imagen.</br>";
  } else {
    if (!almacenarImagen()) {
      $_SESSION['msg'] .= "Error al subir la imagen.</br>";
    }
  }
}

function verificarImagen(): bool
{
  if (!empty($_FILES['imagen_subida']['name'])) {
    $ruta = "app/uploads/";
    $ruta .= basename($_FILES['imagen_subida']['name']);
    $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
    // Restringir formatos y limitar tamaño de imagen
    if ($extension != "jpg" && $extension != "png") {
      return false;
    } else if ($_FILES["imagen_subida"]["size"] > 500000) {
      return false;
    } else {
      return true;
    }
  } else {
    return false;
  }
}

function almacenarImagen(): bool
{
  if (!empty($_FILES['imagen_subida']['name'])) {
    $db = AccesoDatos::getModelo();
    $next_id = $db->obtenerSiguienteID() - 1;
    $ruta = "app/uploads/" . basename($_FILES['imagen_subida']['name']);
    $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
    $img_nombre = str_pad(strval($next_id), 8, "0", STR_PAD_LEFT) . '.' . $extension; // rellena un string hacia la izquierda con ceros hasta alcanzar 8 caracteres
    $img_ruta = "app/uploads/" . $img_nombre;

    if (move_uploaded_file($_FILES['imagen_subida']['tmp_name'], $img_ruta)) {
      return true;
    }
  } else {
    return false;
  }
}

function crudPostModificar()
{
  limpiarArrayEntrada($_POST); //Evito la posible inyección de código
  $cli = new Cliente();

  $cli->id            = $_POST['id'];
  $cli->first_name    = $_POST['first_name'];
  $cli->last_name     = $_POST['last_name'];
  $cli->email         = $_POST['email'];
  $cli->gender        = $_POST['gender'];
  $cli->ip_address    = $_POST['ip_address'];
  $cli->telefono      = $_POST['telefono'];

  $error = [];
  $db = AccesoDatos::getModelo();
  if (!$db->comprobarCorreo($cli, "mod")) {
    $error[] = "Correo ya existente.</br>";
  }
  $regexCorreo = "/^([a-zA-Z0-9\.]+@+[a-zA-Z]+(\.)+[a-zA-Z]{2,3})$/";
  if (preg_match($regexCorreo, $cli->email) == 0) {
    $error[] = "Formato de correo incorrecto.</br>";
  }
  if (!filter_var($cli->ip_address, FILTER_VALIDATE_IP)) {
    $error[] = "Formato de IP incorrecto.</br>";
  }
  $regexpTel = "/^\d{3}-\d{3}-\d{4}$/";
  if (!preg_match($regexpTel, $cli->telefono)) {
    $error[] = "Formato de teléfono incorrecto.</br>";
  }
  if (count($error) === 0) {
    if ($db->modCliente($cli)) {
      $_SESSION['msg'] = "El usuario " . $cli->first_name . " ha sido modificado.</br>";
    } else {
      $_SESSION['msg'] = "Error al modificar el usuario " . $cli->first_name . ".</br>";
    }
  } else {
    $_SESSION['msg'] = "Error al modificar el usuario " . $cli->first_name . ".</br>";
    foreach ($error as $valor) {
      $_SESSION['msg'] .= $valor;
    }
  }
  if(is_uploaded_file($_FILES['imagen_subida']['tmp_name'])){
    if (!verificarImagen()) {
      $_SESSION['msg'] .= "Error con el tamaño o formato de la imagen.</br>";
    } else {
      if (!cambiarImagen($cli)) {
        $_SESSION['msg'] .= "Error al subir la imagen.</br>";
      } else {
        $_SESSION['msg'] .= "La imagen del usuario " . $cli->first_name . " ha sido modificada.</br>";
      }
    }
  }
}

function cambiarImagen($cli): bool
{
  if (!empty($_FILES['imagen_subida']['name'])) {
    //primero borramos la imagen anterior, si es que existe
    $img_existente_jpg = "app/uploads/" . str_pad($cli->id, 8, "0", STR_PAD_LEFT) . ".jpg";
    $img_existente_png = "app/uploads/" . str_pad($cli->id, 8, "0", STR_PAD_LEFT) . ".png";
    if (file_exists($img_existente_jpg)) {
      unlink($img_existente_jpg);
    }
    if (file_exists($img_existente_png)) {
      unlink($img_existente_png);
    }
    //creamos la imagen nueva en su directorio correspondiente
    $ruta = "app/uploads/" . basename($_FILES['imagen_subida']['name']);
    $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
    $img_nombre = str_pad(strval($cli->id), 8, "0", STR_PAD_LEFT) . '.' . $extension; // rellena un string hacia la izquierda con ceros hasta alcanzar 8 caracteres
    $img_ruta = "app/uploads/" . $img_nombre;

    if (move_uploaded_file($_FILES['imagen_subida']['tmp_name'], $img_ruta)) {
      return true;
    }
  } else {
    return false;
  }
}

//CREAR PDF
function crearPDF($id)
{
  $db = AccesoDatos::getModelo();
  $cli = $db->getCliente($id);

  $pdf = new FPDF();
  $pdf->AddPage();
  $posicionY_inicial = 20;
  $pdf->SetFillColor(232, 232, 232);

  $pdf->SetFont('Arial', 'B', 12);
  $pdf->SetY($posicionY_inicial);
  $pdf->SetX(40);
  $pdf->Cell(30, 6, 'ID', 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(40);
  $pdf->Cell(30, 6, 'NOMBRE', 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(40);
  $pdf->Cell(30, 6, 'APELLIDO', 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(40);
  $pdf->Cell(30, 6, 'CORREO', 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(40);
  $pdf->Cell(30, 6, 'GENERO', 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(40);
  $pdf->Cell(30, 6, 'IP', 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(40);
  $pdf->Cell(30, 6, 'TELEFONO', 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(40);
  $pdf->Cell(30, 30, 'PAIS', 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(40);
  $pdf->Cell(30, 30, 'FOTO', 1, 0, 'L', 1);

  $pdf->SetFont('Arial', '', 12);
  $pdf->SetY($posicionY_inicial);
  $pdf->SetX(70);
  $pdf->Cell(100, 6, $cli->id, 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(70);
  $pdf->Cell(100, 6, $cli->first_name, 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(70);
  $pdf->Cell(100, 6, $cli->last_name, 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(70);
  $pdf->Cell(100, 6, $cli->email, 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(70);
  $pdf->Cell(100, 6, $cli->gender, 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(70);
  $pdf->Cell(100, 6, $cli->ip_address, 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(70);
  $pdf->Cell(100, 6, $cli->telefono, 1, 0, 'L', 1);
  $pdf->Ln();
  $pdf->SetX(70);

  $pdf->Image(obtenerBandera($cli)["url"], null, null, 0, 30, "jpg");
  $pdf->SetX(70);
  $pdf->Image(obtenerImagen($cli)["url"], null, null, 0, 30);

  $pdf->Output();
}

//CHECK LOGIN
function comprobarLogin($login, $password): bool
{
  $resul = false;
  $db = AccesoDatos::getModelo();
  $user = $db->getUsuario($login);
  if ($user != false) {
    if ($user->password == $password) {
      $resul = true;
    }
  }
  return $resul;
}

//CHECK ROL
function comprobarRol($login): bool
{
  $db = AccesoDatos::getModelo();
  $user = $db->getUsuario($login);
  return $user->rol;
}
