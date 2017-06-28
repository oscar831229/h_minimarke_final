<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Back-Office
 * @copyright	BH-TECK Inc. 2009-2014
 * @version		$Id: delivery.php,v b2eca735ff08 2012/07/24 01:20:33 andres $
 */

if (!defined("JASMIN_SYSTEM_CAPTION")) {
	$dathot = EntityManager::get("Dathot")->findFirst();
	define("JASMIN_SYSTEM_CAPTION", $dathot->getNombre());
	define("JASMIN_DARK_COLOR", "#EAEAEA");
	define("JASMIN_DRAW_COLOR", "#ABABAB");
	define("JASMIN_LIGHT_COLOR", "#F2F2F2");
	define("JASMIN_NORMAL_COLOR", "#FAFAFA");
	define("JASMIN_NUMBER_COLOR", "#CC0000");
	define("JASMIN_TEXT_COLOR", "#111111");
}

class HfosDelivery {

	static private $_lastError = '';

	static private $_lastResponse = array();

	static private $_lastMailUID = null;

	private static function _doTransport($action, $post=array())
	{
		try {

			$http = new HttpRequest('http://delivery.bhteck.net/'.$action, HttpRequest::METH_POST);
			
			$http->setOptions(array('timeout' => 60, 'connecttimeout' => 30, 'dns_cache_timeout' => 30));
			$http->setPostFields($post);
			$http->send();
			if ($http->getResponseCode()==200) {
				$response = json_decode($http->getResponseBody(), true);
				self::$_lastResponse = $response;
				if (isset($response['status'])) {
					if ($response['status']!='OK') {
						self::$_lastError = $response['message'];
						return false;
					} else {
						return true;
					}
				} else {
					self::$_lastError = $http->getResponseBody();
					return false;
				}
			} else {
				self::$_lastError = $http->getResponseBody();
				return false;
			}

		} catch(HttpInvalidParamException $e) {
			self::$_lastError = 'No se pudo realizar el envío, el servidor puede no tener acceso a Internet';
			return false;
		}

	}

	private static function _getRelayKey()
	{

		ini_set('default_socket_timeout', 60);

		$delivery = EntityManager::get("Delivery")->findFirst();
		if ($delivery==false) {

			$dathot = EntityManager::get("Dathot")->findFirst();

			$colors = array();
			$colors[] = array('type' => 'dark', 'value' => JASMIN_DARK_COLOR);
			$colors[] = array('type' => 'draw', 'value' => JASMIN_DRAW_COLOR);
			$colors[] = array('type' => 'light', 'value' => JASMIN_LIGHT_COLOR);
			$colors[] = array('type' => 'normal', 'value' => JASMIN_NORMAL_COLOR);
			$colors[] = array('type' => 'number', 'value' => JASMIN_NUMBER_COLOR);
			$colors[] = array('type' => 'text', 'value' => JASMIN_TEXT_COLOR);

			$success = self::_doTransport('relay/register', array(
				'nit' => $dathot->getNit(),
				'nombre' => $dathot->getNombre(),
				'nombreComercial' => JASMIN_SYSTEM_CAPTION,
				'razonSocial' => $dathot->getNomcad(),
				'direccion' => $dathot->getDireccion(),
				'telefono' => $dathot->getTelefono(),
				'ciudad' => $dathot->getNomciu(),
				'departamento' => $dathot->getNomdep(),
				'sitioWeb' => $dathot->getSitweb(),
				'email' => $dathot->getEmail(),
				'colors' => json_encode($colors),
				'logo' => base64_encode(file_get_contents( KEF_ABS_PATH . 'public/img/logo.jpg'))
			));

			if ($success==true) {
				$response = self::getLastResponse();
				$relayKey = filter_alpha($response['relayKey']);
				$delivery = new Delivery();
				$delivery->setRelayKey($relayKey);
				$delivery->setCreatedAt(date("Y-m-d"));
				$delivery->save();
			}

			if (function_exists('http_persistent_handles_clean')) {
				http_persistent_handles_clean();
			}
		} else {
			$relayKey = $delivery->getRelayKey();
		}

		return $relayKey;
	}

	public static function send($type, $to, $subject, $body, $from='', $extra=array())
	{

		$relayKey = self::_getRelayKey();
		if ($relayKey==false) {
			return false;
		}

		$attachments = null;
		if (isset($extra['attachments'])) {
			foreach($extra['attachments'] as $name => $file) {
				$attachments[] = array(
					'name' => $name,
					'content' => base64_encode(file_get_contents($file))
				);
			}
			$attachments = json_encode($attachments);
		}

		$relayParams = array(
			'relayKey' => $relayKey,
			'type' => $type,
			'to' => $to[0],
			'toName' => $to[1],
			'subject' => $subject,
			'body' => base64_encode($body),
			'attachments' => $attachments
		);
		if (isset($extra['senderToken'])) {
			$relayParams['senderToken'] = $extra['senderToken'];
		}
		if (isset($extra['template'])) {
			$relayParams['template'] = $extra['template'];
		}

		$success = self::_doTransport('relay/send', $relayParams);
		if ($success) {
			$response = self::getLastResponse();
			self::$_lastMailUID = $response['mailUID'];
			return true;
		} else {
			return false;
		}
	}

	public static function getSentStatus($mailUIDs)
	{

		$relayKey = self::_getRelayKey();
		if ($relayKey==false) {
			return false;
		}

		$success = self::_doTransport('relay/getSendStatus', array(
			'relayKey' => $relayKey,
			'mailUID' => $mailUIDs
		));

		if ($success==true) {
			$response = self::getLastResponse();
			return $response['mailStatuses'];
		} else {
			return false;
		}
	}

	public static function getMailUID() {
		return self::$_lastMailUID;
	}

	/**
	 * Sends a campaign to more than one recipient
	 *
	 * @param	int $campaignCode
	 * @param	array $recipients
	 * @param	string $subject
	 * @param	string $url
	 * @param	string $text
	 * @param	string $file
	 * @param	string $from
	 * @return	boolean
	 */
	public static function sendCampaign($campaignCode, $recipients, $subject, $url, $text, $file, $from='')
	{

		$relayKey = self::_getRelayKey();
		if ($relayKey==false) {
			return false;
		}

		$request = array();

		$path = 'temp/'.$file.'.jpg';
		if (file_exists($path)) {
			$request['file'] = array(
				'name' => $file,
				'content' => base64_encode(file_get_contents($path))
			);
		} else {
			self::$_lastError = 'La imagen de la campaña no se pudo adjuntar';
			return false;
		}

		$request['campaignId'] = $campaignCode;
		$request['subject'] = $subject;
		$request['url'] = $url;
		$request['text'] = $text;
		$request['recipients'] = $recipients;
		$request['from'] = $from;

		return self::_doTransport('relay/sendCampaign', array(
			'relayKey' => $relayKey,
			'request' => $request
		));

	}


	public static function getCampaignStat($campaignCode)
	{
		$relayKey = self::_getRelayKey();
		if ($relayKey==false) {
			return false;
		}
		$success = self::_doTransport('campaign/getStat', array(
			'relayKey' => $relayKey,
			'campaignCode' => $campaignCode
		));
		if ($success==true) {
			$response = self::$_lastResponse;
			return $response['stats'];
		} else {
			error(self::$_lastError);
			return false;
		}
	}

	public static function sendInvoice($email, $prefac, $numfac, $name, $files, $formatFile='facturaHotel')
	{
		$path = KEF_ABS_PATH . "Library/Hfos/Delivery/Formats/" . $formatFile. ".php";
		if (file_exists($path)) {
			require_once $path;
		} else {
			throw new Exception("No existe el formato a enviar por correo " . $path, 1);
		}
		return self::send('F', array($email, $name), $subject, $body, null, array(
			'attachments' => $files
		));
	}

	public static function sendCartera($email, $name, $prefac, $numfac, $files)
	{
		require_once 'hfos/cartera.php';
		$subject = 'Factura en '.JASMIN_SYSTEM_CAPTION.' [FV'.$prefac.sprintf('%06s', $numfac).']';
		$body = Hfos_Cartera::getCarta($prefac, $numfac);
		$body.='La factura está en formato PDF, para visualizarla es necesario tener instalado Acrobat Reader ó un programa similar, para descargar
la factura haga click en el siguiente enlace:<br/><br/>
%attachment-1%<br/><br/>';
		return self::send('F', array($email, $name), $subject, $body, null, array(
			'attachments' => $files
		));
	}

	public static function getLastError()
	{
		return self::$_lastError;
	}

	public static function getLastResponse()
	{
		return self::$_lastResponse;
	}

}
