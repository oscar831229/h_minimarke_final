<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * HfosMessage
 *
 * Clase para estructurar un mensaje interno del correo de la aplicación
 */
class HfosMessage extends UserComponent {

	private $_toRecipients = array();

	private $_ccRecipients = array();

	private $_subject = '';

	private $_body = '';

	/**
	 * Quién envía el mensaje
	 *
	 * @var string
	 */
	private $_from = false;

	/**
	 * Busca usuarios del front en una lista de usuarios
	 */
	private function _parseList($list){
		$parsedList = array();
		if(trim($list)){
			$hostName = HfosMail::getHostName();
			$elements = preg_split('/,[ ]*/', $list);
			foreach($elements as $element){
				$element = trim($element);
				$element = str_replace("\\\"", '', $element);
				$element = str_replace('"', '', $element);
				if(preg_match('/([a-z\.\-\_A-Z0-9]+)@([a-z\.\-\_A-Z0-9]+)/', $element, $matches)){
					if(preg_match('/\.hfos$/', $matches[0])){
						$usuarioFront = $this->UsuariosFront->findFirst("login = '{$matches[1]}' AND estado='A'");
						if($usuarioFront==false){
							throw new HfosMailException('No existe el usuario con correo interno "'.$matches[0].'"');
						} else {
							$email = '"'.$usuarioFront->getNombre().'" <'.$usuarioFront->getLogin().'@'.$hostName.'.hfos>';
							$parsedList[] = $email;
						}
					} else {
						throw new HfosMailException('El correo externo no está habilitado para enviar a "'.$matches[0].'"');
					}
				} else {
					if($element){
						if(preg_match('/ \[perfil\]$/', $element)){
							$perfilNombre = preg_replace('/ \[perfil\]$/', '', $element);
							$perfilFront = $this->PerfilesFront->findFirst("detalle='$perfilNombre'");
							if($perfilFront==false){
								throw new HfosMailException('No existe el perfil "'.$perfilNombre.'"');
							} else {
								foreach($this->UsuariosFront->find("codprf='{$perfilFront->getCodprf()}' AND estado='A'") as $usuarioFront){
									$email = '"'.$usuarioFront->getNombre().'" <'.$usuarioFront->getLogin().'@'.$hostName.'.hfos>';
									$parsedList[] = $email;
								}
							}
						} else {
							$usuarioFront = $this->UsuariosFront->findFirst("nombre LIKE '$element' AND estado='A'");
							if($usuarioFront==false){
								throw new HfosMailException('No existe el usuario "'.$element.'"');
							} else {
								$email = '"'.$usuarioFront->getNombre().'" <'.$usuarioFront->getLogin().'@'.$hostName.'.hfos>';
								$parsedList[] = $email;
							}
						}
					}
				}
			}
		}
		return $parsedList;
	}

	/**
	 * Establece la lista de destinatarios del mensaje
	 *
	 * @param array $list
	 */
	public function setToList($list){
		$this->_toRecipients = array_merge($this->_toRecipients, $this->_parseList($list));
	}

	/**
	 * Establece la lista de usuarios a ser copiados
	 *
	 * @param array $list
	 */
	public function setCcList($list){
		$this->_ccRecipients = array_merge($this->_ccRecipients, $this->_parseList($list));
	}

	/**
	 * Establece un usuario arbitrario quién envia el mensaje
	 *
	 * @param string $from
	 */
	public function setFrom($from){
		$this->_from = $from;
	}

	/**
	 * Establece el asunto del mensaje
	 *
	 * @param string $subject
	 */
	public function setSubject($subject){
		$this->_subject = $subject;
	}

	/**
	 * Establece el cuerpo del mensaje
	 *
	 * @param string $body
	 */
	public function setBody($body){
		$this->_body = $body;
	}

	/**
	 * Realiza la grabación del mensaje en los respectivos mailbox
	 *
	 */
	public function send(){

		$this->_toRecipients = array_unique($this->_toRecipients);
		$this->_ccRecipients = array_unique($this->_ccRecipients);

		$listAll = array_merge($this->_toRecipients, $this->_ccRecipients);

		$hostName = HfosMail::getHostname();
		$usuarioFront = IdentityManager::getFrontUser();

		if($this->_from==false){
			$email = '<'.$usuarioFront->getLogin().'@'.$hostName.'.hfos>';
			$nombreUsuario = $usuarioFront->getNombre();
			$codigoUsuario = $usuarioFront->getCodusu();
			$fromText = '"'.$usuarioFront->getNombre().'"';
		} else {
			$codigoUsuario = 1;
			if(preg_match('/([a-z\.\-\_A-Z0-9]+)@([a-z\.\-\_A-Z0-9]+)/', $this->_from, $matches)){
				$nombreUsuario = trim(str_replace(array($matches[0], '>', '<'), '', $this->_from));
				$fromText = '"'.$nombreUsuario.'"';
				$email = '<'.$matches[0].'>';
			} else {
				$nombreUsuario = 'Sistema';
				$email = '<admin@localhost.hfos>';
				$fromText = '"Sistema"';
			}
		}

		$fecha = date('r');
		$messageId = $this->Mail->maximum('id')+1;

		$controllerRequest = ControllerRequest::getInstance();

		$headers = array();
		$version = Hfos_Application::getVersion();
		$headers['Received'] = 'from localhost (verified [localhost]) by '.$hostName.'.hfos (HFOS '.$version.') with WebDav id 20 for '.$email.'; '.$fecha;
		$headers['Return-Path'] = $email;
		$headers['Date'] = $fecha;
		$headers['To'] = join(', ', $this->_toRecipients);
		$headers['Cc'] = join(', ', $this->_ccRecipients);
		$headers['From'] = '"'.$nombreUsuario.'" '.$email;
		$headers['Subject'] = $this->_subject;
		$headers['Message-ID'] = '<'.md5($messageId).'@hfos>';
		$headers['X-Priority'] = 2;
		$headers['X-Mailer'] = 'HOTEL FRONT-OFFICE SOLUTION '.$version;
		$headers['MIME-Version'] = '1.0';
		$headers['Content-Type'] = 'text/html';
		$headers['X-Server'] = 'HOTEL FRONT-OFFICE SOLUTION '.$version.' Internal Mail Server ('.Hfos_Application::APP_CODE_NAME.')';
		$headers['X-Authenticated-User'] = $email;
		$headers['X-Rcpt-To'] = $email;
		$headers['X-External-IP'] = $controllerRequest->getClientAddress();
		$headers['Status'] = 'U';
		$headers['X-UIDL'] = '1210694148.2000_33.localhost';

		$mailHeader = array();
		foreach($headers as $name => $value){
			$mailHeader[] = $name.': '.$value;
		}
		$headers = join("\r\n", $mailHeader);

		$preview = strip_tags($this->_body);

		if($this->_subject==''){
			$this->_subject = '(Sin Asunto)';
		}

		$size = i18n::strlen($headers)+i18n::strlen($this->_subject)+i18n::strlen($preview)+i18n::strlen($this->_body);

		foreach($listAll as $itemList){
			if(preg_match('/([a-z\.\-\_A-Z0-9]+)@([a-z\.\-\_A-Z0-9]+)/', $itemList, $matches)){
				$userFront = $this->UsuariosFront->findFirst("login='{$matches[1]}'");
				if($userFront!=false){

					$mail = new Mail();
					$mail->setDbname($userFront->getSchema());
					$mail->setMailbox('inbox');
					$mail->setUsusen($codigoUsuario);
					$mail->setCodusu($userFront->getCodusu());
					$mail->setFromMsg($fromText);
					$mail->setHeaders($headers);
					$mail->setTimsen(time());
					$mail->setSubject($this->_subject);
					$mail->setPreview($preview);
					$mail->setMessage($this->_body);
					$mail->setPriority(2);
					$mail->setAsize($size);
					$mail->setHattach(0);
					$mail->setType('I');
					$mail->setStatus('U');
					if($mail->save()==false){
						foreach($mail->getMessages() as $message){
							throw new HfosMailException($message->getMessage());
						}
					}

				}
			}
		}

		if($this->_from==false){
			$mail = new Mail();
			$mail->setDbname($userFront->getSchema());
			$mail->setMailbox('sent');
			$mail->setUsusen($userFront->getCodusu());
			$mail->setCodusu($userFront->getCodusu());
			$mail->setFromMsg('"'.$usuarioFront->getNombre().'"');
			$mail->setHeaders($headers);
			$mail->setTimsen(time());
			$mail->setSubject($this->_subject);
			$mail->setPreview($preview);
			$mail->setMessage($this->_body);
			$mail->setPriority(2);
			$mail->setAsize($size);
			$mail->setHattach(0);
			$mail->setType('I');
			$mail->setStatus('R');
			if($mail->save()==false){
				foreach($mail->getMessages() as $message){
					throw new HfosMailException($message->getMessage());
				}
			}
		}

	}

}