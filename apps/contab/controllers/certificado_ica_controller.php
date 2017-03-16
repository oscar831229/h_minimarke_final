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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Certificado_IcaController
 *
 * Certificados de Ica
 *
 */
class Certificado_IcaController extends ApplicationController {

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addDays(1);

		Tag::displayTo('ano', $empresa1->getAnoc());
		Tag::displayTo('fechaExpedicion', Date::getCurrentDate());

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		set_time_limit(0);

		$this->setResponse('json');

		try {

			$ano = $this->getPostParam('ano', 'int');
			$bimestre = $this->getPostParam('bimestre', 'int');

			if ($bimestre<7) {
				list($fechaInicial, $fechaFinal) = Date::getTwoMonths($ano, $bimestre);
			} else {
				$fechaInicial = Date::fromParts($ano, 1, 1);
				$fechaFinal = Date::fromParts($ano, 12, 31);
			}

			$periodo = $ano.'11';

			$fechaExpedicion = $this->getPostParam('fechaExpedicion', 'date');

			$comprobCierre = Settings::get('comprob_cierre');

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'int');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'int');

			if (!$cuentaInicial || !$cuentaFinal) {
				throw new Exception('Debe ingresar el rango de cuentas de IVA retenido');
			}

			if ($cuentaInicial!=''&&$cuentaFinal!='') {
				list($cuentaInicial, $cuentaFinal) = Utils::sortRange($cuentaInicial, $cuentaFinal);
			}

			$nitInicial = $this->getPostParam('nitInicial', 'int');
			$nitFinal = $this->getPostParam('nitFinal', 'int');

			if (!$nitInicial || !$nitFinal) {
				throw new Exception('Debe ingresar el rango de tercero');
			}

			if ($nitInicial!=''&&$nitFinal!='') {
				list($nitInicial, $nitFinal) = Utils::sortRange($nitInicial, $nitFinal);
			}

			$fechaExpedicion = new Date($fechaExpedicion);

			if ($cuentaInicial==''&&$cuentaFinal=='') {
				$cuentas = EntityManager::get("Cuentas")->find("porc_iva>0 AND es_auxiliar='S'");
			} else {
				$cuentas = EntityManager::get("Cuentas")->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S'");
			}

			//CUENTAS ICA
			$cuentasIca = array();
			$icaObj = EntityManager::get("Ica")->find(array('columns'=>'cuenta'));
			foreach ($icaObj as $cuentaIca)
			{
				$codigoCuenta = $cuentaIca->getCuenta();
				$cuentasIca[$codigoCuenta]= $codigoCuenta;
				unset($cuentaIca);
			}
			unset($icaObj);

			$bases = array();
			$baseICA = array();
			$listaNits = array();
			$retenciones = array();

			//ICA
			$nitsObj = EntityManager::get("Nits")->find(array('conditions'=>"nit>='$nitInicial' AND nit<='$nitFinal'", 'columns'=>'nit,ap_aereo,nombre', 'order' => 'nit ASC'));
			foreach ($nitsObj as $tercero)
			{
				$numeroNit = trim($tercero->getNit());

				$baseICA[$numeroNit] = array();

				$totalIca = 0;
				$conditionMoviICA = "nit='$numeroNit' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal' AND cuenta IN(".implode(",",array_values($cuentasIca)).")";
				//throw new Exception(print_r($cuentasIca,true));

				$baseICA[$numeroNit]['valor'] = 0;

				$moviObj = EntityManager::get("Movi")->find(array('conditions'=>$conditionMoviICA, 'columns' => 'valor,deb_cre'));
				foreach ($moviObj as $movi)
				{
					if($movi->getDebCre()=='D'){
						$baseICA[$numeroNit]['valor'] -= $movi->getValor();
					}else{
						$baseICA[$numeroNit]['valor'] += $movi->getValor();
					}
					unset($movi);
				}
				unset($moviObj);

				$baseICA[$numeroNit]['porcIca'] = $tercero->getApAereo();

				if ($tercero->getApAereo()>0) {
					$baseICA[$numeroNit]['base'] = $baseICA[$numeroNit]['valor']/$tercero->getApAereo()*1000;
				}

				//RETENCIONES
				$retenciones[$numeroNit]['IVA'] = array(
					'valor' 	=> 0,
					'porcIva' 	=> 0,
					'base' 		=> 0
				);
				$retenciones[$numeroNit]['ICA'] = $baseICA[$numeroNit];

				//LISTA NITS
				$listaNits[$numeroNit] = array(
					'nit' => $numeroNit,
					'razonSocial' => $tercero->getNombre(),
					'sucursal' => 01
				);

				unset($tercero,$numeroNit);
			}
			unset($nitsObj,$baseICA);

			//IVA
			foreach ($cuentas as $cuenta)
			{
				$codigoCuenta = $cuenta->getCuenta();
				$porcIva = $cuenta->getPorcIva();

				if (isset($cuentasIca[$codigoCuenta])) {
					continue;
				}

				if ($nitInicial&&$nitFinal) {
					$saldosns = EntityManager::get("Saldosn")->find("nit>='$nitInicial' AND nit<='$nitFinal' AND cuenta='$codigoCuenta' AND ano_mes=0");
				} else {
					$saldosns = EntityManager::get("Saldosn")->find("cuenta='$codigoCuenta' AND ano_mes=0");
				}

				foreach ($saldosns as $saldosn)
				{
					$numeroNit = trim($saldosn->getNit());

					if (!isset($bases[$numeroNit][$codigoCuenta])) {
						$bases[$numeroNit][$codigoCuenta] = array(
							'porcIva' => $porcIva,
							'valor' => 0,
							'base' => 0,
							'cuenta' => $cuenta
						);
					}

					if (isset($bases[$numeroNit][$codigoCuenta])) {
						$conditions = "comprob!='$comprobCierre' AND cuenta='$codigoCuenta' AND nit='$numeroNit' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
						$moviObj = EntityManager::get("Movi")->find(array('conditions' => $conditions, 'columns' => 'valor,base_grab,deb_cre'));
						foreach ($moviObj as $movi)
						{
							$bases[$numeroNit][$codigoCuenta]['base']+=$movi->getBaseGrab();

							if ($movi->getDebCre()=='D') {
								//DEBITOS
								$bases[$numeroNit][$codigoCuenta]['valor']-=$movi->getValor();
							} else {
								//CREDITOS
								$bases[$numeroNit][$codigoCuenta]['valor']+=$movi->getValor();
							}
							unset($movi);
						}
						unset($conditions, $moviObj);
					}

					if (!isset($listaNits[$numeroNit])) {

						//LISTA NITS
						$tercero = BackCacher::getTercero($numeroNit);
						if ($tercero!=false) {
							$razonSocial = $tercero->getNombre();
						} else {
							$razonSocial = 'No existe en terceros!';
						}
						$listaNits[$numeroNit] = array(
							'nit' => $numeroNit,
							'razonSocial' => $razonSocial,
							'sucursal' => 01
						);
						unset($tercero);
					}

					unset($saldosn,$saldon,$numeroNit);
				}
				unset($cuenta,$saldosns);
			}
			unset($cuentas);

			foreach ($bases as $nit => $cuentaBases)
			{
				$nit = trim($nit);

				foreach ($cuentaBases as $codigoCuentaM => $base)
				{

					if (!isset($retenciones[$nit])) {
						$retenciones[$nit] = array();
					}

					if (!isset($retenciones[$nit]['IVA'])) {
						$retenciones[$nit]['IVA'] = array(
							'valor' => 0,
							'base' => 0,
							'porcIva' => 0,
						);
					}

					//SE VA A IVA
					$retenciones[$nit]['IVA']['base'] += $base['base'];
					$retenciones[$nit]['IVA']['valor'] += $base['valor'];
					$retenciones[$nit]['IVA']['porcIva'] += $base['porcIva'];

					unset($codigoCuentaM, $base);
				}
				unset($cuentaBases, $nit);
			}

			unset($bases);

			ksort($retenciones, SORT_NUMERIC);
			//throw new Exception(print_r($retenciones,true));

			if (!count($retenciones)) {
				throw new Exception('No se encontró movimientos');
			}

			require 'Library/Mpdf/mpdf.php';
			$pdf = new mPDF();
			$pdf->SetDisplayMode('fullpage');
			$pdf->ignore_invalid_utf8 = true;
			$pdf->tMargin = 10;
			$pdf->lMargin = 10;

			$empresa = EntityManager::get("Empresa")->findFirst();
			$empresaNit = EntityManager::get("Nits")->findFirst("nit='{$empresa->getNit()}'");
			if ($empresaNit==false) {
				throw new Exception('Debe crear el hotel como una tercero del sistema antes de continuar');
			}

			//$logoUrl = Utils::getExternalUrl('img/backoffice/logo.png');
			$logoUrl = 'public/img/backoffice/logo.png';

			$html = '<html>
				<head>
					<style type="text/css">'.file_get_contents('public/css/hfos/certificado.css').'</style>
				</head>
			<body>';

			$bimestres = array(
				'1' => 'PRIMER',
				'2' => 'SEGUNDO',
				'3' => 'TERCER',
				'4' => 'CUARTO',
				'5' => 'QUINTO',
				'6' => 'SEXTO',
				'9' => 'ACUMULADO DEL AÑO'
			);

			$bimestreName = $bimestres[$bimestre];
			$nPage = 0;
			$limitPages = count($retenciones);

			foreach ($retenciones as $nit => $cuentaBases)
			{
				$nit = trim($nit);
				$nPage++;
				$tercero = BackCacher::getTercero($nit);
				$totalRetencion = 0;

				if (isset($retenciones[$nit])) {
					//IVA
					$baseIVAF = $retenciones[$nit]['IVA'];
					$baseICAF = $retenciones[$nit]['ICA'];

					if ($baseIVAF['valor']<=0 && $baseICAF['valor']<=0) {
						unset($listaNits[$nit], $retenciones[$nit]);
						continue;
					}

					$valorBaseIva = $baseIVAF['base'];

					//TOTAL
					$totalRetencion = (double) $baseICAF['valor'] + $baseIVAF['valor'];
					$currency = new Currency();

					$porcIva = 0;
					if (abs($baseIVAF['valor']) && abs($valorBaseIva)) {
						$porcIva = abs($baseIVAF['valor'])/abs($valorBaseIva)*100;
						$porcIva = Currency::number($porcIva,2);
					}

					$html.='
					<div class="page">
						<div class="header">
							<table>
								<tr>
									<td><img src="'.$logoUrl.'" width="80"/></td>
									<td>
										<h1>'.$empresa->getNombre().'</h1>
										<h2>'.$empresaNit->getNit().'</h2>
										<h3>'.$empresaNit->getDireccion().'</h3>
									</td>
								</tr>
							</table>
						</div>
						<div class="content">
							<h3>CERTIFICADO DE RETENCION DEL ICA</h3>
							<div class="paragraph">
								CERTIFICAMOS QUE DURANTE EL PERIODO FISCAL DEL '.$bimestreName.' BIMESTRE DE '.$ano.'
								LE EFECTUARON PAGOS A: '.$tercero->getNombre().' CON NIT # '.$tercero->getNit().'
								SOMETIDOS A RETENCION EN EL IMPUESTO SOBRE LAS VENTAS:
							</div>
							<table width="100%" class="conceptos" cellspacing="0">
								<tr>
									<th>CONCEPTO</th>
									<th>VALOR RETENIDO</th>
									<th>%</th>
									<th>BASE</th>
								</tr>
								<tr>
									<td>RETENCIÓN DE IVA</td>
									<td align="right">'.Currency::number(abs($baseIVAF['valor'])).'</td>
									<td align="right">'.$porcIva.'</td>
									<td align="right">'.Currency::number(abs($valorBaseIva)).'</td>
								</tr>
								<tr>
									<td>RETENCIÓN DE ICA</td>
									<td align="right">'.Currency::number(abs($baseICAF['valor'])).'</td>
									<td align="right">'.$baseICAF['porcIca'].'</td>
									<td align="right">'.Currency::number(abs($baseICAF['base'])).'</td>
								</tr>
								<tr>
									<td align="right">TOTAL RETENCIÓN</td>
									<td align="right">'.Currency::number(abs($totalRetencion)).'</td>
									<td align="right" colspan="2">&nbsp;</td>
								</tr>
							</table>
							<div class="paragraph">Son: '.$currency->getMoneyAsText($totalRetencion).'</div>
							<div class="paragraph">ESTOS VALORES FUERON CONSIGNADOS OPORTUNAMENTE EN LA DIRECCIÓN DE IMPUESTOS NACIONALES (DIAN) DE LA CIUDAD DE '.$empresaNit->getCiudadNombre().'</div>
							<br/>
							<div class="paragraph">Ciudad Donde se Practicó la Retención: '.$empresaNit->getCiudadNombre().'</div>
							<div class="paragraph">Fecha Expedición: '.$fechaExpedicion->getLocaleDate('long').'</div>
							<div class="paragraph">NO REQUIERE FIRMA AUTOGRAFA SEGUN DR 836/91 ART. 10</div>
						</div>
					</div>';
					if (count($retenciones)>1 && $nPage!=$limitPages) {
						$html.='<pagebreak />';
					}

					unset($baseICAF, $baseIVAF);
				}

			}

			unset($retenciones);

			$html.='<pagebreak />';

			//LISTA NITS HTML
			$html.='<table width="100%" class="conceptos" cellspacing="0">
				<tr>
					<th>NIT</th>
					<th>RAZÓN SOCIAL</th>
					<th>SUCURSAL</th>
				</tr>';

			ksort($listaNits, SORT_NUMERIC);

			foreach ($listaNits as $info)
			{
				$html.='<tr>
					<td align="left">'.$info['nit'].'</td>
					<td align="left">'.$info['razonSocial'].'</td>
					<td align="center">'.$info['sucursal'].'</td>
				</tr>';
			}

			$html.='</table>';

			//FIN REPORTE
			$html.='</body></html>';

			if (count($listaNits)>0) {

				$pdf->writeHTML($html);
				$fileName = 'certificados.'.mt_rand(1, 9999).'.pdf';
				$pdf->Output('public/temp/'.$fileName);

				return array(
					'status' => 'OK',
					'file' => 'temp/'.$fileName
				);
			} else {
				throw new Exception('No se encontró movimiento');
			}
		}
		catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}
}