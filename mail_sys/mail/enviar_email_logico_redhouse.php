<?php
session_start();
require("../../conf/conf.php");


$_POST['para'];
$asunto=$_POST['asunto'];
$opcion=$_POST['opcion'];
$_POST['id'];
$cc = $_POST['cc'];
$para = $_POST['para'];
$cco=$_POST['cco'];
$texto = $_POST['texto'];
$_POST['documento'];
$id=$_POST['id'];
$documento=$_POST['documento'];
$tipo = $_POST['tipo'];
if ($cc != '') {
	$para = $_POST['para'] . ', ' . $cc;
}


require_once ENLACE_SERVIDOR . 'mod_redhouse_cotizaciones/object/redhouse.cotizaciones.object.php';

require_once ENLACE_SERVIDOR . 'mod_usuarios/object/usuarios.object.php';


$factura3 = new redhouse_Cotizacion($dbh, $_SESSION['Entidad']);
$factura3->fetch($_POST['id']);
$listado_adjuntos = $factura3->obtener_adjuntos_cotizacion($_POST['id']);

$usuario = new usuario($dbh);
$usuario->buscar_data_usuario($_SESSION['usuario']);



//usuario 
 
if (!empty($_POST['enviar'])){ 


include_once(ENLACE_SERVIDOR."mail_sys/mail/PHPMailer/src/EnviarCorreoSmtp.php");
 
	//$mail->SMTPDebug = 2;
	//ENVIAR A VARIOS DESTINOS SEPARADOS POR ,
	$array = explode(",", $para);
	if(count($array)>1){
		foreach($array as $correos) 
		{
			//$mail->addAddress( trim($correos) );  
		}
	}else{
		//	$mail->addAddress( trim($para) );
	}


	//ENVIAR A VARIOS CC SEPARADOS POR ,
	if (!empty($cc)){
		$array2 = explode(",", $cc);
		if(count($array2)>1){
			foreach($array2 as $correos2) 
			{
				//$mail->AddCC( trim($correos2) );  
			}
		}else{
				//$mail->AddCC( trim($cc) );
		}
	}
	


	////////////////////////////////ADJUNTAR ARCHIVOS/////////////////////////
	$attachments = [];
	///// FACTURA PDF
	if($_POST['pdf_adjunto'] == 1)
	{
		$_GET['id'] = $_POST['id'];
		$_GET['ad'] = 1;
		$_GET['d'] = 1;
		switch ($tipo) {
			case 'Cotizacion':
				include ENLACE_SERVIDOR.'mod_redhouse_cotizaciones/class/generar_pdf.php';
			break;
		}
		
		$adjunto = array(
			'archivo_base64' => $content,
			'archivo_nombre' => "$tipo-".$_POST['id'].".pdf",
			'archivo_tipo' => 'base64',
			'archivo_MIME' => 'pdf_files'
		);
		$attachments[] = $adjunto;
	}
	
	if($_POST['excel_adjunto'] == 1)
	{

	    $_GET['id'] = $_POST['id'];
	    $_GET['ad'] = 1;
	    $_GET['d'] = 1;
	    $content = ''; // Inicializa la variable content

	    switch ($tipo) {
	        case 'Cotizacion':
	         
	            include ENLACE_SERVIDOR.'mod_redhouse_cotizaciones/class/generar_excel_para_email.php';
	       	
	            break;
	    }
	   // Adjuntar el archivo al email

			$adjunto = array(
			    'archivo_base64' => $content,
			    'archivo_nombre' => $filename,
			    'archivo_tipo' => 'base64',
			    'archivo_MIME' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			);
			$attachments[] = $adjunto;
					
	}


	//Ahora vamos a adjuntar los archivos 
	//VAMOS A DOCUMENTAR ESTA LINEA YA QUE LOS ADJUNTOS SON COMO SEGUIMIENTO
	/*
	foreach($listado_adjuntos as $adjunto_data) {
	    $entidad = $_SESSION['Entidad'];
	    $filePath = ENLACE_FILES_EMPRESAS . 'imagenes/entidad_' . $entidad . '/cotizacion/' . $adjunto_data->label;

	    if (file_exists($filePath)) {
	        // Obtener el contenido del archivo
	        $content = file_get_contents($filePath);
	        $filename = $adjunto_data->label;
	        // Codificar el contenido en base64
	        $content_base64 = base64_encode($content);
	        // Obtener el tipo MIME del archivo
	        $mime_type = mime_content_type($filePath);



	        // Crear el array de adjunto
	        $adjunto = array(
	            'archivo_base64' => file_get_contents($filePath),
	            'archivo_nombre' => $filename,
	            'archivo_tipo' => 'base64',
	            'archivo_MIME' => $mime_type
	        );

	        $attachments[] = $adjunto;
	    }
	}*/




	
	/// FACTURA XML
	if(isset($_POST['xml_adjunto']) && $_POST['xml_adjunto'] == 1){
		$filePath = $fileName = null;
		$fileName = basename($factura->consecutivo.'.xml');
		$filePath = '/var/www/facturacion_electronica_documentos_SL/documentos/'.$factura3->consecutivo_tipo_documento.'/firmado/'; 
		$file=$filePath.$fileName;	 
		
		if (file_exists($file)) {
			
			
			$adjunto = array(
				'archivo_base64' => (file_get_contents($file)),
				'archivo_nombre' => $fileName,
				'archivo_tipo' => 'base64',
				'archivo_MIME' => 'xml_files'
			);
	
			$attachments[] = $adjunto;
		}
	}



	if(isset($_POST['respuesta_adjunto']) && $_POST['respuesta_adjunto'] == 1){
	
		$fileNameXML='Comprobante_'.$factura3->consecutivo.'.xml';
		
		if ($factura3->electronica_resultado_txt>" ") {
			$ComprobanteXML=$factura3->electronica_resultado_txt;

			$adjunto = array(
				'archivo_base64' => ($ComprobanteXML),
				'archivo_nombre' => $fileNameXML,
				'archivo_tipo' => 'base64',
				'archivo_MIME' => 'xml_files'
			);
	
			$attachments[] = $adjunto;
		}
	}
	
	


	foreach ($_POST as $key => $valor){
		$buscar = "documento_";
		$resultado = strpos($key, $buscar);

	
	
		if($resultado !== FALSE){
			$array = explode("_", $key);
	
			$sql = "Select archivo,archivo_codificado from documentos_sl_adjuntos where rowid = ".$array[1];
			$dbDoc = $dbh->prepare($sql);
			
		
			$dbDoc->execute();
			while($doc = $dbDoc->fetch(PDO::FETCH_OBJ)){
				$url = ENLACE_SERVIDOR.'/documentos/files/'.$doc->archivo_codificado;

	
				$adjunto = array(
					'archivo_base64' => (file_get_contents($url)),
					'archivo_nombre' => $doc->archivo,
					'archivo_tipo' => 'base64',
					'archivo_MIME' => 'document_files'
				);
	
				$attachments[] = $adjunto;
			}
		}
	}


		$subject= $asunto;
		$html = $texto;
		$debug=0;


		$respuesta = Email_SMPT($dbh, $html, $para, $attachments,'enviar_email_logico.php', $subject, $debug,1,"Redhouse Cotización",$usuario->acceso_usuario);
		$error = $respuesta['error'];
	
		


if (!empty($cco)){ $mail->AddBCC($cco, '');}




//send the message, check for errors
if ($error) {

    $forma="Ocurrio Un Error (".$respuesta['error_txt'].")";
	
	$sql = "INSERT INTO `email_log`(`email_enviado`,`fk_factura`, `detalle`, `codigo`, `fecha`) VALUES (:email_enviado, :fk_factura ,:detalle, :codigo, now())";
    $db4 = $dbh->prepare($sql);
	
    $db4->bindValue(':email_enviado', $para, PDO::PARAM_INT);
	$db4->bindValue(':fk_factura', $id, PDO::PARAM_INT);
    $db4->bindValue(':detalle', $forma , PDO::PARAM_STR);
    $db4->bindValue(':codigo', 1, PDO::PARAM_INT);
    $db4->execute();
	
} else {

    $forma='Correo Enviado de Manera Correcta'; 
    
    $correosEnviados = "Correos Enviado de Manera Manual a: ".$para;

    $sql = "INSERT INTO `email_log`(`email_enviado`,`fk_factura`, `detalle`, `codigo`, `fecha`) VALUES (:email_enviado, :fk_factura ,:detalle, :codigo, now())";
    $db4 = $dbh->prepare($sql);
	
    $db4->bindValue(':email_enviado', $para, PDO::PARAM_STR);
	$db4->bindValue(':fk_factura', $id, PDO::PARAM_INT);
    $db4->bindValue(':detalle', $correosEnviados , PDO::PARAM_STR);
    $db4->bindValue(':codigo', 1, PDO::PARAM_INT);
    $db4->execute();
    
}


} /// fin del Envio ya tu sabes!


?>
<html>
  <head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="<?php echo ENLACE_WEB; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->

  <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- fullCalendar 2.2.5-->
    <link href="<?php echo ENLACE_WEB; ?>bootstrap/bootstrap/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
    <link href="../../plugins/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media='print' />
    <!-- Theme style -->
    <link href="<?php echo ENLACE_WEB; ?>bootstrap/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href="<?php echo ENLACE_WEB; ?>bootstrap/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="<?php echo ENLACE_WEB; ?>bootstrap/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />
    <!-- bootstrap wysihtml5 - text editor -->
    <link href="<?php echo ENLACE_WEB; ?>bootstrap/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="skin-blue">
 
<section class="content">
<div class="alert alert-success alert-dismissable">
               
                    <h4>	<i class="icon fa fa-check"></i>  <?php echo $forma; ?></h4>
                    Evento ocurrido <?php echo date('d-m-Y H:i ');?>
                  </div>

				  
				   
					
					<script>
					///setTimeout('window.close()',3000)
					</script>
					<?php //echo "<script languaje='javascript' type='text/javascript'>window.close();</script>"; ?>
			








