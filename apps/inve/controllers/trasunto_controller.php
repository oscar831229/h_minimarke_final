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
 * TrasuntoController
 *
 * Trasunto de los Movimientos
 *
 */
class TrasuntoController extends ApplicationController
{

    /**
     * inicializacion de controlador
     * @return string
     */
    public function initialize()
    {
        $controllerRequest = ControllerRequest::getInstance();
        if ($controllerRequest->isAjax()) {
            View::setRenderLevel(View::LEVEL_LAYOUT);
        }
        parent::initialize();
    }

    /**
     * Controlador de plantilla de inicio
     * @return string
     */
    public function indexAction()
    {

        $empresa = $this->Empresa->findFirst();
        $fechaCierre = $empresa->getFCierrei();
        Tag::displayTo('lineaFinal', $this->Lineas->maximum('linea'));
        $fecha = new Date();
        Tag::displayTo('fechaFinal', $fecha->getDate());
        $fecha->diffMonths(1);
        Tag::displayTo('fechaInicial', $fecha->getDate());

        $this->setParamToView('fechaCierre', $fechaCierre);
        $this->setParamToView('lineas', $this->Lineas->count('group: linea,nombre'));
        $this->setParamToView('almacenes', $this->Almacenes->find(array(
            'estado = "A"',
            'order' => 'nom_almacen'
        )));

        $this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
    }

    /**
     * Genera el reporte de trasunto
     * @return array $return
     */
    public function generarAction()
    {

        $this->setResponse('json');

        $lineaInicial = $this->getPostParam('lineaInicial', 'alpha');
        $lineaFinal = $this->getPostParam('lineaFinal', 'alpha');

        if ($lineaInicial==''||$lineaFinal=='') {
            return array(
                'status' => 'FAILED',
                'message' => 'Indique los almacenes inicial y final del listado'
            );
        }

        $fechaInicial = $this->getPostParam('fechaInicial', 'date');
        $fechaFinal = $this->getPostParam('fechaFinal', 'date');

        if ($fechaInicial==''||$fechaFinal=='') {
            return array(
                'status' => 'FAILED',
                'message' => 'Indique las fechas inicial y final del listado'
            );
        }

        $empresa = $this->Empresa->findFirst();
        $fechaCierre = $empresa->getFCierrei();

        try {

            if (Date::isLater($fechaInicial, $fechaFinal)) {
                return array(
                    'status' => 'FAILED',
                    'message' => 'La fecha final debe ser posterior a la fecha inicial'
                );
            }

            $fechaInicial = new Date($fechaInicial);
            $fechaInicial->toFirstDayOfMonth();

            if (Date::isLater($fechaInicial, $fechaCierre)) {
                $fechaInicio = Date::addInterval($fechaCierre, 1, Date::INTERVAL_DAY);
            }

            $ultimoDia = clone $fechaCierre;
            $ultimoDia->addDays(1);
            $ultimoDia = (string) $ultimoDia;

            $fechaCierre->addDays(1);

            $fechaPeriodoAnt = clone $fechaInicial;
            $fechaPeriodoAnt->diffMonths(1);
            $periodoAnterior = $fechaPeriodoAnt->getPeriod();

        } catch (DateException $e) {
            return array(
                'status'  => 'FAILED',
                'message' => $e->getException()
            );
        }

        $almacenId = $this->getPostParam('almacen');
        $detallado = $this->getPostParam('detallado');

        $reportType = $this->getPostParam('reportType', 'alpha');
        $report = ReportBase::factory($reportType);

        $titulo = new ReportText('MOVIMIENTO DE ALMACÉN POR GRUPO', array(
            'fontSize' => 16,
            'fontWeight' => 'bold',
            'textAlign' => 'center'
        ));

        $titulo2 = new ReportText('Fechas: '.$fechaInicial.' - '.$fechaFinal, array(
            'fontSize' => 11,
            'fontWeight' => 'bold',
            'textAlign' => 'center'
        ));

        $report->setHeader(array($titulo, $titulo2));
        $report->setDocumentTitle('Movimiento de Almacén por Grupo');

        $columns = array(
          'ITEM',
          'REFERENCIA',
          'CANTIDAD',
          'VALOR',
          'CANTIDAD',
          'VALOR',
          'CANTIDAD',
          'VALOR',
          'CANTIDAD',
          'ENTRANTE',
          'SALIENTE',
          'CANTIDAD',
          'VALOR'
        );

        if ($detallado=="S") {
            $report->setColumnHeaders($columns);
        }

        $leftColumn = new ReportStyle(array(
            'textAlign' => 'left',
            'fontSize' => 11,
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

        $leftColumnBold = new ReportStyle(array(
            'textAlign' => 'left',
            'fontSize' => 11,
            'fontWeight' => 'bold'
        ));

        $headerColumnBold = new ReportStyle(array(
            'textAlign' => 'center',
            'fontSize' => 11,
            'fontWeight' => 'bold'
        ));

        $numberFormat = new ReportFormat(array(
            'type' => 'Number',
            'decimals' => 2
        ));

        $report->setCellHeaderStyle(new ReportStyle(array(
            'textAlign' => 'center',
            'backgroundColor' => '#eaeaea'
        )));

        $report->setColumnStyle(array(0, 1), new ReportStyle(array(
            'textAlign' => 'left',
            'fontSize' => 11
        )));

        $report->setColumnStyle(range(2, 12), new ReportStyle(array(
            'textAlign' => 'right',
            'fontSize' => 11,
        )));

        $report->setColumnFormat(range(2, 12), $numberFormat);
        $report->start(true);

        $headers = array();

        //decidimos cuantos espan si es no no es detallada
        if ($detallado=="S") {
            $span = 2;
        } else {
            $span = 1;
        }

        $headers[0] = new ReportRawColumn(array(
            'value' => 'REFERENCIA',
            'style' => $headerColumnBold,
            'span' => $span
        ));

        $headers[2] = new ReportRawColumn(array(
            'value' => 'SALDO ANTERIOR',
            'style' => $headerColumnBold,
            'span' => $span
        ));

        $headers[4] = new ReportRawColumn(array(
            'value' => 'ENTRADAS',
            'style' => $headerColumnBold,
            'span' => $span
        ));

        $headers[6] = new ReportRawColumn(array(
            'value' => 'SALIDAS',
            'style' => $headerColumnBold,
            'span' => $span
        ));

        $headers[8] = new ReportRawColumn(array(
            'value' => 'AJUSTES Y/O TRASLADOS',
            'style' => $headerColumnBold,
            'span' => ($span + 1)
        ));

        $headers[11] = new ReportRawColumn(array(
            'value' => 'NUEVO SALDO',
            'style' => $headerColumnBold,
            'span' => $span
        ));

        $report->addRawRow($headers);

        if ($almacenId != "@") {
            $almacenes = $this->Almacenes->find(array(
                'columns' => 'codigo,nom_almacen',
                'conditions' => 'codigo="' . $almacenId . '" AND estado="A"'
            ));
        } else {
            $almacenes = $this->Almacenes->find(array(
                'columns'  => 'codigo,nom_almacen',
                'conditions' => 'estado="A"'
            ));
        }

        //$periodoPasado = $mesPasado->getPeriod();

        $totInit = array('linea' => 0, 'almacen' => 0);
        $totEntradas = array('linea' => 0, 'almacen' => 0);
        $totSalidas = array('linea' => 0, 'almacen' => 0);
        $totEntradasAjT = array('linea' => 0, 'almacen' => 0);
        $totSalidasAjT = array('linea' => 0, 'almacen' => 0);
        $total = array('linea' => 0, 'almacen' => 0);
        foreach ($almacenes as $almacen) {

            $totInit['almacen'] = 0;
            $totEntradas['almacen'] = 0;
            $totSalidas['almacen'] = 0;
            $totEntradasAjT['almacen'] = 0;
            $totSalidasAjT['almacen'] = 0;
            $total['almacen'] = 0;

            $lineas = $this->Lineas->find(array(
                'columns' => 'linea,nombre',
                "conditions" => "almacen='{$almacen->getCodigo()}' AND linea BETWEEN '$lineaInicial' AND '$lineaFinal'"
            ));
            if (count($lineas) == 0) {
                continue;
            }

            foreach ($lineas as $linea) {

                $totInit['linea'] = 0;
                $totEntradas['linea'] = 0;
                $totSalidas['linea'] = 0;
                $totEntradasAjT['linea'] = 0;
                $totSalidasAjT['linea'] = 0;
                $total['linea'] = 0;
                $referencias = $this->Inve->find("linea='{$linea->getLinea()}'");
                if (count($referencias) == 0) {
                    continue;
                }

                foreach ($referencias as $inve) {

                    /*return array(
                        'status' => 'FAILED',
                        'message' => "'{$fechaInicial}' AND '{$fechaFinal}' AND periodo '{$periodoAnterior}'"
                    );*/

                    $entradas = array();
                    $salidas = array();
                    $entradasT = array();
                    $salidasT = array();
                    $entradasA = array();
                    $salidasA = array();

                    $conditionSaldo = "almacen='{$almacen->getCodigo()}' AND item = '{$inve->getItem()}' and ano_mes='$periodoAnterior'";
                    $saldo = $this->Saldos->findFirst($conditionSaldo);
                    if ($saldo) {
                        $saldoPasado = $saldo->getSaldo();
                        $costoPasado = $saldo->getCosto();
                    } else {
                        $saldoPasado = 0;
                        $costoPasado = 0;
                    }
                    /*if ($inve->getItem()=='792' && $almacen->getCodigo()==9) {
                        return array(
                            'status' => 'FAILED',
                            'message' => "saldoPasado: $saldoPasado , costoPasado: $costoPasado, conditionSaldo: $conditionSaldo"
                        );
                    }*/

                    //Entradas saldo anterior
                    $comprob = sprintf('E%02s', $almacen->getCodigo());
                    $conditionsArrayAnterior = array(
                        "fecha >= '{$ultimoDia}' AND fecha < '{$fechaInicial}'",
                        "item = '{$inve->getItem()}'",
                        "comprob = '$comprob'"
                    );
                    $conditions = join(' AND ', $conditionsArrayAnterior);
                    $saldoPasado += (int)   $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $costoPasado += (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    //Entradas
                    $conditionsArray = array(
                        "fecha >= '{$fechaInicial}' AND fecha <= '{$fechaFinal}'",
                        "item = '{$inve->getItem()}'",
                        "comprob = '$comprob'"
                    );
                    $conditions = join(' AND ', $conditionsArray);
                    /*return array(
                        'status' => 'FAILED',
                        'message' => $conditions
                    );*/
                    
                    $entradas['cantidad'] = (int) $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $entradas['valor']    = (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    //Salidas
                    $comprob = sprintf('C%02s', $almacen->getCodigo());

                    $conditionsArray[2] = "comprob='$comprob'";
                    $conditionsArrayAnterior[2] = "comprob='$comprob'";

                    $conditions = join(' AND ', $conditionsArrayAnterior);
                    $saldoPasado -= (int)   $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $costoPasado -= (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    $conditions = join(' AND ', $conditionsArray);
                    $salidas['cantidad'] = (int) $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $salidas['valor']    = (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    //Traslados entrantes
                    $conditionsArray[2] = "comprob LIKE 'T%'";
                    $conditionsArray[3] = "almacen_destino = '{$almacen->getCodigo()}'";
                    $conditionsArrayAnterior[2] = "comprob LIKE 'T%'";
                    $conditionsArrayAnterior[3] = "almacen_destino = '{$almacen->getCodigo()}'";

                    $conditions = join(' AND ', $conditionsArrayAnterior);
                    $saldoPasado += (int)   $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $costoPasado += (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    $conditions = join(' AND ', $conditionsArray);
                    $entradasT['cantidad'] = (int) $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $entradasT['valor']    = (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    //Traslados salientes
                    $conditionsArray[3] = "almacen_destino!='{$almacen->getCodigo()}'";
                    $conditionsArrayAnterior[3] = "almacen_destino!='{$almacen->getCodigo()}'";

                    $conditions = join(' AND ', $conditionsArrayAnterior);
                    $saldoPasado -= (int)   $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $costoPasado -= (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    $conditions = join(' AND ', $conditionsArray);
                    $salidasT['cantidad'] = (int)$this->Movilin->sum('cantidad', "conditions: $conditions");
                    $salidasT['valor'] = (float)$this->Movilin->sum('valor', "conditions: $conditions");

                    //Ajustes entrantes
                    $comprob = sprintf('A%02s', $almacen->getCodigo());
                    $conditionsArray[2] = "comprob = '$comprob'";
                    $conditionsArray[3] = 'valor > 0';
                    $conditionsArrayAnterior[2] = "comprob = '$comprob'";
                    $conditionsArrayAnterior[3] = 'valor > 0';

                    $conditions = join(' AND ', $conditionsArrayAnterior);
                    $saldoPasado += (int)   $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $costoPasado += (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    $conditions = join(' AND ', $conditionsArrayAnterior);
                    $entradasA['cantidad'] = (int) $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $entradasA['valor']    = (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    //Ajustes salientes
                    $conditionsArray[3] = 'valor < 0';
                    $conditionsArrayAnterior[3] = 'valor < 0';

                    $conditions = join(' AND ', $conditionsArrayAnterior);
                    $saldoPasado -= (int)   $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $costoPasado -= (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    $conditions = join(' AND ', $conditionsArray);
                    $salidasA['cantidad'] = (int) $this->Movilin->sum('cantidad', "conditions: $conditions");
                    $salidasA['valor']    = (float) $this->Movilin->sum('valor', "conditions: $conditions");

                    $totalCantidad        = $saldoPasado + $entradas['cantidad'] - $salidas['cantidad'] + $entradasT['cantidad'] + $entradasA['cantidad'] - ($salidasT['cantidad'] - $salidasA['cantidad']);
                    $totalValor           = $costoPasado + $entradas['valor'] - $salidas['valor'] + $entradasT['valor'] + $entradasA['valor'] - ($salidasT['valor'] - $salidasA['valor']);

                    $row = array(
                        $inve->getItem(),
                        $inve->getDescripcion(),
                        $saldoPasado,
                        $costoPasado,
                        $entradas['cantidad'],
                        $entradas['valor'],
                        $salidas['cantidad'],
                        $salidas['valor'],
                        $entradasT['cantidad'] + $entradasA['cantidad'] - ($salidasT['cantidad'] - $salidasA['cantidad']),
                        $entradasT['valor'] + $entradasA['valor'],
                        $salidasT['valor'] - $salidasA['valor'],
                        $totalCantidad,
                        $totalValor
                    );

                    if ($detallado == "S") {
                        $report->addRow($row);
                    }

                    $totInit['linea']        += $costoPasado;
                    $totEntradas['linea']    += $entradas['valor'];
                    $totSalidas['linea']     += $salidas['valor'];
                    $totEntradasAjT['linea'] += $entradasT['valor'] + $entradasA['valor'];
                    $totSalidasAjT['linea']  += $salidasT['valor'] - $salidasA['valor'];
                    $total['linea']          += $totalValor;
                }
                $totales = array();

                if ($detallado == "S") {
                    $styleColumn = $rightColumn;
                } else {
                    $styleColumn = $leftColumn;
                }

                $totales[0] = new ReportRawColumn(array(
                    'value' => $linea->getLinea().' - '.$linea->getNombre(),
                    'style' => $styleColumn,
                    'span' => $span
                ));

                $totales[2] = new ReportRawColumn(array(
                    'value' => $totInit['linea'],
                    'style' => $rightColumn,
                    'format' => $numberFormat,
                    'span' => $span
                ));

                $totales[4] = new ReportRawColumn(array(
                    'value' => $totEntradas['linea'],
                    'style' => $rightColumn,
                    'format' => $numberFormat,
                    'span' => $span
                ));

                $totales[6] = new ReportRawColumn(array(
                    'value' => $totSalidas['linea'],
                    'style' => $rightColumn,
                    'format' => $numberFormat,
                    'span' => $span
                ));

                $totales[8] = new ReportRawColumn(array(
                    'value' => $totEntradasAjT['linea'],
                    'style' => $rightColumn,
                    'format' => $numberFormat,
                    'span' => $span
                ));

                $totales[10] = new ReportRawColumn(array(
                    'value' => $totSalidasAjT['linea'],
                    'style' => $rightColumn,
                    'format' => $numberFormat,
                    'span' => 1
                ));

                $totales[11] = new ReportRawColumn(array(
                    'value' => $total['linea'],
                    'style' => $rightColumn,
                    'format' => $numberFormat,
                    'span' => $span
                ));

                $report->addRawRow($totales);

                $totInit['almacen']        += $totInit['linea'];
                $totEntradas['almacen']    += $totEntradas['linea'];
                $totSalidas['almacen']     += $totSalidas['linea'];
                $totEntradasAjT['almacen'] += $totEntradasAjT['linea'];
                $totSalidasAjT['almacen']  += $totSalidasAjT['linea'];
                $total['almacen']          += $total['linea'];
            }

            $totales = array();
            $totales[0] = new ReportRawColumn(array(
                'value' => $almacen->getCodigo().' - '.$almacen->getNomAlmacen(),
                'style' => $rightColumnBold,
                'span' => $span
            ));
            $totales[2] = new ReportRawColumn(array(
                'value' => $totInit['almacen'],
                'style' => $rightColumnBold,
                'format' => $numberFormat,
                'span' => $span
            ));

            $totales[4] = new ReportRawColumn(array(
                'value' => $totEntradas['almacen'],
                'style' => $rightColumnBold,
                'format' => $numberFormat,
                'span' => $span
            ));
            $totales[6] = new ReportRawColumn(array(
                'value' => $totSalidas['almacen'],
                'style' => $rightColumnBold,
                'format' => $numberFormat,
                'span' => $span
            ));
            $totales[8] = new ReportRawColumn(array(
                'value' => $totEntradasAjT['almacen'],
                'style' => $rightColumnBold,
                'format' => $numberFormat,
                'span' => $span
            ));
            $totales[10] = new ReportRawColumn(array(
                'value' => $totSalidasAjT['almacen'],
                'style' => $rightColumnBold,
                'format' => $numberFormat,
                'span' => 1
            ));
            $totales[11] = new ReportRawColumn(array(
                'value' => $total['almacen'],
                'style' => $rightColumnBold,
                'format' => $numberFormat,
                'span' => $span
            ));

            //if ($detallado=="S") {
                $report->addRawRow($totales);
            //}
        }
        foreach (array() as $movihead) {

            $nit = BackCacher::getTercero($movihead->getNit());
            if ($nit == false) {
                $tercero = 'NO EXISTE EL TERCERO';
            } else {
                $tercero = $nit->getNombre();
            }

            $row = array(
                $movihead->getComprob(),
                $movihead->getNumero(),
                $movihead->getFecha(),
                $tercero,
                $movihead->getVTotal(),
                $movihead->getIva(),
                $movihead->getIvad(),
                $movihead->getDescuento(),
                $movihead->getRetencion(),
                $movihead->getSaldo(),
                $movihead->getFacturaC()
            );

            //if ($detallado=="S") {
                $report->addRow($row);
            //}

        }

        $report->finish();
        $fileName = $report->outputToFile('public/temp/trasunto');

        return array(
            'status' => 'OK',
            'file' => 'temp/'.$fileName
        );
    }
}
