<?php
  //ini_set('display_errors', 1);
  //ini_set('display_startup_errors', 1);
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;


    function Email_SMPT($dbh,$cuerpo=null , $destinatarios=NULL , $adjuntos=NULL ,$modulo=NULL, $subject,$debug=0, $id=1, $setfrom = "Factuguay.es",$frontemail="")
    {  
    


        $sql= "SELECT * FROM configuracion_php_mailer where id = $id"; 
        $db = $dbh->prepare($sql);
        $db->execute();
        $dato = $db->fetch(PDO::FETCH_OBJ);
      //var_dump($dato);
       // die();
        //mail_sys/mail/PHPMailer/src/EnviarCorreoSmtp.php
        
            require_once(ENLACE_SERVIDOR."mail_sys/mail/PHPMailer/src/PHPMailer.php");
            require_once(ENLACE_SERVIDOR."mail_sys/mail/PHPMailer/src/Exception.php");
            require_once(ENLACE_SERVIDOR."mail_sys/mail/PHPMailer/src/SMTP.php");
             $mail = new PHPMailer(true);

             $response='';
             $codigo='';
             $error  = false;
             $error_txt='';

    try {
        //Server settings
        $mail->SMTPDebug = 1;
        $mail->SMTPDebug = $debug;                    //Enable verbose debug output
        $mail->isSMTP();                         //Send using SMTP
        $mail->Host       = $dato->Host;        //Set the SMTP server to send through
        $mail->SMTPAuth   =  true;              //Enable SMTP authentication
        $mail->Username   = $dato->Username;     //SMTP username
        $mail->Password   = $dato->Password;   //SMTP password
        $mail->Port       = $dato->Port;     //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->Subject = $subject;
        //Recipients
     
        if($frontemail == '')
        {
            $mail->setFrom($dato->FromEmail,  $setfrom);
        }else{
            $mail->setFrom($frontemail,$setfrom);
        }
       
        $array = explode (",", $destinatarios); 

        if (is_array($array)){

            foreach( $array  as $destinatario) {  
                $destinatario= trim($destinatario);
                if (!empty($destinatario)) { 
                    $mail->addAddress($destinatario );//Add a recipient
                } 
                
            } 
        }else{
            $mail->addAddress($destinatarios);//Add a recipient
        }

        //Attachments
        if (!empty( $adjuntos)){

            foreach ($adjuntos as $adjunto) { 
            $mail->AddStringAttachment($adjunto['archivo_base64'] ,$adjunto['archivo_nombre'],$adjunto['archivo_tipo'],$adjunto['archivo_MIME']); 
            } 
         }
         //Attachments

        //Content
        $mail->isHTML(true);  //Set email format to HTML
        $mail->CharSet = 'utf-8';
        $mail->msgHTML(($cuerpo));

            if ($mail->send()) {
                //echo 'Message has been sent';
                    $codigo =202;
            }else{
                
                $codigo =400;
                $error=true;
                $error_txt =$mail->ErrorInfo;

                //Guardando en log error
               /* $query  = "INSERT INTO email_log_error SET 
                fecha_creacion=:fecha_creacion, 
                codigo_error =:codigo_error, 
                detalle =:detalle,
                modulo =:modulo";
                
                $db    = $dbh->prepare($query);
                $db->bindValue(':fecha_creacion', date("Y-m-d H:i:s"), PDO::PARAM_STR);
                $db->bindValue(':codigo_error', $codigo     , PDO::PARAM_STR);
                $db->bindValue(':detalle', $error_txt     , PDO::PARAM_STR);
                $db->bindValue(':modulo', $modulo     , PDO::PARAM_STR);
                $db->execute();*/
            }

       
        } catch (Exception $e) {
            $codigo =400;
            $error=true;
            $error_txt =$mail->ErrorInfo;
            
                //Guardando en log error
                /*$query  = "INSERT INTO email_log_error SET 
                fecha_creacion=:fecha_creacion, 
                codigo_error =:codigo_error, 
                detalle =:detalle,
                modulo =:modulo";
                
                $db    = $dbh->prepare($query);
                $db->bindValue(':fecha_creacion', date("Y-m-d H:i:s"), PDO::PARAM_STR);
                $db->bindValue(':codigo_error', $codigo     , PDO::PARAM_STR);
                $db->bindValue(':detalle', $error_txt     , PDO::PARAM_STR);
                $db->bindValue(':modulo', $modulo     , PDO::PARAM_STR);
                $db->execute();*/
        }

        $respuesta = array();
        $respuesta['codigo']    = $codigo; 
        $respuesta['error']     = $error; 
        $respuesta['error_txt'] = $error_txt; 
        
        return $respuesta;    
    }


    //FUncion para SMTP PERO  CONFIGURADO UN CLIENTE
    function Email_SMPT_desde_cliente($host, $username, $password, $port,$cuerpo=null,$destinatarios=NULL , $adjuntos=NULL,$modulo=NULL,$subject,$debug=0)
    {
        require_once(ENLACE_SERVIDOR."mail_sys/mail/PHPMailer/src/PHPMailer.php");
        require_once(ENLACE_SERVIDOR."mail_sys/mail/PHPMailer/src/Exception.php");
        require_once(ENLACE_SERVIDOR."mail_sys/mail/PHPMailer/src/SMTP.php");
        $mail = new PHPMailer(true);
        $response='';
        $codigo='';
        $error  = false;
        $error_txt='';

    try {
        //Server settings
        $mail->SMTPDebug = 1;
        $mail->SMTPDebug = $debug;                    //Enable verbose debug output
        $mail->isSMTP();                         //Send using SMTP
        $mail->Host       = $host;        //Set the SMTP server to send through
        $mail->SMTPAuth   =  true;              //Enable SMTP authentication
        $mail->Username   = $username;     //SMTP username
        $mail->Password   = $password;   //SMTP password
        $mail->Port       = $port;     //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->Subject = $subject;
        //Recipients
        $mail->setFrom($username, "Factuguay.es");
        $array = explode (",", $destinatarios); 
        if (is_array($array)){
            foreach( $array  as $destinatario) {  
                $destinatario= trim($destinatario);
                if (!empty($destinatario)) { 
                    $mail->addAddress($destinatario );//Add a recipient
                } 
            } 
        }else{
            $mail->addAddress($destinatarios);//Add a recipient
        }

        //Attachments
        if (!empty( $adjuntos)){

            foreach ($adjuntos as $adjunto) { 
            $mail->AddStringAttachment($adjunto['archivo_base64'] ,$adjunto['archivo_nombre'],$adjunto['archivo_tipo'],$adjunto['archivo_MIME']); 
            } 
         }
         //Attachments
        //Content
        $mail->isHTML(true);  //Set email format to HTML
        $mail->CharSet = 'utf-8';
        $mail->msgHTML(($cuerpo));
            if ($mail->send()) {
                    $codigo =202;
            }else{
                $codigo =400;
                $error=true;
                $error_txt =$mail->ErrorInfo;
            }
        } catch (Exception $e) {
            $codigo =400;
            $error=true;
            $error_txt =$mail->ErrorInfo;
        }
        $respuesta = array();
        $respuesta['codigo']    = $codigo; 
        $respuesta['error']     = $error; 
        $respuesta['error_txt'] = $error_txt; 
        return $respuesta;    
    } 

?>