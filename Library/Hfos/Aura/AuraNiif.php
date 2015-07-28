<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author      BH-TECK Inc. 2009-2015
 * @version     $Id$
 */

/**
 * Aura para Niif
 *
 * Realiza las contabilizaciones niif
 */
class AuraNiif extends UserComponent
{
    private $debug;
    private $_transaction;

    /**
     * Constructor de AuraNiif
     *
     * @param string    $codigoComprobante
     * @param int       $numero
     * @param boolean   $debug
     */
    public function __construct($comprob, $numero, $debug = true)
    {
        $this->_transaction = TransactionManager::getUserTransaction();
        $this->Movi->setTransaction($this->_transaction);
        $this->MoviNiif->setTransaction($this->_transaction);
        $this->debug = $debug;

        if ($comprob && $numero) {

            $comprobObj = BackCacher::getComprob($comprob);

            //Si es un compribante que genera NIIF hacer replica
            if ($comprobObj && $comprobObj->getTipoMoviNiif() == 'I') {
                return $this->createMoviNiifByMovi($comprob, $numero);
            }

        }
    }

    /**
     * Create movi niff by Movi
     *
     * @param  string   $comprob
     * @param  int      $numero
     */
    public function createMoviNiifByMovi($comprob, $numero)
    {
        $movis = $this->Movi->find(array(
            "conditions" => "comprob = '$comprob' AND numero= '$numero'"
        ));

        if ($movis && count($movis)) {

            foreach ($movis as $movi) {

                $cuentaMovi = $movi->readAttribute('cuenta');
                $cuenta = EntityManager::get("Cuentas")->findFirst($cuentaMovi);

                $cuentaNiif = $cuenta->getCuentaNiif();
                if (!$cuentaNiif && $this->debug == true) {
                    throw new AuraException("La cuenta '$cuentaMovi' no tiene parametrizada la cuenta NIIF");
                }

                if ($cuenta) {

                    $moviNiif = new MoviNiif();
                    $moviNiif->setTransaction($this->_transaction);

                    foreach ($movi->getAttributes() as $field) {
                        $moviNiif->writeAttribute($field, $movi->readAttribute($field));
                    }

                    //Copiamos cuenta de movi a columna cuenta_movi
                    $moviNiif->writeAttribute('cuenta_movi', $cuentaMovi);

                    //Cambiamos a cuenta niif en column cuenta
                    $moviNiif->writeAttribute('cuenta', $cuentaNiif);

                    if (!$moviNiif->save()) {

                        foreach ($moviNiif->getMessages() as $message) {
                            throw new AuraException($message->getMessage());
                        }

                    }

                }

                unset($movi);
            }

            unset($movis);
        } else {
            throw new AuraException("Movimiento '$comprob-$numero' no existe");
        }

    }
}
