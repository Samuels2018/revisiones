<?php
session_start();
require("../../../conf/conf.php");

echo $sql1="UPDATE documentos_sl_adjuntos_email SET adjuntar=".$_POST['activo']." where rowid = ".$_POST['idAdjunto'];
	$db_ad=$dbh->prepare($sql1);
	$db_ad->execute();
?>