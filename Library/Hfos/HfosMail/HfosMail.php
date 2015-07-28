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
 * HfosMail
 *
 * Componente para manejar el acceso y envio de correo interno y externo
 *
 */
class HfosMail extends UserComponent {

	/**
	 * Servicio de Delivery
	 *
	 * @var WebServiceClient
	 */
	private static $_delivery = null;

	/**
	 * Obtiene el servicio de delivery ó devuelve uno existe si ya se ha obtenido
	 *
	 * @return WebServiceClient
	 */
	private static function _getDeliveryService(){
		if(self::$_delivery===null){
			self::$_delivery = IdentityManager::getAuthedService('identity.delivery');
		}
		return self::$_delivery;
	}

	/**
	 * Obtiene el nombre del host según el config.ini
	 *
	 * @return string
	 */
	public static function getHostname(){
		return CoreConfig::getAppSetting('hostname', 'hfos');
	}

	/**
	 * Obtiene un array con los encabezados asociados al correo
	 *
	 * @param	Mail $mail
	 * @param 	booelan $format
	 * @return	array
	 */
	public static function getHeaders($mail, $format=false){
		$headerMeta = array();
		foreach(explode("\n", $mail->getHeaders()) as $header){
			$meta = explode(':', $header);
			if(isset($meta[1])){
				$headerMeta[$meta[0]] = ltrim(htmlspecialchars($meta[1], ENT_COMPAT, 'UTF-8'));
				if(mb_strpos($meta[1], '@')!==false){
					if($format){
						$headerMeta[$meta[0]] = str_replace("&lt;", "<span style='font-size:11px;color:#969696'>&lt;", $headerMeta[$meta[0]]);
						$headerMeta[$meta[0]] = str_replace("&gt;", "&gt;</span>", $headerMeta[$meta[0]]);
					}
					$headerMeta[$meta[0]] = str_replace("&quot;", "", $headerMeta[$meta[0]]);
				}
			}
		}
		return $headerMeta;
	}

	public static function haveMail(){
		$usuarioFront = IdentityManager::getFrontUser();
		return $usuarioFront ? true : false;
	}

	public static function getUnreadCount(){
		$usuarioFront = IdentityManager::getFrontUser();
		$schema = $usuarioFront->getSchema();
		return self::getModel('Mail')->count(array("dbname='$schema' AND mailbox='inbox' AND codusu='{$usuarioFront->getCodusu()}' AND status='U'"));
	}

	public static function getInboxCount(){
		$usuarioFront = IdentityManager::getFrontUser();
		$schema = $usuarioFront->getSchema();
		return self::getModel('Mail')->count(array("dbname='$schema' AND mailbox='inbox' AND codusu='{$usuarioFront->getCodusu()}' AND status<>'D'"));
	}

	public static function getTrashCount(){
		$usuarioFront = IdentityManager::getFrontUser();
		$schema = $usuarioFront->getSchema();
		return self::getModel('Mail')->count(array("dbname='$schema' AND mailbox='trash' AND codusu='{$usuarioFront->getCodusu()}' AND status<>'D'"));
	}

	public static function renderMailbox($name){
		$usuarioFront = IdentityManager::getFrontUser();

		$boxNames = array(
			'inbox' => 'Bandeja de Entrada',
			'sent' => 'Mensajes Enviados',
			'trash' => 'Papelera'
		);
		if(isset($boxNames[$name])){
			$boxName = $boxNames[$name];
		} else {
			$boxName = 'Desconocido';
		}

		echo '<div class="mailTitleBar">
			<table width="100%">
				<tr>
					<td width="95%" align="left"><div class="window-title-bar">', $boxName, '</div></td>
					<td align="right"><div class="window-close">x</div></td>
				</tr>
			</table>
		</div>
		<div class="mailButtons">
			<table align="center">
				<tr>
					<td><label for="seleccionar">Seleccionar</label></td>
					<td>
						<select class="seleccionar">
							<option value="@">...</option>
							<option value="A">TODOS</option>
							<option value="N">NINGUNO</option>
							<option value="R">LEÍDOS</option>
							<option value="U">NO LEÍDOS</option>
						</select>
					</td>
					<td>&nbsp;</td>';
				if($name!='trash'){
					echo '<td><input type="button" class="responder" value="Responder" disabled/></td>
					<td><input type="button" class="reenviar" value="Reenviar" disabled/></td>
					<td><input type="button" class="suprimir" value="Suprimir" disabled/></td>';
				} else {
					echo '<td><input type="button" class="restaurar" value="Restaurar" disabled/></td>
					<td><input type="button" class="suprimirDefinitivo" value="Suprimir Definitivamente" disabled/></td>';
				}
				echo '
					<td><input type="button" class="marcarLeido" value="Marcar como leído" disabled/></td>
					<td><input type="button" class="marcarNoLeido" value="Marcar como no leído" disabled/></td>
				</tr>
			</table>
		</div>
		<div class="mailPreviews">
			<table width="100%" class="zebraSt hyBrowseTab sortable mailTable" cellspacing="0">
				<thead>
					<tr>
						<th class="small-column"><input type="checkbox" class="seleccionarCheck"/></th>
						<th class="small-column">&nbsp;</th>
						<th>De</th>
						<th>Asunto</th>
						<th class="sortasc">Fecha</th>
						<th class="small-column">&nbsp;</th>
						<th class="small-column">&nbsp;</th>
					</tr>
				</thead>
				<tbody>';
			$schema = $usuarioFront->getSchema();
			$mails = self::getModel('Mail')->find(array("dbname='$schema' AND mailbox='$name' AND codusu='{$usuarioFront->getCodusu()}' AND status<>'D'", "order" => "timsen DESC", "limit" => 100));
			foreach($mails as $mail){

				$fecha = Date::fromTimestamp($mail->getTimsen());

				if($mail->getStatus()=='U'){
					echo '<tr class="message unread" id="', $mail->getSid(), '">';
				} else {
					echo '<tr class="message" id="', $mail->getSid(), '">';
				}
				echo '<td><input type="checkbox"/></td>';
				if($mail->getPriority()==3){
					echo '<td class="flagged"></td>';
				} else {
					echo '<td></td>';
				}
				echo '<td align="left">', str_replace('"', '', $mail->getFromMsg()), '</td>';
				echo '<td align="left"><div class="subject">'.$mail->getSubject(), " <span style='font-size:11px;color:#969696'>", $mail->getPreview(), '</span></div></td>';
				echo '<td align="left">'.$fecha->getLocaleDate('medium').'</td>';
				if($mail->getHAttach()>0){
					echo '<td>'.$mail->getHAttach().'</td>';
				} else {
					echo '<td></td>';
				}
				if($mail->getMailbox()!='trash'){
					echo '<td>', Tag::image(array('backoffice/delete-l.gif', 'class' => 'delete')), '</td>';
				} else {
					echo '<td></td>';
				}
				echo '</tr>';
			}
			echo '</tbody>
			</table>
		</div>
		<div class="mailContent"></div>';
	}

	public static function renderCompose($action, $mail){

		$empresa = self::getModel('Empresa')->findFirst();

		View::getContent();

		$message = '';
		if($mail){
			if($action=='answer'){
				$headers = self::getHeaders($mail);
				if(isset($headers['From'])){
					Tag::displayTo('to', $headers['From']);
				}
				Tag::displayTo('subject', 'Re: '.$mail->getSubject());
			} else {
				if($action=='forward'){
					Tag::displayTo('subject', 'Fw: '.$mail->getSubject());
				}
			}

			if($action=='answer'||$action=='forward'){
				$message.= '<br/><br/><div style="border-left: 1px solid #969696;margin-left: 5px;padding-left: 5px;">'.$mail->getMessage().'</div>';
			}
		}

		$identity = IdentityManager::getActive();
		$message.= '<br/><br/>--<br/>'.$identity['nombres'].' '.$identity['apellidos'].'<br/><span style="color:#969696">'.$empresa->getNombre().'</span>';

		echo '<div class="mailTitleBar">
			<table width="100%">
				<tr>
					<td width="95%"><div class="window-title-bar">Redactar Mensaje</div></td>
					<td align="right"><div class="window-close">x</div></td>
				</tr>
			</table>
		</div>
		<div class="mailButtons" align="left">
			<table>
				<tr>
					<td><input type="button" class="send" value="Enviar"/></td>
					<td><input type="button" class="discard" value="Descartar"/></td>
				</tr>
			</table>
		</div>
		<div class="mailCompose">
			<table align="center" width="100%" class="composeData">
				<tr>
					<td align="right" width="10%"><label for="to">Para</label></td>
					<td align="left">', Tag::textArea('to'), '<div id="to_choices" style="display:none" class="autocomplete"></div></td>
				</tr>
				<tr>
					<td align="right" width="10%"><label for="cc">Copia a</label></td>
					<td align="left">', Tag::textArea('cc'), '<div id="cc_choices" style="display:none" class="autocomplete"></div></td>
				</tr>
				<tr>
					<td align="right" width="10%"><label for="subject">Asunto</label></td>
					<td align="left">', Tag::textField('subject'), '</td>
				</tr>
				<tr>
					<td colspan="2"><textarea id="content">', $message, '</textarea></td>
				</tr>
			</table>
		</div>';
	}

	/**
	 * Realiza un envío de correo consumiendo el servicio de delivery
	 *
	 * @param array $params
	 */
	public static function send($params){
		$delivery = self::_getDeliveryService();
		return $delivery->send($params);
	}

}