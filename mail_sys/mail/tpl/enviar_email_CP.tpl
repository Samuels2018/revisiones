<?php
session_start();
require("../../../conf/conf.php");

$id=$_GET['idCliente'];
 function fecha_hoy(){
  switch (date('m')) {
    case 1:
      $mes='Enero';
      break;
    case 2:
      $mes='Febrero';
      break;
    case 3:
      $mes='Marzo';
      break;
    case 4:
      $mes='Abril';
      break;
    case 5:
      $mes='Mayo';
      break;
    case 6:
      $mes='Junio';
      break;
    case 7:
      $mes='Julio';
      break;
    case 8:
      $mes='Agosto';
      break;
    case 9:
      $mes='Setiembre';
      break;
    case 10:
      $mes='Octubre';
      break;
    case 11:
      $mes='Noviembre';
      break;
    case 12:
      $mes='Diciembre';
      break;
  }
  $fecha=date('d').' de '.$mes.' de '.date('Y');
  return $fecha;
 }
	/*require_once(ENLACE_SERVIDOR.'mod_electronico/object/factura.class.php');
	$factura2	=	 new documentoElectronico($dbh);
	$factura2->fetch($id);*/


 
	
	/*$titulo="Enviar  <i class='fa fa-fw fa-money'></i> <b> Factura ".$factura2->referencia."</b>   Por Email";	
	$empresa=$factura2->tercero;
	$texto='<br><br><p>Te envio la informacion respecto a la factura  '.$factura2->referencia.', <br><br>
	</p>';
		
	$adjunto="<b> Factura ".$factura2->consecutivo."</b>";*/
	

	
	/*$sql = "Select email, email_contador, email_tramitador from fi_terceros where rowid = ".$factura2->tercero;
	$dbTercero = $dbh->prepare($sql);
	$dbTercero->execute();
	$terceroEmail = $dbTercero->fetch(PDO::FETCH_OBJ);
	
	
	$correosExtras = "";
	
	if($terceroEmail->email_contador != ""){
		$correosExtras .= $terceroEmail->email_contador; 
	}else{
		$correosExtras .= "";
	}
	
	if($terceroEmail->email_tramitador != ""){
		if($correosExtras != ""){
			$correosExtras .= ",".$terceroEmail->email_tramitador; 
		}else{
			$correosExtras .= $terceroEmail->email_tramitador; 
		}
	}else{
		$correosExtras .= "";
	}	
	
 
$sql = "Select * from documentos_sl_adjuntos where fk_documento = ".$factura2->fk_file;
$db = $dbh->prepare($sql);
$db->execute();

while($adjuntos = $db->fetch(PDO::FETCH_OBJ)){
$tr .= "
	<tr>
		<td><input type='checkbox' name='documento_".$adjuntos->rowid."' ></td>
		<td>".$adjuntos->archivo."</td>
	</tr>
";
	
} 


/////////////PREPARANDO LOS DATOS PARA EL ASUNTO Y EL CUERPO DE CORREO 
 
$sql = "Select body_correos from fi_configuracion_empresa where rowid = 7";
$db = $dbh->prepare($sql);
$db->execute();
$cuerpoCorreo = $db->fetch(PDO::FETCH_OBJ);

if($factura2->moneda == 1){
	$monedita = "CRC";
}else{
	$monedita = "USD";
}*/
$sql= "select  *  from sistema_tipo_cambio order by rowid DESC   "  ; 
    $db = $dbh->prepare($sql);
    $db->execute();
    $dato = $db->fetch(PDO::FETCH_OBJ);
    $tipo_cambio = $dato->valor;
    $sql= "select   
                    f.* 
                ,   t.nombre 
                ,   sl.origen
                ,   sl.destination
                ,   c.etiqueta  as categoria_servicio_txt

                ,   t.rowid as TerceroID

                ,   sl.consecutive
                ,   t.email
                ,   t.email_tramitador
                ,   t.email_contador
                ,   t.extranjero
 

                    from  fi_facturas         f 
 
                    left  join      fi_terceros             t  on t.rowid = f.fk_tercero  
                    left  join      documentos_sl           sl on sl.rowid = f.fk_file  
                    left  join      diccionario_categoria   c on c.rowid = sl.fk_categoria_servicio  

 


                    where 

                    f.estado_pagada   = 0 

                    and f.estado_hacienda = 'aceptado'
                    
                    and f.fk_tercero = :fk_tercero 

                    order by   f.rowid         "  ; 

                        
             $db = $dbh->prepare($sql);
             $db->bindValue(":fk_tercero" , $_GET['idCliente'] , PDO::PARAM_INT);
             $db->execute();
             $tr = "" ; 

            $ArrayTercero       = array();
            $ArrayTerceroNombre='';
            $ArrayTerceroExtranjero='';
            $email=array();

            $totalDolar=0;
            $totalColon=0;
            $i=1;
            while ($dato = $db->fetch(PDO::FETCH_OBJ)){ 
            	if($i==1){
                $ArrayTerceroNombre= $dato->nombre;
                $ArrayTerceroExtranjero= $dato->extranjero;
                $email['email']=$dato->email;
                $coma=',';
                if($dato->email_tramitador==''){$coma='';}
                $email['extras']=$dato->email_tramitador.$coma.$dato->email_contador;
            }


 




                $nuevafecha = strtotime ( '+'.((int)$dato->dias_credito).'  day' , strtotime (  $dato->fecha   ) ) ;
                $nuevafecha2 = strtotime (  $dato->fecha_vencimiento   )  ;
                $date1 = new DateTime( date('Y-m-d') );
                $date2 = new DateTime( date('Y-m-d', $nuevafecha) );
                $diff = $date1->diff($date2);
                // will output 2 days
                $dias =  $diff->days . ' days ';
                $alarma ="";



         
 


                

                $TOTAL        [ $dato->moneda ]+= $dato->TotalComprobante;
                $TOTAL_ABONADO[ $dato->moneda ]+= $dato->pagado;


                //----------------------------------------------------------
                //
                //          Datos para Graficos 
                //
                //----------------------------------------------------------
                $pendiente = $dato->TotalComprobante  -  $dato->pagado;     
            
                if ( $nuevafecha > strtotime(date('Y-m-d'))  ) {

                                if ( $dias >= 0  and $dias < 31 ){
                                        $PorVencer['30_dias'][$dato->moneda]+=$pendiente ;                                          


                                }   else if ( $dias >= 31  and $dias < 60 ){
                                        $PorVencer['60_dias'][$dato->moneda]+=$pendiente ;
                                       


                                }   else if ( $dias >= 61  and $dias < 90 ){
                                        $PorVencer['90_dias'][$dato->moneda]+=$pendiente ;


                                }   else if ( $dias >= 91   ){
                                        $PorVencer['91_dias'][$dato->moneda]+=$pendiente ;
                             
                                }    

 $style="style='color:blue;' ";
  $alarma = '';

                }    else {

                                  if ( $dias >= 0  and $dias < 31 ){
                                        $Vencido['30_dias'][$dato->moneda]+=$pendiente ;
                                         $ArrayTercero[$dato->TerceroID]['30_dias'][$dato->moneda]+=$pendiente;
                                       

                                }   else if ( $dias >= 31  and $dias < 61 ){
                                        $Vencido['60_dias'][$dato->moneda]+=$pendiente ;
                                        $ArrayTercero[$dato->TerceroID]['60_dias'][$dato->moneda]+=$pendiente;


                                }   else if ( $dias >= 61  and $dias < 91 ){
                                        $Vencido['90_dias'][$dato->moneda]+=$pendiente ;
                                        $ArrayTercero[$dato->TerceroID]['90_dias'][$dato->moneda]+=$pendiente;



                                }   else if ( $dias >= 91   ){
                                        $Vencido['91_dias'][$dato->moneda]+=$pendiente ;
                                         $ArrayTercero[$dato->TerceroID]['91_dias'][$dato->moneda]+=$pendiente;
                               
                                }    



                                $style="style='color:red;' "; $alarma ='<i class="fa fa-fw fa-warning" ></i>'; 
 

                }
                if ($dato->tipo_documento=="ticket"){
                        $tipo_documento = " Ticket";
                        $color          = "danger ";

                } else {
                        $tipo_documento = $dato->tipo_documento;
                        $color          = "primary ";
                }

                if($dato->tipo_documento=='saldo'){
                  $referencia='Saldo '.$dato->rowid;
                  $nuevafecha=$nuevafecha2;
                }
                else{
                  $referencia=$dato->referencia;

                }
                $tr.='<tr>
                <td style="text-align: center;">
                    '.$referencia.'
                </td>
               

                <td style="text-align: center;">
                    '.date('d-m-Y', strtotime($dato->fecha)).'
                </td>
            

                ';
                $tr.='<td style="text-align: center;">';
                if($dato->moneda=='USD'){
                	$totalDolar=$totalDolar+$dato->TotalComprobante;
                	$tr.='$'.number_format($dato->TotalComprobante ,2, ',','.');
                }else{
                	$totalDolar=$totalDolar+0;
                	$tr.='$0,00';
                }
                $tr.='</td>
                <td style="text-align: center;">';
                if($dato->moneda=='CRC'){
                	$totalColon=$totalColon+$dato->TotalComprobante;
                	$tr.='₡'.number_format($dato->TotalComprobante ,2, ',','.');
                }else{
                	$totalColon=$totalColon+0;
                	$tr.='₡0,00';
                }

				$tr.='<td style="text-align: center;">
                    '.date('d/m/Y', ($nuevafecha)).'
                </td>
            </tr>';
            $i++;
            }

$asunto="SOA S&L - ".$ArrayTerceroNombre." - ".fecha_hoy(); 

$resultado = str_replace("%NOMBRECLIENTE%", $factura2->nombre_txt, 								$cuerpoCorreo->body_correos);
$resultado = str_replace("%CONSECUTIVO%", 	$factura2->consecutivo, 							$resultado);
$resultado = str_replace("%MONEDA%", 		$monedita, 											$resultado);
$resultado = str_replace("%MONTO%",			numero_simple($factura2->TotalComprobante),					$resultado);
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

    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media='print' />

    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
 
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    
  </head>
  <body class="skin-blue" style="padding:15px!important;">
 <div class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        
      </div>
      <div class="modal-body">
        <p>Cargando...</p>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<section class="content">

 <div class="row">
<form id="enviar_correo" action="<?php echo ENLACE_WEB; ?>mail_sys/mail/enviar_email_logico_CC.php" method="POST" onsubmit="funcionCarga()">


<input type="hidden" name="id" 	   value="<?php echo $_REQUEST['idCliente']?>" />


 <div class="col-md-9">
              <div class="box box-primary">
                <div class="box-header with-border">
                </div>
                <div class="box-body">
                  
                  <div class="form-group">
                    Destinatario: <input class="form-control"   name="para" placeholder="Para:" value="<?php echo $email['email']; ?>"/>
                  </div>

 

                  <div class="form-group">
                    CC: <input class="form-control" placeholder="CC:" name="cc" value="<?php echo $email['extras'];?>"/>
                  </div>


				
				
                  <div class="form-group">
                    Asunto: <input class="form-control" placeholder="Asunto:"  name="asunto"  value="<?php echo $asunto; ?>" />
                  </div>
                 

                  <div class="form-group">
                    Detalle: <textarea id="compose-textarea" class="form-control" style="height: 300px;font-family: arial;font-size:10pt;" name="texto">
                      <?php if($ArrayTerceroExtranjero==0) {?>
                      <p style="font-family: arial;font-size:10pt;">Estimados (as): <?php echo $ArrayTerceroNombre;?>
                      <br><br>
                      Se detalla el estado de cuenta actualizado para su referencia y revisión. 
                      <br><br>
                      Agradecemos su pronta ayuda con el comprobante de pago de las facturas vencidas para realizar la aplicación en el sistema. </p>
                      <br>
                     <table class="table  table-striped" style="font-family: arial;font-size:10pt;" width="70%" border="0">
                     	<thead>
                     		<tr>
                     			<td colspan="6" style="background-color: transparent;"><img src="<?php echo ENLACE_WEB; ?>bootstrap/images/logoSourcing_blanco.png" width="25%"></td>
                     		</tr>
                     		<tr>
                     			<th colspan="6" style="text-align: left;">ESTADO DE CUENTA</th>
                     		</tr>
                     		<tr style="border: none;">
                     			<th colspan="" style="border: none;text-align: left;">CLIENTE</th>
                     			<td colspan="5" style="border: none;text-align: left;"><?php echo $ArrayTerceroNombre;?></td>
                     		</tr>
                     		<tr style="border: none;">
                     			<th colspan="" style="border: none;text-align: left;">FECHA</th>
                     			<td colspan="5" style="border: none;text-align: left;"><?php echo fecha_hoy();?></td>
                     		</tr>
                     		<tr  style="background-color: #CCC">
                     		<th scope="col" style="text-align: center;" width="10%">FACTURA</th>
                        <th scope="col" style="text-align: center;" width="10%">FECHA</th>
                        <th scope="col" style="text-align: center;" width="5%">USD</th>
                        <th scope="col" style="text-align: center;" width="5%">CRC</th>
                        <th scope="col" style="text-align: center;" width="15%">VENCIMIENTO</th>
                     	</tr>
                     	</thead>
                    <tbody>

                <?php echo $tr;   ?>
                <tr style="border-top:2px solid #000000;">
                	<td></td>
                	<th style="text-align: right;">TOTAL</th>
                	<th style="text-align: center;background-color: #CCC"><?php echo '$'.number_format($totalDolar ,2, ',','.');?></th>
                	<th style="text-align: center;background-color: #CCC"><?php echo '₡'.number_format($totalColon ,2, ',','.');?></th>
                	<td></td>
                </tr>
</tbody>
        </table>
        <br><br>
        <p style="font-family: arial;font-size:10pt;">Su pago electrónico por favor efectuarlo a la siguiente cuenta:<br>
                      Banco:  Banco de Costa Rica<br>
                      Swift:  BCRICRSJ<br>
                      Numero de cuenta: 00103104885<br>
                        
                      IBAN #: CR37015201001031048859<br>
                      <br>

                      Favor mantener una sana relación respecto al término de crédito aprobado.   
                      <br><br><br>
                      Cordialmente</p>
					   <?php } else{ ?>
              <p style="font-family: arial;font-size:10pt;">Dear Customer: <?php echo $ArrayTerceroNombre;?>
                      <br><br>
                      Please review your account statement and advise payment status for due invoices. 
                      <br><br>
                      We look forward to your prompt response. </p>
                      <br>
                     <table class="table  table-striped" style="font-family: arial;font-size:10pt;" width="70%" border="0">
                      <thead>
                        <tr>
                          <td colspan="6" style="background-color: transparent;"><img src="<?php echo ENLACE_WEB; ?>bootstrap/images/logoSourcing_blanco.png" width="25%"></td>
                        </tr>
                        <tr>
                          <th colspan="6" style="text-align: left;">STATEMENT OF ACCOUNT </th>
                        </tr>
                        <tr style="border: none;">
                          <th colspan="" style="border: none;text-align: left;">COSTUMER</th>
                          <td colspan="5" style="border: none;text-align: left;"><?php echo $ArrayTerceroNombre;?></td>
                        </tr>
                        <tr style="border: none;">
                          <th colspan="" style="border: none;text-align: left;">DATE</th>
                          <td colspan="5" style="border: none;text-align: left;"><?php echo fecha_hoy();?></td>
                        </tr>
                        <tr  style="background-color: #CCC">
                        <th scope="col" style="text-align: center;" width="10%">INVOICE</th>
                        <th scope="col" style="text-align: center;" width="10%">DATE</th>
                        <th scope="col" style="text-align: center;" width="5%">USD</th>
                        <th scope="col" style="text-align: center;" width="5%">CRC</th>
                        <th scope="col" style="text-align: center;" width="15%">DUE DATE</th>
                      </tr>
                      </thead>
                    <tbody>

                <?php echo $tr;   ?>
                <tr style="border-top:2px solid #000000;">
                  <td></td>
                  <th style="text-align: right;">TOTAL</th>
                  <th style="text-align: center;background-color: #CCC"><?php echo '$'.number_format($totalDolar ,2, ',','.');?></th>
                  <th style="text-align: center;background-color: #CCC"><?php echo '₡'.number_format($totalColon ,2, ',','.');?></th>
                  <td></td>
                </tr>
</tbody>
        </table>
        <br><br>
        <p style="font-family: arial;font-size:10pt;">See below the bank details:<br>
                      Banco:  Banco de Costa Rica<br>
                      Swift:  BCRICRSJ<br>
                      Numero de cuenta: 00103104885<br>
                        
                      IBAN #: CR37015201001031048859<br>
                      <br>

                      Thank you for your business - We will be more than glad to assist you with any inquiries or concerns you may have. 
                      <br><br><br>
                       Sincerely  </p>

                    <?php } ?>
                    <br><br>
                    <img src="<?php echo ENLACE_WEB; ?>bootstrap/images/firmaCorreo.png" width="25%">
                    </textarea>
                  </div>
                  
		<div class="form-group">
			<hr><small>Adjuntar documentos</small><br>
			
			<table>
			<tr>
					<td colspan="2">
							<form method="post" id="formulario" enctype="multipart/form-data">
								<input type="file" id="files" name="files" onchange="upload_image()" />
							</form>
								<div id="respuesta1">-</div>
								
							</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="adjuntos2">
							<?php 
							echo '<table width="100%">';
 	$sql1="Select * from documentos_CC_adjuntos_email where fk_cliente = ".$_GET['idCliente']."";
	$db_ad=$dbh->prepare($sql1);
	$db_ad->execute();
		while($docu=$db_ad->fetch(PDO::FETCH_OBJ)){
				$checked='checked';
				if($docu->adjuntar==0){
					$checked='';
				}
				echo '<tr><td><input type="checkbox" value="'.$docu->rowid.'" onclick="atChec(this)" '.$checked.'></td><td>'.$docu->archivo.'</td><td><i onclick="eliminarAdjunto('.$docu->rowid.')" class="btn btn-success">Eliminar</i></td></tr>'; 
		}
		echo '</table>'; ?>
						</div>
					</td>
				</tr>
			</table>
			
		</div>


		</div>
		<div class="box-footer">
		  <div class="pull-right">
			 <button type="submit" class="btn btn-primary" name="enviar" id="enviar" value="true" ><i class="fa fa-envelope-o"></i> Enviar</button>
		  </div>
		  <button onClick="self.close();return false;"  class="btn btn-default"><i class="fa fa-times"  ></i> Cerrar </button>
		  <?php //echo ENLACE_WEB; ?>
		</div>
	  </div>
	</div>


           </form> 
</div>
</section>

    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/jQuery/jQuery-2.1.3.min.js"></script>
    
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
   
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
   
  
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/dist/js/app.min.js" type="text/javascript"></script>
    
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/dist/js/demo.js" type="text/javascript"></script>
    
    <script src="<?php echo ENLACE_WEB; ?>bootstrap1/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <script src="<?php echo ENLACE_WEB ?>mail_sys/mail/tinymce/js/tinymce/tinymce.min.js"></script>
	
	
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
			url: "<?php echo ENLACE_WEB; ?>mail_sys/mail/ajax/subirAdjuntoCC.php?idDoc=<?php echo $_GET['idCliente']; ?>",        
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
				$("#adjuntos2").empty();
                $("#adjuntos2").html('<i class="fas fa-cog fa-spin fa-5x fa-fw" ></i>');
				
				$.ajax({ 
                    method: "POST",
					url: "<?php echo ENLACE_WEB; ?>mail_sys/mail/ajax/cargarAdjuntosCC.php",
					data : {
						idDoc : <?php echo $_GET['idCliente']; ?>,
					},
                    beforeSend: function(xhr){
                    
                }
				}).done(function(data) 
                { 
                    $("#adjuntos2").empty(); 
                    $("#adjuntos2").html(data); 
                });
	}
	function atChec(x){
	if($(x).prop("checked") == true){activa=1}
		else{activa=0;}
	idAdj=$(x).val();
	$.ajax({ 
                    method: "POST",
					url: "<?php echo ENLACE_WEB; ?>mail_sys/mail/ajax/editarAdjuntoCC.php",
					data : {
						idAdjunto : idAdj,activo:activa
					},
                    beforeSend: function(xhr){
                    
                }
				}).done(function(data) 
                { 
                    
                });
	}
	function eliminarAdjunto(x){
		var result = confirm("Desea eliminar este archivo del servidor? Esta accion no se puede deshacer.");
			if(result == true){
				$.ajax({ 
                    method: "POST",
					url: "<?php echo ENLACE_WEB; ?>mail_sys/mail/ajax/eliminarAdjuntoCC.php",
					data : {
						idAdjunto : x
					},
                    beforeSend: function(xhr){
                    
                }
				}).done(function(data) 
                { 
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
	function funcionCarga(){
		$(".modal").modal("show");
	}
	</script> 
 
<script>
	//cargarListaAdjuntos();
</script>          
</body>
</html>
   
	
  