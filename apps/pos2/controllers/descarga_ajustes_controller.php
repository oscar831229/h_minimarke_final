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

class Descarga_AjustesController extends ApplicationController
{

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
	}

	public function indexAction()
	{

	}

	public function procesoAction()
	{
		$this->cleanTemplateAfter();

		$codigoComprob = $this->getPostParam('comprob', 'alpha');
		$numero = $this->getPostParam('numero', 'int');
		//$this->Movilin->setDebug(true);

		$interface = array();
		$movilins = $this->Movilin->find("comprob='$codigoComprob' AND numero='$numero'");
		if (count($movilins)) {
			$comprob = $this->Comprob->findFirst("codigo='$codigoComprob'");
			if (!$comprob) {
				Flash::error("No existe el comprobante $codigoComprob");
				return;
			} else {
				if($comprob->comprob_contab==''){
					Flash::error("No existe el comprobante contable del comprobante $codigoComprob");
				}
			}

			echo '<h1>Hotel Front-Office Solution</h1>';
			echo '<h2>Reporte Costo de Ajustes</h2>';
			echo '<h3>Comprobante: ', $codigoComprob, '-', $numero, '</h3>';
			echo '<h3>Fecha de Impresión: ', Date::now(), '</h3>';
			echo '<br>';

			echo '<table><tr><th>Comprob</th><th>Centro Costo</th><th>Cuenta</th><th>Naturaleza</th><th>Valor</th></tr>';
			foreach ($movilins as $movilin) {
				if (!isset($almacen)) {
					$almacen = $this->Almacenes->findFirst("codigo='$movilin->almacen'");
				}
				$item = $this->Inve->findFirst("item='{$movilin->item}'");
				if ($item != false) {
					$linea = $this->Lineas->findFirst("almacen='{$movilin->almacen}' AND linea='{$item->linea}'");
					if ($linea == false) {
						Flash::error("No existe la línea de producto {$item->linea} en el almacen {$movilin->almacen}");
					} else {
						if ($movilin->valor != 0) {
							if (!isset($interface[$comprob->comprob_contab])) {
								$interface[$comprob->comprob_contab] = array();
							}
							if (!isset($interface[$comprob->comprob_contab][$almacen->centro_costo])) {
								$interface[$comprob->comprob_contab][$almacen->centro_costo] = array();
							}
							if (!isset($interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_costo_venta])) {
								$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_costo_venta] = array();
							}

							if ($movilin->valor < 0) {
								$deb_cre = 'D';
								$deb_cre_con = 'C';
							} else {
								$deb_cre = 'C';
								$deb_cre_con = 'D';
							}

							if (!isset($interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_costo_venta][$deb_cre])) {
								$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_costo_venta][$deb_cre] = array();
								$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_costo_venta][$deb_cre]['valor'] = 0;
							}
							$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_costo_venta][$deb_cre]['almacen'] = $almacen->codigo;
							$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_costo_venta][$deb_cre]['valor'] += abs($movilin->valor);

							if (!isset($interface[$comprob->comprob_contab][$almacen->centro_costo])) {
								$interface[$comprob->comprob_contab][$almacen->centro_costo] = array();
							}
							if (!isset($interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_inve])) {
								$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_inve] = array();
							}
							if (!isset($interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_inve][$deb_cre_con])) {
								$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_inve][$deb_cre_con] = array();
								$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_inve][$deb_cre_con]['valor'] = 0;
								$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_inve][$deb_cre_con]['almacen'] = 0;
							}
							$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_inve][$deb_cre_con]['almacen'] = $almacen->codigo;
							$interface[$comprob->comprob_contab][$almacen->centro_costo][$linea->cta_inve][$deb_cre_con]['valor'] += abs($movilin->valor);

						} else {
							Flash::notice("No se tiene en cuenta '" . $item->descripcion . "' cantidad ".$movilin->cantidad." porque el costo es 0");
							return;
						}
					}
				} else {
					Flash::error('No existe el item ' . $movilin->item);
				}

			}

			foreach ($interface as $comprob => $moviComprob) {
				foreach ($moviComprob as $centroCosto => $moviCentro) {
					foreach ($moviCentro as $cuenta => $moviCuenta) {
						foreach ($moviCuenta as $naturaleza => $movi) {
							if ($naturaleza == 'D') {
								echo '<tr>
									<td align="center">' . $comprob . '</td>
									<td align="center">' . $centroCosto . '</td>
									<td>' . $cuenta . '</td>
									<td>DEBITO</td>
									<td align="right">' . LocaleMath::round($movi['valor'], 2) . '</td>
								</tr>';
							} else {
								echo '<tr bgcolor="#f7f7f7">
									<td align="center">' . $comprob . '</td>
									<td align="center">' . $centroCosto . '</td>
									<td>' . $cuenta . '</td>
									<td>CR&Eacute;DITO</td>
									<td align="right">' . LocaleMath::round($movi['valor'], 2) . '</td>
								</tr>';
							}
						}
					}
				}
			}

			echo '</table>';
		} else {
			Flash::error("No existe el comprobante $codigoComprob-$numero");
		}

	}

}