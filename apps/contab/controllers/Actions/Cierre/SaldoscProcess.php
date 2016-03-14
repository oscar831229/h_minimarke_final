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
 * Proceso de recalculo de saldosc ejecutado por el cierre contable
 */
class SaldoscProcess
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
     * Recalcula saldosc con todas las cuentas del plan contable
     *
     */
    public function rebuild()
    {
        $transaction = $this->controller->transaction;
        $fechaCierre = $this->controller->fechaCierre;
        $ultimoCierre = $this->controller->ultimoCierre;
        $periodoCierre = $this->controller->periodoCierre;
        $periodoUltimoCierre = $this->controller->periodoUltimoCierre;

        $saldoscModel = $this->controller->Saldosc->setTransaction($transaction);
        $saldoscModel->deleteAll("ano_mes='$periodoCierre'");

        $conditions = "ano_mes='$periodoUltimoCierre' AND (haber!=0 OR debe!=0 OR saldo!=0)";
        $saldoscObj = $saldoscModel->find($conditions);
        foreach ($saldoscObj as $saldocAnterior) {

            $saldoc = new Saldosc();
            $saldoc->setAnoMes($periodoCierre);
            $saldoc->setTransaction($transaction);
            $saldoc->setDebe($saldocAnterior->getDebe());
            $saldoc->setHaber($saldocAnterior->getHaber());
            $saldoc->setSaldo($saldocAnterior->getSaldo());
            $saldoc->setCuenta($saldocAnterior->getCuenta());

            if ($saldoc->save()==false) {
                foreach ($saldoc->getMessages() as $message) {
                    $transaction->rollback(
                        'Saldos por Cuenta: ' . $message->getMessage() . '. ' .
                        $saldoc->inspect()
                    );
                }
            }
        }
    }
}
