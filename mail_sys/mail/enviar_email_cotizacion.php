﻿<?php
session_start();
require("../../conf/conf.php");

$id=$_GET['id'];
 
	require_once(ENLACE_SERVIDOR.'mod_cotizaciones/object/cotizaciones.object.php');
	$factura2	=	 new Cotizacion($dbh , $_SESSION['Entidad']   );
	$factura2->fetch($id);



 
	
	$titulo="Enviar  <i class='fa fa-fw fa-money'></i> <b> Cotizacion ".$factura2->referencia."</b>   Por Email";	
	$empresa=$factura2->fk_tercero;
	$texto='<br><br><p>Te envio la informacion respecto a la Cotizacion  '.$factura2->referencia.', <br><br>
	</p>';
		
	$adjunto="<b> Cotizacion ".$factura2->consecutivo."</b>";
	

	
	$sql = "Select email from fi_terceros where rowid = ".$factura2->fk_tercero;

	$dbTercero = $dbh->prepare($sql);
	$dbTercero->execute();
	$terceroEmail = $dbTercero->fetch(PDO::FETCH_OBJ);
	
	
	$correosExtras = "";
	

	
 
/////////////PREPARANDO LOS DATOS PARA EL ASUNTO Y EL CUERPO DE CORREO 
 
$sql = "Select body_correos from fi_configuracion_empresa where rowid = 7";
$db = $dbh->prepare($sql);
$db->execute();
$cuerpoCorreo = $db->fetch(PDO::FETCH_OBJ);


if($factura2->moneda == 1){
	$monedita = "EUR";
}else{
	$monedita = "USD";
}

$asunto="CAFE CAMINOS - ".$factura2->referencia;

$resultado = str_replace("%NOMBRECLIENTE%", $factura2->nombre_txt, 								$cuerpoCorreo->body_correos);
$resultado = str_replace("%CONSECUTIVO%", 	$factura2->rowid, 							$resultado);
$resultado = str_replace("%MONEDA%", 		$monedita, 											$resultado);
$resultado = str_replace("%MONTO%",			numero_simple($factura2->total),					$resultado);
$resultado = str_replace("%FECHA%", 		date('d-m-Y', strtotime($factura2->fecha)), 		$resultado);
$texto = $resultado;

//////////////FIN DE PREPARACION
?>
<script>
	function deshabilitarBoton(){
		//$("#enviar").hide();
		//return true;
	}
</script>
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
    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media='print' />
  
  </head>
  <body class="skin-blue" style="padding:15px!important;">
 
<section class="content">

 <div class="row"><!--onSubmit="return deshabilitarBoton();" -->
<form id="enviar_correo" action="<?php echo ENLACE_WEB; ?>mail_sys/mail/enviar_email_logico.php" method="POST" >

<input type="hidden" name="opcion" value="<?php echo $_REQUEST['opcion']?>" />
<input type="hidden" name="id" 	   value="<?php echo $_REQUEST['id']?>" />
<input type="hidden" name="tipo" 	   value="Cotizacion" />


 <div class="col-md-9">
              <div class="box box-primary">
                <div class="box-header with-border">
                <!--  <h3 class="box-title"><?php // echo $titulo; ?></h3> -->
                </div><!-- /.box-header -->
                <div class="box-body">
                  
                  <div class="form-group">
                    Destinatario: <input class="form-control"   name="para" placeholder="Para:" value="<?php echo $terceroEmail->email; ?>"/>
                  </div>

 

                  <div class="form-group">
                    CC: <input class="form-control" placeholder="CC:" name="cc" value="<?php echo $correosExtras ?>"/>
                  </div>


			
				
                  <div class="form-group">
                    Asunto: <input class="form-control" placeholder="Asunto:"  name="asunto"  value="<?php echo $asunto; ?>" />
                  </div>

                  <div class="form-group">
                    Detalle: <textarea id="compose-textarea" class="form-control" style="height: 300px" name="texto">
                      
                    <?php echo $texto; ?>
					
                    </textarea>
                  </div>
                  
		<div class="form-group">

			<table style="width:100%;">
				<tr>
					<td><input type="checkbox" name="pdf_adjunto" checked value='1'></td>
					<td>Cotizacion PDF</td>
				</tr>
		
			
				
				
				<?php echo $tr ?>
			</table>
			
			
		</div>


		</div><!-- /.box-body -->
		<div class="box-footer">
		  <div class="pull-right">
			 <button type="submit" class="btn btn-primary" name="enviar" id="enviar" value="true" ><i class="fa fa-envelope-o"></i> Enviar</button>
		  </div>
		  <button onClick="self.close();return false;"  class="btn btn-default"><i class="fa fa-times"  ></i> Cerrar </button>
		  <?php //echo ENLACE_WEB; ?>
		</div><!-- /.box-footer -->
	  </div><!-- /. box -->
	</div><!-- /.col -->


           </form> 
</div>
</section>
 <!-- jQuery 2.1.3 -->
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
   <!-- <script src="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/fastclick/fastclick.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/dist/js/app.min.js" type="text/javascript"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/dist/js/demo.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <script src="<?php echo ENLACE_WEB ?>mail_sys/mail/tinymce/js/tinymce/tinymce.min.js"></script>
	
	<!-- Page Script -->
	<script>
        tinymce.init({ 
            selector:'textarea', 
            height: 400,
            menubar: false,
            plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern image code"
            ],
            toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | ",
            toolbar2: "styleselect fontselect fontsizeselect",
            toolbar3: "table | charmap | emoticons | print| image link code | preview | anchor link",
        });    
    </script>

	
 
 <script>
 function upload_image(){//Funcion encargada de enviar el archivo via AJAX
		
		//alert();
		$('#respuesta1').empty();
		$('#respuesta1').html('<span>Cargando...</span>');
		
		var inputFileImage = document.getElementById("files");
		var file = inputFileImage.files[0];
		var data = new FormData();
		data.append('fileToUpload',file);
		
		
		
		/*jQuery.each($('#fileToUpload')[0].files, function(i, file) {
			data.append('file'+i, file);
		});*/
		
		console.log('listo para mandar');		
		$.ajax({
			// Url to which the request is send
			url: "<?php echo ENLACE_WEB; ?>mail_sys/mail/ajax/subirAdjunto.php?idDoc=<?php echo $_GET['id']; ?>",        
			type: "POST",             // Type of request to be send, called as method
			data: data, 			  // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{
				$("#respuesta1").empty();
				$("#respuesta1").html(data);
				cargarListaAdjuntos();
			}
		});		
	}
	
	
	function cargarListaAdjuntos(){
				$("#adjuntos").empty();
                $("#adjuntos").html('<i class="fas fa-cog fa-spin fa-5x fa-fw" ></i>');
				
				$.ajax({ 
                    method: "POST",
					url: "<?php echo ENLACE_WEB; ?>mail_sys/mail/ajax/cargarAdjuntos.php",
					data : {
						idDoc : <?php echo $_GET['id']; ?>,
					},
                    beforeSend: function(xhr){
                    
                }
				}).done(function(data) 
                { 
                    $("#adjuntos").empty(); 
                    $("#adjuntos").html(data); 
                });
	}

	
	function eliminarAdjunto(x){
		var result = confirm("Desea eliminar este archivo del servidor? Esta accion no se puede deshacer.");
			if(result == true){
				$.ajax({ 
                    method: "POST",
					url: "<?php echo ENLACE_WEB; ?>mail_sys/mail/ajax/eliminarArchivo.php",
					data : {
						idAdjunto : x
					},
                    beforeSend: function(xhr){
                    
                }
				}).done(function(data) 
                { 
					alert(data);
                    cargarListaAdjuntos();
                });
			}	
	}
	
	
	
	
	function estado_adjunto(x){
		
		if( $('#adjuntar_archivo_'+x).prop('checked') ) {
			valor=1;
		} else {
			valor=0;
		}

		$.ajax({ 
                    method: "POST",
					url: "<?php echo ENLACE_WEB; ?>mail_sys/mail/ajax/actualiza_adjunto.php",
					data : {
						idAdjunto : x , valor : valor
					},
                    beforeSend: function(xhr){
                    
                }
				}).done(function(data) 
                { 
					//alert(data);
                    cargarListaAdjuntos();
                });
		
	}
	</script> 
 
<script>
	cargarListaAdjuntos();
</script>          
</body>
</html>
   
	
  