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

class Analisis_DetalladoController extends ApplicationController
{

	public $total;

	private function itemsDeReceta($codigoItem, $factura, $account, $numeroReceta, $menuItem, $cantidad, $salonId, $almacen)
	{
		$recetap = $this->Recetap->findFirst("numero_rec='$numeroReceta'");
		if ($recetap) {
			$recetals = $this->Recetal->find("numero_rec='$numeroReceta'");
			if (count($recetals)) {
				foreach ($recetals as $recetal) {
					if ($recetal->tipol == 'I') {
						$acantidad = 0;
						if($recetal->divisor==0){
							Flash::error("El divisor en el item ".$recetal->item." de la receta ".$recetap->nombre." es cero");
						} else {
							if($recetap->num_personas==0){
								Flash::error("El número de personas de la receta ".$recetap->nombre." es cero");
							} else {
								$acantidad = ($recetal->cantidad/$recetal->divisor/$recetap->num_personas)*$cantidad;
							}
						}
						if($recetal->item==$codigoItem){
							$salon = $this->Salon->findFirst($salonId);
							echo '<tr>
								<td>', $salon->nombre, '</td>
								<td align="right">', $almacen, '</td>
								<td align="right">', $factura->prefijo_facturacion.'-'.$factura->consecutivo_facturacion, '</td>
								<td align="right">', $factura->fecha, '</td>
								<td>', 'RECETA', '</td>
								<td>', utf8_encode($recetap->nombre), '</td>
								<td>', $menuItem->nombre, '</td>
								<td align="right">', $menuItem->codigo_referencia, '</td>
								<td align="right">', $account->cantidad, '</td>
								<td align="right">', $acantidad, '</td>
							</tr>';
							$this->total+=$acantidad;
						}
					} else {
						$this->itemsDeReceta($codigoItem, $factura, $account, $recetal->item, $menuItem, $cantidad, $salonId, $almacen);
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
			Tag::displayTo('fechaInicial', (string)$datos->readAttribute('fecha'));
			Tag::displayTo('fechaFinal', (string)$datos->readAttribute('fecha'));
		}
	}

	public function procesarAction()
	{

		$codigoItem = $this->getPostParam('item', 'alpha');
		if ($codigoItem == '') {
			Flash::error('Ingrese la referencia a analizar');
			$this->routeTo("action: index");
			return;
		}

		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		$this->total = 0;
		echo '<br/><table class="lista_res" cellspacing="0" align="center">
		<tr>
			<th valign="bottom">Ambiente</th>
			<th valign="bottom">Almacen</th>
			<th valign="bottom">Factura/Orden</th>
			<th valign="bottom">Fecha</th>
			<th valign="bottom">Tipo</th>
			<th valign="bottom">Detalle</th>
			<th valign="bottom">Item de Menú</th>
			<th valign="bottom">Referencia/Receta en el Item</th>
			<th valign="bottom">Cantidad Vendida Item</th>
			<th valign="bottom">Cantidad a Descargar</th>
		</tr>';
		foreach ($this->AccountMaster->find("date(hora)>='$fechaInicial' AND date(hora)<='$fechaFinal' AND estado<>'C'") as $accountMaster) {
			foreach ($accountMaster->getAccount("estado = 'L'") as $account) {
				$factura = $this->Factura->findFirst("account_master_id={$accountMaster->id} AND cuenta='{$account->cuenta}'");
				if ($factura == false) {
					Flash::error($accountMaster->id);
					$factura = new Factura();
				}
				$menuItem = $account->getMenusItems();
				if($menuItem!=false){
					$salonMenusItem = $this->SalonMenusItems->findFirst("menus_items_id='{$menuItem->id}' AND salon_id = '{$accountMaster->salon_id}'");
					if($salonMenusItem!=false){
						if($menuItem->tipo_costo=='I'){
							if($menuItem->descontar=='T'){
								$inve = $this->Inve->findFirst("item='$menuItem->codigo_referencia'");
								if($inve!=false){
									if($inve->getVolumen()>0){
										if($menuItem->codigo_referencia==$codigoItem){
											$salon = $this->Salon->findFirst($accountMaster->salon_id);
											echo '<tr>
												<td>', $salon->nombre, '</td>
												<td align="right">', $salonMenusItem->almacen, '</td>
												<td align="right">', $factura->prefijo_facturacion.'-'.$factura->consecutivo_facturacion, '</td>
												<td align="right">', $factura->fecha, '</td>
												<td>', 'REFERENCIA/TRAGOS', '</td>
												<td>', $inve->getVolumen(), '</td>
												<td>', $menuItem->nombre, '</td>
												<td align="right">', $menuItem->codigo_referencia, '</td>
												<td align="right">', $account->cantidad, '</td>
												<td align="right">', $account->cantidad, '</td>
											</tr>';
											$this->total+=$account->cantidad;
										}
									} else {
										Flash::error("El número de tragos de la referencia '{$menuItem->codigo_referencia}' no está definido");
									}
								} else {
									Flash::error("La referencia '{$menuItem->codigo_referencia}' no existe");
								}
							} else {
								if($menuItem->codigo_referencia==$codigoItem){
									$salon = $this->Salon->findFirst($accountMaster->salon_id);
									echo '<tr>
										<td>', $salon->nombre, '</td>
										<td align="right">', $salonMenusItem->almacen, '</td>
										<td align="right">', $factura->prefijo_facturacion.'-'.$factura->consecutivo_facturacion, '</td>
										<td align="right">', $factura->fecha, '</td>
										<td>', 'REFERENCIA', '</td>
										<td></td>
										<td>', $menuItem->nombre, '</td>
										<td align="right">', $menuItem->codigo_referencia, '</td>
										<td align="right">', $account->cantidad, '</td>
										<td align="right">', $account->cantidad, '</td>
									</tr>';
									$this->total+=$account->cantidad;
								}
							}
						} else {
							if($menuItem->tipo_costo=='R'){
								$this->itemsDeReceta($codigoItem, $factura, $account, $menuItem->codigo_referencia, $menuItem, $account->cantidad, $accountMaster->salon_id, $salonMenusItem->almacen);
							} else {
								if($menuItem->tipo_costo!='N'){
									Flash::error("El tipo de costo de '{$menuItem->nombre}' es desconocido");
								}
							}
						}
					} else {
						Flash::error("El item '{$menuItem->nombre}' se ha vendido pero no existe en el ambiente '{$accountMaster->salon_id}'");
					}
				} else {
					Flash::error("El item de menu '$account->menus_items_id' ya no existe");
				}
			}
		}
		echo '<tr>
			<td colspan="9" align="right"><b>TOTAL</b></td>
			<td align="right">', $this->total, '</td>
		</tr>';
		echo '</table>';


		$this->routeTo("action: index");

	}

	public function interAction()
	{
		new InterfasePOS2();
	}

}
