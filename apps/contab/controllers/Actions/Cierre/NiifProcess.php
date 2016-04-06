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
            $comprobDepreNiif = Settings::get('comprob_depre_niif', 'CO');
            if (!$comprobDepreNiif) {
                throw new Exception("Setting: Comprobante de depreciación niif aun no se ha asignado un valor", 1);
            }


            $cuentaDepreNiif = Settings::get('cuenta_depre_niif', 'CO');
            if (!$cuentaDepreNiif) {
                throw new Exception("Setting: Cuenta de cruce de depreciación niif aun no se ha asignado un valor", 1);
            }

            $porceDepreNiif = Settings::get('porce_depre_niif', 'CO');
            if (!$porceDepreNiif) {
                throw new Exception("Setting: Porcentaje de depreciación niif aun no se ha asignado un valor", 1);
            }

            $porceMesesNiif = Settings::get('porce_meses_niif', 'CO');
            if (!$porceMesesNiif) {
                throw new Exception("Setting: Meses de depreciación niif aun no se ha asignado un valor", 1);
            }

            $carteraNiifModel = $this->controller->CarteraNiif->setTransaction($transaction);
            $descripcion = "Depreciación de cartera niif";
            $carteras = $carteraNiifModel->find("f_emision<='$fechaCierre' AND depre='N'");

            throw new Exception("f_emision<='$fechaCierre' AND depre='N' - " . count($carteras), 1);

            if (count($carteras)) {

                $total = 0;
                $movements = array();
                $decription = "Depreciación de cartera NIIF del $porceDepreNiif% cada $porceMesesNiif meses";
                foreach ($carteras as $cartera) {

                    $fechaEmision = new Date($cartera->getFEmision());

                    $anoMes = $fechaEmision->getPeriod();
                    if (!$anoMes) {
                        $anoMes = $periodoCierre;
                    }

                    $diff = $periodoCierre - $anoMes;
                    $saldoVal = $cartera->getSaldo();
                    #if ($diff > $porceMesesNiif) {
                    if ($diff >= 1 && $saldoVal > 0) {

                        $saldoDepre = $saldoVal * $porceDepreNiif / 100;

                        $movements[] = array(
                            'Folio' => '',
                            'DebCre' => 'C',
                            'BaseGrab' => 0,
                            'Valor' => $saldoDepre,
                            'Nit' => $cartera->getNit(),
                            'Descripcion' => $decription,
                            'Cuenta' => $cartera->getCuenta(),
                            'Fecha' => $cartera->getFEmision(),
                            'FechaVence' => $cartera->getFVence(),
                            'TipoDocumento' => $cartera->getTipoDoc(),
                            'CentroCosto' => $cartera->getCentroCosto(),
                            'NumeroDocumento' => $cartera->getNumeroDoc()
                        );

                        $total += $saldoDepre;
                    }
                }

                if (count($movements)) {

                    $movements[] = array(
                        'Folio' => '',
                        'DebCre' => 'D',
                        'BaseGrab' => 0,
                        'Valor' => $total,
                        'Nit' => '',
                        'Descripcion' => $decription,
                        'Cuenta' => $cuentaDepreNiif,
                        'Fecha' => $cartera->getFEmision(),
                        'FechaVence' => $cartera->getFVence(),
                        'TipoDocumento' => $cartera->getTipoDoc(),
                        'CentroCosto' => $cartera->getCentroCosto(),
                        'NumeroDocumento' => $cartera->getNumeroDoc()
                    );
                    
                    $fechaCierreDate = new Date($fechaCierre);
                    $periodo = $fechaCierreDate->getPeriod();

                    $depreNiif = $this->DepreNiif->setTransaction($transaction)->findFirst("periodo='$periodo'");
                    if (!$depreNiif) {

                        $numero = null;
                        $comprob = $comprobDepreNiif;

                        $depreNiif = new DepreNiif();
                        $depreNiif->setPeriodo($periodo);
                    } else {
                        $comprob = $depreNiif->getComprob();
                        $numero  = $depreNiif->getNumero();
                    }

                    //save aura movi niif
                    $auraNiif = new AuraNiif($comprob, $numero, $fechaCierre);
                    $auraNiif->setTransaction($transaction);

                    foreach ($movements as $movement) {
                        $auraNiif->addMovement($movement);
                    }

                    $auraNiif->save();


                    $depreNiif->setComprob($comprobDepreNiif);
                    $depreNiif->setNumero($auraNiif->getDefaultNumero());

                    $identity = IdentityManager::getActive();
                    $depreNiif->setUsuarioId($identity["id"]);

                    $depreNiif->save();
                }
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
