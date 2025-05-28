<?php
session_start();
require("../../conf/conf.php");



$asunto = $_POST['asunto'];
$opcion = $_POST['opcion'];
$_POST['id'];
$cc = $_POST['cc'];
$para = $_POST['para'];
$cco = $_POST['cco'];
$texto = $_POST['texto'];
$_POST['documento'];
$id = $_POST['id'];
$documento = $_POST['documento'];
$tipo = $_POST['tipo'];
$usuario = $_SESSION['usuario'];
if ($cc != '') {
	$para = $_POST['para'] . ', ' . $cc;
}


include_once(ENLACE_SERVIDOR . "mod_europa_facturacion/object/facturas.object.php");
include_once(ENLACE_SERVIDOR . "mod_europa_albaran_compra/object/Albaran_compra.object.php");
include_once(ENLACE_SERVIDOR . "mod_europa_albaran_venta/object/Albaran_venta.object.php");
include_once(ENLACE_SERVIDOR . "mod_europa_compra/object/compras.object.php");
include_once(ENLACE_SERVIDOR . "mod_europa_presupuestos/object/presupuestos.object.php");
include_once(ENLACE_SERVIDOR . "mod_europa_pedido/object/pedido.object.php");

include_once(ENLACE_SERVIDOR."mod_documento_pdf/object/documento_pdf.php");

include_once(ENLACE_SERVIDOR."mail_sys/mail/enviar_email_object.php");

$Documento	=	 new $tipo($dbh, $_SESSION['Entidad']);
$Documento->fetch($_POST['id']);

// $Plantilla = new Plantilla($dbh, $_SESSION['Entidad']);
// $lista_plantillas = $Plantilla->obtener_plantilla_tipo_documento($Documento->documento);
// $fk_plantilla_usar = 0;
// if(count($lista_plantillas)>0){
// 	foreach ($lista_plantillas as $itemplantilla) {
// 		$fk_plantilla_usar = $itemplantilla["rowid"];
// 		if($itemplantilla["defecto"] == 1){
// 			break;
// 		}
// 	}
// }

if(!$Documento->fk_plantilla > 0){
	if(! ($Documento->fk_serie_plantilla > 0) ){
		require_once ENLACE_SERVIDOR . 'mod_documento_pdf/object/plantilla.object.php';
		$Plantilla = new Plantilla($dbh, $_SESSION['Entidad']);
		$lista_plantillas = $Plantilla->obtener_plantilla_tipo_documento($Documento->documento);
		if(count($lista_plantillas)>0){
			foreach ($lista_plantillas as $itemplantilla) {
				if($itemplantilla["defecto"] == 1){
					$fk_plantilla = $itemplantilla["rowid"];
					break;
				}
			}
		}
	}else{
		$fk_plantilla = $Documento->fk_serie_plantilla;
	}
}else{
	$fk_plantilla = $Documento->fk_plantilla;
}


$DocumentoPdf = new documento_pdf($dbh, $_SESSION['Entidad']);
$DocumentoPdf->objDocumento = $Documento;
$content = $DocumentoPdf->genera_pdf('S', $tipo, $fk_plantilla);

if (!empty($_POST['enviar'])) {

	$enviarEmail = new EnviarEmail($dbh, $_SESSION["Entidad"]);
	$enviar_pdf_adjunto = 0; $enviar_xml_adjunto = 0;
	if ($_POST['pdf_adjunto'] == 1) { $enviar_pdf_adjunto = 1; }
	if ($_POST['xml_factura'] == 1) { $enviar_xml_adjunto = 1; }
	$envioCorreo = $enviarEmail->enviar($Documento, $asunto, $para, $texto, $usuario, $enviar_pdf_adjunto, $content, 0, 'enviar_email_logico.php');
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

			<h4> <i class="icon fa fa-check"></i> <?php echo $forma; ?></h4>
			Evento ocurrido <?php echo date('d-m-Y H:i '); ?>
		</div>




		<script>
			///setTimeout('window.close()',3000)
		</script>
		<?php //echo "<script languaje='javascript' type='text/javascript'>window.close();</script>"; 
		?>