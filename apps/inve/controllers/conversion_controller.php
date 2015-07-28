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
 * ConversionController
 *
 * Controlador de la conversion de unidades
 *
 */
class ConversionController extends HyperFormController {

	static protected $_config = array(
		'model' => 'ConversionUnidades',
		'plural' => 'Conversión de Unidades',
		'single' => 'Conversión de Unidad',
		'genre' => 'F',
		'preferedOrder' => 'unidad',
		'icon' => 'conversion.png',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'primary' => true,
				'size' => 5,
				'maxlength' => 8,
				'filters' => array('int')
			),
			'unidad' => array(
				'single' => 'Unidad',
				'type' => 'relation',
				'relation' => 'Unidad',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_unidad',
				'filters' => array('alpha')
			),
			'unidad_base' => array(
				'single' => 'Unidad Base',
				'type' => 'relation',
				'relation' => 'Unidad',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_unidad',
				'filters' => array('alpha')
			),
			'factor_conversion' => array(
				'single' => 'Factor de Conversion',
				'type' => 'decimal',
				'size' => 10,
				'maxlength' => 16,
				'filters' => array('float')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function getUnidadBaseAction(){
		$this->setResponse('json');
		$unidad = $this->getPostParam('unidad','alpha');
		$unidad = $this->Unidad->findFirst("codigo='$unidad'");
		if($unidad != false){
			$magnitud = $this->Magnitudes->findFirst("id='{$unidad->getMagnitud()}'");
			if($magnitud != false){
				return array(
					'status' => 'OK',
					'unidadBase' => $magnitud->getUnidadBase()
				);
			}
		}
		return array(
			'status' => 'NOT FOUND'
		);
	}

}
