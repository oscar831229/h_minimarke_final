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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

/**
 * Certificado_RetencionController
 *
 * Certificados de Retención
 *
 */
class Certificado_RetencionController extends ApplicationController {

	public function initialize()
    {
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
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

	/**
	 * Metodo que genera un certificado de retencion
	 */
	public function generarAction()
    {

		$this->setResponse('json');

		$listaNits = array();

		$ano = $this->getPostParam('ano', 'int');

		$fechaInicial = Date::fromParts($ano, 1, 1);
		$fechaFinal = Date::fromParts($ano, 12, 31);

		$periodo = $ano.'11';

		$fechaExpedicion = $this->getPostParam('fechaExpedicion', 'date');

		$comprobCierre = Settings::get('comprob_cierre');

		$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
		$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

		$nitInicial = $this->getPostParam('nitInicial', 'terceros');
		$nitFinal = $this->getPostParam('nitFinal', 'terceros');

		if ($nitInicial!=''&&$nitFinal!='') {
			list($nitInicial, $nitFinal) = Utils::sortRange($nitInicial, $nitFinal);
		}

		$fechaExpedicion = new Date($fechaExpedicion);

		if (empty($cuentaInicial) || empty($cuentaFinal)) {
			$cuentas = $this->Cuentas->find("pide_base='S'");
		} else {
			$cuentas = $this->Cuentas->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND pide_base='S'");
		}

		$bases = array();
		foreach ($cuentas as $cuenta) {

			$codigoCuenta = $cuenta->getCuenta();
			if (!empty($nitInicial) && !empty($nitFinal)) {
				$nits = $this->Saldosn->distinct(array("nit", "conditions" => "nit>='$nitInicial' AND nit<='$nitFinal' AND cuenta='$codigoCuenta'"));
			} else {
				$nits = $this->Saldosn->distinct(array("nit", "conditions" => "cuenta='$codigoCuenta'"));
			}

			foreach ($nits as $numeroNit) {

                $numeroNit = trim($numeroNit);

				if (!isset($bases[$numeroNit][$codigoCuenta])) {
					$bases[$numeroNit][$codigoCuenta] = array(
						'porcIva' => $cuenta->getPorcIva(),
						'valor' => 0,
						'base' => 0
					);
				}

				#$conditions = "comprob!='$comprobCierre' AND cuenta='$codigoCuenta' AND nit='$numeroNit' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
                $conditions = "comprob!='$comprobCierre' AND cuenta='$codigoCuenta' AND nit='$numeroNit' AND YEAR(fecha)='$ano'";
                foreach ($this->Movi->find(array($conditions, 'columns' => 'valor,base_grab,deb_cre')) as $movi) {
					$bases[$numeroNit][$codigoCuenta]['base'] += $movi->getBaseGrab();
					if ($movi->getDebCre()=='D') {
						$bases[$numeroNit][$codigoCuenta]['valor'] -= $movi->getValor();
					} else {
						$bases[$numeroNit][$codigoCuenta]['valor'] += $movi->getValor();
					}
				}

				unset($conditions);
				unset($saldon);
			}
			unset($cuenta);
		}

        require 'Library/Mpdf/mpdf.php';
		$pdf = new mPDF();
		$pdf->SetDisplayMode('fullpage');
		$pdf->tMargin = 10;
		$pdf->lMargin = 10;
		$pdf->ignore_invalid_utf8 = true;

		$empresa = $this->Empresa->findFirst();
		$empresaNit = $this->Nits->findFirst("nit='{$empresa->getNit()}'");
		if ($empresaNit==false) {
			return array(
				'status' => 'FAILED',
				'message' => 'Debe crear el hotel como una tercero del sistema antes de continuar'
			);
		}

		//$logoUrl = Utils::getExternalUrl('img/backoffice/logo.png');
		$logoUrl = 'public/img/backoffice/logo.png';

		$html = '<html>
			<head>
				<style type="text/css">'.file_get_contents('public/css/hfos/certificado.css').'</style>
			</head>
		<body>';
		foreach ($bases as $nit => $cuentaBases) {
			$tercero = BackCacher::getTercero($nit);
			if ($tercero==false) {
				$tercero = new Nits();
				$tercero->setNit('0');
				$tercero->setNombre('NO EXISTE TERCERO');
			}
			$html2 ='
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
					<h3>CERTIFICADO DE RETENCION EN LA FUENTE</h3>
					<div class="paragraph">
						PARA DAR CUMPLIMIENTO A LAS DISPOSICIONES VIGENTES SOBRE RETENCION EN LA FUENTE, CERTIFICAMOS QUE DURANTE EL AÑO
						GRAVABLE DE '.$ano.' SE LE RETUVO A: '.$tercero->getNombre().' CON NIT # '.$tercero->getNit().' POR LOS SIGUIENTES CONCEPTOS:
					</div>
					<table width="100%" class="conceptos" cellspacing="0">
						<tr>
							<th>CONCEPTO</th>
							<th>BASE</th>
							<th>VALOR</th>
						</tr>';
						$totalRetencion = 0;
						foreach ($cuentaBases as $codigoCuenta => $valor) {
							if ($valor['valor']>0) {
								$cuenta = BackCacher::getCuenta($codigoCuenta);
								if ($valor['porcIva']>0) {
									$base = $valor['valor']/$valor['porcIva'];
								} else {
									$base = 0;
								}
								$html2.='<tr>
									<td>'.$cuenta->getNombre().'</td>
									<td align="right">'.Currency::number($base).'</td>
									<td align="right">'.Currency::number($valor['valor']).'</td>
								</tr>';
								$totalRetencion += $valor['valor'];
							}
						}
						$html2.='<tr>
							<td align="right" colspan="2">TOTAL RETENCIÓN</td>
							<td align="right">'.Currency::number($totalRetencion).'</td>
						</tr>';
					$currency = new Currency();
					$html2.='</table>
					<div class="paragraph">Son: '.$currency->getMoneyAsText($totalRetencion).'</div>
					<div class="paragraph">ESTOS VALORES FUERON CONSIGNADOS OPORTUNAMENTE EN LA DIRECCIÓN DE IMPUESTOS NACIONALES (DIAN) DE LA CIUDAD DE '.$empresaNit->getCiudadNombre().'</div>
					<br/>
					<div class="paragraph">Fecha Expedición: '.$fechaExpedicion->getLocaleDate('long').'</div>
					<div class="paragraph">NO REQUIERE FIRMA AUTOGRAFA SEGUN DR 836/91 ART. 10</div>
				</div>
			</div>';
			if (count($bases)>1) {
				$html2.='<pagebreak />';
			}

			//Si hay valores agregar
			if ($totalRetencion!=0) {
				$html .= $html2;

				//Add lista nits
				$listaNits[$nit] = array(
					'nit' => $nit,
					'razonSocial' => $tercero->getNombre(),
					'sucursal' => 01
				);
			}
		}

		$html.='<pagebreak />';

		//LISTA NITS HTML
		$html.='<table width="100%" class="conceptos" cellspacing="0">
			<tr>
				<th>NIT</th>
				<th>RAZÓN SOCIAL</th>
				<th>SUCURSAL</th>
			</tr>';
		foreach($listaNits as $info) {
			$html.='<tr>
				<td align="left">'.$info['nit'].'</td>
				<td align="left">'.$info['razonSocial'].'</td>
				<td align="center">'.$info['sucursal'].'</td>
			</tr>';
		}
		$html.='</table>';

		$html.='</body></html>';

		if (count($cuentaBases)>0) {
			$pdf->writeHTML($html);
			$fileName = 'certificados.'.mt_rand(1, 9999).'.pdf';
			$pdf->Output('public/temp/'.$fileName);

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'No se encontró movimiento'
			);
		}

	}

}