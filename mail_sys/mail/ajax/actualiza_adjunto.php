<?php 

	include '../../../conf/conf.php';
	
	$sql = "UPDATE documentos_sl_adjuntos_email SET adjuntar = :adjuntar where rowid = :rowid";
	$db = $dbh->prepare($sql);
	$db->bindValue(':rowid', $_POST['idAdjunto'], PDO::PARAM_INT);
	$db->bindValue(':adjuntar', $_POST['valor'], PDO::PARAM_INT);
	$db->execute();
	
?>