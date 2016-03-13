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
 * Proceso de recalculo de aura ejecutado por el cierre contable
 */
class AuraProcess
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

        $moviModel = $this->controller->Movi->setTransaction($transaction);

        $movis = $moviModel->findForUpdate(
            array(
                "fecha>'$ultimoCierre' AND fecha<='$fechaCierre'",
                'group'   => 'comprob,numero',
                'columns' => 'comprob,numero'
            )
        );

        foreach ($movis as $movi) {
            try {

                $messages = Aura::saveOnPeriod(
                    $movi->getComprob(),
                    $movi->getNumero(),
                    $periodoCierre
                );

                if (count($messages)) {
                    $allMessages[] = array(
                        'comprob'  => $movi->getComprob(),
                        'numero'   => $movi->getNumero(),
                        'messages' => $messages
                    );
                }

                return $allMessages;

            } catch (AuraException $e) {
                $transaction->rollback($e->getMessage());
            }
        }

        return array();
    }
}
