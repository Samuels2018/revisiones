<?php
session_start();
require("../../../conf/conf.php");
//var_dump($_FILES);
echo '<table width="100%">';
$sql1="Select * from documentos_CC_adjuntos_email where fk_cliente = ".$_POST['idDoc'];
	$db_ad=$dbh->prepare($sql1);
	$db_ad->execute();
		while($docu=$db_ad->fetch(PDO::FETCH_OBJ)){
	
			if($docu->rowid>0){
				$checked='checked';
				if($docu->adjuntar==0){
					$checked='';
				}
				echo '<tr><td><input type="checkbox" value="'.$docu->rowid.'" '.$checked.' onclick="atChec(this)"></td><td>'.$docu->archivo.'</td><td><i onclick="eliminarAdjunto('.$docu->rowid.')" class="btn btn-success">Eliminar</i></td></tr>'; 
			}
		}
echo '</table>';
?>