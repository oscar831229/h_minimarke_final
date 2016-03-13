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
 * Proceso de recalculo de saldosca ejecutado por el cierre contable
 */
class SaldoscaProcess
{
    /**
     * @var Controller
     */
    private $controller;

    /**
     * Contructor de la clase
     *
     * @param ControllerBase $controller
     * @param Transaction $transaction
     */
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Recalcula saldosca con todas las cuentas del plan contable
     *
     */
    public function rebuild()
    {
        $transaction = $this->controller->transaction;
        $periodoCierre = $this->controller->periodoCierre;
        $periodoUltimoCierre = $this->controller->periodoUltimoCierre;

        $saldoscaModel = $this->controller->Saldosca->setTransaction($transaction);
        $saldoscas = $saldoscaModel->find("ano_mes='$periodoUltimoCierre'");
        foreach ($saldoscas as $saldocaAnterior) {

            $saldoca = new Saldosca();
            $saldoca->setAnoMes($periodoCierre);
            $saldoca->setTransaction($transaction);
            $saldoca->setNit($saldocaAnterior->getNit());
            $saldoca->setDebe($saldocaAnterior->getDebe());
            $saldoca->setHaber($saldocaAnterior->getHaber());
            $saldoca->setSaldo($saldocaAnterior->getSaldo());
            $saldoca->setCuenta($saldocaAnterior->getCuenta());
            $saldoca->setTipoDoc($saldocaAnterior->getTipoDoc());
            $saldoca->setNumeroDoc($saldocaAnterior->getNumeroDoc());

            if ($saldoca->save()==false) {
                foreach ($saldoca->getMessages() as $message) {
                    $transaction->rollback('Saldos de Cartera: '.$message->getMessage());
                }
            }
        }
    }
}
