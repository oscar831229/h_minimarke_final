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

class SaldosController extends ApplicationController {

	public function initialize(){
		$this->setTemplateAfter("admin_menu");
	}

	public function indexAction(){
		$this->loadModel('Almacenes');

		$meses = array(
			'01' => 'ENERO',
			'02' => 'FEBRERO',
			'03' => 'MARZO',
			'04' => 'ABRIL',
			'05' => 'MAYO',
			'06' => 'JUNIO',
			'07' => 'JULIO',
			'08' => 'AGOSTO',
			'09' => 'SEPTIEMBRE',
			'10' => 'OCTUBRE',
			'11' => 'NOVIEMBRE',
			'12' => 'DICIEMBRE'
		);

		$periodos = array(0 => 'ACTUAL');
		foreach($this->Saldos->distinct(array('ano_mes', 'conditions' => 'ano_mes>0', 'order' => '1 DESC')) as $anoMes){
			$mes = substr($anoMes, 4, 2);
			$periodos[$anoMes] = $meses[$mes].' '.substr($anoMes, 0, 4);
		}
		$this->setParamToView('periodos', $periodos);
	}

	public function procesoAction(){

		$this->setResponse('view');

		try {

			$codigoAlmacen = $this->getQueryParam('almacen', 'int');
			$almacen = $this->Almacenes->findFirst("codigo='{$codigoAlmacen}'");
			if($almacen==false){
				Flash::error("No existe el almacen '$codigoAlmacen'");
				return false;
			}

			$fechaProceso = $this->getQueryParam('fecha', 'date');
			$fechaProceso = new Date($fechaProceso);

			$anoMes = $this->getQueryParam('ano_mes', 'alpha');

			$datos = $this->Datos->findFirst();
			echo '<h1>Saldos Inventarios</h1';
			echo "<h2>", $datos->getNombreHotel(), "</h2>";
			echo '<h2>Almacen: '.$almacen->getCodigo().'-'.$almacen->getNomAlmacen().'</h2>';
			if($anoMes=='0'){
				echo '<h2>Periodo: Actual</h2>';
			} else {
				echo '<h2>Periodo: '.$anoMes.'</h2>';
			}
			echo '<h2>Fecha Impresión: '.Date::now().'</h2>';

			$lineas = array();
			$saldos = array();
			foreach($this->Inve->find("estado='A'", "order: linea,descripcion") as $inve){
				$linea = $this->Lineas->findFirst("linea='{$inve->getLinea()}' AND almacen='$codigoAlmacen'");
				if($linea!=false){
					if(!isset($saldos[$linea->getLinea()])){
						$saldos[$linea->getLinea()] = array();
					}
					$saldos[$linea->getLinea()][] = $inve;
					$lineas[$linea->getLinea()] = $linea;
				}
			}

			$totalCosto = 0;
			$totalSaldo = 0;
			echo '<br>
			<table cellspacing="0">';
			foreach($saldos as $linea => $inves){
				echo '<tr><td colspan="6">', $linea, ' ', $lineas[$linea]->getNombre(), '</td></tr>
				<tr>
					<th>Código</th>
					<th>Descripción</th>
					<th>Unidad</th>
					<th>Saldo</th>
					<th>Costo</th>
					<th>Saldo Físico</th>
				</tr>';
				$totalSaldoLinea = 0;
				$totalCostoLinea = 0;
				foreach($inves as $inve){
					$unidad = $this->Unidad->findFirst("codigo='{$inve->getUnidad()}'");
					echo '<tr>
						<td>', $inve->getItem(), '</td>
						<td>', utf8_encode($inve->getDescripcion()), '</td>';
					if($unidad==false){
						echo '<td>???</td>';
					} else {
						echo '<td>', $unidad->nom_unidad, '</td>';
					}

					$saldo = $this->Saldos->findFirst("almacen='$codigoAlmacen' AND item='{$inve->getItem()}' AND ano_mes='$anoMes'");
					if($saldo==false){
						echo '<td align="right">0,000000</td><td align="right">0,000000</td><td align="right">&nbsp;</td>';
					} else {
						echo '<td align="right">', Currency::number($saldo->getSaldo(), 6), '</td>
						<td align="right">', Currency::number($saldo->getCosto(), 6), '</td>
						<td align="right">&nbsp;</td>';
						$totalSaldoLinea+=$saldo->getSaldo();
						$totalCostoLinea+=$saldo->getCosto();
						$totalSaldo+=$saldo->getSaldo();
						$totalCosto+=$saldo->getCosto();
					}
					echo '</tr>';
				}
				echo '<tr>
					<td colspan="3" align="right"><b>TOTAL LÍNEA</b></td>
					<td align="right"><b>', Currency::number($totalSaldoLinea, 6), '</b></td>
					<td align="right"><b>', Currency::number($totalCostoLinea, 6), '</b></td>
					<td>&nbsp;</td>
				</tr>';
			}

			echo '<tr>
				<td colspan="3" align="right"><b>TOTAL INVENTARIO</b></td>
				<td align="right"><b>', Currency::number($totalSaldo, 6), '</b></td>
				<td align="right"><b>', Currency::number($totalCosto, 6), '</b></td>
				<td>&nbsp;</td>
			</tr>';

			echo '</table>';

		}
		catch(TransactionFailed $e){

		}

	}

	public function desviacionAction(){

		 $sql = "select costo/saldo as costo, ano_mes from ramocol.saldos where item = 16004 and almacen = 1 order by 2";
		 $db = DbBase::rawConnect();
		 $cursor = $db->query($sql);
		 $values = array();
		 while($row = $db->fetchArray($cursor)){
		 	$values[] = $row['costo'];
		 }

		 $o = 1/count($values)*array_sum($values);
		 $sum = 0;
		 foreach($values as $value){
		 	$sum+=pow($value-$o, 2);
		 }
		 echo sqrt(1/count($values)*$sum);

	}

}
