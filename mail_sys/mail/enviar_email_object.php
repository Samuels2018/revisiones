<?php
include_once(ENLACE_SERVIDOR . "mail_sys/mail/PHPMailer/src/EnviarCorreoSmtp.php");

class EnviarEmail extends  Seguridad
{
	public $db;
	public function __construct($db, $entidad = 1)
	{
		parent::__construct($db, $entidad);
		$this->db          = $db;
	}

	function enviar($Documento, $asunto, $para, $cuerpo, $usuario, $pdf_adjunto = 0, $pdf_adjunto_contenido = '', $xml_adjunto = 0, $archivo_php_origen)
	{
		$array = explode(",", $para);
		if (count($array) > 1) {
			foreach ($array as $correos) {
				//$mail->addAddress( trim($correos) );  
			}
		} else {
			//	$mail->addAddress( trim($para) );
		}
		//ENVIAR A VARIOS CC SEPARADOS POR ,
		if (!empty($cc)) {
			$array2 = explode(",", $cc);
			if (count($array2) > 1) {
				foreach ($array2 as $correos2) {
					//$mail->AddCC( trim($correos2) );  
				}
			} else {
				//$mail->AddCC( trim($cc) );
			}
		}

		////////////////////////////////ADJUNTAR ARCHIVOS/////////////////////////
		$attachments = [];

		///// FACTURA PDF
		if ($pdf_adjunto == 1) {
			$adjunto = array(
				'archivo_base64' => $pdf_adjunto_contenido,
				'archivo_nombre' => $Documento->nombre_clase . "-" . $Documento->id . ".pdf",
				'archivo_tipo' => 'base64',
				'archivo_MIME' => 'pdf'
			);
			$attachments[] = $adjunto;
		}

		/// RESPUESTA XML
		if ($xml_adjunto == 1) {
			$date = strtotime($Documento->fecha);
			$month = date('m', $date);
			$year = date('Y', $date);

			$filePath = ENLACE_SERVIDOR_FILES_XML . "{$Documento->entidad}/{$Documento->nombre_clase}/" . $year . "/{$Documento->referencia}.xml";
			$file = $filePath;

			if (file_exists($file)) {
				$adjunto = array(
					'archivo_base64' => (file_get_contents($file)),
					'archivo_nombre' => $Documento->referencia . ".xml",
					'archivo_tipo' => 'base64',
					'archivo_MIME' => 'xml'
				);
				$attachments[] = $adjunto;
			}
		}

		$debug = 0;

		$respuesta = Email_SMPT($this->db, $cuerpo, $para, $attachments, $archivo_php_origen, $asunto, $debug);
		$error = $respuesta['error'];

		if (!empty($cco)) {
			$mail->AddBCC($cco, '');
		}

		//send the message, check for errors
		if ($error) {
			$Documento->registrar_log_documento($usuario, 1, "Error al enviar el/la " . $Documento->nombre_clase . " al correo correo  " . $para . " ");
			$forma = "Ocurrio Un Error (" . $respuesta['error_txt'] . ")";
			$sql = "INSERT INTO `email_log`(`email_enviado`,`fk_factura`, `detalle`, `codigo`, `fecha`) VALUES (:email_enviado, :fk_factura ,:detalle, :codigo, now())";
			$db4 = $this->db->prepare($sql);
			$db4->bindValue(':email_enviado', $para, PDO::PARAM_INT);
			$db4->bindValue(':fk_factura', $Documento->id, PDO::PARAM_INT);
			$db4->bindValue(':detalle', $forma, PDO::PARAM_STR);
			$db4->bindValue(':codigo', 1, PDO::PARAM_INT);
			$db4->execute();
		} else {

			$forma = 'Correo Enviado de Manera Correcta';
			$Documento->registrar_log_documento($usuario, 1, "Se envio un correo electronico a " . $para . " de el/la " . $tipo . " ");
			$correosEnviados = "Correos Enviado de Manera Manual a: " . $para;
			$sql = "INSERT INTO `email_log`(`email_enviado`,`fk_factura`, `detalle`, `codigo`, `fecha`) VALUES (:email_enviado, :fk_factura ,:detalle, :codigo, now())";
			$db4 = $this->db->prepare($sql);
			$db4->bindValue(':email_enviado', $para, PDO::PARAM_STR);
			$db4->bindValue(':fk_factura', $Documento->id, PDO::PARAM_INT);
			$db4->bindValue(':detalle', $correosEnviados, PDO::PARAM_STR);
			$db4->bindValue(':codigo', 1, PDO::PARAM_INT);
			$db4->execute();
		}
	}
}