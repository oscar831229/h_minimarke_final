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
 * Accion_EstadosController
 *
 * Controlador de los acciones de un estado de socios al asignarce a un socio
 *
 */
class Accion_EstadosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'AccionEstados',
		'plural' => 'Acción de Estados',
		'single' => 'Acción de Estado',
		'genre' => 'M',
		'preferedOrder' => 'estados_socios_id',
		'icon' => 'arrow-retweet.png',
		/*'ignoreButtons' => array(
			'import'
		),*/
		'fields' => array(
			'estados_socios_id' => array(
				'single' => 'Estado de Socio',
				'type' => 'relation',
				'relation' => 'EstadosSocios',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'primary' => true,
				'notNull' => true,
				'filters' => array('int')
			),
			'cargos_fijos_id_ini' => array(
				'single' => 'Cargo fijo Inicial',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'primary' => true,
				'notNull' => true,
				'filters' => array('int')
			),
			'cargos_fijos_id_fin' => array(
				'single' => 'Cargo fijo Final',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'notNull' => true,
				'filters' => array('int')
			),
			'borrar_cargo_fijo' => array(
				'single' => 'Borra Cargo Fijo Inicial',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'filters' => array('onechar')
			)
		)
	);
	

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
