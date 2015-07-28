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
 * CuentasController
 *
 * Controlador de las cuentas contables
 *
 */
class CuentasController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Cuentas',
		'plural' => 'cuentas',
		'single' => 'cuenta',
		'genre' => 'F',
		'icon' => 'featured.png',
		'preferedOrder' => 'cuenta',
		'fields' => array(
			'cuenta' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12,
				'primary' => true,
				'filters' => array('cuentas')
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
				'filters' => array('cuentas')
			),
			'cta_retencion' => array(
				'single' => 'Cuenta Retención',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
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
				'filters' => array('cuentas')
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
			),
			'cuenta_niif' => array(
				'single' => 'Cuenta NIIF',
				'type' => 'niif',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('niif')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function newAction(){

	}

	public function editAction(){
		$codigoCuenta = $this->getPostParam('cuenta', 'cuentas');
		$cuenta = $this->Cuentas->findFirst("cuenta='$codigoCuenta'");
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
			$codigoCuenta = $this->getPostParam('cuenta', 'cuentas');
			return $this->Cuentas->findFirst("cuenta='$codigoCuenta'");
		}
	}

	public function queryCuentaAction(){
		$this->setResponse('json');
		$codigoCuenta = $this->getQueryParam('cuenta', 'cuentas');
		$cuenta = $this->Cuentas->findFirst("cuenta='$codigoCuenta'");
		if($cuenta!=false){

			$cuentasAuxiliares = array();

			$tipoDetalle = '';
			$tipo = substr($codigoCuenta, 0, 1);
			$tipoCuenta = $this->Cuentas->findFirst("cuenta='$tipo'");
			if($tipoCuenta){
				$tipoDetalle = $tipoCuenta->getNombre();
				$cuentas = $this->Cuentas->find("cuenta LIKE '$tipo%' AND mayor<>'' AND clase=''", "limit: 15");
				if(count($cuentas)>0){
					$cuentasAuxiliares = array();
					foreach($cuentas as $cuentaAuxiliar){
						$cuentasAuxiliares[] = array(
							'cuenta' => $cuentaAuxiliar->getCuenta(),
							'auxiliar' => $cuentaAuxiliar->getMayor(),
							'nombre' => $cuentaAuxiliar->getNombre()
						);
					}
				}
			}

			$mayorDetalle = '';
			if(strlen($codigoCuenta)>1){
				$mayor = substr($codigoCuenta, 0, 2);
				$mayorCuenta = $this->Cuentas->findFirst("cuenta='$mayor'");
				if($mayorCuenta){
					$mayorDetalle = $mayorCuenta->getNombre();
					$cuentas = $this->Cuentas->find("cuenta LIKE '$mayor%' AND clase<>'' AND subclase=''", "limit: 15");
					if(count($cuentas)>0){
						$cuentasAuxiliares = array();
						foreach($cuentas as $cuentaAuxiliar){
							$cuentasAuxiliares[] = array(
								'cuenta' => $cuentaAuxiliar->getCuenta(),
								'auxiliar' => $cuentaAuxiliar->getClase().$cuentaAuxiliar->getSubclase().$cuentaAuxiliar->getAuxiliar().$cuentaAuxiliar->getSubaux(),
								'nombre' => $cuentaAuxiliar->getNombre()
							);
						}
					}
				}
			}

			$claseDetalle = '';
			if(strlen($codigoCuenta)>3){
				$clase = substr($codigoCuenta, 0, 4);
				$claseCuenta = $this->Cuentas->findFirst("cuenta='$clase'");
				if($claseCuenta){
					$claseDetalle = $claseCuenta->getNombre();
					$cuentas = $this->Cuentas->find("cuenta LIKE '$clase%' AND subclase<>'' AND auxiliar=''");
					if(count($cuentas)>0){
						$cuentasAuxiliares = array();
						foreach($cuentas as $cuentaAuxiliar){
							$cuentasAuxiliares[] = array(
								'cuenta' => $cuentaAuxiliar->getCuenta(),
								'auxiliar' => $cuentaAuxiliar->getSubclase().$cuentaAuxiliar->getAuxiliar().$cuentaAuxiliar->getSubaux(),
								'nombre' => $cuentaAuxiliar->getNombre()
							);
						}
					}
				}
			}

			$subclaseDetalle = '';
			if(strlen($codigoCuenta)>5){
				$subclase = substr($codigoCuenta, 0, 6);
				$subclaseCuenta = $this->Cuentas->findFirst("cuenta='$subclase'");
				if($subclaseCuenta){
					$subclaseDetalle = $subclaseCuenta->getNombre();
					$cuentas = $this->Cuentas->find("cuenta LIKE '$subclase%' AND auxiliar<>'' AND subaux=''", "limit: 15");
					if(count($cuentas)>0){
						$cuentasAuxiliares = array();
						foreach($cuentas as $cuentaAuxiliar){
							$cuentasAuxiliares[] = array(
								'cuenta' => $cuentaAuxiliar->getCuenta(),
								'auxiliar' => $cuentaAuxiliar->getAuxiliar().$cuentaAuxiliar->getSubaux(),
								'nombre' => $cuentaAuxiliar->getNombre()
							);
						}
					}
				}
			}

			$auxiliarDetalle = '';
			if(strlen($codigoCuenta)>8){
				$auxiliar = substr($codigoCuenta, 0, 9);
				$auxiliarCuenta = $this->Cuentas->findFirst("cuenta='$auxiliar'");
				if($auxiliarCuenta){
					$auxiliarDetalle = $auxiliarCuenta->getNombre();
					$cuentas = $this->Cuentas->find("cuenta LIKE '$auxiliar%' AND es_auxiliar='S'", "limit 15");
					if(count($cuentas)>0){
						$cuentasAuxiliares = array();
						foreach($cuentas as $cuentaAuxiliar){
							$cuentasAuxiliares[] = array(
								'cuenta' => $cuentaAuxiliar->getCuenta(),
								'auxiliar' => $cuentaAuxiliar->getSubaux(),
								'nombre' => $cuentaAuxiliar->getNombre()
							);
						}
					}
				}
			}

			return array(
				'existe' => 'S',
				'cuenta' => $cuenta->getCuenta(),
				'nombre' => $cuenta->getNombre(),
				'esAuxiliar' => $cuenta->getEsAuxiliar(),
				'tipoDetalle' => $tipoDetalle,
				'mayorDetalle' => $mayorDetalle,
				'claseDetalle' => $claseDetalle,
				'subclaseDetalle' => $subclaseDetalle,
				'auxiliarDetalle' => $auxiliarDetalle,
				'cuentasAuxiliares' => $cuentasAuxiliares
			);
		} else {
			return array(
				'existe' => 'N'
			);
		}
	}

	public function queryByCuentaAction(){
		$this->setResponse('json');
		$codigoCuenta = $this->getQueryParam('cuenta', 'cuentas');
		$cuenta = $this->Cuentas->findFirst("cuenta='$codigoCuenta'");
		if($cuenta==false){
			return array(
				'status' => 'FAILED',
				'message' => 'NO EXISTE LA CUENTA CONTABLE'
			);
		} else {
			return array(
				'status' => 'OK',
				'nombre' => $cuenta->getNombre()
			);
		}
	}

	public function queryByNameAction(){
		$this->setResponse('json');
		$response = array();
		$nombre = $this->getPostParam('nombre', 'extraspaces');
		if($nombre!=''){
			$nombre = preg_replace('/[ ]+/', '%', $nombre);
			$cuentas = $this->Cuentas->find('nombre LIKE \''.$nombre.'%\' AND es_auxiliar=\'S\'', 'order: nombre', 'limit: 13');
			foreach($cuentas as $cuenta){
				$response[] = array(
					'value' => $cuenta->getCuenta(),
					'selectText' => $cuenta->getNombre(),
					'optionText' => $cuenta->getNombre().' '.$cuenta->getCuenta()
				);
			}
		}
		return $response;
	}

	public function queryAllByCodigoAction(){
		$this->setResponse('json');
		$cuenta = $this->getPostParam('codigo', 'int');
		$response = array();

		$conditions = array();
		$codigoCuenta = $this->getPostParam('codigo', 'int');
		$nombreCuenta = $this->getPostParam('nombre', 'extraspaces');
		if($codigoCuenta!=0){
			$conditions[] = "cuenta LIKE '$codigoCuenta%'";
		}
		if($nombreCuenta!=''){
			$nombreCuenta = preg_replace('/[ ]+/', '%', $nombreCuenta);
			$conditions[] = "nombre LIKE '$nombreCuenta%'";
		}
		$cuentas = $this->Cuentas->find(array(join(' AND ', $conditions), 'order' => 'LENGTH(cuenta),cuenta', 'limit' => 50));
		foreach($cuentas as $cuenta){
			$response[] = array(
				'codigo' => $cuenta->getCuenta(),
				'nombre' => $cuenta->getNombre(),
				'esAuxiliar' => $cuenta->getEsAuxiliar()
			);
		}
		return $response;
	}

	public function queryAllByNombreAction(){

		$this->setResponse('json');
		$response = array();

		$conditions = array();
		$codigoCuenta = $this->getPostParam('codigo', 'int');
		$nombreCuenta = $this->getPostParam('nombre', 'extraspaces');
		if($codigoCuenta!=0){
			$conditions[] = "cuenta LIKE '$codigoCuenta%'";
		}
		if($nombreCuenta!=''){
			$nombreCuenta = preg_replace('/[ ]+/', '%', $nombreCuenta);
			$conditions[] = "nombre LIKE '%$nombreCuenta%'";
		}
		$cuentas = $this->Cuentas->find(array(join(' AND ', $conditions), 'order' => 'nombre', 'limit' => 50));
		foreach($cuentas as $cuenta){
			$response[] = array(
				'codigo' => $cuenta->getCuenta(),
				'nombre' => $cuenta->getNombre(),
				'esAuxiliar' => $cuenta->getEsAuxiliar()
			);
		}
		return $response;
	}

}