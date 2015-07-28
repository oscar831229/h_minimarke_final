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
 * Libro_TercerosController
 *
 * Libro Inventario y Balance con Terceros
 *
 */
class Libro_TercerosController extends ApplicationController
{

    private $_cuentasSinTerceros = array('9','2365','2367','24');

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

    public function generarAction()
    {

        $this->setResponse('json');

        try
        {

            $ano = $this->getPostParam('ano', 'int');

            $cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
            $cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

            $cuentaOrdenInicial = $this->getPostParam('cuentaOrdenInicial', 'cuentas');
            $cuentaOrdenFinal = $this->getPostParam('cuentaOrdenFinal', 'cuentas');

            if ($ano<=0) {
                return array(
                    'status' => 'FAILED',
                    'message' => 'Indique el año del reporte'
                );
            }

            if ($cuentaOrdenInicial=='') {
                return array(
                    'status' => 'FAILED',
                    'message' => 'Indique la cuenta de orden inicial'
                );
            }

            if ($cuentaOrdenFinal=='') {
                return array(
                    'status' => 'FAILED',
                    'message' => 'Indique la cuenta de orden final'
                );
            }

            $reportType = $this->getPostParam('reportType', 'alpha');
            $report = ReportBase::factory($reportType);

            // POR TERCEROS
            $titulo = new ReportText('LIBRO INVENTARIO Y BALANCE', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $fecha = Date::fromParts($ano, 01, 01);
            $ultimoDiaEnero = Date::getLastDayOfMonth($fecha->getMonth(), $fecha->getYear());

            $fecha2 = Date::fromParts($ano, 12, 31);
            $ultimoDiaDiciembre = Date::getLastDayOfMonth($fecha2->getMonth(), $fecha2->getYear());
            $periodoDiciembre = $fecha2->getPeriod();

            $desdeLabel = '';//'DE '.$fecha->getMonthName().' '.$fecha->getDay().' de '.$fecha->getYear();
            $hastaLabel = ' A '.$ultimoDiaDiciembre->getMonthName().' '.$ultimoDiaDiciembre->getDay().' de '.$ultimoDiaDiciembre->getYear();
            $titulo2 = new ReportText($desdeLabel.$hastaLabel, array(
                'fontSize' => 11,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo, $titulo2), false, true);

            // por Terceros
            $report->setDocumentTitle('Libro Inventario y Balance');
            $report->setColumnHeaders(array(
                'CUENTA',
                'TERCERO',
                'DESCRIPCIÓN',
                'VALOR PARCIAL',
                'VALOR PARCIAL',
                'VALOR PARCIAL',
                'VALOR PARCIAL',
                'VALOR PARCIAL',
                'NUEVO SALDO'
            ));

            $report->setColumnStyle(array(3, 4, 5, 6, 7, 8), new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11,
            )));

            $report->setColumnFormat(array(3, 4, 5, 6, 7, 8), new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 2
            )));

            $leftColumn = new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 12
            ));

            $rightColumn = new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 12
            ));

            $rightColumn2 = new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11
            ));

            $report->start(true);

            $comprobanteCierre = '';
            $periodo = $fecha->getPeriod();

            $fechaAnterior = Date::diffInterval($fecha, 1, Date::INTERVAL_MONTH);
            $periodoAnteriorAEnero = $fechaAnterior->getPeriod();

            $fechaAnterior2 = Date::diffInterval($fecha2, 1, Date::INTERVAL_MONTH);
            $periodoAnteriorADiciembre = $fechaAnterior2->getPeriod();

            $conditions = array();
            if (!empty($cuentaInicial) && !empty($cuentaFinal)) {
                $conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
            }
            if (!empty($cuentaOrdenInicial) && !empty($cuentaOrdenFinal)) {
                $conditions[] = "cuenta>='$cuentaOrdenInicial' AND cuenta<='$cuentaOrdenFinal'";
            }

            //OR es_auxiliar='S'
            $conditionsStr = '('.join(' OR ', $conditions).') AND es_auxiliar="S" ';

            $libro = array();

            foreach ($this->Cuentas->find(array('conditions'=>$conditionsStr)) as $cuenta) {
                $codigoCuenta = $cuenta->getCuenta();

                $saldosc = $this->Saldosc->findFirst("cuenta='$codigoCuenta' AND ano_mes='$periodoAnteriorAEnero'");
                #$saldosc = $this->Saldosc->findFirst(array("cuenta='$codigoCuenta'", 'order' => "ano_mes DESC"));

                if ($saldosc!=false) {
                    $libro[$codigoCuenta] = array(
                        'saldoAnterior' => $saldosc->getSaldo(),
                        'debitos' => 0,
                        'creditos' => 0,
                    );
                } else {
                    $libro[$codigoCuenta] = array(
                        'saldoAnterior' => 0,
                        'debitos' => 0,
                        'creditos' => 0
                    );
                }

                $conditions = "cuenta='$codigoCuenta' AND fecha>='$fecha' AND fecha<='$ultimoDiaDiciembre'";

                foreach ($this->Movi->find(array($conditions, 'columns' => 'valor,deb_cre')) as $movi) {
                    if ($movi->getDebCre()=='D') {
                        $libro[$codigoCuenta]['debitos']+=$movi->getValor();
                    } else {
                        $libro[$codigoCuenta]['creditos']+=$movi->getValor();
                    }
                    unset($movi);
                }

                $nuevoSaldo = $libro[$codigoCuenta]['saldoAnterior'] + $libro[$codigoCuenta]['debitos'] - $libro[$codigoCuenta]['creditos'];
                if ($nuevoSaldo==0) {
                    unset($libro[$codigoCuenta]);
                }

                unset($conditions);
                unset($nuevoSaldo);
                unset($cuenta);
                unset($saldosc);
                unset($codigoCuenta);
            }

            if (count($libro)) {

                $partes = array(
                    'tipo' => 1,
                    'mayor' => 2,
                    'clase' => 4,
                    'subclase' => 6,
                    'auxiliar' => 9,
                    'subauxiliar' => 12
                );

                foreach ($libro as $codigoCuenta => $libroCuenta) {

                    foreach ($partes as $tipoParte => $valorNivel) {
                        $length = strlen($codigoCuenta);
                        if ($length>$valorNivel) {
                            $parte = substr($codigoCuenta, 0, $valorNivel);
                            if ($parte!='') {
                                if (!isset($libro[$parte])) {
                                    $libro[$parte] = array(
                                        'saldoAnterior' => $libroCuenta['saldoAnterior'],
                                        'debitos'       => $libroCuenta['debitos'],
                                        'creditos'      => $libroCuenta['creditos']
                                    );
                                } else {
                                    $libro[$parte]['saldoAnterior'] += $libroCuenta['saldoAnterior'];
                                    $libro[$parte]['debitos']       += $libroCuenta['debitos'];
                                    $libro[$parte]['creditos']      += $libroCuenta['creditos'];
                                }
                            }
                            unset($parte);
                        }

                        unset($valorNivel);
                        unset($tipoParte);
                    }

                    unset($codigoCuenta);
                    unset($libroCuenta);
                }
            }

            ksort($libro, SORT_STRING);

            foreach ($libro as $codigoCuenta => $libroCuenta) {

                $cuenta = BackCacher::getCuenta($codigoCuenta);
                if ($cuenta==false) {
                    throw new Exception("La cuenta '$codigoCuenta' no existe!");
                }

                $row = array(
                    $codigoCuenta,
                    '',
                    $cuenta->getNombre(),
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                );

                $length = strlen($codigoCuenta);
                $valor = $libroCuenta['saldoAnterior'] + $libroCuenta['debitos'] - $libroCuenta['creditos'];

                if ($length==1) {
                    $row[8] = $valor;
                } else {
                    if ($length==2) {
                        $row[7] = $valor;
                    } else {
                        if ($length==4) {
                            $row[6] = $valor;
                        } else {
                            if ($length==6) {
                                $row[5] = $valor;
                            } else {
                                if ($length==9) {
                                    $row[4] = $valor;
                                } else {
                                    $row[3] = $valor;
                                }
                            }
                        }
                    }
                }

                if ($valor==0) {
                    continue;
                }

                $report->addRow($row);
                unset($row, $valor);

                if ($cuenta->getEsAuxiliar()=='S') {

                    if ($cuenta->getPideNit()=='S') {

                        //Verifica si la cuenta actual debe mostrar o no teceros (Mejora de Dora y Blasimir Morales)
                        if ($this->_checkCuentaSinTercero($codigoCuenta)==true) {
                            continue;
                        }

                        //PROPIEDADES PLANTA Y EQUIPO
                        if (substr($codigoCuenta, 0, 2)=='15') {
                            foreach ($this->Grupos->find(array('conditions'=>"cta_compra='$codigoCuenta'")) as $grupo) {
                                foreach ($grupo->getActivos() as $activo) {

                                    $report->addRow(array(
                                        '',
                                        '',
                                        $activo->getCodigo().' : '.$activo->getDescripcion(),
                                        $activo->getValorCompra(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        ''
                                    ));
                                    unset($activo);
                                }
                                unset($grupo);
                            }
                        }

                        $saldosnObj = $this->Saldosn->find(array('conditions'=>"cuenta='$codigoCuenta' AND ano_mes='$periodoDiciembre' AND saldo!=0", 'order'=>'CAST(nit AS SIGNED)'));

                        if (!count($saldosnObj)) {

                            $valMovi = array();
                            $conditions = "cuenta='$codigoCuenta' AND fecha>='$fecha' AND fecha<='$ultimoDiaDiciembre'";

                            foreach ($this->Movi->find(array($conditions, 'columns' => 'nit,valor,deb_cre', 'order'=>'CAST(nit AS SIGNED)')) as $movi) {
                                $nit = (float) $movi->getNit();

                                if (!isset($valMovi[$nit])) {
                                    $valMovi[$nit]=0;
                                }

                                if ($movi->getDebCre()=='D') {
                                    $valMovi[$nit]+=$movi->getValor();
                                } else {
                                    $valMovi[$nit]-=$movi->getValor();
                                }

                                unset($movi, $nit);
                            }

                            ksort($valMovi, SORT_NUMERIC);

                            foreach ($valMovi as $nit => $val) {
                                if ($val==0) {
                                    continue;
                                }

                                $tercero = BackCacher::getTercero($nit);

                                $col1   ='';
                                $col2   ='';
                                $col3   ='';
                                $col4   ='';
                                $col5   ='';
                                $col6   ='';

                                if ($length==1) {
                                    $col6 = $val;
                                } else {
                                    if ($length==2) {
                                        $col5 = $val;
                                    } else {
                                        if ($length==4) {
                                            $col4 = $val;
                                        } else {
                                            if ($length==6) {
                                                $col3 = $val;
                                            } else {
                                                if ($length==9) {
                                                    $col2 = $val;
                                                } else {
                                                    $col1 = $val;
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($tercero==false) {
                                    $nombre = 'NO EXISTE TERCERO';
                                } else {
                                    $nombre = $tercero->getNombre();
                                }
                                $cuentaRaw     = new ReportRawColumn(array('value' => '','style' => $leftColumn));
                                $nitRaw     = new ReportRawColumn(array('value' => $nit,'style' => $rightColumn));
                                $nombreRaw  = new ReportRawColumn(array('value' => $nombre,'style' => $leftColumn));
                                $col1Raw    = new ReportRawColumn(array('value' => Currency::money($col1),'style' => $rightColumn2));
                                $col2Raw    = new ReportRawColumn(array('value' => Currency::money($col2),'style' => $rightColumn2));
                                $col3Raw    = new ReportRawColumn(array('value' => Currency::money($col3),'style' => $rightColumn2));
                                $col4Raw    = new ReportRawColumn(array('value' => Currency::money($col4),'style' => $rightColumn2));
                                $col5Raw    = new ReportRawColumn(array('value' => Currency::money($col5),'style' => $rightColumn2));
                                $col6Raw    = new ReportRawColumn(array('value' => Currency::money($col6),'style' => $rightColumn2));

                                $rowTmp = array(
                                    $cuentaRaw,
                                    $nitRaw,
                                    $nombreRaw,
                                    $col1Raw,
                                    $col2Raw,
                                    $col3Raw,
                                    $col4Raw,
                                    $col5Raw,
                                    $col6Raw,
                                );

                                $report->addRawRow($rowTmp);
                                unset($tercero, $val, $rowTmp, $nitRaw, $nombreRaw, $col1Raw, $col2Raw, $col3Raw, $col4Raw, $col5Raw, $col6Raw);

                            }

                        } else {

                            //cuando hay en saldosn
                            foreach ($saldosnObj as $saldon) {
                                if ($saldon->getSaldo()==0) {
                                    continue;
                                }

                                $nit = $saldon->getNit();
                                $tercero = BackCacher::getTercero($nit);

                                $col1   = 0;
                                $col2   = 0;
                                $col3   = 0;
                                $col4   = 0;
                                $col5   = 0;
                                $col6   = 0;

                                if ($length==1) {
                                    $col6 = $saldon->getSaldo();
                                } else {
                                    if ($length==2) {
                                        $col5 = $saldon->getSaldo();
                                    } else {
                                        if ($length==4) {
                                            $col4 = $saldon->getSaldo();
                                        } else {
                                            if ($length==6) {
                                                $col3 = $saldon->getSaldo();
                                            } else {
                                                if ($length==9) {
                                                    $col2 = $saldon->getSaldo();
                                                } else {
                                                    $col1 = $saldon->getSaldo();
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($tercero==false) {
                                    $nombre = 'NO EXISTE TERCERO';
                                } else {
                                    $nombre = $tercero->getNombre();
                                }
                                $cuentaRaw     = new ReportRawColumn(array('value' => '','style' => $leftColumn));
                                $nitRaw     = new ReportRawColumn(array('value' => $nit,'style' => $leftColumn));
                                $nombreRaw  = new ReportRawColumn(array('value' => $nombre,'style' => $leftColumn));
                                $col1Raw    = new ReportRawColumn(array('value' => Currency::money($col1),'style' => $rightColumn2));
                                $col2Raw    = new ReportRawColumn(array('value' => Currency::money($col2),'style' => $rightColumn2));
                                $col3Raw    = new ReportRawColumn(array('value' => Currency::money($col3),'style' => $rightColumn2));
                                $col4Raw    = new ReportRawColumn(array('value' => Currency::money($col4),'style' => $rightColumn2));
                                $col5Raw    = new ReportRawColumn(array('value' => Currency::money($col5),'style' => $rightColumn2));
                                $col6Raw    = new ReportRawColumn(array('value' => Currency::money($col6),'style' => $rightColumn2));

                                $rowTmp = array(
                                    $cuentaRaw,
                                    $nitRaw,
                                    $nombreRaw,
                                    $col1Raw,
                                    $col2Raw,
                                    $col3Raw,
                                    $col4Raw,
                                    $col5Raw,
                                    $col6Raw,
                                );

 //throw new Exception(print_r($rowTmp,true));
                               $report->addRawRow($rowTmp);

                                unset($tercero, $saldon, $rowTmp, $nitRaw, $nombreRaw, $col1Raw, $col2Raw, $col3Raw, $col4Raw, $col5Raw, $col6Raw);
                            }
                            unset($saldosnObj);
                        }
                    }
                }
                unset($length);
            }

            $report->finish();
            $fileName = $report->outputToFile('public/temp/libro-terceros');
            unset($report);

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

    /**
     * Valida si la cuenta empieza con las cuentas que se define que no deben mostrar tercero
     * Ejemplo:
     *  Si la cuenta es 9105 y en cuentasinterceros hay un '9' no muestra ninguna cuenta que tenga 9xxxxxxx, es decir retorna false
     *
     * @return boolean
    */
    private function _checkCuentaSinTercero($codigoCuenta)
    {
        $flag = false;

        foreach ($this->_cuentasSinTerceros as $cuentaSinTercero) {

            $pattern = '/^'.$cuentaSinTercero.'/';
            preg_match($pattern, $codigoCuenta, $matches, PREG_OFFSET_CAPTURE, count($cuentaSinTercero)-1);

            if (count($matches)) {
                $flag = true;
            }

            unset($matches, $pattern, $cuentaSinTercero);
        }

        return $flag;
    }
}