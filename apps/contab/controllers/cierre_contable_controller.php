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

require_once 'Actions/Cierre/NiifProcess.php';
require_once 'Actions/Cierre/AuraProcess.php';
require_once 'Actions/Cierre/SaldoscProcess.php';
require_once 'Actions/Cierre/SaldospProcess.php';
require_once 'Actions/Cierre/SaldosnProcess.php';
require_once 'Actions/Cierre/SaldoscaProcess.php';
require_once 'Actions/Cierre/SaldosnNiifProcess.php';


/**
 * Cierre_ContableController
 *
 * Realiza el cierre contable
 *
 */
class Cierre_ContableController extends ApplicationController
{

    public $transaction;
    public $fechaCierre;
    public $ultimoCierre;
    public $periodoCierre;
    public $periodoUltimoCierre;

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

        $this->setParamToView('fechaCierre', $fechaCierre);
        $this->setParamToView('proximoCierre', $nuevoCierre);
        $this->setParamToView('anoCierre', $empresa1->getAnoc());

        $this->setParamToView('message', 'Haga click en "Hacer Cierre" para cerrar el periodo contable actual');
    }

    /**
     * Cierre de periodo
     */
    public function cierreAction()
    {
        set_time_limit(0);

        $this->setResponse('json');

        $allMessages = array();

        try {

            $transaction = TransactionManager::getUserTransaction();
            $transaction->setRollbackOnAbort(true);
            $this->transaction = $transaction;

            $empresa = $this->Empresa->setTransaction($transaction)->findFirst(array('for_update' => true));
            $ultimoCierre = $empresa->getFCierrec();

            $ultimoCierre->toLastDayOfMonth();
            $fechaCierre = clone $ultimoCierre;
            $fechaCierre->addDays(1);
            $fechaCierre->toLastDayOfMonth();

            $periodoCierre = $fechaCierre->getPeriod();
            $periodoUltimoCierre = $ultimoCierre->getPeriod();

            $this->fechaCierre = $fechaCierre;
            $this->ultimoCierre = $ultimoCierre;
            $this->periodoCierre = $periodoCierre;
            $this->periodoUltimoCierre = $periodoUltimoCierre;

            if (Date::isEarlier($fechaCierre, $ultimoCierre)) {
                $transaction->rollback('El periodo ya fue cerrado');
            } else {
                if (Date::isLater($fechaCierre, Date::getCurrentDate())) {
                    $transaction->rollback('Solo se puede hacer el cierre hasta que acabe el mes');
                } else {
                    if (Date::isLater($fechaCierre, $empresa->getFCierrei()) && $empresa->getContabiliza()=='S') {
                        $transaction->rollback('Debe hacer primero el cierre del periodo de inventarios');
                    }
                }
            }

            if (intval($fechaCierre->getYear()) < intval($ultimoCierre->getYear())) {
                $transaction->rollback(
                    'Debe hacer el cierre anual primero.' .
                    ' FechaCierre: ' . $fechaCierre->getYear() .
                    ', ultimoCierre:' . $ultimoCierre->getYear()
                );
            }

            //Calcular Saldosp del periodo
            $saldospProcess = new SaldospProcess($this);
            $saldospProcess->rebuild();

            //Calcular Saldosc del periodo
            $saldoscProcess = new SaldoscProcess($this);
            $saldoscProcess->rebuild();

            //Calcular Saldosn del periodo
            $saldosnProcess = new SaldosnProcess($this);
            $saldosnProcess->rebuild();

            //Calcular SaldosnNiif del periodo
            $saldosnNiifProcess = new SaldosnNiifProcess($this);
            $saldosnNiifProcess->rebuild();

            //Calcular Saldosca del periodo
            $saldosncaProcess = new SaldoscaProcess($this);
            $saldosncaProcess->rebuild();

            //procesa Aura del periodo
            $auraProcess = new AuraProcess($this);
            $allMessages = $auraProcess->rebuild();

            //procesa Niif del periodo
            $niifProcess = new NiifProcess($this);
            $a = $niifProcess->rebuild();
            //$a = array();

            $allMessages = array_merge_recursive($allMessages, $a);
            if (isset($allMessages) && !count($allMessages)) {

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

        } catch (TransactionFailed $e) {

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
        }   catch(Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage() . $e->getTraceAsString()
            );
        }

    }
}
