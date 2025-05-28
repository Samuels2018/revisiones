<?php
// include "../../../conf/conf.php";
session_start();
require "../../../conf/conf.php";
require_once(ENLACE_SERVIDOR."global/object/info.parametros.php");
$max_size = json_decode($arrayParametersObject)->attachment_sizes_files->valor;
// VALID DEFINITION POST ANFD FILE
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES["fileToUpload"]["type"])):
	$id = intval(str_replace(">","",$_GET['id']));
    // VALID PATH
    $path = ENLACE_SERVIDOR."documentos/files/email/".$id."/";    
    // VALID PATH
    if (!file_exists($path)):
        mkdir($path, 0777, true);
    endif;
    // VARIABLES FILE
    $uploadOk    	= 1;
    $fileTmpPath 	= $_FILES['fileToUpload']['tmp_name'];
	$file 			= $_FILES['fileToUpload']['name'];
	$fileSize 		= $_FILES['fileToUpload']['size'];
	$fileType 		= $_FILES['fileToUpload']['type'];
    $fileExtension 	= pathinfo($file, PATHINFO_EXTENSION);
    // DELETE EMPTY SPACE
	$fileName 		= str_replace(" ","",$file);
	$newFileName 	= md5(time() . $fileName) . '.' . $fileExtension;
	// EXTESIONS ALLOW
	$allowedfileExtensions = array('jpeg', 'jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc', 'xlsx', 'docx', 'pdf');
	// VALID FIEL EXTENSION
	if (!in_array($fileExtension, $allowedfileExtensions)):
		$errors[] = "Lo sentimos, la extensión .".$fileExtension." del archivo, no es permitida.";
        $uploadOk = 0;
	endif;
	// Check file size
    if ($fileSize > $max_size):
        $errors[] = "Lo sentimos, el archivo '".$file."' es demasiado grande, el tamaño máximo admitido es 5MB.";
        $uploadOk = 0;
    endif;
	// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0):
        $errors[] = "Lo sentimos, tu archivo '".$file."' no fue subido.";
	// if everything is ok, try to upload file
    else:
    	// UPLOAD FILE
        if (move_uploaded_file($fileTmpPath, $path.$newFileName)):
        	// QUERY
            $sql = "INSERT INTO documentos_sl_adjuntos_email (fk_documento, archivo, archivo_codificado, fk_usuario, fecha) VALUES (:fk_documento, :archivo, :archivo_codificado, :fk_usuario, NOW());";
            $db = $dbh->prepare($sql);
            $db->bindValue(":fk_documento", 		$id, 					PDO::PARAM_INT);
            $db->bindValue(":archivo", 				$fileName, 				PDO::PARAM_STR);
            $db->bindValue(":archivo_codificado", 	$newFileName, 			PDO::PARAM_STR);
            $db->bindValue(":fk_usuario", 			$_SESSION['Usuario'], 	PDO::PARAM_STR);
            $db->execute();
            $messages[] = "El archivo '".$file."' ha sido subido correctamente.";
        else:
            $errors[] = "Lo sentimos, hubo un error subiendo el archivo '".$file."'.";
        endif;
    endif;


    if (isset($errors)) {?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong>Error!</strong>
		<?php
			foreach ($errors as $error) {
				echo "<p>$error</p>";
			}
		?>
	</div>
	<?php
	}

    if (isset($messages)) {
        ?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong>Aviso!</strong>
		<?php
			foreach ($messages as $message) {
				echo "<p>$message</p>";
			}
		?>
	</div>
	<?php
}
endif;
?>