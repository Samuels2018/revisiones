<?php
session_start();
require("../../../conf/conf.php");
//var_dump($_FILES);
if($_FILES["fileToUpload"]['name']!=null){
    $target_dir = ENLACE_SERVIDOR."documentos/files/";
    $carpeta=$target_dir;
    $nombreAchivoOriginal = $_FILES["fileToUpload"]['name'];
    $uploadOk = 1;
        $NombreArchivo = $nombreAchivoOriginal;
        $patharchivo=$carpeta.$NombreArchivo;
        if(file_exists($patharchivo)){ unlink($patharchivo);}
        if ($_FILES["fileToUpload"]["size"] > 50000000000) {
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {

        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$patharchivo)) {
                
                echo 'Archivo Subido Correctamente';
                $sql="INSERT INTO documentos_CC_adjuntos_email(
                fk_cliente
                , archivo
                , fk_usuario
                , fecha
                ) VALUES (
                :fk_documento
                , :archivo
                , :fk_usuario
                , NOW()
            	)";
                $db     = $dbh->prepare($sql);
		        $db->bindValue(":fk_documento" , $_GET['idDoc']  , PDO::PARAM_INT);
		        $db->bindValue(":archivo" , $NombreArchivo  , PDO::PARAM_STR);
		        $db->bindValue(":fk_usuario" , $_SESSION['Usuario']  , PDO::PARAM_INT);
		        $db->execute();
            } else {
                $errors[]= $_FILES["fileToUpload"]["error"];
            }
        }

     
        if (isset($errors)){
            var_dump($errors);
        }
    }
?>