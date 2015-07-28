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
 * Certificado_IcaController
 *
 * Certificados de Ica
 *
 */
class Certificado_IcaController extends ApplicationController
{
    private $ano;
    private $nits = array();
    private $bimestre;
    private $periodo;
    private $comprobCierre;
    private $nitInicial;
    private $nitFinal;
    private $fechaInicial;
    private $fechaFinal;
    private $cuentaInicial;
    private $cuentaFinal;
    private $fechaExpedicion;
    private $conditionIcaBase;
    private $retenciones = array();
    private $listaNits = array();

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
        $empresa     = $this->Empresa->findFirst();
        $empresa1    = $this->Empresa1->findFirst();
        $fechaCierre = $empresa->getFCierrec();
        $fechaCierre->addDays(1);
        Tag::displayTo('ano', $empresa1->getAnoc());
        Tag::displayTo('fechaExpedicion', Date::getCurrentDate());
        $this->setParamToView('fechaCierre', $fechaCierre);
        $this->setParamToView('anoCierre', $empresa1->getAnoc());
        $this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
    }

    private function inCuentasIca()
    {
        $icaObj = EntityManager::get("Ica")->find(array(
            'columns' => 'cuenta'
        ));
        foreach ($icaObj as $cuentaIca) {
            $codigoCuenta = $cuentaIca->getCuenta();
            $cuenta = BackCacher::getCuenta($codigoCuenta);
            if ($cuenta) {
                if ($cuenta->getPorcRetenc() > 0) {
                    $cuentasIca[] = $cuenta->getCuenta();
                    $cuentasIcaPorcRetenc[$codigoCuenta] = $cuenta->getPorcRetenc();
                } else {
                    throw new Exception("La cuenta '$codigoCuenta' no tiene registrado el porcentaje de ica", 1);
                }
            } else {
                throw new Exception("La cuenta '$codigoCuenta' registrada en cuentas ica no es valida", 1);
            }
            unset($cuentaIca, $cuenta, $codigoCuenta);
        }
        unset($icaObj);
        $inCuentasIca  = implode("','", $cuentasIca);

        return $inCuentasIca;
    }

    private function initializeParameters()
    {
        $this->ano = $this->getPostParam('ano', 'int');
        $this->bimestre = $this->getPostParam('bimestre', 'int');
        if ($this->bimestre < 7) {
            list($this->fechaInicial, $this->fechaFinal) = Date::getTwoMonths($this->ano, $this->bimestre);
        } else {
            $this->fechaInicial = Date::fromParts($this->ano, 1, 1);
            $this->fechaFinal   = Date::fromParts($this->ano, 12, 31);
        }
        $this->periodo         = $this->ano . '11';
        $this->comprobCierre   = Settings::get('comprob_cierre');
        $fechaExpedicion = $this->getPostParam('fechaExpedicion', 'date');
        $this->fechaExpedicion = new Date($fechaExpedicion);
        $this->cuentaInicial   = $this->getPostParam('cuentaInicial', 'int');
        $this->cuentaFinal     = $this->getPostParam('cuentaFinal', 'int');
        $this->nitInicial      = $this->getPostParam('nitInicial', 'int');
        $this->nitFinal        = $this->getPostParam('nitFinal', 'int');
        if (empty($this->cuentaInicial) || empty($this->cuentaFinal)) {
            throw new Exception('Debe ingresar el rango de cuentas de IVA retenido');
        }
        if (empty($this->nitInicial) || empty($this->nitFinal)) {
            throw new Exception('Debe ingresar el rango de tercero');
        }
        list($this->cuentaInicial, $this->cuentaFinal) = Utils::sortRange($this->cuentaInicial, $this->cuentaFinal);
        list($this->nitInicial, $this->nitFinal)       = Utils::sortRange($this->nitInicial, $this->nitFinal);
    }

    private function getConditionsCuentasIca()
    {
        $conditionsCuentas = "es_auxiliar = 'S'";
        if (!empty($this->cuentaInicial) && !empty($this->cuentaFinal)) {
            $conditionsCuentas = "cuenta >= '" . $this->cuentaInicial . "' AND cuenta <= '" . $this->cuentaFinal . "' " .
            "AND es_auxiliar = 'S'";
        }
        return $conditionsCuentas;
    }

    private function getNitsCuentasIca()
    {
        $inCuentasIca = $this->inCuentasIca();

        $this->conditionIcaBase = "fecha >= '" . $this->fechaInicial . "' AND fecha <= '" . $this->fechaFinal . "' " .
        "AND cuenta IN('" . $inCuentasIca . "')";

        $conditionsNits = "nit >= '" . $this->nitInicial . "' AND nit <= '" . $this->nitFinal . "' ".
            "AND fecha >= '" . $this->fechaInicial . "' AND fecha <= '" . $this->fechaFinal . "'" .
            "AND cuenta IN('" . $inCuentasIca . "')";
        $nits = $this->Movi->distinct(array("nit", "conditions" => $conditionsNits));
        if (!count($nits)) {
            throw new Exception("No se encontraton movimiento con las cuentas ica", 1);
        }
        $this->debugF("nits", $nits, true);
        $this->debugF("conditionsNits", $conditionsNits);
        $this->nits = $nits;
        return $this->nits;
    }

    private function addingRetencionICA()
    {
        if (count($this->nits)) {
            foreach ($this->nits as $nit) {
                $tercero = BackCacher::getTercero($nit);
                $conditionMoviICA = $this->conditionIcaBase . " AND nit = '$nit'";
                $this->debugF("conditionMoviICA", $conditionMoviICA);
                $this->getDatosIca($conditionMoviICA);
                //LISTA NITS
                if (!isset($this->listaNits[$nit])) {
                    $razonSocial = 'No existe en terceros!';
                    if ($tercero != false) {
                        $razonSocial = $tercero->getNombre();
                    }
                    $this->listaNits[$nit] = array(
                        'nit'         => $nit,
                        'razonSocial' => $razonSocial,
                        'sucursal'    => 01
                    );
                }
                unset($nit, $tercero, $conditionMoviICA);
            }
        }
    }

    public function generarAction()
    {
        set_time_limit(0);
        $this->setResponse('json');
        try {
            //Get params
            $this->initializeParameters();
            /**
             * ICA
             */
            //Cuentas
            $conditionsCuentas = $this->getConditionsCuentasIca();
            $cuentas = $this->Cuentas->find($conditionsCuentas);
            //Obtenemos Nits con movimiento de cuentas de ica
            $this->getNitsCuentasIca();
            //Add ICA retenecion
            $this->addingRetencionICA();
            /**
             * IVA
             */
            $conditionSaldosn = "ano_mes = 0";
            if ($this->nitInicial && $this->nitFinal) {
                $conditionSaldosn .= " AND nit >= '" . $this->nitInicial . "' AND nit <= '" . $this->nitFinal . "' AND ano_mes = 0";
            }
            $conditionsIva = "comprob != '" . $this->comprobCierre . "' AND fecha >= '" . $this->fechaInicial .
            "' AND fecha <= '" . $this->fechaFinal . "'";
            $this->debugF('conditionsIva', $conditionsIva);
            foreach ($cuentas as $cuenta) {
                $codigoCuenta = $cuenta->getCuenta();
                $porcIva      = $cuenta->getPorcIva();
                if ($porcIva <= 0) {
                    continue;
                }
                $saldosns 	  = $this->Saldosn->find($conditionSaldosn . " AND cuenta = '$codigoCuenta'");
                foreach ($saldosns as $saldosn) {
                    $nit = trim($saldosn->getNit());
                    if (!isset($baseIVA[$nit][$codigoCuenta])) {
	                	$baseIVA[$nit][$codigoCuenta] = array('porcIva' => 0, 'valor' => 0, 'base' => 0);
	                }
                    $conditionMovi = $conditionsIva . " AND nit = '$nit' AND cuenta = '$codigoCuenta'";
	                $debitos  = $this->Movi->sum("valor", $conditionMovi . " AND deb_cre = 'D'");
	                $creditos = $this->Movi->sum("valor", $conditionMovi . " AND deb_cre = 'C'");
	                $base     = $this->Movi->sum("base_grab", $conditionMovi . " AND base_grab > 0");
	                $diffMovi = $debitos - $creditos;
	                $baseIVA[$nit][$codigoCuenta]['valor']   += $diffMovi;
                	$baseIVA[$nit][$codigoCuenta]['porcIva'] = $porcIva;
                	$baseIVA[$nit][$codigoCuenta]['base']    += $base;
                	//RETENCIONES
                	$this->retenciones[$nit][$codigoCuenta]['IVA'] = $baseIVA[$nit][$codigoCuenta];
                	//LISTA NITS
                    if (!isset($this->listaNits[$nit])) {
                        $tercero = BackCacher::getTercero($nit);
                        $razonSocial = 'No existe en terceros!';
                        if ($tercero != false) {
                            $razonSocial = $tercero->getNombre();
                        }
                        $this->listaNits[$nit] = array(
                            'nit'         => $nit,
                            'razonSocial' => $razonSocial,
                            'sucursal'    => 01
                        );
                        unset($tercero);
                    }
                    unset($saldosn,$saldon,$nit);
                }
                unset($cuenta,$saldosns);
            }
            unset($cuentas);

            ksort($this->retenciones, SORT_NUMERIC);
            if (!count($this->retenciones)) {
                throw new Exception('No se encontró movimientos');
            }
            unset($bases);

            require 'Library/Mpdf/mpdf.php';
            $pdf = new mPDF();
            $pdf->tMargin = 10;
            $pdf->lMargin = 10;
            $pdf->SetDisplayMode('fullpage');
            $pdf->ignore_invalid_utf8 = true;
            $empresa    = EntityManager::get("Empresa")->findFirst();
            $empresaNit = EntityManager::get("Nits")->findFirst("nit='{$empresa->getNit()}'");
            if ($empresaNit == false) {
                throw new Exception('Debe crear el hotel como una tercero del sistema antes de continuar');
            }

            //$logoUrl = Utils::getExternalUrl('img/backoffice/logo.png');
            $logoUrl = 'public/img/backoffice/logo.png';

            $html = '<html>
                <head>
                    <style type="text/css">' . file_get_contents('public/css/hfos/certificado.css') . '</style>
                </head>
            <body>';

            $bimestres = array(
                '1' => 'PRIMER',
                '2' => 'SEGUNDO',
                '3' => 'TERCER',
                '4' => 'CUARTO',
                '5' => 'QUINTO',
                '6' => 'SEXTO',
                '9' => 'ACUMULADO DEL AÑO'
            );

            $nPage        = 0;
            $bimestreName = $bimestres[$this->bimestre];
            $limitPages   = count($this->retenciones);
            foreach ($this->retenciones as $nit => $cuentaBases) {
                $nPage++;
                $nit = trim($nit);
                $tercero = BackCacher::getTercero($nit);
                $totalRetencion = 0;
                $html.='
                    <div class="page">
                        <div class="header">
                            <table>
                                <tr>
                                    <td><img src="'.$logoUrl.'" width="80"/></td>
                                    <td>
                                        <h1>' . $empresa->getNombre() . '</h1>
                                        <h2>' . $empresaNit->getNit() . '</h2>
                                        <h3>' . $empresaNit->getDireccion() . '</h3>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="content">
                            <h3>CERTIFICADO DE RETENCION DEL ICA</h3>
                            <div class="paragraph">
                                CERTIFICAMOS QUE DURANTE EL PERIODO FISCAL DEL ' .$bimestreName.' BIMESTRE DE ' . $this->ano . '
                                LE EFECTUARON PAGOS A: ' . $tercero->getNombre() . ' CON NIT # ' . $tercero->getNit() . '
                                SOMETIDOS A RETENCION EN EL IMPUESTO SOBRE LAS VENTAS:
                            </div>
                            <table width="100%" class="conceptos" cellspacing="0">
                                <tr>
                                    <th>CONCEPTO</th>
                                    <th>VALOR RETENIDO</th>
                                    <th>%</th>
                                    <th>BASE</th>
                                </tr>
                                <tr>
                                    <td>RETENCIÓN DE IVA</td>
                                    <td align="right">' . Currency::number(abs($baseIVAF['valor'])) . '</td>
                                    <td align="right">' . $baseIVAF['porceIva'].'</td>
                                    <td align="right">' . Currency::number(abs($valorBaseIva)) . '</td>
                                </tr>';
                $currency = new Currency();
                foreach ($cuentaBases as $codigoCuenta => $cuentasTipo) {

                    //IVA
                    $baseIVAF = $cuentasTipo['IVA'];
                    $baseICAF = $cuentasTipo['ICA'];
                    $valorBaseIca = $baseICAF['base'];
                    //TOTAL
                    $totalRetencion = (double) $baseICAF['valor'] + $baseIVAF['valor'];
                    $cuentas = BackCacher::getCuenta($codigoCuenta);
                    $html .= '<tr>
                        <td>' . $cuentas->getNombre() . '</td>
                        <td align="right">' . Currency::number(abs($baseICAF['valor'])) . '</td>
                        <td align="right">' . $baseICAF['porcIca'] . '</td>
                        <td align="right">' . Currency::number(abs($baseICAF['base'])) . '</td>
                    </tr>';

                    unset($baseICAF, $baseIVAF);
                }
                $html .= '<tr>
                                <td align="right">TOTAL RETENCIÓN</td>
                                <td align="right">' . Currency::number(abs($totalRetencion)) . '</td>
                                <td align="right" colspan="2">&nbsp;</td>
                            </tr>
                        </table>
                        <div class="paragraph">Son: ' . $currency->getMoneyAsText($totalRetencion) . '</div>
                        <div class="paragraph">ESTOS VALORES FUERON CONSIGNADOS OPORTUNAMENTE EN LA DIRECCIÓN DE IMPUESTOS NACIONALES (DIAN) DE LA CIUDAD DE '.$empresaNit->getCiudadNombre().'</div>
                        <br/>
                        <div class="paragraph">Ciudad Donde se Practicó la Retención: ' . $empresaNit->getCiudadNombre() . '</div>
                        <div class="paragraph">Fecha Expedición: ' . $this->fechaExpedicion->getLocaleDate('long') . '</div>
                        <div class="paragraph">NO REQUIERE FIRMA AUTOGRAFA SEGUN DR 836/91 ART. 10</div>
                    </div>
                </div>';

                if ($limitPages > 1 && $nPage != $limitPages) {
                    $html.='<pagebreak />';
                }
            }

            $html .= '<pagebreak />';

            //LISTA NITS HTML
            $html .= '<table width="100%" class="conceptos" cellspacing="0">
                <tr>
                    <th>NIT</th>
                    <th>RAZÓN SOCIAL</th>
                    <th>SUCURSAL</th>
                </tr>';

            ksort($this->listaNits, SORT_NUMERIC);

            foreach ($this->listaNits as $info) {
                $html .= '<tr>
                    <td align="left">' . $info['nit'] . '</td>
                    <td align="left">' . $info['razonSocial'] . '</td>
                    <td align="center">' . $info['sucursal'] . '</td>
                </tr>';
            }

            $html .= '</table>';

            //FIN REPORTE
            $html .= '</body></html>';

            if (count($this->listaNits)>0) {

                $pdf->writeHTML($html);
                $fileName = 'certificados.' . mt_rand(1, 9999) . '.pdf';
                $pdf->Output('public/temp/' . $fileName);

                return array(
                    'status' => 'OK',
                    'file'   => 'temp/' . $fileName
                );
            } else {
                throw new Exception('No se encontró nits a mostrar');
            }
        } catch(Exception $e) {
            return array(
                'status'  => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

    public function getDatosIca($condition)
    {
        set_time_limit(0);

        $nit = '';
        $base = 0;
        $debitos = 0;
        $creditos = 0;
        $baseICA = array();
        $porceIca = array();
        $movis = $this->Movi->find(array(
            "conditions" => $condition,
            "columns" => "valor,nit,cuenta,deb_cre,base_grab"
        ));
        foreach ($movis as $movi) {
            $nit = $movi->getNit();
            $valor = $movi->getValor();
            $base += $movi->getBaseGrab();
            $codigoCuenta = $movi->getCuenta();
            if (!isset($baseICA[$codigoCuenta])) {
                $baseICA[$codigoCuenta] = array('porcIca' => 0, 'valor' => 0, 'base' => 0);
            }
            if ($movi->getDebCre() == 'D') {
                $debitos += $valor;
            } else {
                $creditos += $valor;
            }
            $cuentas = $this->Cuentas->findFirst("cuenta='$codigoCuenta'");
            if (!$cuentas) {
                throw new Exception("No existe la cuenta '$codigoCuenta'", 1);
            }
            $porcRetenc = $cuentas->getPorcRetenc();
            if (!isset($porceIca[$codigoCuenta])) {
                $porceIca[$codigoCuenta] = $porcRetenc;
            }
            if ($porceIca[$codigoCuenta] > 0) {

                $diffMovi = abs($debitos - $creditos);
                $porceIcaNit = $porceIca[$codigoCuenta];

                if (!isset($baseICA[$codigoCuenta])) {
                    $baseICA[$codigoCuenta] = array("valor" => 0, "porceIca" => 0, "base" =>0);
                }
                $baseICA[$codigoCuenta]['valor']   += $diffMovi;
                $baseICA[$codigoCuenta]['porcIca']  = $porceIcaNit;
                $baseICA[$codigoCuenta]['base']    += $diffMovi * $porceIcaNit / 100;
                //throw new Exception(print_r($baseICA[$codigoCuenta] , true));
                $this->retenciones[$nit][$codigoCuenta]['ICA'] = $baseICA[$codigoCuenta];
            } else {
                throw new Exception("La cuenta '$codigoCuenta' no tiene configurado el porcentaje de ica y tiene movimiento contable", 1);
            }
            unset($movi);
        }
        unset($movis);
        //throw new Exception(print_r($baseICA, true));

        return $baseICA;
    }

    public function debugF($title, $data, $new = false)
    {
        if ($new) {
            file_put_contents("/tmp/e.txt", $title . ":" . print_r($data, true));
        } else {
            file_put_contents("/tmp/e.txt", PHP_EOL . $title . ":" . print_r($data, true), FILE_APPEND);
        }
    }
}