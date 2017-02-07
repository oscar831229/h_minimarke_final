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
 * ComprobantesController
 *
 * Controlador de los comprobantes
 *
 */
class ComprobantesController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Comprob',
		'plural' => 'tipos de comprobantes',
		'single' => 'tipo de comprobante',
		'genre' => 'M',
		'icon' => 'category.png',
		'preferedOrder' => 'nom_comprob',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('alpha')
			),
			'nom_comprob' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'diario' => array(
				'type' => 'relation',
				'single' => 'Diario',
				'relation' => 'Diarios',
				'fieldRelation' => 'codigo',
				'detail' => 'nombre',
				'filters' => array('int')
			),
			'cta_iva' => array(
				'single' => 'Cuenta IVA 19% Descontable',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_ivad' => array(
				'single' => 'Cuenta IVA 10% Descontable',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_cartera' => array(
				'single' => 'Cuenta IVA 19% Retenido',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_ivam' => array(
				'single' => 'Cuenta IVA 10% Retenido',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'consecutivo' => array(
				'single' => 'Consecutivo',
				'type' => 'int',
				'size' => 7,
				'maxlength' => 7,
				'notSearch' => true,
				'filters' => array('int')
			),
			'comprob_contab' => array(
				'single' => 'Comprobante Contable',
				'type' => 'relation',
				'relation' => 'Comprob',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_comprob',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('comprob'),
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function getConsecutivoAction(){
		$this->setResponse('json');
		$codigoComprobante = $this->getQueryParam('codigoComprobante', 'comprob');
		$comprob = $this->Comprob->findFirst("codigo='$codigoComprobante'");
		if($comprob==false){
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el código del comprobante'
			);
		} else {
			if($comprob->getConsecutivo()==0){
				$comprob->setConsecutivo(1);
				if($comprob->save()==false){
					foreach($comprob->getMessages() as $message){
						return array(
							'status' => 'FAILED',
							'message' => $message->getMessage()
						);
					}
				}
			}
			return array(
				'status' => 'OK',
				'consecutivo' => $comprob->getConsecutivo()
			);
		}
	}
	
	
	
	/**
	 * Action para autocomplete de centros por codigo
	 *
	 * @return json
	 */
	public function queryComprobAction(){
		$this->setResponse('json');
		$codigoComprob = $this->getQueryParam('comprob', 'alpha');
		$comprob = $this->Comprob->findFirst("codigo='$codigoComprob'");
		if($comprob!=false){
			return array(
			    'status' => 'OK',
				'existe' => 'S',
				'codigo' => $comprob->getCodigo(),
				'nom_comprob' => $comprob->getNomComprob()
			);			
		} else {
			return array(
				'existe' => 'N'
			);
		}
	}
	
	public function queryByComprobAction(){
		$this->setResponse('json');
		$codigoComprob = $this->getQueryParam('comprob', 'alpha');
		$comprob = $this->Comprob->findFirst("codigo='$codigoComprob'");
		if($comprob!=false){
			return array(
			    'status' => 'OK', 
				'existe' => 'S',
				'codigo' => $comprob->getCodigo(),
				'nom_comprob' => $comprob->getNomComprob()
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
			$comprobantes = $this->Comprob->find('nom_comprob LIKE \''.$nombre.'%\'', 'order: nom_comprob', 'limit: 13');
			foreach($comprobantes as $comprobante){
				$response[] = array(
				    'status' => 'OK',
					'value' => $comprobante->getCodigo(),
					'selectText' => $comprobante->getNomComprob(),
					'optionText' => $comprobante->getNomComprob()
				);
			}
		}
		return $response;
	}

}
