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

Core::importFromLibrary('Hfos/Socios','SociosCore.php');
/**
 * Cargos_SociosController
 *
 * Controlador de generacion de cargos mensuales
 *
 */
class Cargos_SociosController extends ApplicationController {

    public function initialize(){
        $controllerRequest = ControllerRequest::getInstance();
        if($controllerRequest->isAjax()){
            View::setRenderLevel(View::LEVEL_LAYOUT);
        }
    }


    /**
     * Vista principal
     *
     */
    public function indexAction(){
        try
        {
            $periodo = EntityManager::get('Periodo')->findFirst(array('conditions'=>'cierre="N"','order'=>'periodo ASC'));
            if($periodo==false){
                $datosClub = $this->DatosClub->findFirst();
                $fecha = new Date($datosClub->getFCierre());
                $periodo = SociosCore::makePeriodo($fecha->getPeriod());
                $periodoStr = $periodo->getPeriodo();
            } else {
                $periodoStr = $periodo->getPeriodo();
            }
            $this->setParamToView('mes', $periodoStr);
            $this->setParamToView('message', 'De click en Generar Cargos Mensuales');
        }
        catch(Exception $e){

        }
    }

    /**
     * Metodo que visualiza el formato a imprimir
     */
    public function getFormatoAction(){
        $this->setResponse('view');
        $controller = $this->getControllerName();
        $this->setParamToView('controller', $controller);
        $periodo = EntityManager::get('Periodo')->minimum('periodo','conditions: cierre="N"');
        $periodo = SociosCore::checkPeriodo($periodo);
        $this->setParamToView('periodo', $periodo);
        $movimientoObj = EntityManager::get('Movimiento')->find(array('conditions'=>'1=1','group'=>'fecha_at','order'=>'fecha_at DESC'));
        $this->setParamToView('movimientoObj', $movimientoObj);
    }

    /**
     * Generar cargos fijos mensuales a socios para factrurar
     */
    public function generarAction()
    {

        set_time_limit(0);

        $this->setResponse('json');

        try
        {

            $transaction = TransactionManager::getUserTransaction();

            Core::importFromLibrary('Hfos/Socios','SociosCore.php');
            //periodo actual
            $periodo = SociosCore::getCurrentPeriodo();

            //variables de generación de facturas
            $fechaFactura = $this->getPostParam('dateIni','date');
            $fechaVencimiento = $this->getPostParam('dateFin','date');
            $sostenimiento = $this->getPostParam('sostenimiento');
            $administracion = $this->getPostParam('administracion');
            $novedades = $this->getPostParam('novedades');
            $consumoMinimo = $this->getPostParam('consumoMinimo');
            $interesesMora = $this->getPostParam('interesesMora');
            $ajusteSostenimiento = $this->getPostParam('ajusteSostenimiento');

            //Recalculamos movimientos
            $configMovi = array(
                'periodo' => $periodo,
                'fechaFactura' => $fechaFactura,
                'fechaVencimiento' => $fechaVencimiento,
                'g_sostenimiento' => $sostenimiento,
                'g_administracion' => $administracion,
                'g_novedades' => $novedades,
                'g_consumoMinimo' => $consumoMinimo,
                'g_interesesMora' => $interesesMora,
                'g_ajusteSostenimiento' => $ajusteSostenimiento
            );

            $sociosFactura = new SociosFactura();
            $sociosFactura->generarCargosSocios($configMovi);
            $sociosFactura->generarMovimiento($configMovi);

            //Commit
            $status = $transaction->commit();

            return array(
                'status'	=> 'OK',
                'message'	=> 'Los cargos de socios han sido creados exitosamente a la fecha "'.$fechaFactura.'"'
            );

        }
        catch(SociosException $e) {
            return array(
                'status'	=> 'FAILED',
                'message'	=> $e->getMessage()
            );
        }
        catch(Exception $e) {
            return array(
                'status'	=> 'FAILED',
                'message'	=> $e->getMessage()
            );
        }
    }

    /**
     * Genera reporte de cargos asignados a socios
     *
     */
    public function reporteCargosGeneradosAction(){
        set_time_limit(0);

        $this->setResponse('json');

        try{
            $transaction = TransactionManager::getUserTransaction();

            $periodo = $this->getPostParam('periodo', 'int');
            $fechaFactura = $this->getPostParam('dateIni', 'date');
            $reportType = $this->getPostParam('reportType', 'alpha');
            $report = ReportBase::factory($reportType);

            $sociosFactura = new SociosFactura();

            if (!$fechaFactura) {
                throw new SociosException("No se ha definido la fecha del movimineto a imprimir");
            }

            $titulo = new ReportText('CARGOS FIJOS GENERADOS', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $titulo2 = new ReportText('PERIODO: '.$periodo, array(
                'fontSize' => 14,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $leftColumnBold = new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 11,
                'fontWeight' => 'bold'
            ));

            $report->setHeader(array($titulo, $titulo2));

            $report->setDocumentTitle('Cargos Fijos Generado');
            $report->setColumnHeaders(array(
                'CARGO FIJO',
                'VALOR',
                'IVA',
                'ICO',
                'TOTAL'
            ));

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));

            $report->setColumnStyle(array(0), new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 11
            )));

            $report->setColumnStyle(array(1, 2, 3, 4), new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
            )));

            $report->setColumnFormat(array(1, 2, 3, 4), new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 0
            )));

            $report->setTotalizeColumns(array(2, 3, 4));

            $report->start(true);

            $empresa = EntityManager::get('DatosClub')->findFirst();

            $totalValorT = 0;
            $totalIvaT	= 0;
            $totalIcoT	= 0;
            $totalTotalT = 0;

            /**
            *'SOCIO',
            *'CARGO FIJO',
            *'VALOR',
            *'IVA',
            *'TOTAL'
            */
            $movimientoObj = EntityManager::get('Movimiento')->find(array('conditions'=>'fecha_at="'.$fechaFactura.'"'));

            $lastSociosId = null;
            $dataSociosId = array();
            $sumatoriasDesc = array();

            foreach ($movimientoObj as $movimiento)
            {

                //Obtenemos socio
                $sociosId = $movimiento->getSociosId();
                if (!$sociosId) {
                    continue;
                }

                $socios = BackCacher::getSocios($sociosId);
                if ($socios==false) {
                    $transaction->rollback('El socio con id "'.$sociosId.'" no existe');
                }

                if (!isset($dataSociosId[$sociosId])) {
                    $dataSociosId[$sociosId] = array();
                }

                //Descipcion de socio
                $dataSociosId[$sociosId]['sociosDesc'] = $socios->getNumeroAccion().' / C.C. '.$socios->getIdentificacion().' / '.$socios->getNombres().' '.$socios->getApellidos();

                //rows
                if (!isset($dataSociosId[$sociosId]['rows'])) {
                    $dataSociosId[$sociosId]['rows'] = array();
                }

                #Detalle Movimiento
                $detalleMovimientoObj = $this->DetalleMovimiento->find(array("movimiento_id={$movimiento->getId()}"));
                foreach ($detalleMovimientoObj as $detalleMovimiento)
                {
                    $dataSociosId[$sociosId]['rows'][] = array(
                        'Nombre'	=> $detalleMovimiento->getDescripcion(),
                        'Valor'		=> $detalleMovimiento->getValor(),
                        'Iva'		=> $detalleMovimiento->getIva(),
                        'Ico'		=> $detalleMovimiento->getIco(),
                        'Total'		=> ($detalleMovimiento->getValor() + $detalleMovimiento->getIva())
                    );

                    unset($detalleMovimiento);
                }

                if (!count($dataSociosId[$sociosId]['rows'])) {
                    unset($dataSociosId[$sociosId]);
                }

                $lastSociosId = $sociosId;

                unset($movimiento, $cargoSocio, $cargoFijo, $socios, $detalleMovimientoObj);

            }
            $nTotal = count($movimientoObj);

            unset($movimientoObj, $empresa, $cargosSocios);

            $showFinanciacion = Settings::get('show_financiacion_socios', 'SO');
            if (!$showFinanciacion) {
                throw new Exception('No se ha configurado si se desea ver o no la financiación en la factura en configuración');
            }

            //$showPOS = Settings::get('show_ordenes_pos', 'SO');
            $showPOS = 'S';

            //space
            $columnaSpace = new ReportRawColumn(array(
                'value' => ' ',
                'style' => $leftColumnBold,
                'span' => 5
            ));

            foreach ($dataSociosId as $sociosId => $data)
            {
                //head
                $columnaNoCorriente = new ReportRawColumn(array(
                    'value' => $data['sociosDesc'],
                    'style' => $leftColumnBold,
                    'span' => 5
                ));
                $report->addRawRow(array(
                    $columnaNoCorriente
                ));

                //Rows
                $totalValor = 0;
                $totalIva = 0;
                $totalIco = 0;
                $total = 0;

                foreach ($data['rows'] as $row)
                {
                    if ($showPOS=='N' && strstr($row['Nombre'], "PUNTO DE VENTA")==true) {
                        continue;
                    }

                    $report->addRow(array(
                        $row['Nombre'],
                        $row['Valor'],
                        $row['Iva'],
                        $row['Ico'],
                        $row['Total']
                    ));

                    //SUMATORIAS
                    if (!strstr($row['Nombre'], "PUNTO DE VENTA (")) {
                        if (!isset($sumatoriasDesc[$row['Nombre']])) {
                            $sumatoriasDesc[$row['Nombre']] = array(
                                'Valor'	=> 0,
                                'Iva'	=> 0,
                                'Ico'	=> 0,
                                'Total'	=> 0
                            );
                        }
                        $sumatoriasDesc[$row['Nombre']]['Valor'] += $row['Valor'];
                        $sumatoriasDesc[$row['Nombre']]['Iva']   += $row['Iva'];
                        $sumatoriasDesc[$row['Nombre']]['Ico']   += $row['Ico'];
                        $sumatoriasDesc[$row['Nombre']]['Total'] += $row['Total'];
                    }

                    $totalValor += $row['Valor'];
                    $totalIva += $row['Iva'];
                    $totalIco += $row['Ico'];
                    $total += $row['Total'];

                    unset($row);
                }
                unset($data['rows']);

                if ($showFinanciacion=='S') {

                    //Financiacion
                    $configPrestamo = array('sociosId' => $sociosId);
                    $prestamoArray = $sociosFactura->getConveniosAFacturar($configPrestamo);

                    if (isset($prestamoArray) && count($prestamoArray)) {

                        foreach ($prestamoArray as $prestamo)
                        {
                            $report->addRow(array(
                                $prestamo['descripcion'],
                                $prestamo['valor'],
                                $prestamo['mora'],
                                $prestamo['total']
                            ));

                            $totalValor += $prestamo['total'];
                            $total += $prestamo['total'];

                            unset($prestamo);
                        }
                        unset($prestamoArray);
                    }
                    unset($prestamoArray, $configPrestamo);
                }

                //Total
                $report->addRow(array(
                    'TOTAL CARGOS',
                    $totalValor,
                    $totalIva,
                    $totalIco,
                    $total
                ));

                //Sumatortia total
                $totalValorT += $totalValor;
                $totalIvaT += $totalIva;
                $totalIcoT += $totalIco;
                $totalTotalT += $total;

                //space
                $report->addRawRow(array(
                    $columnaSpace
                ));

                unset($data, $sociosId,$columnaNoCorriente);
            }

            $sTotal = count($dataSociosId);
            unset($dataSociosId);

            $columnaNoCorriente = new ReportRawColumn(array(
                'value' => "Total Socios Cargados: $sTotal / Total Cargos Calculados: $nTotal",
                'style' => $leftColumnBold,
                'span' => 5
            ));

            $report->addRawRow(array(
                $columnaNoCorriente
            ));

            unset($columnaNoCorriente);

            ///SUMATORIAS DE CONCEPTOS
            foreach ($sumatoriasDesc as $concepto => $conceptoArray)
            {
                $report->addRow(array(
                    "TOTAL CONCEPTO '$concepto'",
                    $conceptoArray['Valor'],
                    $conceptoArray['Iva'],
                    $conceptoArray['Ico'],
                    $conceptoArray['Total'],
                ));
                unset($conceptoArray);
            }
            unset($sumatoriasDesc);

            $cargoSocioSumas = EntityManager::get('CargosSocios')->setTransaction($transaction);

            $totalValor = $cargoSocioSumas->sum('valor','conditions: periodo="'.$periodo.'"');
            $totalIva = $cargoSocioSumas->sum('iva','conditions: periodo="'.$periodo.'"');
            $totalIco = $cargoSocioSumas->sum('ico','conditions: periodo="'.$periodo.'"');
            $totalTotal = $totalValor + $totalIva + $totalIco;

            unset($cargoSocioSumas);

            $report->addRawRow(array(
                $columnaSpace
            ));

            $report->setTotalizeValues(array(
                1 => $totalValorT,
                2 => $totalIvaT,
                3 => $totalIcoT,
                4 => $totalTotalT
            ));

            $report->finish();
            $fileName = $report->outputToFile('public/temp/cargosSocios');

            unset($sociosFactura, $report, $totalValor, $totalIva, $totalTotal);

            return array(
                'status' => 'OK',
                'message' => 'Se realizó el reporte correctamente.',
                'file' => 'temp/'.$fileName
            );
        }
        catch(Exception $e){
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

     public function testAction()
    {
            $this->setResponse("json");
            try
            {
                    $transaction = TransactionManager::getUserTransaction();
                    $sociosCore = new SociosCore();
                    $facturaHotelObj = SociosCore::limpiarBD($transaction);
                    $transaction->commit();
                    //$facturaHotelObj = $sociosCore->getFacturasCartera(201307, 1070585456);
                    return array(
                            'status' => 'OK',
                            'message' => 'OK'
                    );
            }
            catch(SociosException $e) {
                    return array(
                            'status' => 'FAILED',
                            'message' => $e->getMessage()
                    );
            }
    }

}
