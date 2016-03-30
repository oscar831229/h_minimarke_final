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
 * Proceso de recalculo de niif ejecutado por el cierre contable
 */
class NiifProcess
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
        $allMessages = array();

        $transaction = $this->controller->transaction;
        $fechaCierre = $this->controller->fechaCierre;
        $ultimoCierre = $this->controller->ultimoCierre;
        $periodoCierre = $this->controller->periodoCierre;

        try {
            $porceDepreNiif = Settings::get('porce_depre_niif', 'CO');
            if (!$porceDepreNiif) {
                throw new Exception("Setting: Porcentaje de depreciación niif aun no se ha asignado un valor", 1);
            }

            $porceMesesNiif = Settings::get('porce_meses_niif', 'CO');
            if (!$porceMesesNiif) {
                throw new Exception("Setting: Meses de depreciación niif aun no se ha asignado un valor", 1);
            }

            $saldosNiifModel = $this->controller->CarteraNiif->setTransaction($transaction);
            $descripcion = "Depreciación de cartera niif";
            $saldos = $saldosNiifModel->find("ano_mes<'$periodoCierre' AND ano_mes != 0 AND depre='N'");

            //throw new Exception(count($saldos), 1);
            if (count($saldos)) {

                $a = "";
                $movements = array();
                foreach ($saldos as $saldo) {

                    $anoMes = $saldo->getAnoMes();
                    if (!$anoMes) {
                        $anoMes = $periodoCierre;
                    }

                    $diff = $periodoCierre - $anoMes;
                    $a .= PHP_EOL . "<br>$periodoCierre - {$saldo->getAnoMes()}: $diff > $porceMesesNiif";

                    $saldoVal = $saldo->getSaldo();
                    #if ($diff > $porceMesesNiif) {
                    if ($diff >= 1 && $saldoVal > 0) {
                        $saldoDepre = $saldoVal * $porceDepreNiif / 100;

                        $movements[] = array(
                               'Fecha' => $saldo->getFecha(),
                               'FechaVence' => $saldo->getFVence(),
                               'Cuenta' => $saldo->getCuenta(),
                               'Nit' => $saldo->getNit(),
                               'CentroCosto' => $saldo->getCentroCosto(),
                               'Valor' => $saldoDepre,
                               'Descripcion' => $moviTemp->getDescripcion(),
                               'TipoDocumento' => $moviTemp->getTipoDoc(),
                               'NumeroDocumento' => $moviTemp->getNumeroDoc(),
                               'BaseGrab' => $moviTemp->getBaseGrab(),
                               'Folio' => $moviTemp->getNumfol(),
                               'DebCre' => $moviTemp->getDebCre()
                        );
                       */
                    }
                }

                if (count($movements)) {
                    $aura = new Aura($codigoComprobante, $numeroComprob);
                    foreach ($movements as $movement) {
                        $aura->addMovement($movement);
                    }
                    $aura->save();
                }
                throw new Exception($a, 1);
            }


        } catch(Exception $e) {
            $allMessages[] = array(
                'comprob'  => 'NIIF',
                'numero'   => '000',
                'messages' => array('Depreciacion cartera niif: ' . $e->getMessage())
            );
        }

        return $allMessages;
    }
}
