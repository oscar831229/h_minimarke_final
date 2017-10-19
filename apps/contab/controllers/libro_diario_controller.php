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
 * Libro_DiarioController
 *
 * Libro Diario
 *
 */
class Libro_DiarioController extends ApplicationController
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
        Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
        Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));

        $this->setParamToView('fechaCierre', $fechaCierre);
        $this->setParamToView('anoCierre', $empresa1->getAnoc());
        $this->setParamToView('diarios', $this->Diarios->find());

        $this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
    }

    public function generarAction()
    {

        $this->setResponse('json');

        try
        {
            $tipo = $this->getPostParam('tipo');
            $fechaInicial = $this->getPostParam('fechaInicial', 'date');
            $fechaFinal = $this->getPostParam('fechaFinal', 'date');

            if (empty($fechaInicial)||empty($fechaFinal)) {
                return array(
                    'status' => 'FAILED',
                    'message' => 'Indique las fechas inicial y final del libro diario'
                );
            }

            $diarioInicial = (int) $this->getPostParam('diarioInicial', 'int');
            $diarioFinal = (int) $this->getPostParam('diarioFinal', 'int');

            $reportType = $this->getPostParam('reportType', 'alpha');
            $report = ReportBase::factory($reportType);

            if ($tipo == 'M') {
                $title = 'LIBRO DIARIO';
            } else {
                $title = 'LIBRO DIARIO NIIF';
            }
            $titulo = new ReportText($title, array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
                'fontSize' => 11,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo, $titulo2), false, true);
            $report->setDocumentTitle('Libro Diario');
            $report->setColumnHeaders(array(
                'CUENTA',
                'DESCRIPCIÓN',
                'DEBE',
                'HABER'
            ));

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));

            $leftColumn = new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 11
            ));

            $rightColumn = new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
            ));

            $numberFormat = new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 2
            ));

            $report->setColumnStyle(array(0, 1), $leftColumn);

            $report->setColumnStyle(array(2, 3), $rightColumn);

            $report->setColumnFormat(array(2, 3), $numberFormat);

            $subTotalDiario = new ReportRawColumn(array(
                'value' => 'SUBTOTAL DIARIO',
                'style' => $rightColumn,
                'span' => 2
            ));

            $totalDiario = new ReportRawColumn(array(
                'value' => 'TOTAL DIARIO',
                'style' => $rightColumn,
                'span' => 2
            ));

            $totalCuenta = new ReportRawColumn(array(
                'value' => 'TOTAL CUENTA',
                'style' => $rightColumn,
                'span' => 2
            ));

            $report->start(true);

            $libro = array();
            $cuentas = array();
            if ($diarioInicial==0||$diarioFinal==0) {
                $diarios = $this->Diarios->find('order: codigo');
            } else {
                $diarios = $this->Diarios->find(array("codigo>=$diarioInicial AND codigo<=$diarioFinal", 'order' => 'codigo'));
            }

            if ($tipo == 'M') {
                $moviModel = $this->Movi;
            } else { 
                $moviModel = $this->MoviNiif;
            }

            foreach ($diarios as $diario) {
                $codigoDiario = $diario->getCodigo();
                foreach ($this->Comprob->find(array("diario='$codigoDiario'", 'order' => 'codigo')) as $comprob) {
                    $conditions = "comprob='{$comprob->getCodigo()}' AND numero>0 AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
                    foreach ($moviModel->find(array($conditions, 'columns' => 'cuenta,valor,deb_cre')) as $movi) {
                        $codigoCuenta = substr($movi->getCuenta(), 0, 4);
                        if (!isset($libro[$codigoDiario][$codigoCuenta])) {
                            $libro[$codigoDiario][$codigoCuenta] = array(
                                'debe' => 0,
                                'haber' => 0
                            );
                        }
                        if (!isset($cuentas[$codigoCuenta][$codigoDiario])) {
                            $cuentas[$codigoCuenta][$codigoDiario] = array(
                                'debe' => 0,
                                'haber' => 0
                            );
                        }
                        if ($movi->getDebCre()=='D') {
                            $libro[$codigoDiario][$codigoCuenta]['debe']+=$movi->getValor();
                            $cuentas[$codigoCuenta][$codigoDiario]['debe']+=$movi->getValor();
                        } else {
                            $libro[$codigoDiario][$codigoCuenta]['haber']+=$movi->getValor();
                            $cuentas[$codigoCuenta][$codigoDiario]['haber']+=$movi->getValor();
                        }
                        unset($codigoCuenta, $movi);
                    }
                    unset($conditions, $comprob);
                }
                unset($codigoDiario, $diario);
            }

            $subTotalColumnaDebitos = 0;
            $subTotalColumnaCreditos = 0;
            foreach ($libro as $codigoDiario => $libroDiario) {

                $diario = BackCacher::getDiario($codigoDiario);
                $columnaCuenta = new ReportRawColumn(array(
                    'value' => $diario->getCodigo().' : '.$diario->getNombre(),
                    'style' => $leftColumn,
                    'span' => 4
                ));
                $report->addRawRow(array($columnaCuenta));

                ksort($libroDiario, SORT_STRING);
                $totalDiarioDebitos = 0;
                $totalDiarioCreditos = 0;
                foreach ($libroDiario as $codigoCuenta => $libroCuenta) {
                    if ($tipo == 'M') {
                        $cuenta = BackCacher::getCuenta($codigoCuenta);
                    } else {
                        $cuenta = BackCacher::getCuentaNiif($codigoCuenta);
                    }

                    if ($cuenta==false) {
                        $report->addRow(array(
                            $codigoCuenta,
                            'NO EXISTE CUENTA',
                            $libroCuenta['debe'],
                            $libroCuenta['haber']
                        ));
                    } else {
                        $report->addRow(array(
                            $codigoCuenta,
                            $cuenta->getNombre(),
                            $libroCuenta['debe'],
                            $libroCuenta['haber']
                        ));
                    }
                    $totalDiarioDebitos+=$libroCuenta['debe'];
                    $totalDiarioCreditos+=$libroCuenta['haber'];
                }

                $columnaDebitos = new ReportRawColumn(array(
                    'value' => $totalDiarioDebitos,
                    'style' => $rightColumn,
                    'format' => $numberFormat
                ));
                $columnaCreditos = new ReportRawColumn(array(
                    'value' => $totalDiarioCreditos,
                    'style' => $rightColumn,
                    'format' => $numberFormat
                ));

                $report->addRawRow(array(
                    $totalDiario,
                    $columnaDebitos,
                    $columnaCreditos
                ));

                $subTotalColumnaDebitos += $totalDiarioDebitos;
                $subTotalColumnaCreditos += $totalDiarioCreditos;

                unset($libroDiario);
            }

            $columna = new ReportRawColumn(array(
                'value' => '',
                'style' => $leftColumn,
                'span' => 4
            ));
            $report->addRawRow(array($columna));

            $subTotalColumnaDebitosC = new ReportRawColumn(array(
                'value' => $subTotalColumnaDebitos,
                'style' => $rightColumn,
                'format' => $numberFormat
            ));
            $subTotalColumnaCreditosC = new ReportRawColumn(array(
                'value' => $subTotalColumnaCreditos,
                'style' => $rightColumn,
                'format' => $numberFormat
            ));


            $report->addRawRow(array(
                $subTotalDiario,
                $subTotalColumnaDebitosC,
                $subTotalColumnaCreditosC
            ));

            $report->finish();
            $fileName = $report->outputToFile('public/temp/libro-diario');

            return array(
                'status' => 'OK',
                'file' => 'temp/'.$fileName
            );
        } catch(Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

}
