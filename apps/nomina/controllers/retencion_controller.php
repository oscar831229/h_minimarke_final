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
 * RetencionController
 *
 * Controlador de valores de retención en la fuente
 *
 */
class RetencionController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Retencion',
		'plural' => 'valores de retención',
		'single' => 'valor de retención',
		'genre' => 'M',
		'preferedOrder' => 'limite_sup',
		'fields' => array(
			'limite_sup' => array(
				'single' => 'Limite en Pesos',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'primary' => true,
				'filters' => array('double')
			),
			'valor_ret' => array(
				'single' => 'Valor Retención',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'filters' => array('double')
			),
			'porc_ret' => array(
				'single' => 'Porcentaje Retención',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'filters' => array('float')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
