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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * Clase que genera ayudas para impresion y otros
 *
 */
class TpcHelper extends UserComponent {


	/**
	 * Metodo que genera una vista de las cuotas iniciales de un contrato
	 *
	 * @param array $config (
	 *  'sociosId' => int
	 * )
	 * @return string $html
	 */
	public static function generaListaCuotasIniciales($config){

		$sociosId = $config['sociosId'];

		$detalleCuota = EntityManager::get('DetalleCuota')->findFirst(array('conditions'=>'socios_id='.$sociosId));

		$html = '<table class="hyBrowseTab zebraSt sortable" cellspacing="0" cellpadding="0" align="center">';

		if($detalleCuota != false){
			$totalCuota		= 0;
			$totalPagado	= 0;
			$ttoalSaldo		= 0;
			$html.= '
				<tr>
					<th class="sortcol">Número de cuota</th>
					<th class="sortcol">Fecha de pago</th>
					<th class="sortcol">Valor de cuota</th>
					<th class="sortcol">Valor pagado</th>
					<th class="sortcol">Saldo de cuota</th>
					<th class="sortcol">Estado de cuota</th>
				</tr>
				<tr>
					<td align="left">PRIMERA CUOTA</td>
					<td align="center">'.$detalleCuota->getFecha1().'</td>
					<td align="right">'.Currency::number($detalleCuota->getHoy()).'</td>
					<td align="right">'.Currency::number($detalleCuota->getHoyPagado()).'</td>
					<td align="right">'.Currency::number(($detalleCuota->getHoy()-$detalleCuota->getHoyPagado())).'</td>
					<td align="center">'.$detalleCuota->getEstado1().'</td>
				</tr>
				<tr>
					<td align="left">SEGUNDA CUOTA</td>
					<td align="center">'.$detalleCuota->getFecha2().'</td>
					<td align="right">'.Currency::number($detalleCuota->getCuota2()).'</td>
					<td align="right">'.Currency::number($detalleCuota->getCuota2Pagado()).'</td>
					<td align="right">'.Currency::number($detalleCuota->getCuota2()-$detalleCuota->getCuota2Pagado()).'</td>
					<td align="center">'.$detalleCuota->getEstado2().'</td>
				</tr>
				<tr>
					<td align="left">TERCERA CUOTA</td>
					<td align="center">'.$detalleCuota->getFecha3().'</td>
					<td align="right">'.Currency::number($detalleCuota->getCuota3()).'</td>
					<td align="right">'.Currency::number($detalleCuota->getCuota3Pagado()).'</td>
					<td align="right">'.Currency::number($detalleCuota->getCuota3()-$detalleCuota->getCuota3Pagado()).'</td>
					<td align="center">'.$detalleCuota->getEstado3().'</td>
				</tr>
			';
			$totalCuota = $detalleCuota->getHoy() + $detalleCuota->getCuota2() + $detalleCuota->getCuota3();
			$totalPagado = $detalleCuota->getHoyPagado() + $detalleCuota->getCuota2Pagado() + $detalleCuota->getCuota3Pagado();
			$saldo1 = $detalleCuota->getHoy()-$detalleCuota->getHoyPagado();
			$saldo2 = $detalleCuota->getCuota2()-$detalleCuota->getCuota2Pagado();
			$saldo3 = $detalleCuota->getCuota3()-$detalleCuota->getCuota3Pagado();
			$totalSaldo = $saldo1 + $saldo2 + $saldo3;
			$html .= '
				<tr>
					<td align="left"></td>
					<td align="center"></td>
					<td align="right"><b>'.Currency::number($totalCuota).'</b></td>
					<td align="right"><b>'.Currency::number($totalPagado).'</b></td>
					<td align="right"><b>'.Currency::number($totalSaldo).'</b></td>
				</tr>
			';
		}else{
			$html .= '
				<tr>
					<td colspan="6">No se encontró cuotas iniciales de ese contrato</td>
				</tr>
			';
		}

		$html .='
			</table>
		';

		return $html;
	}

	/**
	 * Metodo que genera un listado de recibos de pago de un contrato
	 *
	 * @param array $config (
	 *  'sociosId' => int
	 *  'reservasId' => int
	 * )
	 * @return string $html
	 */
	public static function generaListaRecibosPagos($config){
		$conditions = '';
		$tipo = 'C';
		$id = 0;
		if(isset($config['sociosId'])){
			$sociosId = $config['sociosId'];
			$conditions = 'socios_id='.$sociosId.'';
			$id = $sociosId;
		}
		if(isset($config['reservasId'])){
			$reservasId = $config['reservasId'];
			//Recolectamos los id de los abonos a nombre de esa reserva
			$abonoReservasObj = EntityManager::get('AbonoReservas')->find(array('conditions' => 'reservas_id='.$reservasId));
			$idAbonosReservasArray = array();
			foreach($abonoReservasObj as $abonoReserva){
				$idAbonosReservasArray[]=$abonoReserva->getId();
			}
			if(count($idAbonosReservasArray)>0){
				$conditions = ' abono_reservas_id IN('.implode(', ', $idAbonosReservasArray).')';
			}else{
				$conditions = '1=0';//NO debe salir nada
			}
			$tipo = 'R';
			$id = $reservasId;
		}
		$recibosPagosObj = EntityManager::get('RecibosPagos')->find(array('conditions'=>$conditions));
		$html = '
			<table  class="hyBrowseTab zebraSt sortable" cellspacing="0" cellpadding="0" align="center" width="100%">';
		if(count($recibosPagosObj) > 0){
			$html.= '
				<tr>
					<th class="sortcol">Recibo de Caja</th>
					<th class="sortcol">Fecha de pago</th>
					<th class="sortcol">Concepto</th>
					<th class="sortcol">Valor pagado</th>
					<th class="sortcol">Es pago posterior?</th>
					<th class="sortcol">Estado</th>
					<th class="sortcol">Imprimir</th>
					<th class="sortcol">Detalle Abono</th>
					<th class="sortcol">Anular</th>
				</tr>';
			foreach ($recibosPagosObj as $reciboPago){
				$pagoPosterior = 'No';
				if($reciboPago->getPagoPosterior() == 'S'){
					$pagoPosterior = 'Si';
				}
				//Opción de anualr un recibo de caja solo si se hizo hoy
				$anularHtml = '';
				if($reciboPago->getFechaRecibo()==date('Y-m-d') && $reciboPago->getEstado()=='V'){
					$anularHtml = '<input type="button" value="" alt="'.$reciboPago->getId().'"  class="hyControlButton anularButton anularRecibosPagoButton" title="Anular recibo de caja"/>';
				}
				$estadoRc = 'Activo';
				if($reciboPago->getEstado()!='V'){//V: Activo
					switch($reciboPago->getEstado()){
						case 'A': //Anulado
							$estadoRc = 'Anulado';
							break;
						case 'C': //Trasladado por cambio de contrato
							$estadoRc = 'Trasladado';
							break;
						case 'N': //Nota contable 
							$estadoRc = 'Nota Contable';
							if($reciboPago->getDebCre()=='C'){
								$estadoRc .= ', Crédito';
							} else {
								$estadoRc .= ', Débito';	
							}
							break;	
						case 'K': //Abono a capital
							$estadoRc = 'Abono a Capital';
							break;	
					}
				}
				$html .= '
				<tr>
					<td align="center">'.$reciboPago->getRc().'</td>
					<td align="center">'.$reciboPago->getFechaPago().'</td>
					<td align="center" style="white-space: pre-wrap;">'.$reciboPago->getObservaciones().'</td>
					<td align="right">'.Currency::number($reciboPago->getValorPagado()).'</td>
					<td align="center">'.$pagoPosterior.'</td>
					<td align="center">'.$estadoRc.'</td>
					<td align="center" class="hyTdLeftControlButton">
						<input type="button" value="" alt="'.$reciboPago->getId().'"  class="hyControlButton printButton recibosPagoButton" title="Imprime recibo de caja"/>
					</td>';
				if($reciboPago->getEstado()=='V'){
					$html .= '
					<td align="center" class="hyTdLeftControlButton">
						<input type="button" value="" alt="'.$reciboPago->getId().'"  class="hyControlButton detalleRecibosPagoButton" title="Detalle de recibo de caja"/>
					</td>';
				}
				$html .= '<td align="center" class="hyTdLeftControlButton">'.$anularHtml.'</td>
				</tr>';
			}
			$html .= '
				<tr>
					<td align="center" class="hyTdLeftControlButton" colspan="9">
						<input type="button" id="recibosPagoButtonList" alt="'.$id.'" value="Imprimir Lista" class="hyControlButton printButton" >
					</td>
				</tr>
			';
		}else{
			$html .= '
				<tr>
					<td colspan="9">El contrato no tiene recibos de pago</td>
				</tr>
			';
		}
		$html .='
			</table><br><br><br>
		';
		return $html;
	}

	 /**
	 * Metodo que genera un listado de control de pago de un contrato (saldos)
	 *
	 * @param array $config (
	 *  'sociosId' => int
	 * )
	 * @return string $html
	 */
	public static function generaListaControlPagos($config)
	{

		$sociosId = $config['sociosId'];
		$controlPagosObj = EntityManager::get('ControlPagos')->find(array('conditions'=>'socios_id='.$sociosId));

		$html = '
			<table class="hyBrowseTab zebraSt sortable" cellspacing="1" id="saldoTable" cellpadding="0" align="center">';
		
		if(count($controlPagosObj) > 0){
			$html.= '
				<tr>
					<th class="sortcol">Recibo de Caja</th>
					<th class="sortcol">Fecha de pago</th>
					<th class="sortcol">Valor pagado</th>
					<th class="sortcol">Interes Mora</th>
					<th class="sortcol">Interes Corriente</th>
					<th class="sortcol">Capital</th>
					<th class="sortcol">Saldo</th>';

			$flag = false;
			if (!isset($config['hideHistoria'])) {
				$flag = true;
			}

			if ($flag) {
				$html .=  '
					<th class="sortcol">Nota Historia</th>
					<th class="sortcol">Nota Contable</th>';
			}

			$html .= '
					<!--<th class="sortcol">Imprimir</th>-->
				</tr>';

			$nRc 			= 0;
			$totalPagado	= 0;
			$totalMora		= 0;
			$totalInteres	= 0;
			$totalCapital	= 0;
			$totalSaldo		= 0;
			foreach ($controlPagosObj as $controlPagos){
				$html .= '
				<tr>
					<td align="center">'.$controlPagos->getRc().'</td>
					<td align="center">'.$controlPagos->getFechaPago().'</td>
					<td align="right">'.Currency::number($controlPagos->getPagado()).'</td>
					<td align="right">'.Currency::number($controlPagos->getMora()).'</td>
					<td align="right">'.Currency::number($controlPagos->getInteres()).'</td>
					<td align="right">'.Currency::number($controlPagos->getCapital()).'</td>
					<td align="right">'.Currency::number($controlPagos->getSaldo()).'</td>';

				if ($flag) {
					$html .= '					
					<td align="center">'.$controlPagos->getNotaHistoriaId().'</td>
					<td align="center">'.$controlPagos->getNotaContableId().'</td>
					';
				}

				$html .= '
					<!--<td align="center" class="hyTdLeftControlButton">
						<input type="button" id="controlPagosButton" value="" alt="'.$controlPagos->getRc().
						'" class="hyControlButton printButton" >
					</td>-->
				</tr>';

				$nRc++;
				$totalPagado	+= $controlPagos->getPagado();
				$totalMora		+= $controlPagos->getMora();
				$totalInteres	+= $controlPagos->getInteres();
				$totalCapital	+= $controlPagos->getCapital();
				$totalSaldo		= $controlPagos->getSaldo();
			}
			$html .= '
				<tr>
					<td align="center"><b>'.$nRc.'</b></td>
					<td align="center"></td>
					<td align="right"><b>'.Currency::number($totalPagado).'</b></td>
					<td align="right"><b>'.Currency::number($totalMora).'</b></td>
					<td align="right"><b>'.Currency::number($totalInteres).'</b></td>
					<td align="right"><b>'.Currency::number($totalCapital).'</b></td>
					<td align="right"><b>'.Currency::number($totalSaldo).'</b></td>
					<td align="right"></td>
					<td align="right"></td>
				</tr>';
			/*$html .= '
				<tr>
					<td align="center" class="hyTdLeftControlButton" colspan="10">
						<input type="button" id="printButton" value="Imprimir Lista" class="hyControlButton printButton" >
					</td>
				</tr>
			';*/
		}else{
			$html .= '
				<tr>
					<td colspan="9">El contrato no tiene recibos de pago</td>
				</tr>
			';
		}
		$html .='
			</table><br><br><br>
		';
		return $html;
	}

	/**
	 * Metodo que genera un listado de control de pago de un contrato (saldos)
	 *
	 * @param array $config (
	 *  'sociosId' => int
	 * )
	 * @return string $html
	 */
	public static function generaAmortizacion($config){
		$sociosId = $config['sociosId'];
		$amortizacionObj = EntityManager::get('Amortizacion')->find(array('conditions'=>'socios_id='.$sociosId));
		$html = '
		<table  class="hyBrowseTab zebraSt sortable" cellspacing="0" cellpadding="0" align="center" width="100%">';
		if(count($amortizacionObj) > 0){
			$showPagado = '';
			if(isset($config['showPagado']) && $config['showPagado']==true){
				$showPagado = '<th class="sortcol">Pagado</th>';
			}
			$showEstado = '';
			if(isset($config['showEstado']) && $config['showEstado']==true){
				$showEstado = '<th class="sortcol">Estado</th>';
			}
			$html .= '
				<tr>
					<th class="sortcol">Número Cuota</th>
					<th class="sortcol">Fecha Cuota</th>
					<th class="sortcol">Cuota Fija</th>
					<th class="sortcol">Capital</th>
					<th class="sortcol">Interes Corriente</th>
					<th class="sortcol">Saldo</th>
					'.$showPagado.'
					'.$showEstado.'
				</tr>';
				$nRc					= 0;
				$totalCuotaFija			= 0;
				$totalCapital			= 0;
				$totalInteresCorriente	= 0;
				$totalSaldo				= 0;
				foreach($amortizacionObj as $amortizacion){
					$estado = 'Debe';
					if($amortizacion->getEstado()=='P'){
					   $estado = 'Pagado';
					}
					$showPagado = '';
					if(isset($config['showPagado']) && $config['showPagado']==true){
						$showPagado = '<td align="right">'.Currency::number($amortizacion->getPagado()).'</td>';
					}
					$showEstado = '';
					if(isset($config['showEstado']) && $config['showEstado']==true){
						$showEstado = '<td align="center">'.$estado.'</td>';
					}
					$html .= '
						<tr>
							<td align="center">'.$amortizacion->getNumeroCuota().'</td>
							<td align="center">'.$amortizacion->getFechaCuota().'</td>
							<td align="right">'.Currency::number($amortizacion->getValor()).'</td>
							<td align="right">'.Currency::number($amortizacion->getCapital()).'</td>
							<td align="right">'.Currency::number($amortizacion->getInteres()).'</td>
							<td align="right">'.Currency::number($amortizacion->getSaldo()).'</td>
							'.$showPagado.'
							'.$showEstado.'
						</tr>
					';
					$nRc++;
					$totalCuotaFija			+= $amortizacion->getValor();
					$totalCapital			+= $amortizacion->getCapital();
					$totalInteresCorriente	+= $amortizacion->getInteres();
				}
				$showPagado = '';
				if(isset($config['showPagado']) && $config['showPagado']==true){
					$showPagado = '<td align="center"></td>';
				}
				$html .= '
					<tr>
						<td align="center"><b>'.$nRc.'</b></td>
						<td align="center"></td>
						<td align="right"><b>'.Currency::number($totalCuotaFija).'</b></td>
						<td align="right"><b>'.Currency::number($totalCapital).'</b></td>
						<td align="right"><b>'.Currency::number($totalInteresCorriente).'</b></td>
						<td align="right"><b></b></td>
						'.$showPagado.'
						<td align="center"></td>
					</tr>
				';
				if(isset($config['printList']) && $config['printList']==true){
					$html .= '
					<tr>
						<td colspan="7" align="right" class="hyTdLeftControlButton">
						   <input type="button" id="amortizacionButton" value="Imprimir" class="hyControlButton printButton" >
						</td>
					</tr>
					';
				}
			}else{
			$html .= '
				<tr>
					<td colspan="7">No se encontró amortización del contrato</td>
				</tr>';
			}
		$html .= '
			</table><br><br><br>
		'.Tag::hiddenField(array('id','value'=>$sociosId));
		return $html;
	}

	/**
	 * Metodo que genera el html de la historia del contrato seleccionado
	 * 	
	 */
	static function generaListaHistoria($config){
		$html = '
		<table  class="hyBrowseTab zebraSt sortable" cellspacing="0" cellpadding="0" align="center">';
		$sociosId	= 0;
		if(isset($config['sociosId'])==true){
			$sociosId = $config['sociosId'];
		}
		$reservasId = 0;
		if(isset($config['reservasId'])==true){
			$reservasId = $config['reservasId'];
		}
		if($sociosId>0 || $reservasId >0){
			$notaHistoriaObj = EntityManager::get('NotaHistoria')->find(array('conditions'=>'socios_id='.$sociosId.' OR reservas_id='.$reservasId));
			if(count($notaHistoriaObj) > 0){
				$estadosNotaHistoria = Array(
					'C'	=> 'Cambio de Contrato',
					'E'	=> 'Abono Errado',
					'P'	=> 'Abono Posterior',
					'R'	=> 'Refinanciación',
					'A'	=> 'Recibo de Caja Anulado',
					'K'	=> 'Abono a capital',
				);
				$html .= '
				<tr>
					<th class="sortcol">Fecha</th>
					<th class="sortcol">Observaciones</th>
					<th class="sortcol">Estado</th>
				</tr>';
				foreach($notaHistoriaObj as $notaHistoria){
					$estado = '?';
					if(isset($estadosNotaHistoria[$notaHistoria->getEstado()])){
						$estado = $estadosNotaHistoria[$notaHistoria->getEstado()];
					}
					$html.='
						<tr>
							<td align="center">'.$notaHistoria->getFechaNota().'</td>
							<td align="center">'.$notaHistoria->getObservaciones().'</td>
							<td align="center">'.$estado.'</td>
						</tr>
					';
				}
			}else{
				$html.='
				<tr>
					<td colspan="4" align="center">No se encontró notas de historía</td>
				</tr>
				';
			}
		}
		$html.='</table><br><br><br>';
		return $html;
	}

	/**
	 * Metodo que genera el html de el valor a pagar la fecha da por defecto es hoy a un contrato
	 * 
	 * @param $config(
	 * 		'sociosId'		Es el id del contrato
	 * 		'fecha'			Es la fecha limit e de conteo de mora
	 * )
	 */
	static function estadoCuenta($config){
		Core::importFromLibrary('Hfos/Tpc','Tpc.php');
		try{
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = 0;
			if(!isset($config['sociosId'])){
				$transaction->rollback('estadoCuenta: El id del socios es requerido');
			}
			$sociosId = $config['sociosId'];
			$socios = EntityManager::get('Socios')->findFirst($sociosId);
			if($socios==false){
				$transaction->rollback('estadoCuenta: El Contrato no existe');
			}
			TPC::estadoCuenta($config, $transaction);
			//$transaction->rollback(print_r($config['estadoCuenta'], true));
			$html = '<table class="hyBrowseTab zebraSt sortable" cellspacing="0" cellpadding="0" align="center" width="100%">';
			if($sociosId){
				$html .= '
					<tr>
						<th class="sortcol">Total Cuotas</th>
						<th class="sortcol">Debe Pagar</th>
						<th class="sortcol">Capital</th>
						<th class="sortcol">Total días</th>
						<th class="sortcol">Intereses corrientes liquidados</th>
						<th class="sortcol">Intereses corrientes aplicados</th>
						<th class="sortcol">Diferencía</th>
						<th class="sortcol">Días mora</th>
						<th class="sortcol">Interes mora</th>
						<th class="sortcol">Mora no cancelada</th>
						<th class="sortcol">Saldo</th>
					</tr>
					<tr>
						<td align="center">'.$config['estadoCuenta']['cuotasMora'].'</td>
						<td align="right">'.Currency::number($config['estadoCuenta']['debePagar']).'</td>
						<td align="right">'.Currency::number($config['estadoCuenta']['capital']).'</td>
						<td align="center">'.$config['estadoCuenta']['totalDias'].'</td>
						<td align="right">'.Currency::number($config['estadoCuenta']['interecesCorrientesLiquidacion']).'</td>
						<td align="right">'.Currency::number($config['estadoCuenta']['interecesCorrientesAplicados']).'</td>
						<td align="right">'.Currency::number($config['estadoCuenta']['diferencia']).'</td>
						<td align="center">'.$config['estadoCuenta']['diasMora'].'</td>
						<td align="right">'.Currency::number($config['estadoCuenta']['interesesMora']).'</td>
						<td align="right">'.Currency::number($config['estadoCuenta']['MoraNoCancelada']).'</td>
						<td align="right">'.Currency::number($config['estadoCuenta']['saldo']).'</td>
					</tr>
					';
				$html.='
				<tr>
					<th class="sortcol">Observaciones</th>
					<td colspan="10" align="left">'.$config['estadoCuenta']['detallePago'].'</td>
				</tr>';
			}
			$html.='</table>';
			return $html;
		}catch(Exception $e){
			Flash::error('TpcHelper::estadoCuenta: '.$e->getMessage());
		}
	}
};
