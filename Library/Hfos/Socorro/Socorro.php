<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Socorro
 *
 * Componente para reportar
 *
 */
class Socorro extends UserComponent
{

	public static function getBody($info, $subject, $text)
	{

		return '
		<!DOCTYPE html>
		<html lang="es" dir="ltr">
		<head>
			<meta charset="utf-8">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		</head>
		<body>
			<table cellspacing="0" width="100%">
				<tr>
					<td colspan="2" bgcolor="#292931">
						<img src="http://www.bhteck.com/img/magenta.png?'.$subject.'" alt="Magenta Support System" width="320" height"70"/>
					</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;" width="25%">
					<b>Producto</b></td>
					<td style="border:1px solid #635265">'.$info['appName'].'</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;">
					<b>Versión</b></td>
					<td style="border:1px solid #635265">'.$info['appVersion'].'</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;">
					<b>NIT Cliente</b></td>
					<td style="border:1px solid #635265">'.$info['nit'].'</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;">
					<b>Nombre Cliente</b></td>
					<td style="border:1px solid #635265">'.$info['nombreCliente'].'</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;">
					<b>IP Usuario Interna</b></td>
					<td style="border:1px solid #635265">'.$_SERVER['REMOTE_ADDR'].'</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;">
					<b>Usuario</b></td>
					<td style="border:1px solid #635265">'.$info['usuarioNombre'].'</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;">
					<b>Script PHP</b></td>
					<td style="border:1px solid #635265">'.$_SERVER['PHP_SELF'].'</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;">
					<b>Fecha Servidor</b></td>
					<td style="border:1px solid #635265">'.date('r').'</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#E62163" style="color:#fff;" valign="top">
					<b>MENSAJES</b></td>
					<td style="border:1px solid #635265"><pre>'.$text.'</pre></td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;" valign="top">
					<b>Datos GET</b></td>
					<td style="border:1px solid #635265"><pre>'.htmlentities(print_r($_GET, true), ENT_COMPAT, 'UTF-8').'</pre></td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;" valign="top">
					<b>Datos POST</b></td>
					<td style="border:1px solid #635265"><pre>'.htmlentities(print_r($_POST, true), ENT_COMPAT, 'UTF-8').'</pre></td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;" valign="top">
					<b>Datos SESSION</b></td>
					<td style="border:1px solid #635265"><pre>'.utf8_encode(print_r($_SESSION, true)).'</pre></td>
				</tr>
				<tr>
					<td align="right" bgcolor="#292931" style="color:#fff;" valign="top">
					<b>Datos SERVER</b></td>
					<td style="border:1px solid #635265"><pre>'.print_r($_SERVER, true).'</pre></td>
				</tr>
				<tr>
					<td colspan="2" bgcolor="#ffffff" style="border:1px solid #635265">
						<b>Notas</b>
						<ul>
							<li>No se adjuntó raw-data del problema para inspección</li>
							<li>El usuario NO creó un punto de recuperación</li>
						</ul>
					</td>
				</tr>
			</table>
		</body>
		</html>';
	}

	private static function _getInfo($empresa)
	{
		$identity = IdentityManager::getActive();
		$usuarioNombre = $identity['apellidos'].' '.$identity['nombres'];
		return array(
			'appName'       => CoreConfig::getAppSetting('name'),
			'appVersion'    => Hfos_Application::getVersion(CoreConfig::getAppSetting('code')),
			'nit'           => $empresa->getNit(),
			'nombreCliente' => $empresa->getNombre(),
			'usuarioNombre' => $usuarioNombre
		);
	}

	public static function sendReportFromClient($subject, $text)
	{
		$empresa = BackCacher::getEmpresa();
		$info = self::_getInfo($empresa);
		$subject = substr(md5($empresa->getNit().$text), 0, 20);
		$text = htmlentities($text);
		return self::sendEmail($subject, self::getBody($info, $subject, $text));
	}

	/**
	 * Envia un reporte de error a soporte
	 *
	 * @param CoreException $e
	 */
	public static function sendReport($e)
	{
		switch (get_class($e)) {
			case 'GardienException':
			case 'AuraException':
			case 'TaticoException':
				return false;
				break;
		}
		if (PHP_OS != 'Darwin') {
			$empresa = BackCacher::getEmpresa();
			$message = get_class($e).': '.$e->getMessage().' Code='.$e->getCode().' Line='.$e->getLine().' File='.$e->getFile();
			$subject = $empresa->getNit() . ' ' . $message;
			$subject = substr(md5($subject), 0, 20);
			$text = $message . "\n" . print_r($e->getTrace(), true);
			$info = self::_getInfo($empresa);
			return self::sendEmail($subject, self::getBody($info, $subject, $text));
		}
	}

	public static function sendEmail($subject, $body)
	{

		Core::importFromLibrary('Swift', 'Swift.php');
		Core::importFromLibrary('Swift', 'Swift/Connection/SMTP.php');

		$message = new Swift_Message('Magenta HFOS Error [' . $subject . ']');
		$recipients = new Swift_RecipientList();

		$bodyMessage = new Swift_Message_Part($body, 'text/html');
		$bodyMessage->setCharset('utf-8');
		$message->attach($bodyMessage);

		$recipients->addTo('hugo.ramirez@bhteck.com', 'Hugo Ramirez');
		//$recipients->addTo('andres.gutierrez@bhteck.com', 'Andres Felipe Gutierrez');
		$recipients->addTo('eduar.carvajal@bhteck.com', 'Eduar Carvajal');

		$response = ControllerResponse::getInstance();

		try {
			$smtp = new Swift_Connection_SMTP('smtp.gmail.com', Swift_Connection_SMTP::PORT_SECURE, Swift_Connection_SMTP::ENC_TLS);
			$smtp->setUsername('bhtechsoporte@gmail.com');
			$smtp->setPassword('2fe051873');
			$swift = new Swift($smtp);
			$swift->send($message, $recipients, new Swift_Address('bhtechsoporte@gmail.com', 'bhtechsoporte'));

			$response->setHeader('X-Socorro-Reported: yes');
		} catch (Exception $me) {

			$response = ControllerResponse::getInstance();
			$response->setHeader('X-Socorro-Reported: no');
			$response->setHeader('X-Socorro-Reason: email-problem');
			$response->setHeader('X-Socorro-File: '.Core::getInstancePath().'temp/'.$subject.'.bin');

			$fp = fopen('public/temp/'.$subject.'.bin', 'w');
			if (function_exists('mcrypt_module_open')) {
				$body = preg_replace("/[\t]+/", "", $body);
				$key = 'en%2Fe051873v';
				$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
				fwrite($fp, mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $body, MCRYPT_MODE_ECB, $iv));
			} else {
				fwrite($fp, base64_encode($body));
			}
			fclose($fp);

		}

	}

	public static function exceptionHandler()
	{
		if (Router::isInitialized() == true) {
			Core::importFromActiveApp('controllers/socorro_controller.php');
			Router::routeTo(array('controller' => 'socorro', 'action' => 'index'));
			$socorroController = new SocorroController();
			$socorroController->setResponse('');
			$socorroController->setControllerName('socorro');
			$socorroController->setActionName('index');
			$socorroController->setAllParameters(Router::getAllParameters());
			$socorroController->setParameters(Router::getParameters());
			Dispatcher::setController($socorroController);
			View::setRenderLevel(View::LEVEL_MAIN_VIEW);
			View::handleViewRender($socorroController);
		}
	}

}
