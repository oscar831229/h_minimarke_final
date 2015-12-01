<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * AuraUtils
 *
 * Realiza tareas de recontabilizacion
 *
 */
class AuraUtils extends UserComponent
{

	public function recalculaPeriodoActual()
	{
		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->Saldosc->setTransaction($transaction)->deleteAll("ano_mes=0");
			$this->Saldosn->setTransaction($transaction)->deleteAll("ano_mes=0");
			$this->Saldosp->setTransaction($transaction)->deleteAll("ano_mes=0");

			$empresa = $this->Empresa->findFirst();
			$periodoAnterior = $empresa->getFCierrec()->getPeriod();
			$ultimoCierre = (string) $empresa->getFCierrec();

			$saldosc = array();
			$saldosn = array();
			$saldosp = array();
			foreach($this->Movi->find(array("fecha>'$ultimoCierre'", 'order' => 'fecha')) as $movi) {

				$codigoCuenta = $movi->getCuenta();
				$cuenta = BackCacher::getCuenta($codigoCuenta);
				if ($cuenta==false) {
					$transaction->rollback('No existe la cuenta');
				}

				if (!isset($saldosc[$codigoCuenta])) {
					$saldoc = $this->Saldosc->findFirst("ano_mes='$periodoAnterior' AND cuenta='$codigoCuenta'");
					if ($saldoc==false) {
						$saldosc[$codigoCuenta] = array(
							'debe' => 0,
							'haber' => 0
						);
					} else {
						$saldosc[$codigoCuenta] = array(
							'debe' => $saldoc->getDebe(),
							'haber' => $saldoc->getHaber()
						);
					}
				}

				if ($cuenta->getPideNit()=='S') {
					$nit = $movi->getNit();
					if (!isset($saldosn[$codigoCuenta][$nit])) {
						$saldon = $this->Saldosn->findFirst("ano_mes='$periodoAnterior' AND cuenta='$codigoCuenta' AND nit='$nit'");
						if ($saldon==false) {
							$saldosn[$codigoCuenta][$nit] = array(
								'debe' => 0,
								'haber' => 0
							);
						} else {
							$saldosn[$codigoCuenta][$nit] = array(
								'debe' => $saldon->getDebe(),
								'haber' => $saldon->getHaber()
							);
						}
					}
				}

				if ($cuenta->getPideCentro()=='S') {
					$centroCosto = $movi->getCentroCosto();
					if (!isset($saldosp[$codigoCuenta][$centroCosto])) {
						$saldop = $this->Saldosp->findFirst("ano_mes='$periodoAnterior' AND cuenta='$codigoCuenta' AND centro_costo='$centroCosto'");
						if ($saldop==false) {
							$saldosp[$codigoCuenta][$centroCosto] = array(
								'debe' => 0,
								'haber' => 0
							);
						} else {
							$saldosp[$codigoCuenta][$centroCosto] = array(
								'debe' => $saldop->getDebe(),
								'haber' => $saldop->getHaber()
							);
						}
					}
				}

				if ($movi->getDebCre()=='D') {
					$saldosc[$codigoCuenta]['debe']+=$movi->getValor();
				} else {
					$saldosc[$codigoCuenta]['haber']+=$movi->getValor();
				}

				if ($cuenta->getPideNit()=='S') {
					if ($movi->getDebCre()=='D') {
						$saldosn[$codigoCuenta][$nit]['debe']+=$movi->getValor();
					} else {
						$saldosn[$codigoCuenta][$nit]['haber']+=$movi->getValor();
					}
				}

				if ($cuenta->getPideCentro()=='S') {
					if ($movi->getDebCre()=='D') {
						$saldosp[$codigoCuenta][$centroCosto]['debe']+=$movi->getValor();
					} else {
						$saldosp[$codigoCuenta][$centroCosto]['haber']+=$movi->getValor();
					}
				}

			}

			foreach($saldosc as $cuenta => $saldo) {
				$saldoc = new Saldosc();
				$saldoc->setTransaction($transaction);
				$saldoc->setAnoMes(0);
				$saldoc->setCuenta($cuenta);
				$saldoc->setDebe($saldo['debe']);
				$saldoc->setHaber($saldo['haber']);
				$saldoc->setSaldo($saldo['haber']-$saldo['debe']);
				if ($saldoc->save()==false) {
					foreach($saldoc->getMessages() as $message) {
						$transaction->rollback($message->getMessage());
					}
				}
			}

			foreach($saldosn as $cuenta => $saldosNit) {
				foreach($saldosNit as $nit => $saldo) {
					$saldon = new Saldosn();
					$saldon->setTransaction($transaction);
					$saldon->setAnoMes(0);
					$saldon->setNit($nit);
					$saldon->setCuenta($cuenta);
					$saldon->setDebe($saldo['debe']);
					$saldon->setHaber($saldo['haber']);
					$saldon->setSaldo($saldo['haber']-$saldo['debe']);
					if ($saldon->save()==false) {
						foreach($saldon->getMessages() as $message) {
							$transaction->rollback($message->getMessage());
						}
					}
				}
			}

			foreach($saldosp as $cuenta => $saldosCentro) {
				foreach($saldosCentro as $centroCosto => $saldo) {
					$saldop = new Saldosp();
					$saldop->setTransaction($transaction);
					$saldop->setAnoMes(0);
					$saldop->setCentroCosto($centroCosto);
					$saldop->setCuenta($cuenta);
					$saldop->setDebe($saldo['debe']);
					$saldop->setHaber($saldo['haber']);
					$saldop->setSaldo($saldo['haber']-$saldo['debe']);
					if ($saldop->save()==false) {
						foreach($saldop->getMessages() as $message) {
							$transaction->rollback($message->getMessage());
						}
					}
				}
			}

			//reclaculamos Cartera
			$this->recalculaCarteraAll();

			$transaction->commit();
		} catch(Exception $e){
            Flash::error($e->getMessage());
        }
        catch(TransactionFailed $e) {
			Flash::error($e->getMessage());
		}
	}

    /**
     * Recalcula la tabla cartera segun periodo actual
     */
    public function recalculaCartera()
	{
		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->Cartera->setTransaction($transaction)->deleteAll();
			$transaction->getConnection()->setDebug(true);

			$cartera = array();
			echo '<table>';
			foreach($this->Cuentas->find("pide_fact='S'") as $cuenta) {
				$codigoCuenta = $cuenta->getCuenta();
				$tipo = substr($codigoCuenta, 0, 1);
				foreach($this->Movi->find(array("cuenta='$codigoCuenta'", 'order' => 'fecha')) as $movi) {
					if ($movi->getValor()>0) {

						$nit = $movi->getNit();
						$tipoDoc = $movi->getTipoDoc();
						$numeroDoc = $movi->getNumeroDoc();

						echo '<tr>
							<td>', $codigoCuenta, '</td>
							<td>', $nit, '</td>
							<td>', $tipoDoc, '</td>
							<td>', $numeroDoc, '</td>
						</tr>';


						if (!isset($cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc])) {

							$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc] = array(
								'saldo' => 0,
								'centroCosto' => $movi->getCentroCosto(),
								'fEmision' => (string) $movi->getFecha(),
								'fVence' => (string) $movi->getFVence(),
							);
							if ($tipo=='1') {
								if ($movi->getDebCre()=='D') {
									$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['valor'] = $movi->getValor();
								} else {
									$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['valor'] = 0;
								}
							} else {
								if ($movi->getDebCre()=='C') {
									$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['valor'] = $movi->getValor();
								} else {
									$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['valor'] = 0;
								}
							}
						}

						if ($movi->getDebCre()=='D') {
							if ($tipo=='1') {
								if (Date::isLater($cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['fEmision'], $movi->getFecha())) {
									$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['fEmision'] = (string) $movi->getFecha();
								}
							}
						} else {
							if ($tipo=='2') {
								if (Date::isLater($cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['fEmision'], $movi->getFecha())) {
									$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['fEmision'] = (string) $movi->getFecha();
								}
							}
						}

						if ($movi->getDebCre()=='D') {
							$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['saldo']+=$movi->getValor();
						} else {
							$cartera[$codigoCuenta][$nit][$tipoDoc][$numeroDoc]['saldo']-=$movi->getValor();
						}
					}
				}
			}
			echo '</table>';

			foreach($cartera as $cuenta => $cuentaCartera) {
				foreach($cuentaCartera as $nit => $cuentaNit) {
					foreach($cuentaNit as $tipoDoc => $cuentaTipo) {
						foreach($cuentaTipo as $numeroDoc => $doc) {
							$carter = new Cartera();
							$carter->setTransaction($transaction);
							$carter->setCuenta($cuenta);
							$carter->setNit($nit);
							$carter->setTipoDoc($tipoDoc);
							$carter->setNumeroDoc($numeroDoc);
							$carter->setVendedor(1);
							$carter->setCentroCosto($doc['centroCosto']);
							$carter->setFEmision($doc['fEmision']);
							$carter->setValor($doc['valor']);
							$carter->setSaldo($doc['saldo']);
							$carter->setFVence($doc['fVence']);
							if ($carter->save()==false) {
								foreach($carter->getMessages() as $message) {
									$transaction->rollback($message->getMessage().$carter->inspect());
								}
							}
						}
					}
				}
			}

			$transaction->commit();

		}
		catch(TransactionFailed $e) {
			Flash::error($e->getMessage());
		}
	}

	public function corrigeUnaLinea()
	{
		try {

			$transaction = TransactionManager::getUserTransaction();

			$db = DbBase::rawConnect();

			$schema = '';
			$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
			if (isset($config->hfos->back_db)) {
				$schema = $config->hfos->back_db;
			} else {
				$schema ='ramocol';
			}

			$query = "
				select
					m.comprob,m.numero
				from $schema.movi as m
				where
					(
						select count(*) as total
						from $schema.movi
						where comprob=m.comprob and numero=m.numero
					)<2
				order by m.fecha;
			";

			$listQuery = $db->query($query);
			while($listQueryRow = $db->fetchArray($listQuery)) {
				if (isset($listQueryRow[0]) && $listQueryRow[0]) {
					$comprob = $listQueryRow[0];
					$numero = $listQueryRow[1];

					$movi1 = $this->Movi->setTransaction($transaction)->findFirst("comprob='$comprob' AND numero='$numero'");
					if ($movi1) {

						$movi2 = new Movi();
						$movi2->setTransaction($transaction);

						foreach ($movi1->getAttributes() as $atribute) {
							$movi2->writeAttribute($atribute, $movi1->readAttribute($atribute));
						}

						$deb_cre2 = 'D';
						if ($movi1->getDebCre()=='D') {
							$deb_cre2 = 'C';
						}
						$movi2->setDebCre($deb_cre2);

						if ($movi2->save()==false) {
							foreach ($movi2->getMessages() as $message)
							{
								throw new Exception($message->getMessage());
							}
						}
					}
				}
			}

			$transaction->commit();
		}
		catch(Exception $e) {
			Flash::error($e->getMessage());
		}
		#comprob,numero,fecha,cuenta,nit,centro_costo,valor,deb_cre,descripcion,tipo_doc,numero_doc,base_grab,conciliado,f_vence,numfol
	}

    /**
     * Recalcula la tabla saldosn
     */
    public function recalculateSaldosnAll()
	{
        set_time_limit(0);

		try
		{

			$transaction = TransactionManager::getUserTransaction();
			$this->Saldosn->setTransaction($transaction)->deleteAll();
			//$transaction->getConnection()->setDebug(true);

            $empresa = $this->Empresa->findFirst();
            $fechaCierre = new Date($empresa->getFCierrec());
            $ultimoPeriodoCerrado = $fechaCierre->getPeriod();

            /////////////////////////////////////////////////
            /// Generamos periodos a buscar
            ////////////////////////////////////////////////
            $periodoArray = array();
            // AND fecha>='2003-12-01' AND fecha<='2003-12-31'
            $moviFObj = $this->Movi->find(array("1=1", 'columns'=>"fecha", 'order' => "fecha ASC", 'group' => "fecha"));
            foreach ($moviFObj as $movif) {
                $date=new Date($movif->getFecha());

                $periodo = $date->getPeriod();
                if ($ultimoPeriodoCerrado<$periodo) {
                    $periodo = 0;
                }
                $periodoArray[$periodo]= $periodo;
                unset($periodo,$movif);
            }
            unset($moviFObj);

            //$transaction->rollback(print_r($periodoArray,true));

            /////////////////////////////////////////////////
            //// Recolectamos saldos debitos y creditos
            ////////////////////////////////////////////////
			$saldosn = array();
            $saldosnAcumulado = array();
			foreach ($this->Cuentas->find("pide_nit='S' and cuenta='110510001'") as $cuenta)
			{
				$codigoCuenta = $cuenta->getCuenta();

                foreach ($periodoArray as $periodo) {

                    $q = '';
                    $q = "and nit IN('51632064','11313838','39559129','52744109','1067868321')";
                    $nitsObj = $this->Nits->find("conditions: nit!=0 AND nit!='' $q","columns: nit");
                    foreach ($nitsObj as $nitObj) {
                        //$transaction->rollback(print_r($nitObj,true));

                        $nit = $nitObj->getNit();

                        if ($periodo!=0) {
                            $year = substr($periodo, 0, 4);
                            $month = substr($periodo, 4,2);
                            $fechaIni = "$year-$month-01";
                            $date = new Date($fechaIni);
                            $date->toLastDayOfMonth();
                            $fechaFin = $date->getDate();
                        } else {
                            $date = new Date($fechaCierre);
                            $date->addDays(1);
                            $fechaIni = $date->getDate();
                            $date->toLastDayOfMonth();
                            $fechaFin = $date->getDate();
                        }

                        //Buscamos el movi
                        $queryMovi = "cuenta='$codigoCuenta' AND nit='$nit' AND fecha>='$fechaIni' AND fecha<='$fechaFin'";
                        $queryDebug[]=$queryMovi;
                        //$transaction->rollback($queryMovi);
                        $moviObj = $this->Movi->find(array($queryMovi, 'order' => 'fecha', 'columns' => 'valor,deb_cre'));
                        if (count($moviObj)) {
                            foreach ($moviObj as $movi) {
                                if ($movi->getValor()>0) {

                                    if ($ultimoPeriodoCerrado<$periodo) {
                                        $periodo = 0;
                                    }

                                    if (!isset($saldosn[$periodo][$codigoCuenta][$nit])) {

                                        $saldosn[$periodo][$codigoCuenta][$nit] = array(
                                            'D' => 0,
                                            'C' => 0
                                        );
                                    }

                                    $saldosn[$periodo][$codigoCuenta][$nit][$movi->getDebCre()] += $movi->getValor();

                                }
                                unset($movi);
                            }
                        } else {
                            if ($ultimoPeriodoCerrado<$periodo) {
                                $periodo = 0;
                            }

                            //Si no hay existencia paselo al siguiente mes
                            if (!isset($saldosn[$periodo][$codigoCuenta][$nit])) {

                                $saldosn[$periodo][$codigoCuenta][$nit] = array(
                                    'D' => 0,
                                    'C' => 0
                                );
                            }
                        }
                        unset($nit);
                    }
                }
				unset($cuenta);
			}

            $queryDebug = array();
            ////////////////////////////////////////////////////////
            //// Actualizamos en tabla saldosn o creamos
            ////////////////////////////////////////////////////////
            foreach ($saldosn as $periodo => $periodoArray) {

                foreach ($periodoArray as $cuenta => $cuentaArray) {

                    foreach ($cuentaArray as $nit => $nitArray) {


                        $debe = $nitArray['D'];
                        $haber = $nitArray['C'];
                        $saldo = ($debe - $haber);

                        $queryDebug[]="DebeHoy: $debe, HaberHoy: $haber, saldoHoy: $saldo";

                        //saldo anterior
                        $queryAnterior = "cuenta='$cuenta' AND nit='$nit' AND ano_mes<'$periodo' AND ano_mes!=0 ";
                        $saldosnAnterior = $this->Saldosn->setTransaction($transaction)->findFirst($queryAnterior, "order: ano_mes DESC");
                        if ($saldosnAnterior) {
                            $queryDebug[]="ultimoDebe: {$saldosnAnterior->getDebe()}, ultimoHaber: {$saldosnAnterior->getHaber()}, ultimmoSaldo: {$saldosnAnterior->getSaldo()}";
                            //$transaction->rollback(print_r($saldosnAnterior,true));
                            //$saldo += $saldosnAnterior->getSaldo();
                            $debe += $saldosnAnterior->getDebe();
                            $haber += $saldosnAnterior->getHaber();
                        }

                        $saldo = ($debe - $haber);


                        $queryDebug[]="DebeNuevo: $debe, HaberNuevo: $haber, saldoNuevo: $saldo";

                        $query = "cuenta='$cuenta' AND nit='$nit' AND ano_mes='$periodo'";
                        $debug = $query.", debe: $debe, haber: $haber, saldo: $saldo";
                        $queryDebug[]=$debug;
                        //$transaction->rollback($query.", debe: $debe, haber: $haber, saldo: $saldo");

                        $saldosnObj = $this->Saldosn->findFirst($query);
                        if ($saldosnObj==false) {
                            $saldosnObj = new Saldosn();
                        }

                        if ($saldo==0 && $debe==0 && $haber==0) {
                            unset($nitArray,$saldosnObj,$debe,$haber,$saldo,$query);
                            continue;
                        }

                        $saldosnObj->setTransaction($transaction);
                        $saldosnObj->setCuenta($cuenta);
                        $saldosnObj->setNit($nit);
                        $saldosnObj->setAnoMes($periodo);
                        $saldosnObj->setDebe($debe);
                        $saldosnObj->setHaber($haber);
                        $saldosnObj->setSaldo($saldo);

                        if ($saldosnObj->save()==false) {
                            foreach($saldosnObj->getMessages() as $message) {
                                $transaction->rollback($message->getMessage().$saldosnObj->inspect());
                            }
                        }

                        unset($nitArray,$saldosnObj,$debe,$haber,$saldo,$query);
                    }

                    unset($cuentaArray);
                }

                unset($saldo);
            }
            //$transaction->rollback(print_r($saldosn,true));
            //$transaction->rollback(print_r($queryDebug,true));

            $transaction->commit();
			unset($saldosn);
		}
		catch(TransactionFailed $e) {
			Flash::error($e->getMessage());
		}
	}

	/**
	 * Recalcula la tabla cartera por el nit
	 *
	 * @param  string $nit [description]
	 * @return boolean
	 */
    public function recalculateCarteraByNit($nit)
	{
        set_time_limit(0);

		$transaction = TransactionManager::getUserTransaction();
		$carteraNit = $this->Cartera->setTransaction($transaction)->deleteAll("nit='$nit'");

		//$transaction->getConnection()->setDebug(true);

		/**
		 * Obtenemos las cuentas que piden documento
		 */
		$cuentasCartera = array();
		$cuentas = $this->Cuentas->find("pide_fact='S'", "columns: cuenta");
		foreach ($cuentas as $cuenta) {
			$cuentaCode = $cuenta->getCuenta();
			$cuentasCartera[$cuentaCode] = array();
		}

		/**
		 * Buscamos los movimientos de ese nit con esas cuentas y sumamos debitos y creditos
		 */
		$cuentasIn = implode("','", array_keys($cuentasCartera));
		$movis = $this->Movi->setTransaction($transaction)->find("nit='$nit' AND cuenta IN ('$cuentasIn')", "order: fecha ASC");
		foreach ($movis as $movi) {

			$fecha = $movi->getFecha();
			$valor = $movi->getValor();
			$cuenta = $movi->getCuenta();
			$debcre = $movi->getDebCre();
			$tipoDoc = $movi->getTipoDoc();
			$numeroDoc = $movi->getNumeroDoc();
			$centroCosto = $movi->getCentroCosto();

			if ($debcre == 'C') {
				$valor = $valor * -1;
			}

			if (!isset($cuentasCartera[$cuenta][$tipoDoc][$numeroDoc])) {
				$cuentasCartera[$cuenta][$tipoDoc][$numeroDoc] = array(
					"fecha" => $fecha,
					"valor" => $valor,
					"saldo" => $valor,
					"centroCosto" => $centroCosto
				);
			} else {
				$cuentasCartera[$cuenta][$tipoDoc][$numeroDoc]["saldo"] += $valor;
			}
		}

		/**
		 * Recorremos los resultados y los grabamos en la tabla cartera
		 */
		foreach ($cuentasCartera as $cuentaNum => $array1) {

			if (!count($array1)) {
				unset($cuentasCartera[$cuentaNum]);
				continue;
			}

			foreach ($array1 as $tipoDoc => $array2) {

				foreach ($array2 as $numeroDoc => $data) {

					$condition = "nit='$nit' AND cuenta='$cuentaNum' AND tipo_doc='$tipoDoc' AND numero_doc='$numeroDoc'";
					$cartera = $this->Cartera->setTransaction($transaction)->findFirst($condition);

					if (!$cartera) {
						$cartera = new Cartera;
						$cartera->setNit($nit);
						$cartera->setCuenta($cuentaNum);
						$cartera->setTipoDoc($tipoDoc);
						$cartera->setNumeroDoc($numeroDoc);
					}

					$cartera->setTransaction($transaction);

					$cartera->setVendedor("1");
					$cartera->setValor($data["valor"]);
					$cartera->setSaldo($data["saldo"]);
					$cartera->setFEmision((string) $data["fecha"]);
					$cartera->setFVence((string) $data["fecha"]);
					$cartera->setCentroCosto($data["centroCosto"]);

					if (!$cartera->save()) {
						foreach ($cartera->getMessages() as $key => $message) {
							throw new Exception($message->getMessage());
						}
					}
				}
			}
		}

		//$transaction->rollback(print_r($cuentasCartera, true));

        $transaction->commit();
	}
}
