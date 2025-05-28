<?php
include '../../../conf/conf.php';
// QUERY
$sql = "
	SELECT
		 rowid,
		 fk_documento,
	     archivo,
	     archivo_codificado,
	     fecha
	FROM documentos_sl_adjuntos_email
	WHERE rowid =:rowid;";
$db = $dbh->prepare($sql);
$db->bindValue(':rowid', $_POST['rowid'], PDO::PARAM_INT);
$db->execute();
// VALID ROW
if ($db->rowCount() > 0) {
    $data = $db->fetch(PDO::FETCH_OBJ);

    $filename = ENLACE_SERVIDOR . 'documentos/files/email/' . $data->fk_documento . '/' . $data->archivo_codificado;
    unlink($filename);
    // VALID FILE
    if (!file_exists($filename)) {
        $sql = "DELETE FROM documentos_sl_adjuntos_email WHERE rowid = :rowid";
        $db  = $dbh->prepare($sql);
        $db->bindValue(':rowid', $_POST['rowid'], PDO::PARAM_INT);
        $db->execute();
        echo "Se ha eliminado el archivo correctamente";
    } else {
        echo "No se ha eliminado el archivo, por favor, intentar de nuevo.";
    }

}
