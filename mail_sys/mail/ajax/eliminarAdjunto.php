<?php
session_start();
require("../../../conf/conf.php");

$sql1="Select * from documentos_sl_adjuntos_email where rowid = ".$_POST['idAdjunto'];
	$db_ad=$dbh->prepare($sql1);
	$db_ad->execute();
		$docu=$db_ad->fetch(PDO::FETCH_OBJ);
$target_dir = ENLACE_SERVIDOR."documentos/files/".$docu->archivo;
				unlink($target_dir); 
				$sql1="delete from documentos_sl_adjuntos_email where rowid = ".$_POST['idAdjunto'];
	$db_ad=$dbh->prepare($sql1);
	$db_ad->execute();
?>