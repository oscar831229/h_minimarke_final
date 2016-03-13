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

        $cuentas = $this->controller->Cuentas->find("group: cuenta");
        $conditionBase = "fecha>'$ultimoCierre' AND fecha<='$fechaCierre'";

        $moviModel = $this->controller->Movi->setTransaction($transaction);
        $saldoscModel  = $this->controller->Saldosc->setTransaction($transaction);

        //clean all records in period
        $saldoscModel->deleteAll("ano_mes='$periodoCierre'");

        foreach ($cuentas as $cuenta) {

            $codigoCuenta = $cuenta->getCuenta();

            $condition  = $conditionBase . " AND cuenta LIKE '$codigoCuenta%'";
            $conditionD = $condition . " AND deb_cre = 'D'";
            $conditionC = $condition . " AND deb_cre = 'C'";

            $saldosc = $saldoscModel->findFirst("ano_mes='$periodoCierre' AND cuenta='$codigoCuenta'");

            if (!$saldosc) {
                $saldosc = new Saldosc();
                $saldosc->setCuenta($codigoCuenta);
                $saldosc->setAnoMes($periodoCierre);
                $saldosc->setTransaction($transaction);
            }

            $saldoAnt = $this->getSaldocAnterior($codigoCuenta, $saldoscModel);

            $debe  = $moviModel->sum("valor", "conditions: $conditionD");
            $haber = $moviModel->sum("valor", "conditions: $conditionC");

            $saldo = $debe - $haber;
            $neto  = $saldoAnt + $saldo;

            $saldosc->setNeto($neto);
            $saldosc->setDebe($debe);
            $saldosc->setHaber($haber);
            $saldosc->setSaldo($saldo);

            if (!$saldosc->save()) {
                foreach ($saldosc->getMessages() as $message) {
                    $transaction->rollback('Saldos por Cuenta: ' . $message->getMessage());
                }
            }
        }
    }

    /**
     * retorna le saldo anterior de debitos y creditos
     *
     * @param  string $codigoCuenta
     * @return decimal
     */
    private function getSaldocAnterior($codigoCuenta, $saldoscModel)
    {
        $ultimoCierre = $this->controller->ultimoCierre;

        //Trata de obtener el saldosc del mes anterior
        $fecha = new Date($ultimoCierre);
        $saldosc = $saldoscModel->findFirst(
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
