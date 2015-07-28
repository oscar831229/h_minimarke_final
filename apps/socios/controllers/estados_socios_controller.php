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
 * Estados_SociosController
 *
 * Controlador de Estado de Socios
 *
 */
class Estados_SociosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'EstadosSocios',
		'plural' => 'Estados de Socios',
		'single' => 'Estado de Socio',
		'genre' => 'M',
		'tabName' => 'estados_socios',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'type-user.png',
		/*'ignoreButtons' => array(
			'import'
		),*/
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'auto' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 45,				
				'filters' => array('striptags', 'extraspaces')
			),
			'accion' => array(
				'single' => 'Accion de Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'A' => 'Genera Factura',
					'I' => 'No Genera Factura'
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
