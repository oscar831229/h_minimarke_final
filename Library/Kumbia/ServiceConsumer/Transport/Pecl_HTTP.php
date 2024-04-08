<?php

/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category 	Kumbia
 * @category	Kumbia
 * @package 	ServiceConsumer
 * @subpackage 	Transport
 * @copyright	Copyright (c) 2008-2012 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @version 	$Id: Sockets.php 122 2010-02-11 19:09:18Z gutierrezandresfelipe $
 */

/**
 * Pecl_HTTPTransport
 *
 * Cliente para realizar peticiones HTTP usando la extensión de php pecl_http
 *
 * @category	Kumbia
 * @package 	ServiceConsumer
 * @subpackage 	Transport
 * @copyright	Copyright (c) 2008-2012 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @abstract
 */
class Pecl_HTTPTransport
{

	/**
	 * Host al que se le está realizando la petición
	 *
	 * @var string
	 */
	private $_host;

	/**
	 * Indica si se habilita el envío automático de las cookies recibidas
	 *
	 * @var boolean
	 */
	private $_enableCookies = false;

	/**
	 * Encabezados de la petición
	 *
	 * @var array
	 */
	private $_headers = array();

	/**
	 * Objeto de transporte HTTP
	 *
	 * @var HttpRequest
	 */
	private $_transport;

	/**
	 * Constructor del Pecl_HTTPCommunicator
	 *
	 * @param string $scheme
	 * @param string $host
	 * @param string $uri
	 * @param string $method
	 * @param int $port
	 */
	
	/*
	* Respuesta Nuevo adapter
	*
	*/
	private $response;
	
	private $url;
	
	private $data;
	
	private $method;

	private $header;
	 
	 
	 
	public function __construct($scheme, $host, $uri, $method, $port=80)
	{
		$url = $scheme.'://'.$host.'/'.$uri;
		$this->_transport = new http\Client\Request;
		$this->method = $method;
		if ($method == 'POST') {
			$this->_transport->setRequestMethod('POST');
		} else {
			$this->_transport->setRequestMethod('GET');
		}
		$this->_host = $host;
	}

	/**
	 * Establece un encabezado HTTP a la petición
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addHeader($name, $value)
	{
		$this->_headers[$name] = $value;
	}

	/**
	 * Establece varios encabezados HTTP
	 *
	 * @param array $headers
	 */
	public function setHeaders($headers)
	{
		$this->header = $headers;
	}

	/**
	 * Changes request transaction
	 *
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Establece el cuerpo HTTP de la petición
	 *
	 * @param string $rawBody
	 */
	public function setRawPostData($rawBody)
	{
		$this->data = $rawBody;
	}

	/**
	 * Envía la petición
	 *
	 */
	public function send()
	{


		$addCookies = false;

		if ($this->_enableCookies==true) {

			if (isset($_SESSION['KHC'][$this->_host])) {
				if (isset($_SESSION['KHC'][$this->_host]['time'])) {
					if ($_SESSION['KHC'][$this->_host]['time']>($_SERVER['REQUEST_TIME']-900)) {
						$addCookies = true;
					} else {
						unset($_SESSION['KHC'][$this->_host]);
					}
				} else {
					unset($_SESSION['KHC'][$this->_host]);
				}
			}

		}


		# DATOS
		$body = new http\Message\Body;
		$body->append($this->data);


		# OBJETO REQUEST
		$request = new http\Client\Request("POST", $this->url);
		$request->setHeaders($this->header);
		$request->setBody($body);
		$request->setOptions(array(
			'timeout' => 900,
			'connecttimeout' => 30,
			'dns_cache_timeout' => 30
		));
		
		# CLIENTE 
		$cliente = (new http\Client);

		if($addCookies)
			$cliente->setCookies($_SESSION['KHC'][$this->_host]['cookie']);

		$this->response = $cliente
			->enqueue($request)
			->send()
			->getResponse();


		$cookies = array();
		foreach ($this->response->getCookies() as $cookie) {
			foreach ($cookie->getCookies() as $name => $value) {
				$cookies[][$name] = $value;
			}
		}


		if($this->_enableCookies==true){
			if(!isset($_SESSION['KHC'][$this->_host])){

				if(count($cookies) >0 ){
					$_SESSION['KHC'][$this->_host] = array(
						'time' => $_SERVER['REQUEST_TIME'],
						'cookie' => $cookies[0]
					);
				}
			}
		}

		return true;

	}

	/**
	 * Devuelve el código HTTP de la respuesta a la petición
	 *
	 * @return int
	 */
	public function getResponseCode(){
		return $this->response->getResponseCode();
	}

	/**
	 * Devuelve el cuerpo HTTP de la respuesta a la petición
	 *
	 * @return string
	 */
	public function getResponseBody(){
		return $this->response->getBody()->toString();
	}

	/**
	 * Habilita el envio automático de las cookies recibidas
	 *
	 * @param boolean $enableCookies
	 */
	public function enableCookies($enableCookies){
		$this->_enableCookies = $enableCookies;
	}

	/**
	 * Graba en sesión las cookies de la petición
	 *
	 */
	public function getResponseCookies(){
		if($this->_enableCookies==true){
			$cookies = $this->_transport->getResponseCookies();
			if(count($cookies)){
				$_SESSION['KHC'][$this->_host] = array(
					'time' => $_SERVER['REQUEST_TIME'],
					'cookie' => $cookies[0]->cookies
				);
			}
		}
	}

}
