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
 * @copyright   BH-TECK Inc. 2009-2014
 * @version     $Id$
 */

/**
 * Libro_MayorController
 *
 * Libro Mayor y Balance con Terceros
 *
 */
class Libro_MayorController extends ApplicationController
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

        $this->setParamToView('fechaCierre', $fechaCierre);
        $this->setParamToView('anoCierre', $empresa1->getAnoc());

        $this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
    }

    /**
     * Genrar Libro
     *
     * @return Array
     */
    public function generarAction()
    {

        $this->setResponse('json');

        try
        {
            $ano = $this->getPostParam('ano', 'int');
            $mes = $this->getPostParam('mes', 'int');

            if ($ano<=0) {
                return array(
                    'status' => 'FAILED',
                    'message' => 'Indique el año del reporte'
                );
            }

            if ($mes<=0) {
                return array(
                    'status' => 'FAILED',
                    'message' => 'Indique el mes del reporte'
                );
            }

            $reportType = $this->getPostParam('reportType', 'alpha');
            $report = ReportBase::factory($reportType);

            $titulo = new ReportText('LIBRO MAYOR Y BALANCE', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $fecha = Date::fromParts($ano, $mes, 1);
            $fechaUltimo = Date::getLastDayOfMonth($fecha->getMonth(), $fecha->getYear());
            $periodoFecha = $fechaUltimo->getPeriod();

            $titulo2 = new ReportText('A '.$fechaUltimo->getMonthName().' '.$fechaUltimo->getDay().' de '.$fechaUltimo->getYear(), array(
                'fontSize' => 11,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo, $titulo2), false, true);
            $report->setDocumentTitle('Libro Mayor y Balance');
            $report->setColumnHeaders(array(
                'CUENTA',
                'DESCRIPCIÓN',
                'S. ANTERIOR DEBE',
                'S. ANTERIOR HABER',
                'MOVIMIENTO DEBE',
                'MOVIMIENTO HABER',
                'N. SALDO DEBE',
                'N. SALDO HABER',
            ));

            $report->setColumnStyle(array(2, 3, 4, 5, 6, 7), new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
            )));

            $report->setColumnFormat(array(2, 3, 4, 5, 6, 7), new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 2
            )));

            $report->setTotalizeColumns(array(2, 3, 4, 5, 6, 7));

            $report->start(true);

            $comprobanteCierre = '';
            $periodo = $fecha->getPeriod();

            $fechaAnterior = Date::diffInterval($fecha, 1, Date::INTERVAL_MONTH);
            $periodoAnterior = $fechaAnterior->getPeriod();

            $cuentaInicial = $this->Cuentas->minimum(array("cuenta", 'conditions' => "es_mayor='S'"));
            $cuentaFinal = $this->Cuentas->maximum(array("cuenta", "es_mayor='S'"));
            //throw new Exception("ini: $cuentaInicial, fin: $cuentaFinal", 1);

            $libro = array();
            $cuentasMay = $this->Cuentas->find(array("es_mayor='S'", 'order' => 'cuenta ASC', 'columns' => 'cuenta'));
            foreach ($cuentasMay as $cuentaMay) {
                $codigoCuentaMay = $cuentaMay->getCuenta();

                $totalSaldoAnteriorMayor = 0;
                $totalCreditosMayor = 0;
                $totalDebitosMayor = 0;

                $conditions = "cuenta >= '$codigoCuentaMay' AND cuenta <= '{$codigoCuentaMay}9999999'";
                $cuentasAux = $this->Cuentas->find(array($conditions, 'order' => 'cuenta ASC'));
                foreach ($cuentasAux as $cuenta) {
                    $codigoCuenta = $cuenta->getCuenta();

                    if (!isset($libro[$codigoCuenta])) {
                        $libro[$codigoCuenta] = array(
                            'saldoAnterior' => 0,
                            'debitos' => 0,
                            'creditos' => 0,
                            'es_auxiliar' => true
                        );
                    }
                    $libro[$codigoCuenta]['es_auxiliar'] = true;

                    if ($cuenta->getEsAuxiliar() == 'S') {
                        $conditionsSaldosc = "ano_mes='$periodoAnterior' AND cuenta = '$codigoCuenta'";
                        $saldosc = $this->Saldosc->find($conditionsSaldosc);
                        foreach ($saldosc as $saldoc) {
                            $libro[$codigoCuenta]['saldoAnterior']+=$saldoc->getSaldo();

                            unset($saldoc);
                        }
                        unset($saldosc);
                    }

                    $conditions = "cuenta >= '$codigoCuenta' AND cuenta <= '{$codigoCuenta}9999999' AND fecha>='$fecha' AND fecha<='$fechaUltimo'";
                    $movis = $this->Movi->find(array($conditions, 'columns' => 'valor,deb_cre'));
                    foreach ($movis as $movi) {
                        if ($movi->getDebCre() == 'D') {
                            $libro[$codigoCuenta]['debitos'] += $movi->getValor();
                        } else {
                            $libro[$codigoCuenta]['creditos'] += $movi->getValor();
                        }

                        unset($movi);
                    }

                    $totalSaldoAnteriorMayor += $libro[$codigoCuenta]['saldoAnterior'];
                    $totalDebitosMayor += $libro[$codigoCuenta]['debitos'];
                    $totalCreditosMayor += $libro[$codigoCuenta]['creditos'];

                    unset($conditions);
                    unset($movis);
                    unset($codigoCuenta);
                }

                if (!isset($libro[$codigoCuentaMay])) {
                    $libro[$codigoCuentaMay] = array(
                        'saldoAnterior' => $totalSaldoAnteriorMayor,
                        'debitos' => 0,
                        'creditos' => 0,
                        'es_auxiliar' => false
                    );
                } else {
                    $libro[$codigoCuentaMay]['es_auxiliar'] = false;
                    $libro[$codigoCuentaMay]['saldoAnterior'] += $totalSaldoAnteriorMayor;
                }

                unset($cuenta, $codigoCuentaMay, $totalSaldoAnteriorMayor, $totalDebitosMayor, $totalCreditosMayor);
            }

            $leftColumn = new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 11
            ));

            $rightColumn = new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
            ));

            $rightColumnT = new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 13,
                'fontWeight' => 'bold',
            ));

            $numberFormat = new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 2
            ));

            $subTotalWhite = new ReportRawColumn(array(
                'value' => '',
                'style' => $rightColumn,
                'span' => 8
            ));

            $subTotalCB = new ReportRawColumn(array(
                'value' => 'SUBTOTAL CUENTAS BALANCE',
                'style' => $rightColumnT,
                'span' => 2
            ));

            $subTotalCO = new ReportRawColumn(array(
                'value' => 'SUBTOTAL CUENTAS DE ORDEN',
                'style' => $rightColumnT,
                'span' => 2
            ));

            $flagTotBal = false;

            $totales = array(0, 0, 0, 0, 0, 0);//Cols 1-6

            foreach ($libro as $codigoCuenta => $libroCuenta) {
                $primerNumero = (int) substr($codigoCuenta, 0, 1);

                //Si es Cuenta de Balance
                if ($primerNumero >= 8 && $flagTotBal == false) {

                    $sum1 = new ReportRawColumn(array(
                        'value' => $totales[0],
                        'style' => $rightColumn,
                        'format' => $numberFormat
                    ));
                    $sum2 = new ReportRawColumn(array(
                        'value' => $totales[1],
                        'style' => $rightColumn,
                        'format' => $numberFormat
                    ));
                    $sum3 = new ReportRawColumn(array(
                        'value' =>$totales[2],
                        'style' => $rightColumn,
                        'format' => $numberFormat
                    ));
                    $sum4 = new ReportRawColumn(array(
                        'value' => $totales[3],
                        'style' => $rightColumn,
                        'format' => $numberFormat
                    ));
                    $sum5 = new ReportRawColumn(array(
                        'value' => $totales[4],
                        'style' => $rightColumn,
                        'format' => $numberFormat
                    ));
                    $sum6 = new ReportRawColumn(array(
                        'value' => $totales[5],
                        'style' => $rightColumn,
                        'format' => $numberFormat
                    ));

                    $report->addRawRow(array(
                        $subTotalCB,
                        $sum1,
                        $sum2,
                        $sum3,
                        $sum4,
                        $sum5,
                        $sum6
                    ));

                    $report->addRawRow(array(
                        $subTotalWhite
                    ));

                    //Reiniciamos conteo
                    $totales = array(0, 0, 0, 0, 0, 0);//Cols 1-6

                    //Para no vovler a amostrar el total
                    $flagTotBal = true;

                    unset($sum1, $sum2, $sum3, $sum4, $sum5, $sum6);
                }

                if ($libroCuenta['saldoAnterior'] != 0 || $libroCuenta['debitos'] != 0 || $libroCuenta['creditos'] != 0) {
                    $cuenta = BackCacher::getCuenta($codigoCuenta);

                    $total_nvo = $libroCuenta['saldoAnterior'] + ($libroCuenta['debitos'] - $libroCuenta['creditos']);

                    $col5 = '';
                    $col6 = '';

                    if ($total_nvo > 0) {
                        $col5 = abs($total_nvo);
                    } else {
                        $col6 = abs($total_nvo);
                    }

                    if ($libroCuenta['saldoAnterior'] > 0) {
                        $rowTmp = array(
                            $codigoCuenta,
                            $cuenta->getNombre(),
                            abs($libroCuenta['saldoAnterior']),
                            '',
                            abs($libroCuenta['debitos']),
                            abs($libroCuenta['creditos']),
                            $col5,
                            $col6
                        );
                    } else {
                        $rowTmp = array(
                            $codigoCuenta,
                            $cuenta->getNombre(),
                            '',
                            abs($libroCuenta['saldoAnterior']),
                            abs($libroCuenta['debitos']),
                            abs($libroCuenta['creditos']),
                            $col5,
                            $col6
                        );
                    }
                    #no mostrar auxiliares
                    if (isset($libroCuenta['es_auxiliar']) && $libroCuenta['es_auxiliar'] == true) {
                        continue;
                    }
                    $report->addRow($rowTmp);

                    //Sumamos columnas para totalizar
                    $totales[0] += (float) $rowTmp[2];
                    $totales[1] += (float) $rowTmp[3];
                    $totales[2] += (float) $rowTmp[4];
                    $totales[3] += (float) $rowTmp[5];
                    $totales[4] += (float) $rowTmp[6];
                    $totales[5] += (float) $rowTmp[7];
                }

                unset($libroCuenta);
            }

            //Si es cuenta de orden
            $sum1o = new ReportRawColumn(array(
                'value' => $totales[0],
                'style' => $rightColumn,
                'format' => $numberFormat
            ));
            $sum2o = new ReportRawColumn(array(
                'value' => $totales[1],
                'style' => $rightColumn,
                'format' => $numberFormat
            ));
            $sum3o = new ReportRawColumn(array(
                'value' => $totales[2],
                'style' => $rightColumn,
                'format' => $numberFormat
            ));
            $sum4o = new ReportRawColumn(array(
                'value' => $totales[3],
                'style' => $rightColumn,
                'format' => $numberFormat
            ));
            $sum5o = new ReportRawColumn(array(
                'value' => $totales[4],
                'style' => $rightColumn,
                'format' => $numberFormat
            ));
            $sum6o = new ReportRawColumn(array(
                'value' => $totales[5],
                'style' => $rightColumn,
                'format' => $numberFormat
            ));

            $report->addRawRow(array(
                $subTotalCO,
                $sum1o,
                $sum2o,
                $sum3o,
                $sum4o,
                $sum5o,
                $sum6o
            ));

            $report->addRawRow(array(
                $subTotalWhite
            ));

            //FINISH REPORT
            $report->finish();
            $fileName = $report->outputToFile('public/temp/libro-mayor');

            return array(
                'status' => 'OK',
                'file' => 'temp/'.$fileName
            );
        }
        catch(Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

}
