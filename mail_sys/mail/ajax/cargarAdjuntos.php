<?php
session_start();
include '../../../conf/conf.php';
$file = intval(str_replace(">", "", $_POST['idDoc']));
// QUERY
$sql =
    "SELECT
	 DSE.rowid,
     DSE.archivo,
     DSE.archivo_codificado,
     DSE.fecha,
     FU.nombre as usuario
	FROM documentos_sl_adjuntos_email DSE
	INNER JOIN fi_usuario FU ON FU.rowid = DSE.fk_usuario
	WHERE DSE.fk_documento =:fk_documento
	AND  DSE.activo = 1;";
$db = $dbh->prepare($sql);
$db->bindValue(":fk_documento", $file, PDO::PARAM_INT);
$db->execute();
// HTML
echo "<h3>Archivos Adjuntos</h3>";
echo "<table class='table' style='width:100%;'>";
echo "<tr><td><b>Archivo</b></td><td><b>Fecha</b></td><td><b>Usuario</b></td><td align='center'><b>Acciones</b></td></tr>";
// VALID ROWS
if ($db->rowCount() > 0):
    while ($data = $db->fetch(PDO::FETCH_OBJ)):
        echo "<tr>
				<td>" . $data->archivo . "</td>
				<td>" . date('d-m-Y', strtotime($data->fecha)) . "</td>
				<td>" . $data->usuario . "</td>
				<td align='center'>
				<div class='btn btn-danger' onclick='eliminarAdjunto(".$data->rowid.")'><i class='fa fa-trash'></i> Eliminar</div>
				</td>
			</tr>";
    endwhile;
endif;
echo "</table>";
