<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>CRUD DE USUARIOS</title>
  <link href="web/css/default.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <div id="container" style="width: 600px;">
    <div id="header">
      <h1>MIS CLIENTES CRUD versión 1.1</h1>
    </div>
    <div id="content">
      <hr>
      <form method="POST" style="text-align: center">
        <table style="margin: auto; text-align: left">
          <tr>
            <td>Nombre:</td>
            <td><input type="text" name="login" value="<?= (isset($_REQUEST['nombre'])) ? $_REQUEST['login'] : '' ?>"></td>
          </tr>
          <tr>
            <td>Contraseña: </td>
            <td><input type="password" name="password" value="<?= (isset($_REQUEST['password'])) ? $_REQUEST['password'] : '' ?>"></td>
          </tr>
        </table>
        <br>
        <input type="submit" name="orden" value="Entrar">
      </form>
    </div>
  </div>
</body>

</html>