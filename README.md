# proyecto_php_2024

<h1 align="center">Lista de modificaciones/mejoras al proyecto de php</h1>
<h2 align="center">Puntos 1 al 9</h2>

<h2 align="center">Índice</h3>
<p align="left">
<b><a href="#index.php">index.php</a></b><br>
<b><a href="#crudclientes.php">crudclientes.php</a></b><br>
<b><a href="#comprobarLogin.php">comprobarLogin.php</a></b><br>
<b><a href="#comprobarRol.php">comprobarRol.php</a></b><br>
<b><a href="#crudDetallesSiguiente.php">crudDetallesSiguiente.php</a></b><br>
<b><a href="#crudDetallesAnterior.php">crudDetallesAnterior.php</a></b><br>
<b><a href="#crearPDF.php">crearPDF.php</a></b><br>
<b><a href="#crudModificar.php">crudModificar.php</a></b><br>
<b><a href="#crudModificarSiguiente.php">crudModificarSiguiente.php</a></b><br>
<b><a href="#crudModificarAnterior.php">crudModificarAnterior.php</a></b>
</p>

<h3 align="center" id="index.php">index.php</h3>
<p align="left">
<b>Control de acceso</b> (38-63): si entramos en la página sin iniciar sesión nos redireccionará a <a href="#formulariocontraseña.php">formulariocontraseña.php</a>, donde podremos introducir el usuario y contraseña, que serán comparados con la información en la tabla "User" desde <a href="#comprobarLogin">comprobarLogin</a> en crudclientes.php. En caso de introducir erróneamente la información, aumentará el valor de "contador_intentos", que al llegar a 3 nos impedirá realizar más intentos (<a href="#bloq.php">bloq.php</a>) y nos forzará a reiniciar el navegador. Al iniciar sesión con un usuario y contraseña válidos se dará acceso a la lista de clientes y al resto de funciones de la aplicación, además de reiniciar el contador de intentos.
<br>
<b>Órdenes en CRUD</b> (87-110): en el caso de las órdenes "Nuevo", "Borrar" y "Modificar" el programa llama a la función <a href="#comprobarRol">comprobarRol</a> para determinar el rol del usuario con el que hemos iniciado sesión; sólo podrá acceder a estas funcionalidades en caso de ser 1. Si nuestro rol es 0 se mostrará <a href="#funcionbloq.php">funcionbloq.php</a>.
<br>
<b>Navegación en detalles</b> (120-130): este código no ha sido modificado, pero las funciones <a href="#crudDetallesSiguiente">crudDetallesSiguiente</a> y <a href="#crudDetallesAnterior">crudDetallesAnterior</a> a las que hace referencia en crudclientes.php sí.
<br>
<b>Imprimir</b> (132-136): llama a la funcion <a href="#crearPDF">crearPDF</a> con la información del id del cliente.
<br>
<b>Terminar</b> (148-151): he completado la funcionalidad para que elimine la sesión y pueda reiniciar el programa. No se pedía en la documentación del proyecto, pero me resultaba útil para realizar pruebas y lo he dejado.
<br>
<b>Navegación en modificar</b> (155-165): réplica de la navegación en detalles con ligeras modificaciones. Funciones <a href="#crudModificarSiguiente">crudModificarSiguiente</a> y <a href="#crudModificarAnterior">crudModificarAnterior</a>.
<br>
<b>Orden de los campos</b> (173-180): modificación del código que genera la consulta en <a href="#AccesoDatosPDO.php">AccesoDatosPDO.php</a> para tener en cuenta el campo por el cual la lista será ordenada. Este campo se elije en <a href="#list.php">list.php</a>.
</p>

<h3 align="center" id="crudclientes.php">crudclientes.php</h3>
<p align="left">
<b>crudBorrar</b> (8-16): una vez una entrada ha sido borrada, comprueba si existe imagen asociada almacenada en la carpeta "app/uploads" y la elimina.
<br>
<b>crudAlta</b> (33-34): al utilizar el mismo formulario que <a href="#crudModificar">crudModificar</a> he querido incluir un par de variables para añadir código html adicional, y estas deben estar vacías en el caso de entrar a <a href="#formulario.php">formulario.php</a> con la orden "Nuevo".
</p>


<b id="comprobarLogin">comprobarLogin</b>
<a href="#comprobarLogin">comprobarLogin</a>

<b id="comprobarRol">comprobarRol</b>
<br>
<b id="crudDetallesSiguiente">crudDetallesSiguiente</b>
<br>
<b id="crudDetallesAnterior">crudDetallesAnterior</b>
<br>
<b id="crearPDF">crearPDF</b>
<br>
<b id="crudModificar">crudModificar</b>
<br>
<b id="crudModificarSiguiente">crudModificarSiguiente</b>
<br>
<b id="crudModificarAnterior">crudModificarAnterior</b>

<h1 align="center">EN CONSTRUCCIÓN</h1>
