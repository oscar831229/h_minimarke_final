<?php

class SociosEstadoCuenta extends UserComponent
{
    /**
    * @var ActiveRecordTransaction
    */
    private $_transaction;

    public function _construct()
    {

    }

    /**
     * Agrega a un vector la configuracion constante de estados de cuenta
     * @param array $config configuracion inicial
     */
    public function addConfigDefault(&$config)
    {
        if (!isset($config['showSaldoAnteriorFactura'])) {
            $showSaldoAnteriorFactura = Settings::get('show_saldo_anterior_fac', 'SO');
            if (!$showSaldoAnteriorFactura) {
                throw new SociosException("No se ha definido si se desea ver el saldo anterior o no en la factura");
            }
            $config['showSaldoAnteriorFactura'] = $showSaldoAnteriorFactura;
        }

        if (!isset($config['showPos'])) {
            $showPos = Settings::get('show_ordenes_pos', 'SO');
            if (!$showPos) {
                throw new SociosException("No se ha definido si se desea ver el Consumos del punto de venta o no en la factura");
            }
            $config['showPos'] = $showPos;
        }

        if (!isset($config['showConsumoMinimo'])) {
            $showConsumoMinimo = Settings::get('show_consumo_minimo_fac', 'SO');
            if (!$showConsumoMinimo) {
                throw new SociosException("No se ha definido si se desea ver el consumo minimo o no en la factura");
            }
            $config['showConsumoMinimo'] = $showConsumoMinimo;
        }

        if (!isset($config['cargoFijoCM'])) {
            $cargoFijoCM = Settings::get('cargo_fijo_consumo_minimo', 'SO');
            if (!$cargoFijoCM) {
                throw new SociosException("No se ha definido el cargo fijo del consumo minimo");
            }
            $config['cargoFijoCM'] = $cargoFijoCM;
        }

        //Verificamos si hay financiacion
        if (!isset($config['showFinanciacion'])) {
            $showFinanciacion = Settings::get('show_financiacion_socios', 'SO');
            if (!$showFinanciacion) {
                throw new SociosException('No se ha configurado si se desea ver o no la financiación en la factura en configuración');
            }
            $config['showFinanciacion'] = $showFinanciacion;
        }

        //Verificamos si mostramos recargo con mora
        if (!isset($config['showRecargoMora'])) {
            $showRecargoMora = Settings::get('show_recargo_mora', 'SO');
            if (!$showFinanciacion) {
                throw new SociosException('No se ha configurado si se desea ver o no el regargo por mora en la factura en configuración');
            }
            $config['showRecargoMora'] = $showRecargoMora;
        }

        //Verificamos si mostramos recargo con mora
        if (!isset($config['showCupoPago'])) {
            $showCupoPago = Settings::get('show_cupo_pago', 'SO');
            if (!$showCupoPago) {
                throw new SociosException('No se ha configurado si se desea ver o no el cupon de pago en la factura en configuración');
            }
            $config['showCupoPago'] = $showCupoPago;
        }
    }

    /**
     * Genera el reporte de socios suspendidos en el period actual
     *
     * @param  array $config
     */
    public function estadoCuenta(&$config)
    {
        try {
            $transaction = TransactionManager::getUserTransaction();
            if (Date::isEarlier($config['fechaIni'], '2014-02-28')) {
                //throw new Exception("isEarlier", 1);
                $status = $this->_makeEstadoCuentaOld($config);
            } else {
                //throw new Exception("NOT isEarlier", 1);
                $status = $this->_makeEstadoCuenta($config);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage()/*.'trace: '.print_r($e, true)*/);
        }
    }

    //public function getContentMovi($periodo, $socios, $options)
    public function getContentMovi($fechaIni, $socios, $options)
    {

        //$content = $this->getDetalleMovi($periodo, $socios, $options);
        $content = $this->getDetalleMovi($fechaIni, $socios, $options);

        return $content;
    }

    //public function getDetalleMovi($periodo, $socios, $options)
    public function getDetalleMovi($fechaIni, $socios, $options)
    {

        error_reporting(E_ALL);

        $content = array();
        $numeroDocs = array();
        $tabuDetNumDoc = array();
        $tabuPuntoVenta = array();

        $fechaIniDate = new Date($fechaIni);
        $periodo = $fechaIniDate->getPeriod();
        $periodoAtras = $fechaIniDate->getPeriod();

        $periodoObj = SociosCore::getCurrentPeriodoObject($periodo);
        $year = substr($periodo, 0, 4);
        $month = (int) substr($periodo, 4, 2);
        if ($month<10) {
            $month = "0".$month;
        }

        $fechaCorte = $fechaIni;
        $dateFechaCorte = new Date($fechaCorte);

        $cuentasAjsEstadoCuenta = $options['cuentasAjsEstadoCuenta'];
        $comprobAjustes = $options['comprobAjustes'];
        $comprobNcStr = $options['comprobNcStr'];
        $tipoDocPos = $options['tipoDocPos'];
        $tipoDocSocios = $options['tipoDocSocios'];
        $comprobsPagosAs = $options['comprobsPagosAs'];
        $comprobsPagosAsArray = explode(',', $comprobsPagosAs);
        $comprobsPagosAs = implode("','", $comprobsPagosAsArray);
        $cuentaSaldoAFavor = $options['cuentaSaldoAFavor'];

        $datePos = '';
        $totalPos = 0;
        $totalCargos = 0;
        $descripcionPos = array();

        ////////////////////////////////////////////////////
        // CONSUMOS DE POS DIRECTOS NO EN HOTEL5 Y LLEVADOS A CARTERA
        ////////////////////////////////////////////////////
        $arrayFDP = SociosCore::getFacturaDPos($periodo, $socios->getSociosId());

        foreach ($arrayFDP as $fDP) {
            $desc = $fDP['prefijo_facturacion'].'-'.$fDP['consecutivo_facturacion'];
            $totalPos += $fDP['total'];

            $content[] = array(
                'fecha' => (string) $fDP['fecha'],
                'documento' => $desc,
                'concepto' => $fDP['salon_nombre'],
                'cargos' => $fDP['total'],
                'abonos' => '',
                'tipo' => 'C', //CONSUMOS
                'detalle' => array()
            );
            unset($fDP, $desc);
        }
        unset($arrayFDP);

        //////////////////////////////////////
        // CONSUMOS DE HOTEL5
        //////////////////////////////////////

        //Buscamos los consumos por Cartera de contabilidad por tipo de documento de POS del mes asi esten pagados
        $conditionsPos = "cedula='{$socios->getIdentificacion()}' AND year(fecfac)='$year' AND month(fecfac)='$month' AND saldo!=0 AND estado='A'";
        //throw new SociosException($conditionsPos);

        $facturasHotelObj = EntityManager::get('FacturasHotel')->find(array(
            'conditions' => $conditionsPos,
            'columns' => 'cedula, nota,estado, numfac,prefac,fecfac, total,saldo, numfol'
        ));

        foreach ($facturasHotelObj as $facturasHotel) {
            //verificamos si no se ha hecho un abono a cartera por pago despues de generar factura
            $conditionsReccaj = "cedula='{$socios->getIdentificacion()}' AND nota like '%{$facturasHotel->getPrefac()}-{$facturasHotel->getNumfac()}' AND estado='A'";
            //throw new Exception($conditionsReccaj);

            $abonoCarteraObj = EntityManager::get('ReccajHotel')->find(array(
                'conditions' => $conditionsReccaj,
                'columns' => 'numrec'
            ));

            $totalFacPos = $facturasHotel->getSaldo();

            foreach ($abonoCarteraObj as $abonoCartera) {

                $detrecHotelObj = EntityManager::get('DetrecHotel')->find(array(
                    'conditions' => "numrec='{$abonoCartera->getNumrec()}'",
                    'columns' => 'valor'
                ));
                $valorDetrec = 0;
                foreach ($detrecHotelObj as $detrecHotel) {
                    $valorDetrec += $detrecHotel->getValor();
                    unset($detrecHotel);
                }
                unset($detrecHotelObj);

                //throw new SociosException($valorDetrec."----".$totalFacPos.": ".($valorDetrec-$totalFacPos));

                $totalFacPos -= $valorDetrec;
            }

            if ($totalFacPos!=0) {

                //Obtenemos los ambientes de la factura generadas en pos
                $ambientesFactura = SociosCore::getAmbientesFacturaHotel($facturasHotel->getNumfol(), $socios->getIdentificacion());

                if (count($ambientesFactura)>0) {
                    $ambientesStr = implode(", ", $ambientesFactura);
                } else {
                    //Si no hat ambientes mostramos primer concepto de factura
                    $detfac = EntityManager::get('DetfacHotel')->findFirst("prefac='{$facturasHotel->getPrefac()}' AND numfac='{$facturasHotel->getNumfac()}'");
                    if ($detfac) {
                        $ambientesStr = $detfac->getConcepto();
                    } else {
                        $ambientesStr = '????';
                    }

                }
                //throw new SociosException(print_r($ambientesFactura, true));

                $desc = $facturasHotel->getPrefac().'-'.$facturasHotel->getNumfac();
                $totalPos += $totalFacPos;

                $ca = 0;
                $ab = 0;
                if ($totalFacPos>=0) {
                    $ca = $totalFacPos;
                } else {
                    $ab = abs($totalFacPos);
                }

                $content[] = array(
                    'fecha' => (string) $facturasHotel->getFecfac(),
                    'documento' => $desc,
                    'concepto' => $ambientesStr,
                    'cargos' => $ca,
                    'abonos' => $ab,
                    'tipo' => 'C', //CONSUMOS
                    'detalle' => array()
                );
            }

            unset($facturasHotel);
        }
        unset($facturasHotelObj);

        //////////////////////////////////////
        // SOSTENIMIENTO Y NOVEDADES
        //////////////////////////////////////

        //Buscamos los documentos pendientes del socio
        $conditionsCartera = "nit='{$socios->getIdentificacion()}' and valor>0 AND f_emision>='$year-$month-01' AND f_emision<='$dateFechaCorte' AND tipo_doc='$tipoDocSocios'";
        //throw new SociosException($conditionsCartera);

        $carteraObj = EntityManager::get('Cartera')->find($conditionsCartera, "group: numero_doc,cuenta,f_emision", "order: f_emision ASC", "columns: numero_doc, nit, numero_doc, f_emision");

        $countInvoicers = 0;

        //CARGOS
        foreach ($carteraObj as $cartera) {

            //CARGOS
            $invoObj = EntityManager::get('Invoicer')->findFirst("numero='{$cartera->getNumeroDoc()}' AND fecha_emision>='{$cartera->getFEmision()}'");
            if (!$invoObj) {
                unset($cartera);
                continue;
            }

            //throw new Exception("Error Processing Request", 1);


            if (Date::isEarlier($dateFechaCorte, $invoObj->getFechaEmision()) && $dateFechaCorte!=$invoObj->getFechaEmision()) {
                unset($cartera, $invoObj);
                continue;
            }

            $countInvoicers++;

            $detInvoObj = EntityManager::get('DetalleInvoicer')->find("facturas_id='{$invoObj->getId()}'");
            foreach ($detInvoObj as $invo) {

                $key = $invo->getId();

                if (isset($tabuDetNumDoc[$key])) {
                    unset($invo, $key);
                    continue;
                }

                //NO saldo anterior
                if (strstr($invo->getDescripcion(), 'SALDO PERIODO')) {
                    unset($invo, $key);
                    continue;
                }

                //NO PUNTO DE VENTA
                if (strstr($invo->getDescripcion(), 'PUNTO DE VENTA')) {
                    unset($invo, $key);
                    continue;
                }

                $content[] = array(
                    'fecha' => (string) $invoObj->getFechaEmision()->getDate(),
                    'documento' => "FAC-".$cartera->getNumeroDoc(),
                    'concepto' => $invo->getDescripcion(),
                    'cargos' => $invo->getTotal(),
                    'abonos' => '',
                    'tipo' => 'S', //SOSTENIMIENTO
                    'detalle' => array()
                );
                $totalCargos += $invo->getTotal();

                if (!isset($tabuDetNumDoc[$key])) {
                    $tabuDetNumDoc[$key] = true;
                }

                unset($invo, $key);
            }

            unset($cartera, $invoObj, $detInvoObj);
        }

        //////////////////////////////
        //Ajustes por AJS para cargos
        //////////////////////////////
        $cargosTabu = array();

        $conditionsCargosAjs = "nit='{$socios->getIdentificacion()}' AND month(fecha)='$month' AND year(fecha)='$year' ".
        "AND comprob IN('$comprobAjustes' $comprobNcStr)  AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')";
        //throw new SociosException($conditionsCargosAjs);

        $moviObj = EntityManager::get('Movi')->find(array(
            "conditions" => $conditionsCargosAjs,
            'columns' => 'deb_cre,comprob, numero,fecha, nit,cuenta, tipo_doc, numero_doc,descripcion,valor'
        ));

        foreach ($moviObj as $movi) {
            $key = $movi->getComprob()."-".$movi->getNumero();
            if (!isset($cargosTabu[$key])) {
                $cargosTabu[$key] = true;
            } else {
                unset($movi, $key);
                continue;
            }

            if ($movi) {
                if (Date::isEarlier($dateFechaCorte, $movi->getFecha())) {
                    //throw new Exception("$dateFechaCorte,{$movi->getFecha()}");

                    unset($movi, $key);
                    continue;
                }
            }

            if ($movi->getComprob()==$comprobAjustes) {
                $conditionsMovi2 = "comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}' AND nit='{$socios->getIdentificacion()}' AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')";
            } else {
                $conditionsMovi2 = "comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}' AND nit='{$socios->getIdentificacion()}'  AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')";
            }

            //throw new SociosException($conditionsMovi2);

            $movi2Obj = EntityManager::get('Movi')->find(array(
                'conditions' => $conditionsMovi2,
                'columns' => 'deb_cre,valor'
            ));

            $totalMovi = array('D'=>0, 'C'=>0);
            foreach ($movi2Obj as $movi2) {
                $totalMovi[$movi2->getDebCre()] += $movi2->getValor();
                unset($movi2);
            }
            unset($movi2Obj);

            if ($totalMovi>0) {
                $content[] = array(
                    'fecha' => (string) $movi->getFecha(),
                    'documento' => $key,
                    'concepto' => $movi->getDescripcion(),
                    'cargos' => $totalMovi['D'],
                    'abonos' => $totalMovi['C'],
                    'tipo' => 'J', //AJUSTE
                    'detalle' => array()
                );
            }

            unset($movi);
        }

        //////////////////////////////////////
        // ABONOS
        //////////////////////////////////////

        $abonosTabu = array();

        //ABONOS
        $conditionsAbonos = "nit='{$socios->getIdentificacion()}' AND deb_cre='C' AND month(fecha)='$month' AND year(fecha)='$year'".
        " AND comprob IN('$comprobsPagosAs') AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')";

        //throw new SociosException($conditionsAbonos);

        $moviObj = EntityManager::get('Movi')->find(array(
            "conditions" => $conditionsAbonos,
            'columns' => 'deb_cre,comprob, numero,fecha, nit,cuenta, tipo_doc, numero_doc,descripcion,valor'
        ));

        foreach ($moviObj as $movi) {
            $key = $movi->getComprob()."-".$movi->getNumero();
            if (!isset($abonosTabu[$key])) {
                $abonosTabu[$key] = true;
            } else {
                unset($movi, $key);
                continue;
            }

            if ($movi) {

                if (Date::isEarlier($dateFechaCorte, $movi->getFecha())) {
                    unset($movi, $key);
                    continue;
                }
            }

            //Buscamos recibo de caja activo es decir causado
            $reccaj = EntityManager::get('Reccaj')->findFirst("comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}' AND nit='{$movi->getNit()}' AND estado='C'");

            if ($reccaj) {

                //Buscamos detalle reccaj
                $detReccajObj = EntityManager::get('DetalleReccaj')->find("reccaj_id='{$reccaj->getId()}' AND forma_pago_id>0", "columns: forma_pago_id,valor");

                //Para varias formas de pago armamos
                $desc = array();
                $totTemp = 0;
                foreach ($detReccajObj as $detReccaj) {
                    //Buscamos nombre de forma de pago
                    $formaPago = BackCacher::getFormaPago($detReccaj->getFormaPagoId());
                    if ($formaPago) {
                        $desc[]= $formaPago->getDescripcion();
                    }
                    unset($detReccaj, $formaPago);
                }

                if (!count($desc)) {
                    $desc[]= 'OTRO PAGO';
                }

                $descStr = implode(', ', $desc);

                //Check movi total
                $moviTotz = array('D'=>0, 'C'=>0);
                $moviTempz = EntityManager::get('Movi')->find("comprob='{$reccaj->getComprob()}' AND numero='{$reccaj->getNumero()}' AND nit='{$reccaj->getNit()}' AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')");
                foreach ($moviTempz as $moviT) {
                    $moviTotz[$moviT->getDebCre()] += $moviT->getValor();
                    unset($moviT);
                }
                unset($moviTempz);

                $content[] = array(
                    'fecha' => (string) $movi->getFecha(),
                    'documento' => $movi->getComprob()."-".$movi->getNumero(),
                    'concepto' => $descStr,
                    'cargos' => $moviTotz['D'],
                    'abonos' => $moviTotz['C'],
                    'tipo' => 'A', //ABONO
                    'detalle' => array()
                );
                unset($detReccajObj);
            } else {
                ////////////////////////////////////////////
                //Si no se hizo por opción recibos de caja
                ////////////////////////////////////////////
                $descStr = 'OTRO PAGO';

                //Check movi total
                $moviTotz = array('D'=>0, 'C'=>0);
                $moviTempz = EntityManager::get('Movi')->find("comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}' AND nit='{$movi->getNit()}' AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')");
                foreach ($moviTempz as $moviT) {
                    $moviTotz[$moviT->getDebCre()] += $moviT->getValor();
                    unset($moviT);
                }
                unset($moviTempz);

                $content[] = array(
                    'fecha' => (string) $movi->getFecha(),
                    'documento' => $movi->getComprob()."-".$movi->getNumero(),
                    'concepto' => $descStr,
                    'cargos' => $moviTotz['D'],
                    'abonos' => $moviTotz['C'],
                    'tipo' => 'A', //ABONO
                    'detalle' => array()
                );
                unset($detReccajObj);
            }
            unset($movi, $key, $reccaj);

        }
        unset($cartera, $moviObj, $carteraObj);

        //throw new SociosException(print_r($content, true));
        return $content;
    }

    ////////////////////////////////////////////
    ///OLD ESTADO DE CUENTA CONTENT
    ////////////////////////////////////////////
    public function getContentMoviOld($periodo, $socios, $options)
    {

        $periodoAtras = SociosCore::subPeriodo($periodo, 1);
        //$periodoAtras = $periodo - 1;

        $content = $this->getDetalleMoviOld($periodo, $socios, $options);

        return $content;
    }

    public function getDetalleMoviOld($periodo, $socios, $options)
    {
        error_reporting(E_ALL);

        $content = array();
        $numeroDocs = array();
        $tabuDetNumDoc = array();
        $tabuPuntoVenta = array();

        $periodoAtras = SociosCore::subPeriodo($periodo, 1);

        $periodoObj = SociosCore::getCurrentPeriodoObject($periodo);
        $year = substr($periodo, 0, 4);
        $month = (int) substr($periodo, 4, 2);
        if ($month<10) {
            $month = "0".$month;
        }

        //throw new SociosException($periodoAtras);

        $periodoAtrasObj = SociosCore::getCurrentPeriodoObject($periodoAtras);
        if (!$periodoAtrasObj) {
            throw new SociosException("No existe un periodo creado en el '$periodoAtras'");
        }

        $year2 = substr($periodoAtras, 0, 4);
        $month2 = (int) substr($periodoAtras, 4, 2);
        if ($month2<10) {
            $month2 = "0".$month2;
        }

        $day = $periodoObj->getDiaFactura();
        if ($day<10) {
            $day = "0".$day;
        }

        $fechaCorte = "$year-$month-".$day;

        $dateFechaCorte = new Date($fechaCorte);

        $fechaCorteAtras = "$year2-$month2-".$periodoAtrasObj->getDiaFactura();
        $dateFechaCorteAtras = new Date($fechaCorteAtras);

        //fecha limite a buscar en el mes anterior Ej: 2013-12-02 hasta 2014-01-01
        $dateFechaCorteAtras2 = clone $dateFechaCorteAtras;
        $dateFechaCorteAtras2->addDays(1);
        //throw new SociosException($dateFechaCorteAtras2);

        $cuentasAjsEstadoCuenta = $options['cuentasAjsEstadoCuenta'];
        $comprobAjustes = $options['comprobAjustes'];
        $comprobNcStr = $options['comprobNcStr'];
        $tipoDocPos = $options['tipoDocPos'];
        $tipoDocSocios = $options['tipoDocSocios'];
        $comprobsPagosAs = $options['comprobsPagosAs'];
        $cuentaSaldoAFavor = $options['cuentaSaldoAFavor'];

        $datePos = '';
        $totalPos = 0;
        $totalCargos = 0;
        $descripcionPos = array();

        ////////////////////////////////////////////////////
        // CONSUMOS DE POS DIRECTOS NO EN HOTEL5 Y LLEVADOS A CARTERA
        ////////////////////////////////////////////////////
        $arrayFDP = SociosCore::getFacturaDPos($periodoAtras, $socios->getSociosId());

        foreach ($arrayFDP as $fDP) {
            $desc = $fDP['prefijo_facturacion'].'-'.$fDP['consecutivo_facturacion'];
            $totalPos += $fDP['total'];

            $content[] = array(
                'fecha' => (string) $fDP['fecha'],
                'documento' => $desc,
                'concepto' => $fDP['salon_nombre'],
                'cargos' => $fDP['total'],
                'abonos' => '',
                'tipo' => 'C', //CONSUMOS
                'detalle' => array()
            );
            unset($fDP, $desc);
        }
        unset($arrayFDP);

        //////////////////////////////////////
        // CONSUMOS DE HOTEL5
        //////////////////////////////////////

        //Buscamos los consumos por Cartera de contabilidad por tipo de documento de POS del mes asi esten pagados
        $conditionsPos = "cedula='{$socios->getIdentificacion()}' AND year(fecfac)='$year2' AND month(fecfac)='$month2' AND saldo!=0 AND estado='A'";
        //throw new SociosException($conditionsPos);

        $facturasHotelObj = EntityManager::get('FacturasHotel')->find(array(
            'conditions' => $conditionsPos,
            'columns' => 'cedula, nota,estado, numfac,prefac,fecfac, total,saldo, numfol'
        ));

        foreach ($facturasHotelObj as $facturasHotel) {
            //verificamos si no se ha hecho un abono a cartera por pago despues de generar factura
            $conditionsReccaj = "cedula='{$socios->getIdentificacion()}' AND nota like '%{$facturasHotel->getPrefac()}-{$facturasHotel->getNumfac()}' AND estado='A'";
            //throw new Exception($conditionsReccaj);

            $abonoCarteraObj = EntityManager::get('ReccajHotel')->find(array(
                'conditions' => $conditionsReccaj,
                'columns' => 'numrec'
            ));

            $totalFacPos = $facturasHotel->getSaldo();

            foreach ($abonoCarteraObj as $abonoCartera) {

                $detrecHotelObj = EntityManager::get('DetrecHotel')->find(array(
                    'conditions' => "numrec='{$abonoCartera->getNumrec()}'",
                    'columns' => 'valor'
                ));
                $valorDetrec = 0;
                foreach ($detrecHotelObj as $detrecHotel) {
                    $valorDetrec += $detrecHotel->getValor();
                    unset($detrecHotel);
                }
                unset($detrecHotelObj);

                //throw new SociosException($valorDetrec."----".$totalFacPos.": ".($valorDetrec-$totalFacPos));

                $totalFacPos -= $valorDetrec;
            }

            if ($totalFacPos!=0) {

                //Obtenemos los ambientes de la factura generadas en pos
                $ambientesFactura = SociosCore::getAmbientesFacturaHotel($facturasHotel->getNumfol(), $socios->getIdentificacion());

                if (count($ambientesFactura)>0) {
                    $ambientesStr = implode(", ", $ambientesFactura);
                } else {
                    //Si no hat ambientes mostramos primer concepto de factura
                    $detfac = EntityManager::get('DetfacHotel')->findFirst("prefac='{$facturasHotel->getPrefac()}' AND numfac='{$facturasHotel->getNumfac()}'");
                    if ($detfac) {
                        $ambientesStr = $detfac->getConcepto();
                    } else {
                        $ambientesStr = '????';
                    }

                }
                //throw new SociosException(print_r($ambientesFactura, true));

                $desc = $facturasHotel->getPrefac().'-'.$facturasHotel->getNumfac();
                $totalPos += $totalFacPos;

                $ca = 0;
                $ab = 0;
                if ($totalFacPos>=0) {
                    $ca = $totalFacPos;
                } else {
                    $ab = abs($totalFacPos);
                }

                $content[] = array(
                    'fecha' => (string) $facturasHotel->getFecfac(),
                    'documento' => $desc,
                    'concepto' => $ambientesStr,
                    'cargos' => $ca,
                    'abonos' => $ab,
                    'tipo' => 'C', //CONSUMOS
                    'detalle' => array()
                );
            }

            unset($facturasHotel);
        }
        unset($facturasHotelObj);

        //////////////////////////////////////
        // SOSTENIMIENTO Y NOVEDADES
        //////////////////////////////////////

        //Buscamos los documentos pendientes del socio
        $conditionsCartera = "nit='{$socios->getIdentificacion()}' and valor>0 and f_emision<='$dateFechaCorte' AND f_emision>='{$dateFechaCorteAtras2->getDate()}' AND tipo_doc='$tipoDocSocios'";
        //throw new SociosException($conditionsCartera);

        $carteraObj = EntityManager::get('Cartera')->find($conditionsCartera, "group: numero_doc,cuenta,f_emision", "order: f_emision ASC", "columns: numero_doc, nit, numero_doc,f_emision");

        $countInvoicers = 0;

        //CARGOS
        foreach ($carteraObj as $cartera) {
            //CARGOS
            $invoObj = EntityManager::get('Invoicer')->findFirst("numero='{$cartera->getNumeroDoc()}' AND fecha_emision>='{$dateFechaCorteAtras2->getDate()}'");
            if (!$invoObj) {
                unset($cartera);
                continue;
            }

            if (Date::isEarlier($dateFechaCorte, $invoObj->getFechaEmision()) && $dateFechaCorte!=$invoObj->getFechaEmision()) {
                unset($cartera, $invoObj);
                continue;
            }

            $countInvoicers++;

            $detInvoObj = EntityManager::get('DetalleInvoicer')->find("facturas_id='{$invoObj->getId()}'");
            foreach ($detInvoObj as $invo) {

                $key = $invo->getId();

                if (isset($tabuDetNumDoc[$key])) {
                    unset($invo, $key);
                    continue;
                }

                //NO saldo anterior
                if (strstr($invo->getDescripcion(), 'SALDO PERIODO')) {
                    unset($invo, $key);
                    continue;
                }

                //NO PUNTO DE VENTA
                if (strstr($invo->getDescripcion(), 'PUNTO DE VENTA')) {
                    unset($invo, $key);
                    continue;
                }

                $content[] = array(
                    'fecha' => (string) $invoObj->getFechaEmision()->getDate(),
                    'documento' => "FAC-".$cartera->getNumeroDoc(),
                    'concepto' => $invo->getDescripcion(),
                    'cargos' => $invo->getTotal(),
                    'abonos' => '',
                    'tipo' => 'S', //SOSTENIMIENTO
                    'detalle' => array()
                );
                $totalCargos += $invo->getTotal();

                if (!isset($tabuDetNumDoc[$key])) {
                    $tabuDetNumDoc[$key] = true;
                }

                unset($invo, $key);
            }

            unset($cartera, $invoObj, $detInvoObj);
        }

        //////////////////////////////
        //Ajustes por AJS para cargos
        //////////////////////////////
        $cargosTabu = array();

        $conditionsCargosAjs = "nit='{$socios->getIdentificacion()}' AND month(fecha)='$month2' AND year(fecha)='$year2' AND comprob IN('$comprobAjustes' $comprobNcStr)  AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')";
        //throw new SociosException($conditionsCargosAjs);

        $moviObj = EntityManager::get('Movi')->find(array(
            "conditions" => $conditionsCargosAjs,
            'columns' => 'deb_cre,comprob, numero,fecha, nit,cuenta, tipo_doc, numero_doc,descripcion,valor'
        ));

        foreach ($moviObj as $movi) {
            $key = $movi->getComprob()."-".$movi->getNumero();
            if (!isset($cargosTabu[$key])) {
                $cargosTabu[$key] = true;
            } else {
                unset($movi, $key);
                continue;
            }

            if ($movi) {
                if (Date::isEarlier($dateFechaCorte, $movi->getFecha()) || $dateFechaCorte==$movi->getFecha()) {
                    unset($movi, $key);
                    continue;
                }
            }

            if ($movi->getComprob()==$comprobAjustes) {
                $conditionsMovi2 = "comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}'  AND nit='{$socios->getIdentificacion()}' AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor') ";
            } else {
                $conditionsMovi2 = "comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}' AND nit='{$socios->getIdentificacion()}'  AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')";
            }

            //throw new SociosException($conditionsMovi2);


            $movi2Obj = EntityManager::get('Movi')->find(array(
                'conditions' => $conditionsMovi2,
                'columns' => 'deb_cre,valor'
            ));

            $totalMovi = array('D'=>0, 'C'=>0);
            foreach ($movi2Obj as $movi2) {
                $totalMovi[$movi2->getDebCre()] += $movi2->getValor();
                unset($movi2);
            }
            unset($movi2Obj);

            if ($totalMovi>0) {
                $content[] = array(
                    'fecha' => (string) $movi->getFecha(),
                    'documento' => $key,
                    'concepto' => $movi->getDescripcion(),
                    'cargos' => $totalMovi['D'],
                    'abonos' => $totalMovi['C'],
                    'tipo' => 'J', //AJUSTE
                    'detalle' => array()
                );
            }

            unset($movi);
        }

        //////////////////////////////////////
        // ABONOS
        //////////////////////////////////////

        $abonosTabu = array();

        //ABONOS
        $conditionsAbonos = "nit='{$socios->getIdentificacion()}' AND deb_cre='C' AND month(fecha)='$month2' AND year(fecha)='$year2' AND comprob IN('$comprobsPagosAs') AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')";

        //throw new SociosException($conditionsAbonos);

        $moviObj = EntityManager::get('Movi')->find(array(
            "conditions" => $conditionsAbonos,
            'columns' => 'deb_cre,comprob, numero,fecha, nit,cuenta, tipo_doc, numero_doc,descripcion,valor'
        ));

        foreach ($moviObj as $movi) {
            $key = $movi->getComprob()."-".$movi->getNumero();
            if (!isset($abonosTabu[$key])) {
                $abonosTabu[$key] = true;
            } else {
                unset($movi, $key);
                continue;
            }

            if ($movi) {
                if (Date::isEarlier($dateFechaCorte, $movi->getFecha()) || $dateFechaCorte==$movi->getFecha()) {
                    unset($movi, $key);
                    continue;
                }
            }

            //Buscamos recibo de caja activo es decir causado
            $reccaj = EntityManager::get('Reccaj')->findFirst("comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}' AND nit='{$movi->getNit()}' AND estado='C'");

            if ($reccaj) {

                //Buscamos detalle reccaj
                $detReccajObj = EntityManager::get('DetalleReccaj')->find("reccaj_id='{$reccaj->getId()}' AND forma_pago_id>0", "columns: forma_pago_id,valor");

                //Para varias formas de pago armamos
                $desc = array();
                $totTemp = 0;
                foreach ($detReccajObj as $detReccaj) {
                    //Buscamos nombre de forma de pago
                    $formaPago = BackCacher::getFormaPago($detReccaj->getFormaPagoId());
                    if ($formaPago) {
                        $desc[]= $formaPago->getDescripcion();
                    }
                    unset($detReccaj, $formaPago);
                }

                if (!count($desc)) {
                    $desc[]= 'OTRO PAGO';
                }

                $descStr = implode(', ', $desc);

                //Check movi total
                $moviTotz = array('D'=>0, 'C'=>0);
                $moviTempz = EntityManager::get('Movi')->find("comprob='{$reccaj->getComprob()}' AND numero='{$reccaj->getNumero()}' AND nit='{$reccaj->getNit()}' AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')");
                foreach ($moviTempz as $moviT) {
                    $moviTotz[$moviT->getDebCre()] += $moviT->getValor();
                    unset($moviT);
                }
                unset($moviTempz);

                $content[] = array(
                    'fecha' => (string) $movi->getFecha(),
                    'documento' => $movi->getComprob()."-".$movi->getNumero(),
                    'concepto' => $descStr,
                    'cargos' => $moviTotz['D'],
                    'abonos' => $moviTotz['C'],
                    'tipo' => 'A', //ABONO
                    'detalle' => array()
                );
                unset($detReccajObj);
            } else {
                ////////////////////////////////////////////
                //Si no se hizo por opción recibos de caja
                ////////////////////////////////////////////
                $descStr = 'OTRO PAGO';

                //Check movi total
                $moviTotz = array('D'=>0, 'C'=>0);
                $moviTempz = EntityManager::get('Movi')->find("comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}' AND nit='{$movi->getNit()}' AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')");
                foreach ($moviTempz as $moviT) {
                    $moviTotz[$moviT->getDebCre()] += $moviT->getValor();
                    unset($moviT);
                }
                unset($moviTempz);

                $content[] = array(
                    'fecha' => (string) $movi->getFecha(),
                    'documento' => $movi->getComprob()."-".$movi->getNumero(),
                    'concepto' => $descStr,
                    'cargos' => $moviTotz['D'],
                    'abonos' => $moviTotz['C'],
                    'tipo' => 'A', //ABONO
                    'detalle' => array()
                );
                unset($detReccajObj);
            }
            unset($movi, $key, $reccaj);

        }
        unset($cartera, $moviObj, $carteraObj);

        //throw new SociosException(print_r($content, true));

        return $content;
    }



    /**
    * Guarda la informacion de estado de cuenta
    */
    public function _saveEstadoCuentaConsolidado($datos)
    {
        $transaction = TransactionManager::getUserTransaction();

        $flagNew = false;

        //Se borra otros estados de cuenta del mes
        $yearX = substr($datos['fecha'], 0, 4);
        $monthX = (int) substr($datos['fecha'], 4, 2);
        if ($monthX<10) {
            $monthX = "0".$monthX;
        }
        $conditionsX = "socios_id='{$datos['sociosId']}' AND fecha!='{$datos['fecha']}' AND year(fecha)='$yearX' AND month(fecha)='$monthX'";
        //throw new SociosException($conditionsX);

        $estadoCuentaObj = EntityManager::get('EstadoCuenta')->find($conditionsX);
        foreach ($estadoCuentaObj as $estadoCuenta) {
            $estadoCuenta->delete();
            unset($estadoCuenta);
        }
        unset($estadoCuentaObj);

        $estadoCuenta = EntityManager::get('EstadoCuenta')->findFirst("socios_id='{$datos['sociosId']}' AND fecha='{$datos['fecha']}'");
        if (!$estadoCuenta) {
            $flagNew = true;
            //Registramos datos de estado de cuenta
            $consecutivoEstadoCuenta = Settings::get('consecutivo_estado_cuenta', 'SO');
            if (!$consecutivoEstadoCuenta) {
                throw new SociosException("No se ha colocado el consecutivo de estado de cuenta");
            }

            $estadoCuenta = EntityManager::get('EstadoCuenta', true);
            $estadoCuenta->setNumero($consecutivoEstadoCuenta);
            $estadoCuenta->setFecha($datos['fecha']);
            $estadoCuenta->setFechaSaldo($datos['fechaSaldo']);
            $estadoCuenta->setSociosId($datos['sociosId']);
        } else {

            //Si existe y no esta en el mismo periodo no modificar el registro anterior
            //causa desastres recalcular
            $periodoActual = SociosCore::getCurrentPeriodo();
            $fechaCambios = new Date($datos['fecha']);
            $estadoCuenta->setFechaSaldo($datos['fechaSaldo']);

            // si los periodos son diferente salga de aqui
            //throw new SociosException("if ($periodoActual>{$fechaCambios->getPeriod()}) {".($periodoActual>$fechaCambios->getPeriod()), 1);

            if ($periodoActual>$fechaCambios->getPeriod()) {
                throw new SociosException("No es posible regenerar el estado de cuenta del periodo '{$fechaCambios->getPeriod()}' ya que esta cerrado a '$periodoActual'");

                return $estadoCuenta;
            }
        }

        $totalCargos = 0;
        if (isset($datos['totalCargos']) && !empty($datos['totalCargos'])) {
            $totalCargos = (float) $datos['totalCargos'];
        }

        $totalAbonos = 0;
        if (isset($datos['totalAbonos']) && !empty($datos['totalAbonos'])) {
            $totalAbonos = (float) $datos['totalAbonos'];
        }

        $estadoCuenta->setFecha($datos['fecha']);
        $estadoCuenta->setSaldoAnt((float) $datos['saldoAnterior']);
        $estadoCuenta->setCargos($totalCargos);
        $estadoCuenta->setMora(0);
        $estadoCuenta->setInteres(0);
        $estadoCuenta->setPagos($totalAbonos);

        //Get detalle dias
        //echo $datos['identificacion'], ", ", $datos['fecha'], ", ", $datos['fechaSaldo'];
        $diasEC = $this->_getDiasEstadoCuenta($datos['identificacion'], $datos['fecha'], $datos['fechaSaldo']);

        $estadoCuenta->setd30($diasEC['30']);
        $estadoCuenta->setd60($diasEC['60']);
        $estadoCuenta->setd90($diasEC['90']);
        $estadoCuenta->setd120($diasEC['120']);
        $estadoCuenta->setd120m($diasEC['120m']);

        $estadoCuenta->setSaldoNuevo((float) $datos['valorAPagar']);
        $estadoCuenta->setSaldoNuevoMora((float) $datos['valorAPagarMora']);

        if ($estadoCuenta->save()==false) {
            foreach ($estadoCuenta->getMessages() as $msg) {
                throw new SociosException('EstadoCuenta: '.$msg->getMessage());
            }
        }

        $numero = $estadoCuenta->getNumero();
        $fecha = $datos['fecha'];

        //limpia detalle de estado cuenta
        $detalleEstadoCuentaDel = EntityManager::get('DetalleEstadoCuenta')->deleteAll("numero='$numero'");

        //Guarda el detalle del estado de cuenta
        $contentMovi = $datos['contentMovi'];
        foreach ($contentMovi as $content) {
            $conceptoTemp = '????';
            if (!empty($content['concepto'])) {
                $conceptoTemp = $content['concepto'];
            }
            $fechaTemp = $content['fecha'];
            if (!$fechaTemp or $fechaTemp=='0000-00-00') {
                throw new SociosException($fechaTemp);
                $fechaTemp = $fecha;
            }

            $detalleEstadoCuenta = new DetalleEstadoCuenta();
            $detalleEstadoCuenta->setNumero($numero);
            $detalleEstadoCuenta->setFecha($fechaTemp);
            $detalleEstadoCuenta->setDocumento($content['documento']);
            $detalleEstadoCuenta->setConcepto($conceptoTemp);
            $detalleEstadoCuenta->setCargos((float) $content['cargos']);
            $detalleEstadoCuenta->setAbonos((float) $content['abonos']);
            if ($detalleEstadoCuenta->save()==false) {
                foreach ($detalleEstadoCuenta->getMessages() as $msg) {
                    throw new SociosException($msg->getMessage().print_r($msg, true));
                }
            }
            unset($content, $detalleEstadoCuenta);
        }
        unset($contentMovi);

        //Si es nuevo registro
        if ($flagNew==true) {
            //Aumentamos consecutivo
            $consecutivoEstadoCuenta++;
            $configuration = EntityManager::get('Configuration')->findFirst("application='SO' AND name='consecutivo_estado_cuenta'");
            if ($configuration) {
                $configuration->setValue($consecutivoEstadoCuenta);
                if ($configuration->save()==false) {
                    foreach ($configuration->getMessages() as $msg) {
                        throw new SociosException($msg->getMessage());
                    }
                }
            }
        }

        return $estadoCuenta;

    }

    /**
    * Por medio de el nit de un socio se obtiene el detalle en dias de lo que debe el socio
    * 30,60,90, 120,mas de 120 dias
    */
    public function _getDiasEstadoCuenta($nit, $fechaCorte, $fechaSaldo)
    {
        $ret = array(
            '30' => 0,
            '60' => 0,
            '90' => 0,
            '120' => 0,
            '120m' => 0,
        );

        gc_enable();

        $i=0;
        $carteraObj = EntityManager::get('Cartera')->find("nit='$nit' AND f_emision < '$fechaSaldo' ");
        foreach ($carteraObj as $cartera) {
            $date1 = strtotime($fechaSaldo);
            $date2 = strtotime($cartera->getFEmision());
            $dateDiff = $date1 - $date2;
            $days = floor($dateDiff/(60*60*24));

            if ($days) {
                //throw new SociosException("fecha: $fechaCorte,f_emision:{$cartera->getFEmision()}, diff:".$days. ", sql: nit='$nit' AND f_emision < '$fechaSaldo' ");

                $index = '';
                if ($days<=30) {
                    $index = '30';
                } else {
                    if ($days<=60) {
                        $index = '60';
                    } else {
                        if ($days<=90) {
                            $index = '90';
                        } else {
                            if ($days<=120) {
                                $index = '120';
                            } else {
                                $index = '120m';
                            }
                        }
                    }
                }

                $ret[$index] += $cartera->getValor();
            }

            unset($cartera, $datetime1, $datetime2, $interval);

            if ($i>100) {
                gc_collect_cycles();
                $i = 0;
            }
            $i++;
        }
        unset($carteraObj);

        //throw new SociosException(print_r($ret, true));
        gc_disable();

        return $ret;
    }

    //////////////////////////////////////////////////
    //Validación de Estados de cuenta Vs Contabilidad
    //////////////////////////////////////////////////
    /**
    * Genera el reporte donde muestra el estado de cuenta y la contabilida
    */
    public function estadoCuentaValidacion($config)
    {
        try {
            $this->_transaction = TransactionManager::getUserTransaction();

            if (!$config['fecha']) {
                throw new SociosException("No se ha definido la fecha del estado de cuenta a validar");
            }
            $fecha = $config['fecha'];

            if (!$config['reportType']) {
                throw new SociosException("No se ha definido el tipo de salida a generar la validación");
            }

            $sociosId = false;
            if (isset($config['sociosId']) && $config['sociosId']>0){
                $sociosId = $config['sociosId'];
            }

            $reportType = $config['reportType'];

            $report = ReportBase::factory($reportType);

            $titulo = new ReportText('VALIDACIÓN DE ESTADOS DE CUENTA', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo));

            $report->setDocumentTitle('Validación de estados de cuenta');
            $report->setColumnHeaders(array(
                'ESTADO DE CUENTA',//0
                'NUMERO DE ACCION',//1
                'IDENTIFICACION',//2
                'NOMBRE',//3
                'FECHA',//4
                'SALDO SOCIOS',//5
                'SALDO CONTABILIDAD',//6
                'DIFERENCIA'//7
            ));

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));

            $report->setColumnStyle(array(0, 1,2,3,4), new ReportStyle(array(
                'textAlign' => 'center',
                'fontSize' => 11
            )));

            $report->setColumnStyle(array(5,6,7), new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11
            )));

            $report->setColumnFormat(array(5,6,7), new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 0
            )));

            $report->start(true);

            $totales = array();
            $totales['saldoSocios'] = 0;
            $totales['saldoContab'] = 0;
            $totales['diff'] = 0;

            //Socios
            $query = 'socios_id>0';
            if ($sociosId>0) {
                $query = "socios_id = '$sociosId'";
            }
            $sociosObj = EntityManager::get('Socios')->find(array($query, 'column'=>'socios_id,cobra', 'order'=>"CAST(numero_accion AS SIGNED) ASC"));

            $num = 0;
            foreach ($sociosObj as $socios) {
                //Buscamos Consolidados
                $estadoCuenta= EntityManager::get('EstadoCuenta')->findFirst(array("fecha='$fecha' AND socios_id='{$socios->getSociosId()}'", 'order'=>'numero ASC'));

                if (!$estadoCuenta) {
                    continue;
                }

                $nit = $socios->getIdentificacion();
                $saldoSocios = $estadoCuenta->getSaldoNuevo();

                //Saldo Contabilidad
                $saldoContab = SociosCore::getSaldoContab($fecha, $nit);

                //Diff
                $diff = $saldoSocios - $saldoContab;
                //throw new Exception("({$estadoCuenta->getFecha()}) $diff = $saldoSocios - $saldoContab", 1);

                if ($diff==0) {
                    continue;
                }

                //ROW
                $report->addRow(array(
                    $estadoCuenta->getNumero(),//0
                    $socios->getNumeroAccion(),//1
                    $nit,//2
                    $socios->getNombres()." ".$socios->getApellidos(),//3
                    $fecha,//4
                    $saldoSocios,//5
                    $saldoContab, //6
                    $diff //7
                ));

                //TOTALES
                $totales['saldoSocios'] += $estadoCuenta->getSaldoNuevo();
                $totales['saldoContab'] += $saldoContab;
                $totales['diff'] += $diff;

                $num++;

                unset($estadoCuenta, $socios);
            }
            unset($sociosObj);

            //ROW TOTAL
            $report->addRow(array(
                "#".$num,//0
                "&nbsp;",//1
                "&nbsp;",//2
                "&nbsp;",//3
                "&nbsp;",//4
                $totales['saldoSocios'],//5
                $totales['saldoContab'],//6
                $totales['diff']//7
            ));

            $report->finish();
            $fileName = $report->outputToFile('public/temp/estado_cuenta_validacion');

            return $fileName;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
    * Limpia el estado de cuenta cuando no hay movimineto
    *
    */
    private function _cleanEstadoCuenta($periodo, $socios, $options)
    {
        //periodo Actual
        $year = substr($periodo, 0, 4);
        $month = (int) substr($periodo, 4, 2);
        if ($month<10) {
            $month = "0".$month;
        }

        //periodo Actual
        $periodoAtras = SociosCore::subPeriodo($periodo, 1);
        $yearA = substr($periodoAtras, 0, 4);
        $monthA = (int) substr($periodoAtras, 4, 2);
        if ($monthA<10) {
            $monthA = "0".$monthA;
        }

        $query = "socios_id='{$socios->getSociosId()}' AND month(fecha)='$month' AND year(fecha)='$year'";
        //throw new Exception($query);

        $estadoCuentaObj = EntityManager::get('EstadoCuenta')->find($query);
        foreach ($estadoCuentaObj as $estadoCuenta) {
            //Sacar estado de cuenta atras para revisar el correcto
            $estadoCuentaAtras = EntityManager::get('EstadoCuenta')->findFirst("socios_id='{$socios->getSociosId()}' AND month(fecha)<='$monthA' AND year(fecha)<='$yearA'", "order: fecha DESC");
            if ($estadoCuentaAtras) {
                $estadoCuenta->setSaldoAnt($estadoCuentaAtras->getSaldoNuevo());
            } else {
                $estadoCuenta->setSaldoAnt(0);
            }

            //Datos generales
            $estadoCuenta->setCargos(0);
            $estadoCuenta->setPagos(0);
            $estadoCuenta->setSaldoNuevo($estadoCuenta->getSaldoAnt());
            if ($estadoCuenta->save() == false) {
                foreach ($estadoCuenta->getMessages() as $message) {
                    throw new Exception("estadoCuentaClean: ".$message->getMessage());
                }
            }

            //clean detalle estado cuenta
            $detalleEstadoCuentaObj = EntityManager::get('DetalleEstadoCuenta')->deleteAll("numero='{$estadoCuenta->getNumero()}'");

            unset($estadoCuenta);
        }
        unset($estadoCuentaObj);
    }

    /**
     * Crea estado de cuenta con la version de febero 28 ed 2014
     * @param  array $config
     */
    private function _makeEstadoCuenta(&$config)
    {

        try {
            $totales = array();
            $transaction = TransactionManager::getUserTransaction();
            $datosClub   = EntityManager::get('DatosClub')->findFirst();
            $consecutivo = EntityManager::get('Consecutivos')->findFirst();

            if (!isset($config['sociosId']) || $config['sociosId']<=0) {
                $sociosObj = EntityManager::get('Socios')->find(
                    array(
                        "conditions" => "1=1",
                        "order"      => "CAST(numero_accion AS SIGNED) ASC"
                    )
                );
            } else {
                $sociosObj = EntityManager::get('Socios')->find("socios_id='{$config['sociosId']}'");
                if (!count($sociosObj)) {
                    throw new SociosException("El socio con id '{$config['sociosId']}' no existe");
                }
            }

            $periodoObj = EntityManager::get('Periodo')->findFirst("periodo='".$config["periodo"]."'");
            if (!$periodoObj->getDiaFactura()) {
                throw new SociosException("no se ha definido el dia de facturacion del periodo '".$config["periodo"]."'");
            }

            $periodoInt = $config["periodo"];

            //validamos si contabildiad esta abierta
            self::checkPeriod($periodoInt);

            $year = substr($periodoInt, 0, 4);
            $month = (int) substr($periodoInt, 4, 2);
            if ($month<10) {
                $month = "0" . $month;
            }

            $day = $periodoObj->getDiaFactura();
            if ($day<10) {
                $day = "0" . $day;
            }

            $fechaCorte = $config['fechaIni'];
            $dateFechaCorte = new Date($fechaCorte);
            $dateFechaLimite = new Date($config['fechaFin']);

            $lastDayOfMonth = date::getLastDayOfMonth($month, $year);

            $peridoSub2 = SociosCore::subPeriodo($periodoInt, 2);
            $year2L = substr($peridoSub2, 0, 4);
            $month2L = (int) substr($peridoSub2, 4, 2);

            $fechaLimitShow = new Date("$year2L-$month2L-01");
            $fechaLimitShow->toLastDayOfMonth();
            $fechaLimitShow->addDays(1);

            $fLimitShowLPeriod = $fechaLimitShow->getPeriod();
            $year2LP = substr($fLimitShowLPeriod, 0, 4);
            $month2LP = (int) substr($fLimitShowLPeriod, 4, 2);
            $fechaLimitShowL = date::getLastDayOfMonth($month2LP, $year2LP);

            //buscamos periodo
            $periodo = EntityManager::get('Periodo')->findFirst("periodo='{$dateFechaCorte->getPeriod()}'");
            if (!$periodo) {
                throw new SociosException("No existe un periodo registrado en socios para la fecha limite seleccionada");
            }

            //mensajes de Extracto
            $resumen = Settings::get('resumen_extracto', 'SO');
            $resumenRecaudo = Settings::get('resumen_recaudo', 'SO');
            if (!$resumenRecaudo) {
                $resumenRecaudo = '<br/><br/>';
            }

            //Mas fix
            $ctaCuentaAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');

            //Solo buscamos este tipo de documento para su saldo anterior los FAC se dejan sin contar
            $tipoDocSocios = Settings::get('tipo_doc', 'SO');

            //Base porcentaje Mora
            $basePorcentaje = Settings::get('base_porc_mora_desfecha', 'SO');
            if (!$basePorcentaje) {
                throw new SociosException("No se ha configurado base de procentaje de mora de estado de cuenta");
            }

            $tipoDocPos = Settings::get('tipo_doc_pos', 'SO');
            if (!$tipoDocPos) {
                throw new SociosException("No se ha definido el tipo de documento de POS en cartera");
            }

            $cuentasAjsEstadoCuenta = SociosCore::getCuentaAjusteEstadoCuenta();

            $comprobAjustes = Settings::get('comprob_ajustes', 'SO');
            if (!$comprobAjustes) {
                throw new SociosException('No se ha configurado el comprobante de ajustes de facturas en configuración');
            }

            $comprobNcStr = '';
            $comprobNc = Settings::get('comprob_nc', 'SO');
            if ($comprobNc) {
                $comprobNcStr = "'".$comprobNc."'";
                $comprobNcStr = ", ".str_replace(", ", "', '", $comprobNcStr);
            }

            //comprobante de pagos
            $comprobsPagos = Settings::get('comprobs_pagos', 'SO');
            if (!$comprobsPagos) {
                throw new SociosException("Es necesario dar los comprobantes de pagos en configuración");
            }

            $comprobsPagosA = explode(', ', $comprobsPagos);
            $comprobsPagosAs = implode("', '", $comprobsPagosA);

            //Cuenta de saldo a favor
            $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
            if (!$cuentaSaldoAFavor) {
                throw new SociosException("No se ha configurado la cuenta de saldo a favor de socios");
            }

            $options = array(
                'tipoDocPos' => $tipoDocPos,
                'comprobNcStr' => $comprobNcStr,
                'tipoDocSocios' => $tipoDocSocios,
                'comprobAjustes' => $comprobAjustes,
                'comprobsPagosAs' => $comprobsPagosAs,
                'cuentaSaldoAFavor' => $cuentaSaldoAFavor,
                'cuentasAjsEstadoCuenta' => $cuentasAjsEstadoCuenta
            );

            ///RECORREMOS SOCIOS A GENERAR
            $i = 0;
            foreach ($sociosObj as $socios) {

                //Validacion de solo socios que generan estado de cuenta
                if ($socios->getGeneraEstcue() != 'S') {
                    $this->_cleanEstadoCuenta($dateFechaCorte->getPeriod(), $socios, $options);
        		    continue;
                }

                $sociosId = $socios->getSociosId();

                $this->validaDuplicados($config["fechaIni"], $sociosId);

                $location = BackCacher::getLocation($socios->getCiudadCasa());
                if (!$location) {
                    throw new SociosException('No se ha asignado la ciudad de la casa en el maestro de socios');
                }
                $ciudad = $location->getName();

                $contentMoviHtml = "";

                $saldoMora = 0;
                $totalCargos = 0;
                $totalAbonos = 0;
                $estadoCuentaDetalleArray = array();

                //$contentMovi = $this->getContentMovi($dateFechaCorte->getPeriod(), $socios, $options);
                $contentMovi = $this->getContentMovi($fechaCorte, $socios, $options);

                //Si no tiene movimiento ó no debería generar estado de cuenta limpie estado de cuenta
                if (count($contentMovi)<=0 || $socios->getGeneraEstcue()=='N') {

                    //limpia estado de cuenta si existe cuando se calcula de nuevo y no tiene movimiento
                    if (isset($config['reemplaza']) && $config['reemplaza']==true) {
                        $this->_cleanEstadoCuenta($dateFechaCorte->getPeriod(), $socios, $options);
                    }
                    if (isset($config['showDebug'])==true && $config['showDebug']==true) {
                        throw new SociosException("El estado de cuenta no tiene movimiento a mostrar");
                    }
                    continue;
                }

                //ordenamos los movimientos segun las fechas
                $contentMovi = SociosCore::sortMoviContent($contentMovi);

                foreach ($contentMovi as $content) {
                    //totales
                    $totalCargos += $content['cargos'];
                    $totalAbonos += $content['abonos'];
                    $estadoCuentaDetalleArray[] = $content;

                    unset($content, $valorCargo, $valorAbono);
                }

                $periodoIni2 = SociosCore::subPeriodo($config["periodo"], 1);
                $year2 = substr($periodoIni2, 0, 4);
                $month2 = (int) substr($periodoIni2, 4, 2);
                if ($month2<10) {
                    $month2 = "0".$month2;
                }

                $fechaY = "$year2-$month2-01";
                $fechaYDate = new Date("$year2-$month2-01");
                $fechaYDate->getLastDayOfMonth();

                $saldoAnterior = 0;

                //$estadoCuenta = EntityManager::get('EstadoCuenta')->findFirst("socios_id='$sociosId' AND fecha<='{$fechaYDate->getDate()}'", "order: fecha DESC");
                $estadoCuenta = EntityManager::get('EstadoCuenta')->findFirst("socios_id='$sociosId' AND fecha<'{$config['fechaIni']}'", "order: fecha DESC");

                if ($estadoCuenta) {
                    //Usado en base al estado de cuenta anterior
                    $saldoAnterior = $estadoCuenta->getSaldoNuevo();
                } else {
                    //USado inicialmente por inicializacion de saldos
                    $cartera2Obj = EntityManager::get('Cartera')->find(array("nit='{$socios->getIdentificacion()}' AND f_emision<='$fechaY' AND tipo_doc='$tipoDocSocios'", 'columns'=>'cuenta,saldo,valor'));

                    $ic = 0;
                    foreach ($cartera2Obj as $cartera2) {
                        //Si es cuenta a favor puede estar negativo
                        if ($ctaCuentaAFavor==$cartera2->getCuenta()) {
                            $saldoAnterior += $cartera2->getSaldo();
                        } else {
                            $saldoAnterior += $cartera2->getValor();
                        }
                        unset($cartera2);

                        if ($ic>100) {
                            gc_collect_cycles();
                            $ic=0;
                        }
                        $ic++;
                    }
                    unset($cartera2Obj);
                }

                //throw new Exception($saldoAnterior.", SQL: socios_id='$sociosId' AND fecha<='{$fechaYDate->getDate()}'");

                $valorAPagar = $saldoAnterior + $totalCargos - $totalAbonos;

                $valorAPagarMora = $valorAPagar;
                if ($socios->getPorcMoraDesfecha()>0) {
                    $baseValor = $basePorcentaje * $socios->getPorcMoraDesfecha() / 100;

                    $porcIvaMora = Settings::get("socios_porc_iva_mora", "SO");
                    $porcIvaMora = intval($porcIvaMora);

                    $ivaBase = $baseValor * $porcIvaMora /100;
                    if ($valorAPagar>0) {
                        $valorAPagarMora +=  ($baseValor + $ivaBase);
                    }
                }

                $datos = array(
                    'sociosId'    => $socios->getSociosId(),
                    'fecha' => $fechaCorte,
                    'fechaSaldo' => $fechaLimitShow->getDate(),
                    'accion' =>  $socios->getNumeroAccion(),
                    'identificacion' =>  $socios->getIdentificacion(),
                    'nombre' =>  $socios->getNombres()." ".$socios->getApellidos(),
                    'saldoAnterior' => $saldoAnterior,
                    'totalCargos' => $totalCargos,
                    'totalAbonos' => $totalAbonos,
                    'valorAPagar' => $valorAPagar,
                    'valorAPagarMora' => $valorAPagarMora,
                    'contentMovi' => $contentMovi
                );

                //Almacenamos totales
                $totales[]= $datos;

                if (!isset($config['consolidado']) || $config['consolidado']!=true) {

                    //Guardamos en Tabla Estado de Cartera Consolidado
                    $estadoCuenta = $this->_saveEstadoCuentaConsolidado($datos);

                    unset($estadoCuenta, $datos);
                }
                unset($socios);

                if ($i>100) {
                    gc_collect_cycles();
                    $i=0;
                }
                $i++;
            }
            unset($sociosObj);

            ////////////////////////
            //TOTALES CONSOLIDADOS
            ////////////////////////

            $break = '';
            $countTotales = count($totales);
            $totalFinal = array(
                'saldoAnterior' => 0,
                'totalCargos' => 0,
                'totalAbonos' => 0,
                'valorAPagar' => 0,
                'valorAPagarMora' => 0
            );

            foreach ($totales as $datos) {
                $totalFinal['saldoAnterior'] += $datos['saldoAnterior'];
                $totalFinal['totalCargos'] += $datos['totalCargos'];
                $totalFinal['totalAbonos'] += $datos['totalAbonos'];
                $totalFinal['valorAPagar'] += $datos['valorAPagar'];
                $totalFinal['valorAPagarMora'] += $datos['valorAPagarMora'];

                unset($datos);
            }
            unset($totales, $contentMovi);

            gc_disable();

            return true;
        } catch (Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    /**
     * Valida si un socios tiene mas de un estado de cuenta este mes
     *
     * @param  date $fechaIni
     * @param  integer $sociosId
     */
    private function validaDuplicados($fechaIni, $sociosId)
    {
        $dateIni = new Date($fechaIni);
        $dateIni->toFirstDayOfMonth();

        $dateFin = clone $dateIni;
        $dateFin->toLastDayOfMonth();

        $estadoCuentas = EntityManager::get('EstadoCuenta')->find(
            "socios_id='$sociosId' AND fecha>='$dateIni' AND fecha<='$dateFin'",
            "order: fecha ASC"
        );
        if (count($estadoCuentas)) {
            $fechas = array();
            foreach ($estadoCuentas as $estadoCuenta) {
                if ($fechaIni != $estadoCuenta->getFecha()) {
                    $fechas[]= $estadoCuenta->getFecha();
                }
            }
            if (count($fechas)) {
                $socio = BackCacher::getSocios($sociosId);
                throw new Exception("El socio '" . $socio->getNumeroAccion() . " : " . $socio->getNombres() . " " . $socio->getApellidos() .
                    "' tiene otro estado de cuenta este mes: '" . join(",", $fechas) . "'", 1);
            }
        }
    }

    /**
     * Make estado de cuenta viejos es decir menores a febereo 01 de 2014
     *
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    private function _makeEstadoCuentaOld(&$config)
    {

        try {
            $totales = array();
            $transaction = TransactionManager::getUserTransaction();
            $datosClub = EntityManager::get('DatosClub')->findFirst();
            $consecutivo = EntityManager::get('Consecutivos')->findFirst();

            if (!isset($config['sociosId']) || $config['sociosId']<=0) {
                $sociosObj = EntityManager::get('Socios')->find(array("conditions" => "1=1", "order" => "CAST(numero_accion AS SIGNED) ASC"));
            } else {
                $sociosObj = EntityManager::get('Socios')->find("socios_id='{$config['sociosId']}'");
                if (!count($sociosObj)) {
                    throw new SociosException("El socio con id '{$config['sociosId']}' no existe");
                }
            }

            $periodoObj = EntityManager::get('Periodo')->findFirst("periodo='".$config["periodo"]."'");
            if (!$periodoObj->getDiaFactura()) {
                throw new SociosException("no se ha definido el dia de facturacion del periodo '".$config["periodo"]."'");
            }

            $periodoInt = $config["periodo"];

            $year = substr($periodoInt, 0, 4);
            $month = (int) substr($periodoInt, 4, 2);
            if ($month<10) {
                $month = "0".$month;
            }

            $day = $periodoObj->getDiaFactura();
            if ($day<10) {
                $day = "0".$day;
            }

            $fechaCorte = "$year-$month-".$day;
            $dateFechaCorte = new Date($fechaCorte);
            $config['fechaIni'] = $fechaCorte;

            //Dias de vencimiento de factura
            $diaVenc = $periodoObj->getDiasPlazo();
            if (!$diaVenc) {
                throw new SociosException("No se ha definido los dias de vencimiento del periodo actual");
            }


            //throw new SociosException($fechaCorte);

            $dateFechaLimite = new Date($fechaCorte);
            $dateFechaLimite->addDays($diaVenc);
            $lastDayOfMonth = date::getLastDayOfMonth($month, $year);

            $peridoSub2 = SociosCore::subPeriodo($periodoInt, 2);
            $year2L = substr($peridoSub2, 0, 4);
            $month2L = (int) substr($peridoSub2, 4, 2);

            //throw new SociosException($month2L);
            $fechaLimitShow = date::getLastDayOfMonth($month2L, $year);
            $fechaLimitShow->addDays(1);

            $fLimitShowLPeriod = $fechaLimitShow->getPeriod();
            $year2LP = substr($fLimitShowLPeriod, 0, 4);
            $month2LP = (int) substr($fLimitShowLPeriod, 4, 2);
            $fechaLimitShowL = date::getLastDayOfMonth($month2LP, $year2LP);

            //buscamos periodo
            $periodo = EntityManager::get('Periodo')->findFirst("periodo='{$dateFechaCorte->getPeriod()}'");
            if (!$periodo) {
                throw new SociosException("No existe un periodo registrado en socios para la fecha limite seleccionada");
            }

            //mensajes de Extracto
            $resumen = Settings::get('resumen_extracto', 'SO');
            $resumenRecaudo = Settings::get('resumen_recaudo', 'SO');
            if (!$resumenRecaudo) {
                $resumenRecaudo = '<br/><br/>';
            }

            //Mas fix
            $ctaCuentaAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');

            //Solo buscamos este tipo de documento para su saldo anterior los FAC se dejan sin contar
            $tipoDocSocios = Settings::get('tipo_doc', 'SO');

            //Base porcentaje Mora
            $basePorcentaje = Settings::get('base_porc_mora_desfecha', 'SO');
            if (!$basePorcentaje) {
                throw new SociosException("No se ha configurado base de procentaje de mora de estado de cuenta");
            }

            $tipoDocPos = Settings::get('tipo_doc_pos', 'SO');
            if (!$tipoDocPos) {
                throw new SociosException("No se ha definido el tipo de documento de POS en cartera");
            }

            $cuentasAjsEstadoCuenta = SociosCore::getCuentaAjusteEstadoCuenta();

            $comprobAjustes = Settings::get('comprob_ajustes', 'SO');
            if (!$comprobAjustes) {
                throw new SociosException('No se ha configurado el comprobante de ajustes de facturas en configuración');
            }

            $comprobNcStr = '';
            $comprobNc = Settings::get('comprob_nc', 'SO');
            if ($comprobNc) {
                $comprobNcStr = "'".$comprobNc."'";
                $comprobNcStr = ", ".str_replace(", ", "', '", $comprobNcStr);
            }

            //comprobante de pagos
            $comprobsPagos = Settings::get('comprobs_pagos', 'SO');
            if (!$comprobsPagos) {
                throw new SociosException("Es necesario dar los comprobantes de pagos en configuración");
            }

            $comprobsPagosA = explode(', ', $comprobsPagos);
            $comprobsPagosAs = implode("', '", $comprobsPagosA);

            //Cuenta de saldo a favor
            $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
            if (!$cuentaSaldoAFavor) {
                throw new SociosException("No se ha configurado la cuenta de saldo a favor de socios");
            }

            $options = array(
                'tipoDocSocios' => $tipoDocSocios,
                'tipoDocPos' => $tipoDocPos,
                'cuentasAjsEstadoCuenta' => $cuentasAjsEstadoCuenta,
                'comprobAjustes' => $comprobAjustes,
                'comprobNcStr' => $comprobNcStr,
                'comprobsPagosAs' => $comprobsPagosAs,
                'cuentaSaldoAFavor' => $cuentaSaldoAFavor
            );

            ///RECORREMOS SOCIOS A GENERAR
            foreach ($sociosObj as $socios) {

                $sociosId = $socios->getSociosId();

                $location = BackCacher::getLocation($socios->getCiudadCasa());
                if (!$location) {
                    throw new SociosException('No se ha asignado la ciudad de la casa en el maestro de socios');
                }
                $ciudad = $location->getName();

                $contentMoviHtml = "";

                $saldoMora = 0;
                $totalCargos = 0;
                $totalAbonos = 0;
                $estadoCuentaDetalleArray = array();

                $contentMovi = $this->getContentMoviOld($dateFechaCorte->getPeriod(), $socios, $options);

                if (count($contentMovi)<=0) {
                    //limpia estado de cuenta si existe cuando se calcula de nuevo y no tiene movimiento
                    $this->_cleanEstadoCuenta($dateFechaCorte->getPeriod(), $socios, $options);
                    if (isset($config['showDebug'])==true && $config['showDebug']==true) {
                        ob_end_clean();
                        throw new SociosException("El estado de cuenta no tiene movimiento a mostrar");
                    }
                    continue;
                }

                //ordenamos los movimientos segun las fechas
                $contentMovi = SociosCore::sortMoviContent($contentMovi);

                foreach ($contentMovi as $content) {
                    //totales
                    $totalCargos += $content['cargos'];
                    $totalAbonos += $content['abonos'];
                    $estadoCuentaDetalleArray[] = $content;

                    unset($content, $valorCargo, $valorAbono);
                }

                //$periodoIni2 = $config["periodo"] - 1;
                $periodoIni2 = SociosCore::subPeriodo($config["periodo"], 1);
                $year2 = substr($periodoIni2, 0, 4);
                $month2 = (int) substr($periodoIni2, 4, 2);
                if ($month2<10) {
                    $month2 = "0".$month2;
                }

                $fechaY = "$year2-$month2-01";
                $fechaYDate = new Date("$year2-$month2-01");
                $fechaYDate->getLastDayOfMonth();

                $saldoAnterior = 0;

                $estadoCuenta = EntityManager::get('EstadoCuenta')->findFirst("socios_id='$sociosId' AND fecha<='{$fechaYDate->getDate()}'", "order: fecha DESC");

                if ($estadoCuenta) {
                    //Usado en base al estado de cuenta anterior
                    $saldoAnterior = $estadoCuenta->getSaldoNuevo();
                } else {
                    //USado inicialmente por inicializacion de saldos
                    $cartera2Obj = EntityManager::get('Cartera')->find(array("nit='{$socios->getIdentificacion()}' AND f_emision<='$fechaY' AND tipo_doc='$tipoDocSocios'", 'columns'=>'cuenta,saldo,valor'));

                    foreach ($cartera2Obj as $cartera2) {
                        //Si es cuenta a favor puede estar negativo
                        if ($ctaCuentaAFavor==$cartera2->getCuenta()) {
                            $saldoAnterior += $cartera2->getSaldo();
                        } else {
                            $saldoAnterior += $cartera2->getValor();
                        }
                        unset($cartera2);
                    }
                    unset($cartera2Obj);
                }

                //throw new Exception($saldoAnterior.", SQL: socios_id='$sociosId' AND fecha<='{$fechaYDate->getDate()}'");

                $valorAPagar = $saldoAnterior + $totalCargos - $totalAbonos;

                $valorAPagarMora = $valorAPagar;
                if ($socios->getPorcMoraDesfecha()>0) {
                    $baseValor = $basePorcentaje * $socios->getPorcMoraDesfecha() / 100;

                    $porcIvaMora = Settings::get("socios_porc_iva_mora", "SO");
                    $porcIvaMora = intval($porcIvaMora);
                    
                    $ivaBase = $baseValor * $porcIvaMora /100;
                    if ($valorAPagar>0) {
                        $valorAPagarMora +=  ($baseValor + $ivaBase);
                    }
                }

                $datos = array(
                    'sociosId'  => $socios->getSociosId(),
                    'fecha' => $fechaCorte,
                    'fechaSaldo' => $fechaLimitShow->getDate(),
                    'accion' =>  $socios->getNumeroAccion(),
                    'identificacion' =>  $socios->getIdentificacion(),
                    'nombre' =>  $socios->getNombres()." ".$socios->getApellidos(),
                    'saldoAnterior' => $saldoAnterior,
                    'totalCargos' => $totalCargos,
                    'totalAbonos' => $totalAbonos,
                    'valorAPagar' => $valorAPagar,
                    'valorAPagarMora' => $valorAPagarMora,
                    'contentMovi' => $contentMovi
                );

                //Almacenamos totales
                $totales[]= $datos;

                if (!isset($config['consolidado']) || $config['consolidado']!=true) {

                    //Guardamos en Tabla Estado de Cartera Consolidado
                    $estadoCuenta = $this->_saveEstadoCuentaConsolidado($datos);

                    unset($estadoCuenta, $datos);
                }
                unset($socios);
            }
            unset($sociosObj);

            ////////////////////////
            //TOTALES CONSOLIDADOS
            ////////////////////////

            $break = '';
            $countTotales = count($totales);
            $totalFinal = array(
                'saldoAnterior' => 0,
                'totalCargos' => 0,
                'totalAbonos' => 0,
                'valorAPagar' => 0,
                'valorAPagarMora' => 0
            );

            foreach ($totales as $datos) {
                $totalFinal['saldoAnterior'] += $datos['saldoAnterior'];
                $totalFinal['totalCargos'] += $datos['totalCargos'];
                $totalFinal['totalAbonos'] += $datos['totalAbonos'];
                $totalFinal['valorAPagar'] += $datos['valorAPagar'];
                $totalFinal['valorAPagarMora'] += $datos['valorAPagarMora'];

                unset($datos);
            }
            unset($totales, $contentMovi);

            return true;
        } catch (Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    /**
     * Valida si un periodo esta abierto y si se puede guardar en contabilidad
     *
     * @param  integer $period
     */
    public static function checkPeriod($period)
	{
        $fechaCierre = EntityManager::get('Empresa')->findFirst()->getFCierrec();
		if ($fechaCierre) {
			$dateC = new Date($fechaCierre);

			$year  = substr($period, 0, 4);
			$month = substr($period, 4, 2);
			$dateS = new Date($year . "-" . $month . "-01");

			if (Date::isEarlier($dateS, $dateC)) {
				throw new AuraException("El periodo '$period' es menor al actual cierre contable '$fechaCierre', no se pueden guardar comprobantes");
			}
		}
	}
}
