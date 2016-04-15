<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package     Back-Office
 * @copyright   BH-TECK Inc. 2009-2014
 * @version     $Id$
 */

/**
 * Cierre_ContableController
 *
 * Realiza el cierre contable
 *
 */
class Cierre_ContableController extends ApplicationController
{

    public function initialize()
    {
        $controllerRequest = ControllerRequest::getInstance();
        if ($controllerRequest->isAjax()) {
            View::setRenderLevel(View::LEVEL_LAYOUT);
        }
        parent::initialize();
    }

    public function indexAction()
    {
        $empresa = $this->Empresa->findFirst();
        $empresa1 = $this->Empresa1->findFirst();
        $fechaCierre = $empresa->getFCierrec();

        $nuevoCierre = clone $fechaCierre;
        $nuevoCierre->addDays(1);
        $nuevoCierre->toLastDayOfMonth();
        $this->setParamToView('proximoCierre', $nuevoCierre);

        $this->setParamToView('fechaCierre', $fechaCierre);
        $this->setParamToView('anoCierre', $empresa1->getAnoc());

        $this->setParamToView('message', 'Haga click en "Hacer Cierre" para cerrar el periodo contable actual');
    }

    public function cierreAction()
    {
        $this->setResponse('json');

        try
        {

            set_time_limit(0);
            $allMessages = array();

            $transaction = TransactionManager::getUserTransaction();
            $transaction->setRollbackOnAbort(true);


            $empresa = $this->Empresa->setTransaction($transaction)->findFirst(array('for_update' => true));
            $ultimoCierre = $empresa->getFCierrec();

            $ultimoCierre->toLastDayOfMonth();
            $fechaCierre = clone $ultimoCierre;
            $fechaCierre->addDays(1);
            $fechaCierre->toLastDayOfMonth();

            if (Date::isEarlier($fechaCierre, $ultimoCierre)) {
                $transaction->rollback('El periodo ya fue cerrado');
            } else {
                if (Date::isLater($fechaCierre, Date::getCurrentDate())) {
                    $transaction->rollback('Solo se puede hacer el cierre hasta que acabe el mes');
                } else {
                    if (Date::isLater($fechaCierre, $empresa->getFCierrei())&&$empresa->getContabiliza()=='S') {
                        $transaction->rollback('Debe hacer primero el cierre del periodo de inventarios');
                    }
                }
            }
            /*if ($fechaCierre->getYear()>$ultimoCierre->getYear()) {
                $transaction->rollback('Debe hacer el cierre anual primero '.$fechaCierre->getYear().' '.$ultimoCierre->getYear());
            }*/

            $periodoCierre = $fechaCierre->getPeriod();
            $periodoUltimoCierre = $ultimoCierre->getPeriod();

            //throw new Exception("ano_mes='$periodoCierre'");

            $this->Saldosc->setTransaction($transaction)->deleteAll("ano_mes='$periodoCierre'");

            $this->Saldosn->setTransaction($transaction)->deleteAll("ano_mes='$periodoCierre'");

<<<<<<< HEAD
            $saldospObj = $this->Saldosp->setTransaction($transaction)->find("ano_mes='$periodoCierre'");
            foreach ($saldospObj as $saldosp) {
                $saldosp->setDebe(0);
                $saldosp->setHaber(0);
                $saldosp->setSaldo(0);
                if ($saldosp->save()==false) {
                    foreach ($saldosp->getMessages() as $message) {
                        $transaction->rollback($message->getMessage());
                    }
                }
                unset($saldosp);
            }
            unset($saldospObj);

            $this->Saldosc->setTransaction($transaction)->deleteAll("ano_mes='$periodoCierre'");

            $conditions = "ano_mes='$periodoUltimoCierre' AND (haber!=0 OR debe!=0 OR saldo!=0)";
            //throw new Exception($conditions);

            $saldoscObj = $this->Saldosc->setTransaction($transaction)->find($conditions);
            foreach ($saldoscObj as $saldocAnterior) {
                $saldoc = new Saldosc();
                $saldoc->setTransaction($transaction);
                $saldoc->setCuenta($saldocAnterior->getCuenta());
                $saldoc->setAnoMes($periodoCierre);
                $saldoc->setDebe($saldocAnterior->getDebe());
                $saldoc->setHaber($saldocAnterior->getHaber());
                $saldoc->setSaldo($saldocAnterior->getSaldo());
                if ($saldoc->save()==false) {
                    foreach ($saldoc->getMessages() as $message) {
                        $transaction->rollback('Saldos por Cuenta: '.$message->getMessage().'. '.$saldoc->inspect());
                    }
                }
                unset($saldocAnterior, $saldoc);
            }
            unset($conditions,$saldoscObj);

            //SANDOSN
            $conditions = "ano_mes='$periodoUltimoCierre' AND (haber!=0 OR debe!=0 OR saldo!=0 OR base_grab!=0)";
            $saldosnObj = $this->Saldosn->setTransaction($transaction)->find($conditions);
            foreach ($saldosnObj as $saldonAnterior) {
                $saldon = new Saldosn();
                $saldon->setTransaction($transaction);
                $saldon->setCuenta($saldonAnterior->getCuenta());
                $saldon->setNit(trim($saldonAnterior->getNit()));
                $saldon->setAnoMes($periodoCierre);
                $saldon->setDebe($saldonAnterior->getDebe());
                $saldon->setHaber($saldonAnterior->getHaber());
                $saldon->setSaldo($saldonAnterior->getSaldo());
                $saldon->setBaseGrab($saldonAnterior->getBaseGrab());
                if ($saldon->save()==false) {
                    foreach ($saldon->getMessages() as $message) {
                        $transaction->rollback('Saldos por Nit: '.$message->getMessage().'. '.$saldon->inspect());
                    }
                }
                unset($saldon,$saldonAnterior);
            }
            unset($conditions,$saldosnObj);

            //SALDOSN de COMCIER (solo para cerrar enero)
            $anno = substr($periodoUltimoCierre, 0, 4);
            $mes = substr($periodoUltimoCierre, 4, 2);
            if ($mes==12) {
                foreach ($this->Comcier->find("group: cuentaf,nit") as $comcier) {

                    /**
                     * Aqui se corrigo problema de arraste de saldos de cierre anual
                     * a cuentas con nit que al cerrar el ano paso a ese nit
                     * debe tomar el saldo de saldosc del anopasado en diciembre siempre no el del mes anterior
                     * este caso se presento por el nit 17 en la cuenta 135517*** no debia aprecer porque en saldosc en
                     * 201302 estaba con saldo 0. Por favor no cambiar
                     */
                    $conditionsSaldosc = "ano_mes='{$anno}12' AND cuenta='{$comcier->getCuentaf()}'";
                    //throw new Exception($conditionsSaldosc);

                    $saldoscTemp = $this->Saldosc->setTransaction($transaction)->findFirst($conditionsSaldosc);
                    if ($saldoscTemp) {
                        if ($comcier->getCuentaf()=='135518005005') {
                            //throw new Exception($saldoscTemp->getSaldo());
                        }
                        $saldon = new Saldosn();
                        $saldon->setTransaction($transaction);
                        $saldon->setCuenta($comcier->getCuentaf());
                        $saldon->setNit(trim($comcier->getNit()));
                        $saldon->setAnoMes($periodoCierre);
                        $saldon->setDebe($saldoscTemp->getDebe());
                        $saldon->setHaber($saldoscTemp->getHaber());
                        $saldon->setSaldo($saldoscTemp->getSaldo());
                        $saldon->setBaseGrab(0);
                        if ($saldon->save()==false) {
                            foreach ($saldon->getMessages() as $message) {
                                $transaction->rollback('Saldos por Nit: '.$message->getMessage().'. '.$saldon->inspect());
                            }
                        }
                    }
                    unset($saldoscTemp, $comcier);
                }
            }

            $conditions = "ano_mes='$periodoUltimoCierre'";
            $saldospObj = $this->Saldosp->setTransaction($transaction)->find($conditions);
            foreach ($saldospObj as $saldopAnterior) {
                $saldop = $this->Saldosp->findFirst("cuenta='{$saldopAnterior->getCuenta()}' AND centro_costo='{$saldopAnterior->getCentroCosto()}' AND ano_mes='$periodoCierre'");
                if ($saldop==false) {
                    $saldop = new Saldosp();
                    $saldop->setCuenta($saldopAnterior->getCuenta());
                    $saldop->setCentroCosto($saldopAnterior->getCentroCosto());
                    $saldop->setAnoMes($periodoCierre);
                    $saldop->setPres(0);
                } else {
                    $saldop->setPres($saldopAnterior->getPres());
                }
                $saldop->setTransaction($transaction);
                $saldop->setDebe($saldopAnterior->getDebe());
                $saldop->setHaber($saldopAnterior->getHaber());
                $saldop->setSaldo($saldopAnterior->getSaldo());
                if ($saldop->save()==false) {
                    foreach ($saldop->getMessages() as $message) {
                        $transaction->rollback('Saldos Presupuesto: '.$message->getMessage());
                    }
                }
                unset($saldopAnterior,$saldop);
            }
            unset($conditions,$saldospObj);

            $conditions = "ano_mes='$periodoUltimoCierre'";
            $saldoscaObj = $this->Saldosca->setTransaction($transaction)->find($conditions);
            foreach ($saldoscaObj as $saldocaAnterior) {
                $saldoca = new Saldosca();
                $saldoca->setTransaction($transaction);
                $saldoca->setCuenta($saldocaAnterior->getCuenta());
                $saldoca->setNit(trim($saldocaAnterior->getNit()));
                $saldoca->setTipoDoc($saldocaAnterior->getTipoDoc());
                $saldoca->setNumeroDoc($saldocaAnterior->getNumeroDoc());
                $saldoca->setAnoMes($periodoCierre);
                $saldoca->setDebe($saldocaAnterior->getDebe());
                $saldoca->setHaber($saldocaAnterior->getHaber());
                $saldoca->setSaldo($saldocaAnterior->getSaldo());
                if ($saldoca->save()==false) {
                    foreach ($saldoca->getMessages() as $message) {
                        $transaction->rollback('Saldos de Cartera: '.$message->getMessage());
                    }
                }
                unset($saldoca,$saldocaAnterior);
            }
            unset($conditions,$saldoscaObj);

            $conditions = "fecha>'$ultimoCierre' AND fecha<='$fechaCierre'";
            $movis = $this->Movi->setTransaction($transaction)->findForUpdate(array($conditions, 'group' => 'comprob,numero', 'columns' => 'comprob,numero'));
            foreach ($movis as $movi) {
                try
                {
                    $messages = Aura::saveOnPeriod($movi->getComprob(), $movi->getNumero(), $periodoCierre);
                    if (count($messages)) {
                        $allMessages[] = array(
                            'comprob' => $movi->getComprob(),
                            'numero' => $movi->getComprob(),
                            'messages' => $messages
                        );
                    }

                    //Create movi niif
                    $comprob = BackCacher::getComprob($movi->getComprob());
                    if ($comprob && $comprob->getTipoMoviNiif() == 'I') {
                        $auraNiif = new AuraNiif($movi->getComprob(), $movi->getComprob());
                        $auraNiif->setTransaction($transaction);
                        $auraNiif->createMoviNiifByMovi($movi->getComprob(), $movi->getComprob());
                    }

                    unset($messages, $movi);
                }
                catch (AuraException $e) {
                    $transaction->rollback($e->getMessage());
                }
                unset($movi);
            }
            unset($movis);
=======
                //procesa Niif del periodo
            //$niifProcess = new NiifProcess($this);
            //$a = $niifProcess->rebuild();

            $a = array();

            $allMessages = array_merge_recursive($allMessages, $a);
            if (isset($allMessages) && !count($allMessages)) {
>>>>>>> b4b585d95185e2aa4cce50d26854bd21aada6b59

            if (count($allMessages)==0) {
                $empresa->setFCierrec((string)$fechaCierre);
                if ($empresa->save()==false) {
                    foreach ($empresa->getMessages() as $message) {
                        $transaction->rollback('Empresa: '.$message->getMessage());
                    }
                }

                $transaction->commit();

                $nuevoCierre = clone $fechaCierre;
                $nuevoCierre->addDays(1);
                $proximoCierre = Date::getLastDayOfMonth($nuevoCierre->getMonth(), $nuevoCierre->getYear());

                return array(
                    'status' => 'OK',
                    'cierreActual' => $fechaCierre->getLocaleDate('short'),
                    'proximoCierre' => $proximoCierre->getLocaleDate('long')
                );

            } else {
                $transaction->rollback('El movimiento del periodo a cerrar tiene inconsistencias');
            }
        }
        catch (TransactionFailed $e) {

            if (count($allMessages)>0) {

                $reportType = $this->getPostParam('reportType', 'alpha');
                $report = ReportBase::factory($reportType);

                $titulo = new ReportText('INCONSISTENCIAS MOVIMIENTO CIERRE CONTABLE', array(
                    'fontSize' => 16,
                    'fontWeight' => 'bold',
                    'textAlign' => 'center'
                ));

                $titulo2 = new ReportText('Fecha Cierre: '.$fechaCierre, array(
                    'fontSize' => 11,
                    'fontWeight' => 'bold',
                    'textAlign' => 'center'
                ));

                $report->setHeader(array($titulo, $titulo2));
                $report->setDocumentTitle('Incosistencias Movimiento Cierre Contable');
                $report->setColumnHeaders(array(
                    'COMPROBANTE',
                    'NÚMERO',
                    'NOVEDAD'
                ));

                $report->setCellHeaderStyle(new ReportStyle(array(
                    'textAlign' => 'center',
                    'backgroundColor' => '#eaeaea'
                )));

                $report->setColumnStyle(array(1, 2, 3), new ReportStyle(array(
                    'textAlign' => 'left',
                    'fontSize' => 11
                )));

                $report->start(true);

                foreach ($allMessages as $message) {
                    foreach ($message['messages'] as $auraMessage) {
                        $comprob = BackCacher::getComprob($message['comprob']);
                        if ($comprob==false) {
                            $report->addRow(array(
                                $message['comprob'].' / NO EXISTE COMPROBANTE',
                                $message['numero'],
                                $auraMessage
                            ));
                        } else {
                            $report->addRow(array(
                                $message['comprob'].' / '.$comprob->getNomComprob(),
                                $message['numero'],
                                $auraMessage
                            ));
                        }
                        unset($auraMessage);
                    }
                    unset($message);
                }

                $report->finish();
                $fileName = $report->outputToFile('public/temp/cierre-contable');

                return array(
                    'status' => 'FAILED',
                    'message' => $e->getMessage(),
                    'url' => 'temp/'.$fileName
                );

            } else {
                return array(
                    'status' => 'FAILED',
                    'message' => $e->getMessage()
                );
            }
        }
        catch(Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }

    }
}
