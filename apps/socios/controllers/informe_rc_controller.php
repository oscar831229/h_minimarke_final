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

Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
Core::importFromLibrary('Hfos/Socios', 'SociosEstadoCuenta.php');

/**
 * Informe_RcController
 *
 * Controlador de generacion de informe de recibos de caja
 *
 */
class Informe_RcController extends ApplicationController
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
        $periodoStr = SociosCore::getCurrentPeriodo();
        
        $periodo = SociosCore::getCurrentPeriodo();
        $ano = substr($periodo, 0, 4);
        $mes = substr($periodo, 4, 2);

        $fechaIniStr = "$ano-$mes-01";
        //throw new Exception($fechaIniStr);
        
        $fechaFinDate = new Date($fechaIniStr);
        $fechaFinDate->toLastDayOfMonth();

        Tag::displayTo("fechaIni", $fechaIniStr);
        Tag::displayTo("fechaFin", $fechaFinDate->getDate());

        $this->setParamToView('mes', $periodo);
        $this->setParamToView('message', 'De click en imprimir para generar el informe');
    }
    
    /**
     * Metodo que imprime los recibos de caja
     *
     */
    public function reporteAction()
    {
        $this->setResponse('json');
        
        try {

            $transaction = TransactionManager::getUserTransaction();
            
            $sociosId = $this->getPostParam('sociosId', 'int');
            $fechaIni = $this->getPostParam('fechaIni', 'date');
            $fechaFin = $this->getPostParam('fechaFin', 'date');
            
            $fechaIniDate = new Date($fechaIni);
            $periodo = $fechaIniDate->getPeriod();
            
            $reportType = $this->getPostParam('reportType', 'alpha');
            //$reportType = 'pdf';
            
            $config = array(
                'reportType'=> $reportType,
                'sociosId'  => $sociosId,
                'periodo'   => $periodo,
                'fechaIni'  => $fechaIni,
                'fechaFin'  => $fechaFin
            );


            $report = ReportBase::factory($reportType);

            $titulo = new ReportText('INFORME DE RECIBOS DE CAJA', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $titulo2 = new ReportText('Desde: '.$fechaIni.' - '.$fechaFin, array(
                'fontSize' => 11,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo, $titulo2), false, true);

            $report->setDocumentTitle('Balance de Comprobación');

            $headers = array(
                'FECHA',
                'DERECHO',
                'CC/NIT',
                'NOMBRE Y APELLIDO',
                'PREF',
                'RC No.',
                'VALOR',
                'FORMA DE PAGO',
                'CLASIFICACIÓN'
            );

            $report->setColumnHeaders($headers);

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));

            $report->setColumnStyle(0, new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 11
            )));

            $report->setColumnStyle(1, new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 11
            )));

            $report->setColumnStyle(array(6), new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
            )));

            $numberFormat = new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 2
            ));
            $report->setColumnFormat(array(6), $numberFormat);

            $leftColumn = new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 11
            ));

            $rightColumn = new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
            ));

            $report->setTotalizeColumns(array(6));

            $report->start(true);

            //Buscamos los comprobantes de pagos en socios
            $comprobsPagos = Settings::get('comprobs_pagos', 'SO');
            if (!$comprobsPagos) {
                throw new Exception("No se ha definido los comprobates de pagos en socios", 1);
            }
            $comprobsArray = explode(",", $comprobsPagos);
            $comprobsStr = "'" . implode("','", $comprobsArray) . "'";

            //Buscamos datos a mostrar cuenta 11 que son de caja
            $query = "fecha >= '$fechaIni' AND fecha<= '$fechaFin' AND comprob IN ($comprobsStr) AND deb_cre='D' AND cuenta LIKE '11%'";
            $movis = EntityManager::get('Movi')->find(array(
                "conditions" => $query
            ));

            $total = 0;
            $rows = array();
            foreach ($movis as $movi) {
                $key = $movi->getComprob() . "-" . $movi->getNumero();
                if (!isset($rows[$key])) {
                    $rows[$key] = array();
                }

                $nit = $movi->getNit();
                    
                //Solo acumula debitos
                $valor = $movi->getValor();
                
                //si no existe buscamos datos
                if (!isset($rows[$key][$nit])) {

                    $derecho = '';
                    $nombre = '';
                    $clasificacion = 'Venta';
                    
                    //Valor de comprobante
                    $comprob = $movi->getComprob();
                    $numero = $movi->getNumero();

                    //Obtenemos la forma de pago
                    $formaPagoArray = array();
                    $reccaj = EntityManager::get('Reccaj')->findFirst("comprob='$comprob' AND numero='$numero'");
                    if ($reccaj) {
                        $nit = $reccaj->getNit();
                
                        $detalleFP = EntityManager::get("DetalleReccaj")->find("reccaj_id='{$reccaj->getId()}'");
                        foreach ($detalleFP as $fp) {
                            $formaPagoId = $fp->getFormaPagoId();
                            if ($formaPagoId>0) {
                                $formasPagos = EntityManager::get("FormaPago")->findFirst($formaPagoId);
                                if ($formasPagos) {
                                    $formaPagoArray[] = $formasPagos->getDescripcion(); 
                                }
                            }
                            unset($fp);
                        }
                        unset($detalleFP);

                        //Buscamos en maestro de socios
                        $socios = EntityManager::get("Socios")->findFirst("identificacion='$nit'");
                        if ($socios) {
                            $derecho = $socios->getNumeroAccion();
                            $nombre = $socios->getApellidos() . " " . $socios->getNombres();
                            $clasificacion = $socios->getTipoSocios()->getNombre();
                        } else {
                            $tercero = BackCacher::getTercero($nit);
                            if ($tercero) {
                                $nombre = $tercero->getNombre();
                            }
                        }
                    }
                    
                    //Agregamos fila
                    $rows[$key][$nit] = array(
                        "fecha" => $movi->getFecha(),
                        "derecho" => $derecho,
                        "nit" => $nit,
                        "nombre" => $nombre,
                        "comprob" => $comprob,
                        "numero" => $numero,
                        "valor" => $valor,
                        "formaPago" => implode(", ", $formaPagoArray),
                        "clasificacion" => $clasificacion
                    );
                } else {
                    //Sumamos valores
                    $rows[$key][$nit]["valor"] += $valor;
                }
                
            }
            //throw new Exception($query);
            
            foreach ($rows as $key => $data1) {
                foreach ($data1 as $nit => $row) {
                    $report->addRow(array_values($row));
                    $total += $row["valor"];
                    unset($row, $nit);
                }
                unset($key, $data1);
            }
            

            $report->setTotalizeValues(array(
                6 => $total
            ));

            $report->finish();
            $fileName = $report->outputToFile('public/temp/informeRc');

            return array(
                'status' => 'OK',
                'message' => 'El informe ha sido generado correctamente',
                'file' => 'temp/'.$fileName
            );

        } catch (SociosException $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        } catch (Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }
}
