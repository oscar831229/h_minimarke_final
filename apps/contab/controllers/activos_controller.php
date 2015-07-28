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
 * ActivosController
 *
 * Controlador de activos fijos
 *
 */
class ActivosController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Activos',
		'plural' => 'activos fijos',
		'single' => 'activo fijo',
		'genre' => 'M',
		'icon' => 'sofa.png',
		'preferedOrder' => 'descripcion',
		'fields' => array(
			'id' => array(
				'single' => 'Id',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'codigo' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 5,
				'filters' => array('int')
			),
			'descripcion' => array(
				'single' => 'Descripción',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'grupo' => array(
				'single' => 'Grupo',
				'type' => 'relation',
				'relation' => 'Grupos',
				'fieldRelation' => 'linea',
				'detail' => 'nombre',
				'conditions' => "es_auxiliar='S'",
				'filters' => array('alpha')
			),
			'centro_costo' => array(
				'single' => 'Centro Costo',
				'type' => 'relation',
				'relation' => 'Centros',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_centro',
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'tipos_activos_id' => array(
				'single' => 'Tipo',
				'type' => 'relation',
				'relation' => 'TiposActivos',
				'fieldRelation' => 'codigo',
				'detail' => 'nombre',
				'notBrowse' => true,
				'filters' => array('int')
			),
			'cantidad' => array(
				'single' => 'Cantidad',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 5,
				'notSearch' => true,
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('int')
			),
			'ubicacion' => array(
				'single' => 'Ubicación',
				'type' => 'relation',
				'relation' => 'Ubicacion',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_ubica',
				'notBrowse' => true,
				'filters' => array('int')
			),
			'fecha_compra' => array(
				'single' => 'Fecha Compra',
				'type' => 'date',
				'default' => '',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('date')
			),
			'valor_compra' => array(
				'single' => 'Base Valor Compra',
				'type' => 'decimal',
				'size' => 12,
				'maxlength' => 15,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('double')
			),
			'valor_iva' => array(
				'single' => 'IVA Valor Compra',
				'type' => 'decimal',
				'size' => 12,
				'maxlength' => 15,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('double')
			),
			'numero_fac' => array(
				'single' => 'No. Factura',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 10,
				'notBrowse' => true,
				'filters' => array('int')
			),
			'serie' => array(
				'single' => 'Serie',
				'type' => 'text',
				'size' => 15,
				'maxlength' => 15,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('extraspaces')
			),
			'proveedor' => array(
				'single' => 'Proveedor',
				'type' => 'tercero',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('terceros')
			),
			'responsable' => array(
				'single' => 'Responsable',
				'type' => 'tercero',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('terceros')
			),
			'meses_a_dep' => array(
				'single' => 'Meses a Depreciar',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int')
			),
			'forma_pago' => array(
				'single' => 'Forma de Pago',
				'type' => 'relation',
				'relation' => 'FormaPago',
				'fieldRelation' => 'codigo',
				'detail' => 'descripcion',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int')
			),
			'inventariado' => array(
				'single' => 'Inventariado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notSearch' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'fecha_inv' => array(
				'single' => 'Fecha Inventariado',
				'type' => 'date',
				'default' => '',
				'notSearch' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('date')
			),
			'comprob' => array(
				'single' => 'Comprobante Entrada',
				'type' => 'relation',
				'relation' => 'Comprob',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_comprob',
				'notBrowse' => true,
				'notSearch' => true,
				'readOnly' => true,
				'filters' => array('comprob')
			),
			'numero' => array(
				'single' => 'Número Comprobante',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 5,
				'notBrowse' => true,
				'notSearch' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notSearch' => true,
				'notBrowse' => true,
				'notReport' => true,
				'values' => array(
					'B' => 'BUENO',
					'R' => 'REGULAR',
					'M' => 'MALO',
					'I' => 'INACTIVO/PARA BAJA'
				),
				'filters' => array('alpha')
			)
		)
	);

	/**
	 *
	 * Metodo que usa HfosTag::ActivosField que busca por codigo
	 */
	public function queryByCodigoAction(){
		$this->setResponse('json');
		$numeroActivo = $this->getQueryParam('codigo', 'alpha');
		$activo = $this->Activos->findFirst("codigo='$numeroActivo'");
		if($activo==false){
			return array(
				'status' => 'FAILED',
				'message' => 'NO EXISTE EL ACTIVO'
			);
		} else {
			return array(
				'status' => 'OK',
				'nombre' => $activo->getDescripcion()
			);
		}
	}

	/**
	 *
	 * Metodo que usa HfosTag::ActivosField que busca por descripcion
	 */
	public function queryByNameAction()
    {
		$this->setResponse('json');
		$response = array();
		$nombre = $this->getPostParam('nombre', 'extraspaces');
		if ($nombre != '') {
			$nombre = preg_replace('/[ ]+/', '%', $nombre);
			$activos = $this->Activos->find('descripcion LIKE \''.$nombre.'%\'', 'order: descripcion', 'limit: 13');
			foreach ($activos as $activo) {
				$response[] = array(
					'value' => $activo->getCodigo(),
					'selectText' => $activo->getDescripcion(),
					'optionText' => $activo->getDescripcion()
				);
			}
		}
		return $response;
	}

	public function initialize()
    {
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
