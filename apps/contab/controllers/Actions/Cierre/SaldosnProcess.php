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
 * Proceso de recalculo de saldosn ejecutado por el cierre contable
 */
class SaldosnProcess
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
     * Recalcula saldosn con todas las cuentas del plan contable
     *
     */
    public function rebuild()
    {
        $transaction = $this->controller->transaction;
        $periodoCierre = $this->controller->periodoCierre;
        $periodoUltimoCierre = $this->controller->periodoUltimoCierre;
        $saldosnModel = $this->controller->Saldosn->setTransaction($transaction);

        //clean all records in period
        $saldosnModel->deleteAll("ano_mes='$periodoCierre'");

        $saldosns = $saldosnModel->find("ano_mes='$periodoUltimoCierre'");
        foreach ($saldosns as $saldonAnterior) {

            $saldon = new Saldosn();
            $saldon->setAnoMes($periodoCierre);
            $saldon->setTransaction($transaction);
            $saldon->setNit($saldonAnterior->getNit());
            $saldon->setDebe($saldonAnterior->getDebe());
            $saldon->setHaber($saldonAnterior->getHaber());
            $saldon->setSaldo($saldonAnterior->getSaldo());
            $saldon->setCuenta($saldonAnterior->getCuenta());
            $saldon->setBaseGrab($saldonAnterior->getBaseGrab());

            if ($saldon->save()==false) {

                foreach ($saldon->getMessages() as $message) {
                    $transaction->rollback(
                        'Saldos por Nit: ' . $message->getMessage() . '. ' .
                        $saldon->inspect()
                    );
                }
            }
        }

        $this->comcier();
    }

    /**
     * SALDOSN de COMCIER (solo para cerrar enero)
     *
     */
    public function comcier()
    {
        $periodoUltimoCierre = $this->controller->periodoUltimoCierre;

        $year = substr($periodoUltimoCierre, 0, 4);
        $mes  = substr($periodoUltimoCierre, 4, 2);

        if (intval($mes) == 12) {

            $transaction = $this->controller->transaction;
            $periodoCierre = $this->controller->periodoCierre;
            $comcierModel = $this->controller->Comcier->setTransaction($transaction);
            $saldoscModel = $this->controller->Saldosc->setTransaction($transaction);

            $comciers = $comcierModel->find("group: cuentaf,nit");
            foreach ($comciers as $comcier) {

                /**
                 * Aqui se corrigo problema de arraste de saldos de cierre anual
                 * a cuentas con nit que al cerrar el ano paso a ese nit
                 * debe tomar el saldo de saldosc del anopasado en diciembre siempre no el del mes anterior
                 * este caso se presento por el nit 17 en la cuenta 135517*** no debia aprecer porque en saldosc en
                 * 201302 estaba con saldo 0. Por favor no cambiar
                */
                $conditionsSaldosc = "ano_mes='{$year}12' AND cuenta='{$comcier->getCuentaf()}'";

                $saldoscTemp = $saldoscModel->findFirst($conditionsSaldosc);
                if ($saldoscTemp) {

                    $saldon = new Saldosn();
                    $saldon->setBaseGrab(0);
                    $saldon->setAnoMes($periodoCierre);
                    $saldon->setNit($comcier->getNit());
                    $saldon->setTransaction($transaction);
                    $saldon->setDebe($saldoscTemp->getDebe());
                    $saldon->setCuenta($comcier->getCuentaf());
                    $saldon->setHaber($saldoscTemp->getHaber());
                    $saldon->setSaldo($saldoscTemp->getSaldo());

                    if ($saldon->save()==false) {
                        foreach ($saldon->getMessages() as $message) {
                            $transaction->rollback('Saldos Comcier por Nit: '.$message->getMessage().'. '.$saldon->inspect());
                        }
                    }
                }
            }
        }
    }

}
