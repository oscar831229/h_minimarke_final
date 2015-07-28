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

class Exportar_ConsumosController extends ApplicationController
{

	public $analisis;

	private function itemsDeReceta($numeroReceta, $menuItem, $cantidad, $salonId, $comanda, $fecha, $almacen)
	{
		$recetap = $this->Recetap->findFirst("numero_rec='$numeroReceta'");
		if ($recetap){
			$recetals = $this->Recetal->find("numero_rec='$numeroReceta'");
			if (count($recetals)) {
				foreach ($recetals as $recetal) {
					if ($recetal->tipol == 'I') {

						$acantidad = 0;
						if ($recetal->divisor==0) {
							Flash::error("El divisor en el item ".$recetal->item." de la receta ".$recetap->nombre." es cero");
						} else {
							if ($recetap->num_personas==0) {
								Flash::error("El número de personas de la receta ".$recetap->nombre." es cero");
							} else {
								$acantidad = ($recetal->cantidad / $recetal->divisor / $recetap->num_personas) * $cantidad;
							}
						}

						if(!isset($this->analisis[$salonId][$comanda][$fecha][$almacen][$recetal->item])){
							$this->analisis[$salonId][$comanda][$fecha][$almacen][$recetal->item] = array(
								'venta' => 0
							);
						}
						$this->analisis[$salonId][$comanda][$fecha][$almacen][$recetal->item]['venta']+=$acantidad;
					} else {
						$this->itemsDeReceta($recetal->item, $menuItem, $cantidad, $salonId, $comanda, $fecha, $almacen);
					}
				}
			} else {
				Flash::error("La receta '{$recetap->nombre}' no tiene ingredientes");
			}
		} else {
			Flash::error("La receta '$numeroReceta' del item '{$menuItem->nombre}' no existe");
		}
	}

	public function initialize()
	{
		$this->setTemplateAfter("admin_menu");
	}

	public function indexAction()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isGet()) {
			$datos = $this->Datos->findFirst();
			Tag::displayTo('fechaInicial', (string) $datos->readAttribute('fecha'));
			Tag::displayTo('fechaFinal', (string) $datos->readAttribute('fecha'));
		}
	}

	public function procesarAction()
	{

		$formato = $this->getPostParam('formato', 'alpha');
		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		$this->analisis = array();
		foreach ($this->AccountMaster->find("date(hora) >= '$fechaInicial' AND date(hora) <= '$fechaFinal' AND estado<>'C'") as $accountMaster) {

			foreach ($accountMaster->getAccount("estado<>'C'") as $account) {

				$menuItem = $account->getMenusItems();
				if ($menuItem != false) {

					$fecha = str_replace('-', '', substr($accountMaster->hora, 0, 10));

					$modifier = new Modifiers();
					$salonMenusItem = $this->SalonMenusItems->findFirst("menus_items_id='{$menuItem->id}' AND salon_id = '{$accountMaster->salon_id}'");
					if ($salonMenusItem != false) {
						if ($menuItem->tipo_costo == 'I') {

							if (!isset($this->analisis[$accountMaster->salon_id][$account->comanda])) {
								$this->analisis[$account->comanda] = array();
							}

							if (!isset($this->analisis[$accountMaster->salon_id][$account->comanda])) {
								$this->analisis[$accountMaster->salon_id][$account->comanda] = array();
							}

							if (!isset($this->analisis[$accountMaster->salon_id][$account->comanda][$fecha])) {
								$this->analisis[$accountMaster->salon_id][$account->comanda][$fecha] = array();
							}

							if (!isset($this->analisis[$accountMaster->salon_id][$account->comanda][$fecha][$salonMenusItem->almacen])) {
								$this->analisis[$accountMaster->salon_id][$account->comanda][$fecha][$salonMenusItem->almacen] = array();
							}

							if (!isset($this->analisis[$accountMaster->salon_id][$account->comanda][$fecha][$salonMenusItem->almacen] [$menuItem->codigo_referencia])) {
								$this->analisis[$accountMaster->salon_id][$account->comanda][$fecha][$salonMenusItem->almacen][$menuItem->codigo_referencia] = array(
									'venta' => 0
								);
							}

							if ($menuItem->descontar == 'T') {
								$inve = $this->Inve->findFirst("item='$menuItem->codigo_referencia'");
								if ($inve != false) {
									if ($inve->getVolumen() > 0) {
										$this->analisis[$accountMaster->salon_id][$account->comanda][$fecha][$salonMenusItem->almacen][$menuItem->codigo_referencia]['venta']+=($account->cantidad/$inve->getVolumen());
									} else {
										Flash::error("El número de tragos de la referencia '{$menuItem->codigo_referencia}' no está definido");
									}
								} else {
									Flash::error("La referencia '{$menuItem->codigo_referencia}' no existe");
								}
							} else {
								$this->analisis[$accountMaster->salon_id][$account->comanda][$fecha][$salonMenusItem->almacen][$menuItem->codigo_referencia]['venta']+=$account->cantidad;
							}

						} else {
							if ($menuItem->tipo_costo == 'R') {
								$this->itemsDeReceta($menuItem->codigo_referencia, $menuItem, $account->cantidad, $accountMaster->salon_id, $account->comanda, $fecha, $salonMenusItem->almacen);
							} else {
								if ($menuItem->tipo_costo != 'N') {
									Flash::error("El tipo de costo de '{$menuItem->nombre}' es desconocido");
								}
							}
						}

						/*foreach ($account->getAccountModifiers() as $accountModifier){
							$modifier = $accountModifier->getModifiers();
							if ($modifier!=false){
								if ($menuItem->tipo_costo=='I'){

									if(!isset($this->analisis[$accountMaster->salon_id])){
										$this->analisis[$accountMaster->salon_id] = array();
									}

									if(!isset($this->analisis[$accountMaster->salon_id][$salonMenusItem->almacen])){
										$this->analisis[$accountMaster->salon_id][$salonMenusItem->almacen] = array();
									}

									if(!isset($this->analisis[$accountMaster->salon_id][$salonMenusItem->almacen][$modifier->codigo_referencia])){
										$this->analisis[$accountMaster->salon_id][$salonMenusItem->almacen][$modifier->codigo_referencia] = array(
											'venta' => 0
										);
									}

									if($menuItem->descontar=='T'){
										$inve = $this->Inve->findFirst("item='$modifier->codigo_referencia'");
										if($inve!=false){
											if($inve->getVolumen()>0){
												$this->analisis[$accountMaster->salon_id][$salonMenusItem->almacen][$modifier->codigo_referencia]['venta']+=($account->cantidad/$inve->getVolumen());
											} else {
												Flash::error("El número de tragos de la referencia '{$modifier->codigo_referencia}' no está definido");
											}
										} else {
											Flash::error("La referencia '{$modifier->codigo_referencia}' no existe");
										}
									} else {
										$this->analisis[$accountMaster->salon_id][$salonMenusItem->almacen][$modifier->codigo_referencia]['venta']+=$account->cantidad;
									}

								} else {
									if($menuItem->tipo_costo=='R'){
										$this->itemsDeReceta($modifier->codigo_referencia, $modifier, $account->cantidad, $accountMaster->salon_id, $salonMenusItem->almacen);
									} else {
										if($menuItem->tipo_costo!='N'){
											Flash::error("El tipo de costo del modificador '{$modifier->nombre}' es desconocido");
										}
									}
								}
							}
						}*/

					} else {
						Flash::error("El item '{$modifier->nombre}' se ha vendido pero no existe en el ambiente '{$accountMaster->salon_id}'");
					}
				} else {
					Flash::error("El item de menu '$account->menus_items_id' ya no existe");
				}
			}
		}

		$report = new Report('Excel');

		$report->setPagination(false);

		ReportComponent::load(array('Text', 'Style', 'Format'));

		$numberFormat = new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		));

		$report->setColumnFormat(array(4, 5, 6, 7, 8), $numberFormat);

		$report->start(true);

		$consecutivo = 1;
		ksort($this->analisis);
		foreach ($this->analisis as $ambiente => $datosAmbiente) {

			ksort($datosAmbiente);
			$salon = $this->Salon->findFirst($ambiente);

			foreach ($datosAmbiente as $comanda => $datosComanda) {

				ksort($datosComanda);
				foreach ($datosComanda as $fecha => $datosAlmacen) {

					foreach ($datosAlmacen as $almacen => $datos) {

						foreach ($datos as $item => $cantidad) {

							$inve = $this->Inve->findFirst("item='$item'");
							if ($inve != false){

								if ($inve != false) {
									$report->addRow(array(
										$consecutivo,
										$comanda,
										$fecha,
										$item,
										utf8_encode($inve->getDescripcion()),
										$cantidad['venta'],
										100,
										$almacen,
										$salon->centro_costo
									));
									$consecutivo++;
								}
							} else {
								Flash::error("La referencia '$item' no existe");
							}
						}
					}
				}
			}
		}

		$report->finish();

		$fileName = $report->outputToFile('public/temp/report');

		$this->setParamToView('generated', true);
		$this->setParamToView('fileName', $fileName);

		$this->routeTo("action: index");

	}

	public function interAction()
	{
		new InterfasePOS2();
	}

}
