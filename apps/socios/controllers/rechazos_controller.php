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
 * RechazosController
 *
 * Controlador de Solicitudes Rechazadas
 *
 */
class RechazosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Rechazos',
		'plural' => 'Solicitudes Rechazadas',
		'single' => 'Solicitud Rechazada',
		'genre' => 'M',
		'tabName' => 'Datos Basicos',
		'preferedOrder' => 'nombres,apellidos ASC',
		'icon' => 'switch.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'tipo_documentos_id' => array(
				'single' => 'Tipo de Documento',
				'type' => 'relation',
				'relation' => 'tipoDocumentos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'filters' => array('int')
			),			
			'identificacion' => array(
				'single' => 'Identificación',
				'type' => 'int',
				'size' => 14,
				'maxlength' => 20,
				'filters' => array('int')
			),
			'nombres' => array(
				'single' => 'Nombres',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 60,				
				'filters' => array('striptags', 'extraspaces')
			),
			'apellidos' => array(
				'single' => 'Apellidos',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 60,
				'filters' => array('striptags', 'extraspaces')
			),
			'fecha_solicitud' => array(
				'single' => 'Fecha de Solicitud',
				'type' => 'date',
				'default' => '',
				'filters' => array('date')
			),
			'observaciones' => array(
				'single' => 'Observaciones',
				'type' => 'textarea',
				'default' => '',
				'cols' => 30,
				'rows' => 5,
				'filters' => array('striptags', 'extraspaces')
			)
		),
		'extras' => array(
			0 => array(
				'partial' => 'ambientacion',
				'tabName' => 'Ambientación'
			),
			1 => array(
				'partial' => 'primera_vuelta',
				'tabName' => 'Primera Vuelta'
			),
			2 => array(
				'partial' => 'segunda_vuelta',
				'tabName' => 'Segunda Vuelta'
			)
		)
		
	);
	
	/**
	 * Carga info para partials
	 *
	 */
	public function beforeNew(){
		$this->setParamToView('state', 'new');
		Tag::displayTo('estado', 'A');
		return true;
	}
	
	/**
	 * Carga los demas datos del rechazo
	 *
	 */
	public function beforeEdit(){
		$id = $this->getPostParam('id');
		if($id){
    	    $rechazos = new Rechazos();
    	    $rechazo = $rechazos->findFirst('id='.$id);
    	    $this->setParamToView('rechazo',$rechazo);
		}
		return true;
	}
	
	/**
	 * Metood que crea/actualiza un rechazo
	 *
	 * @param Transaction $transaction
	 * @param ActiveRecord $record
	 */
	private function _actualizarRechazo(&$transaction, &$record){
	    foreach ($record->getAttributesNames() as $field){
	        if($this->getPostParam($field)){
	            $record->writeAttribute($field,$this->getPostParam($field));
	        }
	    }
	    return true;
	}
	
	/**
	 * Metodo que se ejecuta cuando ya se creo el registro de socios
	 *
	 * @param transacion $transaction
	 * @param ActiveRecord $record
	 * @return boolean
	 */
	public function afterInsert($transaction, $record){
		return $this->_actualizarRechazo($transaction, $record);
	}
	
	/**
	 * Metodo que se ejecuta cuando ya se actualizo el registro de socios
	 *
	 * @param transacion $transaction
	 * @param ActiveRecord $record
	 * @return boolean
	 */
	public function afterUpdate($transaction, $record){
		return $this->_actualizarRechazo($transaction, $record);
	}

	
	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}
