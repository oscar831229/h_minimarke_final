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
 * Campos_MediosController
 *
 * Campos y cuentas de los formatos de medios magnéticos
 *
 */
class Campos_MediosController extends ApplicationController {

	private $_campos = array(
		'Cpt' => 'Concepto',
		'Tdoc' => 'Tipo de Documento',
		'Nid' => 'Número de Identificación',
		'Dv' => 'Digito de Verificación',
		'Apl1' => 'Primer Apellido',
		'Apl2' => 'Segundo Apellido',
		'Nom1' => 'Primer Nombre',
		'Nom2' => 'Segundo Nombre',
		'Raz' => 'Razón Social',
		'Dir' => 'Dirección',
		'Dpto' => 'Departamento',
		'Mun' => 'Municipio',
		'Pais' => 'País',
		'Pag' => 'Valor del pago o abono',
		'Por' => 'Porcentaje de participación',
		'Ded' => 'Pagos ó Abonos en cuenta que no constituyan costo ó deducción',
		'Ret' => 'Retención que le prácticaron',
		'Sal' => 'Saldo al 31 de Diciembre',
		'Vabo' => 'Valor abono o pago sujeto a retención',
		'Val' => 'Valor Patrimonial',
		'Vimp' => 'Valor del impuesto descontable (1005) ó Generado (1006)',
		'Vdes' => 'Valor solicitado como descuento',
		'Vpag' => 'Valor acumulado del pago',
		'Vpar' => 'Valor participación',
		'Vret' => 'Valor de la retención'
	);

	private $_camposCalculos = array(
		'Pag' => 'Valor del pago o abono',
		'Por' => 'Porcentaje de participación',
		'Ded' => 'Pagos ó Abonos en cuenta que no constituyan costo ó deducción',
		'Ret' => 'Retención que le prácticaron',
		'Sal' => 'Saldo al 31 de Diciembre',
		'Vabo' => 'Valor abono o pago sujeto a retención',
		'Val' => 'Valor Patrimonial',
		'Vimp' => 'Valor del impuesto descontable (1005) ó Generado (1006)',
		'Vdes' => 'Valor solicitado como descuento',
		'Vpag' => 'Valor acumulado del pago',
		'Vpar' => 'Valor participación',
		'Vret' => 'Valor de la retención'
	);

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$formatos = array();
		foreach($this->Magfor->find("order: codfor") as $magfor){
			$formatos[$magfor->getCodFor()] = $magfor->getCodFor().' : '.utf8_encode($magfor->getNombre());
		}
		$this->setParamToView('formatos', $formatos);
		$this->setParamToView('message', 'Seleccione el formato y código para editar los campos y rangos de cuentas asociados');
	}

	public function getCamposAction(){
		$this->setResponse('view');
		$codigosFormato = array(
			'0' => '0 : NINGUNO'
		);
		$camposFormato = array();
		$codigoFormato = $this->getPostParam('codigoFormato', 'int');
		if($codigoFormato>0){
			foreach($this->Magcam->find("codfor='$codigoFormato'", "order: posicion") as $campoFormato){
				$camposFormato[$campoFormato->getCampo()] = 1;
			}
			$codigoFormato = $this->getPostParam('codigoFormato', 'int');
			foreach($this->Magcod->find("codfor='$codigoFormato'", "order: codigo") as $magcod){
				$codigosFormato[$magcod->getCodigo()] = $magcod->getCodigo().' : '.utf8_encode($magcod->getNombre());
			}
		}
		$this->setParamToView('campos', $this->_campos);
		$this->setParamToView('camposCalculos', $this->_camposCalculos);
		$this->setParamToView('camposFormato', $camposFormato);
		$this->setParamToView('codigosFormato', $codigosFormato);
		$this->setParamToView('cuentasFormato', $this->Magcue->find("codfor='$codigoFormato'"));
	}

	public function guardarAction(){
		$this->setResponse('json');

		$controllerRequest = ControllerRequest::getInstance();
		$codigoFormato = $controllerRequest->getParamPost('magfor', 'int');
		if($codigoFormato>0){
			if(!$controllerRequest->isSetPostParam('camposFormato')){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique los campos que lleva el formato seleccionado'
				);
			}
			try {

				$transaction = TransactionManager::getUserTransaction();

				$codigos = $this->getPostParam('codigos', 'int');
				$campos = $this->getPostParam('campos', 'alpha');
				$cuentaIniciales = $this->getPostParam('cueini', 'cuentas');
				$cuentaFinales = $this->getPostParam('cuefin', 'cuentas');

				$camposSeleccionados = array();
				$camposFormato = $this->getPostParam('camposFormato', 'alpha');

				$position = 1;
				$this->Magcam->setTransaction($transaction);
				$this->Magcam->deleteAll("codfor='$codigoFormato'");
				foreach($camposFormato as $campoFormato){
					$magcam = new Magcam();
					$magcam->setTransaction($transaction);
					$magcam->setCodfor($codigoFormato);
					$magcam->setCampo($campoFormato);
					$magcam->setPosicion($position);
					if($magcam->save()==false){
						foreach($magcam->getMessages() as $message){
							$transaction->rollback('Campos Formato: '.$message->getMessage());
						}
					}
					$camposSeleccionados[$campoFormato] = true;
					$position++;
				}

				$position = 0;
				$rangosValidos = 0;

				$this->Magcue->setTransaction($transaction);
				$this->Magcue->deleteAll("codfor='$codigoFormato'");
				foreach($campos as $campo){
					if($cuentaIniciales[$position]!=''&&$cuentaFinales[$position]!=''){
						if(isset($camposSeleccionados[$campos[$position]])){
							$magcue = new Magcue();
							$magcue->setTransaction($transaction);
							$magcue->setCodfor($codigoFormato);
							$magcue->setCodigo($codigos[$position]);
							$magcue->setCampo($campos[$position]);
							$magcue->setCueini($cuentaIniciales[$position]);
							$magcue->setCuefin($cuentaFinales[$position]);
							if($magcue->save()==false){
								foreach($magcue->getMessages() as $message){
									$transaction->rollback('Rangos Cuentas:'.$message->getMessage());
								}
							}
							$rangosValidos++;
						} else {
							$transaction->rollback('Ha indicado un rango de cuentas para el campo "'.$this->_campos[$campos[$position]].'" pero este no aparece en la lista de campos del formato');
						}
					}
					$position++;
				}
				$transaction->commit();
			}
			catch(TransactionFailed $e){
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}
			return array(
				'status' => 'OK',
				'message' => 'Se actualizaron correctamente los campos y cuentas del formato'
			);
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'Indique el formato a configurar'
			);
		}
	}

}