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

class Recalcula_SaldosController extends ApplicationController
{

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
	}

	public function indexAction()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isGet()==true){
			$fecha = new Date();
			$fecha->diffMonths(1);
			Tag::displayTo('fecha', Date::getFirstDayOfMonth($fecha->getMonth()));
		}
		$this->loadModel('Almacenes');
	}

	public function procesoAction()
	{
		$config = CoreConfig::readFromActiveApplication('app.ini');
		if (!isset($config->pos->back_version)) {
			return $this->routeToAction('proceso60');
		} else {
			if(version_compare($config->pos->back_version, '6.0', '>=')){
				return $this->routeToAction('proceso60');
			} else {
				return $this->routeToAction('proceso54');
			}
		}
	}

	public function proceso60Action()
	{

		$codigoItem = $this->getQueryParam('item', 'alpha');
		$inve = $this->Inve->findFirst("item='{$codigoItem}'");
		if($inve==false){
			Flash::error("No existe la referencia '$codigoItem'");
			$this->routeToAction('index');
			return false;
		}

		$codigoAlmacen = $this->getQueryParam('almacen', 'int');
		$almacen = $this->Almacenes->findFirst("codigo='{$codigoAlmacen}'");
		if($almacen==false){
			Flash::error("No existe el almacen '$codigoAlmacen'");
			$this->routeToAction('index');
			return false;
		}

		$fecha = $this->getPostParam('fecha', 'date');

		echo Tag::stylesheetLink('hfos/kardex');

		$this->setResponse('view');

		try {
			echo Tatico::showKardex($codigoItem, $codigoAlmacen, $fecha);
		}
		catch(TaticoException $e){
			Flash::error($e->getMessage());
		}

	}

	public function proceso54Action()
	{

		try {

			echo Tag::stylesheetLink('hfos/kardex');

			$codigoItem = $this->getQueryParam('item', 'alpha');
			$inve = $this->Inve->findFirst("item='{$codigoItem}'");
			if($inve==false){
				Flash::error("No existe la referencia '$codigoItem'");
				$this->routeToAction('index');
				return false;
			}

			$codigoAlmacen = $this->getQueryParam('almacen', 'int');
			$almacen = $this->Almacenes->findFirst("codigo='{$codigoAlmacen}'");
			if($almacen==false){
				Flash::error("No existe el almacen '$codigoAlmacen'");
				$this->routeToAction('index');
				return false;
			}

			$this->setResponse('view');
			set_time_limit(0);

			$fechaProceso = $this->getQueryParam('fecha', 'date');
			$fechaProceso = new Date($fechaProceso);

			ActiveRecord::disableEvents(true);
			$transaction = TransactionManager::getUserTransaction();
			//$transaction->getConnection()->setDebug(true);
			$this->Movihead->setTransaction($transaction);
			$this->Movilin->setTransaction($transaction);
			$this->Saldos->setTransaction($transaction);

			$empresa = $this->Empresa->findFirst();
			$datos = $this->Datos->findFirst();

			#foreach($this->Inve->find() as $inve){

			echo '<h1 class="kardex">Kardex Inventario</h1>';
			echo "<h2 class='kardex'>", $datos->getNombreHotel(), "</h2>";
			echo '<h2 class="kardex">Almacén: '.$almacen->getCodigo().'-'.$almacen->getNomAlmacen().'</h2>';
			echo '<h2 class="kardex">Referencia: '.$inve->item.'-', utf8_encode($inve->descripcion), '</h2>';
			echo '<h2 class="kardex">Fecha: '.$fechaProceso.'</h2>';
			echo '<h2 class="kardex">Fecha Reporte: '.Date::now().'</h2>';

			$saldos = array();
			$saldosc = array();
			$periodoAnterior = 0;
			$nuevoSaldo = 0;
			$movilins = $this->Movilin->find("item='{$inve->item}'", "order: fecha");
			echo '<br/>
			<table cellspacing="0" class="kardex">
				<tr>
					<th>Comprobante</th>
					<th>Número</th>
					<th>Movimiento</th>
					<th>Fecha</th>
					<th>Almacen</th>
					<th>Costo</th>
					<th>Cantidad</th>
					<th>Nuevo Saldo</th>
				</tr>';
			foreach($movilins as $movilin){
				$fecha = new Date($movilin->getFecha());
				if(substr($movilin->getComprob(), 0, 1)=="E"){
					if(!isset($saldos[$movilin->getAlmacen()][$movilin->getItem()])){
						$saldos[$movilin->getAlmacen()][$movilin->getItem()]['saldo'] = 0;
						$saldos[$movilin->getAlmacen()][$movilin->getItem()]['costo'] = 0;
					}
					$saldos[$movilin->getAlmacen()][$movilin->getItem()]['saldo']+=$movilin->getCantidad();
					$saldos[$movilin->getAlmacen()][$movilin->getItem()]['costo']+=($movilin->getValor());
					if($movilin->getAlmacen()==$codigoAlmacen){

						if($periodoAnterior==''){
							if(!isset($saldosc[$fecha->getPeriod()])){
								$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
							}
						} else {
							if($periodoAnterior!=$fecha->getPeriod()){
								if(isset($saldosc[$periodoAnterior])){
									$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
									$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
								} else {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							}
						}
						$saldosc[$fecha->getPeriod()]['costo']+=$movilin->getValor();
						$saldosc[$fecha->getPeriod()]['saldo']+=$movilin->getCantidad();
						$periodoAnterior = $fecha->getPeriod();

						$nuevoSaldo+=$movilin->getCantidad();
						if(Date::compareDates($fechaProceso, $fecha)<1){
							echo '<tr>
								<td align="right">', $movilin->getComprob(), '</td>
								<td align="right">', $movilin->getNumero(), '</td>
								<td>ENTRADA</td>
								<td align="right">', $fecha, '</td>
								<td align="right">', $movilin->getAlmacen(), '</td>
								<td align="right">', Currency::number($movilin->getValor(), 6), '</td>
								<td align="right">', $movilin->getCantidad(), '</td>
								<td align="right">', Currency::number($nuevoSaldo, 6), '</td>
							</tr>';
						}
					}
					continue;
				}

				//Ajuste
				if(substr($movilin->getComprob(), 0, 1)=="A"){
					if(!isset($saldos[$movilin->getAlmacen()][$movilin->getItem()])){
						$saldos[$movilin->getAlmacen()][$movilin->getItem()]['saldo'] = 0;
						$saldos[$movilin->getAlmacen()][$movilin->getItem()]['costo'] = 0;
					}
					$saldos[$movilin->getAlmacen()][$movilin->getItem()]['saldo']+=$movilin->getCantidad();
					$saldos[$movilin->getAlmacen()][$movilin->getItem()]['costo']+=($movilin->getValor());
					if($movilin->getAlmacen()==$codigoAlmacen){

						if($periodoAnterior==''){
							if(!isset($saldosc[$fecha->getPeriod()])){
								$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
							}
						} else {
							if($periodoAnterior!=$fecha->getPeriod()){
								if(isset($saldosc[$periodoAnterior])){
									$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
									$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
								} else {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							}
						}
						$saldosc[$fecha->getPeriod()]['costo']+=$movilin->getValor();
						$saldosc[$fecha->getPeriod()]['saldo']+=$movilin->getCantidad();
						$periodoAnterior = $fecha->getPeriod();

						$nuevoSaldo+=$movilin->getCantidad();
						if(Date::compareDates($fechaProceso, $fecha)<1){
							echo '<tr>
								<td align="right">', $movilin->getComprob(), '</td>
								<td align="right">', $movilin->getNumero(), '</td>
								<td>AJUSTE</td>
								<td align="right">', $fecha, '</td>
								<td align="right">', $movilin->getAlmacen(), '</td>
								<td align="right">', Currency::number($movilin->getValor(), 6), '</td>
								<td align="right">', $movilin->getCantidad(), '</td>
								<td align="right">', Currency::number($nuevoSaldo, 6), '</td>
							</tr>';
						}

					}
					continue;
				}

				//Traslados
				if(substr($movilin->getComprob(), 0, 1)=="T"){
					if($movilin->getAlmacen()!=$movilin->getAlmacenDestino()){
						if(!isset($saldos[$movilin->getAlmacen()][$movilin->getItem()])){
							$saldos[$movilin->getAlmacen()][$movilin->getItem()]['saldo'] = 0;
							$saldos[$movilin->getAlmacen()][$movilin->getItem()]['costo'] = 0;
						}
						if(!isset($saldos[$movilin->getAlmacenDestino()][$movilin->getItem()])){
							$saldos[$movilin->getAlmacenDestino()][$movilin->getItem()]['saldo'] = 0;
							$saldos[$movilin->getAlmacenDestino()][$movilin->getItem()]['costo'] = 0;
						}
						$saldos[$movilin->getAlmacen()][$movilin->getItem()]['saldo']-=$movilin->getCantidad();
						$saldos[$movilin->getAlmacen()][$movilin->getItem()]['costo']-=($movilin->getValor());
						$saldos[$movilin->getAlmacenDestino()][$movilin->getItem()]['saldo']+=$movilin->getCantidad();
						$saldos[$movilin->getAlmacenDestino()][$movilin->getItem()]['costo']+=($movilin->getValor());
						if($movilin->getAlmacenDestino()==$codigoAlmacen){

							if($periodoAnterior==''){
								if(!isset($saldosc[$fecha->getPeriod()])){
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							} else {
								if($periodoAnterior!=$fecha->getPeriod()){
									if(isset($saldosc[$periodoAnterior])){
										$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
										$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
									} else {
										$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
									}
								}
							}
							$saldosc[$fecha->getPeriod()]['costo']+=$movilin->getValor();
							$saldosc[$fecha->getPeriod()]['saldo']+=$movilin->getCantidad();
							$periodoAnterior = $fecha->getPeriod();

							$nuevoSaldo+=$movilin->getCantidad();
							if(Date::compareDates($fechaProceso, $fecha)<1){
								echo '<tr>
									<td align="right">', $movilin->getComprob(), '</td>
									<td align="right">', $movilin->getNumero(), '</td>
									<td>T. ENTRAN</td>
									<td align="right">', $fecha, '</td>
									<td align="right">', $movilin->getAlmacenDestino(), '</td>
									<td align="right">', Currency::number($movilin->getValor(), 6), '</td>
									<td align="right">', $movilin->getCantidad(), '</td>
									<td align="right">', Currency::number($nuevoSaldo, 6), '</td>
								</tr>';
							}

						}
						if($movilin->getAlmacen()==$codigoAlmacen){

							if($periodoAnterior==''){
								if(!isset($saldosc[$fecha->getPeriod()])){
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							} else {
								if($periodoAnterior!=$fecha->getPeriod()){
									if(isset($saldosc[$periodoAnterior])){
										$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
										$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
									} else {
										$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
									}
								}
							}
							$saldosc[$fecha->getPeriod()]['costo']-=$movilin->getValor();
							$saldosc[$fecha->getPeriod()]['saldo']-=$movilin->getCantidad();
							$periodoAnterior = $fecha->getPeriod();

							$nuevoSaldo-=$movilin->getCantidad();
							if(Date::compareDates($fechaProceso, $fecha)<1){
								echo '<tr>
									<td align="right">', $movilin->getComprob(), '</td>
									<td align="right">', $movilin->getNumero(), '</td>
									<td>T. SALEN</td>
									<td align="right">', $fecha, '</td>
									<td align="right">', $movilin->getAlmacen(), '</td>
									<td align="right">', Currency::number($movilin->getValor(), 6), '</td>
									<td align="right">', $movilin->getCantidad(), '</td>
									<td align="right">', Currency::number($nuevoSaldo, 6), '</td>
								</tr>';
							}

						}
					}
					continue;
				}

				//Consumos
				if(substr($movilin->getComprob(), 0, 1)=="C"){
					if(!isset($saldos[$movilin->getAlmacen()][$movilin->getItem()])){
						$saldos[$movilin->getAlmacen()][$movilin->getItem()]['saldo'] = 0;
						$saldos[$movilin->getAlmacen()][$movilin->getItem()]['costo'] = 0;
					}
					$saldos[$movilin->getAlmacen()][$movilin->getItem()]['saldo']-=$movilin->getCantidad();
					$saldos[$movilin->getAlmacen()][$movilin->getItem()]['costo']-=$movilin->getValor();
					if($movilin->getAlmacen()==$codigoAlmacen){

						if($periodoAnterior==''){
							if(!isset($saldosc[$fecha->getPeriod()])){
								$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
							}
						} else {
							if($periodoAnterior!=$fecha->getPeriod()){
								if(isset($saldosc[$periodoAnterior])){
									$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
									$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
								} else {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							}
						}
						$saldosc[$fecha->getPeriod()]['costo']-=$movilin->getValor();
						$saldosc[$fecha->getPeriod()]['saldo']-=$movilin->getCantidad();
						$periodoAnterior = $fecha->getPeriod();

						$nuevoSaldo-=$movilin->getCantidad();
						if(Date::compareDates($fechaProceso, $fecha)<1){
							echo '<tr>
								<td align="right">', $movilin->getComprob(), '</td>
								<td align="right">', $movilin->getNumero(), '</td>
								<td>SALEN</td>
								<td align="right">', $fecha, '</td>
								<td align="right">', $movilin->getAlmacen(), '</td>
								<td align="right">', Currency::number($movilin->getValor(), 6), '</td>
								<td align="right">', $movilin->getCantidad(), '</td>
								<td align="right">', Currency::number($nuevoSaldo, 6), '</td>
							</tr>';
						}

					}
					continue;
				}
			}
			echo '</table>';

			$saldoTotal = 0;
			$costoTotal = 0;
			echo '<br/>
			<b>Saldos/Costos Actuales</b>
			<table cellspacing="0" class="kardex"><thead><tr><th>Almacen</th><th>Saldo</th><th>Costo</th></tr>';
			foreach($saldos as $numeroAlmacen => $saldo){
				foreach($saldo as $item => $s){
					echo '<tr>
						<td align="right">'.$numeroAlmacen.'</td>
						<td align="right">'.Currency::number($s['saldo'], 6).'</td>
						<td align="right">'.Currency::number($s['costo'], 6).'</td>
					</tr>';
					$saldoTotal+=$s['saldo'];
					$costoTotal+=$s['costo'];
					$saldo = $this->Saldos->findFirst("almacen='$numeroAlmacen' AND item='{$inve->item}' AND ano_mes=0");
					if($saldo!=false){
						$saldo->setTransaction($transaction);
						$saldo->costo = $s['costo'];
						$saldo->saldo = $s['saldo'];
						if($saldo->save()==false){
							foreach($saldo->getMessages() as $message){
								Flash::error($saldo->getMessage());
							}
						}
					}
				}
			}
			echo '<tr>
				<td align="right"><b>Consolidado</b></td>
				<td align="right"><b>', Currency::number($saldoTotal, 6), '</b></td>
				<td align="right"><b>', Currency::number($costoTotal, 6), '</b></td>
			</tr></table>';

			if(count($saldosc)>1){
				echo '<br/><b>Histórico Referencia</b>';
				echo '<table cellspacing="0" class="kardex">
					<thead>
						<tr>
							<th>Periodo</th>
							<th>Saldo</th>
							<th>Costo</th>
							<th>Saldo Almacenes</th>
							<th>Costo Almacenes</th>
						</tr>';
				$periodoAnterior = '0';
				foreach($saldosc as $periodo => $saldoc){
					$saldosPeriodos = $this->Saldos->find("almacen='$codigoAlmacen' AND item='{$inve->item}' AND ano_mes<='$periodo' AND ano_mes>'$periodoAnterior'");
					foreach($saldosPeriodos as $saldo){

						$saldo->setTransaction($transaction);
						$saldo->saldo = $saldoc['saldo'];
						$saldo->costo = $saldoc['costo'];
						if($saldo->save()==false){
							foreach($saldo->getMessages() as $message){
								Flash::error($saldo->getMessage());
							}
						}

						//Recalcular el almacen=0
						$saldoPeriodo = 0;
						$costoPeriodo = 0;
						$saldosPeriodos = $this->Saldos->find("item='{$inve->item}' AND ano_mes='{$saldo->getAnoMes()}' AND almacen<>0");
						foreach($saldosPeriodos as $saldo){
							$saldoPeriodo+=$saldo->getSaldo();
							$costoPeriodo+=$saldo->getCosto();
						}
						$saldoAlmacenCero = $this->Saldos->findFirst("almacen='0' AND item='{$inve->item}' AND ano_mes='{$saldo->getAnoMes()}'");
						if($saldoAlmacenCero!=false){
							$saldoAlmacenCero->setTransaction($transaction);
							$saldoAlmacenCero->saldo = $saldoPeriodo;
							$saldoAlmacenCero->costo = $costoPeriodo;
							if($saldoAlmacenCero->save()==false){
								foreach($saldoAlmacenCero->getMessages() as $message){
									Flash::error($saldoAlmacenCero->getMessage());
								}
							}
						}

						echo '<tr>
							<td>', $saldo->getAnoMes(), '</td>
							<td align="right">', Currency::number($saldoc['saldo'], 6), '</td>
							<td align="right">', Currency::number($saldoc['costo'], 6), '</td>
							<td align="right">', Currency::number($saldoPeriodo, 6), '</td>
							<td align="right">', Currency::number($costoPeriodo, 6), '</td>
						</tr>';
					}
					$periodoAnterior = $periodo;
				}

				$fechaCierre = new Date($empresa->getFCierrei());
				$periodoCierre = $fechaCierre->getYear().sprintf('%02s', $fechaCierre->getMonth());
				$saldosPeriodos = $this->Saldos->find("almacen='$codigoAlmacen' AND item='{$inve->item}' AND ano_mes<='$periodoCierre' AND ano_mes>'$periodoAnterior'");
				foreach($saldosPeriodos as $saldo){
					$saldo->setTransaction($transaction);
					$saldo->saldo = $saldoc['saldo'];
					$saldo->costo = $saldoc['costo'];
					if($saldo->save()==false){
						foreach($saldo->getMessages() as $message){
							Flash::error($saldo->getMessage());
						}
					}

					//Recalcular el almacen=0
					$saldoPeriodo = 0;
					$costoPeriodo = 0;
					$saldosPeriodosAnoMes = $this->Saldos->find("item='{$inve->item}' AND ano_mes='{$saldo->getAnoMes()}' AND almacen<>0");
					foreach($saldosPeriodosAnoMes as $saldoAnoMes){
						$saldoPeriodo+=$saldoAnoMes->getSaldo();
						$costoPeriodo+=$saldoAnoMes->getCosto();
					}
					$saldoAlmacenCero = $this->Saldos->findFirst("almacen='0' AND item='{$inve->item}' AND ano_mes='{$saldo->getAnoMes()}'");
					if($saldoAlmacenCero!=false){
						$saldoAlmacenCero->setTransaction($transaction);
						$saldoAlmacenCero->saldo = $saldoPeriodo;
						$saldoAlmacenCero->costo = $costoPeriodo;
						if($saldoAlmacenCero->save()==false){
							foreach($saldoAlmacenCero->getMessages() as $message){
								Flash::error($saldoAlmacenCero->getMessage());
							}
						}
					}

					echo '<tr>
						<td>'.$saldo->getAnoMes().'</td>
						<td align="right">', Currency::number($saldoc['saldo'], 6), '</td>
						<td align="right">', Currency::number($saldoc['costo'], 6), '</td>
						<td align="right">', Currency::number($saldoPeriodo, 6), '</td>
						<td align="right">', Currency::number($costoPeriodo, 6), '</td>
					</tr>';
				}
				echo '</table>';
			} else {
				echo '<br><span>La referencia no tiene histórico</span>';
			}

			$inve->setTransaction($transaction);
			$inve->saldo_actual = $saldoTotal;
			$inve->costo_actual = $costoTotal;
			if($inve->save()==false){
				foreach($inve->getMessages() as $message){
					Flash::error($saldo->getMessage());
				}
			}

			#}

			$transaction->commit();
		}
		catch(TransactionFailed $e){

		}
	}

	public function queryReferenciasAction()
	{
		$this->setResponse('view');
		$nombre = $this->getPostParam('nombre');
		echo '<ul>';
		if ($nombre != '') {
			foreach ($this->Inve->find("descripcion LIKE '%$nombre%' AND estado='A'", 'order: descripcion', 'columns: item,descripcion', 'limit: 20') as $inve){
				echo '<li id="', $inve->getItem(), '">', utf8_encode($inve->getDescripcion()), '</li>';
			}
		}
		echo '</ul>';
	}

	public function queryByItemAction()
	{
		$this->setResponse('json');
		$item = $this->getPostParam('item');
		$inve = $this->Inve->findFirst("item='$item' AND estado='A'");
		if($inve==false){
			return 'NO EXISTE LA REFERENCIA';
		} else {
			return utf8_encode($inve->getDescripcion());
		}
	}

	public function quitaNegativosAction()
	{
		$saldos = array();
		foreach ($this->Saldos->find("ano_mes = 0 AND saldo < 0") as $saldo) {
			$saldos[$saldo->almacen][(string) $saldo->item] = -$saldo->saldo;
		}
		print_r($saldos);
	}

}
