# proyecto_php_2024

<h1 align="center">Lista de modificaciones/mejoras al proyecto de php</h1>
<h2 align="center">Puntos 1 al 9</h2>
<h2 align="center">Índice</h3>
<p align="left">
<b><a href="#index.php">index.php</a></b><br>
<b><a href="#crudclientes.php">crudclientes.php</a></b><br>
<b><a href="#AccesoDatosPDO.php">AccesoDatosPDO.php</a></b><br>
<b><a href="#Usuario.php">Usuario.php</a></b><br>
<b><a href="#bloq.php">bloq.php</a></b><br>
<b><a href="#detalles.php">detalles.php</a></b><br>
<b><a href="#formulario.php">formulario.php</a></b><br>
<b><a href="#formulariocontraseña.php">formulariocontraseña.php</a></b><br>
<b><a href="#funcionbloq.php">funcionbloq.php</a></b><br>
<b><a href="#list.php">list.php</a></b><br>
</p>

<h3 align="center" id="index.php">index.php</h3>
<p align="left">
<b>Control de acceso</b> (38-63): si entramos en la página sin iniciar sesión nos redireccionará a <a href="#formulariocontraseña.php">formulariocontraseña.php</a>, donde podremos introducir el usuario y contraseña, que serán comparados con la información en la tabla "User" desde <a href="#comprobarLogin">comprobarLogin</a> en crudclientes.php. En caso de introducir erróneamente la información, aumentará el valor de "contador_intentos", que al llegar a 3 nos impedirá realizar más intentos (<a href="#bloq.php">bloq.php</a>) y nos forzará a reiniciar el navegador. Al iniciar sesión con un usuario y contraseña válidos se dará acceso a la lista de clientes y al resto de funciones de la aplicación, además de reiniciar el contador de intentos.
<br>
<b id="ordenesCRUD">Órdenes en CRUD</b> (87-110): en el caso de las órdenes "Nuevo", "Borrar" y "Modificar" el programa llama a la función <a href="#comprobarRol">comprobarRol</a> para determinar el rol del usuario con el que hemos iniciado sesión; sólo podrá acceder a estas funcionalidades en caso de ser 1. Si nuestro rol es 0 se mostrará <a href="#funcionbloq.php">funcionbloq.php</a>.
<br>
<b>Navegación en detalles</b> (120-130): este código no ha sido modificado, pero las funciones <a href="#crudDetallesSiguiente-Anterior">crudDetallesSiguiente y crudDetallesAnterior</a> a las que hace referencia en crudclientes.php sí.
<br>
<b>Imprimir</b> (132-136): llama a la función <a href="#crearPDF">crearPDF</a> con la información del id del cliente.
<br>
<b>Terminar</b> (148-151): he completado la funcionalidad para que elimine la sesión y pueda reiniciar el programa. No se pedía en la documentación del proyecto, pero me resultaba útil para realizar pruebas y lo he dejado.
<br>
<b>Navegación en modificar</b> (155-165): réplica de la navegación en detalles con ligeras modificaciones. Funciones <a href="#crudModificarSiguiente-Anterior">crudModificarSiguiente y crudModificarAnterior</a>.
<br>
<b>Orden de los campos</b> (173-180): modificación del código que genera la consulta en <a href="#getClientes">getClientes</a>, AccesoDatosPDO.php para tener en cuenta el campo por el cual la lista será ordenada. Este campo se elije en <a href="#list.php">list.php</a>.
</p>

<h3 align="center" id="crudclientes.php">crudclientes.php</h3>
<p align="left">
<b id="crudBorrar">crudBorrar</b> (8-16): una vez una entrada ha sido borrada, comprueba si existe imagen asociada almacenada en la carpeta "app/uploads" y la elimina.
<br>
<b id="crudAlta">crudAlta</b> (33-34): al utilizar el mismo formulario que <a href="#crudModificar">crudModificar</a> he querido incluir un par de variables para añadir código html adicional, y estas deben estar vacías en el caso de entrar a <a href="#formulario.php">formulario.php</a> con la orden "Nuevo".
<br>
<b id="crudDetalles">crudDetalles</b> (42-43): nuevas variables $imagen y $bandera para mostrar junto al resto de la información del cliente. Funciones <a href="#obtenerImagen">obtenerImagen</a> y <a href="#obtenerBandera">obtenerBandera</a>.
<br>
<b id="obtenerImagen">obtenerImagen</b> (47-76): comprueba si existe imagen asociada al id del cliente y de no ser así la extrae de <a href="https://robohash.org/">robohash.org</a>. En ambos casos devuelve un array con distintas cadenas de texto, correspondientes a las necesidades de cada sección del proyecto que requiera esta función:
<ul>
<li>url — <a href="#crearPDF">crearPDF</a> — Únicamente la dirección de la imagen.</li>
<li>msg — <a href="#detalles.php">detalles.php</a> — Etiqueta HTML de imagen.</li>
<li>form — <a href="#formulario.php">formulario.php</a> — Etiqueta HTML de imagen dentro de una fila nueva en la tabla del formulario para modificar.</li>
</ul>
<b id="obtenerBandera">obtenerBandera</b> (78-97): extrae un JSON con información de la ubicación del usuario según su IP mediante <a href="http://ip-api.com">ip-api.com</a>. Entre esa información se encuentra el código del país, que es utilizado en <a href="https://flagcdn.com">flagcdn.com</a> para conseguir una dirección con la bandera de dicho pais. Al igual que la función anterior, esta devuelve un array con distintas cadenas.
<ul>
<li>url — <a href="#crearPDF">crearPDF</a> — Únicamente la dirección de la imagen.</li>
<li>msg — <a href="#detalles.php">detalles.php</a> — Etiqueta HTML de imagen.</li>
</ul>
<b id="crudDetallesSiguiente-Anterior">crudDetallesSiguiente/crudDetallesAnterior</b> (103-104/112-113): añade la imagen del cliente y la bandera al cambiar de cliente. La orden se procesa con <a href="#getClienteSiguiente-Anterior">getClienteSiguiente/Anterior</a> en AccesoDatosDPO.php.
<br>
<b id="crudModificar">crudModificar</b> (121-124): añade los botones de navegación "Anterior" y "Siguiente" y la imagen del cliente.
<br>
<b id="crudModificarSiguiente-Anterior">crudModificarSiguiente/crudModificarAnterior</b> (131-133/141-143): añade la imagen del cliente y la bandera al cambiar de cliente. La orden se procesa con <a href="#getClienteSiguiente-Anterior">getClienteSiguiente/Anterior</a> en AccesoDatosDPO.php.
<br>
<b id="crudPostAlta">crudPostAlta</b> (160-200): valida el formato del correo (<a href="#comprobarCorreo">comprobarCorreo</a>), la IP y el teléfono antes de crear el cliente nuevo. Según realiza comprobaciones, una variable array recopila los errores y los muestra al final del proceso en caso de haber. También comprueba el formato y tamaño de la imagen (<a href="#verificarImagen">verificarImagen</a>), pero lo hace de forma independiente ya que se puede reintentar la subida tras crear el cliente mediante la página de modificación.
<br>
<b id="verificarImagen">verificarImagen</b> (202-219): comprueba que la extensión sea .jpg o .png y el tamaño inferior a 500kb.
<br>
<b id="almacenarImagen">almacenarImagen</b> (223-229): guarda la imagen subida en la carpeta "uploads" con el formato de nombre especificado. Para ello debe encontrar el próximo ID que aún no ha sido creado mediante <a href="#obtenerSiguienteID">obtenerSiguienteID</a>.
<br>
<b id="crudPostModificar">crudPostModificar</b> (256-300): similar a <a href="#crudPostAlta">crudPostAlta</a>. Como diferencia tenemos la creación de un segundo objeto cliente que recoje la información original mediante el ID invariable para compararlo con los datos cambiados (o no) y así determinar si debe ejecutar la función <a href="#modCliente">modCliente</a> en AccesoDatosPDO.php. Esto es así para poder cambiar la imagen almacenada sin que muestre mensajes de error por no haber cambiado datos y haber forzado a ejecutar una consulta que daría error.
<br>
<b id="cambiarImagen">cambiarImagen</b> (303-327): la diferencia con <a href="#almacenarImagen">almacenarImagen</a> es la eliminación de la imagen ya guardada en caso de existir.
<br>
<b id="crearPDF">crearPDF</b> (329-399): utiliza <a href="http://www.fpdf.org/">fpdf.org</a> para crear PDFs con PHP. Uso simple en este caso, solo indico dónde va cada propiedad del objeto cliente en la página y al final añado tambien la imagen y la bandera del país.
<br>
<b id="comprobarLogin">comprobarLogin</b> (402-413): primero comprueba que existe un usuario con el nombre dado y después que la contraseña coincide con la de este. Si es así devuelve true.
<br>
<b id="comprobarRol">comprobarRol</b> (415-421): devuelve directamente el rol del usuario dado. 0 o 1.
<br>
</p>

<h3 align="center" id="AccesoDatosPDO.php">AccesoDatosPDO.php</h3>
<p align="left">
<b id="getClientes">getClientes</b> (64): añade el orden en el que se debe mostrar la tabla. Este campo se elije en <a href="#list.php">list.php</a>.
<br>
<b id="getClienteSiguiente-Anterior">getClienteSiguiente/Anterior</b> (95-114/121-140): modifica la consulta por defecto para añadir el orden en el que se muestra la tabla en la función anterior para poder navegar entre los clientes según este orden. En el caso de "Siguiente" el orden es ascendente, en "Anterior" descendente. También hace que funcione como un bucle; al seleccionar "Siguiente" desde el último cliente de la lista nos mostrará el primero, y de igual forma con "Anterior" pero mostrando el último.
<br>
<b id="comprobarCorreo">comprobarCorreo</b> (182-201): si la función es ejecutada desde <a href="#crudPostAlta">crudPostAlta</a> comprueba si el correo introducido existe en la base de datos. Si es ejecutada desde <a href="#crudPostModificar">crudPostModificar</a> hace la comparación eliminando el correo del propio usuario, que ya existe, de los posibles resultados.
<br>
<b id="obtenerSiguienteID">obtenerSiguienteID</b> (203-209): obtiene el próximo ID que será creado mediante la consulta del "AUTO INCREMENT".
<br>
<b id="getUsuario">getUsuario</b> (227-240): misma función que getCliente pero con la nueva tabla "User".
<br>
</p>

<h3 align="center" id="Usuario.php">Usuario.php</h3>
<p align="left">
Nueva tabla creada para los puntos 8 y 9. Hay un .txt en la raíz del proyecto con algunos ejemplos de usuarios para realizar pruebas.
</p>

<h3 align="center" id="bloq.php">bloq.php</h3>
<p align="left">
Página simple que se muestra cuando se han realizado demasiados intentos de inicio de sesión en <a href="#formulariocontraseña.php">formulariocontraseña.php</a>.
</p>

<h3 align="center" id="detalles.php">detalles.php</h3>
<p align="left">
Añadidas variables de <a href="#obtenerImagen">imagen</a> y <a href="#obtenerBandera">bandera</a>, además de los botones <a href="#crearPDF">Imprimir</a> y <a href="#crudPostModificar">Modificar</a>.
</p>

<h3 align="center" id="formulario.php">formulario.php</h3>
<p align="left">
Añade un campo para subir una imagen y otro para mostrar la ya existente (en caso de entrar desde la <a href="#ordenesCRUD">orden</a> "Modificar").
</p>

<h3 align="center" id="formulariocontraseña.php">formulariocontraseña.php</h3>
Página simple con formulario en POST que recoge los valores de usuario y contraseña necesarios para visualizar el resto del proyecto y que son procesados en <a href="#comprobarLogin">comprobarLogin</a>.
<p align="left">
</p>

<h3 align="center" id="funcionbloq.php">funcionbloq.php</h3>
Muestra un texto en sustitución de las funciones <a href="#crudAlta">crudAlta</a>, <a href="#crudBorrar">crudBorrar</a> y <a href="#crudModificar">crudModificar</a> en caso de que el <a href="#comprobarRol">rol</a> del usuario no sea 1.
<p align="left">
</p>

<h3 align="center" id="list.php">list.php</h3>
Añadidos botones <a href="#crearPDF">Imprimir</a> y Terminar.
<p align="left">
</p>
