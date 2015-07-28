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
 * Cambio_CategoriaController
 *
 * Controlador de cambio de categoría
 *
 */
class Cambio_CategoriaController extends HyperFormController {

	static protected $_config = array(
		'model' => 'CambioCategoria',
		'plural' => 'Cambio de Categorias',
		'single' => 'Cambio de Categoría',
		'genre' => 'M',
		'preferedOrder' => 'id DESC',
		'icon' => 'cartera.png',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 9,
				'maxlength' => 9,
				'primary' => true,
				'filters' => array('int')
			),
			'socios_id' => array(
				'single' => 'Socio',
				'type' => 'Socio',
				'filters' => array('alpha')
			),
			'tipo_socios_id' => array(
				'single' => 'Tipo de Socios',
				'type' => 'relation',
				'relation' => 'TipoSocios',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'notNull' => true,
				'filters' => array('int')
			),
			'descripcion' => array(
				'single' => 'Observaciones',
				'type' => 'textarea',
				'cols' => 40,
				'rows' => 5,
				'maxlength' => 20,
				'notNull' => true,
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('striptags')
			)
		)
	);
	
	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	private function _cambiarCategoria($transaction, $record)
	{
		$sociosId = $record->getSociosId();
		$tipoSociosId = $record->getTipoSociosId();
		//Buscamos cargos fijos de categoria
		$conditions = "tipo_socios_id='$tipoSociosId'";
		$cargosFijosCategoria = EntityManager::get("CargosFijosCategoria")->findFirst($conditions);
		if (!$cargosFijosCategoria) {
			$transaction->rollback("No se ha creado los cargos fijos para la categoria");
		}
		//Borramos cargos fijos actuales
		$asignacionCargosObj = EntityManager::get("AsignacionCargos")->setTransaction($transaction)->find("socios_id='{$record->getSociosId()}'");
		foreach ($asignacionCargosObj as $asignacionCargos) {
			$asignacionCargos->setEstado("I");
			if (!$asignacionCargos->save()) {
                foreach ($asignacionCargos->getMessages() as $message) {
                    $transaction->rollback($message->getMessage());
                }
            }
		}
		//asignamos cargos fijos de categoria
		for ($i = 1; $i <= 7; $i++) {
			$campoX = "carfijo" . $i;
			$cf = $cargosFijosCategoria->readAttribute($campoX);
			if ($cf) {
				$carFijoTmp = new AsignacionCargos();
				$carFijoTmp->setTransaction($transaction);
				$carFijoTmp->setSociosId($sociosId);
				$carFijoTmp->setCargosFijosId($cf);
				$carFijoTmp->setEstado("A");
				if (!$carFijoTmp->save()) {
	                foreach ($carFijoTmp->getMessages() as $message) {
	                    $transaction->rollback($message->getMessage());
	                }
	            }
	        }
		}
	}

	public function beforeInsert($transaction, $record)
    {
        $record->setFecha(date("Y-m-d H:i:s"));
        $this->_cambiarCategoria($transaction, $record);
        return true;
    }

    /*public function beforeUpdate($transaction, $record)
    {
        $record->setFecha(date("Y-m-d H:i:s"));
       	return true;
    }*/
}
