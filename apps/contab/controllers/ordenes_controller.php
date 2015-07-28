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
 * OrdenesController
 *
 * Controlador de Ordenes de Servicio
 *
 */
class OrdenesController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('message', 'Ingrese un criterio de búsqueda para consultar ordenes de servicio');
	}

	public function buscarAction(){

		$this->setResponse('json');

		$numeroInicial = $this->getPostParam('numeroInicial', 'int');
		$numeroFinal = $this->getPostParam('numeroFinal', 'int');

		$terceroInicial = $this->getPostParam('nitInicial', 'terceros');
		$terceroFinal = $this->getPostParam('nitFinal', 'terceros');

		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		$numeroFactura = $this->getPostParam('numeroFactura', 'int');

		$contabilizada = $this->getPostParam('contabilizada');

		$response = array();

		$conditions = array();
		if($terceroInicial!=''&&$terceroFinal!=''){
			$conditions[] = 'nit >= \''.$terceroInicial.'\' AND nit <= \''.$terceroFinal.'\'';
		}
		if($fechaInicial!=''&&$fechaFinal!=''){
			$conditions[] = 'fecha >= \''.$fechaInicial.'\' AND fecha <= \''.$fechaFinal.'\'';
		}
		if($numeroInicial>0&&$numeroFinal>0){
			$conditions[] = 'numero >= \''.$numeroInicial.'\' AND numero <= \''.$numeroFinal.'\'';
		}
		if($numeroFactura>0){
			$conditions[] = 'num_fac = \''.$numeroFactura.'\'';
		}
		if($contabilizada=='S'){
			$conditions[] = "nota='D'";
		} else {
			if($contabilizada=='N'){
				$conditions[] = "nota=''";
			}
		}

		$params = array(
			'columns' => 'comprob,numero,fecha,nit,nota',
			'group' => 'comprob,numero,fecha,nit,nota',
			'order' => 'fecha DESC,numero DESC',
			'limit' => 250
		);
		if(count($conditions)>0){
			$params[0] = join(' AND ', $conditions);
		}
		$ordenes = $this->Oserv->find($params);
		if(count($ordenes)==0){
			$response['number'] = '0';
		} else {
			if(count($ordenes)==1){
				$orden = $ordenes->getFirst();
				$response['number'] = '1';
				$response['key'] = 'codigoComprob='.$orden->getComprob().'&numero='.$orden->getNumero();
			} else {
				$responseResults = array(
					'headers' => array(
						array('name' => 'Comprobante', 'ordered' => 'N'),
						array('name' => 'Número', 'ordered' => 'N'),
						array('name' => 'Fecha', 'ordered' => 'S'),
						array('name' => 'Tercero', 'ordered' => 'N'),
						array('name' => 'Contabilizada', 'ordered' => 'N')
					)
				);
				$data = array();
				foreach($ordenes as $orden){
					$tercero = BackCacher::getTercero($orden->getNit());
					if($tercero==false){
						$terceroNombre = 'NO EXISTE EL TERCERO';
					} else {
						$terceroNombre = $tercero->getNombre();
					}
					$data[] = array(
						'primary' => array('codigoComprob='.$orden->getComprob().'&numero='.$orden->getNumero()),
						'data' => array(
							array('key' => 'comprob', 'value' => $orden->getComprob()),
							array('key' => 'numero', 'value' => $orden->getNumero()),
							array('key' => 'fecha', 'value' => (string)$orden->getFecha()),
							array('key' => 'nit', 'value' => $terceroNombre),
							array('key' => 'contab', 'value' => ($orden->getNota()=='D' ? 'SI' : 'NO')),
						)
					);
				}
				$responseResults['data'] = $data;
				$response['numberResults'] = count($responseResults['data']);
				$response['results'] = $responseResults;
				$response['number'] = 'n';
			}
		}

		return $response;
	}

	public function editarAction(){

		$codigoComprob = $this->getPostParam('codigoComprob', 'comprob');
		$numero = $this->getPostParam('numero', 'int');

		if($numero>0){
			$ordenes = $this->Oserv->find("comprob='$codigoComprob' AND numero='$numero'");
			if(count($ordenes)==0){
				Flash::error('No existe la orden de servicio');
				return $this->routeTo('errores');
			}
		} else {
			Flash::error('No existe la orden de servicio');
			return $this->routeTo('errores');
		}

		$orden = $ordenes->getFirst();
		Tag::displayTo('codigoComprob', $orden->getComprob());
		Tag::displayTo('numero', $orden->getNumero());
		Tag::displayTo('nit', $orden->getNit());
		Tag::displayTo('fechaOrden', (string)$orden->getFecha());

		$this->setParamToView('ordenes', $ordenes);
		$this->setParamToView('centros', $this->Centros->find());

		if($orden->getNota()=='D'){
			$this->setParamToView('message', 'La orden de servicio ya fue contabilizada con factura '.$orden->getNumFac());
		} else {
			$this->setParamToView('message', 'Indique los items de servicio de la orden y haga click en "Grabar"');
		}

	}

	public function nuevaAction(){
		$this->setParamToView('centros', $this->Centros->find());
		Tag::displayTo('fechaOrden', Date::getCurrentDate());
		$this->setParamToView('message', 'Indique los items de servicio de la orden y haga click en "Grabar"');
	}

	public function guardarAction(){
		$this->setResponse('json');

		$nit = $this->getPostParam('nit', 'terceros');
		if($nit==''){
			return array(
				'status' => 'FAILED',
				'message' => 'Indique el tercero de la orden'
			);
		} else {
			$tercero = BackCacher::getTercero($nit);
			if($tercero==false){
				return array(
					'status' => 'FAILED',
					'message' => 'El tercero no existe'
				);
			}
		}

		$empresa = $this->Empresa->findFirst();

		$fechaOrden = $this->getPostParam('fechaOrden', 'date');
		if(!Date::isLater($fechaOrden, $empresa->getFCierrec())){
			return array(
				'status' => 'FAILED',
				'message' => 'La fecha de la orden debe estar dentro del periodo contable activo'
			);
		}

		$codigoComprob = Settings::get('comprob_ordenes');
		if($codigoComprob==null){
			return array(
				'status' => 'FAILED',
				'message' => 'No se ha definido el comprobante de ordenes de servicio'
			);
		} else {
			$comprob = $this->Comprob->findFirst("codigo='$codigoComprob'");
			if($comprob==false){
				return array(
					'status' => 'FAILED',
					'message' => 'No existe el comprobante '.$codigoComprob
				);
			}
		}

		try {

			$transaction = TransactionManager::getUserTransaction();

			$request = ControllerRequest::getInstance();
			if($request->isSetPostParam('numero')==false){
				$maxNumero = ($this->Oserv->maximum(array('numero', 'conditions' => "comprob='$codigoComprob'"))+1);
				if($comprob->getConsecutivo()<$maxNumero){
					$numero = $maxNumero;
				} else {
					$numero = $comprob->getConsecutivo();
				}
			} else {

				$codigoComprob = $this->getPostParam('codigoComprob', 'comprob');
				$numero = $this->getPostParam('numero', 'int');

				$ordenes = $this->Oserv->find("comprob='$codigoComprob' AND numero='$numero'");
				if(count($ordenes)==0){
					return array(
						'status' => 'FAILED',
						'message' => 'No existe el comprobante'
					);
				}

				$orden = $ordenes->getFirst();
				if($orden->getNota()=='D'){
					return array(
						'status' => 'FAILED',
						'message' => 'No se puede modificar la orden porque ya fue contabilizada'
					);
				}

				$this->Oserv->setTransaction($transaction);
				$this->Oserv->deleteAll("comprob='$codigoComprob' AND numero='$numero'");

			}

			$comprob->setTransaction($transaction);
			$comprob->setConsecutivo($numero+1);

			$items = $this->getPostParam('item');
			$valores = $this->getPostParam('valor', 'double');
			$centroCosto = $this->getPostParam('centroCosto', 'int');
			$descripciones = $this->getPostParam('descripcion', 'striptags');

			$validRows = 0;
			$numberRows = count($items);
			for($i=0;$i<$numberRows;$i++){
				if($valores[$i]>0){
					if($items[$i]<=0){
						$transaction->rollback('Indique el item de servicio en la línea '.($i+1));
					} else {
						$refe = BackCacher::getRefe($items[$i]);
						if($refe==false){
							$transaction->rollback('El item de servicio no existe en la línea '.($i+1));
						}
					}
					if(trim($descripciones[$i])==''){
						$transaction->rollback('Indique la descripción del item de servicio en la línea '.($i+1));
					}
					$oserv = new Oserv();
					$oserv->setTransaction($transaction);
					$oserv->setComprob($codigoComprob);
					$oserv->setNumero($numero);
					$oserv->setFecha($fechaOrden);
					$oserv->setNit($nit);
					$oserv->setItem($items[$i]);
					$oserv->setCentroCosto($centroCosto[$i]);
					$oserv->setDescripcion($descripciones[$i]);
					$oserv->setValor($valores[$i]);
					if($oserv->save()==false){
						foreach($oserv->getMessages() as $message){
							$transaction->rollback('Oserv: '.$message->getMessage());
						}
					}
					$validRows++;
				}
			}

			if($validRows==0){
				$transaction->rollback('Ingrese al menos una línea de servicio');
			}

			if($comprob->save()==false){
				foreach($comprob->getMessages() as $message){
					$transaction->rollback('Comprobante: '.$message->getMessage());
				}
			}

			$transaction->commit();
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			);
		}

		if($request->isSetPostParam('numero')==false){
			return array(
				'status' => 'OK',
				'message' => 'Se creó el comprobante '.$comprob->getNomComprob().'/'.$numero
			);
		} else {
			return array(
				'status' => 'OK',
				'message' => 'Se actualizó el comprobante '.$comprob->getNomComprob().'/'.$numero
			);
		}

	}

	public function eliminarAction(){

		$this->setResponse('json');

		$codigoComprob = $this->getPostParam('codigoComprob', 'comprob');
		$numero = $this->getPostParam('numero', 'int');

		$comprob = $this->Comprob->findFirst("codigo='$codigoComprob'");
		if($comprob==false){
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el comprobante'
			);
		}

		$ordenes = $this->Oserv->find("comprob='$codigoComprob' AND numero='$numero'");
		if(count($ordenes)==0){
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el comprobante'
			);
		}

		$orden = $ordenes->getFirst();
		if($orden->getNota()=='D'){
			return array(
				'status' => 'FAILED',
				'message' => 'No se puede eliminar la orden porque ya fue contabilizada'
			);
		}

		$this->Oserv->deleteAll("comprob='$codigoComprob' AND numero='$numero'");

		return array(
			'status' => 'OK',
			'message' => 'Se eliminó el comprobante '.$comprob->getNomComprob().'/'.$numero
		);
	}

	public function contabilizarAction(){
		$this->setResponse('json');
		try {

			$transaction = TransactionManager::getUserTransaction();

			$codigoComprob = $this->getPostParam('codigoComprob', 'comprob');
			$numero = $this->getPostParam('numero', 'int');

			$comprob = $this->Comprob->findFirst("codigo='$codigoComprob'");
			if($comprob==false){
				$transaction->rollback('No existe el comprobante');
			}

			$ordenes = $this->Oserv->find("comprob='$codigoComprob' AND numero='$numero'");
			if(count($ordenes)==0){
				$transaction->rollback('No existe el comprobante');
			}

			$orden = $ordenes->getFirst();
			if($orden->getNota()=='D'){
				$transaction->rollback('No se puede contabilizar la orden porque ya fue contabilizada');
			}

			$tercero = BackCacher::getTercero($orden->getNit());
			if($tercero==false){
				$transaction->rollback('El tercero "'.$orden->getNit().'" de la orden de servicio no existe');
			}

			$comprobContab = $this->Comprob->findFirst(array("codigo='{$comprob->getComprobContab()}'", 'for_update' => true));
			if($comprobContab==false){
				$transaction->rollback('No existe el comprobante de contabilización de ordenes '.$comprob->getComprobContab());
			}

			//echo "<br>","comprob='{$comprob->getComprobContab()}'";
			/*
			///Rombre esquema
			$maxNumero = ($this->Oserv->maximum(array('numero', 'conditions' => "comprob='{$comprob->getComprobContab()}'"))+1);
			if($comprob->getConsecutivo()<$maxNumero){
				$consecutivo = $maxNumero;
			} else {*/
				$consecutivo = $comprobContab->getConsecutivo();
			//}

			$empresa = $this->Empresa->findFirst();
			$terceroEmpresa = BackCacher::getTercero($empresa->getNit());
			if($terceroEmpresa==false){
				$transaction->rollback('Es necesario crear el hotel como un tercero');
			}
			if($terceroEmpresa->getEstadoNit()!=''){
				$regimenHotel = $terceroEmpresa->getEstadoNit();
			} else {
				$regimenHotel = 'G';
			}

			$fechaMovi = $this->getPostParam('fechaMovi', 'date');
			$fechaMovi = Date::getCurrentDate();

			try {

				//echo "comprob: ",$comprobContab->getCodigo(),", numero: ",$consecutivo;

				$aura = new Aura($comprobContab->getCodigo(), $consecutivo, null, Aura::OP_CREATE);

				$linea = 1;

				$movis = array();

				foreach ($ordenes as $orden) {

					$item = $orden->getItem();

					$valorIva = 0;
					$valorMonto = 0;
					$refe = BackCacher::getRefe($orden->getItem());
					if($refe==false){
						$transaction->rollback('No existe el item de servicio "'.$orden->getItem().'" en la línea '.$linea);
					}

					$lineaser = $refe->getLineaser();
					if($lineaser==false){
						$transaction->rollback('No existe la línea de servicio "'.$refe->getLinea().'" en la línea '.$linea);
					}

					$cuentaGasto = BackCacher::getCuenta($lineaser->getCtaGasto());
					if($cuentaGasto==false){
						$transaction->rollback('La cuenta de gasto no existe ó no es auxiliar para la línea de servicio '.$lineaser->getLinea().' en la línea '.$linea);
					}

					//////DEBITOS/////
					$valorMontoDebitos[$item] = 0;
					if(!isset($movis[$item][$cuentaGasto->getCuenta()])){

						$movis[$item][$cuentaGasto->getCuenta()] = array(
							'Fecha' 	  => $fechaMovi,
							'Cuenta' 	  => $cuentaGasto->getCuenta(),
							'Nit' 		  => $orden->getNit(),
							'Descripcion' => $orden->getDescripcion(),
							'CentroCosto' => $orden->getCentroCosto(),
							'Valor' 	  => LocaleMath::round($orden->getValor(),2),
							'BaseGrab' 	  => 0,
							'DebCre' 	  => 'D'
						);
						$valorMonto+=$orden->getValor();
						$valorMontoDebitos[$item]+=$orden->getValor();
					} else {
						if($movis[$item][$cuentaGasto->getCuenta()]['DebCre']=='D'){
							$movis[$item][$cuentaGasto->getCuenta()]['Valor']+=LocaleMath::round($orden->getValor(),2);
							$valorMonto+=$orden->getValor();
							$valorMontoDebitos[$item]+=$orden->getValor();
						}
					}

					/*$aura->addMovement(array(
						'Fecha' => $fechaMovi,
						'Cuenta' => $cuentaGasto->getCuenta(),
						'Nit' => $orden->getNit(),
						'Descripcion' => $orden->getDescripcion(),
						'CentroCosto' => $orden->getCentroCosto(),
						'Valor' => $orden->getValor(),
						'BaseGrab' => 0,
						'DebCre' => 'D'
					));*/

					if($lineaser->getPorcIva()>0){
						$valorMontoCredito[$item] = 0;

						if($tercero->getEstadoNit()=='S'){
							$cuenta = BackCacher::getCuenta($lineaser->getCtaEx1());
							if($cuenta==false){
								$transaction->rollback('No existe la cuenta de IVA para regímen simplificado en la línea de servicio '.$lineaser->getLinea());
							}
						} else {
							$cuenta = BackCacher::getCuenta($lineaser->getCtaIva());
							if($cuenta==false){
								$transaction->rollback('No existe la cuenta de IVA para otros regímenes en la línea de servicio '.$lineaser->getLinea());
							}
						}

						if ($cuenta->getPorcIva() > 0) {

							$valorIva = $orden->getValor() * $cuenta->getPorcIva() / 100;
							$valorIva = LocaleMath::round($valorIva, 2);

							if ($valorIva > 0 ) {
								$cta = $cuenta->getCuenta();
								if(!isset($movis[$item][$cta])){
									$movis[$item][$cta] = array(
										'Fecha' 	  => $fechaMovi,
										'Cuenta' 	  => $cta,
										'Descripcion' => 'IVA FAC. '.$consecutivo,
										'Nit' 		  => $orden->getNit(),
										'CentroCosto' => $orden->getCentroCosto(),
										'Valor' 	  => $valorIva,
										'BaseGrab' 	  => $orden->getValor(),
										'DebCre' 	  => 'D'
									);
									$valorMonto += $valorIva;
									$valorMontoCredito[$item] -= $valorIva;
								} else {
									if ($movis[$item][$cta]['DebCre'] == 'D') {
										$movis[$item][$cta]['Valor'] += $valorIva;
										$movis[$item][$cta]['Descripcion'] .= '+ IVA FAC. ' . $consecutivo;
										$valorMonto += $valorIva;
										$valorMontoCredito[$item] -= $valorIva;
									}
								}
							}

							/*$aura->addMovement(array(
								'Fecha' => $fechaMovi,
								'Cuenta' => $cuenta->getCuenta(),
								'Descripcion' => 'IVA FAC. '.$consecutivo,
								'Nit' => $orden->getNit(),
								'CentroCosto' => $orden->getCentroCosto(),
								'Valor' => $valorIva,
								'BaseGrab' => $orden->getValor(),
								'DebCre' => 'D'
							));*/

						}

						//////CREDITOS/////
						$useDebcreT = false;
						if ($valorIva > 0) {
							if (
								!(
									$tercero->getTipoNit() == 1
									||
									$regimenHotel == $tercero->getEstadoNit()
									||
									(
										$regimenHotel == 'C' && $tercero->getEstadoNit()!='S'
									)
								)
							){
								$cta = $cuenta->getCuenta();
								if(!isset($movis[$item][$cta])){

									$movis[$item][$cta] = array(
										'Fecha' 	  => $fechaMovi,
										'Cuenta' 	  => $cta,
										'Descripcion' => 'RETENCION POR IVA FAC. '.$consecutivo,
										'Nit' 	  	  => $orden->getNit(),
										'CentroCosto' => $orden->getCentroCosto(),
										'Valor' 	  => $valorIva,
										'BaseGrab' 	  => $orden->getValor(),
										'DebCre' 	  => 'C'
									);

									$valorMonto -= $valorIva;
									$valorMontoCredito[$item] += $valorIva;
									$useDebcreT = true;
								} else {
									if($movis[$item][$cta]['DebCre']=='C'){
										$movis[$item][$cta]['Valor'] += $valorIva;
										$valorMonto -= $valorIva;
										$valorMontoCredito[$item] += $valorIva;
									}
								}

								/*$aura->addMovement(array(
									'Fecha' => $fechaMovi,
									'Cuenta' => $cuenta->getCuenta(),
									'Descripcion' => 'RETENCION POR IVA FAC. '.$consecutivo,
									'Nit' => $orden->getNit(),
									'CentroCosto' => $orden->getCentroCosto(),
									'Valor' => $valorIva,
									'BaseGrab' => $orden->getValor(),
									'DebCre' => 'C'
								));*/
							}
						}
					}

					if($tercero->getAutoret()!='S'){

						$cuenta = BackCacher::getCuenta($lineaser->getCtaRetencion());
						if($cuenta!=false){
							if($cuenta->getPorcIva()>0){
								$valorRetencion = $orden->getValor()*$cuenta->getPorcIva();

								$valorRetencion = LocaleMath::round($valorRetencion, 2);
								if ($valorRetencion > 0) {

									if(!isset($movis[$item][$cuenta->getCuenta()])){

										$movis[$item][$cuenta->getCuenta()] = array(
											'Fecha' 	  => $fechaMovi,
											'Cuenta' 	  => $cuenta->getCuenta(),
											'Descripcion' => $cuenta->getNombre(),
											'Nit' 	      => $orden->getNit(),
											'CentroCosto' => $orden->getCentroCosto(),
											'Valor' 	  => $valorRetencion,
											'BaseGrab' 	  => $orden->getValor(),
											'FechaVence'  => $fechaMovi,
											'DebCre' 	  => 'C'
										);
										$valorMonto-=$valorRetencion;
										if(!isset($valorMontoCredito[$item])){
											$valorMontoCredito[$item] = 0;
										}
										$valorMontoCredito[$item]+=$valorRetencion;
										$useDebcreT = false;
									} else {

										if(!isset($valorMontoCredito[$item])){
											$valorMontoCredito[$item] = 0;
										}
										if(isset($movis[$item][$cuenta->getCuenta()]) && $movis[$item][$cuenta->getCuenta()]['DebCre']=='C'){
											$valorMonto-=$valorRetencion;
											if($item!=$oldItem){
												$valorMontoCredito[$item]=$valorRetencion;
												$movis[$item][$cuenta->getCuenta()]['Valor']=$valorRetencion;
											} else {
												$movis[$item][$cuenta->getCuenta()]['Valor']+=$valorRetencion;
												$valorMontoCredito[$item]+=$valorRetencion;
											}
										}
									}
								}

								/*$aura->addMovement(array(
									'Fecha' => $fechaMovi,
									'Cuenta' => $cuenta->getCuenta(),
									'Descripcion' => $cuenta->getNombre(),
									'Nit' => $orden->getNit(),
									'CentroCosto' => $orden->getCentroCosto(),
									'Valor' => $valorRetencion,
									'BaseGrab' => $orden->getValor(),
									'FechaVence' => $fechaMovi,
									'DebCre' => 'C'
								));*/

							}
						}

						if(!isset($valorMontoCredito[$item])){
							$valorMontoCredito[$item] = 0;
						}

						$ica = $this->Ica->findFirst("codigo='{$tercero->getApAereo()}'");
						if($ica!=false){
							$cuenta = BackCacher::getCuenta($ica->getCuenta());
							$valorIca = $orden->getValor()*($ica->getCodigo()/1000);

							$valorIca = LocaleMath::round($valorIca, 2);

							if ($valorIca > 0) {

								if(!isset($movis[$item][$cuenta->getCuenta()])){

									$movis[$item][$cuenta->getCuenta()] = array(
										'Fecha' 	  => $fechaMovi,
										'Cuenta' 	  => $cuenta->getCuenta(),
										'Descripcion' => $cuenta->getNombre(),
										'Nit' 		  => $orden->getNit(),
										'CentroCosto' => $orden->getCentroCosto(),
										'Valor' 	  => $valorIca,
										'BaseGrab' 	  => $orden->getValor(),
										'DebCre' 	  => 'C'
									);
									$valorMonto-=$valorIca;
									$valorMontoCredito[$item]+=$valorIca;
									$useDebcreT = false;
								} else {
									if($movis[$item][$cuenta->getCuenta()]['DebCre']=='C'){
										$valorMonto-=$valorIca;
									}
								}
							}


							/*$aura->addMovement(array(
								'Fecha' => $fechaMovi,
								'Cuenta' => $cuenta->getCuenta(),
								'Descripcion' => $cuenta->getNombre(),
								'Nit' => $orden->getNit(),
								'CentroCosto' => $orden->getCentroCosto(),
								'Valor' => $valorIca,
								'BaseGrab' => $orden->getValor(),
								'DebCre' => 'C'
							));*/


						}

					}

					//Cuenta retencion iva de linea
					if($lineaser->getCtaRetiva()>0){
						$cuenta = BackCacher::getCuenta($lineaser->getCtaRetiva());
						if (!$cuenta){
							$transaction->rollback('No existe la cuenta de rete-iva "'.$lineaser->getCtaRetiva().'" en la línea '.$linea);
						}
						$valorIva = $orden->getValor()*$cuenta->getPorcIva();
						if($tercero->getEstadoNit()=='S'){
							$valorIva*=0.5;
						}

						$valorIva = LocaleMath::round($valorIva, 2);

						//echo "<br>", $cuenta->getPorcIva();

						if ($valorIva>0) {
							if(!isset($movis[$item][$cuenta->getCuenta()])){

								$movis[$item][$cuenta->getCuenta()] = array(
									'Fecha' 		=> $fechaMovi,
									'Cuenta' 		=> $cuenta->getCuenta(),
									'Descripcion' 	=> $cuenta->getNombre(),
									'Nit' 			=> $orden->getNit(),
									'CentroCosto' 	=> $orden->getCentroCosto(),
									'Valor' 		=> $valorIva,
									'BaseGrab' 		=> $orden->getValor(),
									'DebCre' 		=> 'C'
								);
								$valorMonto-=$valorIva;
								//echo "<br>valorMonto 8: ",$valorMonto;
								//$valorMontoCredito[$item]+=$valorIva;
								//echo "<br>8valorMontoCredito: ",$valorMontoCredito[$item];
								$useDebcreT = false;
							} else {
								if($movis[$item][$cuenta->getCuenta()]['DebCre']=='C'){
									//$movis[$item][$cuenta->getCuenta()]['Valor']+=$valorIca;
									$valorMonto-=$valorIva;
								}
							}
						}
					}


					if(isset($valorMontoCredito[$item])){
						$valorMonto -= $valorMontoCredito[$item];
						$valorT = ($valorMontoDebitos[$item]-$valorMontoCredito[$item]);
					} else {
						if(isset($valorMontoDebitos[$item])){
							$valorT = $valorMontoDebitos[$item];
						}else{
							$valorT = $valorMonto;
						}
					}

					$cuenta = BackCacher::getCuenta($lineaser->getCtaCartera());
					if($cuenta==false){
						$transaction->rollback('No existe la cuenta de cuentas por pagar de la linea "'.$lineaser->getDescripcion().'" en la línea '.$linea);
					}

					if(!isset($movis[$item][$cuenta->getCuenta()])){

						$movis[$item][$cuenta->getCuenta()] = array(
							'Fecha' 	  	  => $fechaMovi,
							'Cuenta' 	  	  => $cuenta->getCuenta(),
							'Descripcion' 	  => 'FACTURA No. '.$consecutivo,
							'Nit' 		  	  => $orden->getNit(),
							'CentroCosto' 	  => $orden->getCentroCosto(),
							//'Valor' => $valorMonto,
							'Valor' 	  	  => $valorT,
							'TipoDocumento'   => 'FAC',
							'NumeroDocumento' => $consecutivo,
							'BaseGrab' 		  => 0,
							'FechaVence' 	  => $fechaMovi,
							'DebCre' 		  => 'C'
						);
					} else {
						if($movis[$item][$cuenta->getCuenta()]['DebCre']=='C'){
							//$movis[$item][$cuenta->getCuenta()]['Valor']+=$valorMonto;
							$movis[$item][$cuenta->getCuenta()]['Valor']+=$valorT;
						}
					}

					/*$aura->addMovement(array(
						'Fecha' => $fechaMovi,
						'Cuenta' => $cuenta->getCuenta(),
						'Descripcion' => 'FACTURA No. '.$consecutivo,
						'Nit' => $orden->getNit(),
						'CentroCosto' => $orden->getCentroCosto(),
						'Valor' => $valorMonto,
						'TipoDocumento' => 'FAC',
						'NumeroDocumento' => $consecutivo,
						'BaseGrab' => 0,
						'FechaVence' => $fechaMovi,
						'DebCre' => 'C'
					));*/

					$linea++;

					$oldItem = $item;
				}

				//print_r($movis);
				//print_r($valorMontoDebitos);
				//print_r($valorMontoCredito);
				//$transaction->rollback('test->valorMonto:'.$valorMonto.', valorMontoCredito: '.$valorMontoCredito[$item].', valorMontoDebitos: '.$valorMontoDebitos[$item]);

				//add Aura movements
				foreach ($movis as $item => $cuentaObj){
					foreach ($cuentaObj as $cuenta => $configAura) {
						$aura->addMovement($configAura);
					}
				}
				$aura->save();
			}
			catch(AuraException $e){
				$transaction->rollback($e->getMessage().', en el item de servicio '.$orden->getItem());
			}

			$this->Oserv->setTransaction($transaction);
			$this->Oserv->updateAll("nota='D',num_fac='".$aura->getConsecutivo()."'", "comprob='$codigoComprob' AND numero='$numero'");

			$message = 'Se contabilizó la orden de servicio '.$comprob->getNomComprob().'/'.$numero.' generando
			el comprobante '.$comprobContab->getNomComprob().'/'.$aura->getConsecutivo();

			$transaction->commit();

			return array(
				'status' => 'OK',
				'message' => $message
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			);
		}

	}

	public function imprimirAction(){
		$this->setResponse('view');
		$content = '';

		$codigoComprob = $this->getQueryParam('codigoComprob', 'comprob');
		$numero = $this->getQueryParam('numero', 'int');

		$conditions = "comprob='$codigoComprob' AND numero='$numero'";
		if($this->Oserv->count($conditions)==0){
			Flash::error('No existe la orden de servicio');
			return;
		}

		$empresa = $this->Empresa->findFirst();

		$orden = $this->Oserv->findFirst($conditions);

		$tercero = BackCacher::getTercero($orden->getNit());
		if($tercero==false){
			Flash::error('No existe el tercero de la orden de servicio');
			return;
		}

		$terceroEmpresa = BackCacher::getTercero($empresa->getNit());
		if($terceroEmpresa->getEstadoNit()!=''){
			$regimenHotel = $terceroEmpresa->getEstadoNit();
		} else {
			$regimenHotel = 'G';
		}

$content = '


'.$empresa->getNombre().'  NIT No :'.$empresa->getNit().'                      Fecha :'.Date::getCurrentDate('m/d/Y').'
ORDEN DE SERVICIO No. :     '.$numero.'                   FECHA DE LA ORDEN :'.$orden->getFecha()->getUsingFormat('m/d/y').'
COMPROBANTE CONTAB No :     '.$orden->getNumFac().'
Señor(es) :
'.$tercero->getNombre().' ('.$tercero->getNit().')
'.$tercero->getDireccion().'   Tel :'.$tercero->getTelefono().'
'.$tercero->getCiudadNombre().'
Sec Item         D e s c r i p c i o n                     C.C.    Valor Total
';

$linea = 1;
$total = 0;
$totalIva = 0;
$totalIca = 0;
$totalFxP = 0;
$totalRetencion = 0;
$totalRetencionIva = 0;
foreach($this->Oserv->find($conditions) as $oserv){

	$refe = BackCacher::getRefe($oserv->getItem());
	if($refe==false){
		Flash::error('No existe el item de servicio "'.$oserv->getItem().'" en la línea '.$linea);
		return;
	}

	$content.= $linea.' '.sprintf('% 14s', $oserv->getItem()).' '.sprintf('% -40s', $oserv->getDescripcion()).' '.
	$oserv->getCentroCosto().' '.sprintf('% 15s', Currency::money($oserv->getValor())).PHP_EOL;
	$total+=$oserv->getValor();

	$valorMonto = 0;
	$valorIva = 0;
	$retencionIva = 0;

	$lineaser = $refe->getLineaser();
	if($lineaser==false){
		Flash::error('No existe la línea de servicio "'.$refe->getLinea().'" en la línea '.$linea);
		return;
	}

	$cuentaGasto = BackCacher::getCuenta($lineaser->getCtaGasto());
	if($cuentaGasto==false){
		Flash::error('La cuenta de gasto no existe ó no es auxiliar para la línea '.$lineaser->getLinea().' en la línea '.$linea);
		return;
	}
	$valorMonto+=$oserv->getValor();

	if($tercero->getEstadoNit()=='S'){
		$cuenta = $this->Cuentas->findFirst($lineaser->getCtaEx1());
	} else {
		$cuenta = $this->Cuentas->findFirst($lineaser->getCtaIva());
	}
	if($cuenta!=false){
		if($cuenta->getPorcIva()>0){
			$valorIva = $oserv->getValor()*$cuenta->getPorcIva();
			if($tercero->getEstadoNit()=='S'){
				$valorIva*=0.5;
			}
			$valorMonto+=$valorIva;
			$totalIva+=$valorIva;
		} else {
			if($lineaser->getPorcIva()>0){
				$valorIva = $oserv->getValor()*$lineaser->getPorcIva();
				if($tercero->getEstadoNit()=='S'){
					$valorIva*=0.5;
				}
				$totalIva+=$valorIva;
				$valorMonto+=$valorIva;
			}
		}
		if($valorIva>0){
			if(!($tercero->getTipoNit()==1 || ($regimenHotel==$tercero->getEstadoNit()) || ($regimenHotel=='C' && $tercero->getEstadoNit()!='S'))){
				$retencionIva+=$valorIva;
				$valorMonto-=$valorIva;
				$totalRetencionIva+=$valorIva;
			}
		}
	}

	if($tercero->getAutoret()!='S'){
		$cuenta = BackCacher::getCuenta($lineaser->getCtaRetencion());
		if($cuenta!=false){
			if($cuenta->getPorcIva()>0){
				$valorRetencion = $oserv->getValor()*$cuenta->getPorcIva();
				$valorMonto-=$valorRetencion;
				$totalRetencion+=$valorRetencion;
			}
		}
		$ica = $this->Ica->findFirst("codigo='{$tercero->getApAereo()}'");
		if($ica!=false){
			$cuenta = BackCacher::getCuenta($ica->getCuenta());
			$valorIca = $oserv->getValor()*($ica->getCodigo()/1000);
			$valorMonto-=$valorIca;
			$totalIca+=$valorIca;
		}
	}

	$cuenta = BackCacher::getCuenta($lineaser->getCtaCartera());
	if($cuenta==false){
		Flash::error('No existe la cuenta de cuentas por pagar de la linea "'.$lineaser->getDescripcion().'" en la línea '.$linea);
	}
	$totalFxP+=$valorMonto;

	$linea++;
}

for($i=$linea;$i<10;$i++){
	$content.=PHP_EOL;
}

$currency = new Currency();
$content.='-------------------------------------------------------------------------------
          TOTAL ORDEN  :'.sprintf('% 14s', Currency::money($total)).'            RETENCION IVA :'.sprintf('% 14s', Currency::money($totalRetencionIva)).'
          VALOR IVA    :'.sprintf('% 14s', Currency::money($totalIva)).'            VR RETENCION  :'.sprintf('% 14s', Currency::money($totalRetencion)).'
          VR I.C.A.    :'.sprintf('% 14s', Currency::money($totalIca)).'            T O T A L     :'.sprintf('% 14s', Currency::money($totalFxP)).'
===============================================================================
Son : '.$currency->getMoneyAsText($total).'


SC[ ]    MAJ[ ]    UP [ ]
PR[ ]    TRA[ ]    CTE[ ]   FRA[ ]                           Aceptada


-------------------------  -------------------------   -------------------------
  Jefe de Departamento         Vo.Bo. Gerencia             Firma Proveedor
';

header('Content-Type: text/plain');
echo $content;

	}

}
