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
 * @author      BH-TECK Inc. 2009-2014
 * @version     $Id$
 */

/**
 * ReciboCajaController
 *
 * Generación de recibos de caja en cartera
 */
class Recibo_cajaController extends ApplicationController
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
        $this->setParamToView('message', 'Ingrese un criterio de búsqueda para consultar recibos de caja');
    }

    public function buscarAction()
    {

        $this->setResponse('json');

        $nit = $this->getPostParam('tercero', 'Terceros');
        $numeroInicial = $this->getPostParam('numeroInicial', 'int');
        $numeroFinal = $this->getPostParam('numeroFinal', 'int');
        $estado = $this->getPostParam('estado', 'onechar');

        $response = array();

        $conditions = array();
        if ($nit>0) {
            $conditions[] = 'nit = \''.$nit.'\'';
        }
        if ($numeroInicial>0&&$numeroFinal>0) {
            $conditions[] = 'rc >= \''.$numeroInicial.'\' AND rc <= \''.$numeroFinal.'\'';
        }
        if ($estado!='@') {
            $conditions[] = 'estado = \''.$estado.'\'';
        }
        if (count($conditions)==0) {
            $conditions[] = '1=1';
        }

        //print_r($conditions);exit;
        if (count($conditions)>0) {
            $reccajObj = $this->Reccaj->find(array(join(' AND ', $conditions), 'order' => 'rc DESC'));
        } else {
            $reccajObj = $this->Reccaj->find(array('order' => 'rc DESC'));
        }
        if (count($reccajObj)==0) {
            $response['number'] = '0';
        } else {
            if (count($reccajObj)==1) {
                $reccaj = $reccajObj->getFirst();
                $response['number'] = '1';
                $response['key'] = 'id='.$reccaj->getId();
            } else {
                $responseResults = array(
                    'headers' => array(
                        array('name' => 'Rc', 'ordered' => 'S'),
                        array('name' => 'Tercero', 'ordered' => 'N'),
                        array('name' => 'Valor', 'ordered' => 'N', 'align' => 'right'),
                        array('name' => 'Estado', 'ordered' => 'N')
                    )
                );
                $data = array();
                foreach ($reccajObj as $reccaj) {
                    $tercero = BackCacher::getTercero($reccaj->getNit());
                    if (!$tercero) {
                        continue;
                    }

                    $valorReccaj = $reccaj->getValor();

                    $data[] = array(
                        'primary' => array('id='.$reccaj->getId()),
                        'data' => array(
                            array('key' => 'rc', 'value' => $reccaj->getRc()),
                            array('key' => 'tercero', 'value' => $tercero->getNit().' / '.$tercero->getNombre()),
                            array('key' => 'valor', 'value' => Currency::number($valorReccaj)),
                            array('key' => 'estado', 'value' => $reccaj->getEstado()),
                        )
                    );
                }
                $responseResults['data'] = $data;
                $response['numberResults'] = count($responseResults['data']);
                $response['results'] = $responseResults;
                $response['number'] = 'n';
            }
        }

        return $response;
    }

    public function verAction()
    {
        $reccajId = $this->getPostParam('id', 'int');
        if ($reccajId>0) {
            $reccaj = $this->Reccaj->findFirst($reccajId);
            if ($reccaj==false) {
                Flash::error('No existe el recibo de caja');
                $this->routeToAction('errores');
            }
            if ($reccaj->getEstado()=='A') {
                $this->setParamToView('message', 'El recibo de caja está anulado');
            }
            $new = $this->getPostParam('new', 'int');
            if ($new==true) {
                Flash::success('Se creó el recibo de caja correctamente');
            }
            $nits = $this->Nits->findFirst(array('conditions'=>"nit='{$reccaj->getNit()}'"));
            $comprob = $this->Comprob->findFirst(array('conditions'=>"codigo='{$reccaj->getComprob()}'"));
            $this->setParamToView('reccaj', $reccaj);
            $this->setParamToView('comprob', $comprob);
            $this->setParamToView('nits', $nits);
        } else {
            Flash::error('No existe el recibo de caja');
            $this->routeToAction('errores');
        }
    }

    public function erroresAction()
    {

    }

    public function nuevoAction()
    {
        $this->setParamToView('centros', $this->Centros->find("estado='A'", "order: nom_centro"));
        $this->setParamToView('formaPago', $this->FormaPago->find("estado='A'", "order: descripcion"));
        $this->setParamToView('message', 'Ingrese los datos del recibo de caja y la contabilización y haga click en "Grabar"');
    }

    /**
    * Genera recibo de caja contablemente
    */
    public function generarAction()
    {
        $this->setResponse('json');
        try {

            $transaction = TransactionManager::getUserTransaction();

            $rules = array(
                'nit' => array(
                    'message' => 'Debe indicar el tercero'
                )
            );
            if ($this->validateRequired($rules)==false) {
                foreach ($this->getValidationMessages() as $message) {
                    $transaction->rollback($message->getMessage());
                }
            } else {

                $nitNumero = $this->getPostParam('nit', 'terceros');
                $tercero = BackCacher::getTercero($nitNumero);

                $valor = $this->getPostParam('fp_valor', 'float');
                if (count($valor)<=0) {
                    $transaction->rollback('El valor del recibo de caja es inválido');
                }

                $cuenta = $this->getPostParam('cuenta', 'cuentas');
                $centroCosto = $this->getPostParam('centroCosto', 'int');

                //$comprobRc = Settings::get('comprob_rc');
                $comprobRc = $this->getPostParam('comprob', 'alpha');
                if ($comprobRc) {
                    $comprobRc = strtoupper($comprobRc);
                }
                //Se coge el numero de comprobante seleccionado como consecutivo de recibo de caja
                $comprob = EntityManager::get('Comprob')->setTransaction($transaction)->findFirst(array('conditions'=>"codigo='$comprobRc'"));
                if ($comprob==false) {
                    $transaction->rollback('El comprobante seleccionado para el recibos de caja no existe');
                }

                $fechaHoy = Date::getCurrentDate();
                $fechaMovi = $this->getPostParam('fechaMovi', 'date');
                $numeroDocumento = $this->getPostParam('numeroDocumento', 'int');

                try
                {
                    $aura = new Aura($comprobRc, 0, null, Aura::OP_CREATE);

                    $fCuentas = $this->getPostParam('f_cuenta', 'cuentas');
                    $fCentroCostos = $this->getPostParam('f_centroCosto', 'int');
                    $fValor2 = $this->getPostParam('f_valor2', 'double');
                    $fNaturaleza = $this->getPostParam('f_naturaleza', 'onechar');
                    $fNit = $this->getPostParam('f_nit2', 'terceros');
                    $fDescripcion = $this->getPostParam('f_descripcion');

                    $valorTotal = 0;

                    //Abono a cartera
                    $numeroDocs = $this->getPostParam('numeroDoc');
                    $tipoDocDefault = '';
                    if (is_array($numeroDocs)) {
                        $numeroDocumentos = count($numeroDocs);
                        for ($i=0; $i<$numeroDocumentos; $i++) {

                            $numeroDoc = explode('_', $numeroDocs[$i]);

                            $numeroDoc[0] = $this->filter($numeroDoc[0], 'alpha');//Tipo_doc
                            $numeroDoc[1] = $this->filter($numeroDoc[1], 'int');//Numero_doc
                            $numeroDoc[2] = $this->filter($numeroDoc[2], 'cuentas');//Cuenta

                            $cartera = $this->Cartera->findFirst("nit='$nitNumero' AND tipo_doc='{$numeroDoc[0]}' AND numero_doc='{$numeroDoc[1]}'");
                            if ($cartera!=false) {
                                $documento = $this->Documentos->findFirst(array('conditions'=>"codigo='{$numeroDoc[0]}'"));
                                $abonosCartera = $this->getPostParam('abono'.$numeroDocs[$i], 'double');
                                foreach ($abonosCartera as $valorAbonoCartera) {
                                    if ($valorAbonoCartera!=0) {

                                        $debCre = '';
                                        if ($valorAbonoCartera>-1) {
                                            $debCre = 'C';
                                            $valorTotal += $valorAbonoCartera;
                                        } else {
                                            $debCre = 'D';
                                        }
                                        if (!isset($numeroDoc[1]) || empty($numeroDoc[1])) {
                                            $numeroDoc[0] = 'CXS';
                                            $numeroDoc[1] = $numeroDocumento;
                                        }

                                        $aura->addMovement(array(
                                            'Fecha'             => $fechaMovi,
                                            'Cuenta'            => $numeroDoc[2],
                                            'Nit'               => $nitNumero,
                                            'CentroCosto'       => $cartera->getCentroCosto(),
                                            'Descripcion'       => 'PAGO A '.$documento->getNomDocumen().' #'.$numeroDoc[0].'-'.$numeroDoc[1],
                                            'Valor'             => abs($valorAbonoCartera),
                                            'TipoDocumento'     => $numeroDoc[0],
                                            'NumeroDocumento'   => $numeroDoc[1],
                                            'DebCre'            => $debCre
                                        ));

                                    }
                                }

                            } else {
                                $transaction->rollback("No se encontro el documento ".$numeroDoc[0].'-'.$numeroDoc[1]);
                            }

                            $tipoDocDefault = $numeroDoc[0];
                        }
                    }

                    //add movi at contabilidad tab
                    if (is_array($fCuentas)) {
                        if (!isset($numeroDoc[1]) || empty($numeroDoc[1])) {
                            $numeroDoc[0] = 'CXS';
                            $numeroDoc[1] = $numeroDocumento;
                        }
                        $numeroContras = count($fCuentas);
                        for ($i=0; $i<$numeroContras; $i++) {
                            $aura->addMovement(array(
                                'Fecha'             => $fechaMovi,
                                'Cuenta'            => $fCuentas[$i],
                                'Nit'               => $fNit[$i],
                                'CentroCosto'       => $fCentroCostos[$i],
                                'Descripcion'       => $fDescripcion[$i],
                                'Valor'             => $fValor2[$i],
                                'DebCre'            => $fNaturaleza[$i],
                                'TipoDocumento'     => $numeroDoc[0],
                                'NumeroDocumento'   => $numeroDoc[1]
                            ));

                            if ($fNaturaleza[$i]=='C') {
                                $valorTotal += $fValor2[$i];
                            }

                        }

                    }

                    //Formas de Pago
                    $fpFormaPago    = $this->getPostParam('fp_formaPago', 'int');
                    $fpNumero       = $this->getPostParam('fp_numero', 'int');
                    $fpDescripcion  = $this->getPostParam('fp_descripcion');
                    $fpValor        = $this->getPostParam('fp_valor', 'double');

                    if (count($fpFormaPago)>0 and is_array($fpFormaPago)==true) {

                        //throw new Exception(print_r($fpDescripcion,true));
                        foreach ($fpFormaPago as $index => $formaPagoId) {
                            if (!$formaPagoId) {
                                continue;
                            }

                            $formaPago = EntityManager::get('FormaPago')->findFirst($formaPagoId);
                            if ($formaPago==false) {
                                $transaction->rollback('grabarReciboCaja: No existe la forma de pago con id: '.$formaPagoId);
                            }
                            if (!$formaPago->getCtaContable()) {
                                $transaction->rollback('grabarReciboCaja: Es necesario definir la cuenta contable de la forma de pago: '.$formaPago->getDescripcion());
                            }

                            $descripcion = 'ABONO A RC POR FORMA DE PAGO ('.strtoupper($formaPago->getDescripcion()).')';
                            if (isset($fpDescripcion[$index]) && !empty($fpDescripcion[$index])) {
                                $descripcion = $fpDescripcion[$index];
                            }

                            //throw new Exception($descripcion.": ".is_array($fpDescripcion));


                            if (isset($fpNumero[$index])==true && $fpNumero[$index]) {
                                $descripcion.=' numero:'.$fpNumero[$index];
                            }
                            $aura->addMovement(array(
                                'Fecha'             => $fechaMovi,
                                'Cuenta'            => $formaPago->getCtaContable(),
                                'CentroCosto'       => $fCentroCostos[$i-1],
                                'Nit'               => $nitNumero,
                                'Descripcion'       => $descripcion,
                                'Valor'             => $fpValor[$index],
                                'DebCre'            => 'D',
                                'NumeroDocumento'   => $numeroDocumento,
                                'TipoDocumento'     => $tipoDocDefault
                            ));

                            //$valorTotal += $fpValor[$index];
                        }
                    }

                    $aura->save();
                    $rc = $aura->getConsecutivo($comprobRc);
                } catch(AuraException $e) {
                    $transaction->rollback('Error al grabar comprobante: '.$e->getMessage());
                }

                $fecha = $this->getPostParam('fecha', 'date');
                $beneficiario = $this->getPostParam('beneficiario');
                $observacion = $this->getPostParam('observacion');

                $reccaj = new Reccaj();
                $reccaj->setTransaction($transaction);
                $reccaj->setNit($nitNumero);
                $reccaj->setNombre(strtoupper($beneficiario));
                $reccaj->setDireccion($tercero->getDireccion());
                $reccaj->setCiudad($tercero->getLocciu());
                $reccaj->setTelefono($tercero->getTelefono());
                $reccaj->setFecha($fechaHoy);
                $reccaj->setComprob($comprobRc);
                $reccaj->setNumero($aura->getConsecutivo());
                $identity = IdentityManager::getActive();
                $reccaj->setCodusu($identity['id']);//De session
                $reccaj->setObservaciones($observacion);
                //$transaction->rollback("valorTotal: ".$valorTotal);
                $reccaj->setValor($valorTotal);
                $reccaj->setEstado('C');
                $reccaj->setRc($rc);

                if ($reccaj->save()==false) {
                    foreach ($reccaj->getMessages() as $message) {
                        $transaction->rollback('Reccaj: '.$message->getMessage());
                    }
                }

                if (is_array($fpFormaPago)==true && count($fpFormaPago)>0) {
                    foreach ($fpFormaPago as $index => $formaPagoId) {
                        if (!$formaPagoId) {
                            continue;
                        }
                        $detalleReccaj = new DetalleReccaj();
                        $detalleReccaj->setTransaction($transaction);
                        $detalleReccaj->setReccajId($reccaj->getId());
                        $detalleReccaj->setFormaPagoId($formaPagoId);
                        $detalleReccaj->setNumero($fpNumero[$index]);
                        $detalleReccaj->setValor($fpValor[$index]);
                        if ($detalleReccaj->save()==false) {
                            foreach ($detalleReccaj->getMessages() as $message) {
                                $transaction->rollback('DetalleReccaj: '.$message->getMessage());
                            }
                        }
                    }
                }

                //Abono a cartera
                $numeroDocs = $this->getPostParam('numeroDoc');
                if (is_array($numeroDocs)) {
                    $numeroDocumentos = count($numeroDocs);
                    for ($i=0; $i<$numeroDocumentos; $i++) {

                        $numeroDoc = explode('_', $numeroDocs[$i]);

                        $numeroDoc[0] = $this->filter($numeroDoc[0], 'alpha');
                        $numeroDoc[1] = $this->filter($numeroDoc[1], 'int');

                        $cartera = $this->Cartera->findFirst("nit='$nitNumero' AND tipo_doc='{$numeroDoc[0]}' AND numero_doc='{$numeroDoc[1]}'");
                        if ($cartera!=false) {
                            $documento = $this->Documentos->findFirst(array('conditions'=>"codigo='{$numeroDoc[0]}'"));
                            $abonosCartera = $this->getPostParam('abono'.$numeroDocs[$i], 'double');
                            foreach ($abonosCartera as $valorAbonoCartera) {
                                if ($valorAbonoCartera>0) {
                                    $detalleReccaj = new DetalleReccaj();
                                    $detalleReccaj->setTransaction($transaction);
                                        $detalleReccaj->setReccajId($reccaj->getId());
                                    //$detalleReccaj->setFormaPagoId(5);//Caja Menor
                                    $detalleReccaj->setNumero(0);
                                    $detalleReccaj->setValor($valorAbonoCartera);
                                    if ($detalleReccaj->save()==false) {
                                        foreach ($detalleReccaj->getMessages() as $message) {
                                            $transaction->rollback('DetalleReccaj: '.$message->getMessage());
                                        }
                                    }
                                }
                            }

                        } else {
                            $transaction->rollback("No se encontro el documento ".$numeroDoc[0].'-'.$numeroDoc[1]);
                        }
                    }
                }


                $transaction->commit();

                return array(
                    'status' => 'OK',
                    'id' => 'id='.$reccaj->getId()
                );
            }
        } catch(Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

    public function mostrarAction()
    {

    }

    public function anularAction()
    {
        $this->setResponse('json');
        try
        {
            $transaction = TransactionManager::getUserTransaction();
            $this->Reccaj->setTransaction($transaction);
            $reccajId = $this->getPostParam('id', 'int');
            if ($reccajId>0) {
                $reccaj = $this->Reccaj->findFirst($reccajId);
                if ($reccaj==false) {
                    $transaction->rollback('El recibo de caja no ha sido emitido');
                } else {
                    if ($reccaj->getEstado()=='A') {
                        $transaction->rollback('El recibo de caja ya había sido anulado');
                    }
                    $reccaj->setEstado('A');
                    if ($reccaj->save()==false) {
                        foreach ($reccaj->getMessages() as $message) {
                            $transaction->rollback('ReciboCaja: '.$message->getMessage());
                        }
                    }
                    try
                    {
                        $aura = new Aura($reccaj->getComprob(), $reccaj->getNumero(), null, Aura::OP_CREATE);
                        $aura->delete();
                    } catch(AuraException $e) {
                        $transaction->rollback('Eliminando Comprobante: '.$e->getMessage());
                    }
                    $transaction->commit();
                    return array(
                        'status' => 'OK',
                        'message' => 'Se anuló el recibo de caja correctamente'
                    );
                }
            } else {
                $transaction->rollback('El recibo de caja no ha sido emitido');
            }
        } catch(TransactionFailed $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

    public function getCarteraAction()
    {
        $this->setResponse('json');
        $documentos = array();
        $nitTercero = $this->getPostParam('nit', 'alpha');
        if ($nitTercero!='') {
            foreach ($this->Documentos->find(array('conditions' => 'cartera="S"')) as $documento) {
                $carteraObj = $this->Cartera->find(array("nit='$nitTercero' AND tipo_doc='{$documento->getCodigo()}' AND saldo!=0", "order" => "f_emision ASC"));
                foreach ($carteraObj as $cartera) {
                    $documentos[] = array(
                        'tipoDoc' => $cartera->getTipoDoc(),
                        'numeroDoc' => $cartera->getNumeroDoc(),
                        'cuenta' => $cartera->getCuenta(),
                        'fEmision' => (string)$cartera->getFEmision(),
                        'fVence' => (string)$cartera->getFVence(),
                        'saldo' => Currency::number(abs($cartera->getSaldo())),
                        'saldoValor' => $cartera->getSaldo()
                    );
                    unset($cartera);
                }
                unset($documento,$carteraObj);
            }
        }
        return array(
            'status' => 'OK',
            'documentos' => $documentos
        );
    }

    private function _printElement($pdf, $coord, $text)
    {
        $length = strlen($text)*0.25;
        return '<div style="font-family:monospace;position:absolute;top:'.$coord['y'].'cm;left:'.$coord['x'].'cm;width:'.$length.'cm">'.$text.'</div>';
    }

    public function imprimirAction()
    {

        $this->setResponse('view');

        $reccajId = $this->getQueryParam('id', 'int');
        if ($reccajId>0) {
            $reccaj = $this->Reccaj->findFirst($reccajId);
            if ($reccaj==false) {
                View::setRenderLevel(View::LEVEL_NO_RENDER);
                Flash::error('El recibo de caja no existe');
                return;
            }
        } else {
            View::setRenderLevel(View::LEVEL_NO_RENDER);
            Flash::error('El recibo de caja no existe');
            return;
        }

        $detalleReccajObj = EntityManager::get('DetalleReccaj')->find(array('conditions'=>"reccaj_id=".$reccaj->getId()." AND forma_pago_id>0"));

        $currency = new Currency();
        $fechaRc = $reccaj->getFecha();
        $suma = $currency->getMoneyAsText($reccaj->getValor(), 'PESOS', 'CENTAVOS');

        $empresa = EntityManager::get('Empresa')->findFirst();
        $empresaNits = EntityManager::get('Nits')->findFirst(array('conditions'=>"nit='{$empresa->getNit()}'"));
        $ciudadEmpresaNits = '';
        if ($empresaNits!=false && $empresaNits->getLocciu()>0) {
            $ciudadEmpresaNits = $empresaNits->getLocation()->getName();
        }

        $reccajNit = EntityManager::get('Nits')->findFirst(array('conditions'=>"nit='{$reccaj->getNit()}'"));
        $ciudadReccajNit = '';
        if ($reccajNit!=false && $reccajNit->getLocciu()>0) {
            $ciudadReccajNit = $reccajNit->getLocation()->getName();
        }

        try {

            require 'Library/Mpdf/mpdf.php';
            $pdf = new mPDF();
            $pdf->tMargin = 0;
            $pdf->lMargin = 0;
            $pdf->ignore_invalid_utf8 = true;

            $identity = IdentityManager::getActive();
            $usuarios = EntityManager::get('Usuarios')->findFirst($reccaj->getCodusu());

            $movimientoHtml = '<table width="100%" class="con">';
            $moviObj = $this->Movi->find(array('conditions'=>"comprob='{$reccaj->getComprob()}' AND numero='{$reccaj->getNumero()}'"));
            foreach ($moviObj as $movi) {
                $movimientoHtml .= "<tr>
                    <td>{$movi->getCuenta()}</td>
                    <td>{$movi->getDescripcion()}</td>
                    <td>{$movi->getValor()}</td>
                    <td>{$movi->getDebCre()}</td>
                </tr>";
            }
            $movimientoHtml .= '</table>';


            $html = '
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
        <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache"/>
        <style type="text/css">
            @font-face {
                font-family: "Anivers";
                src: local("Anivers"),
                     url("font/Fonts/Anivers.otf") format("opentype");
            }
            @media print {
                body {
                    background: white;
                    margin: 0px;
                }
                table {
                    width: 100%;
                }
                .tmain {
                    background: white;
                    border: 1px solid #969696;
                    -moz-border-radius: 7px;
                }
                .t1 {
                    font-size: 16px;
                    font-family: "Anivers", Verdana;
                    color: #000;
                }
                div, span {
                    font-family: Verdana;
                    font-size: 10px;
                    color: #000;
                }
                .numrec {
                    font-family: "Anivers", Courier New;
                    font-size: 24px;
                    color: #940000;
                }
                .lab {
                    font-weight: bold
                }
                .con {
                    font-family: "Anivers";
                    font-size: 10px;
                }
                .b {
                    border: 1px solid #969696;
                }
                p.page { page-break-after: always; }
            }
            @media screen {
                .tmain {
                    background: #F9FFF9;
                    border: 1px solid #586058;
                    -moz-border-radius: 7px;
                }
                .t1 {
                    font-size: 16px;
                    font-family: "Anivers", Verdana;
                    color: #005500;
                }
                div, span {
                    font-family: Verdana;
                    font-size: 11px;
                    color: #506E1E;
                }
                .numrec {
                    font-family: "Anivers", Courier New;
                    font-size: 20px;
                    color: #E80202;
                }
                .lab {
                    font-weight: bold
                }
                .con {
                    font-family: "Lucida Console";
                    font-size: 10px;
                }
                .b {
                    border-right: 1px solid #7A9855;
                    border-top: 1px solid #7A9855;
                }
                .bt {
                    border-left: 1px solid #7A9855;
                    border-bottom: 1px solid #7A9855;
                    -moz-border-radius: 7px;
                }
                p.page { page-break-after: always; }
            }
        </style>
        <title>Recibo de Caja '.$reccaj->getRc().'</title>
    </head>
    <body bgcolor="white">';

            $userName = '';
            if ($usuarios) {
                $userName = $usuarios->getNombres().' '.$usuarios->getApellidos();
            }

            #Content
            $content .= '
                <table width="650" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <table cellpadding="10">
                                <tr>
                                    <td width="128"><img src="public/img/backoffice/logo.jpg" height="80" /></td>
                                    <td valign="top" width="350">
                                        <span class="t1">'.$empresa->getNombre().'</span><br>
                                        <span>
                                            NIT '.$empresa->getNit().'<br>
                                            '.$empresaNits->getDireccion().'<br>
                                            Teléfono: (091) '.$empresaNits->getTelefono().'<br>
                                            Fax: (091) '.$empresaNits->getFax().'<br>
                                            '.$ciudadEmpresaNits.'<br>
                                        </span>
                                    </td>
                                    <td align="center" width="210">
                                        <span class="t1" style="color:black">RECIBO DE CAJA</span><br>
                                        <span class="numrec">
                                            <b>No. '.$reccaj->getComprob().'/'.$reccaj->getNumero().'</b>
                                        </span><br>
                                        <span style="font-size:9px">IVA REGIMEN COMUN<br>NO SOMOS GRANDES CONTRIBUYENTES</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="640" cellspacing="0" cellpadding="3" class="bt">
                                <tr>
                                    <td colspan="2" class="b" bgcolor="#C2E0AF">
                                        <span class="lab">Comprobante</span>
                                        <span class="con">'.$reccaj->getComprob().'</span>
                                    </td>
                                    <td class="b"><span class="lab">Fecha</span>
                                        <span class="con">'.$reccaj->getFecha().'</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="b">
                                        <span class="lab">Recibimos de</span>
                                        <span class="con">'.$reccaj->getNombre().'</span>
                                    </td>
                                    <td class="b">
                                        <span class="lab">C.C o NIT</span>
                                        <span class="con">'.$reccaj->getNit().'</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="b">
                                        <span class="lab">Dirección</span>
                                        <span class="con">'.$reccajNit->getDireccion().'</span>
                                    </td>
                                    <td class="b">
                                        <span class="lab">Ciudad</span>
                                        <span class="con">'.$ciudadReccajNit.'</span>
                                    </td>
                                    <td class="b">
                                        <span class="lab">Teléfono</span>
                                        <span class="con">'.$reccajNit->getTelefono().'&nbsp;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="b" colspan="3" bgcolor="#C2E0AF">
                                        <span class="lab">La suma de</span>
                                        <span class="con">'.$suma.'</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="b" colspan="3" bgcolor="#C2E0AF"><span class="lab">Por concepto de</span></td>
                                </tr>
                                <tr>
                                    <td class="b" colspan="3">
                                        <span class="con">'.$reccaj->getObservaciones().'</span>
                                        <br/><br/>
                                        '.$movimientoHtml.'
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="b">
                                        <br>
                                        <table cellpadding="0" align="center" cellspacing="0" class="bt">
                                            <tr bgcolor="#C2E0AF">
                                                <td align="center" class="b"><span class="lab">Forma de Pago</span></td>
                                                <td align="center" class="b"><span class="lab">N&uacute;mero</span></td>
                                                <td align="center" class="b"><span class="lab">Valor</span></td>
                                            </tr>';
            if (count($detalleReccajObj)) {
                foreach ($detalleReccajObj as $detalleReccaj) {

                    $formaPagoDesc = 'CARTERA';
                    if ($detalleReccaj->getFormaPagoId()>0) {
                        $formaPago = $this->FormaPago->findFirst($detalleReccaj->getFormaPagoId());

                        if ($formaPago!=false) {
                            $formaPagoDesc = $formaPago->getDescripcion();
                        }
                    }

                    $content .='
                        <tr>
                            <td align="left" class="b">
                                <span class="con" style="text-decoration:none">
                                    '.$formaPagoDesc.'
                                </span>
                            </td>
                            <td align="center" class="b">
                                <span class="con" style="text-decoration:none">'.$detalleReccaj->getNumero().'</span>
                            </td>
                            </td>
                                <td align="right" class="b">
                                    <span class="con" style="text-decoration:none">
                                        &nbsp;'.Currency::money($detalleReccaj->getValor()).'
                                    </span>
                                </td>
                        </tr>';
                }
            } else {
                //OTROS
                $content .='
                            <tr>
                                <td align="left" class="b">
                                    <span class="con" style="text-decoration:none">
                                        OTROS
                                    </span>
                                </td>
                                <td align="center" class="b">
                                    <span class="con" style="text-decoration:none"></span>
                                </td>
                                </td>
                                    <td align="right" class="b">
                                        <span class="con" style="text-decoration:none">
                                            &nbsp;'.Currency::money($reccaj->getValor()).'
                                        </span>
                                    </td>
                            </tr>';
            }

                                $content .='
                                            <tr bgcolor="" id="tr">
                                                <td align="left" class="b" colspan=2 bgcolor="#C2E0AF"><b>TOTAL</b></td>
                                                <td align="center" class="b">
                                                    <span style="font-size:16px">$&nbsp;'.Currency::money($reccaj->getValor()).'</span>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                    </td>
                                </tr>
                            </table>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="b" style="background:#F2F2F2">
                            <span class="lab">Cajero </span><span class="con">'.$identity['nombres'].' '.$identity['apellidos'].'</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="b" style="background:#F2F2F2">
                            <span class="lab">Vendedor </span><span class="con">'.$userName.'</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="b" style="background:#F2F2F2" align="right">
                            <span class="lab">Fecha Impresión:</span><span class="con">'.date('Y-m-d H:m').'</span>
                        </td>
                    </tr>
                </table>
                <br/>';

                $html .= $content;

                $breakCount = count($detalleReccajObj)+count($moviObj);

            if ($breakCount>7) {
                $html .= '<pagebreak />';
            }
            $html .= '<hr/>';

            $html .= $content;
            $html .= '
            </body>
        </html>';


            $pdf->writeHTML($html);
            $pdf->Output('public/temp/reccaj-'.$reccaj->getId().'.pdf');

        }
        catch(mPDFException $e) {
            Flash::error('Error en PDF: '.$e->getMessage());
            return;
        }

        $response = $this->getResponseInstance();
        $response->setHeader('Location: '.Core::getInstancePath().'temp/reccaj-'.$reccaj->getId().'.pdf?v='.mt_rand(1, 10000));

    }

    public function testAction()
    {
        $this->setResponse('json');
        $transaction = TransactionManager::getUserTransaction();
        $sequences = IdentityManager::getAuthedService('sequences.sequences');
        $sequences->setTransaction($transaction);
        $rc = $sequences->getConsecutivo('RC');//Obtiene el rc por webservice
        return array(
            'status' => 'FAILED',
            'message' => 'rc: ' . $rc . ', sequencesObj: ' . print_r($sequences, true)
        );
    }

    public function queryByNitAction()
    {
        $this->setResponse('json');
        $numeroNit = $this->getQueryParam('nit', 'alpha');
        $nit = $this->Nits->findFirst("nit='$numeroNit'");
        if ($nit==false) {
            return array(
                'status' => 'FAILED',
                'message' => 'NO EXISTE EL TERCERO'
            );
        } else {
            return array(
                'status' => 'OK',
                'nombre' => $nit->getNombre()
            );
        }
    }
}
