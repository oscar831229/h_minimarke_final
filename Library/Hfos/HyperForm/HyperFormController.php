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

/**
 * HyperFormController
 *
 * Controlador para formularios HyperForm
 *
 */
class HyperFormController extends ApplicationController {

	/**
	 * Mensajes generados por los prevalidadores
	 *
	 * @var array
	 */
	private $_messages = array();

	/**
	 * Configuración del formulario
	 *
	 * @var array
	 */
	protected static $_config = array();

	/**
	 * Establece la configuración del formulario
	 *
	 * @param array $config
	 */
	public static function setConfig($config){
		self::$_config = $config;
	}

	/**
	 * Método inicializador del controlador
	 *
	 */
	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		$this->setPersistance(true);
	}

	/**
	 * Acción por defecto del controlador
	 *
	 */
	public function indexAction(){
		if(method_exists($this, 'beforeIndex')){
			$this->beforeIndex();
		}
		HyperForm::queryPage(self::$_config);
	}

	/**
	 * Acción para mostrar el formulario de crear uno nuevo
	 *
	 */
	public function newAction(){
		if(method_exists($this, 'beforeNew')){
			$this->beforeNew();
		}
		HyperForm::newPage(self::$_config);
	}

	/**
	 * Acción para mostrar el formulario de editar un registro
	 *
	 */
	public function editAction(){
		if(method_exists($this, 'beforeSearch')){
			$conditions = $this->beforeSearch();
		} else {
			$conditions = array();
		}
		HyperForm::editPage($this, self::$_config, $conditions);
	}

	/**
	 * Acción para crear ó actualizar un registro
	 *
	 * @return string
	 */
	public function saveAction(){
		return HyperForm::save($this, self::$_config);
	}

	/**
	 * Acción para eliminar un registro
	 *
	 * @return string
	 */
	public function deleteAction(){
		return HyperForm::deleteRecord($this, self::$_config);
	}

	/**
	 * Acción para buscar un registro
	 *
	 * @return string
	 */
	public function searchAction(){
		if(method_exists($this, 'beforeSearch')){
			$conditions = $this->beforeSearch();
		} else {
			$conditions = array();
		}
		return HyperForm::search($this, self::$_config, $conditions);
	}

	/**
	 * Obtiene la página de detalles de un registro
	 *
	 * @return string
	 */
	public function getRecordDetailsAction(){
		if(method_exists($this, 'beforeSearch')){
			$conditions = $this->beforeSearch();
		} else {
			$conditions = array();
		}
		return HyperForm::getRecordDetails($this, self::$_config, $conditions);
	}

	/**
	 * Muestra las revisiones que haya tenido el registro
	 *
	 */
	public function rcsAction(){
		HyperForm::getRecordRcs($this, self::$_config);
	}

	/**
	 * Muestra la pantalla de importar
	 *
	 */
	public function importAction(){
		HyperForm::import($this, self::$_config);
	}

	/**
	 * Carga el archivo a importar y realiza el proceso
	 *
	 */
	public function loadAction(){
		HyperForm::load($this, self::$_config);
	}

	/**
	 * Este método permite simular la generación de un reporte
	 *
	 * @return array
	 */
	public function reportAction(){
		$this->setResponse('view');
		$_POST['reportType'] = 'excel';
		return HyperForm::search($this, self::$_config, array());
	}

	/**
	 * Limpia la cola de mensajes
	 *
	 */
	public function clearMessages(){
		$this->_messages = array();
	}

	/**
	 * Agrega un mensaje a la cola de mensajes
	 *
	 * @param array $message
	 */
	public function appendMessage($message){
		$this->_messages[] = $message;
	}

	/**
	 * Devuelve los mensajes de la cola de mensajes
	 *
	 * @return array
	 */
	public function getMessages(){
		return $this->_messages;
	}

	public function uploadFieldAction(){
		$this->setResponse('view');
		print HfosTag::uploadField();
	}

	

}
