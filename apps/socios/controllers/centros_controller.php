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
 * CentrosController
 *
 * Controlador de los centros de costo
 *
 */
class CentrosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Centros',
		'plural' => 'centros de costo',
		'single' => 'centro de costo',
		'genre' => 'M',
		'icon' => 'centros.png',
		'preferedOrder' => 'nom_centro',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'filters' => array('int')
			),
			'nom_centro' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'responsable' => array(
				'single' => 'Responsable',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'ACTIVO',
					'I' => 'INACTIVO'
				),
				'filters' => array('onechar')
			)
		)
	);
	
	/**
	 * Action para autocomplete de centros por codigo
	 *
	 * @return json
	 */
	public function queryCentroAction(){
		$this->setResponse('json');
		$codigoCentro = $this->getQueryParam('centro', 'alpha');
		$centro = $this->Centros->findFirst("codigo='$codigoCentro'");
		if($centro!=false){
			return array(
			    'status' => 'OK', 
				'existe' => 'S',
				'codigo' => $centro->getCodigo(),
				'nombre' => $centro->getNomCentro(),
				'responsable' => $cuenta->getResponsable()
			);			
		} else {
			return array(
				'existe' => 'N'
			);
		}
	}
	
	public function queryByCentroAction(){
		$this->setResponse('json');
		$codigoCentro = $this->getQueryParam('centro', 'alpha');
		$centro = $this->Centros->findFirst("codigo='$codigoCentro'");
		if($centro!=false){
			return array(
			    'status' => 'OK', 
				'existe' => 'S',
				'codigo' => $centro->getCodigo(),
				'nombre' => $centro->getNomCentro(),
				'responsable' => $centro->getResponsable()
			);			
		} else {
			return array(
				'existe' => 'N'
			);
		}
	}

	public function queryByNameAction(){
		$this->setResponse('json');
		$response = array();
		$nombre = $this->getPostParam('nombre', 'extraspaces');
		if($nombre!=''){
			$centros = $this->Centros->find('nom_centro LIKE \''.$nombre.'%\'', 'order: nom_centro', 'limit: 13');
			foreach($centros as $centro){
				$response[] = array(
					'value' => $centro->getCodigo(),
					'selectText' => $centro->getNomCentro(),
					'optionText' => $centro->getNomcentro()
				);
			}
		}
		return $response;
	}

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
