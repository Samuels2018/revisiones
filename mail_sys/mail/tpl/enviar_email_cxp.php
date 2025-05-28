<?php
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
 $array=array();
foreach ($idCXP as $key => $value) {
  $array[$key]=$value['id'];
}
$ids=implode(',', $array);
$sql= "select  *  from sistema_tipo_cambio order by rowid DESC   "  ; 
    $db = $dbh->prepare($sql);
    $db->execute();
    $dato = $db->fetch(PDO::FETCH_OBJ);
    $tipo_cambio = $dato->valor;
    $sql= "select   
                    f.* 
                ,   t.nombre 
                /*,   sl.origen
                ,   sl.destination
                ,   c.etiqueta  as categoria_servicio_txt*/

                ,   t.rowid as TerceroID
                ,   d.consecutivo as referencia

                /*,   sl.consecutive*/
                ,   t.email
 

                    from  cuentas_por_pagar         f 
 
                    left  join      fi_terceros_proveedores             t  on t.rowid = f.fk_proveedor 
                    left join   documentos_recibidos    d  on d.rowid=f.fk_documento 
                    /*left  join      documentos_sl           sl on sl.rowid = f.fk_file  
                    left  join      diccionario_categoria   c on c.rowid = sl.fk_categoria_servicio  */

 


                    where 

                    /*f.cuenta_pagada   = 0 

                    and f.estado_hacienda = 'aceptado'*/
                    
                    f.rowid IN ($ids) 

                    order by   f.rowid         "  ; 

                        
             $db = $dbh->prepare($sql);
             //$db->bindValue(":rowid" , $idCXP , PDO::PARAM_INT);
             $db->execute();
             $tr = "" ; 

            $ArrayTercero       = array();
            /*$ArrayTerceroNombre=array();
            $ArrayTerceroExtranjero='';
            $email=array();*/

            $totalDolar=0;
            $totalColon=0;
            $i=1;
            while ($dato = $db->fetch(PDO::FETCH_OBJ)){ 
            	//if($i==1){
              if(is_null($dato->fk_documento) || $dato->fk_documento==''){}
                else{
                $ArrayTercero[$dato->TerceroID]['nombre']= $dato->nombre;
                $ArrayTercero[$dato->TerceroID]['email']= $dato->email;
               /* $ArrayTerceroExtranjero= $dato->extranjero;
                $email['email']=$dato->email;
                $coma=',';
                if($dato->email_tramitador==''){$coma='';}
                $email['extras']=$dato->email_tramitador.$coma.$dato->email_contador;
            }*/


 




                //$nuevafecha = strtotime ( '+'.((int)$dato->dias_credito).'  day' , strtotime (  $dato->fecha   ) ) ;
                $date1 = new DateTime( date('Y-m-d', strtotime($dato->fecha_documento)) );
                $date2 = new DateTime( date('Y-m-d', strtotime($dato->fecha_vencimiento)) );
                $diff = $date1->diff($date2);
                // will output 2 days
                $dias =  $diff->days . ' d&iacute;as ';
                $alarma ="";
                $vencimiento=date('d-m-Y', strtotime($dato->fecha_vencimiento));



         
 


                

                $TOTAL        [ $dato->moneda ]+= $dato->monto;
                $TOTAL_ABONADO[ $dato->moneda ]+= $dato->monto_pagado;


                //----------------------------------------------------------
                //
                //          Datos para Graficos 
                //
                //----------------------------------------------------------
                $pendiente = $dato->monto  -  $dato->monto_pagado;     
            
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
                if ($dato->tipo_documento=="simplificada"){
                        $tipo_documento = "Simplificada";
                        $color          = "danger ";

                } else {
                        $tipo_documento = $dato->tipo_documento;
                        $color          = "primary ";
                }

                if($dato->tipo_documento=='saldo'){
                  $referencia='Saldo '.$dato->rowid;
                }
                else{
                  $referencia=$dato->referencia;
                }
                $ArrayTercero[$dato->TerceroID]['tr'][$i]='<tr>
                <td style="text-align: center;">
                    '.$referencia.'
                </td>
               

                <td style="text-align: center;">
                    '.date('d-m-Y', strtotime($dato->fecha_documento)).'
                </td>
            

                ';
                $abono=$idCXP[$dato->rowid]['abono'];
                $saldo=$dato->monto-$dato->monto_pagado;
                $tot=$abono+$saldo;
                if($dato->moneda=='USD'){
                	$ArrayTercero[$dato->TerceroID]['tdolar']=$ArrayTercero[$dato->TerceroID]['tdolar']+$tot;
                  $ArrayTercero[$dato->TerceroID]['adolar']=$ArrayTercero[$dato->TerceroID]['adolar']+$abono;
                  $ArrayTercero[$dato->TerceroID]['sdolar']=$ArrayTercero[$dato->TerceroID]['sdolar']+$saldo;
                  $totalDolarC='$'.number_format($tot ,2, ',','.');
                  $abonoDolarC='$'.number_format($abono ,2, ',','.');
                  $saldoDolarC='$'.number_format($saldo ,2, ',','.');
                }else{
                	$ArrayTercero[$dato->TerceroID]['tdolar']=$ArrayTercero[$dato->TerceroID]['tdolar']+0;
                  $ArrayTercero[$dato->TerceroID]['adolar']=$ArrayTercero[$dato->TerceroID]['adolar']+0;
                  $ArrayTercero[$dato->TerceroID]['sdolar']=$ArrayTercero[$dato->TerceroID]['sdolar']+0;
                	$totalDolarC='$0,00';
                  $abonoDolarC='$0,00';
                  $saldoDolarC='$0,00';
                }
                $ArrayTercero[$dato->TerceroID]['tr'][$i].='<td style="text-align: center;">'.$totalDolarC.'</td>';
                $ArrayTercero[$dato->TerceroID]['tr'][$i].='<td style="text-align: center;">'.$abonoDolarC.'</td>';
                $ArrayTercero[$dato->TerceroID]['tr'][$i].='<td style="text-align: center;">'.$saldoDolarC.'</td>';
                if($dato->moneda=='CRC'){
                	$ArrayTercero[$dato->TerceroID]['tcolon']=$ArrayTercero[$dato->TerceroID]['tcolon']+$tot;
                  $ArrayTercero[$dato->TerceroID]['acolon']=$ArrayTercero[$dato->TerceroID]['acolon']+$abono;
                  $ArrayTercero[$dato->TerceroID]['scolon']=$ArrayTercero[$dato->TerceroID]['scolon']+$saldo;
                	//$ArrayTercero[$dato->TerceroID]['tr'][$i].='₡'.number_format($dato->monto ,2, ',','.');
                  $totalColonC='₡'.number_format($tot ,2, ',','.');
                  $abonoColonC='₡'.number_format($abono ,2, ',','.');
                  $saldoColonC='₡'.number_format($saldo ,2, ',','.');
                }else{
                	$ArrayTercero[$dato->TerceroID]['tcolon']=$ArrayTercero[$dato->TerceroID]['tcolon']+0;
                  $ArrayTercero[$dato->TerceroID]['acolon']=$ArrayTercero[$dato->TerceroID]['acolon']+0;
                  $ArrayTercero[$dato->TerceroID]['scolon']=$ArrayTercero[$dato->TerceroID]['scolon']+0;
                	//$ArrayTercero[$dato->TerceroID]['tr'][$i].='₡0,00';
                  $totalColonC='₡0,00';
                  $abonoColonC='₡0,00';
                  $saldoColonC='₡0,00';
                }
                $ArrayTercero[$dato->TerceroID]['tr'][$i].='<td style="text-align: center;">'.$totalColonC.'</td>';
                $ArrayTercero[$dato->TerceroID]['tr'][$i].='<td style="text-align: center;">'.$abonoColonC.'</td>';
                $ArrayTercero[$dato->TerceroID]['tr'][$i].='<td style="text-align: center;">'.$saldoColonC.'</td>';

				$ArrayTercero[$dato->TerceroID]['tr'][$i].='<td style="text-align: center;">
                    '.$vencimiento.'
                </td>
            </tr>';
            $i++;
          }
            }

                    ?>
                    
  