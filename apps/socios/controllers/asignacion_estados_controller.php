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
 * Asignacion_EstadosController
 *
 * Controlador de los Asignacion de estados a un socio
 *
 */
class Asignacion_EstadosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'AsignacionEstados',
		'plural' => 'Asignación de Estados',
		'single' => 'Asignación de Estado',
		'genre' => 'M',
		'preferedOrder' => 'id DESC',
		'icon' => 'cartera.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('int')
			),
			'socios_id' => array(
				'single' => 'Socio',
				'type' => 'Socio',
				'filters' => array('alpha')
			),
			'estados_socios_id' => array(
				'single' => 'Estado de Socio',
				'type' => 'relation',
				'relation' => 'EstadosSocios',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'notNull' => true,
				'filters' => array('int')
			),
			'fecha_ini' => array(
				'single' => 'Fecha de Inicial de Estado',
				'type' => 'date',
				'default' => '',
				'notNull' => true,
				'filters' => array('date')
			),
			'fecha_fin' => array(
				'single' => 'Fecha de Final de Estado',
				'type' => 'date',
				'default' => '',
				'notNull' => true,
				'filters' => array('date')
			),
			'observaciones' => array(
				'single' => 'Observaciones',
				'type' => 'textarea',
				'cols' => 40,
				'rows' => 5,
				'maxlength' => 20,
				'notNull' => true,
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('striptags')
			)
		)
	);
	
	/**
	 * Actualiza socios
	 *
	 * @param unknown_type $transaction
	 * @param unknown_type $record
	 * @return boolean
	 */
	public function _actualizarSocio($transaction,$record)
	{
		Core::importFromLibrary('Hfos/Socios','SociosCore.php');
		
		//Cambia estado y aumotatiza el proceso de accion segun estado
		SociosCore::cambiarEstadoSocio($record->getSociosId(), $record->getEstadosSociosId());
		
		return true;
	}
	
	/**
	 * Metodo que se ejecuta despues de insertar la asignacion de estado a un socio
	 *
	 * @param Transaction $transaction
	 * @param Activerecord $record
	 * @return boolean
	 */
	public function beforeInsert($transaction,$record){
		return $this->_actualizarSocio($transaction,$record);
	}
	
	/**
	 * Metodo que se ejecuta despeus de actualizar
	 *
	 * @param unknown_type $transaction
	 * @param unknown_type $record
	 * @return unknown
	 */
	public function beforeUpdate($transaction,$record){
		return $this->_actualizarSocio($transaction,$record);
	}
	

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
