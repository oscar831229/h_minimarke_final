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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * TipodocController
 *
 * Controlador de los tipos de documentos
 *
 */
class TipodocController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Tipodoc',
		'plural' => 'tipos de documentos',
		'single' => 'tipo de documento',
		'genre' => 'M',
		'icon' => 'my-account.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'clase' => array(
				'single' => 'Clase',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'C' => 'CLIENTE',
					'A' => 'EMPRESA',
					'E' => 'EXTRANJERO'
				),
				'filters' => array('alpha')
			),
			'predeterminado' => array(
				'single' => 'Predeterminado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function queryByClaseAction(){
		$this->setResponse('json');
		$response = array();
		$clase = $this->getPostParam('clase', 'onechar');
		foreach($this->Tipodoc->find(array("clase='$clase'", 'order' => 'predeterminado DESC,nombre')) as $tipodoc){
			$response[] = array(
				'value' => $tipodoc->getCodigo(),
				'text' => $tipodoc->getNombre(),
			);
		}
		return $response;
	}

}