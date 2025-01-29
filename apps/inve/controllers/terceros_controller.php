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
 * TercerosController
 *
 * Controlador de los terceros
 *
 */
class TercerosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Nits',
		'plural' => 'terceros',
		'single' => 'tercero',
		'genre' => 'M',
		'icon' => 'business-contact.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'nit' => array(
				'single' => 'Documento',
				'type' => 'text',
				'size' => 18,
				'maxlength' => 18,
				'primary' => true,
				'filters' => array('terceros')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'tipodoc' => array(
				'single' => 'Tipo Documento',
				'type' => 'relation',
				'relation' => 'Tipodoc',
				'fieldRelation' => 'codigo',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
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
				'filters' => array('onechar')
			),
			'direccion' => array(
				'single' => 'Dirección',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'telefono' => array(
				'single' => 'Teléfonos',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'fax' => array(
				'single' => 'Fax',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'email' => array(
				'single' => 'Email',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 145,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('striptags')
			),
			'celular' => array(
				'single' => 'Celular',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 145,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'locciu' => array(
				'single' => 'Ciudad',
				'type' => 'ciudad',
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'contacto' => array(
				'single' => 'Contacto',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'lista' => array(
				'single' => 'Tipo',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'1' => 'PROVEEDOR',
					'2' => 'CLIENTE',
					'3' => 'EMPLEADO',
					'4' => 'CLIENTE/PROVEEDOR',
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'autoret' => array(
				'single' => 'Autoretenedor',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('onechar')
			),
			'plazo' => array(
				'single' => 'Plazo certificado Hortifrutícula?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('onechar')
			),
			'estado_nit' => array(
				'single' => 'Tipo de Régimen',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'NO APLICA',
					'C' => 'COMUN',
					'G' => 'GRAN CONTRIBUYENTE',
					'S' => 'SIMPLIFICADO'
				),
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('alpha')
			),
			'resp_iva' => array(
				'single' => 'Responsable IVA',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('alpha')
			),
			'tipo_nit' => array(
				'single' => 'IVA Retenido',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					0 => 'SI',
					1 => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			/*'cupo' => array(
				'single' => 'Cupo',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('alpha')
			),*/
			'ap_aereo' => array(
				'single' => 'Porcentaje de ICA',
				'type' => 'text',
				'size' => 7,
				'maxlength' => 7,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('double')
			),
			'porce_cree' => array(
				'single' => 'Porcentaje de CREE',
				'type' => 'relation',
				'relation' => 'CuentasCree',
				'fieldRelation' => 'porce',
				'detail' => 'porce',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('double')
			),
			'retecompras_id' => array(
				'single' => 'Retención de Compras',
				'type' => 'relation',
				'relation' => 'Retecompras',
				'fieldRelation' => 'codigo',
				'detail' => 'descripcion',
				'maxlength' => 1,
				'notBrowse' => true,
				//'notSearch' => true,
				//'notReport' => true,
				'filters' => array('double')
			),
			'grupo_niif' => array(
				'single' => 'Grupo NIIF',
				'type' => 'int',
				'size' => 2,
				'maxlength' => 2,
				'notNull'	=> false,
				'filters' => array('int')
			),
			'docum_sopor' => array(
				'single' => 'Generar documento soporte',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('alpha')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function queryByNitAction(){
		$this->setResponse('json');
		$numeroNit = $this->getQueryParam('nit', 'alpha');
		$nit = $this->Nits->findFirst("nit='$numeroNit'");
		if($nit==false){
			return array(
				'status' => 'FAILED',
				'message' => 'NO EXISTE EL TERCERO'
			);
		} else {
			return array(
				'status' => 'OK',
				'nombre' => $nit->getNombre()
			);
		}
	}

	public function queryByNameAction(){
		$this->setResponse('json');
		$response = array();
		$nombre = $this->getPostParam('nombre', 'extraspaces');
		if($nombre!=''){
			$nombre = preg_replace('/[ ]+/', ' ', $nombre);
			$nits = $this->Nits->find('nombre LIKE \''.$nombre.'%\'', 'order: nombre', 'limit: 13');
			foreach($nits as $nit){
				$response[] = array(
					'value' => $nit->getNit(),
					'selectText' => $nit->getNombre(),
					'optionText' => $nit->getNombre()
				);
			}
		}
		return $response;
	}

	public function crearAction(){
		$this->setParamToView('tiposDocumentos', $this->Tipodoc->find(array("clase='C'", "order" => "predeterminado DESC,nombre")));
	}

}
