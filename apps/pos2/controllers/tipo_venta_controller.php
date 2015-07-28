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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class Tipo_VentaController extends ApplicationController {

	public function indexAction(){
		$this->setTemplateAfter("admin_menu");
		$this->loadModel('Salon');
	}

	public function getServiciosAction($id){
		$this->setResponse('view');
		$servicios = array();
		$id = $this->filter($id, 'int');
		foreach($this->SalonTipoVenta->find("salon_id='$id'") as $salonTipoVenta){
			$servicios[] = $salonTipoVenta->getTipoVentaId();
		}
		$this->setParamToView('servicios', $servicios);
		$this->loadModel('TipoVenta');
	}

	public function saveServicioAction($tipoVentaId, $salonId){
		$this->setResponse('view');
		$tipoVentaId = $this->filter($tipoVentaId, 'onechar');
		$salonId = $this->filter($salonId, 'int');
		$salonTipoVenta = $this->SalonTipoVenta->findFirst("salon_id='{$salonId}' AND tipo_venta_id='{$tipoVentaId}'");
		if($salonTipoVenta==false){
			$salonTipoVenta = new SalonTipoVenta();
			$salonTipoVenta->setSalonId($salonId);
			$salonTipoVenta->setTipoVentaId($tipoVentaId);
			$salonTipoVenta->save();
		} else {
			$salonTipoVenta->delete();
		}
	}

}