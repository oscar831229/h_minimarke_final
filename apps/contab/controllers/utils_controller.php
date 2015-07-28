<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class UtilsController extends ApplicationController
{

	public function recalculaPeriodoActualAction()
	{
		$this->setResponse('view');
		$util = new AuraUtils();
		$util->recalculaPeriodoActual();
	}

	public function recalculaCarteraAction()
	{
		$this->setResponse('view');
		$util = new AuraUtils();
		$util->recalculaCartera();
	}

	public function emisionCarteraAction()
	{
		$this->setResponse('view');
		echo '<table>';
		foreach($this->Cuentas->find(array("pide_fact='S'", "order" => "cuenta")) as $cuenta){
			$codigoCuenta = $cuenta->getCuenta();
			foreach($this->Movi->find(array("cuenta='$codigoCuenta' AND fecha>='2011-08-01'", "order" => "fecha,tipo_doc,numero_doc")) as $movi){
				$conditions = "cuenta='$codigoCuenta' AND nit='{$movi->getNit()}' AND tipo_doc='{$movi->getTipoDoc()}' AND numero_doc='{$movi->getNumeroDoc()}'";
				$cartera = $this->Cartera->findFirst($conditions);
				if($cartera==false){
					echo '<tr>
						<td>', $movi->getFecha(), '</td>
						<td>', $movi->getComprob(), '</td>
						<td>', $codigoCuenta, '</td>
						<td>', $movi->getTipoDoc(), '</td>
						<td>', $movi->getNumeroDoc(), '</td>
						<td>', $movi->getNumeroDoc(), '</td>
						<td>NO TIENE</td>
					</tr>';
				} else {
					echo '<tr>
						<td>', $movi->getFecha(), '</td>
						<td>', $movi->getComprob(), '</td>
						<td>', $codigoCuenta, '</td>
						<td>', $movi->getTipoDoc(), '</td>
						<td>', $movi->getNumeroDoc(), '</td>
						<td>', $movi->getNumeroDoc(), '</td>
						<td>', $cartera->getFEmision(), '</td>
					</tr>';
				}
			}
		}
	}

}