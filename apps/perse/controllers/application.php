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

Core::importFromLibrary('Hfos', 'Loader/Loader.php');

class ControllerBase {

	protected function _loadTraslation(){
		if(MemoryRegistry::exists('traslate')==false){
			if(Session::isSetData('locale')==false){
				if(Browser::isMobile()){
					$locale = Locale::getBrowser();
					$locale->forceCountry();
					Session::set('locale', $locale->getLocaleString());
				} else {
					Session::set('locale', 'es_CO');
				}
			}
			$locale = new Locale(Session::get('locale'));
			Locale::setApplication($locale);
			$language = $locale->getLanguage();
			$lcPath = 'apps/'.Router::getApplication().'/languages/'.$language.'/LC_MESSAGES/'.$language.'.php';
			if(file_exists($lcPath)){
				require $lcPath;
			} else {
				require 'apps/'.Router::getApplication().'/languages/es/LC_MESSAGES/es.php';
				Session::set('locale', 'es_CO');
			}
			$traslate = new Traslate('Array', $messages);
			$this->setParamToView('traslate', $traslate);
			MemoryRegistry::set('traslate', $traslate);
		} else {
			$traslate = MemoryRegistry::get('traslate');
		}
		if(Session::isSetData('currencyId')==false){
			MoneyChange::setCurrencyFromLocale();
		}
		return $traslate;
	}

	public function initialize(){
		if(SessionNamespace::exists('guestInfo')){
			$guestInfo = SessionNamespace::get('guestInfo');
			Tag::setDocumentTitle($guestInfo->getCliente().' / '.$guestInfo->getHotel().' - Persé');
		} else {
			$hotel = $this->Dathot->findFirst();
			Tag::setDocumentTitle('Persé');
		}
		LocaleMath::enableBcMath();
		$this->_loadTraslation();
	}

	public function init(){
		if(Browser::isMobile()==false){
			if(SessionNamespace::exists('guestInfo')){
				Router::routeTo(array('controller' => 'accounts'));
			} else {
				Router::routeTo(array('controller' => 'index'));
			}
		} else {
			Router::routeTo(array('controller' => 'mobile'));
		}
	}

	public function beforeFilter(){
		$guestInfo = SessionNamespace::get('guestInfo');
		$controllerName = Router::getController();
		if($controllerName=='accounts'){
			if(!$guestInfo){
				Router::routeTo(array('controller' => 'index'));
				return false;
			}
		}
		return true;
	}

	public function notFoundAction(){
		Router::routeTo(array('controller' => 'index', 'action' => 'notFound'));
	}

}

