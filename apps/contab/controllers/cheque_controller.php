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
 * @author      BH-TECK Inc. 2009-2012
 * @version     $Id$
 */

/**
 * ChequeController
 *
 * Generación de cheques en tesoreria
 */
class ChequeController extends ApplicationController {

    public function initialize(){
        $controllerRequest = ControllerRequest::getInstance();
        if($controllerRequest->isAjax()){
            View::setRenderLevel(View::LEVEL_LAYOUT);
        }
        parent::initialize();
    }

    public function indexAction(){
        $chequeras = array();
        foreach($this->Chequeras->find() as $chequera){
            $cuentaBancos = $chequera->getCuentasBancos();
            if($cuentaBancos!=false){
                $chequeras[$chequera->getId()] = $cuentaBancos->getDescripcion().' '.$chequera->getNumeroInicial().'-'.$chequera->getNumeroFinal();
            }
        }
        if(count($chequeras)){
            asort($chequeras);
            $this->setParamToView('chequeras', $chequeras);
            $this->setParamToView('message', 'Ingrese un criterio de búsqueda para consultar cheques');
        } else {
            $this->routeToAction('noChequeras');
        }
    }

    public function noChequerasAction() {

    }

    public function buscarAction() {

        $this->setResponse('json');

        $chequerasId = $this->getPostParam('chequerasId', 'int');
        $numeroInicial = $this->getPostParam('numeroInicial', 'int');
        $numeroFinal = $this->getPostParam('numeroFinal', 'int');
        $estado = $this->getPostParam('estado', 'onechar');

        $response = array();

        $conditions = array();
        if($chequerasId>0){
            $conditions[] = 'chequeras_id = \''.$chequerasId.'\'';
        }
        if($numeroInicial>0&&$numeroFinal>0){
            if($numeroInicial==$numeroFinal){
                $conditions[] = 'numero_cheque = \''.$numeroInicial.'\'';
            } else {
                $conditions[] = 'numero_cheque >= \''.$numeroInicial.'\' AND numero_cheque <= \''.$numeroFinal.'\'';
            }
        }
        if($estado!='@'){
            $conditions[] = 'estado = \''.$estado.'\'';
        }

        if(count($conditions)>0){
            $cheques = $this->Cheque->find(array(join(' AND ', $conditions), 'order' => 'numero DESC'));
        } else {
            $cheques = $this->Cheque->find(array('order' => 'numero DESC'));
        }
        if(count($cheques)==0){
            $response['number'] = '0';
        } else {
            if(count($cheques)==1){
                $cheque = $cheques->getFirst();
                $response['number'] = '1';
                $response['key'] = 'id='.$cheque->getId();
            } else {
                $responseResults = array(
                    'headers' => array(
                        array('name' => 'Chequera', 'ordered' => 'N'),
                        array('name' => 'Número', 'ordered' => 'S'),
                        array('name' => 'Comprobante', 'ordered' => 'N'),
                        array('name' => 'Valor', 'ordered' => 'N', 'align' => 'right'),
                        array('name' => 'Estado', 'ordered' => 'N')
                    )
                );
                $data = array();
                foreach($cheques as $cheque){
                    $chequera = BackCacher::getChequera($cheque->getChequerasId());
                    $cuentaBanco = BackCacher::getCuentaBanco($chequera->getCuentasBancosId());
                    $data[] = array(
                        'primary' => array('id='.$cheque->getId()),
                        'data' => array(
                            array('key' => 'chequera', 'value' => $cuentaBanco->getDescripcion()),
                            array('key' => 'numero', 'value' => $cheque->getNumeroCheque()),
                            array('key' => 'comprobante', 'value' => $cheque->getComprob().'-'.$cheque->getNumero()),
                            array('key' => 'valor', 'value' => Currency::number($cheque->getValor())),
                            array('key' => 'estado', 'value' => $cheque->getDetalleEstado()),
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

    public function verAction() {
        $chequeId = $this->getPostParam('id', 'int');
        if($chequeId>0){
            $cheque = $this->Cheque->findFirst($chequeId);
            if($cheque==false){
                Flash::error('No existe el cheque');
                $this->routeToAction('errores');
            }
            if($cheque->getEstado()=='A'){
                $this->setParamToView('message', 'El cheque está anulado');
            }
            $new = $this->getPostParam('new', 'int');
            if($new==true){
                Flash::success('Se creó el cheque correctamente');
            }
            $this->setParamToView('cheque', $cheque);
        } else {
            Flash::error('No existe el cheque');
            $this->routeToAction('errores');
        }
    }

    public function erroresAction(){

    }

    public function nuevoAction(){
        $chequeras = array();
        foreach($this->Chequeras->find(array('conditions'=>'estado="A"')) as $chequera){
            $cuentaBancos = $chequera->getCuentasBancos();
            if($cuentaBancos!=false){
                $chequeras[$chequera->getId()] = $cuentaBancos->getDescripcion().' '.$chequera->getNumeroInicial().'-'.$chequera->getNumeroFinal();
            }
        }
        asort($chequeras);
        $this->setParamToView('chequeras', $chequeras);
        $this->setParamToView('centros', $this->Centros->find(array("estado='A'", "order" => "nom_centro")));
        $this->setParamToView('message', 'Ingrese los datos del cheque y la contabilización y haga click en "Grabar"');
    }

    public function nextChequeAction(){
        $this->setResponse('json');
        $chequeraId = $this->getPostParam('chequeraId', 'int');
        $chequera = $this->Chequeras->findFirst($chequeraId);
        if($chequera==false){
            return array(
                'status' => 'FAILED',
                'lista' => array()
            );
        } else {
            $cheques = array();
            $conditions = "chequeras_id='$chequeraId' AND estado = 'A'";
            foreach($this->Cheque->find($conditions) as $cheque){
                $cheques[$cheque->getNumeroCheque()] = 1;
            }
            $n = 1;
            $lista = array();
            for($i=$chequera->getNumeroInicial();$i<=$chequera->getNumeroFinal();++$i){
                if(!isset($cheques[$i])){
                    $lista[] = $i;
                    $n++;
                }
            }
            return array(
                'status' => 'OK',
                'lista' => $lista
            );
        }
    }

    public function generarAction(){
        $this->setResponse('json');
        try {

            $transaction = TransactionManager::getUserTransaction();

            $rules = array(
                'nit' => array(
                    'message' => 'Debe indicar el tercero'
                ),
                'chequeraId' => array(
                    'message' => 'Debe indicar la chequera',
                    'nullValue' => '@'
                )
            );
            if($this->validateRequired($rules)==false){
                foreach($this->getValidationMessages() as $message){
                    $transaction->rollback($message->getMessage());
                }
            } else {

                $nitNumero = $this->getPostParam('nit', 'terceros');

                $valor = $this->getPostParam('valor', 'float');
                if($valor<=0){
                    $transaction->rollback('El valor del cheque es inválido');
                }

                $numeroCheque = $this->getPostParam('numeroCheque', 'int');
                if($numeroCheque<=0){
                    $transaction->rollback('El número de cheque no es válido');
                }

                $chequeraId = $this->getPostParam('chequeraId', 'int');
                $chequera = $this->Chequeras->findFirst($chequeraId);
                if($chequera==false){
                    $transaction->rollback('La chequera es inválida');
                }

                $existe = $this->Cheque->count("chequeras_id='$chequeraId' AND numero_cheque='$numeroCheque' AND estado='E'");
                if($existe){
                    $transaction->rollback('El cheque '.$numeroCheque.' ya fue emitido en la chequera');
                }

                $comprobCheque = Settings::get('comprob_cheque');
                $cuentaBanco = $chequera->getCuentasBancos();

                $fechaHoy = Date::getCurrentDate();
                $fechaMovi = $this->getPostParam('fechaMovi', 'date');

                $beneficiario = $this->getPostParam('beneficiario');
                $beneficiario = i18n::strtoupper($beneficiario);

                try {
                    $aura = new Aura($comprobCheque, 0, null, Aura::OP_CREATE);
                    $fCuentas = $this->getPostParam('f_cuenta', 'cuentas');
                    $fCentroCostos = $this->getPostParam('f_centroCosto', 'int');
                    $fValor2 = $this->getPostParam('f_valor2');
                    $fNaturaleza = $this->getPostParam('f_naturaleza', 'onechar');
                    $fNit = $this->getPostParam('f_nit2', 'terceros');
                    $fDescripcion = $this->getPostParam('f_descripcion');
                    if(is_array($fCuentas)){
                        $numeroContras = count($fCuentas);
                        for($i=0;$i<$numeroContras;$i++){
                            $aura->addMovement(array(
                                'Fecha' => $fechaMovi,
                                'Cuenta' => $fCuentas[$i],
                                'Nit' => $fNit[$i],
                                'CentroCosto' => $fCentroCostos[$i],
                                'Descripcion' => $fDescripcion[$i],
                                'Valor' => $fValor2[$i],
                                'DebCre' => $fNaturaleza[$i]
                            ));
                        }
                    }
                    $numeroDocs = $this->getPostParam('numeroDoc');
                    if(is_array($numeroDocs)){
                        $numeroDocumentos = count($numeroDocs);
                        for($i=0;$i<$numeroDocumentos;$i++){

                            $numeroDoc = explode('_', $numeroDocs[$i]);

                            $numeroDoc[0] = $this->filter($numeroDoc[0], 'alpha');
                            $numeroDoc[1] = $this->filter($numeroDoc[1], 'int');

                            $cartera = $this->Cartera->findFirst("nit='$nitNumero' AND tipo_doc='{$numeroDoc[0]}' AND numero_doc='{$numeroDoc[1]}'");
                            if($cartera!=false){

                                $valorCartera = $this->getPostParam('abono'.$numeroDocs[$i]);

                                $aura->addMovement(
                                    array(
                                        'Fecha' => $fechaMovi,
                                        'Cuenta' => $cartera->getCuenta(),
                                        'Nit' => $nitNumero,
                                        'CentroCosto' => $cartera->getCentroCosto(),
                                        'Descripcion' => 'PAGO A FACTURA #'.$numeroDoc[0].'-'.$numeroDoc[1],
                                        'Valor' => $valorCartera,
                                        'TipoDocumento' => $numeroDoc[0],
                                        'NumeroDocumento' => $numeroDoc[1],
                                        'DebCre' => 'D'
                                    )
                                );
                            } else {
                                $transaction->rollback("No se encontro el documento ".$numeroDoc[0].'-'.$numeroDoc[1]);
                            }
                        }
                    }
                    $aura->addMovement(array(
                        'Fecha' => $fechaMovi,
                        'Cuenta' => $cuentaBanco->getCuenta(),
                        'CentroCosto' => $cuentaBanco->getCentroCosto(),
                        'Nit' => $nitNumero,
                        'Descripcion' => 'CHEQUE# '.$numeroCheque.'/'.$beneficiario,
                        'Valor' => $valor,
                        'DebCre' => 'C'
                    ));
                    $aura->save();
                }
                catch(AuraException $e){
                    $transaction->rollback('Error al grabar comprobante: '.$e->getMessage());
                }

                $fecha = $this->getPostParam('fecha', 'date');
                $observacion = $this->getPostParam('observacion');
                $cheque = new Cheque();
                $cheque->setTransaction($transaction);
                $cheque->setChequerasId($chequeraId);
                $cheque->setComprob($comprobCheque);
                $cheque->setNumero($aura->getConsecutivo());
                $cheque->setNit($nitNumero);
                $cheque->setNumeroCheque($numeroCheque);
                $cheque->setFecha($fechaHoy);
                $cheque->setHora(Date::getCurrentTime());
                $cheque->setFechaCheque($fecha);
                $cheque->setValor($valor);
                $cheque->setBeneficiario($beneficiario);
                $cheque->setObservaciones(i18n::strtoupper($observacion));
                $cheque->setEstado('E');
                if($cheque->save()==false){
                    foreach($cheque->getMessages() as $message){
                        $transaction->rollback('Cheque: '.$message->getMessage());
                    }
                }

                $transaction->commit();

                return array(
                    'status' => 'OK',
                    'id' => 'id='.$cheque->getId()
                );
            }
        }
        catch(TransactionFailed $e){
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

    public function mostrarAction(){

    }

    public function anularAction(){
        $this->setResponse('json');
        try {
            $transaction = TransactionManager::getUserTransaction();
            $this->Cheque->setTransaction($transaction);
            $chequeId = $this->getPostParam('id', 'int');
            if($chequeId>0){
                $cheque = $this->Cheque->findFirst($chequeId);
                if($cheque==false){
                    $transaction->rollback('El cheque no ha sido emitido');
                } else {
                    if($cheque->getEstado()=='A'){
                        $transaction->rollback('El cheque ya había sido anulado');
                    }
                    $cheque->setEstado('A');
                    if($cheque->save()==false){
                        foreach($cheque->getMessages() as $message){
                            $transaction->rollback('Cheque: '.$message->getMessage());
                        }
                    }
                    try {
                        $aura = new Aura($cheque->getComprob(), $cheque->getNumero(), null, Aura::OP_CREATE);
                        $aura->delete();
                    }
                    catch(AuraException $e){
                        $transaction->rollback('Eliminando Comprobante: '.$e->getMessage());
                    }
                    $transaction->commit();
                    return array(
                        'status' => 'OK',
                        'message' => 'Se anuló el cheque correctamente'
                    );
                }
            } else {
                $transaction->rollback('El cheque no ha sido emitido');
            }
        }
        catch(TransactionFailed $e){
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

    public function getCarteraAction(){
        $this->setResponse('json');
        $documentos = array();
        $nitTercero = $this->getPostParam('nit', 'alpha');
        if($nitTercero!=''){
            foreach ($this->Documentos->find(array('conditions'=>'cartera="S"')) as $documento) {
                $carteraObj = $this->Cartera->find(array("nit='$nitTercero' AND tipo_doc = '{$documento->getCodigo()}' AND saldo<0", "order" => "numero_doc"));
                foreach($carteraObj as $cartera){
                    $documentos[] = array(
                        'tipoDoc' => $cartera->getTipoDoc(),
                        'numeroDoc' => $cartera->getNumeroDoc(),
                        'cuenta' => $cartera->getCuenta(),
                        'fEmision' => (string)$cartera->getFEmision(),
                        'fVence' => (string)$cartera->getFVence(),
                        'saldo' => Currency::number(abs($cartera->getSaldo())),
                        'saldoValor' => -$cartera->getSaldo()
                    );
                }
            }

        }
        /*if($nitTercero!=''){
            foreach($this->Cartera->find(array("nit='$nitTercero' AND (tipo_doc='FXP' OR tipo_doc='CXP') AND saldo<0", "order" => "numero_doc")) as $cartera){
                $documentos[] = array(
                    'tipoDoc' => $cartera->getTipoDoc(),
                    'numeroDoc' => $cartera->getNumeroDoc(),
                    'cuenta' => $cartera->getCuenta(),
                    'fEmision' => (string)$cartera->getFEmision(),
                    'fVence' => (string)$cartera->getFVence(),
                    'saldo' => Currency::number(abs($cartera->getSaldo())),
                    'saldoValor' => -$cartera->getSaldo()
                );
            }
        }*/
        return array(
            'status' => 'OK',
            'documentos' => $documentos
        );
    }

    private function _printElement($pdf, $x, $y, $text){
        if(($pos = strpos($text, '<br>'))===false){
            $length = strlen($text)*0.25;
        } else {
            $length = $pos*0.25;
        }
        $text = str_replace(' ', '&nbsp;', $text);
        return '<div style="font-family:monospace;position:absolute;top:'.$y.'cm;left:'.$x.'cm;width:'.$length.'cm">'.$text.'</div>';
    }


    /**
    * Add line in TXT FORMAT
    */
    private function _printElementTxt(&$txt, $x, $y, $text, $rellenar=false){

        $x = ceil($x);
        $y = ceil($y);

        if($rellenar==false){
            $rellenar = ' ';
        }

        //echo 'x:', $x, ',y:', $y, ',text:', $text, ',rellenar:', $rellenar, PHP_EOL;
        $limitCols = 85;

        //Si no existe la linea crear una linea de con n espacios segn $limitCols
        if(!isset($txt[$y])){
            $txt[$y] = str_pad($rellenar, $limitCols);
        }
        $str = $txt[$y];

        //Ahora rempalzamos en los espacio segun posiciones
        $i2=0;
        for($i=0;$i<strlen($str);$i++){

            //si estamos en el carcater de la posicion substituimos la letra
            if($i==$x){
                $i2 = $i;
                for($j=0;$j<strlen($text);$j++){
                    $str[$i2]=$text[$j];
                    $i2++;
                }
            }

            if($rellenar!=' ' && $i>$i2 && $i>$x){
                $str[$i]=$rellenar;
            }

        }

        $txt[$y] = $str;

        return $str;

    }


    public function imprimirAction(){

        $this->setResponse('view');

        $chequeId = $this->getQueryParam('id', 'int');
        if($chequeId>0){
            $cheque = $this->Cheque->findFirst($chequeId);
            if($cheque==false){
                View::setRenderLevel(View::LEVEL_NO_RENDER);
                Flash::error('El cheque no existe');
                return;
            }
        } else {
            View::setRenderLevel(View::LEVEL_NO_RENDER);
            Flash::error('El cheque no existe');
            return;
        }

        $formatoCheque = $this->FormatoCheque->findFirst("chequeras_id={$cheque->getChequerasId()}");
        if($formatoCheque==false){
            $chequera = $this->Chequeras->findFirst($cheque->getChequerasId());
            View::setRenderLevel(View::LEVEL_NO_RENDER);
            Flash::error('No se ha configurado el formato de cheques '.$chequera->getNombre());
            return;
        }

        //Centimetros
        if($formatoCheque->getMedida()=='CM'){

            $html = '';
            $currency = new Currency();
            $formatoCheque = $cheque->getChequeras()->getFormatoCheque();
            $formato = array();
            $items = array(
                'ano', 'mes', 'dia', 'valor', 'tercero', 'suma',
                'numero', 'nota', 'cuenta', 'detalle', 'debito',
                'credito', 'valor_movi', 'empresa', 'num_cheque',
                'cuenta_bancaria'
            );
            foreach($items as $item){
                $rPos = $formatoCheque->readAttribute('r_'.$item);
                $cPos = $formatoCheque->readAttribute('p_'.$item);
                if($cPos>0&&$rPos>0){
                    $formato[$item] = array('x' => $cPos, 'y' => $rPos);
                }
            }

            $mRows = $formatoCheque->readAttribute('r_numero');
            if(!$mRows){
                $mRows = 40;
            }

            $fechaCheque = $cheque->getFecha();
            $suma = $currency->getMoneyAsText($cheque->getValor(), 'PESOS', 'CENTAVOS');
            $suma = str_replace('COLOMBIANOS', '', $suma);

            try {

                require 'Library/Mpdf/mpdf.php';
                $pdf = new mPDF();
                $pdf->tMargin = 0;
                $pdf->lMargin = 0;
                $pdf->ignore_invalid_utf8 = true;

                $html = '<html><head>
                        <style type="text/css">
                            body {
                                font-family: "lucidaconsole";
                                /*
                                font-family: "Verdana";
                                font-family: "monospace";
                                font-family: "Courier New";
                                font-family: "Lucida Console";
                                */
                                margin:0px;padding:0px;
                            }
                        </style>
                    </head><body>';
                foreach($formato as $item => $coord){
                    switch($item){
                        case 'ano':
                            $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], $fechaCheque->getYear());
                            break;
                        case 'mes':
                            $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], $fechaCheque->getMonth());
                            break;
                        case 'dia':
                            $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], $fechaCheque->getDay());
                            break;
                        case 'valor':
                            $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], Locale::money($cheque->getValor()));
                            break;
                        case 'tercero':
                            $value = '';
                            $final = array();
                            $parts = preg_split('/[ ]+/', $cheque->getBeneficiario());
                            foreach($parts as $part){
                                if(i18n::strlen($value.$part)<60){
                                    $value.=$part;
                                    $final[] = $part;
                                }
                            }
                            $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], join(' ', $final));
                            break;
                        case 'suma':
                            $value = '';
                            $final = array();
                            $extra = array();
                            $parts = preg_split('/[ ]+/', $suma);
                            foreach($parts as $part){
                                if(i18n::strlen($value.$part)<60){
                                    $value.=$part;
                                    $final[] = $part;
                                } else {
                                    $extra[] = $part;
                                }
                            }
                            if(count($extra)>0){
                                $suma = join(' ', $extra);
                                //$formato[$j+1][$i] = 'suma';
                            }
                            $html.='<p style="lucidaconsole">'.$this->_printElement($pdf, $coord['x'], $coord['y'], join(' ', $final));
                            $html.=$this->_printElement($pdf, $coord['x'], $coord['y']+1, join(' ', $extra));
                            break;
                        case 'numero':
                            $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], $cheque->getComprob().'-'.$cheque->getNumero());
                            break;
                        case 'nota':
                            $nota = wordwrap($cheque->getObservaciones(), 72, '<br>');
                            $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], $nota);
                            break;
                        case 'empresa':
                            if ( $coord['x']>0 && $coord['y']>0) {
                                $empresa = $this->Empresa->findFirst();
                                $empresa = wordwrap($empresa->getNombre(), 72, '<br>');
                                $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], $empresa);
                            }
                            break;
                        case 'num_cheque':
                            if ( $coord['x']>0 && $coord['y']>0) {
                                $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], $cheque->getNumeroCheque());
                            }
                            break;
                        case 'cuenta_bancaria':
                            if ( $coord['x']>0 && $coord['y']>0) {
                                $chequera = $this->Chequeras->findFirst($cheque->getChequerasId());
                                $cuentasBancos = $this->CuentasBancos->findFirst($chequera->getCuentasBancosId());
                                $html.=$this->_printElement($pdf, $coord['x'], $coord['y'], $cuentasBancos->getDescripcion());
                            }
                            break;
                    }
                }

                $delta = 0;
                foreach($cheque->getMovi() as $movi){
                    if(isset($formato['cuenta'])){
                        $html.=$this->_printElement($pdf, $formato['cuenta']['x'], $formato['cuenta']['y']+$delta, $movi->getCuenta());
                    }
                    if(isset($formato['detalle'])){
                        $html.=$this->_printElement($pdf, $formato['detalle']['x'], $formato['cuenta']['y']+$delta, $movi->getDescripcion());
                    }
                    if(isset($formato['debito'])){
                        if($movi->getDebCre()=='D'){
                            $valor = sprintf('% 15s', Locale::money($movi->getValor()));
                            $html.=$this->_printElement($pdf, $formato['debito']['x'], $formato['cuenta']['y']+$delta, $valor);
                        }
                    }
                    if(isset($formato['credito'])){
                        if($movi->getDebCre()=='C'){
                            $valor = sprintf('% 15s', Locale::money($movi->getValor()));
                            $html.=$this->_printElement($pdf, $formato['credito']['x'], $formato['cuenta']['y']+$delta, $valor);
                        }
                    }
                    if(isset($formato['valor_movi'])){
                        $valor = sprintf('%15s', Locale::money($movi->getValor()));
                        $html.=$this->_printElement($pdf, $formato['valor_movi']['x'], $formato['cuenta']['y']+$delta, $valor.' '.$movi->getDebCre());
                    }
                    $delta += 0.5;
                }

                $pdf->writeHTML($html);
                $pdf->Output('public/temp/cheque-'.$cheque->getId().'.pdf');

            }
            catch(mPDFException $e){
                Flash::error('Error en PDF: '.$e->getMessage());
                return;
            }

            $response = $this->getResponseInstance();

            $response->setHeader('Location: '.Core::getInstancePath().'temp/cheque-'.$cheque->getId().'.pdf?v='.mt_rand(1, 10000));

        } else {

            if($formatoCheque->getMedida()=='PO'){
                //TXT
                $txt = array();
                $currency = new Currency();
                $formatoCheque = $cheque->getChequeras()->getFormatoCheque();
                $formato = array();
                $items = array(
                    'ano', 'mes', 'dia', 'valor', 'tercero', 'suma',
                    'numero', 'nota', 'cuenta', 'detalle', 'debito',
                    'credito', 'valor_movi', 'empresa', 'num_cheque',
                    'cuenta_bancaria'
                );
                foreach($items as $item){
                    $rPos = $formatoCheque->readAttribute('r_'.$item);
                    $cPos = $formatoCheque->readAttribute('p_'.$item);
                    if($cPos>0&&$rPos>0){
                        $formato[$item] = array('x' => $cPos, 'y' => $rPos);
                    }
                }

                $mRows = $formatoCheque->readAttribute('r_numero');
                if(!$mRows){
                    $mRows = 40;
                }

                $fechaCheque = $cheque->getFecha();
                $suma = $currency->getMoneyAsText($cheque->getValor(), 'PESOS', 'CENTAVOS');
                $suma = str_replace('COLOMBIANOS', '', $suma);

                for($i=0;$i<50;$i++){
                    $this->_printElementTxt($txt, $i, $i, ' ');
                }

                foreach($formato as $item => $coord){
                    switch($item){
                        case 'ano':
                            $this->_printElementTxt($txt, $coord['x'], $coord['y'], $fechaCheque->getYear());
                            break;
                        case 'mes':
                            $this->_printElementTxt($txt, $coord['x'], $coord['y'], $fechaCheque->getMonth());
                            break;
                        case 'dia':
                            $this->_printElementTxt($txt, $coord['x'], $coord['y'], $fechaCheque->getDay());
                            break;
                        case 'valor':
                            $this->_printElementTxt($txt, $coord['x'], $coord['y'], Locale::money($cheque->getValor()));
                            break;
                        case 'tercero':
                            $value = '';
                            $final = array();
                            $parts = preg_split('/[ ]+/', $cheque->getBeneficiario());
                            foreach($parts as $part){
                                if(i18n::strlen($value.$part)<60){
                                    $value.=$part;
                                    $final[] = $part;
                                }
                            }
                            $this->_printElementTxt($txt, $coord['x'], $coord['y'], join(' ', $final));
                            $this->_printElementTxt($txt, 73, $coord['y'], $cheque->getNit());
                            break;
                        case 'suma':
                            $value = '';
                            $final = array();
                            $extra = array();
                            $parts = preg_split('/[ ]+/', $suma);
                            foreach($parts as $part){
                                if(i18n::strlen($value.$part)<=60){
                                    $final[] = $part;
                                } else {
                                    $extra[] = $part;
                                }
                                $value.=$part;
                            }
                            if(count($extra)>0){
                                $suma = join(' ', $extra);
                                //$formato[$j+1][$i] = 'suma';
                            }
                            $this->_printElementTxt($txt, $coord['x'], $coord['y'], join(' ', $final), '*');
                            $this->_printElementTxt($txt, $coord['x'], $coord['y']+1, join(' ', $extra), '*');
                            break;
                        case 'numero':
                            $this->_printElementTxt($txt, $coord['x'], $coord['y'], $cheque->getComprob().'-'.$cheque->getNumero());
                            break;
                        case 'nota':
                            $nota = wordwrap($cheque->getObservaciones(), 72, '<br>');
                            $this->_printElementTxt($txt, $coord['x'], $coord['y'], $nota);
                            break;
                        case 'empresa':
                            if ( $coord['x']>0 && $coord['y']>0) {
                                $empresa = $this->Empresa->findFirst();
                                $empresa = wordwrap($empresa->getNombre(), 72, '<br>');
                                $this->_printElementTxt($txt, $coord['x'], $coord['y'], $empresa);
                            }
                            break;
                        case 'num_cheque':
                            if ( $coord['x']>0 && $coord['y']>0) {
                                $this->_printElementTxt($txt, $coord['x'], $coord['y'], 'CHEQUE# '.$cheque->getNumeroCheque());
                            }
                            break;
                        case 'cuenta_bancaria':
                            if ( $coord['x']>0 && $coord['y']>0) {
                                $chequera = $this->Chequeras->findFirst($cheque->getChequerasId());
                                $cuentasBancos = $this->CuentasBancos->findFirst($chequera->getCuentasBancosId());
                                $this->_printElementTxt($txt, $coord['x'], $coord['y'], $cuentasBancos->getDescripcion());
                            }
                            break;
                    }
                }

                $delta = 0;
                foreach($cheque->getMovi() as $movi){
                    if(isset($formato['cuenta'])){
                        $this->_printElementTxt($txt, $formato['cuenta']['x'], $formato['cuenta']['y']+$delta, $movi->getCuenta());
                    }
                    if(isset($formato['detalle'])){
                        $this->_printElementTxt($txt, $formato['detalle']['x'], $formato['cuenta']['y']+$delta, $movi->getDescripcion());
                    }
                    if(isset($formato['debito'])){
                        if($movi->getDebCre()=='D'){
                            $valor = sprintf('% 15s', Locale::money($movi->getValor()));
                            $this->_printElementTxt($txt, $formato['debito']['x'], $formato['cuenta']['y']+$delta, $valor);
                        }
                    }
                    if(isset($formato['credito'])){
                        if($movi->getDebCre()=='C'){
                            $valor = sprintf('% 15s', Locale::money($movi->getValor()));
                            $this->_printElementTxt($txt, $formato['credito']['x'], $formato['cuenta']['y']+$delta, $valor);
                        }
                    }
                    if(isset($formato['valor_movi'])){
                        $valor = sprintf('%15s', Locale::money($movi->getValor()));
                        $this->_printElementTxt($txt, $formato['valor_movi']['x'], $formato['cuenta']['y']+$delta, $valor.' '.$movi->getDebCre());
                    }
                    if($delta>7){
                        $delta+=7;
                    }else{
                        $delta ++;
                    }
                }

                //print_r($txt);

                echo $txtContent = implode(PHP_EOL, $txt);

                //file_put_contents('public/temp/cheque-'.$cheque->getId().'.txt', $txtContent);

                $response = $this->getResponseInstance();

                //$response->setHeader('Location: '.Core::getInstancePath().'temp/cheque-'.$cheque->getId().'.txt?v='.mt_rand(1, 10000));
                $response->setHeader("Content-Type: plain/text");
                $response->setHeader("Content-Disposition: Attachment; filename=".'cheque-'.$cheque->getId().'.txt');
                $response->setHeader("Pragma: no-cache");

            } else {

                //HTML
                $this->setParamToView('formatoCheque', $formatoCheque);
                $this->setParamToView('cheque', $cheque);

                View::renderPartial('html');

            }



        }

    }

}
