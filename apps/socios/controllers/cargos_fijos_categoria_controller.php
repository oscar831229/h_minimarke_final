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
 * Cargos_Fijos_CategoriaController
 *
 * Controlador de los cargos fijos por categoria
 *
 */
class Cargos_Fijos_CategoriaController extends HyperFormController {

	static protected $_config = array(
		'model' => 'CargosFijosCategoria',
		'plural' => 'Cargos Fijos x Categorías',
		'single' => 'Cargo Fijo x Categoría',
		'genre' => 'M',
		'preferedOrder' => 'id DESC',
		'icon' => 'cartera-2.png',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 11,
				'maxlength' => 11,
				'primary' => true,
				'filters' => array('int')
			),
			'tipo_socios_id' => array(
				'single' => 'Tipo de Socio',
				'type' => 'relation',
				'relation' => 'TipoSocios',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				//'notBrowse' => true,
				//'notSearch' => true,
				//'notReport' => true,
				'filters' => array('int')
			),
			'carfijo1' => array(
				'single' => 'Cargo Fijo 1',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'carfijo2' => array(
				'single' => 'Cargo Fijo 2',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'carfijo2' => array(
				'single' => 'Cargo Fijo 2',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'carfijo3' => array(
				'single' => 'Cargo Fijo 3',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'carfijo4' => array(
				'single' => 'Cargo Fijo 4',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'carfijo5' => array(
				'single' => 'Cargo Fijo 5',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'carfijo6' => array(
				'single' => 'Cargo Fijo 6',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'carfijo7' => array(
				'single' => 'Cargo Fijo 7',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
