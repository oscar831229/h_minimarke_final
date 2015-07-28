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
 * DiferidosController
 *
 * Controlador de diferidos
 *
 */
class DiferidosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Diferidos',
		'plural' => 'activos diferidos',
		'single' => 'activo diferido',
		'icon' => 'home.png',
		'genre' => 'M',
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
				'relation' => 'GruposDiferidos',
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
			'fecha_compra' => array(
				'single' => 'Fecha Compra',
				'type' => 'date',
				'default' => '',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('date')
			),
			'valor_compra' => array(
				'single' => 'Valor Compra',
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
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('int')
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
			'proveedor' => array(
				'single' => 'Proveedor',
				'type' => 'tercero',
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('terceros')
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

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}


  /**
   *
   * Metodo que usa HfosTag::DiferidosField que busca por codigo
   */
  public function queryByCodigoAction(){
    $this->setResponse('json');
    $numeroDiferido = $this->getQueryParam('codigo', 'alpha');
    $diferido = $this->Diferidos->findFirst("id='$numeroDiferido'");
    if($diferido==false){
      return array(
        'status' => 'FAILED',
        'message' => 'NO EXISTE EL DIFERIDOS'
      );
    } else {
      return array(
        'status' => 'OK',
        'nombre' => $diferido->getDescripcion()
      );
    }
  }

  /**
   *
   * Metodo que usa HfosTag::DiferidosField que busca por descripcion
   */
  public function queryByNameAction(){
    $this->setResponse('json');
    $response = array();
    $nombre = $this->getPostParam('nombre', 'extraspaces');
    if($nombre!=''){
      $nombre = preg_replace('/[ ]+/', '%', $nombre);
      $diferidos = $this->Diferidos->find('descripcion LIKE \''.$nombre.'%\'', 'order: descripcion', 'limit: 13');
      foreach($diferidos as $diferido){
        $response[] = array(
          'value' => $diferido->getId(),
          'selectText' => $diferido->getDescripcion(),
          'optionText' => $diferido->getDescripcion()
        );
      }
    }
    return $response;
  }

}
