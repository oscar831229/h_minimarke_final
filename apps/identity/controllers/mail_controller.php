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

class MailController extends ApplicationController {

	public function getMessageCountAction(){
		$this->setResponse('json');
		return array(
			'status' => 'OK',
			'unread' => HfosMail::getUnreadCount(),
			'inbox' => HfosMail::getInboxCount(),
			'trash' => HfosMail::getTrashCount(),
		);
	}

	public function getInboxAction(){
		$this->setResponse('view');
	}

	public function getComposeAction(){
		$this->setResponse('view');

		$this->setParamToView('mail', null);

		$action = $this->getPostParam('action', 'alpha');
		if($action=='answer'||$action=='forward'){
			$messageId = $this->getPostParam('messageId', 'alpha');
			if($messageId!==''){
				$usuarioFront = IdentityManager::getFrontUser();
				$mail = $this->Mail->findFirst("sid='$messageId' AND codusu='{$usuarioFront->getCodusu()}'");
				if($mail==false){
					Flash::error('No existe el mensaje '.$messageId);
				} else {
					$this->setParamToView('mail', $mail);
				}
			}
		}

		$this->setParamToView('action', $action);
	}

	public function getSentAction(){
		$this->setResponse('view');
	}

	public function getTrashAction(){
		$this->setResponse('view');
	}

	public function openMessageAction($messageId=null){

		$this->setResponse('view');
		try {

			$messageId = $this->getPostParam('sid', 'alpha');
			if($messageId==''){
				Flash::error('No existe el mensaje');
				return;
			}

			$usuarioFront = IdentityManager::getFrontUser();

			$mail = $this->Mail->findFirst("sid='$messageId' AND codusu='{$usuarioFront->getCodusu()}'");
			if($mail==false){
				Flash::error('No existe el mensaje '.$messageId);
				return;
			}

			$this->setParamToView('mail', $mail);
			$this->setParamToView('messageId', $messageId);

		}
		catch(IdentityManagerException $e){
			Flash::error($e->getMessage());
		}
	}

	public function readMessageAction($messageId=null){
		$this->setResponse('view');

		try {

			$messageId = $this->filter($messageId, 'alpha');
			if($messageId==''){
				Flash::error('No existe el mensaje');
				return;
			}

			$usuarioFront = IdentityManager::getFrontUser();

			$mail = $this->Mail->findFirst("sid='$messageId' AND codusu='{$usuarioFront->getCodusu()}'");
			if($mail==false){
				Flash::error('No existe el mensaje '.$messageId);
				return;
			}

			if($mail->getStatus()=='U'){
				$mail->setStatus('R');
				if($mail->save()==false){
					foreach($mail->getMessages() as $message){
						Flash::error('Mail: '.$message->getMessage());
					}
				}
			}

			$this->setParamToView('mail', $mail);
			$this->setParamToView('messageId', $messageId);

		}
		catch(IdentityManagerException $e){
			Flash::error($e->getMessage());
		}
	}

	public function markReadAction(){
		try {
			$this->setResponse('json');
			$messages = $this->getPostParam('messages', 'alpha');
			if(count($messages)){

				$usuarioFront = IdentityManager::getFrontUser();

				$transaction = TransactionManager::getUserTransaction();
				$this->Mail->setTransaction($transaction);

				foreach($messages as $messageId){
					$mail = $this->Mail->findFirst("sid='$messageId' AND codusu='{$usuarioFront->getCodusu()}'");
					if($mail==false){
						$transaction->rollback('No existe el mensaje '.$messageId);
					}
					if($mail->getStatus()!='R'){
						$mail->setStatus('R');
						if($mail->save()==false){
							foreach($mail->getMessages() as $message){
								$transaction->rollback('Mail: '.$message->getMessage());
							}
						}
					}
				}

				$transaction->commit();

			}
			return array(
				'status' => 'OK'
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(IdentityManagerException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function markUnreadAction(){
		try {
			$this->setResponse('json');
			$messages = $this->getPostParam('messages', 'alpha');
			if(count($messages)){

				$usuarioFront = IdentityManager::getFrontUser();

				$transaction = TransactionManager::getUserTransaction();
				$this->Mail->setTransaction($transaction);

				foreach($messages as $messageId){
					$mail = $this->Mail->findFirst("sid='$messageId' AND codusu='{$usuarioFront->getCodusu()}'");
					if($mail==false){
						$transaction->rollback('No existe el mensaje '.$messageId);
					}
					if($mail->getStatus()!='U'){
						$mail->setStatus('U');
						if($mail->save()==false){
							foreach($mail->getMessages() as $message){
								$transaction->rollback('Mail: '.$message->getMessage());
							}
						}
					}
				}

				$transaction->commit();

			}
			return array(
				'status' => 'OK'
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(IdentityManagerException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function deleteAction(){
		try {
			$this->setResponse('json');
			$messages = $this->getPostParam('messages', 'alpha');
			if(count($messages)){

				$usuarioFront = IdentityManager::getFrontUser();

				$transaction = TransactionManager::getUserTransaction();
				$this->Mail->setTransaction($transaction);

				foreach($messages as $messageId){
					$mail = $this->Mail->findFirst("sid='$messageId' AND codusu='{$usuarioFront->getCodusu()}'");
					if($mail==false){
						$transaction->rollback('No existe el mensaje '.$messageId);
					}
					if($mail->getMailbox()!='trash'){
						$mail->setMailbox('trash');
						if($mail->save()==false){
							foreach($mail->getMessages() as $message){
								$transaction->rollback('Mail: '.$message->getMessage());
							}
						}
					}
				}

				$transaction->commit();

			}
			return array(
				'status' => 'OK'
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(IdentityManagerException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function restoreAction(){
		try {
			$this->setResponse('json');
			$messages = $this->getPostParam('messages', 'alpha');
			if(count($messages)){

				$usuarioFront = IdentityManager::getFrontUser();

				$transaction = TransactionManager::getUserTransaction();
				$this->Mail->setTransaction($transaction);

				foreach($messages as $messageId){
					$mail = $this->Mail->findFirst("sid='$messageId' AND codusu='{$usuarioFront->getCodusu()}'");
					if($mail==false){
						$transaction->rollback('No existe el mensaje '.$messageId);
					}
					if($mail->getMailbox()!='inbox'){
						$mail->setMailbox('inbox');
						if($mail->save()==false){
							foreach($mail->getMessages() as $message){
								$transaction->rollback('Mail: '.$message->getMessage());
							}
						}
					}
				}

				$transaction->commit();

			}
			return array(
				'status' => 'OK'
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(IdentityManagerException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function deleteDefinitiveAction(){
		try {

			$this->setResponse('json');
			$messages = $this->getPostParam('messages', 'alpha');
			if(count($messages)){

				$usuarioFront = IdentityManager::getFrontUser();

				$transaction = TransactionManager::getUserTransaction();
				$this->Mail->setTransaction($transaction);

				foreach($messages as $messageId){
					$mail = $this->Mail->findFirst("sid='$messageId' AND codusu='{$usuarioFront->getCodusu()}'");
					if($mail==false){
						$transaction->rollback('No existe el mensaje '.$messageId);
					}
					if($mail->getStatus()!='D'){
						$mail->setStatus('D');
						if($mail->save()==false){
							foreach($mail->getMessages() as $message){
								$transaction->rollback('Mail: '.$message->getMessage());
							}
						}
					}
				}

				$transaction->commit();
			}
			return array(
				'status' => 'OK'
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(IdentityManagerException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function getDirectoryAction(){
		$this->setResponse('view');
		echo '<ul>';
		$id = $this->getPostParam('id', 'extraspaces', 'striptags');
		foreach($this->UsuariosFront->find("nombre LIKE '%$id%' AND estado='A'") as $usuarioFront){
			echo '<li>"', ucwords(i18n::strtolower($usuarioFront->getNombre())), '"</li>';
		}
		foreach($this->PerfilesFront->find("detalle LIKE '%$id%'") as $perfilFront){
			echo '<li>"', ucwords(i18n::strtolower($perfilFront->getDetalle())), ' [perfil]"</li>';
		}
		echo '</ul>';
	}

	public function deliveryAction(){
		$this->setResponse('json');

		try {

			$toList = $this->getPostParam('to');
			$ccList = $this->getPostParam('cc');
			$subject = $this->getPostParam('subject', 'striptags', 'extraspaces');
			$content = $this->getPostParam('content');

			$message = new HfosMessage();
			$message->setToList($toList);
			$message->setCcList($ccList);
			$message->setSubject($subject);
			$message->setBody($content);
			$message->send();

		}
		catch(HfosMailException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		return array(
			'status' => 'OK'
		);
	}

}