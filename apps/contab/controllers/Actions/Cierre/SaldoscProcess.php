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
     * @var Transaction
     */
    private $transaction;

    /**
     * Contructor de la clase
     *
     * @param ControllerBase $controller
     * @param Transaction $transaction
     */
    public function __construct($controller, $transaction)
    {
        $this->controller = $controller;
        $this->transaction = $transaction;
    }

    /**
     * Recalcula saldosc con todas las cuentas del plan contable
     *
     */
    public function rebuildSaldosc()
    {
        $cuentas = $this->controller->Cuentas->find("group: cuenta");
        $conditionBase = "fecha>'{$this->controller->ultimoCierre}' AND fecha<='{$this->controller->fechaCierre}'";
        foreach ($cuentas as $cuenta) {

            $codigoCuenta = $cuenta->getCuenta();
            $condition = $conditionBase . " AND cuenta LIKE '$codigoCuenta%'";
            $conditionD = $condition . " AND deb_cre = 'D'";
            $conditionC = $condition . " AND deb_cre = 'C'";

            $saldosc = $this->controller->Saldosc
                ->setTransaction($this->controller->transaction)
                ->findFirst("ano_mes='{$this->controller->periodoCierre}' AND cuenta='$codigoCuenta'");

            if (!$saldosc) {
                $saldosc = new Saldosc();
                $saldosc->setCuenta($codigoCuenta);
                $saldosc->setAnoMes($this->controller->periodoCierre);
                $saldosc->setTransaction($this->transaction);
            }

            $saldoAnt = $this->getSaldocAnterior($codigoCuenta);
            $moviModel = $this->controller->Movi->setTransaction($this->transaction);
            $debe  = $moviModel->sum("valor", "conditions: $conditionD");
            $haber = $moviModel->sum("valor", "conditions: $conditionC");
            $neto  = $debe - $haber;
            $saldo = $saldoAnt + $neto;

            if ($codigoCuenta == '110505001') {
                //throw new Exception("saldoAnt: $saldoAnt, debe: $debe, haber: $haber, neto: $neto, saldo: $saldo, conditionD: $conditionD", 1);
            }

            $saldosc->setDebe($debe);
            $saldosc->setHaber($haber);
            $saldosc->setSaldo($saldo);
            $saldosc->setNeto($neto);

            if (!$saldosc->save()) {
                foreach ($saldosc->getMessages() as $message) {
                    $this->transaction->rollback('Saldos por Cuenta: ' . $message->getMessage());
                }
            }

            if ($codigoCuenta == '110505001') {
                //throw new Exception(print_r($saldosc, true));
            }
        }
    }

    /**
     * retorna le saldo anterior de debitos y creditos
     *
     * @param  string $codigoCuenta
     * @return decimal
     */
    private function getSaldocAnterior($codigoCuenta)
    {
        $ultimoCierre = $this->controller->ultimoCierre;

        //Trata de obtener el saldosc del mes anterior
        $fecha = new Date($ultimoCierre);
        $saldosc = $this->controller->Saldosc->findFirst(
            "cuenta='$codigoCuenta' AND ano_mes<='{$fecha->getPeriod()}'",
            "order: ano_mes DESC"
        );
        if ($saldosc) {
            return $saldosc->getSaldo();
        }

        return 0;

        //Retorna el valor debitos y credito de todo el movimineto si no existe
        //un saldosc en el mes anterior
        /*$condition  = "fecha<='$ultimoCierre' AND cuenta LIKE '$codigoCuenta%'";
        $conditionD = $condition . " AND deb_cre = 'D'";
        $conditionC = $condition . " AND deb_cre = 'C'";

        $debe  = $this->controller->Movi->sum("valor", "conditions: $conditionD");
        $haber = $this->controller->Movi->sum("valor", "conditions: $conditionC");

        return ($debe - $haber);*/
    }
}
