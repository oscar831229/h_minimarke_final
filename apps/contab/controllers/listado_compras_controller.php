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
 * Listado_ComprasController
 *
 * Listado de Compras
 *
 */
class Listado_ComprasController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$empresa1 = $this->Empresa1->findFirst();
		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrec();

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		Tag::displayTo('ano', $fechaCierre->getYear());
		Tag::displayTo('mes', $fechaCierre->getMonth());
	}

	public function generarAction(){

		$this->setResponse('json');

		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		$comprobInicial = $this->getPostParam('comprobInicial', 'comprob');
		$comprobFinal = $this->getPostParam('comprobFinal', 'comprob');

		$numeroInicial = $this->getPostParam('numeroInicial', 'int');
		$numeroFinal = $this->getPostParam('numeroFinal', 'int');

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

  		$titulo = new ReportText('LISTADO DE COMPRAS', array(
			'fontSize' => 16,
   			'fontWeight' => 'bold',
   			'textAlign' => 'center'
  		));

 		$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
			'fontSize' => 11,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
 		));

  		$report->setHeader(array($titulo, $titulo2));
  		$report->setDocumentTitle('Listado de Compras');

  		$report->setColumnHeaders(array(
  			'COMPROBANTE',
  			'FECHA',
  			'NO. DOCUMENTO',
  			'NOMBRE',
  			'COMP. GRAV. 16%',
  			'COMP. GRAV. 10%',
  			'NO GRAVADAS',
  			'SALDO MES',
  			'BASE',
  			'NUEVO SALDO',
  			'BASE'
  		));

  		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));
		$report->setColumnStyle(0, new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		)));

		$leftColumn = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		));

		$leftColumnBold = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11,
			'fontWeight' => 'bold'
		));

		$report->setColumnStyle(1, $leftColumn);

		$report->setColumnStyle(array(2, 3, 4, 5, 6), new ReportStyle(array(
  			'textAlign' => 'right',
  			'fontSize' => 11,
  		)));

		$report->setColumnFormat(array(2, 3, 4, 5, 6), new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		)));

		$report->setRowsPerPage(35);
		$report->setTotalizeColumns(array(2, 5, 6));

		$report->start(true);

		$conditions = array();
		if($comprobInicial!=''&&$comprobFinal!=''){
			$conditions[] = "comprob>='$comprobInicial' AND comprob<='$comprobFinal'";
		}
		if($numeroInicial!=''&&$numeroFinal!=''){
			$conditions[] = "numero>='$numeroInicial' AND numero<='$numeroFinal'";
		}
		if($fechaInicial!=''&&$fechaFinal!=''){
			$conditions[] = "fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
		}
		$conditions = join(' AND ', $conditions);

		$totald = 0;
		$totalret = 0;
		$totalriva = 0;
		$totaldocg = 0;
		$totaldocg1 = 0;
		$totaldoc = 0;
		$totalic = 0;
		$totalis = 0;
		$totalih = 0;
		$totalim = 0;
		$totalit = 0;

		$numeroDoc = 0;
		$nitTercero = 0;
		$listaCompras = array();
		foreach($this->Movi->find($conditions, 'order: comprob,numero') as $movi){
			$codigoComprob = $movi->getComprob();
			$numero = $movi->getNumero();
			if(!isset($listaCompras[$codigoComprob][$numero])){
				$listaCompras[$codigoComprob][$numero] = array(
					'compGrav16' => 0,
					'compGrav10' => 0,
					'regComp' => 0,
					'simplServ' => 0,
					'iva10Comp' => 0,
					'iva10Serv' => 0,
					'iva16Comp' => 0,
					'iva16Serv' => 0,
					'iva16Hono' => 0,
					'iva16Comi' => 0,
					'iva16Segu' => 0,
					'ivaReten' => 0,
					'valReten' => 0,
					'valTotal' => 0,
					'numDocum' => ''
				);
			}
			$codigoCuenta = $movi->getCuenta();
			$cuenta = BackCacher::getCuenta($codigoCuenta);
			if($cuenta->getPideFact()=='S'){
				$numeroDoc = $movi->getNumeroDoc();
				$nitTercero = $movi->getNit();
				if(!$nitTercero){
					$nitTercero = '0';
				}
			}
			if(substr($codigoCuenta, 0, 4)=='2365'){
				$totald-=$movi->getValor();
				$totalret+=$movi->getValor();
			} else {
				if(substr($codigoCuenta, 0, 4)=='2367'){
					$totald-=$movi->getValor();
					$totalriva+=$movi->getValor();
				} else {
					if(substr($codigoCuenta, 0, 6)=='240805'){
						$totald+=$movi->getValor();
						if($cuenta->getPorcIva()==0.16){
							if($movi->getDebCre()=='D'&&$movi->getBaseGrab()!=''){
								$listaCompras[$codigoComprob][$numero]['compGrav16']+=$movi->getBaseGrab();
							}
							if(substr($codigoCuenta, 6)=='002004'||substr($codigoCuenta, 6)=='002010'){
								$totalic+=$movi->getValor();
							} else {
								if(substr($codigoCuenta, 6)=='003002'||substr($codigoCuenta, 6)=='003009'){
									$totalis+=$movi->getValor();
								} else {
									if(substr($codigoCuenta, 6)=='003003'){
										$totalih+=$movi->getValor();
									} else {
										if(substr($codigoCuenta, 6)=='003004'){
											$totalim+=$movi->getValor();
										} else {
											if(substr($codigoCuenta, 6)=='003006'){
												$totalig+=$movi->getValor();
											} else {
												$totalit+=$movi->getValor();
											}
										}
									}
								}
							}
						} else {
							if($movi->getDebCre()=='D'&&$movi->getBaseGrab()!=''){
								$listaCompras[$codigoComprob][$numero]['compGrav10']+=$movi->getBaseGrab();
							}
							if(substr($codigoCuenta, 11)=='3'){
								$totali3+=$movi->getValor();
							} else {
								if(substr($codigoCuenta, 11)=='1'){
									$totali4+=$movi->getValor();
								} else {
									if(substr($codigoCuenta, 11)=='5'){
										$listaCompras[$codigoComprob][$numero]['compNoGrav']+=$movi->getValor();
									} else {
										if(substr($codigoCuenta, 11)=='0'){
											$listaCompras[$codigoComprob][$numero]['regComp']+=$movi->getValor();
										}
									}
								}
							}
						}
					} else {
						if($movi->getDebCre()=='D'){
							$totald+=$movi->getValor();
							$totaldoc+=$movi->getValor();
						}
					}
				}
			}
		}


		$totalDebitos = 0;
		$totalCreditos = 0;


		$retenciones = array();
		$cuentas = $this->Cuentas->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'", "columns: cuenta", "order: cuenta");
		foreach($cuentas as $cuenta){

			$totalSaldoAnterior = 0;
			$totalBaseAnterior = 0;
			$totalSaldoNuevo = 0;
			$totalBaseNueva = 0;
			$codigoCuenta = $cuenta->getCuenta();
			$conditions = "cuenta='$codigoCuenta' AND (ano_mes=0 OR ano_mes='$periodo' OR ano_mes='$periodoAnterior')";
			$saldosns = $this->Saldosn->find($conditions, "order: ano_mes,nit");
			if(count($saldosns)){
				foreach($saldosns as $saldosn){
					$nitTercero = $saldosn->getNit();
					if(!isset($retenciones[$codigoCuenta][$nitTercero])){
						$retenciones[$codigoCuenta][$nitTercero] = array(
							'saldoAnterior' => 0,
							'baseAnterior' => 0,
							'saldoNueva' => 0,
							'baseNueva' => 0
						);
					}
					if($saldosn->getAnoMes()==$periodo){
						$retenciones[$codigoCuenta][$nitTercero]['saldoAnterior'] = $saldosn->getSaldo();
						$retenciones[$codigoCuenta][$nitTercero]['baseAnterior'] = $saldosn->getBaseGrab();
						$totalSaldoAnterior+=$saldosn->getSaldo();
						$totalBaseAnterior+=$saldosn->getBaseGrab();
					} else {
						if($saldosn->getAnoMes()==0){
							$conditions = "cuenta='$codigoCuenta' AND nit='$nitTercero' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
							$movis = $this->Movi->find($conditions, "columns: valor,deb_cre,base_grab");
							foreach($movis as $movi){
								if($movi->getDebCre()=='D'||$movi->getDebCre()=='0'){
									$retenciones[$codigoCuenta][$nitTercero]['saldoNueva']+=$movi->getValor();
									$totalSaldoNuevo+=$movi->getValor();
								} else {
									$retenciones[$codigoCuenta][$nitTercero]['saldoNueva']-=$movi->getValor();
									$totalSaldoNuevo-=$movi->getValor();
								}
								$retenciones[$codigoCuenta][$nitTercero]['baseNueva']+=$movi->getBaseGrab();
								$totalBaseNueva+=$movi->getBaseGrab();
								unset($movi);
							}
						}
					}
					unset($saldosn);
				}
			}
		}

		foreach($retenciones as $codigoCuenta => $retencionesCuenta){
			$cuenta = BackCacher::getCuenta($codigoCuenta);
			$columnaCuenta = new ReportRawColumn(array(
				'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
				'style' => $leftColumnBold,
				'span' => 8
			));
			$report->addRawRow(array($columnaCuenta));
			foreach($retencionesCuenta as $nitTercero => $retencionesTercero){
				$nit = BackCacher::getTercero($nitTercero);
				if($nit==false){
					$nombreTercero = 'NO EXISTE EL TERCERO';
				} else {
					$nombreTercero = $nit->getNombre();
				}
				$report->addRow(array(
					$nitTercero,
					$nombreTercero,
					$retencionesTercero['saldoAnterior'],
					$retencionesTercero['baseAnterior'],
					$retencionesTercero['saldoNueva'],
					$retencionesTercero['baseNueva'],
					-$retencionesTercero['saldoAnterior']-$retencionesTercero['saldoNueva'],
					-$retencionesTercero['baseAnterior']-$retencionesTercero['baseNueva']
				));
				unset($nombreTercero);
			}
		}

		$report->finish();
		$fileName = $report->outputToFile('public/temp/retencion-acumulada');

		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);

	}

}