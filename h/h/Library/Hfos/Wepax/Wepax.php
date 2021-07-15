<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class WepaxException extends Exception {

}

class Wepax {

	protected static $_service = array();

	public static function isEnabled(){
		return CoreConfig::getAppSetting('wepax.enabled', 'wepax');
	}

	public static function isDebug(){
		return CoreConfig::getAppSetting('wepax.debug', 'wepax');
	}

	protected static function _setCookie($headers){
		foreach(explode("\n", $headers) as $line){
			if(strpos($line, ': ')){
				$header = explode(': ', $line);
				if($header[0]=='Set-Cookie'){
					$cookies = explode('; ', $header[1]);
					foreach($cookies as $cookie){
						$parts = explode('=', $cookie);
						if($parts[0]=='PHPSESSID'){
							$_SESSION['WEPAX_COOKIE'][$serviceName] = $parts[1];
							break;
						}
					}
				}
			}
		}
	}

	protected static function _getService($serviceName){
		if(!isset(self::$_service[$serviceName])){
			$wepaxServer = CoreConfig::getAppSetting('wepax.server', 'wepax');
			$serviceDefinition = array(
				'uri' => 'http://app-services',
				'location' => 'http://'.$wepaxServer.'/services/'.$serviceName,
				'trace' => true
			);
			self::$_service[$serviceName] = new SoapClient(null, $serviceDefinition);
			try {
				if(isset($_SESSION['WEPAX_COOKIE'][$serviceName])&&$_SESSION['WEPAX_COOKIE'][$serviceName]){
					self::$_service[$serviceName]->__setCookie('PHPSESSID', $_SESSION['WEPAX_COOKIE'][$serviceName]);
				} else {
					$wepaxKey = CoreConfig::getAppSetting('wepax.key', 'wepax');
					if(self::$_service[$serviceName]->startSession($wepaxKey)==true){
						$headers = self::$_service[$serviceName]->__getLastResponseHeaders();
						self::_setCookie($serviceName, $headers);
						if(isset($_SESSION['WEPAX_COOKIE'])){
							self::$_service[$serviceName]->__setCookie('PHPSESSID', $_SESSION['WEPAX_COOKIE'][$serviceName]);
						}
					} else {
						throw new WepaxException("Licencia de producto inválida");
					}
				}
				return self::$_service[$serviceName];
			}
			catch(SoapFault $e){
				if(self::isDebug()){
					$wepaxDebugTo = CoreConfig::getAppSetting('wepax.debug_to', 'wepax');
					if($wepaxDebugTo=='file'){
						$wepaxLogger = new Logger('File', 'wepax.debug.txt');
						$wepaxLogger->log($service->__getLastResponseHeaders());
						$wepaxLogger->log($service->__getLastResponse());
					} else {
						if($wepaxDebugTo=='stdout'){
							echo 'Last-Response-Headers: '.self::$_service[$serviceName]->__getLastResponseHeaders().PHP_EOL;
							echo 'Last-Response: '.self::$_service[$serviceName]->__getLastResponse().PHP_EOL;
						}
					}
				}
				throw new WepaxException($e->getMessage());
			}
		} else {
			return self::$_service[$serviceName];
		}
	}

	protected static function _getPaymentsService(){
		return self::_getService('payments');
	}

	public static function invokeMethod($methodName, $argument){
		$value = null;
		if(self::isEnabled()){
			try {
				$service = self::_getPaymentsService();
				try {
					$value = $service->$methodName($argument);
					if(self::isDebug()){
						$wepaxDebugTo = CoreConfig::getAppSetting('wepax.debug_to', 'wepax');
						if($wepaxDebugTo=='file'){
							$wepaxLogger = new Logger('File', 'wepax.debug.txt');
							$wepaxLogger->log($service->__getLastResponseHeaders());
							$wepaxLogger->log($service->__getLastResponse());
						} else {
							if($wepaxDebugTo=='stdout'){
								echo 'Last-Request-Headers: '.self::$_service->__getLastRequestHeaders().PHP_EOL;
								echo 'Last-Request: '.self::$_service->__getLastRequest().PHP_EOL;
								echo 'Last-Response-Headers: '.self::$_service->__getLastResponseHeaders().PHP_EOL;
								echo 'Last-Response: '.self::$_service->__getLastResponse().PHP_EOL;
							}
						}
					}
				}
				catch(SoapFault $e){
					if(self::isDebug()){
						$wepaxDebugTo = CoreConfig::getAppSetting('wepax.debug_to', 'wepax');
						if($wepaxDebugTo=='file'){
							$wepaxLogger = new Logger('File', 'wepax.debug.txt');
							$wepaxLogger->log($service->__getLastResponse());
						} else {
							if($wepaxDebugTo=='stdout'){
								echo 'Last-Response-Headers: '.self::$_service->__getLastResponseHeaders().PHP_EOL;
								echo 'Last-Response: '.self::$_service->__getLastResponse().PHP_EOL;
							}
						}
					}
					throw new WepaxException($e->getMessage());
				}
			}
			catch(WepaxException $e){
				$message = null;
				try {
					$message = self::$_service->getReason();
				}
				catch(SoapFault $e){
					$message = $e->getMessage();
				}
				unset($_SESSION['WEPAX_COOKIE']);
				self::$_service = null;
				throw new WepaxException($e->getMessage().$message);
			}
		}
		return $value;
	}

	public static function generateLocalTicket($type=''){
		return md5($type.uniqid().microtime(true));
	}

}