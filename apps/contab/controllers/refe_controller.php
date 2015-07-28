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
 * RefeController
 *
 * Controlador de los items de servicio
 *
 */
class RefeController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Refe',
		'plural' => 'items de servicio',
		'single' => 'item de servicio',
		'genre' => 'M',
		'preferedOrder' => 'descripcion',
		'fields' => array(
			'item' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12,
				'primary' => true,
				'filters' => array('double')
			),
			'descripcion' => array(
				'single' => 'Descripción',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'linea' => array(
				'single' => 'Linea',
				'type' => 'relation',
				'relation' => 'Lineaser',
				'fieldRelation' => 'linea',
				'detail' => 'descripcion',
				'filters' => array('int')
			)
		)
	);

	public function initialize()
	{
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function getItemAction()
	{
		$this->setResponse('json');
		$item = $this->getPostParam('item');
		if ($item > 0) {
			$refe = $this->Refe->findFirst($item);
			if($refe==false){
				return array(
					'status' => 'FAILED',
					'message' => 'No existe el item de servicio'
				);
			} else {
				return array(
					'status' => 'OK',
					'descripcion' => $refe->getDescripcion()
				);
			}
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el item de servicio'
			);
		}
	}

	public function queryByDescriptionAction()
	{
		$this->setResponse('json');
		$response = array();
		$descripcion = $this->getPostParam('descripcion', 'extraspaces');
		if($descripcion!=''){
			$refes = $this->Refe->find('descripcion LIKE \''.$descripcion.'%\'', 'order: descripcion', 'limit: 13');
			foreach($refes as $refe){
				$response[] = array(
					'value' => $refe->getItem(),
					'selectText' => $refe->getDescripcion(),
					'optionText' => $refe->getDescripcion()
				);
			}
		}
		return $response;
	}

}
