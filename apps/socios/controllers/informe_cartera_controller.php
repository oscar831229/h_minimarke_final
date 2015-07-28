<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package     Back-Office
 * @copyright   BH-TECK Inc. 2009-2010
 * @version     $Id$
 */

/**
 * Informe_CarteraController
 *
 * Controlador del informe de carteras por edades
 */
class Informe_CarteraController extends ApplicationController
{

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

        Tag::displayTo('fechaLimite', Date::getCurrentDate());
        $this->setParamToView('fechaCierre', $fechaCierre);
        $this->setParamToView('anoCierre', $empresa1->getAnoc());

        $this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
    }

    public function generarAction()
    {

        $this->setResponse('json');
        try
        {
            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
            
            $span = 13;
            $spanTotal = 5;
            $iIni = 5;
            $iFin = 12;

            $fechaLimite = $this->getPostParam('fechaLimite', 'date');

            $cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
            $cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');
            $sociosActivos = $this->getPostParam('activos', 'int');
            $sociosId = $this->getPostParam('socios_id', 'int');
            $consolidado = $this->getPostParam('consolidado', 'int');

            if ($sociosId) {
                $socios = BackCacher::getSocios($sociosId);
                if (!$socios) {
                    throw new Exception("El socios con id  '$sociosId' no existe");
                }
            }

            //Cuentas de cartera de socios
            $cuentasLike = Settings::get('cuenta_ajustes_estado_cuenta', 'SO');
            $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
            
            $conditions = array();
            $conditions[] = "f_emision<='$fechaLimite' AND (cuenta LIKE '$cuentasLike' OR cuenta='$cuentaSaldoAFavor')";

            if ($sociosId) {
                $conditions[] = "nit='{$socios->getIdentificacion()}'";
            }

            $reportType = $this->getPostParam('reportType', 'alpha');
            $report = ReportBase::factory($reportType);

            $titulo = new ReportText('CARTERA POR EDADES SOCIOS', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $titulo2 = new ReportText('Documentos por Vencimiento Hasta: '.$fechaLimite, array(
                'fontSize' => 11,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo, $titulo2));
            $report->setDocumentTitle('Cartera Por Edades Socios');
            $report->setColumnHeaders(array(
                'SOCIO',//0
                'CUENTA',//1
                'CPBTE. No.',//2
                'FACTURA No.',//3
                'FECHA FRA',//4
                'VR. FACTURA',//5
                'SALDO POR PAGAR',//6
                '1 A 30',//7
                '31 A 60',//8
                '61 A 90',//9
                '91 A 120',//10
                '> 4 MESES',//11
                'DESDE',//12
                'DÍAS',//13
            ));

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
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

            $rightColumn = new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
            ));

            $rightColumnBold = new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
                'fontWeight' => 'bold'
            ));

            $numberFormat = new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 2
            ));

            $report->setColumnStyle(array(0, 1, 2, 3, 4), $leftColumn);
            $report->setColumnStyle(array(5, 6, 7, 8, 9, 10, 11, 12, 13), $rightColumn);
            $report->setColumnFormat(array(5, 6, 7, 8, 9, 10, 11, 12, 13), $numberFormat);

            $columnaGranTotal = new ReportRawColumn(array(
                'value' => 'GRAN TOTAL',
                'style' => $rightColumnBold,
                'span' => $spanTotal
            ));

            $columnaBr = new ReportRawColumn(array(
                'value' => '',
                'span' => $spanTotal
            ));

            $report->start(true);

            $cuentaAnterior = '';
            $terceroAnterior = '';
            $totalCuenta = array();
            $totalTercero = array();

            $granTotal = array();
            for ($i=$iIni; $i<$iFin; $i++) {
                $granTotal[$i] = 0;
            }

            $sociosConditionStr = join(' AND ', $conditions);
            if ($sociosActivos>0) {
                if ($sociosConditionStr) {
                    $sociosConditionStr .= " AND ";
                }
                $sociosConditionStr .= " nit IN (SELECT identificacion FROM socios WHERE cobra='S')";
            }

            $datos = array();
            $ultimaCuenta = '';
            $ultimoTercero = '';
            $terceroAnterior = '';
            $nombreTercero = "";


            $carteras = $this->Cartera->find(array($sociosConditionStr, 'order' => 'nit,cuenta,f_emision ASC'));
            foreach ($carteras as $cartera) {
                $nombreTercero = "";
                $codigoCuenta = $cartera->getCuenta();
                if ($terceroAnterior!=$cartera->getNit()) {
                    if ($cuentaAnterior!='') {
                        $columnaTotalCuenta = new ReportRawColumn(array(
                            'value' => 'TOTAL CUENTA No. ' . $codigoCuenta,
                            'style' => $rightColumnBold,
                            'span' => $spanTotal
                        ));
                        $totales = array($columnaTotalCuenta);
                        for ($i=$iIni; $i<$iFin; $i++) {
                            if (isset($totalCuenta[$i])) {
                                $totales[$i] = new ReportRawColumn(array(
                                    'value' => $totalCuenta[$i],
                                    'style' => $rightColumnBold,
                                    'format' => $numberFormat
                                ));
                            }
                        }
                        if ($consolidado!=1) {
                            //$report->addRawRow($totales);
                        }
                    }

                    $totalTercero2 = 0;
                    //MOSTRAMOS TOTAL DE TERCERO ANTERIOR
                    if ($terceroAnterior!='') {
                        $terceroAnt = BackCacher::getTercero($terceroAnterior);
                        if ($terceroAnt) {
                            $nombreTercero = $terceroAnt->getNombre();
                            $columnaTotalTercero = new ReportRawColumn(array(
                                'value' => 'TOTAL: ' . $nombreTercero,
                                'style' => $rightColumnBold,
                                'span' => $spanTotal
                            ));
                            $totales = array($columnaTotalTercero);
                            $totalTercero2 = 0;
                            for ($i=$iIni; $i<$iFin; $i++) {
                                $totales[$i] = new ReportRawColumn(array(
                                    'value' => $totalTercero[$i],
                                    'style' => $rightColumn,
                                    'format' => $numberFormat
                                ));
                                $totalTercero2 += $totalTercero[$i];
                            }
                            if ($totalTercero2!=0) {
                                $report->addRawRow($totales);
                            } 
                        }
                    }

                    $tercero = BackCacher::getTercero($cartera->getNit());
                    if ($tercero) {
                        $socios = EntityManager::get('Socios')->findFirst("identificacion='{$tercero->getNit()}'");
                        if ($socios) {
                            $porcX = '';
                            if ($socios->getPorcMoraDesfecha()>0) {
                                $porcX = "(" . intval($socios->getPorcMoraDesfecha()) . "%)";
                            }
                            $nombre =  $socios->getIdentificacion() . ": " . $socios->getNumeroAccion() . " / ".$socios->getNombres() . " " . $socios->getApellidos() . " / " . $socios->getTipoSocios()->getNombre() . " $porcX";
                        } else {
                            $nombre = $tercero->getNit().' : '.$tercero->getNombre();
                        }
                        $nombreTercero = $nombre;
                        $columnaCuenta = new ReportRawColumn(array(
                            'value' => $nombreTercero,
                            'style' => $leftColumnBold,
                            'span' => $span
                        ));
                    } else {
                        $nombreTercero = $cartera->getNit().' : NO EXISTE EL TERCERO';
                        $columnaCuenta = new ReportRawColumn(array(
                            'value' => $nombreTercero,
                            'style' => $leftColumnBold,
                            'span' => $span
                        ));
                    }
                    if ($consolidado!=1 && $totalTercero2!=0) {
                        $report->addRawRow(array($columnaBr));
                        //$report->addRawRow(array($columnaCuenta));
                    }

                    $terceroAnterior = $cartera->getNit();
                    $cuentaAnterior = '';

                    for ($i=$iIni; $i<$iFin; $i++) {
                        $totalCuenta[$i] = 0;
                        $totalTercero[$i] = 0;
                    }
                }

                if (!$cartera->getFVence()) {
                    $cartera->setFVence($cartera->setFEmision());
                }

                $codigoComprob = '';
                $numeroComprob = 0;
                $saldoCartera = 0;
                $conditions = "cuenta='$codigoCuenta' AND
                nit='{$cartera->getNit()}' AND
                tipo_doc='{$cartera->getTipoDoc()}' AND
                numero_doc='{$cartera->getNumeroDoc()}' AND
                fecha<='$fechaLimite'";
                $movis = $this->Movi->find($conditions, "order: nit,cuenta,fecha ASC");
                foreach ($movis as $movi) {
                    if (substr($codigoCuenta, 0, 1)=='1') {
                        if ($movi->getDebCre()=='D') {
                            $codigoComprob = $movi->getComprob();
                            $numeroComprob = $movi->getNumero();
                            $saldoCartera += $movi->getValor();
                        } else {
                            $saldoCartera -= abs($movi->getValor());
                        }
                    } else {
                        if ($movi->getDebCre()=='D') {
                            $codigoComprob = $movi->getComprob();
                            $numeroComprob = $movi->getNumero();
                            $saldoCartera+=$movi->getValor();
                        } else {
                            $saldoCartera -= abs($movi->getValor());
                        }
                    }
                    unset($movi);
                }
                unset($movis);

                if ($saldoCartera!=0) {

                    if ($cuentaAnterior!=$codigoCuenta) {

                        if ($cuentaAnterior!='') {
                            $columnaTotalCuenta = new ReportRawColumn(array(
                                'value' => 'TOTAL CUENTA No. ' . $cuentaAnterior,
                                'style' => $rightColumnBold,
                                'span' => $spanTotal
                            ));
                            $totales = array($columnaTotalCuenta);
                            for ($i=$iIni; $i<$iFin; $i++) {
                                $totales[$i] = new ReportRawColumn(array(
                                    'value' => $totalCuenta[$i],
                                    'style' => $rightColumnBold,
                                    'format' => $numberFormat
                                ));
                            }
                            if ($consolidado!=1) {
                                //$report->addRawRow($totales);
                            }
                        }

                        $cuenta = BackCacher::getCuenta($codigoCuenta);
                        if ($cuenta==false) {
                            return array(
                                'status' => 'FAILED',
                                'message' => "No existe la cuenta '$codigoCuenta' en el plan contable"
                            );
                        }
                        $columnaCuenta = new ReportRawColumn(array(
                            'value' => 'CUENTA No. '.$cuenta->getCuenta().' : '.$cuenta->getNombre(),
                            'style' => $leftColumnBold,
                            'span' => $span
                        ));

                        if ($consolidado!=1) {
                           //$report->addRawRow(array($columnaCuenta));
                        }

                        $cuentaAnterior = $codigoCuenta;
                        //$terceroAnterior = '';

                        for ($i=$iIni; $i<$iFin; $i++) {
                            $totalCuenta[$i] = 0;
                            //$totalTercero[$i] = 0;
                        }
                    }
                    
                    $day = SociosCore::getCarteraTime($cartera, $fechaLimite);
                    $dias = SociosCore::getDays($cartera, $fechaLimite);
                    $timecartera = array('30'=>0, '60' => 0, '90' => 0, '120' => 0, '120m' => 0);
                    $timecartera[$day]=$cartera->getSaldo();

                    $tercero = BackCacher::getTercero($cartera->getNit());
                    $row = array(
                        $tercero->getNit() . ": " . $tercero->getNombre(),//0
                        $codigoCuenta,//1
                        $codigoComprob . "-" . $numeroComprob,//2
                        $cartera->getTipoDoc() . "-" . $cartera->getNumeroDoc(),//3
                        $cartera->getFEmision(),//4
                        $cartera->getValor(),//5
                        $saldoCartera,//6
                        $timecartera['30'],//7
                        $timecartera['60'],//8
                        $timecartera['90'],//9
                        $timecartera['120'],//10
                        $timecartera['120m'],//11
                        $cartera->getFVence(),//12
                        $dias//13
                    );

                    if ($consolidado!=1) {
                        $report->addRow($row);
                    }

                    //Acumular totales por cuenta y tercero
                    for ($i=$iIni; $i<$iFin; $i++) {
                        $totalCuenta[$i]+=$row[$i];
                        $totalTercero[$i]+=$row[$i];
                        $granTotal[$i]+=$row[$i];
                    }
                }

                $ultimaCuenta = $codigoCuenta;
                $ultimoTercero = $nombreTercero;

                unset($cartera);
            }
            unset($carteras);

            $columnaTotalCuenta = new ReportRawColumn(array(
                'value' => 'TOTAL CUENTA No. ' . $ultimaCuenta,
                'style' => $rightColumnBold,
                'span' => $spanTotal
            ));

            $totales = array($columnaTotalCuenta);
            for ($i=$iIni; $i<$iFin; $i++) {
                if (isset($totalCuenta[$i])) {
                    $totales[$i] = new ReportRawColumn(array(
                        'value' => $totalCuenta[$i],
                        'style' => $rightColumnBold,
                        'format' => $numberFormat
                    ));
                }
            }
            if ($consolidado!=1) {
                //$report->addRawRow($totales);
            }

            $columnaTotalTercero = new ReportRawColumn(array(
                'value' => 'TOTAL: ' . $ultimoTercero,
                'style' => $rightColumnBold,
                'span' => $spanTotal
            ));
            
            $totales = array($columnaTotalTercero);
            $totalTercero2 = 0;
            for ($i=$iIni; $i<$iFin; $i++) {
                if (isset($totalTercero[$i])) {
                    $totales[$i] = new ReportRawColumn(array(
                        'value' => $totalTercero[$i],
                        'style' => $rightColumn,
                        'format' => $numberFormat
                    ));
                    $totalTercero2 += $totalTercero[$i];
                }
            }
            if ($consolidado!=1 && $totalTercero2!=0) {
                $report->addRawRow($totales);
            }

            $gtotales = array($columnaBr);
            for ($i=$iIni; $i<$iFin; $i++) {
                $gtotales[$i] = new ReportRawColumn(array(
                    'value' => ''
                ));
            }
            $report->addRawRow($gtotales);

            //GRAN TOTAL
            $gtotales = array($columnaGranTotal);
            for ($i=$iIni; $i<$iFin; $i++) {
                $gtotales[$i] = new ReportRawColumn(array(
                    'value' => $granTotal[$i],
                    'style' => $rightColumn,
                    'format' => $numberFormat
                ));
            }
            $report->addRawRow($gtotales);

            $report->finish();
            $fileName = $report->outputToFile('public/temp/cartera-edades-socios');

            return array(
                'status' => 'OK',
                'file' => 'temp/'.$fileName
            );
        } catch(Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage() . ", Trace: " . print_r($e->getTrace(), true)
            );
        }
    }
}
