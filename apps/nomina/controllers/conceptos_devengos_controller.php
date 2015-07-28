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
 * Conceptos_DevengosController
 *
 * Controlador de los conceptos basicos de devengos
 *
 */
class Conceptos_DevengosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'ConceptosDevengos',
		'plural' => 'conceptos de devengos',
		'single' => 'concepto de devengos',
		'genre' => 'M',
		'tabName' => 'General',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
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
			'tipo' => array(
				'single' => 'Tipo',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'H' => 'HORAS',
					'D' => 'DIAS'
				),
				'filters' => array('onechar')
			),
			'vacaciones' => array(
				'single' => 'Base para vacaciones',
				'type' => 'closed-domain',
				'notBrowse' => true,
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'parafiscales' => array(
				'single' => 'Genera Aportes Parafiscales?',
				'type' => 'closed-domain',
				'notBrowse' => true,
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'prima' => array(
				'single' => 'Base Prima Servicios?',
				'type' => 'closed-domain',
				'notBrowse' => true,
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'salud' => array(
				'single' => 'Base para Salud?',
				'type' => 'closed-domain',
				'notBrowse' => true,
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'retencion' => array(
				'single' => 'Causa Rentención?',
				'type' => 'closed-domain',
				'notBrowse' => true,
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'porc_retencion' => array(
				'single' => 'Porcentaje Retención',
				'type' => 'text',
				'notBrowse' => true,
				'size' => 7,
				'maxlength' => 7,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			'cesantias' => array(
				'single' => 'Base para Cesantias?',
				'type' => 'closed-domain',
				'notBrowse' => true,
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			/*'porc_salario' => array(
				'single' => 'Porcentaje Salario',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),*/
			'provision' => array(
				'single' => 'Base para Provisiones?',
				'type' => 'closed-domain',
				'notBrowse' => true,
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'porc_recargo' => array(
				'single' => 'Porcentaje Recargo',
				'type' => 'text',
				'size' => 7,
				'maxlength' => 7,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			'cuenta' => array(
				'single' => 'Cuenta Contable',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
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
		),
		'extras' => array(
			0 => array(
				'partial' => 'macro',
				'tabName' => 'Formula/Condiciones'
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function beforeEdit($record){
		Tag::displayTo('variable', $record->getVariable());
		Tag::displayTo('formula', $record->getFormula());
	}

	private function _assignFormulaData($record){
		$variable = $this->getPostParam('variable', 'ascii', 'striptags', 'extraspaces');
		$formula = $this->getPostParam('formula', 'ascii', 'striptags', 'extraspaces');
		$record->setVariable($variable);
		$record->setFormula($formula);
		return true;
	}

	public function beforeInsert($transaction, $record){
		return self::_assignFormulaData($record);
	}

	public function beforeUpdate($transaction, $record){
		return self::_assignFormulaData($record);
	}

}
