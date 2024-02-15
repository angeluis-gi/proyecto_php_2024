<form>
  <button type="submit" name="orden" value="Nuevo"> Cliente Nuevo </button><br>
</form>
<br>

<form>
  <table>
    <tr>
      <th><input type="submit" name="ordenacion" value="id"></th>
      <th><input type="submit" name="ordenacion" value="first_name"></th>
      <th><input type="submit" name="ordenacion" value="email"></th>
      <th><input type="submit" name="ordenacion" value="gender"></th>
      <th><input type="submit" name="ordenacion" value="ip_address"></th>
      <th><input type="submit" name="ordenacion" value="telefono"></th>
    </tr>
    <?php foreach ($tvalores as $valor) : ?>
      <tr>
        <td><?= $valor->id ?> </td>
        <td><?= $valor->first_name ?> </td>
        <td><?= $valor->email ?> </td>
        <td><?= $valor->gender ?> </td>
        <td><?= $valor->ip_address ?> </td>
        <td><?= $valor->telefono ?> </td>
        <td><a href="#" onclick="confirmarBorrar('<?= $valor->first_name ?>',<?= $valor->id ?>);">Borrar</a></td>
        <td><a href="?orden=Modificar&id=<?= $valor->id ?>">Modificar</a></td>
        <td><a href="?orden=Detalles&id=<?= $valor->id ?>">Detalles</a></td>
        <td><a href="?orden=Imprimir&id=<?= $valor->id ?>">Imprimir</a></td>

      <tr>
      <?php endforeach ?>
  </table>

  <br>
  <button type="submit" name="nav" value="Primero"> << </button>
  <button type="submit" name="nav" value="Anterior"> < </button>
  <button type="submit" name="nav" value="Siguiente"> > </button>
  <button type="submit" name="nav" value="Ultimo"> >> </button>
</form>
<br>
<form method="POST">
  <input type="submit" name="orden" value="Terminar">
</form>