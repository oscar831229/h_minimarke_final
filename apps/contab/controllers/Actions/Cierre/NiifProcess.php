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

            $saldosNiifModel = $this->controller->SaldosNiif->setTransaction($transaction);

            $saldos = $saldosNiifModel->find("ano_mes<'$periodoCierre' AND ano_mes != 0 AND depre='N'");

            //throw new Exception(count($saldos), 1);
            if (count($saldos)) {

                $a = "";
                foreach ($saldos as $saldo) {
                    //throw new Exception(print_r($saldo, true), 1);

                    $anoMes = $saldo->getAnoMes();
                    if (!$anoMes) {
                        $anoMes = $periodoCierre;
                    }

                    $diff = $periodoCierre - $anoMes;
                    $a .= PHP_EOL . "<br>$periodoCierre - {$saldo->getAnoMes()}: $diff > $porceMesesNiif";

                    if ($diff > $porceMesesNiif) {
                        
                    }
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
