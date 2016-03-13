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
 * Proceso de recalculo de saldosp ejecutado por el cierre contable
 */
class SaldospProcess
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
     * Recalcula saldosp con todas las cuentas del plan contable
     *
     */
    public function rebuild()
    {
        $transaction   = $this->controller->transaction;
        $periodoCierre = $this->controller->periodoCierre;
        $saldospModel  = $this->controller->Saldosp->setTransaction($transaction);

        $saldosps = $saldospModel->find("ano_mes='$periodoCierre'");
        foreach ($saldosps as $saldosp) {
            $saldosp->setDebe(0);
            $saldosp->setHaber(0);
            $saldosp->setSaldo(0);
            if ($saldosp->save()==false) {
                foreach ($saldosp->getMessages() as $message) {
                    $transaction->rollback($message->getMessage());
                }
            }
        }

        $this->saldosp2();
    }

    /**
     * Segunda iteracion de saldosp
     *
     */
    public function saldosp2()
    {
        $transaction = $this->controller->transaction;
        $periodoCierre = $this->controller->periodoCierre;
        $periodoUltimoCierre = $this->controller->periodoUltimoCierre;

        $saldospModel = $this->controller->Saldosp->setTransaction($transaction);
        $saldosps = $saldospModel->find("ano_mes='$periodoUltimoCierre'");
        foreach ($saldosps as $saldopAnterior) {

            $condition = "cuenta='{$saldopAnterior->getCuenta()}' " .
                "AND centro_costo='{$saldopAnterior->getCentroCosto()}' " .
                "AND ano_mes='$periodoCierre'";

            $saldop = $saldospModel->findFirst($condition);

            if ($saldop==false) {
                $saldop = new Saldosp();
                $saldop->setPres(0);
                $saldop->setAnoMes($periodoCierre);
                $saldop->setCuenta($saldopAnterior->getCuenta());
                $saldop->setCentroCosto($saldopAnterior->getCentroCosto());
            } else {
                $saldop->setPres($saldopAnterior->getPres());
            }

            $saldop->setTransaction($transaction);
            $saldop->setDebe($saldopAnterior->getDebe());
            $saldop->setHaber($saldopAnterior->getHaber());
            $saldop->setSaldo($saldopAnterior->getSaldo());

            if ($saldop->save()==false) {

                foreach ($saldop->getMessages() as $message) {
                    $transaction->rollback('Saldos Presupuesto: ' . $message->getMessage());
                }
            }
        }
    }
}
