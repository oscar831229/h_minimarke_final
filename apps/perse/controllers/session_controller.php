<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Persé
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class SessionController extends ApplicationController {

	private function _failLogin(){
		$traslate = $this->_loadTraslation();
		if(Browser::isMobile()==false){
			return $this->routeTo(array('controller' => 'index', 'action' => 'index'));
		} else {
			return $this->routeTo(array('controller' => 'mobile', 'action' => 'index'));
		}
	}

	public function loginAction(){

		$traslate = $this->_loadTraslation();

		$numeroHabitacion = $this->getPostParam('numeroHabitacion', 'alpha');


		$clave = $this->getPostParam('clave', 'alpha');
		$perse = $this->Perse->findFirst("clave='$clave' AND enaper='S'");
		if($perse==false){
			Flash::error($traslate['noClave']);
			return $this->_failLogin();
		}

		$folio = $this->Folio->findFirst("numfol='{$perse->getNumfol()}' AND numhab='$numeroHabitacion' AND estado='I'");
		if($folio==false){
			Flash::error($traslate['noCombHab']);
			return $this->_failLogin();
		}

		$dathot = $this->Dathot->findFirst();
		$guestInfo = SessionNamespace::add('guestInfo');

		$cliente = $this->Clientes->findFirst("cedula='{$folio->getCedula()}'");
		if($cliente==false){
			Flash::error($traslate['errCliente']);
			return $this->_failLogin();
		}

		$nombreCliente = ucwords(i18n::strtolower($cliente->getNombre()));
		$guestInfo->setCliente($nombreCliente);
		$guestInfo->setHabitacion($folio->getNumhab());
		$guestInfo->setFolio($folio->getNumfol());
		$guestInfo->setHotel($dathot->getNombre());

		$territory = $this->Territory->findFirst($cliente->getLocnac());
		$locale = Locale::fromCountry($territory->getNameEn());
		if($locale!=''&&strlen($locale)==5){
			if(Session::get('locale')!=((string)$locale)){
				Session::set('locale', (string)$locale);
				MemoryRegistry::reset('traslate');
				$this->_loadTraslation();
			}
		}

		if(Browser::isMobile()==false){
			$this->redirect('accounts');
		} else {
			$this->redirect('mobile/accounts');
		}
	}

	public function setLocaleAction($locale=''){
		$language = $this->filter($locale, 'locale');
		if($language!=''){
			$locale = new Locale($language);
			Session::set('locale', $language);
			MoneyChange::setCurrencyFromLocale();
		}
		if(isset($_SERVER['HTTP_REFERER'])&&strpos($_SERVER['HTTP_REFERER'], 'setLocale')===false){
			$this->setResponse('view');
			header('Location: '.$_SERVER['HTTP_REFERER']);
		} else {
			$this->routeTo(array('controller' => 'index'));
		}
	}

	public function setCurrencyAction($currencyId=0){
		$currencyId = $this->filter($currencyId, 'int');
		if($currencyId>0){
			$currency = $this->Currencies->findFirst($currencyId);
			if($currencyId!=false){
				$traslate = $this->_loadTraslation();
				Session::set('currencyId', $currencyId);
				Session::set('currencyFactor', MoneyChange::getCurrency($currencyId));
			}
		}
		if(isset($_SERVER['HTTP_REFERER'])&&strpos($_SERVER['HTTP_REFERER'], 'setCurrency')===false){
			$this->setResponse('view');
			header('Location: '.$_SERVER['HTTP_REFERER']);
		} else {
			$this->routeTo(array('controller' => 'index'));
		}
	}

	public function logoutAction(){
		Session::unsetData('locale');
		SessionNamespace::drop('guestInfo');
		if(Browser::isMobile()==false){
			$this->redirect('index');
		} else {
			$this->redirect('mobile');
		}
	}

}