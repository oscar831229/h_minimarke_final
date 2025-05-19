<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2013
 * @version		$Id$
 */

class Sincronizar_TercerosController extends ApplicationController
{
	
	public function indexAction()
	{
        $this->loadModel('Salon');
		$datos = $this->Datos->findFirst();
		$this->setParamToView('facturas', $this->Factura->find("fecha='{$datos->getFecha()}' AND estado='A'"));
	}

    public function consultarAction($fecha){

        $this->setResponse('json');

        // Instancia de Tercero
        $tercero = new Tercero();

        // Instancia de sincronizarTercero con inyección de dependencia
        $sincronizar = new SincronizarTercero($tercero);

        $data = $sincronizar->getInfoTerceros($fecha);

        return [
            'success' => true,
            'message' => '',
            'data'    => $data
        ];

    }

    public function procesarAction($fecha){
        
        $this->setResponse('json');

        try {

			// Instancia de Tercero
			$tercero = new Tercero();

			// Instancia de sincronizarTercero con inyección de dependencia
			$sincronizar = new SincronizarTercero($tercero);

			$sincronizar->sincronizarDateInvoice($fecha);


		} catch (Exception $e) {
			//echo "Error: " . $e->getMessage();
		}

        return [
            'success' => true,
            'message' => '',
            'data'    => []
        ];


    }

    public function sincronizarAction($fecha){
        
        $this->setResponse('json');

        try {

			// Instancia de Tercero
			$tercero = new Tercero();

			// Instancia de sincronizarTercero con inyección de dependencia
			$sincronizar = new SincronizarTercero($tercero);

			$sincronizar->sincronizarDateInvoice($fecha);


		} catch (Exception $e) {
			//echo "Error: " . $e->getMessage();
		}

        return [
            'success' => true,
            'message' => '',
            'data'    => []
        ];


    }




}
