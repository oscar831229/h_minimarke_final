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
 * Proceso de recalculo de saldosnNiif ejecutado por el cierre contable
 */
class SaldosnNiifProcess
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
     * Recalcula saldosnNiif con todas las cuentas del plan contable
     *
     */
    public function rebuild()
    {
        $transaction = $this->controller->transaction;
        $periodoCierre = $this->controller->periodoCierre;
        $periodoUltimoCierre = $this->controller->periodoUltimoCierre;
        $saldosnNiifModel = $this->controller->SaldosNiif->setTransaction($transaction);

        //clean all records in period
        $saldosnNiifModel->deleteAll("ano_mes='$periodoCierre'");

        $saldosnNiifs = $saldosnNiifModel->find("ano_mes='$periodoUltimoCierre'");
        foreach ($saldosnNiifs as $saldosnNiif) {

            $saldon = new SaldosNiif();
            $saldon->setDepre('N');
            $saldon->setAnoMes($periodoCierre);
            $saldon->setTransaction($transaction);
            $saldon->setNit($saldosnNiif->getNit());
            $saldon->setDebe($saldosnNiif->getDebe());
            $saldon->setHaber($saldosnNiif->getHaber());
            $saldon->setSaldo($saldosnNiif->getSaldo());
            $saldon->setCuenta($saldosnNiif->getCuenta());
            $saldon->setBaseGrab($saldosnNiif->getBaseGrab());

            if ($saldon->save()==false) {

                foreach ($saldon->getMessages() as $message) {
                    $transaction->rollback(
                        'Saldos Niif por Nit: ' . $message->getMessage() .
                        '. ' . $saldon->inspect()
                    );
                }
            }
        }
    }
}
