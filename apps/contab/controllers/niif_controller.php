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
 * NiifController
 *
 * Controlador de las cuentas niif
 *
 */
class NiifController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Niif',
		'plural' => 'Niifs',
		'single' => 'Niif',
		'genre' => 'M',
		'icon' => 'product.png',
		'preferedOrder' => 'cuenta',
		'fields' => array(
			'cuenta' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12,
				'primary' => true,
				'filters' => array('niif')
			),
			'tipo' => array(
				'single' => 'Tipo',
				'type' => 'text',
				'size' => 1,
				'maxlength' => 1,
				'notBrowse' => true,
				'notSearch' => true,
				'notDetails' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'mayor' => array(
				'single' => 'Mayor',
				'type' => 'text',
				'size' => 1,
				'maxlength' => 1,
				'notBrowse' => true,
				'notSearch' => true,
				'notDetails' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'clase' => array(
				'single' => 'Clase',
				'type' => 'text',
				'size' => 2,
				'maxlength' => 2,
				'notBrowse' => true,
				'notSearch' => true,
				'notDetails' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'subclase' => array(
				'single' => 'Sublase',
				'type' => 'text',
				'size' => 2,
				'maxlength' => 2,
				'notBrowse' => true,
				'notSearch' => true,
				'notDetails' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'auxiliar' => array(
				'single' => 'Auxiliar',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'notDetails' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'subaux' => array(
				'single' => 'Sub-Auxiliar',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'notDetails' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 70,
				'filters' => array('striptags', 'extraspaces')
			),
			'es_auxiliar' => array(
				'single' => 'Es Auxiliar?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('onechar')
			),
			'pide_nit' => array(
				'single' => 'Pide Tercero?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'pide_ban' => array(
				'single' => 'Para Bancos?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'pide_base' => array(
				'single' => 'Pide Base?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'porc_iva' => array(
				'single' => 'Porcentaje de IVA (%)',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('double')
			),
			'pide_fact' => array(
				'single' => 'Pide Documento?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'pide_centro' => array(
				'single' => 'Pide Centro de Costo?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'es_mayor' => array(
				'single' => 'Es Cuenta Mayor?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'contrapartida' => array(
				'single' => 'Contrapartida',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('niif')
			),
			'cta_retencion' => array(
				'single' => 'Cuenta Retención',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('niif')
			),
			'porc_retenc' => array(
				'single' => 'Porcentaje de Retención',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			'cta_iva' => array(
				'single' => 'Cuenta Retención',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('niif')
			),
			'porcen_iva' => array(
				'single' => 'Porcentaje de IVA',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('double')
			),
			'usa_revelacion' => array(
				'single' => 'Usa Revelaciones Niif?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function newAction(){

	}

	public function editAction(){
		$codigoCuenta = $this->getPostParam('cuenta', 'niif');
		$cuenta = $this->Niif->findFirst("cuenta='$codigoCuenta'");
		if($cuenta!=false){
			foreach($cuenta->getAttributes() as $attribute){
				Tag::displayTo($attribute, $cuenta->readAttribute($attribute));
			}
		}
		$this->setParamToView('codigoCuenta', $codigoCuenta);
	}

	public function getRecordToSave(){
		$hyperAction = $this->getPostParam('hyperAction', 'alpha');
		if($hyperAction=='insert'){
			return false;
		} else {
			$codigoCuenta = $this->getPostParam('cuenta', 'niif');
			return $this->Niif->findFirst("cuenta='$codigoCuenta'");
		}
	}

	/**
	 * COMPONENT
	 */

	public function queryByNiifAction()
	{
		$this->setResponse('json');

		$codigoNiif = $this->getQueryParam('cuenta', 'niif');
		$niif 	    = $this->Niif->findFirst("cuenta='$codigoNiif'");

		if($niif == false){
			return array(
				'status'  => 'FAILED',
				'message' => 'NO EXISTE NIIF CONTABLE'
			);
		} else {
			return array(
				'status' => 'OK',
				'nombre' => $niif->getNombre()
			);
		}
	}

	public function queryByNameAction()
	{
		$this->setResponse('json');

		$response = array();
		$nombre   = $this->getPostParam('nombre', 'extraspaces');

		if ($nombre!='') {
			$nombre  = preg_replace('/[ ]+/', '%', $nombre);
			$niifs = $this->Niif->find('nombre LIKE \''.$nombre.'%\' AND es_auxiliar=\'S\'', 'order: nombre', 'limit: 13');
			foreach($niifs as $niif){
				$response[] = array(
					'value' 	 => $niif->getCuenta(),
					'selectText' => $niif->getNombre(),
					'optionText' => $niif->getNombre().' '.$niif->getCuenta()
				);
			}
		}
		return $response;
	}
}
