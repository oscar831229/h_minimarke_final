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

Core::importFromLibrary('Hfos/Tpc','Tpc.php');
Core::importFromLibrary('Hfos/Tpc','TpcHelper.php');
/**
 * TPC
 *
 * Clase componente que controla procesos que genera los informes de tpc
 *
 */
class TpcInformes extends UserComponent {

	/**
	 * Metodo que genera el informe de propietarios 
	 * 
	 * @param $config => array(
	 * 	'fechaCompraIni'	=> date,
	 * 	'fechaCompraFin'	=> date,
	 * 	'estadoContrato'	=> char,
	 * 	'estadoMovimiento'	=> char,
	 * 	'porcentajeSigno'	=> string,
	 * 	'porcentajeValor'	=> int,
	 * 	'ordenField'		=> string,
	 * 	'sortField'			=> string,
	 * 	'reportType'		=> string (html, pdf, excel, texto)
	 * )
	 */
	static function propietarios(&$config, $transaction){
		$report		= ReportBase::factory($config['reportType']);
		$i=1;
		$entities	= array('Socios', 'MembresiasSocios', 'PagoSaldo', 'DetalleCuota','DerechoAfiliacion');
		$headers	= array();
		$wheres		= array('1=1');
		//TITULO PRINCIPAL
		$headers[]= new ReportText('INFORME DE PROPIETARIOS', array(
			'fontSize' => 16,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		//Sub titulos segun criterios de búsqueda
		//Fecha de compra
		if(isset($config['fechaCompraIni'])==true && empty($config['fechaCompraIni'])==false
			&& isset($config['fechaCompraFin'])==true && empty($config['fechaCompraFin'])==false
		){
			$headers[]= new ReportText('Fecha de Compra entre: '.$config['fechaCompraIni'].' a '.$config['fechaCompraFin'], array(
				'fontSize' => 13,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));
			$wheres[]="{#Socios}.fecha_compra >= '{$config['fechaCompraIni']}' AND {#Socios}.fecha_compra <= '{$config['fechaCompraFin']}'";
		}
		//Estados de Contrato
		if(isset($config['estadoContrato'])==true && empty($config['estadoContrato'])==false && $config['estadoContrato']!='@'){
			$textoEstados = 'Estado de Contrato: '.$config['estadoContrato'];
			$wheres[]="{#Socios}.estado_contrato = '{$config['estadoContrato']}'";
			if(isset($config['estadoMovimiento'])==true && empty($config['estadoMovimiento'])==false && $config['estadoMovimiento']!='@'){
				$textoEstados .= ' y Estado de Movimiento: '.$config['estadoMovimiento'];
				$wheres[]="{#Socios}.estado_movimiento = '{$config['estadoMovimiento']}'";
			}
			$headers[]= new ReportText($textoEstados, array(
				'fontSize' => 13,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));
		}
		$ordenField = array();
		//Porcentaje de Saldo
		if(isset($config['porcentajeValor'])==true && empty($config['porcentajeValor'])==false && $config['porcentajeValor']!='@'){
			$signo = '=';
			if(isset($config['porcentajeSigno'])==true && empty($config['porcentajeSigno'])==false && $config['porcentajeSigno']!='@'){
				$signo = $config['porcentajeSigno'];
			}
			$textoEstados = 'Saldo '.$signo.' '.$config['porcentajeValor'].'%';
			$headers[]= new ReportText($textoEstados, array(
				'fontSize' => 13,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));
			//Condiciones de JOIN para saber el % de saldo
			$wheres[]=' ({#MembresiasSocios}.saldo_pagar*'.$config['porcentajeValor'].'/100)'.$signo.'(SELECT saldo FROM control_pagos WHERE socios_id={#Socios}.id ORDER BY  fecha_pago DESC LIMIT 1) ';
		}
		$configJoin = array(
			'entities'		=> $entities,
			'fields'		=> array('{#Socios}.id','{#Socios}.numero_contrato','{#Socios}.fecha_compra','{#Socios}.nombres','{#Socios}.apellidos','{#Socios}.identificacion','{#Socios}.estado_contrato','{#Socios}.estado_movimiento','{#DetalleCuota}.cuota2','{#DetalleCuota}.cuota3','{#MembresiasSocios}.valor_total','{#MembresiasSocios}.cuota_inicial','{#MembresiasSocios}.saldo_pagar','{#PagoSaldo}.numero_cuotas','(SELECT saldo FROM control_pagos WHERE socios_id={#Socios}.id ORDER BY  fecha_pago DESC LIMIT 1) as saldo','(SELECT cuota_saldo FROM recibos_pagos WHERE socios_id={#Socios}.id ORDER BY  fecha_pago DESC LIMIT 1) as cuota_saldo','{#MembresiasSocios}.puntos_ano', '(select valor from derecho_afiliacion where id={#MembresiasSocios}.derecho_afiliacion_id) as derecho_afiliacion_valor'),
			'conditions'	=> implode(' AND ', $wheres).' GROUP BY socios.id',
			'groupFields'	=> array('socios.id')
		);
		//Orden de campos
		$sortFieldS	= 'ASC';
		$ordenFieldS = '{#Socios}.numero_contrato ASC';
		if(isset($config['ordenField'])==true && empty($config['ordenField'])==false && $config['ordenField']!='@'){
			if(isset($config['sortField'])==true && empty($config['sortField'])==false){
				$sortFieldS = $config['sortField'];
			}
			$ordenFieldS = '{#Socios}.'.$config['ordenField'].' '.$sortFieldS;
		}
		$ordenField[]=$ordenFieldS;
		//agregamos el order de todos 
		$configJoin['order']= implode(', ',$ordenField);
		//Consultamos según criterios de búsqueda
		//$transaction->rollback(print_r($configJoin, true).'<br><br>'.print_r($config,true));
		$query = new ActiveRecordJoin($configJoin);
		//$transaction->rollback(print_r($query->getResultSet(),true));
		if(count($query->getResultSet())<=0){
			$transaction->rollback('No se encontraron registros');
		}
		$report->setHeader($headers);
		$report->setDocumentTitle('Informe de Propietarios');
		$report->setColumnHeaders(array(
			'NUM.',
			'NÚMERO DE CONTRATO',
			'NOMBRE AFILIADO 1',
			'CÉDULA AFILIADO 1',
			'NOMBRE AFILIADO 2',
			'CÉDULA AFILIADO 2',
			'FECHA DE COMPRA',
			'VALOR TOTAL COMPRA',
			'CUOTAS INICALES',
			'NÚMERO CUOTAS',
			'VALOR FINANCIACIÓN',
			'CUOTAS FINANCIACIÓN',
			'SALDO ACTUAL',
			'CUOTA ACTUAL',
			'DERECHO DE AFILIACIÓN',
			'PUNTOS POR AÑO',
			'ESTADO CONTRATO',
			'ESTADO MOVIMIENTO'
		));
		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));
		$report->setColumnStyle(array(0,1,2,3,4,5,6,9,11,13,15,16,17), new ReportStyle(array(
			'textAlign' => 'center',
			'fontSize' => 11
		)));
		$report->setColumnStyle(array(7, 8, 10, 12, 14), new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
		)));
		$report->setTotalizeColumns(array(7, 8, 10, 12, 14));
		$report->start(true);
		$empresa = self::getModel('Empresa')->findFirst();
		$totalCompra = $totalFinanciacion = $totalSaldo = $totalCuotaIni = $totalDerechoAfiliacion = 0;
		//Buscamos los tipos de estados
		$estadoContratoArray = array();
		foreach(EntityManager::get('EstadoContrato')->find() as $estado){
			$estadoContratoArray[$estado->getCodigo()]=$estado->getNombre();
		}
		$estadoMovimientoArray = array();
		foreach(EntityManager::get('EstadoMovimiento')->find() as $estado){
			$estadoMovimientoArray[$estado->getCodigo()]=$estado->getNombre();
		}
		foreach($query->getResultSet() as $result){
			/*if(method_exists($result, 'getNumeroContrato')==false){
				continue;
			}*/
			//Obtenemos el conyugue del contrato
			$conyugues = EntityManager::get('Conyuges')->findFirst(array('conditions'=>'socios_id='.$result->getId()));
			if($conyugues==false){
				$conyugues = EntityManager::get('Conyuges',true);
			}
			if(!$conyugues->getNombres()){
				$conyugues->setNombres('--');
			}
			if(!$conyugues->getIdentificacion()){
				$conyugues->setIdentificacion('--');
			}
			//Obtenemos el numero de cuotas iniciales que pidio en el contrato
			$numCuotasiniciales = 1;
			if($result->getCuota2()>0){$numCuotasiniciales++;}
			if($result->getCuota3()>0){$numCuotasiniciales++;}
			//Sacamos el detalle del estado 
			$estadoContratoLabel = '?';
			if(isset($estadoContratoArray[$result->getEstadoContrato()])==true){
				$estadoContratoLabel = $estadoContratoArray[$result->getEstadoContrato()];
			}
			$estadoMovimientoLabel = '?';
			if(isset($estadoMovimientoArray[$result->getEstadoMovimiento()])==true){
				$estadoMovimientoLabel = $estadoMovimientoArray[$result->getEstadoMovimiento()];
			}
			/*//Buscamos información de saldo actual
			$Socio = EntityManager::get('Socios')->findFirst($result->getId());
			if($Socio==false){
				$transaction->rollback('El id del contrato no existe');
			}
			$dataSaldo = array('SocioId'=>$Socio->getId());
			TPC::buscarRangoPagoEnAmortizacion($Socio,0,$dataSaldo, $transaction);
			$transaction->rollback(print_r($dataSaldo,true));*/
			//Si no tiene nada queire decir que no ha hecho ningun pago por tanto en vez de cero es el saldo a pagar
			if(!$result->getSaldo()){
				$result->saldo = $result->getSaldoPagar();
			}
			if(!$result->getCuotaSaldo()){
				$result->cuota_saldo = 1;
			}
			//Add new row
			$report->addRow(array(
				$i,
				$result->getNumeroContrato(),
				$result->getNombres().' '.$result->getApellidos(),
				$result->getIdentificacion(),
				$conyugues->getNombres().' '.$conyugues->getApellidos(),
				$conyugues->getIdentificacion(),
				$result->getFechaCompra(),
				Currency::number($result->getValorTotal()),
				Currency::number($result->getCuotaInicial()),
				$numCuotasiniciales,
				Currency::number($result->getSaldoPagar()),
				$result->getNumeroCuotas(),
				Currency::number($result->getSaldo()),
				$result->getCuotaSaldo(),
				Currency::number($result->getDerechoAfiliacionValor()),
				$result->getPuntosAno(),
				$estadoContratoLabel,
				$estadoMovimientoLabel
			));
			$i++;
			//Sumatorias
			$totalCompra+=$result->getValorTotal();
			$totalCuotaIni+=$result->getCuotaInicial();
			$totalSaldo+=$result->getSaldo();
			$totalFinanciacion+=$result->getSaldoPagar();
			$totalDerechoAfiliacion+=$result->getDerechoAfiliacionValor();
		}
		$report->setTotalizeValues(array(
			7	=> Currency::number($totalCompra),
			8	=> Currency::number($totalCuotaIni),
			10	=> Currency::number($totalFinanciacion),
			12	=> Currency::number($totalSaldo),
			14  => Currency::number($totalDerechoAfiliacion)
		));
		$report->finish();
		$config['file']= $report->outputToFile('public/temp/propietarios');
	}


	//////////////////////////////////////////////////////////////////////
	// CUENTAS DE COBRO
	//////////////////////////////////////////////////////////////////////

	/**
	 * Metodo que genera el informe de cuentas de cobro
	 * 
	 * @param $config 	=> array(
	 * 	'sociosId'		=> int,
	 * 	'periodoIni'	=> int,
	 * 	'periodoFin'	=> int,
	 * 	'tipoContrato'	=> int,
	 * 	'reportType'	=> string (html, pdf, excel, texto)
	 * )
	 */
	static function cuentaCobro(&$config){

		#Validación
		$listaValidar = array(
			array('name' => 'periodoIni', 'message' => 'Es necesario indicar periodo inicial al generar cuenta de cobro'),
			array('name' => 'periodoFin', 'message' => 'Es necesario indicar periodo final al generar cuenta de cobro'),
			array('name' => 'reportType', 'message' => 'Es necesario indicar el tipo de salida de la cuenta de cobro (Html o Pdf)'),
		);

		$html = '';

		$reportType = $config['reportType'];

		#periodos
		$periodoIni = EntityManager::get('Periodo')->findFirst($config['periodoIni'])->getPeriodo();
		$periodoFin = EntityManager::get('Periodo')->findFirst($config['periodoFin'])->getPeriodo();

		#Condiciones de busqueda
		$conditions = "periodo>=$periodoIni AND periodo<=$periodoFin";
		if (isset($config['sociosId']) && $config['sociosId']) {
			$conditions .= " AND sociosId={$config['sociosId']}";
		}

		#Html base
		$html = '
		<html>
			<head>
				<title>Cuenta de Cobro</title>
				'.Tag::stylesheetLink('hfos/recibo').'
			</head>
			<body>';
		

		#buscamos los socios existentes
		$cuentaCobroObj = EntityManager::get('CuentaCobro')->find(array('conditions'=>$conditions));
		foreach ($cuentaCobroObj as $cuentaCobro) 
		{
			
			#buscamos info de socio
			$socios = EntityManager::get('Socios')->findFirst($cuentaCobro->getSociosId());
			if ($socios==false) {
				throw new Exception("El socios con id '{$cuentaCobro->getSociosId()}' no existe");				
			}

			#filtro de tipo de contrato
			if (isset($config['tipoContrato']) && $config['tipoContrato']>0 && $config['tipoContrato']!=$socios->getTipoContratoId()) {
				continue;
			}

			#creamos html de cuenta de cobro del socio
			$tpcInformes = new TpcInformes();
			$html .= $tpcInformes->_makeCuentaCobro($socios, $cuentaCobro);
			
			unset($socios, $cuentaCobro);
		}

		$html .= '</body>
		</html>';

		if ($config['reportType']=='pdf') {
			#creamos pdf
			Core::importFromLibrary('Mpdf','mpdf.php');
		}

		unset($sociosObj);

		#Creación de archivo que contendrá el reporte
		$dir = 'public/temp/';
		$rand = mt_rand(10000,10000);

		$fileName = 'cuentaCobro.html';
		if($reportType=='html'){
			$fileName = 'cuentaCobro-'.$rand.'.html';
			file_put_contents($dir.$fileName,$html);
			$urlFile = 'temp/'.$fileName;
		}else{
			$fileName = 'cuentaCobro-'.$rand.'.pdf';
			Core::importFromLibrary('Mpdf','mpdf.php');
			$mpdf = new mPDF();
			$mpdf->showImageErrors = true;
			$mpdf->WriteHTML($html);
			$mpdf->Output($dir.$fileName);
			$urlFile = 'temp/'.$fileName;
		}

		$config['file'] = $urlFile;

	}

	private function _makeCuentaCobro($socios, $cuentaCobro) 
	{
		$html = '
				<div align="center">
					<div class="page">						
						<table width="100%" align="left" cellspacing="0" style="border: 1px solid #C0C0C0" class="datos-c">
							<tr>
								<td width="100%" align="center" valign="middle" >
									<table width="100%" cellspacing="0">
										<tr>
											<td align="center" valign="middle" width="200" class="small">
												'.Tag::image(array('tpc/logo.png','style'=>'width:125px; height:52px;')).'
											</td>
											<td align="center" valign="middle" colspan="2" class="small">
												<b>
													<div class="datos" align="center">
														<div class="title">
														CUENTA DE COBRO No. 
														<span style="color:red;">'.$cuentaCobro->getConsecutivo().'</span>
														</div>
														Proyecto e Inversiones TPC S.A<br/>
														NIT: 900.141.821-1
													</div>
												</b>
											</td>
										</tr>
										<tr>
											<td width="20%" bgcolor="#007634"></td>
											<td width="30%" bgcolor="#5BB041"></td>
											<td width="40%" bgcolor="#9BD54D"></td>
										</tr>
									</table>
									<br/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<div>
										<b>Contrato No.:</b>
										'.$socios->getNumeroContrato().'
									</div>
									<div>
										<b>Identificación:</b>
										'.$socios->getIdentificacion().'
									</div>
									<div>
										<b>Nombres y Apellidos:</b>
										'.$socios->getNombres().' '.$socios->getApellidos().'
									</div>
								</td>
							</tr>
							<tr>
								<td width="100%" align="center" valign="middle" >
									<br/>
									<table width="100%" align="center" cellspacing="0">
										<tr>
											<td valign="top">
												<div class="datos-c">
													<b>Fecha de Corte</b><br>
													'.$cuentaCobro->getFechaCorte().'
												</div>
											</td>
											<td valign="top">
												<div class="datos-c">
													<b>Fecha Oportuna de Pago</b><br>
													'.$cuentaCobro->getFechaLimitePago().'
												</div>
											</td>
											<td valign="top">
												<b>Pago Mínimo:</b><br>
												$&nbsp;<span align="right" class="total">'.Currency::number($cuentaCobro->getPagoMinimo()).'</span>
											</td>
											<td valign="top" >
												<div class="datos-c">
													<b>Pago Total</b><br>
													$&nbsp;<span align="right" class="total">'.Currency::number($cuentaCobro->getPagoTotal()).'</span>
												</div>
											</td>
										</tr>
									</table>
									<br/>
									<table width="100%" align="center" cellspacing="0" cellpadding="5">										
										<tr>
											<td valign="top" colspan="4" width="100%">
												<div class="concepto">
													<b>Estado de Cuenta:</b>
												</div>
											</td>
										</tr>
										<tr>
											<td align="left">
												<b>Derecho Afiliación</b><br/>
												$&nbsp;'.Currency::number($cuentaCobro->getValorDerechoAfiliacion()).'
											</td>
											<td align="left">
												<b>Cuotas Iniciales</b><br/>
												$&nbsp;'.Currency::number($cuentaCobro->getValorCuotaInicial()).'
											</td>
											<td align="left">
												<b>Valor Financiación</b><br/>
												$&nbsp;'.Currency::number($cuentaCobro->getValorCuotaFinanciacion()).'
											</td>
											<td align="left">
												<b>Base Interes Corrientes</b><br/>
												'.$cuentaCobro->getBaseCorriente().'
											</td>
										</tr>

										<tr>
											<td align="left">
												<b>Saldo Derecho Afiliación</b><br/>
												$&nbsp;'.Currency::number($cuentaCobro->getSaldoDerechoAfiliacion()).'
											</td>
											<td align="left">
												<b>Saldo Cuotas Iniciales</b><br/>
												$&nbsp;'.Currency::number($cuentaCobro->getSaldoCuotaInicial()).'
											</td>
											<td align="left">
												<b>Saldo Valor Financiación</b><br/>
												$&nbsp;'.Currency::number($cuentaCobro->getSaldoCuotaFinanciacion()).'
											</td>
											<td align="left">
												<b>Base Interes Mora</b><br/>
												'.$cuentaCobro->getBaseMora().'
											</td>
										</tr>
										
										<tr>
											<td valign="top" colspan="4" width="100%">
												<div class="concepto">
													<b>Estado de Cuenta:</b>
												</div>
											</td>
										</tr>

										<tr>
											<td valign="top" colspan="4" width="100%" class="saldoTable">
												'.TpcHelper::generaListaControlPagos(array('sociosId'=>$cuentaCobro->getSociosId(), 'hideHistoria' => true)).'
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</div>
			';

		return $html;
	}
}
