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
 * Facturas_GeneradasController
 *
 * Controlador de las facturas generadas
 *
 */
class Facturas_GeneradasController extends ApplicationController
{

    public function initialize()
    {
        $controllerRequest = ControllerRequest::getInstance();
        if ($controllerRequest->isAjax()) {
            View::setRenderLevel(View::LEVEL_LAYOUT);
        }
    }

    /**
     * Vista principal
     *
     */
    public function indexAction()
    {
        $this->setParamToView('message', 'Seleccione los criterios de busqueda');
        $periodos = EntityManager::get('Periodo')->find(array('order'=>'periodo DESC'));
        $this->setParamToView('periodos', $periodos);
    }

    /**
     * Cambia el numero de accion de un socio
     *
     *
     */
    public function generarAction()
    {
        $this->setResponse('json');

        try {

            $transaction        = TransactionManager::getUserTransaction();

            //Parametros de busqueda
            $periodoStr= $this->getPostParam('periodo', 'int');
             //detallado
            $detallado= $this->getPostParam('detallado', 'int');

            $reportType = $this->getPostParam('reportType', 'alpha');
            //$reportType   = 'html';

            //TITULO PRINCIPAL
            $headers[]= new ReportText('INFORME DE FACTURAS GENERADAS', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $headers[]= new ReportText('Fecha de emisión: '.date('Y-m-d'), array(
                'fontSize' => 13,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $i = 1;

            $report = ReportBase::factory($reportType);
            $report->setHeader($headers);
            $report->setDocumentTitle('Informe de Facturas Generadas');
            $report->setColumnHeaders(array(
                'NUM.',//0
                'NO. ACCIÓN',//1
                'NOMBRE',//2
                'CÉDULA',//3
                'NO. FACTURA',//4
                'FECHA',//5
                'COMPROB',//6
                'CONCEPTO',//7
                'TIPO', //8
                'VALOR',//9
                'ESTADO'//10
            ));
            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));
            $report->setColumnStyle(array(0,1,2,3,4,5,6,7,8,10), new ReportStyle(array(
                'textAlign' => 'center',
                'fontSize' => 11
            )));
            $report->setColumnStyle(array(9), new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11
            )));
            $report->setColumnFormat(array(9), new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 0
            )));
            $report->start(true);

            $total=0;

            $showFinanciacion = Settings::get('show_financiacion_socios', 'SO');
            if (!$showFinanciacion) {
                throw new Exception('No se ha configurado si se desea ver o no la financiación en la factura en configuración');
            }

            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');

            $sociosFactura = new SociosFactura();

            //Buscamos segun periodo
            $facturaObj = EntityManager::get('Factura')->find(array("periodo='$periodoStr' AND estado='D'"));
            foreach ($facturaObj as $factura) {

                $sociosId = $factura->getSociosId();

                //Get info de socio
                $socio = BackCacher::getSocios($sociosId);

                if ($socio==false || !$socio->getNumeroAccion()) {
                    continue;
                }

                //SUM MOVIMIENTO
                $valorTotal = 0;
                $movimiento = EntityManager::get('Movimiento')->findFirst(array("socios_id='$sociosId' AND periodo='$periodoStr' and id='{$factura->getMovimientoId()}'"));
                if (!$movimiento) {
                    unset($sociosId,$factura);
                    continue;
                }

                $detalleFacturaObj = EntityManager::get('DetalleFactura')->find(array("factura_id='{$factura->getId()}'", 'order'=>'descripcion ASC'));
                if (!count($detalleFacturaObj)) {
                    unset($sociosId,$factura,$movimiento);
                    continue;
                }

                $totalFactura = 0;
                foreach ($detalleFacturaObj as $detalleFactura) {

                    //Buscamos detalle de movimiento con la descripcion del detale de factura
                    $query = "movimiento_id='{$movimiento->getId()}' AND descripcion LIKE '%{$detalleFactura->getDescripcion()}%'";
                    $detalleMovimiento = EntityManager::get('DetalleMovimiento')->findFirst($query);

                    //Tipo
                    $tipoConceptoDT = '';

                    if ($detalleMovimiento) {
                        //Si usa cargos fijos
                        $cargosSocios = $detalleMovimiento->getCargosSocios();
                        if ($cargosSocios && $cargosSocios->getCargosFijosId()>0) {
                            $cargosFijos = BackCacher::getCargosFijos($cargosSocios->getCargosFijosId());
                            if ($cargosFijos) {
                                $tipo = '';
                                $tipoChar = $cargosFijos->getTipoCargo();
                                if ($tipoChar && isset(SociosCore::$_tiposCargosFijos[$tipoChar])) {
                                    $tipo = SociosCore::$_tiposCargosFijos[$tipoChar];
                                }

                                $clase = '';
                                $claseChar = $cargosFijos->getClaseCargo();
                                if ($claseChar && isset(SociosCore::$_claseCargosFijos[$claseChar])) {
                                    $clase = SociosCore::$_claseCargosFijos[$claseChar];
                                }
                                $tipoConceptoDT = "$tipo - $clase";
                            }
                        } else {
                            if ($detalleMovimiento->getTipoMovi() && isset(SociosCore::$_tipoMovimientoAutomatico[$detalleMovimiento->getTipoMovi()])) {
                                $tipoConceptoDT = SociosCore::$_tipoMovimientoAutomatico[$detalleMovimiento->getTipoMovi()];
                            } else {
                                $tipoConceptoDT = $detalleMovimiento->getTipoMovi();
                            }
                        }
                    }

                    $totalLinea = $detalleFactura->getValor() + $detalleFactura->getIva()  + $detalleFactura->getIco();

                    if ($detallado) {

                        $report->addRow(array(
                            $i,
                            $socio->getNumeroAccion(),
                            $socio->getNombres().' '.$socio->getApellidos(),
                            $socio->getIdentificacion(),
                            $factura->getNumero(),
                            $factura->getFechaFactura(),
                            $factura->getComprobContab().'-'.$factura->getNumeroContab(),
                            $detalleFactura->getDescripcion(),
                            $tipoConceptoDT,
                            $totalLinea,
                            $socio->getEstadosSocios()->getNombre()
                        ));

                    }

                    $totalFactura += $totalLinea;
                    $valorTotal += $totalLinea;
                    unset($detalleMovimiento,$cargosFijos);
                }

                //Convenios
                if ($showFinanciacion=='S') {

                    $configPrestamo = array('sociosId' => $sociosId);
                    $prestamoArray = $sociosFactura->getConveniosAFacturar($configPrestamo);

                    if (isset($prestamoArray) && count($prestamoArray)) {

                        foreach ($prestamoArray as $prestamo) {
                            $report->addRow(array(
                                $i,
                                $socio->getNumeroAccion(),
                                $socio->getNombres().' '.$socio->getApellidos(),
                                $socio->getIdentificacion(),
                                $factura->getNumero(),
                                $factura->getComprobContab().'-'.$factura->getNumeroContab(),
                                $prestamo['descripcion'],
                                'CONVENIO',
                                $prestamo['total'],
                                $socio->getEstadosSocios()->getNombre()
                            ));

                            $totalFactura += $prestamo['total'];
                            $valorTotal += $prestamo['total'];

                            unset($prestamo);
                        }
                        unset($prestamoArray);
                    }
                    unset($prestamoArray, $configPrestamo);
                }

                if ($totalFactura!=$factura->getTotalFactura()) {
                    $factura->setTotalFactura($totalFactura);
                    $factura->save();
                }

                if (!$detallado) {
                    //Add new row
                    $report->addRow(array(
                        $i,
                        $socio->getNumeroAccion(),
                        $socio->getNombres().' '.$socio->getApellidos(),
                        $socio->getIdentificacion(),
                        $factura->getNumero(),
                        $factura->getFechaFactura(),
                        $factura->getComprobContab().'-'.$factura->getNumeroContab(),
                        '',
                        'TOTAL FACTURA',
                        $factura->getTotalFactura(),
                        $socio->getEstadosSocios()->getNombre()
                    ));
                } else {
                    //Add new row
                    $report->addRow(array(
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        'TOTAL FACTURA',
                        $factura->getTotalFactura(),
                        ''
                    ));

                    //Add new row
                    $columnaCuenta = new ReportRawColumn(array(
                        'value' => '',
                        'span' => 10
                    ));
                    $report->addRawRow(array($columnaCuenta));
                }
                $i++;

                $total+= $totalFactura;
            }

            //Total
            $report->addRow(array(
                '',
                'TOTAL FACTURAS',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $total,
                ''
            ));

            $report->finish();
            $config['file']= $report->outputToFile('public/temp/facturas_generadas');

            return array(
                'status'    => 'OK',
                'message'   => 'Se genero el informe exitosamente',
                'file'      => 'temp/'.$config['file']
            );

        }
        catch(Exception $e) {
            return array(
                'status'    => 'FAILED',
                'message'   => $e->getMessage()
            );
        }

    }
}
