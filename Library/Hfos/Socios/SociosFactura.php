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
 * @author         BH-TECK Inc. 2009-2010
 * @version        $Id$
 */
set_time_limit(0);

require_once 'SociosException.php';
/**
 * SociosFactura
 *
 * Clase central que controla procesos internos de facturación de Socios
 *
 */
class SociosFactura extends UserComponent
{

    private $_externalTransaction;
    private $_transaction;

    public function __constructor()
    {
        $this->_externalTransaction = TransactionManager::hasUserTransaction();
        $this->_transaction = TransactionManager::getUserTransaction();
    }

    /**
     * Agrega a un vector de configuracion datos staticos que solo se dben cargar una vez
     * @param Array $config Configuracion inicial
     */
    public function addConfigDefault(&$config)
    {
        //Tipo de Docuemnto usado en Socios
        if (!isset($config['tipoDocSocios'])) {
            $tipoDocSocios = Settings::get('tipo_doc', 'SO');
            if (!$tipoDocSocios) {
                throw new SociosException("No se ha definido el tipo de documento de la facturacion de socios");
            }
            $config['tipoDocSocios'] = $tipoDocSocios;
        }

        //Tipo de Docuemnto usado en POS
        if (!isset($config['tipoDocPos'])) {
            $tipoDocPos = Settings::get('tipo_doc_pos', 'SO');
            if (!$tipoDocPos) {
                throw new SociosException("No se ha definido el tipo de documento de consumos de socios");
            }
            $config['tipoDocPos'] = $tipoDocPos;
        }

        //Buscamos si hay vales de consumo en pos para agregar a factura
        if (!isset($config['showPos'])) {
            $showPos = Settings::get('show_ordenes_pos', 'SO');
            if (!$showPos) {
                throw new SociosException('No se ha definido si se desea ver o no los vales de consumo de pos en la factura en configuración');
            }
            $config['showPos'] = $showPos;
        }

        //Calcula mora de factura o estado de cuenta
        if (!isset($config['calcularMoraDe'])) {
            $calcularMoraDe = Settings::get('calcular_mora_de', 'SO');
            if (!$calcularMoraDe) {
                throw new SociosException('No se ha definido de que forma desea calcular la mora en la factura en configuración');
            }
            $config['calcularMoraDe'] = $calcularMoraDe;
        }

        //Show saldo anterior?
        if (!isset($config['showSaldoAnterior'])) {
            $showSaldoAnterior = Settings::get('show_saldo_anterior_fac', 'SO');
            if (!$showSaldoAnterior) {
                throw new Exception("No se ha definido si se desea ver saldo anterior");
            }
            $config['showSaldoAnterior'] = $showSaldoAnterior;
        }

        //Show showFacturaPos?
        if (!isset($config['showFacturaPos'])) {
            $showFacturaPos = Settings::get('show_ordenes_pos', 'SO');
            if (!$showFacturaPos) {
                throw new Exception("No se ha definido si se desea ver facturas de consumo");
            }
            $config['showFacturaPos'] = $showFacturaPos;
        }

        //cargo fijo mora
        if (!isset($config['cargoFijoMoraId'])) {
            $cargoFijoMoraId = Settings::get('cargo_fijo_mora', 'SO');
            if (!$cargoFijoMoraId) {
                throw new SociosException("No se ha definido el cargo fijo asociado a la mora por saldo anterior");
            }
            $config['cargoFijoMoraId'] = $cargoFijoMoraId;
        }

        //cargo fijo de ajuste de socios
        if (!isset($config['cargoFijoAjusteId'])) {
            $cargoFijoAjusteId = Settings::get('cargo_fijo_ajuste', 'SO');
            if (!$cargoFijoAjusteId) {
                throw new SociosException("No se ha definido el cargo fijo asociado a los ajustes");
            }
            $config['cargoFijoAjusteId'] = $cargoFijoAjusteId;
        }

        //Cargo fijo de consumo minimo
        if (!isset($config['ctaCM'])) {
            $ctaCM = Settings::get('cargo_fijo_consumo_minimo', 'SO');
            if (!$ctaCM) {
                throw new SociosException("No se ha definido el cargo fijo de la cuota minima");
            }
            $config['ctaCM'] = $ctaCM;
        }

        //Comprobante de factura
        if (!isset($config['codigoComprob']) || !isset($config['comprobFactura'])) {
            $codigoComprob = Settings::get('comprob_factura', 'SO');
            if ($codigoComprob=='') {
                throw new SociosException('No se ha configurado el comprobante de socios');
            }
            $config['codigoComprob'] = $codigoComprob;
            $config['comprobFactura'] = $codigoComprob;
        }
    }
    
    /**
     * Metodo que obtiene el saldo anterior
     * 
     * @param array $config(
     *     SociosId    => 1,
     *     periodo        => '2011'
     * )
     * @param ActiveRecordTransaction $this->_transaction
     * 
     * @return double
     */
    public function calcularSaldoAnterior($config)
    {

        if (isset($config['SociosId'])==false || $config['SociosId']<=0) {
            throw new SociosException('Es necesario dar el id del socio');
        }
        
        $Socios = BackCacher::getSocios($config['SociosId']);
        if ($Socios==false) {
            throw new SociosException('El socio no existe');
        }
        $sociosId    = $config['SociosId'];

        if (isset($config['periodo'])==false || $config['periodo']<=0) {
            throw new SociosException('Es necesario dar el periodo para calcular su mora');
        }

        $periodo = $config['periodo'];
        $periodoAnterior = EntityManager::get('Periodo')->setTransaction($this->_transaction)->maximum('periodo', 'conditions: periodo < "'.$periodo.'"');
        if ($periodoAnterior==false) {
            return 0;
        }
        $config['periodoAnterior'] = $periodoAnterior;

        //Solo buscar cuentas de cargos fijos
        if (!isset($config['listaCuentasCargosFijosStr'])) {
            $listaCuentasCargosFijosStr = implode(',', $this->_getCuentasCargosFijos());
            $config['listaCuentasCargosFijosStr'] = $listaCuentasCargosFijosStr;
        }

        //Datos DEFAULT
        $tipoDocSocios = $config['tipoDocSocios'];
        $tipoDocPos = $config['tipoDocPos'];

        //buscamos en cartera solo saldos con cuentas de cargos fijos
        $total = 0;
        $saldoCarteraObj = $this->Cartera->setTransaction($this->_transaction)->find(array('conditions'=>"nit='{$Socios->getIdentificacion()}' AND tipo_doc IN('$tipoDocSocios','$tipoDocPos')"));
        foreach ($saldoCarteraObj as $saldoCartera) {
            $total += $saldoCartera->getSaldo();
            unset($total);
        }
        
        unset($saldoCarteraObj, $Socios);

        return $total;
    }


    /**
     * Metodo que calcula la mora de factura
     * 
     * @param array $config(
     *     SociosId    => 1,
     *     periodo     => '2011'
     * )
     * @param ActiveRecordTransaction $this->_transaction
     * 
     * @return double
     */
    public function calcularMora(&$config)
    {
        if (isset($config['SociosId'])==false || $config['SociosId']<=0) {
            throw new SociosException('Es necesario dar el id del socio');
        }

        $Socios = BackCacher::getSocios($config['SociosId']);
        if ($Socios==false) {
            throw new SociosException('El socio con id "'.$config['SociosId'].'" no existe');
        }
        $sociosId    = $config['SociosId'];
        $nit = $Socios->getIdentificacion();
        
        if (isset($config['periodo'])==false || $config['periodo']<=0) {
            throw new SociosException('Es necesario dar el periodo para calcular su mora');
        }
        $periodo = $config['periodo'];
        $periodoAnterior = EntityManager::get('Periodo')->setTransaction($this->_transaction)->maximum('periodo', 'conditions: periodo < "'.$periodo.'"');
        if ($periodoAnterior==false) {
            throw new Exception("Se esta calculando mora de un periodo no creado ".($periodo-1));
        }
        //throw new Exception("periodo: $periodo, periodoAnt: " . print_r($periodoAnterior, true), 1);
        
        $config['periodoAnterior'] = $periodoAnterior;
        $fechaFactura = $config['fechaFactura'];

        #sacamos saldo de cartera siempre
        $base = $config['saldoAnteriorMora'];
        //throw new Exception("base: $base", 1);
        
        $ret = 0;
        if ($base>0) {

            //Periodo
            $periodo = EntityManager::get('Periodo')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'periodo = "'.$periodoAnterior.'"'));
            if ($periodo==false) {
                throw new SociosException('El periodo '.$periodoAnterior.' no existe');
            }

            $interesMoraPeriodo = (float) $periodo->getInteresesMora();

            //Restamos pagos del periodo
            $fechaActual = new Date($fechaFactura);
	    $fechaActual->toLastDayOfMonth();
	    $lastDay = $fechaActual->getDay();
	    	
            $year = substr($fechaFactura, 0, 4);
	    $month = substr($fechaFactura, 5, 2);
	    
	    switch($lastDay) {
		case '31':
			$day = "30";
			break;
		case '30':
                        $day = "29";
                        break;
		case '29':
                        $day = "28";
                        break;
		case '28':
                        $day = "27";
                        break;
		default:
			throw new Exception("No se ha definido bien el dia de fact sostenimineto en calculo de saldoAnterio");
			break;
	    }

	    $fechaFactura2 = $year . "-" . $month . "-" . $day;
	    $existenPagos = SociosCore::getPagosPeriodoXSocioExists($fechaActual->getPeriod(), $nit);
            if ($existenPagos==true) {
                //throw new Exception("Existen pagos este mes, baseAnt: $base", 1);
                //CUENTAS SOCIOS
		$cuentasSocios = Settings::get('cuenta_ajustes_estado_cuenta', 'SO');

		//CARGO FIJO CONSUMO MINIMO
		$ctaConsumos = Settings::get('cuenta_consumos', 'SO');
		if (!$ctaConsumos) {
			throw new Exception("La cuenta de consumos no esta parametrizada en configuraci�n");
		}
		//DIF TOTAL CUENTAS SOCIOS SIN CONSUMOS
           	$deb = EntityManager::get('Movi')->sum(array("column"=>"valor", "conditions"=>"nit='$nit' AND fecha<'$fechaFactura2' AND cuenta LIKE '$cuentasSocios%' AND cuenta!='$ctaConsumos' AND deb_cre='D'")); 
           	$cre = EntityManager::get('Movi')->sum(array("column"=>"valor", "conditions"=>"nit='$nit' AND fecha<'$fechaFactura2' AND cuenta LIKE '$cuentasSocios%' AND cuenta!='$ctaConsumos' AND deb_cre='C'"));
		$diff = $deb - $cre;
		if ($diff == 0) {
			$base = 0;
		} else {
			if ($diff > 0 ) {
				$base = $diff;
			} else {
				$base = 0;
			}
		}

		//CONSUMOS PERIODO ACTUAL
		$debConsumos = EntityManager::get('Movi')->sum(array("column"=>"valor", "conditions"=>"nit='$nit' AND fecha<'$year-$month-01' AND cuenta LIKE '$cuentasSocios%' AND cuenta='$ctaConsumos' AND deb_cre='D'"));
                $creConsumos = EntityManager::get('Movi')->sum(array("column"=>"valor", "conditions"=>"nit='$nit' AND fecha<'$year-$month-01' AND cuenta LIKE '$cuentasSocios%' AND cuenta='$ctaConsumos' AND deb_cre='C'"));
                $diffConsumos = $debConsumos - $creConsumos;
		$base += $diffConsumos;
		if ($base<0) {
			$base = 0;
		}

		//PAGOS Y AJUSTES
		$comprobArray = array();
		$comprobAjustes = Settings::get('comprob_ajustes', 'SO');
                if (!$comprobAjustes) {
                        throw new Exception("El comprobante de ajuste no esta parametrizado en configuraci?n");
                }
		$comprobNCStr = Settings::get('comprob_nc', 'SO');
                if (!$comprobNCStr) {
                        throw new Exception("Los comprobantes de nota contable no esta parametrizado en configuraci?n");
                }
		$comprobNCArray = explode(",", $comprobNCStr);
		$comprobPagosStr = Settings::get('comprobs_pagos', 'SO');
                if (!$comprobPagosStr) {
                        throw new Exception("Los comprobantes de pagos no esta parametrizado en configuraci?n");
                }
		$comprobPagosArray = explode(",", $comprobPagosStr);

		//agregando comprobantes
		$comprobArray[]=$comprobAjustes;
		$comprobArray = array_merge($comprobArray, $comprobNCArray);
		$comprobArray = array_merge($comprobArray, $comprobPagosArray);
		$comprobStr = "'" . implode("','", $comprobArray) . "'";		

		//buscamos movimiento de este mes
		$debPagos = 0;
		//EntityManager::get('Movi')->sum(array("column"=>"valor", "conditions"=>"nit='$nit' AND fecha>'$year-$month-01' AND fecha<'$fechaFactura2' AND cuenta LIKE '$cuentasSocios%' AND cuenta='$ctaConsumos' AND deb_cre='D'"));
                $crePagos = EntityManager::get('Movi')->sum(array("column"=>"valor", "conditions"=>"nit='$nit' AND fecha>'$year-$month-01' AND fecha<'$fechaFactura2' AND cuenta LIKE '$cuentasSocios%' AND cuenta='$ctaConsumos' AND deb_cre='C'"));
                $diffPagos = $debPagos - $crePagos;
                $base += $diffPagos;
                if ($base<0) {
                        $base = 0;
                }	
            }
	
            //throw new SociosException("base: $base, fechaFactura2: $fechaFactura2, fechaFactura: $fechaFactura, deb: $deb, cre: $cre, diff: $diff, creConsumos: $creConsumos, debConsumos: $debConsumos, diffConsumos: $diffConsumos, comprobs: $comprobStr, debPagos: $debPagos, crePagos: $crePagos, diffPagos: $diffPagos");
            
            $ret = $base * $interesMoraPeriodo / 100;
	    $ret = LocaleMath::round($ret, 0); 
        }

	if ($ret <= 1) {
	  $ret = 0;
	}

        return $ret;
    }

    /**
     * Metodo que calcula la mora x dias segun facturas
     * 
     * @param array $config(
     *     SociosId    => 1,
     *     periodo        => '2011'
     * )
     * @param ActiveRecordTransaction $this->_transaction
     * 
     * @return double
     */
    public function calcularMoraXFecha(&$config)
    {
        if (isset($config['SociosId'])==false || $config['SociosId']<=0) {
            throw new SociosException('Es necesario dar el id del socio');
        }

        $Socios = BackCacher::getSocios($config['SociosId']);
        if ($Socios==false) {
            throw new SociosException('El socio con id "'.$config['SociosId'].'" no existe');
        }
        $sociosId = $config['SociosId'];
        $nit = $Socios->getIdentificacion();
        
        if (isset($config['periodo'])==false || $config['periodo']<=0) {
            throw new SociosException('Es necesario dar el periodo para calcular su mora');
        }
        $periodo = $config['periodo'];
        $periodoAnterior = EntityManager::get('Periodo')->setTransaction($this->_transaction)->maximum('periodo', 'conditions: periodo < "'.$periodo.'"');
        if ($periodoAnterior==false) {
            throw new Exception("Se esta calculando mora de un periodo no creado ".($periodo-1));
        }

        $config['periodoAnterior'] = $periodoAnterior;
        $fechaFactura = $config['fechaFactura'];
        
        $ret = 0;
            
        //Saldo de estado de cuenta anterior
        $base = $config['saldoAnteriorMora'];
        
        if ($base>0) {

            //Porcentaje de mora en el periodo anterior
            $periodo = EntityManager::get('Periodo')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'periodo = "' . $periodoAnterior . '"'));
            if ($periodo==false) {
                throw new SociosException('El periodo '.$periodoAnterior.' no existe');
            }
            $interesMoraPeriodo = (float) $periodo->getInteresesMora();
            
            /*//Obtenemos fecha limite de pago de estado de cuenta
            //Contamos dias de mora
            $dias = SociosCore::diffDiasFechas($fechaFactura, $fechaLimitePago->getDate());

            //Sacamos mora diaria
            $moraDiaria = ($base * $interesMoraPeriodo / 100) / 30;

            //mora
            $mora = $dias * $moraDiaria;
            
            $ret = $mora;
*/
            unset($fechaLimitePago, $dias, $moraDiaria, $mora);
        }
        unset($base, $Socios, $sociosId, $nit, $periodo, $fechaFactura, $periodoAnterior);

        return $ret;
    }

    /**
     * Limpia los movimientos en bd socios de un periodo
     * @param Array $config
     * @return Boolean
     */
    public function cleanMovimientoSocios(&$config)
    {
            
        try {
            $this->_transaction = TransactionManager::getUserTransaction();
            
            $conditions = "fecha_at='{$config['fechaFactura']}'";
            if (isset($config['sociosId']) && $config['sociosId']>0) {
                $conditions .= "AND socios_id='{$config['sociosId']}'";
            }

            $conditionsd = "fecha='{$config['fechaFactura']}'";
            if (isset($config['sociosId']) && $config['sociosId']>0) {
                $conditionsd .= "AND socios_id='{$config['sociosId']}'";
            }
            
            //Borramos detalles de movimiento
            $movimientoObj = EntityManager::get('Movimiento')->setTransaction($this->_transaction)->find($conditions);
            foreach ($movimientoObj as $movimientoRow) {
                //Borramos movimiento
                $status = $movimientoRow->setTransaction($this->_transaction)->delete();
                
                if ($status==false) {
                    throw new SociosException("No se pudo borrar el movimiento de la fecha el movimiento con id ({$movimientoRow->getId()})");
                }

                unset($movimientoRow);
            }
            //Borramos detalles de movimiento
            $detalleMovimientoObj = EntityManager::get('DetalleMovimiento')->setTransaction($this->_transaction)->find($conditionsd);
            foreach ($detalleMovimientoObj as $detalleMovimiento) {
                //Borramos movimiento
                $status = $detalleMovimiento->setTransaction($this->_transaction)->delete();
                
                if ($status==false) {
                    throw new SociosException("No se pudo borrar el detalle de movimiento de la fecha el movimiento con id ({$detalleMovimiento->getId()})");
                }

                unset($detalleMovimiento);
            }
            
            unset($movimientoObj, $detalleMovimiento, $conditions, $movimiento);
            
            return true;
        } catch (Exception $e) {
            throw new SociosException("cleanMovimientoSocios: ".$e->getMessage());
        }
    }

    
    
    /**
     * Obtiene los saldos pedientes de los socios
     * @param Array $configSaldos
     * @return Array
     */
    public function obtenerSaldosCartera($configSaldos)
    {
        $saldosCartera = array();
        
        $conditions = 'saldo!=0';
        if (isset($configSaldos['sociosId']) && $configSaldos['sociosId']) {
                
            $socios = BackCacher::getSocios($configSaldos['sociosId']);
            
            if ($socios->getIdentificacion()) {
                $conditions .= " AND nit='{$socios->getIdentificacion()}'";
            }
            
            unset($socios);
        }

        //Datos DEFAULT
        if (!isset($configSaldos['tipoDocSocios'])) {
            $this->addConfigDefault($configSaldos);
        }
        $tipoDocSocios = $configSaldos['tipoDocSocios'];
        $tipoDocPos = $configSaldos['tipoDocPos'];

        if (!isset($configSaldos['fechaFactura'])) {
            throw new SociosException("No se ha definido la fecha limite a buscar saldos");
        }
        $fechaFactura = $configSaldos['fechaFactura'];

        $conditions .= " AND f_emision < '$fechaFactura' AND tipo_doc IN ('$tipoDocPos','$tipoDocSocios')";

        //throw new SociosException($conditions);
        
        $i=0;
        $numeroMeses = array();
        $carteraObj = EntityManager::get('Cartera')->setTransaction($this->_transaction)->find($conditions);
        foreach ($carteraObj as $cartera) {
            $nit = $cartera->getNit();
            if (!isset($saldosCartera[$nit])) {
                $saldosCartera[$nit] = 0;
            }
            
            $saldosCartera[$nit] += $cartera->getSaldo();
            
            //Agregamos conteo de meses
            $fechaTemp = new Date($cartera->getFEmision());
            $fechaTempPeriod = $fechaTemp->getPeriod();
            
            if (!isset($numeroMeses[$nit])) {
                $numeroMeses[$nit] = array();
            }
            $numeroMeses[$nit][$fechaTempPeriod] = $fechaTempPeriod;
            unset($cartera);
        }
        
        unset($carteraObj);
        
        //agregamos numero de meses
        $saldosCartera['numeroMeses'] = $numeroMeses;
         
        return $saldosCartera;
    }
    
    /**
     * Proceso que genera el movimiento de cargos fijos de un socio
     * @param Array $config
     * @return Int numeroLineas
     */
    private function _generarMovimientoCargosFijosYCartera(&$config)
    {

        try
        {
            gc_enable();
            $this->_transaction = TransactionManager::getUserTransaction();

            //Agregamos a configuracion datos estaticos de configuracion
            $this->addConfigDefault($config);

            $sociosCore = new SociosCore();
                
            $date = new Date($config['fechaFactura']);
            if (isset($config['sociosId'])) {
                $sociosId = $config['sociosId'];
            }
            
            $fechaFactura = $config['fechaFactura'];
            $fechaVenc = $config['fechaVenc'];
            $periodoAbierto = $config['periodoAbierto'];
            $comprobFactura = $config['comprobFactura'];
            $periodoAnterior = $config['periodoAnterior'];
            $cargosFijosArray = $config['cargosFijos'];
            $tipoSociosArray = $config['tipoSocios'];
            
            //Obtenemos los saldos pendientes
            if (!isset($config['saldosCarteraArray'])) {
                $saldosCarteraArray = $this->obtenerSaldosCartera($config);
                $config['saldosCarteraArray'] = $saldosCarteraArray;
            } else {
                $saldosCarteraArray = $config['saldosCarteraArray'];
            }
            
            //Buscamos si hay vales de consumo en pos para agregar a factura
            $showPos = $config['showPos'];
                    
            //Recorremos socios para generar cada cargo o saldo en factura que tenga estado Activo
            if (isset($config['sociosId'])) {
                $sociosAll = EntityManager::get('Socios')->find(array("socios_id='{$config['sociosId']}'", 'columns' => 'socios_id,identificacion,titular_id,numero_accion,estados_socios_id,tipo_socios_id,genera_mora,consumo_minimo,ajuste_sostenimiento'));
            } else {
                $sociosAll = EntityManager::get('Socios')->find(array("1=1", 'columns' => 'socios_id,identificacion,titular_id,numero_accion,estados_socios_id,tipo_socios_id,genera_mora,consumo_minimo,ajuste_sostenimiento', 'order'=>'CAST(numero_accion AS SIGNED) ASC'));
            }
            
            if (!count($sociosAll)) {
                //throw new SociosException('No se encontro socios con cobro="Si" a generar movimiento.');
            }
            
            //Calcula mora de factura o estado de cuenta
            $calcularMoraDe = $config['calcularMoraDe'];

            //tipo documento de pos
            $tipoDoc = $config['tipoDocSocios'];

            //tipo documento de pos
            $tipoDocPos = $config['tipoDocPos'];

            //Show saldo anterior?
            $showSaldoAnterior = $config['showSaldoAnterior'];

            //Show showFacturaPos?
            $showFacturaPos = $config['showFacturaPos'];

            //cargo fijo mora
            $cargoFijoMoraId = $config['cargoFijoMoraId'];

            $cargoFijoAjusteId = $config['cargoFijoAjusteId'];

            //Validamos si el periodo actual es de la factura
            $dateX = new Date($fechaFactura);
            if ($dateX->getPeriod()!=$periodoAbierto) {
                throw new SociosException("El periodo actual '$periodoAbierto' no esta en el periodo de la factura a generar '$fechaFactura'");
            }

            $periodoIni2 = $periodoAbierto;
            $year2 = substr($periodoIni2, 0, 4);
            $month2 = substr($periodoIni2, 4, 2);
            $fechaY = "$year2-$month2-01";
            $fechaLimit2 = new Date($fechaY);
            $fechaLimit2->toLastDayOfMonth();

            $periodoIni3 = SociosCore::subPeriodo($periodoAbierto, 1);
            $year3 = substr($periodoIni3, 0, 4);
            $month3 = substr($periodoIni3, 4, 2);
            $fechaZ = "$year3-$month3-01";
            $fechaLimit3 = new Date($fechaZ);
            $fechaLimit3->toLastDayOfMonth();

            $i=0;
            foreach ($sociosAll as $socios) {
                //id
                $sociosId     = $socios->getSociosId();
                    
                //Generamos movimiento
                $movimiento = new Movimiento();
                $movimiento->setTransaction($this->_transaction);
                
                //Crea o modifica un movimiento maestro
                $movimiento->setSociosId($sociosId);
                $movimiento->setPeriodo($periodoAbierto);
                $movimiento->setFacturaId(0);//temporal
                $movimiento->setIvaMora(0);//temporal

                //Add fecha de factura a movimiento para multiples facturas
                $movimiento->setFechaAt($fechaFactura);

                ////////////////////////////
                //Obtenemos el saldo anterior
                ////////////////////////////
                $saldoAnterior = 0;
                $saldoAnteriorMora = 0;
                $nitSocio = $socios->getIdentificacion();

                switch ($calcularMoraDe) {
                    case 'F': //Factura
                        //Saldo de Mora de factura
                        if (isset($saldosCarteraArray[$nitSocio]) && $saldosCarteraArray[$nitSocio]) {
                            $saldoAnteriorMora = $saldosCarteraArray[$socios->getIdentificacion()];
                        }
                        break;
                    case 'E'://Estado cuenta
                    case 'D'://mora diaria
                        $estadoCuenta = EntityManager::get('EstadoCuenta')->findFirst("socios_id='$sociosId' AND fecha<'{$fechaLimit2->getDate()}'", "order: fecha desc");
                        if ($estadoCuenta) {
                            $saldoAnteriorMora = $estadoCuenta->getSaldoNuevo();
                            //throw new SociosException($saldoAnteriorMora);
                        }
                        break;
                    default:
                        throw new SociosException('No se ha definido el tipo de calculo del saldo anterior');
                        break;

                }

                //throw new SociosException($saldoAnteriorMora);
                
                $saldoAnterior = LocaleMath::round($saldoAnterior, 0);
                $movimiento->setSaldoAnterior($saldoAnterior);

                ///////////////////////
                //Calculamos La Mora
                ///////////////////////
                $mora = 0;
                $ivaMora = 0;
                if ($saldoAnteriorMora>0 && $saldoAnterior>=0 && $config['g_interesesMora']=='M' && $socios->getGeneraMora()=='S') {
                    
                    //Config de calulos de saldos y mora
                    $configCalculos = array(
                        'SociosId' => $sociosId,
                        'periodo' => $periodoAbierto,
                        'saldoAnteriorMora' => $saldoAnteriorMora,
                        'fechaFactura' => $fechaFactura
                    );
                    
                    //Calculo de mora por días
                    $tipoMora = SociosCore::getSettingsValue('calcular_mora_de', 'SO');

                    switch ($tipoMora) {
                        case 'E':
                            //Estado de cuenta
                            $mora = $this->calcularMora($configCalculos);
                            break;
                        case 'D':
                            //Por fecha de Factura x mora diaria
                            $mora = $this->calcularMoraXFecha($configCalculos);
                            break;
                        default:
                            throw new SociosException("no se reconoce el tipo de calculo de mora en configuracion");
                            break;
                    }

                    unset($configCalculos);
                    
                    $mora = LocaleMath::round($mora, 0);

                    //Calculamos el iva de la mora
                    $ivaMora = $mora * 16 / 100;
                    $ivaMora = LocaleMath::round($ivaMora, 0);
                }
                
                $movimiento->setMora($mora);
                $movimiento->setIvaMora($ivaMora);

                //Obtenemos la suma de los cargos del periodo al socio
                $cargosMes = EntityManager::get('CargosSocios')->setTransaction($this->_transaction)->sum('cuota_aplicar', 'conditions: socios_id="'.$sociosId.'" AND fecha="'.$config['fechaFactura'].'"');
                $cargosMes = LocaleMath::round($cargosMes, 0);
                $movimiento->setCargosMes($cargosMes);

                //Asignamos el saldo nuevo
                $saldoActual = $saldoAnterior + $mora + $ivaMora + $cargosMes;
                $saldoActual = LocaleMath::round($saldoActual, 0);
                $movimiento->setSaldoActual($saldoActual);
                
                //Save
                if ($movimiento->save()==false) {
                    foreach ($movimiento->getMessages() as $message) {
                        throw new SociosException('movimiento: '.$message->getMessage());
                    }
                }

                $movimientoId = $movimiento->getId();

                //Saldos Anteriores
                if ($saldoAnterior && $showSaldoAnterior=='S') {

                    $detalleMovimiento = new DetalleMovimiento();
                    $detalleMovimiento->setTransaction($this->_transaction);
                    $detalleMovimiento->setMovimientoId($movimientoId);
                    $detalleMovimiento->setSociosId($sociosId);
                    $detalleMovimiento->setFecha($date->getDate());
                    $detalleMovimiento->setFechaVenc($fechaVenc->getDate());
                    $detalleMovimiento->setTipo('C');//DEBITO o CREDITO
                    $detalleMovimiento->setTipoDocumento($comprobFactura);//FACTURA
                    $detalleMovimiento->setCargosSociosId(0);
                    $detalleMovimiento->setDescripcion('SALDO PERIODO '.$periodoAnterior);
                    $detalleMovimiento->setValor($saldoAnterior);
                    $detalleMovimiento->setIva(0);
                    $detalleMovimiento->setIco(0);
                    $detalleMovimiento->setTotal($saldoAnterior);
                    $detalleMovimiento->setEstado('A'); //Activo
                    $detalleMovimiento->setTipoMovi('S');//SALDO ANTERIOR

                    if ($detalleMovimiento->save()==false) {
                        foreach ($detalleMovimiento->getMessages() as $message) {
                            throw new SociosException('SALDO ANTERIOR:'.$message->getMessage());
                        }
                    }

                    unset($detalleMovimiento);
                }

                //Saldos Anteriores Mora
                if ($mora>0) {
                    $cargoFijoMora = BackCacher::getCargosFijos($cargoFijoMoraId);
                    if (!$cargoFijoMora) {
                        throw new SociosException("El cargo fijo seleccionado a la mora no existe");
                    }
                    $mora = LocaleMath::round($mora, 0); 
                    $ivaMora = LocaleMath::round($ivaMora, 0); 
                    $detalleMovimiento = new DetalleMovimiento();
                    $detalleMovimiento->setTransaction($this->_transaction);
                    $detalleMovimiento->setMovimientoId($movimientoId);
                    $detalleMovimiento->setSociosId($sociosId);
                    $detalleMovimiento->setFecha($date->getDate());
                    $detalleMovimiento->setFechaVenc($fechaVenc->getDate());
                    $detalleMovimiento->setTipo($cargoFijoMora->getNaturaleza());//DEBITO o CREDITO
                    $detalleMovimiento->setTipoDocumento($comprobFactura);//FACTURA
                    $detalleMovimiento->setCargosSociosId(0);
                    $detalleMovimiento->setDescripcion($cargoFijoMora->getNombre().' PERIODO '.$periodoAnterior);
                    $detalleMovimiento->setValor($mora);
                    $detalleMovimiento->setIva($ivaMora);
                    $detalleMovimiento->setIco(0);
                    $detalleMovimiento->setTotal($mora+$ivaMora);
                    $detalleMovimiento->setEstado('A');//Activo
                    $detalleMovimiento->setTipoMovi('M');//MORA

                    if ($detalleMovimiento->save()==false) {
                        foreach ($detalleMovimiento->getMessages() as $message) {
                            throw new SociosException('MORA: '.$message->getMessage());
                        }
                    }
                    unset($detalleMovimiento,$cargoFijoMora);
                }
                
                //Buscamos cargos fijos asignados de un socios en especifico en la fecha a facturar
                $cargosSociosObj = EntityManager::get('CargosSocios')->setTransaction($this->_transaction)->find(array('conditions'=>"socios_id={$socios->getSociosId()} AND fecha='{$config['fechaFactura']}' "));

                foreach ($cargosSociosObj as $cargosSocio) {
                    if (!isset($cargosFijosArray[$cargosSocio->getCargosFijosId()])) {
                        throw new SociosException("El cargo fijo con id '{$cargosSocio->getCargosFijosId()}' no existe");
                    }
                    
                    //buscamos los cargos asignado a ese socio que esten activos y sin procesar
                    $cargosFijos = $cargosFijosArray[$cargosSocio->getCargosFijosId()];

                    if (!isset($cargosFijos['naturaleza']) || !$cargosFijos['naturaleza']) {
                        throw new SociosException("El cargo fijo '{$cargosFijos['id']}/{$cargosFijos['nombre']}' no tiene definido que naturaleza es");
                    }

                    //Creamos detalle de movmiento
                    $detalleMovimiento = new DetalleMovimiento();
                    $detalleMovimiento->setTransaction($this->_transaction);
                    $detalleMovimiento->setMovimientoId($movimientoId);
                    $detalleMovimiento->setSociosId($sociosId);
                    $detalleMovimiento->setFecha($date->getDate());
                    $detalleMovimiento->setFechaVenc($fechaVenc->getDate());
                    $detalleMovimiento->setTipo($cargosFijos['naturaleza']); //DEBITO o CREDITO
                    $detalleMovimiento->setTipoDocumento($comprobFactura); //FACTURA
                    $detalleMovimiento->setCargosSociosId($cargosSocio->getId());
                    $detalleMovimiento->setDescripcion($cargosSocio->getDescripcion().' PERIODO '.$periodoAbierto);

                    $valor = $cargosSocio->getValor();
                    $valor = LocaleMath::round($valor, 0);

                    if ($valor<=0) {
                        continue;
                    }
                    $detalleMovimiento->setValor($valor);

                    $iva = $cargosSocio->getIva();
                    $iva = LocaleMath::round($iva, 0);
                    $detalleMovimiento->setIva($iva);

                    $ico = $cargosSocio->getIco();
                    $ico = LocaleMath::round($ico, 0);
                    $detalleMovimiento->setIco($ico);

                    $totalCA = $cargosSocio->getCuotaAplicar();
                    $totalCA = LocaleMath::round($totalCA, 0);
                    $detalleMovimiento->setTotal($totalCA);

                    $detalleMovimiento->setEstado('A');//Activo
                    $detalleMovimiento->setTipoMovi('C');//CARGO FIJO

                    if ($detalleMovimiento->save()==false) {
                        foreach ($detalleMovimiento->getMessages() as $message) {
                            throw new SociosException($message->getMessage());
                        }
                    }

                    //Cargo socio
                    $cargosSocio->setTransaction($this->_transaction);
                    $cargosSocio->setEstado('P');//Paso de A a P de procesado
                    if ($cargosSocio->save()==false) {
                        foreach ($cargosSocio->getMessages() as $message) {
                            throw new SociosException('Cargo Socio: '.$message->getMessage());
                        }
                    }
                    
                    unset($saldoAnterior, $mora, $cargosSocio, $detalleMovimiento, $valor, $iva, $totalCA);
                }
                
                ///////////////////////////
                //Lectura de POS No pagadas
                ///////////////////////////
                    
                //Obtenemos facturas pasadas a cartera
                $detallePOS = array();

                if ($showFacturaPos=='S') {
                
                    $facturaHotelObj = $sociosCore->getFacturasCartera((int) $periodoAbierto, $socios->getIdentificacion());
                    
                    if (isset($facturaHotelObj['facturas']) && count($facturaHotelObj['facturas'])>0) {
                        foreach ($facturaHotelObj['facturas'] as $facturaHotel) {
                            //Existe un saldo a esa factura? Es decir ya la pagaron?
                            $saldoFacturaEnCartera = EntityManager::get('Cartera')->findFirst("tipo_doc='$tipoDocPos' AND numero_doc='{$facturaHotel['numfac']}' AND saldo>0");

                            if ($saldoFacturaEnCartera) {
                                if (!isset($detallePOS[1])) {
                                    $detallePOS[1] = array(
                                        'detalle' => $facturaHotel['prefac'].'-'.$facturaHotel['numfac'],
                                        'iva'     => $facturaHotel['totiva'],
                                        'ico'     => $facturaHotel['totimp'],
                                        'total'   => $facturaHotel['total'],
                                    );
                                } else {
                                    $detallePOS[1]['detalle']  .= ', ' . $facturaHotel['prefac'].'-'.$facturaHotel['numfac'];
                                    $detallePOS[1]['iva']      += $facturaHotel['totiva'];
                                    $detallePOS[1]['ico']      += $facturaHotel['totimp'];
                                    $detallePOS[1]['total']    += $facturaHotel['total'];
                                }
                            }

                            unset($detalleMovimiento, $facturaPos, $facturaHotel, $saldoFacturaEnCartera);
                        }
                    }

                
                    /////////////////////////
                    //Agregar ajuste de Consumos
                    /////////////////////////
                    $ajusteConsumosObj = EntityManager::get('AjusteConsumos')->setTransaction($this->_transaction)->find(array("socios_id='$sociosId' AND periodo='$periodoAbierto'"));
                    if (count($ajusteConsumosObj)>0) {
                        foreach ($ajusteConsumosObj as $ajusteConsumos) {
                            if (!isset($detallePOS[1])) {
                                $detallePOS[1] = array(
                                    'detalle'     => $ajusteConsumos->getPrefijo().'-'.$ajusteConsumos->getNumero(),
                                    'iva'         => $ajusteConsumos->getIva(),
                                    'total'       => $ajusteConsumos->getValor(),
                                );
                            } else {
                                $detallePOS[1]['detalle']  .= ', '.$ajusteConsumos->getPrefijo().'-'.$ajusteConsumos->getNumero();
                                $detallePOS[1]['iva']      += $ajusteConsumos->getIva();
                                $detallePOS[1]['total']    += $ajusteConsumos->getValor();
                            }
                            unset($ajusteConsumos);
                        }
                    }
                    unset($ajusteConsumosObj);

                    if (isset($detallePOS[1]['detalle'])) {
                        $detallePOS[1]['detalle'] = 'PUNTO DE VENTA ('.$detallePOS[1]['detalle'].')';
                    }
                    
                    if (isset($detallePOS[1])) {
                        
                        $detalle = $detallePOS[1]['detalle'];
                        $total = $detallePOS[1]['total'];
                        $iva = $detallePOS[1]['iva'];
                        $ico = $detallePOS[1]['ico'];
                        $valor = $total - $iva - $ico;

                        //Creamos detalle de movimiento
                        $detalleMovimiento = new DetalleMovimiento();
                        $detalleMovimiento->setTransaction($this->_transaction);
                        $detalleMovimiento->setMovimientoId($movimientoId);
                        $detalleMovimiento->setSociosId($sociosId);
                        $detalleMovimiento->setFecha($date->getDate());
                        $detalleMovimiento->setFechaVenc($fechaVenc->getDate());
                        $detalleMovimiento->setTipo('C'); //CREDITO
                        $detalleMovimiento->setTipoDocumento($comprobFactura); //FACTURA
                        $detalleMovimiento->setCargosSociosId(0);
                        $detalleMovimiento->setDescripcion($detalle);
                        $detalleMovimiento->setValor($valor);
                        $detalleMovimiento->setIva($iva);
                        $detalleMovimiento->setIco($ico);
                        $detalleMovimiento->setTotal($total);
                        $detalleMovimiento->setEstado('A');//Activo
                        $detalleMovimiento->setTipoMovi('P');//PUNTO DE VENTA

                        if ($detalleMovimiento->save()==false) {
                            foreach ($detalleMovimiento->getMessages() as $message) {
                                throw new SociosException('POS:'.$message->getMessage());
                            }
                        }
                        
                            
                    }
                }
                
                /////////////////
                //CONSUMO MINIMO
                /////////////////
                if (!$socios->getTipoSociosId() || !isset($tipoSociosArray[$socios->getTipoSociosId()])) {
                    throw new SociosException("El socio con accion '{$socios->getNumeroAccion()}' no tiene asignado un tipo de socio valido/activo");
                }
                
                //Segun tipo de socios hay un valor de consumo minimo
                $tipoSociosObj = $tipoSociosArray[$socios->getTipoSociosId()];
                $cuotaMinimaValor = $tipoSociosObj['cuota_minima'];
                $cuotaMinimaIva = ($cuotaMinimaValor*16/100);
                $cuotaMinima = $cuotaMinimaValor + $cuotaMinimaIva;
                
                $moraCuota = $tipoSociosObj['mora_cuota'];
                
                //Si cuota minima es mayor a cero y se selecciono al generar factura ademas el socio si se le cobra consumo minimo
                if ($cuotaMinima>0 && $config['g_consumoMinimo']=='C' && $socios->getConsumoMinimo()=='S') {

                    ////////////////////////////////////////////////////
                    // CONSUMOS DE POS DIRECTOS NO EN HOTEL5
                    ////////////////////////////////////////////////////
                    $facturasDirectasPos = $sociosCore->getFacturasDirectasPos($periodoAbierto, $socios->getIdentificacion());
                    
                    //throw new SociosException(print_r($facturasDirectasPos,true));

                    $totalFDP = 0;
                    //Contamos consumos de Factura Directas de POS
                    foreach ($facturasDirectasPos as $factura) {
                        $totalFDP += $factura['total'];
                        unset($factura);
                    }

                    ////////////////////////////////////////////////////
                    // CONSUMOS DE POS DIRECTOS EN HOTEL5
                    ////////////////////////////////////////////////////
                    $facturasDirectasHotel = $sociosCore->getFacturasDirectasAll($periodoAbierto, $socios->getIdentificacion());

                    $totalFD = 0;
                    $detalleCM = 'CONSUMO MINIMO';
                    
                    //throw new SociosException(print_r($facturasDirectasHotel,true));
                    
                    //Contamos consumos de Factura Directas de POS
                    foreach ($facturasDirectasHotel as $facturaHotel) {
                        $totalFD += $facturaHotel['total'];
                        unset($facturaHotel);
                    }
                    unset($facturasDirectasHotel);
                    

                    $totalCM = $cuotaMinima - ($totalFD+$totalFDP);
                    //throw new SociosException($totalCM);
                    
                    //Obetenems el cargo fijos del socio
                    $ctaCM = $config['ctaCM'];

                    $cFCM = BackCacher::getCargosFijos($ctaCM);
                    if (!$cFCM) {
                        throw new SociosException("No existe el cargo fijo '$ctaCM' de la cuota minima");
                    }

                    //Sacamos BASE
                    $valorCM = $totalCM / 1.16;
                    $valorCM = LocaleMath::round($valorCM);

                    //throw new SociosException($valorCM);
                    
                    //IVA
                    $ivaCM = 0;
                    if ($cFCM->getPorcentajeIva()>0) {
                        $porcIvaCM = (int) $cFCM->getPorcentajeIva();
                        $ivaCM = (($valorCM * $porcIvaCM) / 100);
                        $ivaCM = LocaleMath::round($ivaCM);
                        //throw new SociosException($ivaCM."= $totalCM * $porcIvaCM / 100");
                        $totalCM += $ivaCM;
                    }

                    //Si total es mayor a cero quiere decir que falto a saldo para cumplir cuota minima
                    if ($totalCM>0 && $valorCM>0) {
                        //Creamos detalle de movimiento
                        $detalleMovimiento = new DetalleMovimiento();
                        $detalleMovimiento->setTransaction($this->_transaction);
                        $detalleMovimiento->setMovimientoId($movimientoId);
                        $detalleMovimiento->setSociosId($sociosId);
                        $detalleMovimiento->setFecha($config['fechaFactura']);
                        $detalleMovimiento->setFechaVenc($config['fechaVencimiento']);
                        $detalleMovimiento->setTipo('C'); //CREDITO
                        $detalleMovimiento->setTipoDocumento($comprobFactura); //FACTURA
                        $detalleMovimiento->setCargosSociosId(0);
                        $detalleMovimiento->setDescripcion($cFCM->getNombre());
                        $detalleMovimiento->setValor($valorCM);
                        $detalleMovimiento->setIva($ivaCM);
                        $detalleMovimiento->setIco(0);
                        $detalleMovimiento->setTotal($totalCM);
                        $detalleMovimiento->setEstado('A');//Activo
                        $detalleMovimiento->setTipoMovi('N');//CUOTA MINIMA

                        if ($detalleMovimiento->save()==false) {
                            foreach ($detalleMovimiento->getMessages() as $message) {
                                throw new SociosException('POS CUOTA MINIMA:'.$message->getMessage());
                            }
                        }
                    }
                    //throw new SociosException($totalCM);
                    
                }

                ////////////////////////
                //Novedades de Factura
                ////////////////////////

                //si desea facturar novedades tendra 'N'
                if ($config['g_novedades']=='N') {
                    $date = $config['date'];
                    $novedadesFacturaObj = EntityManager::get('NovedadesFactura')->find("periodo='{$date->getPeriod()}' AND socios_id='$sociosId' AND estado='A'");

                    foreach ($novedadesFacturaObj as $novedadesFactura) {
                        $cargosFijosObj = BackCacher::getCargosFijos($novedadesFactura->getCargosFijosId());
                        if (!$cargosFijosObj) {
                            throw new SociosException("El cargo fijo con id '{$novedadesFactura->getCargosFijosId()}' no existe");
                        }

                        $cargosFijos = SociosCore::modelToArray($cargosFijosObj);

                        if (!isset($cargosFijos['naturaleza']) || !$cargosFijos['naturaleza']) {
                            throw new SociosException("El cargo fijo '{$cargosFijos['id']}/{$cargosFijos['nombre']}' no tiene definido que naturaleza es");
                        }

                        //Creamos detalle de movimiento
                        $detalleMovimiento = new DetalleMovimiento();
                        $detalleMovimiento->setTransaction($this->_transaction);
                        $detalleMovimiento->setMovimientoId($movimientoId);
                        $detalleMovimiento->setSociosId($sociosId);
                        $detalleMovimiento->setFecha($date->getDate());
                        $detalleMovimiento->setFechaVenc($fechaVenc->getDate());
                        $detalleMovimiento->setTipo('C'); //CREDITO
                        $detalleMovimiento->setTipoDocumento($comprobFactura); //FACTURA
                        $detalleMovimiento->setCargosSociosId(0);
                        $detalleMovimiento->setDescripcion($cargosFijos['nombre']);
                        $detalleMovimiento->setValor($novedadesFactura->getValor());
                        $detalleMovimiento->setIva($novedadesFactura->getIva());
                        $detalleMovimiento->setIco($novedadesFactura->getIco());
                        $detalleMovimiento->setTotal($novedadesFactura->getValor()+$novedadesFactura->getIva());
                        $detalleMovimiento->setEstado('A');//Activo
                        $detalleMovimiento->setTipoMovi('O');//Novedad de Factura

                        if ($detalleMovimiento->save()==false) {
                            foreach ($detalleMovimiento->getMessages() as $message) {
                                throw new SociosException('NOVEDAD DE FACTURA:'.$message->getMessage());
                            }
                        }
                        unset($novedadesFactura,$cargosFijos);
                    }
                    unset($novedadesFacturaObj);
                }

                //////////////////////////////////////////////
                // Calculo automatico de Ajuste Sostenimineto
                //////////////////////////////////////////////

                //si desea facturar ajuste sostenimiento tendra 'T'
                
                if ($config['g_ajusteSostenimiento']=='T' && $socios->getAjusteSostenimiento()=='S') {
                    //throw new SociosException($config['g_ajusteSostenimiento'].",".$socios->getAjusteSostenimiento());
    
                    $date = $config['date'];

                    $periodo4 = SociosCore::subPeriodo($periodoAbierto, 1);
                    $year4 = substr($periodo4, 0, 4);
                    $month4 = substr($periodo4, 4, 2);
                    $fecha4 = "$year4-$month4-01";
                    $fechaLimit4 = new Date($fecha4);
                    $fechaLimit4->toLastDayOfMonth();

                    $year5 = substr($periodoAbierto, 0, 4);
                    $month5 = substr($periodoAbierto, 4, 2);
                    $fecha5 = "$year5-$month5-01";
                    $fechaLimit5 = new Date($fecha5);
                    $fechaLimit5->toLastDayOfMonth();
    

                    //buscamos factura de sostenimiento si tiene
                    $conditionsFSoste = "fecha_factura='$fecha5' AND socios_id='$sociosId'";
                    //throw new SociosException($conditionsFSoste);
                    
                    $facturaSoste = EntityManager::get('Factura')->findFirst($conditionsFSoste);
                    if ($facturaSoste) {
                        $fechaSosteVenc = $facturaSoste->getFechaVencimiento();

                        //buscamos pagos que se realizaron a esta factura CXS - ###
                        $conditionsMSoste = "comprob!='$comprobFactura' AND tipo_doc='$tipoDoc' AND numero_doc='{$facturaSoste->getNumero()}' AND deb_cre='C'";
                        //throw new SociosException($conditionsMSoste);
                        
                        $moviSoste = EntityManager::get('Movi')->findFirst($conditionsMSoste);
                        
                        //si hay pago
                        if ($moviSoste) {

                            //throw new SociosException("Aqui ,{$moviSoste->getFecha()} - {$facturaSoste->getFechaVencimiento()},".Date::isEarlier($moviSoste->getFecha(), $facturaSoste->getFechaVencimiento()));

                            if (!Date::isEarlier($moviSoste->getFecha(), $facturaSoste->getFechaVencimiento()) && $facturaSoste->getFechaVencimiento()!=$moviSoste->getFecha()) {
                                $cargosFijos = BackCacher::getCargosFijos($cargoFijoAjusteId);
                                if (!$cargosFijos) {
                                    throw new SociosException("El cargo fijo con id '$cargoFijoAjusteId' no existe");
                                }

                                //calculamos cuanto es el ajuste de sostenimineto segun maestro de socios
                                $calculosSoste = SociosCore::getAjusteSostenimiento($sociosId);

                                //Creamos detalle de movimiento
                                $detalleMovimiento = new DetalleMovimiento();
                                $detalleMovimiento->setTransaction($this->_transaction);
                                $detalleMovimiento->setMovimientoId($movimientoId);
                                $detalleMovimiento->setSociosId($sociosId);
                                $detalleMovimiento->setFecha($date->getDate());
                                $detalleMovimiento->setFechaVenc($fechaVenc->getDate());
                                $detalleMovimiento->setTipo('C'); //CREDITO
                                $detalleMovimiento->setTipoDocumento($comprobFactura); //FACTURA
                                $detalleMovimiento->setCargosSociosId(0);
                                $detalleMovimiento->setDescripcion($cargosFijos->getNombre());
                                $detalleMovimiento->setValor($calculosSoste['base']);
                                $detalleMovimiento->setIva($calculosSoste['iva']);
                                $detalleMovimiento->setIco($calculosSoste['ico']);
                                $detalleMovimiento->setTotal($calculosSoste['total']);
                                $detalleMovimiento->setEstado('A');//Activo
                                $detalleMovimiento->setTipoMovi('T');//Ajuste Sostenimiento

                                if ($detalleMovimiento->save()==false) {
                                    foreach ($detalleMovimiento->getMessages() as $message) {
                                        throw new SociosException('AJUSTE SOSTENIMIENTO:'.$message->getMessage());
                                    }
                                }
                            }
                        } else {
                            //NO HIZO NINGUN PAGO
                            $cargosFijos = BackCacher::getCargosFijos($cargoFijoAjusteId);
                            if (!$cargosFijos) {
                                throw new SociosException("El cargo fijo con id '$cargoFijoAjusteId' no existe");
                            }

                            //calculamos cuanto es el ajuste de sostenimineto segun maestro de socios
                            $calculosSoste = SociosCore::getAjusteSostenimiento($sociosId);

                            //Creamos detalle de movimiento
                            $detalleMovimiento = new DetalleMovimiento();
                            $detalleMovimiento->setTransaction($this->_transaction);
                            $detalleMovimiento->setMovimientoId($movimientoId);
                            $detalleMovimiento->setSociosId($sociosId);
                            $detalleMovimiento->setFecha($date->getDate());
                            $detalleMovimiento->setFechaVenc($fechaVenc->getDate());
                            $detalleMovimiento->setTipo('C'); //CREDITO
                            $detalleMovimiento->setTipoDocumento($comprobFactura); //FACTURA
                            $detalleMovimiento->setCargosSociosId(0);
                            $detalleMovimiento->setDescripcion($cargosFijos->getNombre());
                            $detalleMovimiento->setValor($calculosSoste['base']);
                            $detalleMovimiento->setIva($calculosSoste['iva']);
                            $detalleMovimiento->setIco($calculosSoste['ico']);
                            $detalleMovimiento->setTotal($calculosSoste['total']);
                            $detalleMovimiento->setEstado('A');//Activo
                            $detalleMovimiento->setTipoMovi('T');//Ajuste Sostenimiento

                            if ($detalleMovimiento->save()==false) {
                                foreach ($detalleMovimiento->getMessages() as $message) {
                                    throw new SociosException('AJUSTE SOSTENIMIENTO:'.$message->getMessage());
                                }
                            }
                        }
                    }
                }

                if ($i>100) {
                    gc_collect_cycles();
                    $i=0;
                }

                $i++;
                unset($movimiento, $cargosSociosObj, $socios, $sociosId);
            }
            
            unset($sociosAll);
            gc_disable();
            
            return true;
        } catch (SociosException $e) {
            throw new SociosException($e->getMessage() . print_r($e, true));
        } catch (Exception $e) {
            throw new SociosException("_generarMovimientoCargosFijosYCartera: ".$e->getMessage().", Line:".print_r($e->getTrace(), true));
        }
    }

    /**
     * Metodo que genera el movimiento del periodo antes de hacer la factura
     * 
     * @param array $config(
     *     SociosId    => 1,
     *     periodo        => '2011'
     * )
     * @param ActiveRecordTransaction $this->_transaction
     * 
     * @return double
     */
    public function generarMovimiento(&$config)
    {
        set_time_limit(0);
    
        try
        {
                        
            $this->_transaction = TransactionManager::getUserTransaction();

            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');

            //Agregamos a configuracion datos estaticos de configuracion
            $this->addConfigDefault($config);

            $periodo = SociosCore::getCurrentPeriodo();
            $periodoAbierto = $periodo;
            
            $sociosCore = new SociosCore();
            
            //Obtenemos cargos fijos
            $cargosFijos = $sociosCore->getCargosFijos();
            $config["cargosFijos"] = $cargosFijos;
            
            //Obtenemos Tipos Socios
            $tipoSocios = $sociosCore->getTipoSocios();
            $config["tipoSocios"] = $tipoSocios;
            
            //Obtenemos periodo
            $periodo = EntityManager::get('Periodo')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'periodo="'.$periodoAbierto.'"'));
            if ($periodo==false) {
                throw new SociosException('No existe el periodo "'.$periodoAbierto.'"');
            }
            $config['periodoAbierto'] = $periodoAbierto;

            //obtenemos datos de cartera
            $config['porcMora'] = $periodo->getInteresesMora();

            //obtenemos datos de cartera
            $diaVenc = $periodo->getDiasPlazo();//Dias de vencimiento de factura
            if (!$diaVenc) {
                throw new SociosException('No se ha configurado el día de vencimiento de factura en configuración');
            }
            $config['diaVenc'] = $diaVenc;
            
            //obtenemos datos de cartera
            $comprobFactura = $config['comprobFactura'];
            
            //generamos fechas de rango de factura dadas por formulario
            $fechaFactura = $config['fechaFactura'];
            $fechaVencimiento = $config['fechaVencimiento'];
            
            $date = new Date($fechaFactura);
            
            try
            {
                $fechaVenc = new Date($fechaVencimiento);
            }
            catch(DateException $e) {
                $fechaVenc = clone $date;
                $fechaVenc->toLastDayOfMonth();
            }
            
            $config['date'] = $date;
            $config['fechaVenc'] = $fechaVenc;
            
            //limpiamos los movimientos de un(s) socio(s) en un periodo
            $movimientoBorrado = $this->cleanMovimientoSocios($config);
            
            // && true==false
            if ($movimientoBorrado==true) {

                $periodo            = (int) $config['periodo'];
                $periodoAnterior    = SociosCore::subPeriodo($periodo, 1);
                $config['periodoAnterior'] = $periodoAnterior;
                
                //Generamos cargos fijos en movimiento
                $flag = $this->_generarMovimientoCargosFijosYCartera($config);
                if (!$flag) {
                    throw new SociosException('No se pudo generar el movimiento');
                }
            }
            
            unset($periodo,$config,$periodoAnterior,$movimientoBorrado,$fechaVenc,$date,$comprobFactura);
            
            return true;
        }
        catch (Exception $e) {
            throw new SociosException("generarMovimiento: ".$e->getMessage());
        }
    }


    /**
    * Graba tercero si no existe
    */
    public function checkTercero($socios)
    {
        if (!$this->_transaction) {
            $this->_transaction = TransactionManager::getUserTransaction();
        }

        SociosCore::saveTerceros($socios->getSociosId(), $this->_transaction);
    }


    /**
    * Make factura
    *
    * @param array $config
    * @param ActiveRecordTransaction $this->_transaction
    */
    private function _makeFactura(&$config)
    {

        try
        {
            gc_enable();

            $this->_transaction = TransactionManager::getUserTransaction();

            //Configuraicon por defecto
            $this->addConfigDefault($config);

            //Comprobante de factura
            $codigoComprob = $config['codigoComprob'];

            //////////////////////////////
            // SOCIOS CON CARGOS FIJOS
            //////////////////////////////

            //Buscamos los socios distintos en el periodo
            if (isset($config['sociosId']) && $config['sociosId']>0) {
                //throw new SociosException("periodo='{$config['periodoAbierto']}' AND socios_id='{$config['sociosId']}'");
                $movimientoObj = EntityManager::get('Movimiento')->setTransaction($this->_transaction)->find(array('conditions'=>"fecha_at='{$config['fechaFactura']}' AND socios_id='{$config['sociosId']}'"));
            } else {
                $movimientoObj = EntityManager::get('Movimiento')->setTransaction($this->_transaction)->find(array('conditions'=>"fecha_at='{$config['fechaFactura']}'"));
            }

            $options = array();

            //Recorremos socios y se genera la factura para cada uno
            foreach ($movimientoObj as $movimiento) {
                $consecutivoFactura = $this->getConsecutivoFactura();
            
                //Si se dijo que solo a un socio los demas son omitidos
                if (isset($config['sociosId']) && $config['sociosId']>0) {
                    if ($config['sociosId'] != $movimiento->getSociosId()) {
                        unset($movimiento);
                        continue;
                    }
                }

                //se busca informacio de socio
                $sociosId = $movimiento->getSociosId();

                $socios = BackCacher::getSocios($sociosId);
                if ($socios==false) {
                    throw new SociosException('El socios con id "'.$sociosId.'" no existe!');
                }
                $config['sociosIdMovi'] = $sociosId;

                //miramos si el socio no existe en terceros (nits)
                $this->checkTercero($socios);
                
                //Se mira el detalle del movimiento si existe
                $detalleMovimiento = EntityManager::get('DetalleMovimiento')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"movimiento_id='".$movimiento->getId()."' AND fecha='{$config['fechaFactura']}'"));
                if ($detalleMovimiento==false) {

                    //Limpia detalle de factura si no hay nada que calcular pero deja en existencia
                    $factura = EntityManager::get('Factura')->setTransaction($this->_transaction)->findFirst("socios_id='$sociosId' AND fecha_factura='{$config['fechaFactura']}'");
                    if ($factura) {
                        //Limpiamos el detalle de factura
                        $status = $this->limpiarDetalleFactura($factura->getId());

                        //Invoicer
                        $invoicer = EntityManager::get('Invoicer')->setTransaction($this->_transaction)->findFirst("numero='{$factura->getNumero()}' AND fecha_emision='{$config['fechaFactura']}'");
                        if ($invoicer) {
                            //Borramos Detalle Invoicer por si hay basura
                            $this->_cleanDetalleInvoicer($invoicer->getId());
                        }
                    }
                    unset($factura, $movimiento);

                    continue;
                }
                
                //Si no hay registro de movimiento en facturas es un nuevo registro
                $flagNew = false;
                $factura = EntityManager::get('Factura')->setTransaction($this->_transaction)->findFirst("socios_id='$sociosId' AND fecha_factura='{$config['fechaFactura']}'");
                if (!$factura) {
                    $flagNew = true;
                    $factura = new Factura();
                    $factura->setTransaction($this->_transaction);
                    $factura->setNumero($consecutivoFactura);
                }

                //Inicia transaccion en la factura
                $factura->setSociosId($sociosId);
                $factura->setMovimientoId($movimiento->getId());
                $factura->setFechaFactura($config['fechaFactura']);
                $factura->setPeriodo($config['periodoAbierto']);
                $factura->setFechaVencimiento($config['fechaVencimiento']);
                $factura->setSaldoVencido($movimiento->getSaldoAnterior());
                $factura->setSaldoMora($movimiento->getMora());
                $factura->setDiasMora(0);
                $factura->setMoraPagado(0);
                $factura->setCuotaVigente($movimiento->getCargosMes());
                $factura->setVigentePagado(0);

                $totalFactura = 0;
                $detalleMovimientoObj = EntityManager::get('DetalleMovimiento')->setTransaction($this->_transaction)->find(array("conditions"=>"movimiento_id='{$movimiento->getId()}' AND estado='A' AND fecha='{$config['fechaFactura']}'"));
                foreach ($detalleMovimientoObj as $detalleMovimiento) {
                    $totalFactura += $detalleMovimiento->getTotal();
                    unset($detalleMovimiento);
                }
                
                $factura->setTotalFactura($totalFactura);
                $factura->setEstado('D');//Debe

                /////////////
                //Prestamos
                /////////////
                $valUltAbono = 0;
                $fecUltAbono = '';
                $salAntNeto = $salAntInteres = $cargoMes = 0;
                
                //Obtenemos el estado actual de saldo de prestamo
                $prestamosSocios = EntityManager::get('PrestamosSocios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId.' AND estado="D"'));
                
                $cargoMes = 0;

                //Si hay prestamos
                if ($prestamosSocios!=false) {
                
                    //Buscamos en amortizacion la cuota pendiente
                    $amortizacion = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId().' AND estado="D"', 'order'=>'numero_cuota ASC'));
                    
                    if ($amortizacion!=false) {
                        $cargoMes = LocaleMath::round($amortizacion->getValor(), 0);
                        $salAntNeto = $amortizacion->getSaldo()+$amortizacion->getValor();
                        $salAntInteres = $amortizacion->getInteres();
                    }

                }

                $config['cargoMes'] = $cargoMes;
                $config['salAntNeto'] = $salAntNeto;
                $config['salAntInteres'] = $salAntInteres;

                $factura->setValUltAbono($valUltAbono);
                $factura->setFecUltAbono($fecUltAbono);
                $factura->setSalAntNeto($salAntNeto);
                $factura->setSalAntInteres($salAntInteres);
                $factura->setCargoMes($cargoMes);
                $factura->setSalActual($totalFactura+$cargoMes);

                //Obtenemos mora de periodo
                $interesMora = SociosCore::getCurrentPeriodoMora($config['periodoAbierto']);

                $valMora = ($factura->getSalActual()*$interesMora)/100;
                $factura->setSalActMora($factura->getSalActual()+$valMora);
                
                //guardar
                if ($factura->save()==false) {
                    foreach ($factura->getMessages() as $msg) {
                        throw new SociosException('factura: '.$msg->getMessage());
                    }
                }

                //Detalle factura
                $config['factura'] = $factura;
                $config['facturaId'] = $factura->getId();
                $config['movimiento'] = $movimiento;
                $config['detalleMovimiento'] = $detalleMovimientoObj;

                //Creamos detalle de factura
                $statusDF = $this->_detalleFactura($config);
                if ($statusDF==false) {
                    continue;
                }

                //Asignamos el id de la factura al movimiento
                $movimiento->setFacturaId($factura->getId());
                if ($movimiento->save()==false) {
                    foreach ($movimiento->getMessages() as $msg) {
                        throw new SociosException($msg->getMessage());
                    }
                }

                //Add prestamos a factura
                $this->_addPrestamosToFactura($config);

                //Validamos datos de ingresos a terceros
                $this->_addResumenes($config);

                //Array SociosData
                $sociosData = $this->actualizaDatosDeFacturaArray($socios);

                //Array FacturaData
                $facturaData = $this->actualizaDatosDeFacturaArray($factura);

                $n = 0;
                $detalleFacturaData = array();
                foreach ($config['detalleFacturasObj'] as $detalleFactura) {
                    $detallateFacturaData     = $this->actualizaDatosDeFacturaArray($detalleFactura);
                    $detalleFacturaData[$n] = $detallateFacturaData;
                    $n++;
                    unset($detalleFactura);
                }

                //Array MovimientoData
                $movimientoData = $this->actualizaDatosDeFacturaArray($movimiento);

                $n = 0;
                $detalleMovimientoData = array();
                foreach ($config['detalleMovimiento'] as $detalleMovimiento) {
                    //Obtenemos arrayData del modelo
                    $detalleMovimientoArrayData = $this->actualizaDatosDeFacturaArray($detalleMovimiento);
                    $detalleMovimientoData[$n]     = $detalleMovimientoArrayData;

                    $cargoFijoId = 'SALDO';

                    ////////////////////////
                    // ASIGNACION DE CARGOS
                    ////////////////////////
                    $cargosSocios = $this->CargosSocios->findFirst($detalleMovimiento->getCargosSociosId());
                    if ($cargosSocios!=false) {
                        $cargoFijoId = $cargosSocios->getCargosFijosId();
                    }
                    
                    //////////////
                    //CUOTA MINIMA
                    /////////////
                    if ($detalleMovimiento->getTipoMovi()=='N') {
                        $cargosFijos = EntityManager::get('CargosFijos')->findFirst("nombre='{$detalleMovimiento->getDescripcion()}'");
                        if ($cargosFijos!=false) {
                            $cargoFijoId = $cargosFijos->getId();
                        }
                    }

                    //////////////
                    //NOVEDADES
                    /////////////
                    if ($detalleMovimiento->getTipoMovi()=='O') {
                        $cargosFijos = EntityManager::get('CargosFijos')->findFirst("nombre='{$detalleMovimiento->getDescripcion()}'");
                        if ($cargosFijos!=false) {
                            $cargoFijoId = $cargosFijos->getId();
                        }
                    }

                    ///////////////////////
                    //AjUSTE SOSTENIMIENTO
                    ///////////////////////
                    if ($detalleMovimiento->getTipoMovi()=='T') {
                        $cargosFijos = EntityManager::get('CargosFijos')->findFirst("nombre='{$detalleMovimiento->getDescripcion()}'");
                        if ($cargosFijos!=false) {
                            $cargoFijoId = $cargosFijos->getId();
                        }
                    }

                    //RESULTADO
                    $detalleMovimientoData[$n]['cargos_fijos_id']     = $cargoFijoId;
                    $detalleMovimientoData[$n]['cantidad']             = 1;
                    $detalleMovimientoData[$n]['descuento']         = 0;

                    $n++;
                }

                //generar invoicing
                $optionsLocal = array(
                    'apps'                    => 'SO',//Socios
                    'periodo'                => $config['periodoAbierto'],
                    'items'                 => $config['items'],
                    'precios'                 => $config['precios'],
                    'cantidades'             => $config['cantidades'],
                    'descuentos'             => $config['descuentos'],
                    'codigoComprob'            => $codigoComprob,
                    'numeroAccion'             => $socios->getNumeroAccion(),
                    'nitDocumento'             => $socios->getIdentificacion(),
                    'nitEntregarDocumento'     => $socios->getIdentificacion(),
                    'fechaVencimiento'         => $config['fechaVenc']->getDate(),
                    'fechaFactura'            => $config['date']->getDate(),
                    'prestamo'                => $config['prestamoArray'],
                    'consecutivos'            => $config['consecutivosId'],

                    //Debug
                    'socios'                => $sociosData,
                    'factura'                => $facturaData,
                    'detalleFacturas'        => $detalleFacturaData,
                    'detalleFacturasObj'    => $config['detalleFacturasObj'],
                    'movimiento'            => $movimientoData,
                    'detalleMovimiento'        => $detalleMovimientoData,
                    'resumenVenta'            => $config['resumenVenta'],
                    'resumenIva'            => $config['resumenIva'],
                    'resumenIco'            => $config['resumenIco'],
                );

                //para un socio
                if (isset($config['sociosId'])) {
                    $optionsLocal['facturas'] = array($config['sociosId']);
                }

                $options[] = $optionsLocal;

                unset($optionsLocal);

                //solo aumenta si es nuevo
                if ($flagNew==true) {
                    $this->setConsecutivoFactura($consecutivoFactura+1);
                }

                gc_collect_cycles();
            }

            //////////////////////////////
            // SOCIOS CON SOLO CONVENIOS
            //////////////////////////////

            if (!count($movimientoObj)) {

                if (isset($config['sociosId']) && $config['sociosId']>0) {
                    $prestamosSociosObj = EntityManager::get('PrestamosSocios')->setTransaction($this->_transaction)->find("socios_id='{$config['sociosId']}' AND estado='D'");
                } else {
                    $prestamosSociosObj = EntityManager::get('PrestamosSocios')->setTransaction($this->_transaction)->find("estado='D'");
                }

                //Recorremos socios y se genera la factura para cada uno
                foreach ($prestamosSociosObj as $prestamosSocios) {
                    //ID
                    $config['sociosId'] = $sociosId = $prestamosSocios->getSociosId();

                    $socios = BackCacher::getSocios($sociosId);
                    if ($socios==false) {
                        throw new SociosException('El socios con id "'.$sociosId.'" no existe!');
                    }
                    $config['sociosIdMovi'] = $sociosId;

                    //miramos si el socio no existe en terceros (nits)
                    $this->checkTercero($socios);
                    
                    //////////////////////
                    // No anula porque esta replazando el existente
                    /////////////////////
                    //anular facturas del periodo
                    /*$this->anularFacturasPeriodo(array(
                        'facturas'=>array($sociosId),
                        'fechaFactura'=>$config['fechaFactura'], 
                        'nits'=>array($socios->getIdentificacion()))
                    );*/
                    
                    //Si no hay registro de movimiento en facturas es un nuevo registro
                    $flagNew = false;
                    $factura = EntityManager::get('Factura')->setTransaction($this->_transaction)->findFirst("socios_id='$sociosId' AND fecha_factura='{$config['fechaFactura']}'");
                    if (!$factura) {
                        $flagNew = true;
                        $factura = new Factura();
                        $factura->setTransaction($this->_transaction);
                        $factura->setNumero($consecutivoFactura);
                    }

                    //Inicia transaccion en la factura
                    $factura->setNumero($consecutivoFactura);
                    $factura->setSociosId($sociosId);
                    $factura->setMovimientoId(0);
                    $factura->setFechaFactura($config['fechaFactura']);
                    $factura->setPeriodo($config['periodoAbierto']);
                    $factura->setFechaVencimiento($config['fechaVencimiento']);
                    $factura->setSaldoVencido(0);
                    $factura->setSaldoMora(0);
                    $factura->setDiasMora(0);
                    $factura->setMoraPagado(0);
                    $factura->setCuotaVigente(0);
                    $factura->setVigentePagado(0);

                    $totalFactura = 0;
                    
                    $factura->setTotalFactura($totalFactura);
                    $factura->setEstado('D');//Debe

                    /////////////
                    //Prestamos
                    /////////////
                    $valUltAbono = 0;
                    $fecUltAbono = '';
                    $salAntNeto = $salAntInteres = $cargoMes = 0;
                    
                    $cargoMes = 0;

                    //Buscamos en amortizacion la cuota pendiente
                    $amortizacion = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId().' AND estado="D"', 'order'=>'numero_cuota ASC'));
                    
                    if ($amortizacion!=false) {
                        $cargoMes = LocaleMath::round($amortizacion->getValor(), 0);
                        $salAntNeto = $amortizacion->getSaldo()+$amortizacion->getValor();
                        $salAntInteres = $amortizacion->getInteres();
                    }

                    $config['cargoMes'] = $cargoMes;
                    $config['salAntNeto'] = $salAntNeto;
                    $config['salAntInteres'] = $salAntInteres;

                    $factura->setValUltAbono($valUltAbono);
                    $factura->setFecUltAbono($fecUltAbono);
                    $factura->setSalAntNeto($salAntNeto);
                    $factura->setSalAntInteres($salAntInteres);
                    $factura->setCargoMes($cargoMes);
                    $factura->setSalActual($totalFactura+$cargoMes);

                    //Obtenemos mora de fecha
                    $interesMora = SociosCore::getCurrentPeriodoMora($config['periodoAbierto']);
                    
                    $valMora = ($factura->getSalActual()*$interesMora)/100;
                    $factura->setSalActMora($factura->getSalActual()+$valMora);
                    
                    //guardar
                    if ($factura->save()==false) {
                        foreach ($factura->getMessages() as $msg) {
                            throw new SociosException('factura: '.$msg->getMessage());
                        }
                    }

                    //Detalle factura
                    $config['factura'] = $factura;
                    $config['facturaId'] = $factura->getId();
                    
                    //Add prestamos a factura
                    $this->_addPrestamosToFactura($config);

                    //Array SociosData
                    $sociosData = $this->actualizaDatosDeFacturaArray($socios);

                    $movimientoData = array();
                    $detalleMovimientoData = array();

                    //Array FacturaData
                    $facturaData = $this->actualizaDatosDeFacturaArray($factura);

                    $n = 0;
                    $detalleFacturaData = array();

                    //generar invoicing
                    $optionsLocal = array(
                        'apps'                    => 'SO',//Socios
                        'periodo'                => $config['periodoAbierto'],
                        'items'                 => array(),
                        'precios'                 => array(),
                        'cantidades'             => array(),
                        'descuentos'             => array(),
                        'codigoComprob'            => $codigoComprob,
                        'numeroAccion'             => $socios->getNumeroAccion(),
                        'nitDocumento'             => $socios->getIdentificacion(),
                        'nitEntregarDocumento'     => $socios->getIdentificacion(),
                        'fechaVencimiento'         => $config['fechaVenc']->getDate(),
                        'fechaFactura'            => $config['date']->getDate(),
                        'prestamo'                => $config['prestamoArray'],
                        'consecutivos'            => $config['consecutivosId'],

                        //Debug
                        'socios'                => $sociosData,
                        'factura'                => $facturaData,
                        'detalleFacturas'        => $detalleFacturaData,
                        'movimiento'            => $movimientoData,
                        'detalleMovimiento'        => $detalleMovimientoData,
                        'resumenVenta'            => array('0' => 0, '10' => 0, '16' => 0),
                        'resumenIva'            => array('0' => 0, '10' => 0, '16' => 0),
                        'resumenIco'            => array('8' => 0)
                    );

                    //para un socio
                    if (isset($config['sociosId'])) {
                        $optionsLocal['facturas'] = array($config['sociosId']);
                    }

                    $options[] = $optionsLocal;

                    //solo aumenta si es nuevo
                    if ($flagNew==true) {
                        $this->setConsecutivoFactura($consecutivoFactura+1);
                    }

                    unset($optionsLocal);
                }
            }

            //make invoicer data
            $this->_makeInvoicer($options);

            unset($options, $factura, $detalleMovimientoObj, $ultimoRc, $socios, $cargosSocios, $movimientoObj);

            return $config;
        }
        catch(Exception $e) {
            throw new SociosException('_makeFactura: '.$e->getMessage().', Line: '.$e->getLine()/*. ', Trace: '.print_r($e->getTrace(),true)*/);
        }

    }

    /**
    * Agregando resumenes de factura
    */
    private function _addResumenes(&$config)
    {
        $this->_transaction = TransactionManager::getUserTransaction();

        $resumenIva     = array('0' => 0, '10' => 0, '16' => 0);
        $resumenIco     = array('8' => 0);
        $resumenVenta     = array('0' => 0, '10' => 0, '16' => 0);
        
        $empresa = EntityManager::get('Empresa')->findFirst();
                
        $nits = EntityManager::get('Nits')->findFirst(array('conditions'=>"nit='{$empresa->getNit()}'"));
        if ($nits==false) {
            throw new SociosException('El nit del club no esta creado en terceros: '.$empresa->getNit());
        }
        if (!$nits->getEstadoNit()) {
            throw new SociosException('No se ha configurado el regimen de la empresa en terceros');
        }
        $regimenCuentas = EntityManager::get('RegimenCuentas')->findFirst(array('conditions'=>"regimen='{$nits->getEstadoNit()}'"));
        if ($regimenCuentas==false) {
            throw new SociosException('No se ha configurado las cuentas segun regimen de la empresa');
        }

        $movimiento = $config['movimiento'];

        
        foreach ($config['detalleMovimiento'] as $detalleMovimiento) {
            //throw new SociosException(print_r($detalleMovimiento,true));
            $codigoCuentaIva = null;
            $codigoCuentaIva = 0;
                
            $cargosSociosId = $detalleMovimiento->getCargosSociosId();

            //Solo por cargos socios
            if ($cargosSociosId || in_array($detalleMovimiento->getTipoMovi(), array('O', 'M', 'N', 'T'))==true) {

                //TIPO DE MOVIMIENTO NOVEDAD
                if ($detalleMovimiento->getTipoMovi()=='O') {
                    $cargosFijos = $this->CargosFijos->findFirst("nombre='{$detalleMovimiento->getDescripcion()}'");
                    if ($cargosFijos==false) {
                        throw new SociosException('El cargo fijo de novedad con nombre "'.$detalleMovimiento->getDescripcion().'" no existe, en la línea '.($n+1));
                    }
                } else {
                    //TIPO DE MOVIMIENTO MORA
                    if ($detalleMovimiento->getTipoMovi()=='M') {
                        $cargoFijoMora = Settings::get('cargo_fijo_mora', 'SO');
                        $cargosFijos = BackCacher::getCargosFijos($cargoFijoMora);
                    } else {
                        //TIPO DE MOVIMIENTO CONSUMO
                        if ($detalleMovimiento->getTipoMovi()=='N') {
                            $cargoFijoConsumo = Settings::get('cargo_fijo_consumo_minimo', 'SO');
                            $cargosFijos = BackCacher::getCargosFijos($cargoFijoConsumo);
                        } else {
                            //TIPO DE MOVIMIENTO AJUSTE SOSTENIMIENTO
                            if ($detalleMovimiento->getTipoMovi()=='T') {
                                $cargoFijoAjuste = Settings::get('cargo_fijo_ajuste', 'SO');
                                $cargosFijos = BackCacher::getCargosFijos($cargoFijoAjuste);
                            } else {
                                //OTROS
                                $cargosSocios = $this->CargosSocios->setTransaction($this->_transaction)->findFirst($cargosSociosId);
                                if ($cargosSocios==false) {
                                    throw new SociosException('No existe el cargo_socio '.$cargosSociosId);
                                }
                                $cargosFijosId = $cargosSocios->getCargosFijosId();

                                $cargosFijos = BackCacher::getCargosFijos($cargosFijosId);
                                if ($cargosFijos==false) {
                                    throw new SociosException('El cargo fijo con código "'.$item.'" no existe, en la línea '.($n+1));
                                }
                            }
                        }
                    }
                }

                if ($cargosFijos->getPorcentajeIva()===null) {
                    throw new SociosException('No se ha definido el porcentaje de IVA de venta del cargo fijo '.$cargosFijos->getNombre().', en la línea '.($n+1));
                }
                
                $valorTotal = $detalleMovimiento->getTotal();
                $baseIva = $detalleMovimiento->getValor();
                $baseIco = $detalleMovimiento->getValor();
                $iva = $detalleMovimiento->getIva();
                $ico = $detalleMovimiento->getIco();

                //IVA
                if ($cargosFijos->getPorcentajeIva()>0) {
                    $ivaSel = (int) $cargosFijos->getPorcentajeIva();
                    $codigoCuentaIva = $cargosFijos->getCuentaIva();
                    $cuentaIva = BackCacher::getCuenta($codigoCuentaIva);
                    if ($cuentaIva==false) {
                        throw new SociosException('La cuenta de contabilización ('.$codigoCuentaIva.') del IVA del '.$cargosFijos->getPorcentajeIva().'%  de Ventas en Regimen Cuentas configurada en el comprobante de facturación no existe ('.$codigoCuentaIva.')');
                    } else {
                        if ($cuentaIva->getEsAuxiliar()!='S') {
                            throw new SociosException('La cuenta de contabilización auxiliar ('.$codigoCuentaIva.') del IVA del '.$cargosFijos->getPorcentajeIva().'%  de Ventas en Regimen Cuentas configurada en el comprobante de facturación no es auxiliar ('.$codigoCuentaIva.')');
                        }
                    }
                } else {
                    $baseIva = LocaleMath::round($valorTotal, 0);
                    $iva = 0;
                }

                //ICO
                if ($ico>0) {
                    $icoSel = (int) $cargosFijos->getIco();
                    $codigoCuentaIco = $cargosFijos->getCuentaIco();
                    $cuentaIco = BackCacher::getCuenta($codigoCuentaIco);
                    if ($cuentaIco==false) {
                        throw new SociosException('La cuenta de ICO del cargo fijo "'.$cargosFijos->getNombre().'" no existe');
                    } else {
                        if ($cuentaIco->getEsAuxiliar()!='S') {
                            throw new SociosException('La cuenta de contabilización auxiliar ('.$codigoCuentaIco.') del cargo fijo '.$cargosFijos->getNombre().'  no es auxiliar');
                        }
                    }
                } else {
                    $baseIco = LocaleMath::round($valorTotal, 0);
                    $ico = 0;
                }

                if (!isset($resumenIco['8'])) {
                    $resumenIco['8'] = 0;
                }
                $resumenIco['8']+=$ico;

                //Resumen ingreso a terceros
                if ($cargosFijos->getIngresoTercero()=='N') {

                    if ($cargosFijos->getPorcentajeIva()>0) {
                
                        if (!isset($resumenVenta['16'])) {
                            $resumenVenta['16'] = 0;
                        }
                        $resumenVenta['16']+=$baseIva;
                        
                    } else {

                        if ($ico>0) {
                            if (!isset($resumenVenta['16'])) {
                                $resumenVenta['16'] = 0;
                            }
                            $resumenVenta['16']+=$baseIco;

                        } else {

                            if (!isset($resumenVenta['0'])) {
                                $resumenVenta['0']=0;
                            }
                            $resumenVenta['0']+=$baseIva;
                        }
                    }
                    
                } else {

                    if (!isset($resumenVenta['10'])) {
                        $resumenVenta['10']=0;
                    }
                    $resumenVenta['10']+=$baseIva;
                }
                
                if (!isset($resumenIva['16'])) {
                    $resumenIva['16'] = 0;
                }
                $resumenIva['16']+=$iva;

            } else {
                if ($detalleMovimiento->getTipoMovi()!='P') {
                    //throw new SociosException(print_r($detalleMovimiento,true));
                }
            }
        }

        //throw new SociosException(print_r($resumenIco,true));
        $config['resumenIva']   = $resumenIva;
        $config['resumenIco']   = $resumenIco;
        $config['resumenVenta'] = $resumenVenta;
    }

    /**
     * Convierte un modelo a array
     * 
     * @param Object $model
     * @return array $modelData 
    */
    public function actualizaDatosDeFacturaArray($model)
    {
        $modelData = array();
        foreach ($model->getAttributes() as $field) {
            $modelData[$field] = $model->readAttribute($field);
        }
        return $modelData;
    }

    /**
    * Make Invoicer with array invoicer
    *
    * @param array $options
    * @param ActiveRecordTransaction $this->_transaction
    */
    private function _makeInvoicer($options)
    {
        $this->_transaction = TransactionManager::getUserTransaction();

        try
        {
            gc_enable();

            //Generamos compribante contable con numero de factura generada
            $auraConsecutivoOld=null;
            foreach ($options as $indexF => $optionsLocal) {

                $facturaId = $optionsLocal['factura']['id'];
                $factura = $this->Factura->setTransaction($this->_transaction)->findFirst($facturaId);

                //add movi
                if ($auraConsecutivoOld!=null) {
                    $optionsLocal['auraConsecutivo'] = $auraConsecutivoOld;
                }

                //clean comprobs factura
                $this->_cleanComprobsFactura($factura);

                //Creando comprobante de factura
                $auraConsecutivo = null;
                if (count($optionsLocal['movimiento'])>1) {
                    $auraConsecutivo = $this->_addAura($optionsLocal);
                    if (isset($optionsLocal['auraConsecutivo']) && $optionsLocal['auraConsecutivo']>0) {
                        $auraConsecutivoOld = $optionsLocal['auraConsecutivo'];
                    }
                }
                
                if ($auraConsecutivo > 0) {
                    $factura->setComprobContab($optionsLocal['codigoComprob']);
                    $factura->setNumeroContab($auraConsecutivo);
                }

                if ($factura->save()==false) {
                    foreach ($factura->getMessages() as $msg) {
                        throw new SociosException('makeInvoicer2: '.$msg->getMessage());
                    }
                }

                //add invoicer
                $this->addInvoicer($optionsLocal);

                //Actualizamos datos en factura
                $options[$indexF]['factura'] = $this->actualizaDatosDeFacturaArray($factura);

                unset($optionsLocal, $facturaId, $factura, $auraConsecutivo);
                
                gc_collect_cycles();    
            }

            return $options;
        }
        catch(Exception $e) {
            throw new SociosException("_makeInvoicer: ".$e->getMessage()/*print_r($e,true)*/);
        }

    }

    

    /**
    * Add to Factura the finantiation
    *
    * @param array $config
    * @param ActiveRecordTransaction $this->_transaction
    */
    public function _addPrestamosToFactura(&$config)
    {

        try
        {
        
            $this->_transaction = TransactionManager::getUserTransaction();

            $prestamosSociosObj = EntityManager::get('PrestamosSocios')->setTransaction($this->_transaction)->find(array('conditions'=>'socios_id='.$config['sociosIdMovi'].' AND estado="D"'));

            $prestamoArray = array();

            //periodo actual
            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
            $periodo = SociosCore::getCurrentPeriodo();
            $periodoObj = EntityManager::get('Periodo')->findFirst("periodo='$periodo'");
            if (!$periodoObj) {
                throw new SociosException("No se encontro periodo $periodo");
            }
            
            $fechaFactura = SociosCore::getFechaFactura($periodo);

            $mora = SociosCore::getCurrentPeriodoMora();
                    
            foreach ($prestamosSociosObj as $prestamosSocios) {
                
                //ultimo abonos
                $ultimoAbono = 0.00;
                $fechaUltimo = '';

                $valorPeriodo = 0;
                $valor = 0;
                $valorMora = 0;
                $valorTotal = 0;
                $totalDiasMora = 0;
                $periodoAmortizacion = '';
                $amortizacionSaldo = '';

                $totalConvenio = $prestamosSocios->getValorFinanciacion();

                //revisar estado de amortizacion
                $this->revisarConvenios($prestamosSocios);
                
                //Cuota a hoy de amortizacion
                $amortizacionHoy = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId()." AND MONTH(fecha_cuota)='{$fechaFactura->getMonth()}' AND YEAR(fecha_cuota)='{$fechaFactura->getYear()}'",'order'=>'numero_cuota ASC'));
                if (!$amortizacionHoy) {
                    $amortizacionHoy = EntityManager::get('Amortizacion', true)->setTransaction($this->_transaction);
                    $amortizacionHoy->setFechaCuota($fechaFactura);
                }

                //Cuota actual de amortizacion
                $amortizacionSaldoObj = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->find(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId().' AND estado="D"'." AND fecha_cuota<'{$fechaFactura->getDate()}'",'order'=>'numero_cuota ASC'));
                foreach ($amortizacionSaldoObj as $amortizacionSaldo) {
                    //echo "<br>fecha_cuota:".$amortizacionSaldo->getFechaCuota();
                    //$amortizacionSaldo = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId().' AND estado="D"','order'=>'numero_cuota ASC'));

                    
                    //encontramos meses de mora de cuota extraordinaria
                    try
                    {
                        $fechaAmortizacion = new Date($amortizacionSaldo->getFechaCuota());
                        $periodoAmortizacion = $fechaAmortizacion->getPeriod();
                    }
                    catch(Exception $e) {
                        $year = substr($amortizacionSaldo->getFechaCuota(), 0, 4);
                        $month = substr($amortizacionSaldo->getFechaCuota(), 5, 2);
                        $fechaAmortizacion = new Date("$year-$month-01");
                        $fechaAmortizacionStr = $fechaAmortizacion->getLastDayOfMonth($month, $year);
                        $fechaAmortizacion = new Date($fechaAmortizacionStr);
                        $periodoAmortizacion = $fechaAmortizacion->getPeriod();
                    }

                    //buscamos meses de diferencia y sacamos saldos anteriores de cuotas extraordinarias
                    $diffPeriod = Date::diffMonthsPeriod($periodoAmortizacion, $periodo);
                    if ($diffPeriod>0) {

                        //$valor = ($amortizacionSaldo->getValor() * $diffPeriod);
                        $valor += $amortizacionSaldo->getValor();
                        $valorPeriodo = $amortizacionSaldo->getValor();

                        $valorMoraDia = LocaleMath::round(($amortizacionSaldo->getValor() * $mora / 100 / 30), 0);
                        
                        //Dias de mora
                        $fechaPeriodoAmortizacion = new Date($amortizacionHoy->getFechaCuota());
                        $diffDias = Date::difference($fechaAmortizacion, $fechaPeriodoAmortizacion);
                        $diffDias = abs($diffDias);
                        
                        $totalDiasMora += $diffDias;

                        $valorMora += ($valorMoraDia * $diffDias);
                                
                    }
                    //echo "<br>"."diffperiod: $diffPeriod, diffDias: $diffDias, valor: $valor, valorMoraDia: $valorMoraDia, valorMora : $valorMora, fechaAmortizacion: $fechaAmortizacion,fechaFactura: $fechaFactura";
                }
                $valorTotal = $valor + $valorMora;
    
                
                //throw new SociosException("diffDias: $totalDiasMora, valor: $valor, valorMoraDia: $valorMoraDia, valorMora: $valorMora, fechaFactura: $fechaFactura");
                if ($valorTotal) {
                    $prestamoArray[] = array(
                        'descripcion'    => 'SALDO CUOTA EXTRA. DESDE PERIODO '.$periodoAmortizacion,
                        'valor'         => $valor,
                        'mora'            => $valorMora,
                        'total'         => $valorTotal
                    );
                }

                if ($amortizacionHoy) {

                    $prestamoArray[] = array(
                        'descripcion'    => 'CUOTA EXTRA. PERIODO '.$periodo,
                        'valor'         => $amortizacionHoy->getValor(),
                        'mora'            => 0,
                        'total'         => $amortizacionHoy->getValor()
                    );

                }

            }
            //throw new SociosException(print_r($prestamoArray,true));
            $config['prestamoArray'] = $prestamoArray;
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage(). "?");
        }
    }

    /**
     * Retorna los prestamos actuales a pagar 
    */
    public function getPrestamosActuales($config)
    {
        $this->_transaction = TransactionManager::getUserTransaction();

        $prestamosSociosObj = EntityManager::get('PrestamosSocios')->find(array('conditions'=>'socios_id='.$config['sociosIdMovi'].' AND estado="D"'));

        $prestamoArray = array();

        foreach ($prestamosSociosObj as $prestamosSocios) {
            $totalConvenio = $prestamosSocios->getValorFinanciacion();

            //Cuota actual
            $amortizacion = EntityManager::get('Amortizacion')->findFirst(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId().' AND estado="D"'));

            if ($amortizacion!=false) {

                if (!isset($config['cargoMes'])) {
                    $cargoMes = LocaleMath::round($amortizacion->getValor(), 0);
                    $salAntNeto = $amortizacion->getSaldo() + $amortizacion->getValor();
                    $salAntInteres = $amortizacion->getInteres();

                    $config['cargoMes']      = $cargoMes;
                    $config['salAntNeto']      = $salAntNeto;
                    $config['salAntInteres'] = $salAntInteres;
                }
                
                $prestamoArray[] = array(
                    'prestamosSocios'     => $prestamosSocios,
                    'amortizacion'        => $amortizacion,
                    'fechaCuota'        => $amortizacion->getFechaCuota(),
                    'cargoMes'             => $config['cargoMes'],
                    'salAntNeto'         => $config['salAntNeto'],
                    'salAntInteres'     => $config['salAntInteres'],
                    'totalConvenio'        => $totalConvenio
                );

            }

            unset($prestamosSocios, $totalConvenio, $amortizacion);
        }

        unset($prestamosSociosObj, $config);

        return $prestamoArray;
    }

    /**
    * Make detalle de factura
    *
    * @param array $config
    * @param ActiveRecordTransaction $this->_transaction
    */
    private function _detalleFactura(&$config)
    {
        $this->_transaction = TransactionManager::getUserTransaction();

        $factura    = $config['factura'];
        $facturaId  = $config['facturaId'];
        $movimiento = $config['movimiento'];

        //Limpiamos el detalle de factura por si hay basura
        $this->limpiarDetalleFactura($factura->getId());

        //Buscamos lo detalles del movimiento
        $detalleMovimientoObj = EntityManager::get('DetalleMovimiento')->setTransaction($this->_transaction)->find(array('conditions'=>"movimiento_id='".$movimiento->getId()."' AND estado='A' AND fecha='{$config['fechaFactura']}'"));
        if (!count($detalleMovimientoObj)) {
            throw new SociosException('El detalle del movimiento con id '.$movimiento->getId().' esta vacio');
        }

        $items = $precios = $cantidades = $descuentos = $detalleFacturasObj = $detalleMovimientosFiltered = array();

        $countShow = 0;
        //Recorremos los detalles de movimiento y volvemos a ingresar los datos al detalle de la factura
        foreach ($detalleMovimientoObj as $detalleMovimiento) {

            $cargosSociosId = $detalleMovimiento->getCargosSociosId();

            //Solo para cargos fijos
            if (is_numeric($cargosSociosId)==true && $cargosSociosId> 0) {
            
                $cargosSocios = EntityManager::get('CargosSocios')->setTransaction($this->_transaction)->findFirst($detalleMovimiento->getCargosSociosId());
                if ($cargosSocios != false) {

                    $cargosFijos = BackCacher::getCargosFijos($cargosSocios->getCargosFijosId());
                    if ($cargosFijos == false) {
                        throw new SociosException('El cargo fijo con id '.$cargosSocios->getCargosFijosId().' no existe');
                    }

                    $cargosFijosId = $cargosFijos->getId();

                    if (!isset($detalleMovimientosFiltered[$cargosFijosId])) {
                        $detalle        = $cargosFijos->getNombre();
                        //almacenamos id de cargo fijo
                        $items[$cargosFijosId]         = $cargosFijos->getId();
                        $precios[$cargosFijosId]    = $detalleMovimiento->getValor();
                        $cantidades[$cargosFijosId]    = 1;//:/
                        $descuentos[$cargosFijosId]    = 0;//:/

                        $detalleMovimientosFiltered[$cargosFijosId] = array(
                            'detalle' => $detalle,
                            'iva'     => $detalleMovimiento->getIva(),
                            'ico'     => $detalleMovimiento->getIco(),
                            'valor'   => $detalleMovimiento->getValor()
                        );
                    } else {
                        $detalleMovimientosFiltered[$cargosFijosId]['iva'] += $detalleMovimiento->getIva();
                        $detalleMovimientosFiltered[$cargosFijosId]['ico'] += $detalleMovimiento->getIco();
                        $detalleMovimientosFiltered[$cargosFijosId]['valor'] += $detalleMovimiento->getValor();
                    }

                }
            
            } else {
                $detalle = $detalleMovimiento->getDescripcion();
            
                //Vales de consumo POS
                if ($detalleMovimiento->getTipoMovi()=='P') {
                    if (!isset($detalleMovimientosFiltered['P'])) {
                        $detalleMovimientosFiltered['P'] = array(
                            'detalle' => $detalle,
                            'iva'     => $detalleMovimiento->getIva(),
                            'ico'     => $detalleMovimiento->getIco(),
                            'valor'   => $detalleMovimiento->getValor()
                        );
                    } else {
                        $detalleMovimientosFiltered['P']['detalle'] .= ', '.$detalle;
                        $detalleMovimientosFiltered['P']['iva'] += $detalleMovimiento->getIva();
                        $detalleMovimientosFiltered['P']['ico'] += $detalleMovimiento->getIco();
                        $detalleMovimientosFiltered['P']['valor'] += $detalleMovimiento->getValor();
                    }
                } else {
                    $detalleMovimientosFiltered[] = array(
                        'detalle' => $detalle,
                        'iva'     => $detalleMovimiento->getIva(),
                        'ico'     => $detalleMovimiento->getIco(),
                        'valor'   => $detalleMovimiento->getValor()
                    );
                }
            }

            unset($detalle, $detalleMovimiento, $cargosSociosId);
        }
        unset($detalleMovimientoObj);

        //Movimiento filtrado
        foreach ($detalleMovimientosFiltered as $index => $detalleMovimiento) {
            $detalleFactura    = EntityManager::get('DetalleFactura', true)->setTransaction($this->_transaction);
            $detalleFactura->setFacturaId($facturaId);
            $detalleFactura->setDescripcion($detalleMovimiento['detalle']);

            if (!isset($detalleMovimiento['iva'])) {
                $detalleMovimiento['iva'] = 0;
            }
            $detalleFactura->setIva($detalleMovimiento['iva']);
            $detalleFactura->setIco($detalleMovimiento['ico']);

            $detalleFactura->setValor($detalleMovimiento['valor']);
            
            if ($detalleFactura->save()==false) {

                foreach ($detalleFactura->getMessages() as $msg) {
                    throw new SociosException($msg->getMessage());
                }

            }

            $detalleFacturasObj[] = $detalleFactura;

            unset($detalleFactura);
        }

        unset($detalleMovimientosFiltered);

        $config['items']         = $items;
        $config['precios']         = $precios;
        $config['cantidades']     = $cantidades;
        $config['descuentos']     = $descuentos;

        $config['detalleFacturasObj'] = $detalleFacturasObj;

        return true;
    }

    /**
     * Metodo que genera las factura de un periodo
     *
     * @param array $conf = array(
     *     'periodo' => 201008,
     *     'sociosId' //solo para una factura
     * )
     * @param ActiveRecordTransaction $this->_transaction
     * @return $config 
     */
    public function generarFactura(&$config)
    {
        //set_time_limit(0);

        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();

            //El periodo es obligatorio
            if (!isset($config['periodo']) || empty($config['periodo'])) {
                throw new SociosException('Es necesario el periodo a facturar');
            }
            
            $periodoAbierto = $config['periodo'];
            
            if (!$config['fechaFactura']) {
                throw new SociosException('Es necesario dar la fecha de la factura a generar en el periodo a facturar');
            }

            $config['periodoAbierto'] = $periodoAbierto;

            //Carga datos del CLub
            $datosClub = EntityManager::get('DatosClub')->setTransaction($this->_transaction)->findFirst();

            $config['datosClub'] = $datosClub;
            
            Rcs::disable();
            
            //Carga en vista el periodo
            $periodo = SociosCore::makePeriodo($periodoAbierto, $this->_transaction);
            $config['periodoObj'] = $periodo;

            $date = new Date($config['fechaFactura']);
            $config['date'] = $date;

            //cogemos las fecha final de periodo
            $fechaVenc = new Date($config['fechaVencimiento']);

            //obtenemos datos de cartera
            $diaVenc = Settings::get('dia_venc', 'SO');//Dias de vencimiento de factura
            if (!$diaVenc) {
                throw new SociosException('No se ha configurado el día de vencimiento de factura');
            }

            //le sumamos los dias de de vencimineto a la fecha de final de periodo
            $config['diaVenc'] = $diaVenc;
            $config['fechaVenc'] = $fechaVenc;
            $config['consecutivosId'] = $periodo->getConsecutivosId();

            //Limpiar si no hay nada
            $detmovi = EntityManager::get('DetalleMovimiento')->setTransaction($this->_transaction)->findFirst("socios_id='{$config['sociosId']}' AND fecha='{$config['fechaFactura']}'");

            if (!$detmovi) {
                $sociosIdArray = array();
                $nitsArray = array();

                $facturaObj = EntityManager::get("Factura")->setTransaction($this->_transaction)->find(array("conditions"=>"fecha_factura='{$config['fechaFactura']}' AND socios_id='{$config['sociosId']}'",'columns'=>'socios_id'));
                foreach ($facturaObj as $factura) {
                    $socio = BackCacher::getSocios($factura->getSociosId());
                    if ($socio) {
                        $sociosIdArray[]=$factura->getSociosId();
                        $nitsArray[]=$socio->getIdentificacion();
                    }
                    unset($socio,$factura);
                }
                unset($facturaObj);
                
                if (count($nitsArray)>0) {
                    $configAnulaF = array(
                        'nits'        => $nitsArray,
                        'facturas'     => $sociosIdArray,
                        'fechaFactura'     => $config['fechaFactura'],
                        'showDebug' => true
                    );
                    
                    $status = $this->anularFacturasPeriodo($configAnulaF);
                }
            }
            
            //Make factura
            $config = $this->_makeFactura($config);

            return $config;
        }
        catch(Exception $e) {
            throw new SociosException('generarFactura: '.$e->getMessage());
        }

    }

    /**
    * Anula facturas declaradas en $config en el perido
    *
    * @param array $config
    * @param ActiveRecordTransaction $this->_transaction
    */
    public function anularFacturasPeriodo($config)
    {

        $this->_transaction = TransactionManager::getUserTransaction();
    
        $where   = array('conditions' => 'fecha_factura="'.$config['fechaFactura'].'"');
    
        if (isset($config['facturas'])) {
            $sociosIdArray = $config['facturas'];
            $sociosIdWhere = implode(',', $sociosIdArray);
            if ($sociosIdWhere) {
                $where['conditions']     = 'socios_id IN('.$sociosIdWhere.') AND fecha_factura="'.$config['fechaFactura'].'"';
            } else {
                $where['conditions']     = 'fecha_factura="'.$config['fechaFactura'].'"';
            }
        }
        
        $configAnula = array(
            'apps'        => 'SO', //Socios
            'facturas'    => array(),
            'fechaFactura'    => $config['fechaFactura'],
            'nits'        => array()
        );

        $facturaObj = EntityManager::get('Factura')->setTransaction($this->_transaction)->find($where);

        if (!count($facturaObj) && isset($config['showDebug']) && $config['showDebug']) {
            throw new SociosException("No existen facturas a borrar en la fecha '{$config['fechaFactura']}'");
        }

        $numerosContab = array();
        foreach ($facturaObj as $factura) {

            //Se borra movimiento en contabilidad de la factura
            if ($factura->getComprobContab() && $factura->getNumeroContab()) {

                $socios = BackCacher::getSocios($factura->getSociosId());
                $configAnula['nits'][] = $socios->getIdentificacion();

                $moviExists = EntityManager::get('Movi')->setTransaction($this->_transaction)->findFirst("comprob='{$factura->getComprobContab()}' AND numero='{$factura->getNumeroContab()}'");
                if ($moviExists && !in_array($factura->getNumeroContab(), $numerosContab)) {

                    //Aura delete
                    try
                    {
                        $aura = new Aura($factura->getComprobContab(), $factura->getNumeroContab());
                        $aura->delete();
                    }
                    catch(Exception $e) {
                        throw new SociosException('anularFacturasPeriodo: '.$e->getMessage());
                    }
                    $numerosContab[] = $factura->getNumeroContab();
                }
                $configAnula['facturas'][] = $factura->getId();
            }

            //Borramos detalle de factura
            $detalleFacturaObj = EntityManager::get('DetalleFactura')->setTransaction($this->_transaction)->find("factura_id='{$factura->getId()}'");
            foreach ($detalleFacturaObj as $detalleFactura) {
                $detalleFactura->delete();
                unset($detalleFactura);
            }
            unset($detalleFacturaObj);

            //Se borra factura
            $factura->setTransaction($this->_transaction)->delete();

            //Borramos de invoicer
            $this->_cleanInvoicer(array(
                'fechaFactura'    => $config['fechaFactura'],
                'facturaNumero' => $factura->getNumero()
            ));

            unset($factura);
        }
        unset($facturaObj);

        //Borramos en invoicer
        return $this->disableInvoicer($configAnula);
    }

    /**
    * Limpia los comprobantes hechos de la factura
    */
    public function _cleanComprobsFactura($factura)
    {
        //Comprob facturas socios
        $comprobSocios = Settings::get('comprob_factura', 'SO');
        if (!$comprobSocios) {
            throw new SociosException("No se ha definido el comprobante de Facturacion de socios");
        }

        $socios = BackCacher::getSocios($factura->getSociosId());

        $conditions = "comprob='$comprobSocios' AND fecha='{$factura->getFechaFactura()}' and nit='{$socios->getIdentificacion()}'";
        //throw new SociosException($conditions);
        
        $moviObj = EntityManager::get('Movi')->setTransaction($this->_transaction)->find(array("conditions" => $conditions, "group" => "numero"));
        foreach ($moviObj as $movi) {

            //Aura delete
            try
            {
                $aura = new Aura($movi->getComprob(), $movi->getNumero());
                $aura->delete();
            }
            catch(Exception $e) {
                throw new SociosException('_cleanComprobsFactura: '.$e->getMessage());
            }
            unset($movi);
        }
        return true;
    }

    /**
     * Metodo que obtiene el consecutivo de facturacion
     */
    public function getConsecutivoFactura($facturaId = 0)
    {
        $this->_transaction = TransactionManager::getUserTransaction();

        if ($facturaId>0) {
            $factura = EntityManager::get('Factura')->setTransaction($this->_transaction)->findFirst($facturaId);
            if (!$factura) {
                throw new SociosException("La factura no existe ". $facturaId);
            }
            $periodo = SociosCore::getCurrentPeriodoObject($factura->getPeriodo());
        } else {
            $periodo = SociosCore::getCurrentPeriodoObject();
        }

        if (!$periodo->getConsecutivosId()) {
            throw new SociosException("No se ha asignado el consecutivo del periodo");
        }
        $consecutivos = EntityManager::get('Consecutivos')->setTransaction($this->_transaction)->findFirst($periodo->getConsecutivosId());
        if (!$consecutivos) {
            throw new SociosException("No existe el consecutivo ".$periodo->getConsecutivosId());
        }
        //throw new SociosException($periodo->getConsecutivosId()."-".$consecutivos->getNumeroActual());
        
        return $consecutivos->getNumeroActual();
    }


    /**
     * Obtiene el interes de mora de una fecha
     * 
     * @params string $fecha default today
     * @params ActiveRecordsTransaction $this->_transaction
     * 
     * @return double interes_mensual
     */
    public function getTasaDeMora($fecha = '')
    {
    
        Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');

        if (!$fecha) {
            $fecha = date('Y-m-d');
        }

        if (!$this->_transaction) {
            $this->_transaction = TransactionManager::getUserTransaction();
        }

        $fechaDate = new Date($fecha); 
        $periodo = SociosCore::getCurrentPeriodoObject($fechaDate->getPeriod());
        if ($periodo == false) {
            throw new SociosException('No se ha asignado el interes de mora para la fecha '.$fecha);
        }
        return $periodo->getInteresesMora();
    }

    /**
     * Crear los registros de una amortizacion y relaciona con un cliente
     * 
     * @param array $config(
     *     'prestamosSociosId',
     *     'valorFinanciacion',
     *     'fechaCompra',
     *     'plazoMeses'
     * )
     * @param ActiverecordTransaction $this->_transaction
     */
    public function crearAmortizacion(&$config)
    {
        
        Core::importFromLibrary('Hfos/Tpc', 'Tpc.php');
        
        //validaciones
        if (isset($config['prestamosSociosId'])==false || $config['prestamosSociosId']<=0) {
            throw new SociosException('Es necesario de el id del prestamo para crear la amortizacion');
        }
        
        if (isset($config['valorFinanciacion'])==false || $config['valorFinanciacion']<=0) {
            throw new SociosException('El valor a financiar debe ser mayor a 0');
        }
        
        if (isset($config['fechaCompra'])==false || !$config['fechaCompra']) {
            throw new SociosException('La fecha de inicio de financiación es necesaria.');
        }
        
        if (isset($config['plazoMeses'])==false || $config['plazoMeses']<=0) {
            throw new SociosException('El número de cuotas debe ser mayor a 0');
        }

        if (!isset($config['tasaMesVencido'])) {
            $config['tasaMesVencido'] = 0;
        }

        $this->_transaction = TransactionManager::getUserTransaction();
        
        //borramos anteriores amortizaciones
        $pretamosSociosDel = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->delete("prestamos_socios_id={$config['prestamosSociosId']}");
        
        //obtenemos la mora de esta fecha
        $interesUsura2 = $this->getTasaDeMora($config['fechaCompra'], $this->_transaction);
        
        //generamos los calculos de la amortización
        $dataAmortizacion = array(
            'valorFinanciacion'        => $config['valorFinanciacion'],
            'valorTotalCompra'        => $config['valorFinanciacion'],
            'fechaCompra'            => $config['fechaCompra'],//"29-06-2010"
            'fechaPagoFinanciacion'    => $config['fechaCompra'],//"30-09-2010"
            'plazoMeses'            => $config['plazoMeses'],//24
            'tasaMesVencido'        => $config['tasaMesVencido'],//interes corriente
            'tasaMora'                => $interesUsura2,
            'debug'                    => false
        );
        $amortizacionArray = TPC::generarAmortizacion($dataAmortizacion);
        
        //insertamos los registros generados
        foreach ($amortizacionArray as $data) {
            $amortizacion = EntityManager::get('Amortizacion', true)->setTransaction($this->_transaction);
            $amortizacion->setPrestamosSociosId($config['prestamosSociosId']);
            $amortizacion->setNumeroCuota($data['cuota']);
            $amortizacion->setValor($data['cuotaFija']);
            $amortizacion->setCapital($data['abonoCapital']);
            $amortizacion->setInteres($data['intereses']);
            $amortizacion->setSaldo($data['saldo']);
            $amortizacion->setFechaCuota($data['periodo']);
            $amortizacion->setEstado('D');//debe
            $amortizacion->setPagado(0);
            if ($amortizacion->save()==false) {
                foreach ($amortizacion->getMessages() as $message) {
                    throw new SociosException($message->getMessage());
                }
            }
        }

        unset($amortizacionArray);
    }


    /**
    * Add a Aura
    * 
    * tipo_soporte char(3)
    * numero_soporte int unsigned
    *
    * @param array $options
    * @return int $auraConsecutivo
    */
    private function _addAura(&$options)
    {
        
        try
        {

            if (TransactionManager::hasUserTransaction()) {
                $this->_transaction = TransactionManager::getUserTransaction();
            }

            $fechaVencimiento     = $options['fechaVencimiento'];
            $codigoComprob         = $options['codigoComprob'];
            $numeroAccion         = $options['numeroAccion'];
            $fechaFactura         = $options['fechaFactura'];
            $nitDocumento         = $options['nitDocumento'];
            $facturaId             = $options['factura']['id'];
            $periodo             = $options['periodo'];
            
            $numeroComprob    = 0;
            if (isset($options['factura']['numero_contab']) && $options['factura']['numero_contab']>0) {
                $numeroComprob    = (int) $options['factura']['numero_contab'];
            }
            
            //Nit
            if (!$nitDocumento) {
                throw new SociosException('El nit del movimiento no puede ser nulo. '.$options['numeroAccion'].' -> nit: '.$options['nitDocumento']);
            }

            $numeroDocumento = Filter::bring($options['factura']['numero'], 'int');
            
            $detalleMovimientoObj = $options['detalleMovimiento'];

            //obtenemos datos de cartera
            $tipoDoc = Settings::get('tipo_doc', 'SO');//porcentaje de mora de factura
            if (!$tipoDoc) {
                throw new SociosException('No se ha configurado el tipo de documento de factura');
            }

            //fecha periodo
            $day = substr($periodo, 4);
            if ($day<10) {
                $day = '0'.$day;
            }
            $fechaPeriodo = substr($periodo, 0, 4).'-'.$day;
            
            $auraConsecutivo = 0;
            
            if (!isset($options['auraConsecutivo'])) {
                $aura = new Aura($codigoComprob, 0, $fechaFactura);
            } else {
                $auraConsecutivo = $options['auraConsecutivo'];
                $auraConsecutivo++;
                $aura = new Aura($codigoComprob, $auraConsecutivo, $fechaFactura);
            }

            $total = 0;
            $movements = array();
                    
            if ($nitDocumento && !strstr($nitDocumento, '-') && count($detalleMovimientoObj)>0) {
                
                foreach ($detalleMovimientoObj as $detalleMovimiento) {
                    $cargosFijosId = (int) $detalleMovimiento['cargos_fijos_id'];

                    if ($cargosFijosId) {

                        /////////////////////////////////////////////////
                        //BLOQUE PARA CARGOS FIJOS
                        /////////////////////////////////////////////////
                        
                        $tempNit = $nitDocumento;

                        //CARGOS FIJOS
                        $cargosFijos = BackCacher::getCargosFijos($cargosFijosId);
                        if ($cargosFijos) {
                            $teceroFijo = $cargosFijos->getTerceroFijo();
                            if ($teceroFijo) {
                                $nits = BackCacher::getTercero($teceroFijo);
                                if (!$nits) {
                                    throw new SociosException('El tercero con nit "'.$tercero.'" en el cargo fijo "'.$cargosFijos->getNombre().'" no existe');
                                }
                                $tempNit = $teceroFijo;
                            }
                        }
                        
                        $cuentaContable = $cargosFijos->getCuentaContable();
                        
                        //Creditos/Debitos segun naturaleza de cargo fijo
                        if (!isset($movements[$cuentaContable])) {
                            $movements[$cuentaContable] = array(
                                'Descripcion'    => $detalleMovimiento['descripcion'],
                                'Nit'            => $tempNit,
                                'CentroCosto'    => $cargosFijos->getCentroCostos(),
                                'Cuenta'        => $cuentaContable,
                                'Valor'            => $detalleMovimiento['valor'],
                                'BaseGrab'        => $detalleMovimiento['valor'],
                                'TipoDocumento' => $tipoDoc,
                                'NumeroDocumento' => $numeroDocumento,
                                'FechaVence'    => $fechaVencimiento,
                                'DebCre'        => $cargosFijos->getNaturaleza(),
                                'debug'            => true
                            );
                                
                        } else {
                            //Si existe ya la cuenta acumule el valor
                            $movements[$cuentaContable]['Valor'] += $detalleMovimiento['valor'];
                        }
                        $total += $detalleMovimiento['valor'];

                        //Si hay iva
                        $debitoIva= 0;
                        if ($detalleMovimiento['iva']>0 && $detalleMovimiento['iva']) {
                            if (!isset($movements[$cargosFijos->getCuentaIva()])) {
                                $movements[$cargosFijos->getCuentaIva()] = array(
                                    'Descripcion'    => 'IVA '.$detalleMovimiento['descripcion'],
                                    'Nit'            => $tempNit,
                                    'CentroCosto'    => $cargosFijos->getCentroCostosIva(),
                                    'Cuenta'        => $cargosFijos->getCuentaIva(),
                                    'Valor'            => $detalleMovimiento['iva'],
                                    'BaseGrab'         => $detalleMovimiento['valor'],
                                    'TipoDocumento' => $tipoDoc,
                                    'NumeroDocumento' => $numeroDocumento,
                                    'FechaVence'     => $fechaVencimiento,
                                    'DebCre'         => $cargosFijos->getNaturaleza(),
                                    'debug'         => true
                                );
                            } else {
                                //Si existe ya la cuenta acumule el valor
                                $movements[$cargosFijos->getCuentaIva()]['Valor'] += $detalleMovimiento['iva'];
                            }
                            $debitoIva += $detalleMovimiento['iva'];
                        }

                        //Si hay ico
                        $debitoIco= 0;
                        if ($detalleMovimiento['ico']>0 && $detalleMovimiento['ico']) {
                            if (!isset($movements[$cargosFijos->getCuentaIco()])) {
                                $movements[$cargosFijos->getCuentaIco()] = array(
                                    'Descripcion'    => 'ICO '.$detalleMovimiento['descripcion'],
                                    'Nit'            => $tempNit,
                                    'CentroCosto'    => $cargosFijos->getCentroCostosIva(),
                                    'Cuenta'        => $cargosFijos->getCuentaIco(),
                                    'Valor'            => $detalleMovimiento['ico'],
                                    'BaseGrab'         => $detalleMovimiento['valor'],
                                    'TipoDocumento' => $tipoDoc,
                                    'NumeroDocumento' => $numeroDocumento,
                                    'FechaVence'     => $fechaVencimiento,
                                    'DebCre'         => $cargosFijos->getNaturaleza(),
                                    'debug'         => true
                                );
                            } else {
                                //Si existe ya la cuenta acumule el valor
                                $movements[$cargosFijos->getCuentaIco()]['Valor'] += $detalleMovimiento['ico'];
                            }
                            $debitoIco += $detalleMovimiento['ico'];
                        }

                        //Consolidar
                        $consolidarDbCre = '';
                        if ($cargosFijos->getNaturaleza()=='C') {
                            $consolidarDbCre = 'D';
                        } else {
                            $consolidarDbCre = 'C';
                        }
                        $valorTot = ($detalleMovimiento['valor']+$detalleMovimiento['iva']+$detalleMovimiento['ico']);
                        $valorTot = LocaleMath::round($valorTot, 0);
                        if (!isset($movements[$cargosFijos->getCuentaConsolidar()])) {
                            $movements[$cargosFijos->getCuentaConsolidar()] = array(
                                //'Descripcion'    => 'FACTURA POR COBRAR TERCERO '.$nitDocumento,
                                'Descripcion'    => $cargosFijos->getNombre(),
                                'Nit'            => $tempNit,
                                'CentroCosto'     => $cargosFijos->getCentroCostos(),
                                'Cuenta'         => $cargosFijos->getCuentaConsolidar(),
                                'Valor'         => $valorTot,
                                'BaseGrab'         => 0,
                                'TipoDocumento' => $tipoDoc,
                                'NumeroDocumento' => $numeroDocumento,
                                'FechaVence'     => $fechaVencimiento,
                                'DebCre'         => $consolidarDbCre,
                                'debug'         => true
                            );
                        } else {
                            //Si existe ya la cuenta acumule el valor
                            $movements[$cargosFijos->getCuentaConsolidar()]['Valor'] += $valorTot;
                        }
                        
                    } else {
                        /////////////////////////////////////////////////
                        //BLOQUE PARA CONCEPTOS QUE NO SON CARGOS FIJOS
                        /////////////////////////////////////////////////

                        //MORA SALDOS ANTERIORES
                        if (strstr($detalleMovimiento['descripcion'], 'MORA')) {

                            $cargosFijosMoraId = Settings::get('cargo_fijo_mora', 'SO');
                            if ($cargosFijosMoraId=='') {
                                throw new SociosException('No se ha configurado el cargo fijo de la mora de socios');
                            }

                            $cargosFijosMora = BackCacher::getCargosFijos($cargosFijosMoraId);
                            if (!$cargosFijosMora) {
                                throw new SociosException('El cargo fijo de la mora no existe');
                            }

                            //Creditos/Debitos segun naturaleza de cargo fijo
                            $valor = $detalleMovimiento['valor'];
                            //$valor = LocaleMath::round($valor, 0);
                            $movements[] = array(
                                'Descripcion' => $detalleMovimiento['descripcion'],
                                'Nit' => $nitDocumento,
                                'CentroCosto' => $cargosFijosMora->getCentroCostos(),
                                'Cuenta' => $cargosFijosMora->getCuentaContable(),
                                'Valor' => $valor,
                                'BaseGrab' => $valor,
                                'TipoDocumento' => $tipoDoc,
                                'NumeroDocumento' => $numeroDocumento,
                                'FechaVence' => $fechaVencimiento,
                                'DebCre' => $detalleMovimiento['tipo'],
                                'debug' => true
                            );

                            //Si hay iva
                            if ($detalleMovimiento['iva']>0 && $detalleMovimiento['iva']) {
                                $movements[] = array(
                                    'Descripcion' => 'IVA '.$detalleMovimiento['descripcion'],
                                    'Nit' => $nitDocumento,
                                    'CentroCosto' => $cargosFijosMora->getCentroCostosIva(),
                                    'Cuenta' => $cargosFijosMora->getCuentaIva(),
                                    'Valor' => $detalleMovimiento['iva'],
                                    'BaseGrab' => $detalleMovimiento['valor'],
                                    'TipoDocumento' => $tipoDoc,
                                    'NumeroDocumento' => $numeroDocumento,
                                    'FechaVence' => $fechaVencimiento,
                                    'DebCre' => $detalleMovimiento['tipo'],
                                    'debug' => true
                                );

                                $total += $detalleMovimiento['iva'];
                            }

                            //Consolidar
                            $consolidarDbCre = '';
                            if ($detalleMovimiento['tipo']=='C') {
                                $consolidarDbCre = 'D';
                            } else {
                                $consolidarDbCre = 'C';
                            }
                            $movements[] = array(
                                'Descripcion' => 'MORA POR COBRAR TERCERO '.$nitDocumento,
                                'Nit' => $nitDocumento,
                                'CentroCosto' => $cargosFijosMora->getCentroCostos(),
                                'Cuenta' => $cargosFijosMora->getCuentaConsolidar(),
                                'Valor' => ($detalleMovimiento['valor']+$detalleMovimiento['iva']),
                                'BaseGrab' => 0,
                                'TipoDocumento' => $tipoDoc,
                                'NumeroDocumento' => $numeroDocumento,
                                'FechaVence' => $fechaVencimiento,
                                'DebCre' => $consolidarDbCre,
                                'debug' => true
                            );

                        }

                    }
                }

                /*//Valida movements
                if (!isset($movements) || count($movements)<2) {
                    throw new SociosException("El movimiento no puede tener menos de 2 lineas en el comprobante. ".print_r($movements,true));
                }*/

                if (count($movements)>1) {
                    foreach ($movements as $movement) {
                        $aura->addMovement($movement);
                        unset($movement);
                    }
                }
            }

            $status = false;
            $auraConsecutivo = 0;
            if (count($movements)>1) {
                $status = $aura->save();
                $auraConsecutivo = $aura->getConsecutivo($codigoComprob);
            }
            
            $options['aura'] = $aura;
            $options['auraStatus'] = $status;
            $options['auraConsecutivo'] = $auraConsecutivo;

            unset($detalleMovimientoObj, $movements, $aura);

            return $auraConsecutivo;
            
        }
        catch(Exception $e) {
            throw new SociosException('Contabilidad: ' . $e->getMessage() . ",<br>" . print_r($e->getTrace(), true));
        }

    }
    
    /**
     * Crea el movimiento en contabilidad del prestamo
     * 
     */
    public function makePrestamosAura($transaction, $prestamosSocios)
    {
        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();

            //Obtenemos comprobante de financiación
            $numeroComprob = 0;
            
            if ($prestamosSocios!=false) {
                
                #Si es modificación
                if ($prestamosSocios->getComprob()) {
                    $numeroComprob = $prestamosSocios->getNumero();
                    $comprobFinanciacion = $prestamosSocios->getComprob();
                } else {
                    
                    #si es movimiento nuevo
                    $comprobFinanciacion = Settings::get('comprob_financiacion', 'SO');
                    if (!$comprobFinanciacion) {
                        throw new SociosException('Configuración: No se ha configurado el comprobante de financiación');
                    }
                }
                
            }
            
            #centro mora
            $centroMora = Settings::get('centro_mora', 'SO');
            if (!$centroMora) {
                throw new SociosException('Configuración: No se ha configurado el centro mora');
            }
            
            #Socios
            $socios = BackCacher::getSocios($prestamosSocios->getSociosId());
            if (!$socios) {
                throw new SociosException('El socio con id "'.$prestamosSocios->getSociosId().'" no existe');
            }
            
            //se crea llamado a servicio web de aura
            $aura = new Aura($comprobFinanciacion, $numeroComprob);
            
            $aura->addMovement(array(
                'Descripcion'    => 'PRESTAMOS SOCIOS No.'.$prestamosSocios->getId(),
                'Nit'            => $socios->getIdentificacion(),
                'CentroCosto'     => $centroMora,
                'Cuenta'         => $prestamosSocios->getCuenta(),
                'Valor'         => $prestamosSocios->getValorFinanciacion(),
                'BaseGrab'         => 0,
                'TipoDocumento' => $comprobFinanciacion,
                'NumeroDocumento' => $prestamosSocios->getId(),
                'FechaVence'     => '',
                'DebCre'         => 'C',
                'debug'         => true
            ));
            
            $aura->addMovement(array(
                'Descripcion'    => 'PRESTAMOS SOCIOS No.'.$prestamosSocios->getId(),
                'Nit'            => $socios->getIdentificacion(),
                'CentroCosto'     => $centroMora,
                'Cuenta'         => $prestamosSocios->getCuentaCruce(),
                'Valor'         => $prestamosSocios->getValorFinanciacion(),
                'BaseGrab'         => 0,
                'TipoDocumento' => $comprobFinanciacion,
                'NumeroDocumento' => $prestamosSocios->getId(),
                'FechaVence'     => '',
                'DebCre'         => 'D',
                'debug'         => true
            ));
            
            $aura->save();
            $auraConsecutivo = $aura->getConsecutivo($comprobFinanciacion);
            
            $prestamosSocios->setComprob($comprobFinanciacion);
            $prestamosSocios->setNumero($auraConsecutivo);
            
            $prestamosSocios->save();
            
            unset($socios,$aura,$prestamosSocios);
        }
        catch(Exception $e) {
            throw new SociosException('Contabilidad: ' . $e->getMessage());
        }
                
    }

    /**
     * Ajusta los saldos de contabilidad de un socio por Aura
     * @param Array $config (
     *     date string
     *     file string with path
     * )
     * @return Boolean
     */
    /*public function ajustarSaldos($config)
    {

        $arr_data = array();
        $date = $config['date'];
        $cuentasCartera = $this->_getCuentasCargosFijos();
        if (!count($cuentasCartera)) {
            throw new SociosException("No se ha configurado las cuentas en cargos fijos");
        }

        $cuentaCrucePagos = Settings::get('cuenta_cruce_pagos', 'SO');
        if (!$cuentaCrucePagos) {
            throw new SociosException("No se ha configurado la cuenta de cruce de pagos en configuracion");
        }

        $cuentaCruce = $cuentaCrucePagos;

        $cuentaMora = Settings::get('cuenta_mora', 'SO');
        if (!$cuentaMora) {
            throw new SociosException("No se ha configurado la cuenta de mora en configuracion");
        }
        $cuentaAjuste = $cuentaMora;
        $file = $config['file'];

        Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
        $periodo = SociosCore::getCurrentPeriodo();

        $year = substr($periodo, 0, 4);
        $month = substr($periodo, 4, 2);
        $fechaInicial = "$year-$month-01";

        if (!$file) {
            throw new SociosException('El archivo no se pudo cargar al servidor');
        } else {

            if (!preg_match('/\.xlsx$/', $file)) {
                throw new SociosException('El archivo cargado parece no ser de Microsoft Excel 2007 o superior');
            }

            try
            {

                //throw new SociosException($fechaInicial);
                
                $transaction = TransactionManager::getUserTransaction();
                
                $comprob = Settings::get('comprob_ajustes', 'SO');
                if (!$comprob) {
                    throw new SociosException('El comprobante de ajustes no se ha definido en configuración');
                }
                
                Core::importFromLibrary('PHPExcel', 'Classes/PHPExcel.php');
                #echo "<br>",$file;
                $arr_data = array();
                
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                $objReader->setReadDataOnly(true);
                
                $objPHPExcel = $objReader->load($file);
                $total_sheets=$objPHPExcel->getSheetCount(); // here 4
                $allSheetName=$objPHPExcel->getSheetNames(); // array ([0]=>'student',[1]=>'teacher',[2]=>'school',[3]=>'college')
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
                $highestRow = $objWorksheet->getHighestRow(); // here 5
                $highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);  // here 5
                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        if (is_array($arr_data)) {
                            $arr_data[$row-1][$col]=$value;
                        }
                    }
                }
                
                if (!count($arr_data)) {
                    throw new SociosException("El archivo esta vacio", 1);
                }
                
                //throw new SociosException("ArrData: ".print_r($arr_data,true));

                unset($objReader);
                $errors = array();
                foreach ($arr_data as $line) {
                    $numeroAccion = trim($line[0]);
                    $identificacion = trim($line[1]);
                    $cuenta = $line[2];
                    $saldoNuevo = $line[3];
                    $numfact = $line[4];
                    
                    $movements = array();
                    
                    #Buscamos socios solo activos
                    $socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst("numero_accion='$numeroAccion' AND identificacion='$identificacion'");
                    if ($socios==false) {
                        //throw new SociosException("El socio con número de acción '$numeroAccion' y cédula '$identificacion' no existe o no esta activo", 1);
                        $errors[] = "El socio con número de acción '$numeroAccion' y cédula '$identificacion' no existe o no esta activo";
                        continue;
                    }
                    
                    $saldoTotal = array('D'=>0, 'C'=>0);
                        
                    #CARTERA
                    $saldo = 0;
                    $carteraObj = EntityManager::get('Cartera')->setTransaction($transaction)->find("nit='$identificacion' and cuenta='$cuenta' and saldo<>0 and f_emision<'$fechaInicial'");
                    foreach ($carteraObj as $cartera) {
                        $saldoCartera = $cartera->getSaldo();

                        if ($saldoCartera>0) {
                            $debCre = 'C';
                            $saldoTotal['C'] += abs($saldoCartera);
                        } else {
                            $debCre = 'D';
                            $saldoTotal['D'] += abs($saldoCartera);
                        
                        }
                        $movements[] = array(
                            'Descripcion'    => 'AJUSTE SALDO '.$date,
                            'Nit'            => $identificacion,
                            'CentroCosto'    => 130,
                            'Cuenta'        => $cuenta,
                            'Valor'            => abs($saldoCartera),
                            'BaseGrab'        => 0,
                            'TipoDocumento'    => 'CXS',
                            'NumeroDocumento' => $cartera->getNumeroDoc(),
                            'FechaVence'    => $date,
                            'DebCre'        => $debCre,
                            'debug'            => true
                        );
                        unset($cartera,$saldoCartera);
                    }
                    unset($carteraObj);
        
                    #AJUSTE NUEVO
                    if ($saldoNuevo!=0) {
                        
                        if ($saldoNuevo<0) {
                            $debCre = 'C';
                            $saldoTotal['C'] += abs($saldoNuevo);
                        } else {
                            $debCre = 'D';
                            $saldoTotal['D'] += abs($saldoNuevo);
                        }
                        
                        $saldoNuevo2 = abs($saldoNuevo);
                        
                        $movements[] = array(
                            'Descripcion'    => 'AJUSTE SALDO '.$date,
                            'Nit'            => $identificacion,
                            'CentroCosto'    => 130,
                            'Cuenta'        => $cuenta,//SOSTENIMIENTO
                            'Valor'            => $saldoNuevo2,
                            'BaseGrab'        => 0,
                            'TipoDocumento'    => 'CXS',
                            'NumeroDocumento' => $numfact,
                            'FechaVence'    => $date,
                            'DebCre'        => $debCre,
                            'debug'            => true
                        );
                    
                        
                        
                    }
                    #echo "<br>saldoTotal2";    print_r($saldoTotal);
        
                    #CRUCE
                    $diffSaldo = abs(abs($saldoTotal['D']) - abs($saldoTotal['C']));
                    if ($diffSaldo>0) {
                        #echo "<br>diff: $diffSaldo = ({$saldoTotal['D']} - {$saldoTotal['C']})";
                        
                        $debCre = 'C';
                        if (abs($saldoTotal['D'])<abs($saldoTotal['C'])) {
                            $debCre = 'D';
                        }
                        
                        $diffSaldo2 = abs($diffSaldo);
                        
                        $movements[] = array(
                            'Descripcion'    => 'AJUSTE SALDO '.$date,
                            'Nit'            => $identificacion,
                            'CentroCosto'    => 130,
                            'Cuenta'        => $cuentaCruce,//CAJA
                            'Valor'            => $diffSaldo2,
                            'BaseGrab'        => 0,
                            'TipoDocumento'    => 'CXS',
                            'NumeroDocumento' => $numfact,
                            'FechaVence'    => $date,
                            'DebCre'        => $debCre,
                            'debug'            => true
                        );
                    }
                
                    //throw new SociosException(print_r($movements,true));
                    
                    #print_r($movements);
                    if (count($movements)) {
                        try
                        {
                            $aura = new Aura($comprob, 0);
                            foreach ($movements as $movement) {
                                #print_r($movement);
                                $aura->addMovement($movement);
                            }
                            $auraStatus = $aura->save();
                            $auraConsecutivo = $aura->getConsecutivo();

                            //Creamos historial de ajuste
                            $identity = IdentityManager::getActive();
                            $ajusteSaldos = EntityManager::get('AjusteSaldos', true)->setTransaction($transaction);
                            $ajusteSaldos->setComprob($comprob);
                            $ajusteSaldos->setNumero($auraConsecutivo);
                            $ajusteSaldos->setFechaHora(date('Y-m-d H:i:s'));
                            $ajusteSaldos->setPeriodo($periodo);
                            $ajusteSaldos->setUsuariosId($identity['id']);
                            if (!$ajusteSaldos->save()) {
                                foreach ($ajusteSaldos->getMessages() as $message) {
                                    throw new SociosException($message->getMessage());
                                }
                            }
                        }
                        catch(AuraException $e) {
                            throw new SociosException("Error en Aura: " . $e->getMessage() . "movements: " . print_r($movements, true));
                        }
                    }
                    
                    
                    #echo "<br><h2>OK</h2>";
                    
                    unset($arr_data, $saldoTotal, $identificacion, $socios, $movements, $aura, $auraConsecutivo, $debCre, $diffSaldo);
                    
                }
                if (count($errors)) {
                    throw new SociosException(implode('<br>', $errors));
                }
                $transaction->commit();
                return true;
                
            }
            catch(Exception $e) {
                throw new SociosException('Ocurrio un error al subir el archivo. ' . $e->getMessage());
            }
        }
        
    }*/
    
    /**
     * Ajusta los saldos de contabilidad de un socio por Aura de lso prestamos
     * @param Array $config (
     *     date string
     *     file string with path
     * )
     * @return Boolean
     */
    /*public function ajustarPrestamos($config)
    {

        $arr_data = array();
        $date = $config['date'];
        $file = $config['file'];
        
        if (!$file) {
            throw new SociosException('El archivo no se pudo cargar al servidor');
        } else {

            if (!preg_match('/\.xlsx$/', $file)) {
                throw new SociosException('El archivo cargado parece no ser de Microsoft Excel 2007 o superior');
            }

            try
            {
                $transaction = TransactionManager::getUserTransaction();
                
                $comprob = Settings::get('comprob_ajustes', 'SO');
                if (!$comprob) {
                    throw new SociosException('El comprobante de ajustes no se ha definido en configuración');
                }
                
                Core::importFromLibrary('PHPExcel', 'Classes/PHPExcel.php');
                #echo "<br>",$file;
                $arr_data = array();
                
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                $objReader->setReadDataOnly(true);
                
                $objPHPExcel = $objReader->load($file);
                $total_sheets=$objPHPExcel->getSheetCount(); // here 4
                $allSheetName=$objPHPExcel->getSheetNames(); // array ([0]=>'student',[1]=>'teacher',[2]=>'school',[3]=>'college')
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
                $highestRow = $objWorksheet->getHighestRow(); // here 5
                $highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);  // here 5
                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        if (is_array($arr_data)) {
                            $arr_data[$row-1][$col]=$value;
                        }
                    }
                }
                
                if (!count($arr_data)) {
                    throw new SociosException("El archivo esta vacio", 1);
                }
                
                unset($objReader);
                
                $movements = array();
                
                foreach ($arr_data as $line) {
                    $numeroAccion = $line[0];
                    $identificacion = $line[1];
                    $saldoNuevo = $line[2];
                    
                    #Buscamos el socios si esta activo
                    $socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst("numero_accion='$numeroAccion' AND identificacion='$identificacion' AND estados_socios_id=1");
                    if ($socios==false) {
                        throw new SociosException("El socio con número de acción '$numeroAccion' y identificación '$identificacion' no existe o no esta activo", 1);
                    }
                    
                    #Buscamos el prestamo activo del socio
                    $prestamosSocios = EntityManager::get('PrestamosSocios')->setTransaction($transaction)->findFirst("socios_id='{$socios->getSociosId()}' AND estado='D'");
                    if ($prestamosSocios==false) {
                        throw new SociosException("El socio con número de acción '$numeroAccion' y identificación '$identificacion' no tiene prestamos con estado 'Debe'", 1);
                    }
                    $numDoc = $prestamosSocios->getId();
                    
                    #Buscamos el movimiento del prestamo
                    $movi = EntityManager::get('Movi')->setTransaction($transaction)->findFirst("comprob='{$prestamosSocios->getComprob()}' AND numero='{$prestamosSocios->getNumero()}'");
                    if ($movi==false) {
                        throw new SociosException("El socio con número de acción '$numeroAccion' y identificación '$identificacion' no tiene el movimiento contable en contabilidad", 1);
                    }
                    
                    #Buscamos saldo del prestamo
                    $saldo = 0;
                    $moviObj = EntityManager::get('Movi')->setTransaction($transaction)->find("nit='$identificacion' AND cuenta='{$prestamosSocios->getCuenta()}'");
                    foreach ($moviObj as $movi) {
                            
                        if ($movi->getDebCre()=='C') {
                            $saldo += $movi->getValor();
                        } else {
                            $saldo -= $movi->getValor();
                        }
                        
                    }
                    
                    $diffAjuste = $saldo - $saldoNuevo;
                    if ($diffAjuste < 0) {
                        //CREDITO
                        $debCre = 'C';
                        $debCreC = 'D';
                    } else {
                        //DEBITO
                        $debCre = 'D';
                        $debCreC = 'C';
                    }
                    $diffAjusteVal = abs($diffAjuste);
                    
                    $cuenta = $prestamosSocios->getCuenta();
                    $cuentaCruce = $prestamosSocios->getCuentaCruce();
                    
                    $movements[] = array(
                        'Descripcion'    => 'AJUSTE PRESTAMOS '.$date,
                        'Nit'            => $identificacion,
                        'CentroCosto'    => 130,
                        'Cuenta'        => $cuenta,
                        'Valor'            => $diffAjusteVal,
                        'BaseGrab'        => 0,
                        'TipoDocumento'    => 'CXS',
                        'NumeroDocumento' => $numDoc,
                        'FechaVence'    => $date,
                        'DebCre'        => $debCre,
                        'debug'            => true
                    );
                    
                    
                    //CRUCE DE CUENTAS
                    $movements[] = array(
                        'Descripcion'    => 'AJUSTE PRESTAMOS '.$date,
                        'Nit'            => $identificacion,
                        'CentroCosto'    => 130,
                        'Cuenta'        => $cuentaCruce,
                        'Valor'            => $diffAjusteVal,
                        'BaseGrab'        => 0,
                        'TipoDocumento'    => 'CXS',
                        'NumeroDocumento' => $numDoc,
                        'FechaVence'    => $date,
                        'DebCre'        => $debCreC,
                        'debug'            => true
                    );
                    
                }

                
                $aura = new Aura($comprob, 0, $date);
                
                //Make Comprob
                foreach ($movements as $movement) {
                    $aura->addMovement($movement);
                }
                
                //throw new SociosException(print_r($movements,true), 1);
                
                $aura->save();
                
                $rc = $aura->getConsecutivo();
                    
                //Creamos historial de ajuste
                $identity = IdentityManager::getActive();
                $ajustePagos = EntityManager::get('AjustePagos', true)->setTransaction($transaction);
                $ajustePagos->setComprob($comprob);
                $ajustePagos->setNumero($rc);
                $ajustePagos->setFechaHora(date('Y-m-d H:i:s'));
                $ajustePagos->setPeriodo($periodo);
                $ajustePagos->setUsuariosId($identity['id']);
                if (!$ajustePagos->save()) {
                    foreach ($ajustePagos->getMessages() as $message) {
                        throw new SociosException($message->getMessage());
                    }
                }
                        
                $transaction->commit();
                return true;
                
            }
            catch(Exception $e) {
                throw new SociosException('Ocurrio un error al subir el archivo. '.$e->getMessage());
            }
        }
        
    }*/

    /**
     * importa los pagos desde un excel y crea comprobantes en contabilidad por Aura
     * @param Array $config (
     *      fecha string date
     *      comprob string
     *      file string with path
     * )
     * @return Boolean
     */
    public function importarPagos($config)
    {
        try {

            if (!isset($config['fecha']) || empty($config['fecha'])) {
                throw new SociosException("Es necesario dar la fecha a importar pagos", 1);
            }
            $fecha = $config['fecha'];

            if (!isset($config['comprob']) || empty($config['comprob'])) {
                throw new SociosException("Es necesario dar el comprobante para importar pagos", 1);
            }
            $comprob = $config['comprob'];

            if (!isset($config['file']) || empty($config['file'])) {
                throw new SociosException("Es necesario dar la ruta del archivo a importar pagos", 1);
            }
            $file = $config['file'];
            
            if (!$file) {
                throw new SociosException('El archivo no se pudo cargar al servidor');
            } else {

                if (!preg_match('/\.xlsx$/', $file)) {
                    throw new SociosException('El archivo cargado parece no ser de Microsoft Excel 2007 o superior');
                }

                $transaction = TransactionManager::getUserTransaction();
                
                Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
                $periodo = SociosCore::getCurrentPeriodo();
                
                //Cuentas de Ajustes '132505%'
                $cuentasCartera = Settings::get('cuenta_ajustes_estado_cuenta', 'SO');
                if (!$cuentasCartera) {
                    throw new SociosException("No se ha definido las 'Cuentas de Ajustes' en Configuración");
                }

                //cuenta que va a capital al importar pagos y sobro plata
                $ctaCapital = Settings::get('capital_importar_pagos', 'SO');
                if (!$ctaCapital) {
                    throw new SociosException("No se ha definido la 'Cuenta de Captal' en Configuración");
                }

                //cuenta que va a cruza el movi al importar pagos
                $ctaCruce = Settings::get('contrapartida_importar_pagos', 'SO');
                if (!$ctaCruce) {
                    throw new SociosException("No se ha definido la 'Cuenta de Cruce' en Configuración");
                }

                //forma de pago al importar pagos
                $formaPagoId = Settings::get('forma_pago_importar_pagos', 'SO');
                if (!$formaPagoId) {
                    throw new SociosException("No se ha definido la 'Forma Pago Importar pagos' en Configuración");
                }

                //obtnemos orden de cuentas a importar pagos
                $ctaImportarPagos = EntityManager::get("Configuration")->find(array(
                    "conditions" => "application='SO' AND name LIKE 'importar_pagos_%' and value!=''",
                    "order" => "id ASC"
                ));
                $ordenCuentas = array();
                foreach ($ctaImportarPagos as $setting) {
                    $ordenCuentas[]= $setting->getValue();                
                }
                unset($ctaImportarPagos);

                $arrData = SociosCore::obtenerDatosDeExcel($file);

                //Agrupamos los pagos por comprobantes y numero acción este caso para ingresos y pago con tarjeta
                $data = array();
                foreach ($arrData as $line) {
                    $numeroAccion = trim($line[0]);
                    $socios = EntityManager::get("Socios")->findFirst("numero_accion='$numeroAccion'");
                    if (!$socios) {
                        throw new SociosException("El socios con acción '$numeroAccion' no existe en maestro de socios");
                    }
                    if (!isset($data[$numeroAccion])) {
                        $data[$numeroAccion] = array();
                    }
                    $data[$numeroAccion][] = array(
                        "socios_id" => $socios->getSociosId(),
                        "nit" => $socios->getIdentificacion(),
                        "valor" => $line[1]
                    );
                    unset($line, $socios);
                }
                unset($arrData);
                
/*
1. fecha antigua
2. Aplicación en Cuentas,; Interes de Mora, Sostenimiento, Consumo Minimo, Consumos, Cuota regalo de navidad, Si genera saldo aplicar a iria a Sostenimiento.
*/

                $identity = IdentityManager::getActive();
                $aPagar = array();  
                $sobro = array();      
                foreach ($data as $numeroAccion => $dataArray) {
                    foreach ($dataArray as $line) {
                        $nit = $line["nit"];
                        $valorPagado = $line["valor"];
                        $valorPagadoInit = $line["valor"];

                        if (!isset($aPagar[$nit])) {
                            $aPagar[$nit] = array();
                        }

                        //preparamos el array con las fecha en orden de la ultima a la primera
                        $sql ="nit='$nit' AND cuenta LIKE '$cuentasCartera%' AND saldo>0 AND f_emision<'$fecha'";
                        $carteraObj = EntityManager::get("Cartera")->find(array(
                            "columns" => "f_emision",
                            "conditions" => $sql,
                            "order" => "f_emision ASC"
                        ));
                        foreach ($carteraObj as $cartera) {
                            $fecha2 = (string) $cartera->getFEmision()->getDate();
                            if (!isset($aPagar[$nit][$fecha2])) {
                                $aPagar[$nit][$fecha2] = array();
                            }
                            unset($fecha2, $cartera);
                        }
                        unset($carteraObj, $sql);

                        //Orden definido en settings de pagos
                        foreach ($ordenCuentas as $cuenta) {
                            //buscamos las cuentas de cartera que faltan por pagar
                            $sql = "nit='$nit' AND cuenta = '$cuenta' AND saldo>0 AND f_emision<'$fecha'";
                            $carteraObj = EntityManager::get("Cartera")->find(array(
                                "conditions" => $sql,
                                "order" => "f_emision ASC"
                            ));
                            
                            foreach ($carteraObj as $cartera) {
                                //Lo que alcanse el pago del socio
                                if ($valorPagado <= 0) {
                                    continue;
                                }

                                //que hay que pagar
                                $fecha2 = (string) $cartera->getFEmision()->getDate();
                                if (!isset($aPagar[$nit][$fecha2][$cuenta])) {
                                    $aPagar[$nit][$fecha2][$cuenta] = array();
                                }
                                $temp = array();
                                foreach ($cartera->getAttributes() as $campo) {
                                    $temp[$campo] = $cartera->readAttribute($campo);
                                }

                                //descontamos lo pagado
                                if ($temp["saldo"] > $valorPagado) {
                                    $temp["pago"] = $valorPagado;
                                    $valorPagado = 0;
                                } else {
                                    $temp["pago"] = $temp["saldo"];
                                    $valorPagado -= $temp["saldo"];    
                                }
                                $temp["valorPagado"] = $valorPagado;
                                $temp["valorPagadoInit"] = $valorPagadoInit;
                            
                                //new row
                                $aPagar[$nit][$fecha2][$cuenta][] = $temp;
                            
                                unset($cartera, $fecha2, $temp);
                            }
                            unset($cuenta, $carteraObj);
                        }

                        //Otras cuentas que no estan en orden de cuentas
                        $sql ="nit='$nit' AND cuenta NOT IN (" . implode(",", $ordenCuentas) . ") AND cuenta LIKE '$cuentasCartera%' AND saldo>0 AND f_emision<'$fecha'";
                        //throw new SociosException($sql);
                        
                        $carteraObj = EntityManager::get("Cartera")->find(array(
                            "conditions" => $sql,
                            "order" => "f_emision ASC"
                        ));
                        
                        foreach ($carteraObj as $cartera) {
                            //Lo que alcanse el pago del socio
                            if ($valorPagado <= 0) {
                                continue;
                            }
                            //miramos que pagar
                            $fecha2 = (string) $cartera->getFEmision()->getDate();
                            $cuenta = $cartera->getCuenta();
                            if (!isset($aPagar[$nit][$fecha2][$cuenta])) {
                                $aPagar[$nit][$fecha2][$cuenta] = array();
                            }
                            $temp = array();
                            foreach ($cartera->getAttributes() as $campo) {
                                $temp[$campo] = $cartera->readAttribute($campo);
                            }
                            //descontamos lo pagado
                            if ($temp["saldo"] > $valorPagado) {
                                $temp["pago"] = $valorPagado;
                                $valorPagado = 0;
                            } else {
                                $temp["pago"] = $temp["saldo"];
                                $valorPagado -= $temp["saldo"];    
                            }
                            $temp["valorPagado"] = $valorPagado;
                            $temp["valorPagadoInit"] = $valorPagadoInit;
                            
                            //New row
                            $aPagar[$nit][$fecha2][$cuenta][] = $temp;

                            unset($cartera, $fecha2, $cuenta, $temp);
                        }
                        unset($carteraObj);

                        //Sobro dinero
                        if ($valorPagado > 0) {
                            $sobro[$nit][] = $valorPagado;
                        }

                        unset($line);
                    }
                    unset($dataArray);
                }
                unset($data);

                //DEBUG
                /*echo "aPagar: ";print_r($aPagar);
                echo "sobro: ";print_r($sobro);
                exit;*/
                
                //Ya teninedo listo todo para apagar en ese orden creamos el aura
                foreach ($aPagar as $nit => $nitArray) {
                    $aura = new Aura($comprob, 0, $fecha);
                    $lastPago = null;
                    $valorTotal = 0;
                    foreach ($nitArray as $fecha2 => $fechaArray) {
                        foreach ($fechaArray as $cuenta => $cuentaArray) {
                            foreach ($cuentaArray as $pago) {
                                
                                //pago
                                $aura->addMovement(array(
                                    'Descripcion' => 'IMPORTAR PAGO ' . $fecha,
                                    'Nit' => $pago['nit'],
                                    'CentroCosto' => $pago['centro_costo'],
                                    'Cuenta' => $pago['cuenta'],
                                    'Valor' => $pago['pago'],
                                    'BaseGrab' => 0,
                                    'TipoDocumento' => $pago['tipo_doc'],
                                    'NumeroDocumento' => $pago['numero_doc'],
                                    'FechaVence' => $pago['f_vence'],
                                    'DebCre' => 'C',
                                    'debug'  => true
                                ));
                                $valorTotal += $pago['pago'];

                                $lastPago = $pago;
                                unset($pago);
                            }
                            unset($cuentaArray, $cuenta);
                        }
                        unset($fechaArray, $fecha2);
                    }

                    try {

                        //Agregamos movimiento de lo que sobro
                        if (isset($sobro[$nit]) == true) {
                            foreach ($sobro[$nit] as $valorSobro) {
                                //pago
                                $aura->addMovement(array(
                                    'Descripcion' => 'IMPORTAR PAGO (SOBRO)' . $fecha,
                                    'Nit' => $lastPago['nit'],
                                    'CentroCosto' => $lastPago['centro_costo'],
                                    'Cuenta' => $ctaCapital,
                                    'Valor' => $valorSobro,
                                    'BaseGrab' => 0,
                                    'TipoDocumento' => $lastPago['tipo_doc'],
                                    'NumeroDocumento' => $lastPago['numero_doc'],
                                    'FechaVence' => $lastPago['f_vence'],
                                    'DebCre' => 'C',
                                    'debug'  => true
                                ));
                                $valorTotal += $valorSobro;
                            }
                        }

                        //CRUCE
                        $aura->addMovement(array(
                            'Descripcion' => 'IMPORTAR PAGO (CRUCE)' . $fecha,
                            'Nit' => $lastPago['nit'],
                            'CentroCosto' => $lastPago['centro_costo'],
                            'Cuenta' => $ctaCruce,
                            'Valor' => $valorTotal,
                            'BaseGrab' => 0,
                            'TipoDocumento' => $lastPago['tipo_doc'],
                            'NumeroDocumento' => $lastPago['numero_doc'],
                            'FechaVence' => $lastPago['f_vence'],
                            'DebCre' => 'D',
                            'debug'  => true
                        ));

                        //throw new SociosException("valorTotal: " . $valorTotal . ", nit: " . $nit, 1);
                        
                        $statusAura = $aura->save(); 
                        //var_dump($statusAura);exit;
                        if ($statusAura == true ) {
                        
                            //CREAMOS RC
                            $rc = $aura->getConsecutivo();
                            
                            $reccaj = new Reccaj();
                            $reccaj->setTransaction($transaction);
                            $reccaj->setNit($nit);
                            $tercero = BackCacher::getTercero($nit);
                            if (!$tercero) {
                                throw new SociosException("El nit '$nit' no existe en Terceros", 1);
                            }
                            $reccaj->setNombre(strtoupper($tercero->getNombre()));
                            $reccaj->setDireccion($tercero->getDireccion());
                            $reccaj->setCiudad($tercero->getLocciu());
                            $reccaj->setTelefono($tercero->getTelefono());
                            $reccaj->setFecha($fecha);
                            $reccaj->setComprob($comprob);
                            $reccaj->setNumero($rc);
                            $reccaj->setCodusu($identity['id']);//De session
                            $reccaj->setObservaciones('PAGO POR OPCION IMPORTAR PAGOS ' . $fecha);
                            $reccaj->setValor($valorTotal);
                            $reccaj->setEstado('C');
                            $reccaj->setRc($rc);
                            
                            if ($reccaj->save() == false) {
                                foreach ($reccaj->getMessages() as $message) {
                                    $transaction->rollback('Reccaj: ' . $message->getMessage());
                                }
                            }
            
                            $detalleReccaj = new DetalleReccaj();
                            $detalleReccaj->setTransaction($transaction);
                            $detalleReccaj->setReccajId($reccaj->getId());
                            $detalleReccaj->setFormaPagoId($formaPagoId);//PRIMERO
                            $detalleReccaj->setNumero(1);
                            $detalleReccaj->setValor($valorTotal);
                            if ($detalleReccaj->save() == false) {
                                foreach ($detalleReccaj->getMessages() as $message) {
                                    $transaction->rollback('DetalleReccaj: ' . $message->getMessage());
                                }
                            }


                            //IMPORTAR PAGOS REGISTRO
                            $importarPagos = new ImportarPagos();
                            $importarPagos->setTransaction($transaction);
                            $importarPagos->setFecha($fecha);
                            $importarPagos->setComprob($comprob);
                            $importarPagos->setNumero($rc);
                            $importarPagos->setNit($lastPago['nit']);
                            $importarPagos->setValor($valorTotal);
                            $importarPagos->setUsuariosId($identity['id']);
                            $importarPagos->setDateCreate(date("Y-m-d H:i:s"));
                            if ($importarPagos->save() == false) {
                                foreach ($importarPagos->getMessages() as $message) {
                                    $transaction->rollback('importarPagos: ' . $message->getMessage());
                                }
                            }
                            
                        } else {
                            throw new SociosException("Aura not save");
                        }
                    } catch(AuraException $e) {
                        throw new SociosException($e->getMessage());
                    }
                    unset($nitArray, $nit, $aura, $reccaj, $detalleReccaj);
                }
                unset($aPagar, $sobro);

                $transaction->commit();
                return true;
            }
            return false;
        } catch(Exception $e) {
            throw new SociosException($e->getMessage()/*. ", trace: " . print_r($e->getTrace(), true)*/ );
            return false;
        }
    }

    /**
     * Obtiene un listado de cuentas que usa los cargos fijos para cartera
     * 
     * @return array
     */
    public function _getCuentasCargosFijos()
    {
        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();
            
            $cuentas = array();
            $cargosFijosObj = EntityManager::get('CargosFijos')->setTransaction($this->_transaction)->find(array("columns"=>"cuenta_consolidar","group"=>"cuenta_consolidar"));
            foreach ($cargosFijosObj as $cargosFijos) {
                $cuentas[]= $cargosFijos->getCuentaConsolidar();
            }
            
            return $cuentas;
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage()."....");
        }
        
    }
    

    /**
     * Actualiza los socios a estado suspendido si deben algo en cartera de los cargos fijos
     */
    public function checkAutoSuspencion()
    {
        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();
            
            //Meses de auto suspencion, pero si es cero no hace nada
            $mesesAutosuspension = (int) Settings::get('autosuspender_meses', 'SO');
            if (!$mesesAutosuspension) {
                return true;
            }
            
            $estadoAutosuspension = Settings::get('autosuspender_estado', 'SO');
            if (!$estadoAutosuspension) {
                throw new SociosException("El estado para autosuspención no se ha definido en configuración.", 1);
            }
            
            $periodo = SociosCore::getCurrentPeriodo();
            $ano = substr($periodo, 0, 4);
            $mes = substr($periodo, 4, 2);
            $fechaIni = "$ano-$mes-01";
            $fechaFin = new Date("$ano-$mes-01");
            $fechaFin->getLastDayOfMonth();

            $cuentasCargosFijos = $this->_getCuentasCargosFijos();
            $configSaldos = array('cuentas'=>$cuentasCargosFijos, 'fechaFactura' => $fechaIni);
            $saldosPorNit = $this->obtenerSaldosCartera($configSaldos);
            $saldosPorNitMeses = $saldosPorNit['numeroMeses'];
            $identity = IdentityManager::getActive();

            $sociosObj = EntityManager::get('Socios')->setTransaction($this->_transaction)->find(array("group"=>"socios_id"));
            foreach ($sociosObj as $socios) {
                $nit = $socios->getIdentificacion();

                //Contamos meses de diferencia al periodo actual
                $difMonths = 0; 
                if (isset($saldosPorNitMeses[$nit])) {
                    foreach ($saldosPorNitMeses[$nit] as $p) {
                        $difMonths += Date::diffMonthsPeriod($p, $periodo);
                    }
                }

                if (isset($saldosPorNit[$nit]) && $saldosPorNit[$nit] > 0 && $difMonths >= $mesesAutosuspension) {
                    //throw new SociosException($difMonths . ":$periodo,mesesAutosuspension: $mesesAutosuspension, ".print_r($saldosPorNitMeses['1020751393'], true));
            
                    $socios->setEstadosSociosId($estadoAutosuspension);
                    if (!$socios->save()) {
                        foreach ($socios->getMessages() as $message) {
                            throw new SociosException($message->getMessage());
                        }
                    } else {
                        $msg = 'Suspendido Automaticamente por falta de pago en periodo ' . $periodo . 
                        ', numero de meses sin pago: ' . $difMonths. ', total a pagar: $'.$saldosPorNit[$socios->getIdentificacion()];
                        
                        //Si cambio estado crear registro de suspendido
                        $suspendido = EntityManager::get('Suspendidos', true)->setTransaction($this->_transaction);
                        $suspendido->setSociosId($socios->getSociosId());
                        $suspendido->setPeriodo($periodo);
                        $suspendido->setUsuariosId($identity['id']);
                        $suspendido->setObservacion($msg);
                        if (!$suspendido->save()) {
                            foreach ($suspendido->getMessages() as $message) {
                                throw new SociosException($message->getMessage());
                            }
                        }
                        
                        //Si cambio estado crear registro de asgnacion de estados
                        $asignacionEstados = EntityManager::get('AsignacionEstados', true)->setTransaction($this->_transaction);
                        $asignacionEstados->setSociosId($socios->getSociosId());
                        $asignacionEstados->setEstadosSociosId($estadoAutosuspension);
                        
                        $asignacionEstados->setFechaIni($fechaIni);
                        $asignacionEstados->setFechaFin($fechaFin->getDate());
                        
                        $asignacionEstados->setObservaciones($msg);
                        if (!$asignacionEstados->save()) {
                            foreach ($asignacionEstados->getMessages() as $message) {
                                throw new SociosException($message->getMessage());
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->_transaction->rollback('Ocurrio un problema. ' . $e->getMessage() . ", Trace: " . print_r($e->getTrace(), true));
        }
    }

    /**
     * Actualiza los socios a estado activo si deben algo en cartera de los cargos fijos
     */
    public function checkAutoActivar()
    {
        try {
            $this->_transaction = TransactionManager::getUserTransaction();
            
            $periodo = SociosCore::getCurrentPeriodo();
            $cuentasCargosFijos = $this->_getCuentasCargosFijos();
            $suspendidosObj = EntityManager::get('Suspendidos')->setTransaction($this->_transaction)->find(array("periodo='$periodo'"));
            
            foreach ($suspendidosObj as $suspendidos) {
                $socios = BackCacher::getSocios($suspendidos->getSociosId());
                if ($socios != false) {
                    $socios->setTransaction($this->_transaction);
                    $socios->setEstadosSociosId(1);//Activo
                    if (!$socios->save()) {
                        foreach ($socios->getMessages() as $message) {
                            throw new SociosException($message->getMessage());
                        }
                    }
                }
                $suspendidos->setTransaction($this->_transaction)->delete();
            }
        } catch (Exception $e) {
            throw new SociosException('Ocurrio un problema. ' . $e->getMessage() . print_r($e->getTrace(), true));
        }
    }

    /**
     * Ajusta los consumos de punto de venta de un socio por Aura
     * @param Array $config (
     *     date string
     *     file string with path
     * )
     * @return Boolean
     */
    public function ajustarConsumos($config)
    {

        $arr_data = array();
        $date = $config['date'];
        $file = $config['file'];

        Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
        $periodo = SociosCore::getCurrentPeriodo();
        
        if (!$file) {
            throw new SociosException('El archivo no se pudo cargar al servidor');
        } else {

            if (!preg_match('/\.xlsx$/', $file)) {
                throw new SociosException('El archivo cargado parece no ser de Microsoft Excel 2007 o superior');
            }

            try
            {
                $transaction = TransactionManager::getUserTransaction();
                
                Core::importFromLibrary('PHPExcel', 'Classes/PHPExcel.php');
                $arr_data = array();
                
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                $objReader->setReadDataOnly(true);
                
                $objPHPExcel = $objReader->load($file);
                $total_sheets=$objPHPExcel->getSheetCount(); // here 4
                $allSheetName=$objPHPExcel->getSheetNames(); // array ([0]=>'student',[1]=>'teacher',[2]=>'school',[3]=>'college')
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
                $highestRow = $objWorksheet->getHighestRow(); // here 5
                $highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);  // here 5
                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        if (is_array($arr_data)) {
                            $arr_data[$row-1][$col]=$value;
                        }
                    }
                }
                
                if (!count($arr_data)) {
                    throw new SociosException("El archivo esta vacio", 1);
                }
                
                unset($objReader);
                
                $identity = IdentityManager::getActive();

                $ajusteConsumos = EntityManager::get('AjusteConsumos')->setTransaction($transaction)->deleteAll("periodo='$periodo'");
                    
                foreach ($arr_data as $line) {
                    #print_r($line);
                    
                    $numeroAccion     = $line[0];
                    $identificacion = $line[1];
                    $prefijo         = $line[2];
                    $numero         = $line[3];
                    $iva             = $line[4];
                    $saldoNuevo     = $line[5];
                    
                    $movements = array();
                    
                    #Buscamos socios solo activos
                    $socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst("numero_accion='$numeroAccion' AND identificacion='$identificacion' AND estados_socios_id=1");
                    if ($socios==false) {
                        throw new SociosException("El socio con número de acción '$numeroAccion' y cédula '$identificacion' no existe o no esta activo", 1);
                    }

                    //Si cambio estado crear registro de suspendido
                    $ajusteConsumos = EntityManager::get('AjusteConsumos', true)->setTransaction($transaction);
                    $ajusteConsumos->setSociosId($socios->getSociosId());
                    $ajusteConsumos->setPrefijo($prefijo);
                    $ajusteConsumos->setNumero($numero);
                    $ajusteConsumos->setFechaHora(date('Y-m-d H:i:s'));
                    $ajusteConsumos->setPeriodo($periodo);
                    $ajusteConsumos->setUsuariosId($identity['id']);
                    $ajusteConsumos->setIva($iva);
                    $ajusteConsumos->setValor($saldoNuevo);
                    if (!$ajusteConsumos->save()) {
                        foreach ($ajusteConsumos->getMessages() as $message) {
                            throw new SociosException($message->getMessage());
                        }
                    }
                    #echo "<br><h2>OK</h2>";
                    
                    unset($arr_data, $identificacion, $socios, $movements, $ajusteConsumos);
                    
                }
                $transaction->commit();
                return true;
                
            }
            catch(Exception $e) {
                throw new SociosException('Ocurrio un error al subir el archivo. '.$e->getMessage());
            }
        }
        
    }

    /**
    * Borra ajuste de pagos del periodo actual
    */
    public function borrarAjustePagos()
    {
        try
        {
            
            $transaction = TransactionManager::getUserTransaction();
                
            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
            $periodo = SociosCore::getCurrentPeriodo();

            $ajustePagosObj = EntityManager::get('AjustePagos', true)->setTransaction($transaction)->find(array("periodo='$periodo'"));
            if (count($ajustePagosObj)) {
                foreach ($ajustePagosObj as $ajustePagos) {
                    $comprob = $ajustePagos->getComprob();
                    $numero = $ajustePagos->getNumero();

                    $movi = EntityManager::get('Movi')->setTransaction($transaction)->findFirst(array("comprob='$comprob' AND numero='$numero'"));
                    if ($movi!=false) {
                        $aura = new Aura($comprob, $numero);
                        $status = $aura->delete();

                        if ($status) {
                            $ajustePagos->delete();
                        }
                    }

                    unset($comprob, $numero, $ajustePagos, $movi);
                }
            }
            
            $transaction->commit();

            return array(
                'status' => 'OK',
                'message' => 'Se borraron los ajustes de pagos del periodo correctamente'
            );
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    /**
    * Borra ajuste de convenios del periodo actual
    */
    public function borrarAjusteConvenios()
    {
        try
        {
            
            $transaction = TransactionManager::getUserTransaction();
                
            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
            $periodo = SociosCore::getCurrentPeriodo();

            $ajustePrestamosObj = EntityManager::get('AjustePrestamos', true)->setTransaction($transaction)->find(array("periodo='$periodo'"));
            if (count($ajustePrestamosObj)) {
                foreach ($ajustePrestamosObj as $ajustePrestamos) {
                    $comprob = $ajustePrestamos->getComprob();
                    $numero = $ajustePrestamos->getNumero();

                    $movi = EntityManager::get('Movi')->setTransaction($transaction)->findFirst(array("comprob='$comprob' AND numero='$numero'"));
                    if ($movi!=false) {
                        $aura = new Aura($comprob, $numero);
                        $status = $aura->delete();

                        if ($status) {
                            $ajustePrestamos->delete();
                        }
                    }

                    unset($comprob, $numero, $ajustePrestamos, $movi);
                }
            }
            
            $transaction->commit();

            return array(
                'status' => 'OK',
                'message' => 'Se borraron los ajustes de prestamos del periodo correctamente'
            );
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    /**
    * Borra ajuste de saldos del periodo actual
    */
    public function borrarAjusteSaldos()
    {
        try
        {
            
            $transaction = TransactionManager::getUserTransaction();
                
            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
            $periodo = SociosCore::getCurrentPeriodo();

            $ajusteSaldosObj = EntityManager::get('AjusteSaldos', true)->setTransaction($transaction)->find(array("periodo='$periodo'"));
            if (count($ajusteSaldosObj)) {
                foreach ($ajusteSaldosObj as $ajusteSaldos) {
                    $comprob = $ajusteSaldos->getComprob();
                    $numero = $ajusteSaldos->getNumero();

                    $movi = EntityManager::get('Movi')->setTransaction($transaction)->findFirst(array("comprob='$comprob' AND numero='$numero'"));
                    if ($movi!=false) {
                        $aura = new Aura($comprob, $numero);
                        $status = $aura->delete();

                        if ($status) {
                            $ajusteSaldos->delete();
                        }
                    }

                    unset($comprob, $numero, $ajusteSaldos, $movi);
                }
            }

            return array(
                'status' => 'OK',
                'message' => 'Se borraron los ajustes de saldos del periodo correctamente'
            );
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    /**
    * Borra ajuste de consumos del periodo actual
    */
    public function borrarAjusteConsumos()
    {
        try
        {
            
            $transaction = TransactionManager::getUserTransaction();
                
            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
            $periodo = SociosCore::getCurrentPeriodo();

            $ajusteConsumosObj = EntityManager::get('AjusteConsumos', true)->setTransaction($transaction)->find(array("periodo='$periodo'"));
            if (count($ajusteConsumosObj)) {
                foreach ($ajusteConsumosObj as $ajusteConsumos) {
                    $ajusteConsumos->delete();
                    unset($ajusteConsumos);
                }
            }
            
            $transaction->commit();

            return array(
                'status' => 'OK',
                'message' => 'Se borraron los ajustes de consumos del periodo correctamente'
            );
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    /**
     * Devuelve el numero de meses que no se genero factura por estado suspendido principalmente
     *  
     * @param array $config (
     *     'sociosId' => int 
     * ) 
     * @return ActiveRecord $factura 
    */
    public function facturasNoCausadas($config)
    {
        try
        {
            $periodoActual = SociosCore::getCurrentPeriodo();
            $config['periodo'] = (string) $periodoActual;

            if (!isset($config['sociosId'])) {
                throw new SociosException("Debe dar el id del socios", 1);
            }

            //Obtenemos la ultima factura generada al socio
            $ultimaFactura = $this->_getUltimaFactura($config);
            if ($ultimaFactura!=false) {
                $ultimoPeriodo = $ultimaFactura->getPeriodo();
            } else {
                //Si no tiene facturas en el pasado coje el periodo actual
                $ultimoPeriodo = $periodoActual;
            }

            //Diff de meses en periodo
            $diff = Date::diffMonthsPeriod($periodoActual, $ultimoPeriodo);

            return $diff;
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage());
        }
        
    }

    /**
     * Obtiene la ultima factura generada a un socio
     * 
     * @param array $config (
     *     'sociosId' => int 
     * ) 
     * @return ActiveRecord $factura 
    */
    private function _getUltimaFactura($config)
    {

        if (!isset($config['sociosId'])) {
            throw new SociosException("Debe proveer el id del socio a buscar");
        }
        $transaction = TransactionManager::getUserTransaction();
            
        $factura = EntityManager::get('Factura')->setTransaction($transaction)->findFirst(array("socios_id='{$config['sociosId']}'",'order'=>'periodo DESC'));
        
        return $factura;
    }

    /**
     * Genera los cargos de socios del periodo con base a los cargos fijos asignados 
    */
    public function generarCargosSocios(&$config)
    {
        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();
            
            $date = new Date();

            //Periodo actual
            $periodo = SociosCore::getCurrentPeriodo();
                
            //limpiamos los cargos_socios de la fecha a facturar
            $cargosSocios = EntityManager::get('CargosSocios')->setTransaction($this->_transaction)->delete('fecha="'.$config['fechaFactura'].'"');

            //Buscamos Asignacion de cargos de un socio
            $clases = array();

            $asignacionCargosObj = EntityManager::get('AsignacionCargos')->setTransaction($this->_transaction)->find(array('conditions'=>'estado="A"'));

            foreach ($asignacionCargosObj as $asignacionCargos) {
            
                $cargosFijos = $asignacionCargos->getCargosFijos();

                //Si se va a generar cosas que no son sostenimiento skip
                if ($cargosFijos->getClaseCargo()=='S') {
                    if (!$config['g_sostenimiento']) {
                        continue;
                    }
                }
            
                //Si se va a generar cosas que no son administracion skip
                if ($cargosFijos->getClaseCargo()=='A') {
                    if (!$config['g_administracion']) {
                        continue;
                    }
                }
            
                $sociosId = $asignacionCargos->getSociosId();

                //Buscamos los socios que tengan tipos de cargos y estado Activo
                $socios = BackCacher::getSocios($sociosId);
        
                if ($socios) {
                    
                    //Generamos los cargos de un socio
                    $cargosSocios = new CargosSocios();
                    $cargosSocios->setTransaction($this->_transaction);
                    $cargosSocios->setFecha($config['fechaFactura']);
                    $cargosSocios->setPeriodo($periodo);
                    $cargosSocios->setSociosId($sociosId);
                    $cargosSocios->setCargosFijosId($cargosFijos->getId());
                    $cargosSocios->setDescripcion($cargosFijos->getNombre());
                    $cargosSocios->setValor($cargosFijos->getValor());
                    
                    $iva = $cargosFijos->getValor() * $cargosFijos->getPorcentajeIva() / 100;
                    $iva = LocaleMath::round($iva, 0);
                    $cargosSocios->setIva($iva);

                    $ico = $cargosFijos->getValor() * $cargosFijos->getIco() / 100;
                    $ico = LocaleMath::round($ico, 0);
                    $cargosSocios->setIco($ico);

                    $cargosSocios->setCuotaAplicar($cargosSocios->getValor() + $cargosSocios->getIva() + $cargosSocios->getIco());
                    $cargosSocios->setEstado('P');

                    if ($cargosSocios->save()==false) {
                        foreach ($cargosSocios->getMessages() as $msg) {
                            throw new SociosException($msg->getMessage());
                        }
                    }

                    unset($iva, $cargosSocios);
                }

                unset($asignacionCargos, $socios, $cargosFijos);
            }

            unset($asignacionCargosObj, $periodo, $date);
        } catch(Exception $e) {
            throw new SociosException("_generarCargosSocios: ".$e->getMessage());
        }
    }

    /**
    * Genera Ajuste de saldos anteriores segun factura actual
    */
    public function ajusteSaldosAnteriores()
    {
        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();
            
            $date = new Date();

            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
            //Periodo actual
            $periodo = SociosCore::getCurrentPeriodo();
            $periodoAnterior = $periodo - 1;

            $cuentaMora = Settings::get("cuenta_mora", "SO");

            $rows = array();
                
            $movimientoObj = EntityManager::get("Movimiento")->find("periodo=$periodo");
            foreach ($movimientoObj as $movimiento) {
                //Si no tiene saldo anterio buscar el siguiente
                $existeSaldoAnterior = EntityManager::get("DetalleMovimiento")->findFirst("movimiento_id={$movimiento->getId()} AND descripcion LIKE 'SALDO PERIODO%'");
                if ($existeSaldoAnterior==false) {
                    continue;
                }


                //$socios = BackCacher::getSocios($movimiento->getSociosId());
                //$nit = trim($socios->getIdentificacion());
                $nit = $movimiento->getSociosId();
                if (isset($row[$nit])) {
                    continue;
                }

                if (!isset($rows[$nit])) {
                    $rows[$nit] = array($cuentaMora=>0);
                }

                $socios = BackCacher::getSocios($nit);
                echo "<br><h3>($nit) {$socios->getNumeroAccion()}, cc. {$socios->getIdentificacion()}</h3>";

                //buscamos el saldo anterior y cuanto vale lo de este mes
                $valorSaldoAnterior = 0;
                $valorPagarPeriodo = 0;
                $valorMoraPeriodo = 0;

                $a = array();
                $b = array();
                //inicializamos los valores de otras cuentas a los socios
                $cargosFijosObj = EntityManager::get("CargosFijos")->find(array("group"=>"cuenta_consolidar"));
                foreach ($cargosFijosObj as $cargosFijos) {
                    $cuenta = $cargosFijos->getCuentaConsolidar();
                    if (!isset($a[$cuenta])) {
                        $a[$cuenta] = 0;
                    }
                }

                $detalleMovimientoObj = EntityManager::get("DetalleMovimiento")->find("movimiento_id={$movimiento->getId()} AND estado='A'");
                foreach ($detalleMovimientoObj as $detalleMovimiento) {
                    $desc = $detalleMovimiento->getDescripcion();
                        
                    if (strstr($desc, "SALDO PERIODO")) {
                        $valorSaldoAnterior += $detalleMovimiento->getTotal();
                    } else {
                        $descTemp = str_replace("PERIODO $periodo", "", $desc);
                            
                        if (strstr($descTemp, "MORA DE SALDO")) {
                            $valorMoraPeriodo += $detalleMovimiento->getTotal();
                        } else {
                            $cuenta = "130505050";
                            $cargosFijos = EntityManager::get("CargosFijos")->findFirst("nombre = '$descTemp'");
                            if ($cargosFijos) {
                                $cuenta = $cargosFijos->getCuentaConsolidar();
                            }
                            if (!isset($a[$cuenta])) {
                                $a[$cuenta] = 0;
                            }
                            if (!isset($b[$descTemp])) {
                                $b[$descTemp] = 0;
                            }
                        
                            $valorPagarPeriodo += $detalleMovimiento->getTotal();
                            $a[$cuenta] += $detalleMovimiento->getTotal();
                            $b[$descTemp] += $detalleMovimiento->getTotal();
                        }
                    }
                    unset($detalleMovimiento);
                }
                //$valorMoraPeriodo = 0;
                $totalMora = 0;
                $ivaMora = 0;
                if ($valorSaldoAnterior>0) {
                    $valorMoraPeriodo = LocaleMath::round(($valorSaldoAnterior*1.9/100), 0);
                    //Calculamos el iva de la mora
                    $ivaMora = ($valorMoraPeriodo * 16 / 100);
                    $ivaMora = LocaleMath::round($ivaMora, 0);
                }
                $totalMora = ($valorMoraPeriodo + $ivaMora);
                $valorPagarPeriodoConMora = ($valorPagarPeriodo+$totalMora);

                echo "<br/><br/>".$movimiento->getId().":<br>";
                echo "valorSaldoAnterior:".$valorSaldoAnterior;
                echo ",valorPagarPeriodo:".$valorPagarPeriodo;
                echo ",valorMoraPeriodo:".$valorMoraPeriodo;
                echo ",valorIvaMoraPeriodo:".$ivaMora;
                echo ",valorTotalMoraPeriodo:".$totalMora;
                echo ",valorPagarPeriodoConMora:".$valorPagarPeriodoConMora;
                echo "<br>a:".print_r($a, true)."<br>";
                echo "<br>b:".print_r($b, true)."<br>";

                //validamos cuales se van a la 130505050
                if ($valorSaldoAnterior<0 || $valorPagarPeriodo<=0 || $valorSaldoAnterior<$valorPagarPeriodo) {
                    //se va a sostenimiento
                    echo ",cuentaDefault: $cuentaMora(valor:".$valorSaldoAnterior.")";
                    $rows[$nit][$cuentaMora] = $valorSaldoAnterior;
                } else {
                    //Si son iguales aplicar la misma
                    if (ceil($valorSaldoAnterior)==ceil($valorPagarPeriodo)) {
                        foreach ($a as $cuenta => $valor) {
                            echo ",cuenta1: $cuenta(valor: $valor)";
                            if (!isset($rows[$nit][$cuenta])) {
                                $rows[$nit][$cuenta] = 0;
                            }
                            $rows[$nit][$cuenta] += $valor;
                        }
                    } else {
                        $diffValor = 0 ;
                        //Si es mayor al saldo del periodo pero no mayor al doble
                        if (($valorSaldoAnterior>$valorPagarPeriodo) && (($valorPagarPeriodo*2)>$valorSaldoAnterior)) {
                            $diffValor = $valorSaldoAnterior;
                            foreach ($a as $cuenta => $valor) {
                                echo ",cuenta2: $cuenta(valor: $valor)";
                                if (!isset($rows[$nit][$cuenta])) {
                                    $rows[$nit][$cuenta] = 0;
                                }
                                $rows[$nit][$cuenta] += $valor;

                                $diffValor-=$valor;
                            }
                            echo ",diffValor2($cuentaMora): ".$diffValor;
                            $rows[$nit][$cuentaMora] += $diffValor;
                            echo ",cuenta2: $cuentaMora(valor: {$rows[$nit][$cuentaMora]})";
                                
                        } else {
                            $diffValor = 0 ;
                            //Si es mayor al saldo del periodo pero no mayor al doble
                            if (($valorSaldoAnterior>($valorPagarPeriodo*2)) && (($valorPagarPeriodo*3)>$valorSaldoAnterior)) {
                                $diffValor = $valorSaldoAnterior;
                                foreach ($a as $cuenta => $valor) {
                                    $valor = $valor*2;
                                    echo ",cuenta3: $cuenta(valor: $valor)";
                                    if (!isset($rows[$nit][$cuenta])) {
                                        $rows[$nit][$cuenta] = 0;
                                    }
                                    $rows[$nit][$cuenta] += $valor;

                                    $diffValor-=$valor;
                                }
                                echo ",diffValor3: ".$diffValor;
                                $rows[$nit][$cuentaMora] += $diffValor;
                            } else {
                                $diffValor = 0 ;
                                //Si es mayor al saldo del periodo pero no mayor al doble
                                if (($valorSaldoAnterior>($valorPagarPeriodo*3)) && (($valorPagarPeriodo*4)>$valorSaldoAnterior)) {
                                    $diffValor = $valorSaldoAnterior;
                                    foreach ($a as $cuenta => $valor) {
                                        $valor = $valor*3;
                                        echo ",cuenta4: $cuenta(valor: $valor)";
                                        if (!isset($rows[$nit][$cuenta])) {
                                            $rows[$nit][$cuenta] = 0;
                                        }
                                        $rows[$nit][$cuenta] += $valor;

                                        $diffValor-=$valor;
                                    }
                                    echo ",diffValor4: ".$diffValor;
                                    $rows[$nit][$cuentaMora] += $diffValor;
                                } else {
                                    $diffValor = 0 ;
                                    //Si es mayor al saldo del periodo pero no mayor al doble
                                    if (($valorSaldoAnterior>($valorPagarPeriodo*4)) && (($valorPagarPeriodo*5)>$valorSaldoAnterior)) {
                                        $diffValor = $valorSaldoAnterior;
                                        foreach ($a as $cuenta => $valor) {
                                            $valor = $valor*4;
                                            echo ",cuenta5: $cuenta(valor: $valor)";
                                            if (!isset($rows[$nit][$cuenta])) {
                                                $rows[$nit][$cuenta] = 0;
                                            }
                                            $rows[$nit][$cuenta] += $valor;

                                            $diffValor-=$valor;
                                        }
                                        echo ",diffValor5: ".$diffValor;
                                        $rows[$nit][$cuentaMora] += $diffValor;
                                    } else {
                                        $diffValor = 0 ;
                                        //Si es mayor al saldo del periodo pero no mayor al doble
                                        if (($valorSaldoAnterior>($valorPagarPeriodo*5)) && (($valorPagarPeriodo*6)>$valorSaldoAnterior)) {
                                            $diffValor = $valorSaldoAnterior;
                                            foreach ($a as $cuenta => $valor) {
                                                $valor = $valor*5;
                                                echo ",cuenta6: $cuenta(valor: $valor)";
                                                if (!isset($rows[$nit][$cuenta])) {
                                                    $rows[$nit][$cuenta] = 0;
                                                }
                                                $rows[$nit][$cuenta] += $valor;

                                                $diffValor-=$valor;
                                            }
                                            echo ",diffValor6: ".$diffValor;
                                            $rows[$nit][$cuentaMora] += $diffValor;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if ($rows[$nit][$cuentaMora]>0) {
                    $rows[$nit][$cuentaMora] -= $totalMora;
                }
                unset($a, $movimiento, $detalleMovimientoObj);
            }
            unset($movimientoObj);

            $report = ReportBase::factory("excel");

            $titulo = new ReportText('REPORTE DE AJUSTE DE SOCIOS AUTO', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo));

            $report->setDocumentTitle('REPORTE DE AJUSTE DE SOCIOS AUTO');
            $report->setColumnHeaders(array(
                'NUMERO DE ACCION',
                'IDENTIFICACION',
                'CUENTA',
                'VALOR',
                'NUMERO FACTURA ANT.'
            ));

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));

            $report->setColumnStyle(array(0,1,2,3), new ReportStyle(array(
                'textAlign' => 'center',
                'fontSize' => 11
            )));

            print_r($rows);

            $report->start(true);

            foreach ($rows as $sociosId => $row) {
                $socios = BackCacher::getSocios($sociosId);
                if ($socios!=false) {

                    $numFactAnterior = 1;
                    $factura = EntityManager::get("Factura")->findFirst("socios_id='$sociosId' AND periodo='$periodoAnterior'");
                    if ($factura) {
                        $numFactAnterior = $factura->getNumero();
                    }

                    foreach ($row as $cuenta => $valor) {
                        $report->addRow(array(
                            $socios->getNumeroAccion(),
                            $socios->getIdentificacion(),
                            $cuenta,
                            $valor,
                            $numFactAnterior
                        ));
                    }
                }
            }
            
            $report->finish();
            echo "<br>",$fileName = $report->outputToFile('public/temp/ajuste-saldos-auto');
        }
        catch(Exception $e) {
            throw new SociosException("_generarCargosSocios: ".$e->getMessage());
        }
    }

    /**
    * Revisa el estado del convenio activo de un socios actualizando la tabla de amortizacion
    *
    * @param ActiveRecord $prestamosSocios
    */
    public function revisarConvenios($prestamosSocios)
    {
        if (!$prestamosSocios) {
            throw new SociosException("Debe dar el prestamo del socios para revisar el convenio", 1);
        }
        if (!$this->_transaction) {
            $this->_transaction = TransactionManager::getUserTransaction();
        }
            
        $sociosId = $prestamosSocios->getSociosId();
        $socios = BackCacher::getSocios($sociosId);
    
        if ($socios) {
            $nit = $socios->getIdentificacion();
            $valorInicial = $prestamosSocios->getValorFinanciacion();
            $moviObj2 = $this->Movi->setTransaction($this->_transaction)->find("cuenta='{$prestamosSocios->getCuenta()}' AND nit='$nit' AND deb_cre='C'");
            $valorPagado = 0;
            foreach ($moviObj2 as $movi) {
                $valorPagado += $movi->getValor();
            }
            //throw new SociosException("cuenta='{$prestamosSocios->getCuenta()}' AND nit='$nit' AND deb_cre='C':  ".$valorPagado);
            
            $saldoPendiente    = (float) abs($valorInicial - $valorPagado);
            
            $amortizacionObj = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->find("prestamos_socios_id=".$prestamosSocios->getId());
            //throw new SociosException(count($amortizacionObj).', '."prestamos_socios_id=".$prestamosSocios->getId());
            
            foreach ($amortizacionObj as $amortizacion) {
                $estado= 'D';
                $saldoAmortizacion = (float) $amortizacion->getSaldo();

                //throw new SociosException("saldoAmortizacion: $saldoAmortizacion, saldopendiente: $saldoPendiente, valorPagado: $valorPagado");
                

                if ($saldoAmortizacion>=$saldoPendiente) {
                    $estado= 'P';
                }
                $amortizacion->setTransaction($this->_transaction);
                $amortizacion->setEstado($estado);//Pagado
                if ($amortizacion->save()==false) {
                    foreach ($amortizacion->getMessages() as $msg) {
                        throw new SociosException("Amortizacion: ".$msg->getMessage());
                    }
                }
            }
        }
    }


    /**
    * Retorna informacion de prestamos a facturar en el periodo actual
    *
    * @param array $config
    * @return array
    */
    public function getConveniosAFacturar(&$config)
    {

        try
        {
        
            $this->_transaction = TransactionManager::getUserTransaction();

            $prestamosSociosObj = EntityManager::get('PrestamosSocios')->setTransaction($this->_transaction)->find(array('conditions'=>'socios_id='.$config['sociosId'].' AND estado="D"'));

            $prestamoArray = array();

            //periodo actual
            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
            $periodo = SociosCore::getCurrentPeriodo();
            $periodoObj = EntityManager::get('Periodo')->findFirst("periodo='$periodo'");
            if (!$periodoObj) {
                throw new SociosException("No se encontro periodo $periodo");
            }
            
            $fechaFactura = SociosCore::getFechaFactura($periodo);

            $mora = SociosCore::getCurrentPeriodoMora();
                    
            foreach ($prestamosSociosObj as $prestamosSocios) {
                
                //ultimo abonos
                $ultimoAbono = 0.00;
                $fechaUltimo = '';

                $valorPeriodo = 0;
                $valor = 0;
                $valorMora = 0;
                $valorTotal = 0;
                $totalDiasMora = 0;
                $periodoAmortizacion = '';
                $amortizacionSaldo = '';

                $totalConvenio = $prestamosSocios->getValorFinanciacion();

                //revisar estado de amortizacion
                $this->revisarConvenios($prestamosSocios);
                
                //Cuota a hoy de amortizacion
                $amortizacionHoy = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId()." AND MONTH(fecha_cuota)='{$fechaFactura->getMonth()}' AND YEAR(fecha_cuota)='{$fechaFactura->getYear()}'",'order'=>'numero_cuota ASC'));
                if (!$amortizacionHoy) {
                    $amortizacionHoy = EntityManager::get('Amortizacion', true)->setTransaction($this->_transaction);
                    $amortizacionHoy->setFechaCuota($fechaFactura);
                }

                //Cuota actual de amortizacion
                $amortizacionSaldoObj = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->find(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId().' AND estado="D"'." AND fecha_cuota<'{$fechaFactura->getDate()}'",'order'=>'numero_cuota ASC'));
                foreach ($amortizacionSaldoObj as $amortizacionSaldo) {
                    
                    //encontramos meses de mora de cuota extraordinaria
                    try
                    {
                        $fechaAmortizacion = new Date($amortizacionSaldo->getFechaCuota());
                        $periodoAmortizacion = $fechaAmortizacion->getPeriod();
                    }
                    catch(Exception $e) {
                        $year = substr($amortizacionSaldo->getFechaCuota(), 0, 4);
                        $month = substr($amortizacionSaldo->getFechaCuota(), 5, 2);
                        $fechaAmortizacion = new Date("$year-$month-01");
                        $fechaAmortizacionStr = $fechaAmortizacion->getLastDayOfMonth($month, $year);
                        $fechaAmortizacion = new Date($fechaAmortizacionStr);
                        $periodoAmortizacion = $fechaAmortizacion->getPeriod();
                    }

                    //buscamos meses de diferencia y sacamos saldos anteriores de cuotas extraordinarias
                    $diffPeriod = Date::diffMonthsPeriod($periodoAmortizacion, $periodo);
                    if ($diffPeriod>0) {

                        //$valor = ($amortizacionSaldo->getValor() * $diffPeriod);
                        $valor += $amortizacionSaldo->getValor();
                        $valorPeriodo = $amortizacionSaldo->getValor();

                        $valorMoraDia = LocaleMath::round(($amortizacionSaldo->getValor() * $mora / 100 / 30), 0);
                        
                        //Dias de mora
                        $fechaPeriodoAmortizacion = new Date($amortizacionHoy->getFechaCuota());
                        $diffDias = Date::difference($fechaAmortizacion, $fechaPeriodoAmortizacion);
                        $diffDias = abs($diffDias);
                        
                        $totalDiasMora += $diffDias;

                        $valorMora += ($valorMoraDia * $diffDias);
                                
                    }
                    //echo "<br>"."diffperiod: $diffPeriod, diffDias: $diffDias, valor: $valor, valorMoraDia: $valorMoraDia, valorMora : $valorMora, fechaAmortizacion: $fechaAmortizacion,fechaFactura: $fechaFactura";
                }
                $valorTotal = $valor + $valorMora;
    
                
                //throw new SociosException("diffDias: $totalDiasMora, valor: $valor, valorMoraDia: $valorMoraDia, valorMora: $valorMora, fechaFactura: $fechaFactura");
                        
                if ($valorTotal) {
                    $prestamoArray[] = array(
                        'descripcion'    => 'SALDO CUOTA EXTRA. DESDE PERIODO '.$periodoAmortizacion,
                        'valor'         => $valor,
                        'mora'            => $valorMora,
                        'total'         => $valorTotal
                    );
                }

                //throw new SociosException($valorMora);
                

                if ($amortizacionHoy) {

                    $prestamoArray[] = array(
                        'descripcion'    => 'CUOTA EXTRA. PERIODO '.$periodo,
                        'valor'         => $amortizacionHoy->getValor(),
                        'mora'            => 0,
                        'total'         => $amortizacionHoy->getValor()
                    );

                }

            }
            //throw new SociosException(print_r($prestamoArray,true));
            return $prestamoArray;
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    //////////////////////////////////////////
    ///// INVOICER EN HFOS_SOCIOS
    //////////////////////////////////////////

    /**
    * Validate a Invoicer
    *
    * @param array $options
    */
    private function _validateInvoicer(&$options)
    {

        $this->_transaction = TransactionManager::getUserTransaction();

        $nitDocumento = Filter::bring($options['nitDocumento'], 'terceros');
        if ($nitDocumento=='') {
            throw new SociosException('Indique el tercero al que se le generará la factura');
        } else {
            $tercero = BackCacher::getTercero($nitDocumento);
            if ($tercero==false) {
                throw new SociosException('No existe el tercero con número de documento "'.$nitDocumento.'"');
            }
        }
        $options['nitDocumento'] = $nitDocumento;
        $options['tercero']      = $tercero;

        $fechaFactura = $options['fechaFactura'];
        $fechaVencimiento = $options['fechaVencimiento'];

        try
        {
            if (Date::isLater($fechaFactura, $fechaVencimiento)) {
                throw new SociosException('La fecha de vencimiento no puede ser menor a la fecha de la factura');
            }
        }
        catch(DateException $e) {
            throw new SociosException($e->getMessage());
        }
        $options['fechaFactura'] = $fechaFactura;
        $options['fechaVencimiento'] = $fechaVencimiento;

        /*$codigoAlmacen = Settings::get('almacen_venta');
        if (!$codigoAlmacen) {
            throw new SociosException('No se ha configurado el almacén donde se descargan las referencias de la factura');
        } else {
            $almacen = BackCacher::getAlmacen($codigoAlmacen);
            if ($almacen==false) {
                throw new SociosException('El almacén donde se descargan las referencias de la factura configurado no existe');
            }
        }
        $options['codigoAlmacen']     = $codigoAlmacen;
        $options['almacen']         = $almacen;*/
        
        $codigoComprob = Settings::get('comprob_factura', 'SO');
        if ($codigoComprob=='') {
            throw new SociosException('No se ha configurado el comprobante de socios');
        }
        $options['codigoComprob'] = $codigoComprob;

        //throw new SociosException($codigoComprob);
        $comprob = BackCacher::getComprob($codigoComprob);
        //$comprob = EntityManager::get('Comprob')->findFirst(array('conditions'=>"codigo='$codigoComprob'"));
        if ($comprob==false) {
            throw new SociosException('El comprobante '.$codigoComprob.' no existe');
        }
        $options['comprob'] = $comprob;

        /*$nitEntregarDocumento = $options['nitEntregarDocumento'];
        if ($nitEntregarDocumento=='') {
            throw new SociosException('Indique el tercero al que se le entregará la mercancía');
        } else {
            $terceroEntregar = BackCacher::getTercero($nitEntregarDocumento);
            if ($terceroEntregar==false) {
                throw new SociosException('No existe el tercero con número de documento "'.$nitEntregarDocumento.'"');
            }
        }
        $options['nitEntregarDocumento'] = $nitEntregarDocumento;
        $options['terceroEntregar']      = $terceroEntregar;*/

    }

    /**
    * Validate and add items per apps
    *
    * @param array $options
    */
    public function _addItem(&$options)
    {

        $codigoAlmacen     = $options['codigoAlmacen'];
        $totalFactura     = $options['totalFactura'];
        $resumenVenta     = $options['resumenVenta'];
        $resumenIva     = $options['resumenIva'];
        $cantidades     = $options['cantidades'];
        $descuentos     = $options['descuentos'];
        $comprob         = $options['comprob'];
        $detalles         = $options['detalles'];
        $precios         = $options['precios'];
        $item             = $options['item'];
        $n                 = $options['n'];

        $empresa = EntityManager::get('Empresa')->findFirst();
        //throw new SociosException($empresa->getNit());
        $nits = EntityManager::get('Nits')->findFirst(array('conditions'=>"nit='{$empresa->getNit()}'"));
        if ($nits==false) {
            throw new SociosException('El nit de la empresa no esta creado en terceros: '.$empresa->getNit());
        }
        if (!$nits->getEstadoNit()) {
            throw new SociosException('No se ha configurado el regimen de la empresa en terceros');
        }
        $regimenCuentas = EntityManager::get('RegimenCuentas')->findFirst(array('conditions'=>"regimen='{$nits->getEstadoNit()}'"));
        if ($regimenCuentas==false) {
            throw new SociosException('No se ha configurado las cuentas segun regimen de la empresa');
        }

        //$cargosFijos = BackCacher::getCargosFijos($item);
        $cargosFijos = $this->CargosFijos->findFirst($item);
        if ($cargosFijos==false) {
            throw new SociosException('El cargo fijo con código "'.$item.'" no existe, en la línea '.($n+1));
        }
        $options['cargosFijos'] = $cargosFijos;

        $cuentaVenta = BackCacher::getCuenta($cargosFijos->getCuentaContable());
        if ($cuentaVenta==false) {
            throw new SociosException('La cuenta de venta no existe, para el cargo fijo "'.$cargosFijos->getNombre().'", en la línea '.($n+1));
        }
        $options['cuentaVenta'] = $cuentaVenta;

        if ($cantidades[$n]<=0) {
            throw new SociosException('La cantidad debe ser mayor o igual a cero en la línea '.($n+1));
        }

        /*if ($precios[$n]<=0) {
            throw new SociosException('El precio debe ser mayor o igual a cero en la línea '.($n+1).': '.print_r($options,true));
        }*/

        $codigoCuentaIva = null;

        if ($cargosFijos->getPorcentajeIva()===null) {
            throw new SociosException('No se ha definido el porcentaje de IVA de venta del cargo fijo '.$cargosFijos->getNombre().', en la línea '.($n+1));
        }

        $codigoCuentaIva = 0;
        
        $valorTotal = ($precios[$n] * $cantidades[$n]);

        if ($cargosFijos->getPorcentajeIva()>0) {

            $baseIva = $valorTotal / ( 1 + ($cargosFijos->getPorcentajeIva()/100));
            $iva = $valorTotal - $baseIva;

            if ($cargosFijos->getPorcentajeIva()==16||$cargosFijos->getPorcentajeIva()==10) {
                $ivaSel = (int) $cargosFijos->getPorcentajeIva();
                if ($ivaSel==16) {
                    //16%
                    $codigoCuentaIva = $regimenCuentas->getCtaIva16v();
                } else {
                    //10%
                    $codigoCuentaIva = $regimenCuentas->getCtaIva10v();
                }
                $cuentaIva = BackCacher::getCuenta($codigoCuentaIva);
                if ($cuentaIva==false) {
                    throw new SociosException('La cuenta de contabilización ('.$codigoCuentaIva.') del IVA del '.$cargosFijos->getPorcentajeIva().'%  de Ventas en Regimen Cuentas configurada en el comprobante de facturación no existe ('.$codigoCuentaIva.')');
                } else {
                    if ($cuentaIva->getEsAuxiliar()!='S') {
                        throw new SociosException('La cuenta de contabilización auxiliar ('.$codigoCuentaIva.') del IVA del '.$cargosFijos->getPorcentajeIva().'%  de Ventas en Regimen Cuentas configurada en el comprobante de facturación no es auxiliar ('.$codigoCuentaIva.')');
                    }
                }
            } else {
                throw new SociosException('La facturación no está soportada para IVA del '.$cargosFijos->getPorcentajeIva().'%');
            }
        } else {
            $baseIva = LocaleMath::round($precios[$n]*$cantidades[$n], 0);
            $iva = 0;
        }
        
        
        if ($cargosFijos->getIngresoTercero()=='N') {

            if ($cargosFijos->getPorcentajeIva()>0) {
        
                if (!isset($resumenVenta[16])) {
                    $resumenVenta[16] = 0;
                }
                $resumenVenta[16]+=$baseIva;
                
            } else {

                if (!isset($resumenVenta[0])) {
                    $resumenVenta[0]=0;
                }
                $resumenVenta[0]+=$baseIva;

            }

            
        } else {

            if (!isset($resumenVenta[10])) {
                $resumenVenta[10]=0;
            }
            $resumenVenta[10]+=$baseIva;

        }
        
        
        
        if (!isset($resumenIva[16])) {
            $resumenIva[16] = 0;
        }
        $resumenIva[16]+=$iva;

        $detalles[] = array(
            'Item'             => $item,
            'Descripcion'     => $cargosFijos->getNombre(),
            'Cantidad'         => $cantidades[$n],
            'Descuento'     => $descuentos[$n],
            'CuentaVenta'     => $cargosFijos->getCuentaContable(),
            'BaseIva'         => $baseIva,
            'PorcentajeIva' => $cargosFijos->getPorcentajeIva(),
            'CuentaIva'     => $codigoCuentaIva,
            'Iva'             => $iva,
            'Valor'         => $precios[$n],
            'cargosFijos'    => $cargosFijos
        );

        $totalFactura += LocaleMath::round($precios[$n]*$cantidades[$n], 0) + $iva;

        if (!count($detalles)) {
            throw new SociosException('Agregue primero cargos a la factura antes de generarla.');
        }

        $options['totalFactura'] = $totalFactura;
        $options['resumenVenta'] = $resumenVenta;
        $options['resumenIva'] = $resumenIva;
        $options['detalles'] = $detalles;
        $options['baseIva'] = $baseIva;
        $options['iva'] = $iva;
        
    }

    /**
    * Add a Factura
    *
    * @param array $options
    * @return $options
    */
    private function _addFactura($options)
    {

        try
        {

            $socios = $options['socios'];
            $facturaSO = $options['factura'];
            $resumenIva = $options['resumenIva'];
            $resumenIco = $options['resumenIco'];
            $codigoComprob = $options['codigoComprob'];
            $auraConsecutivo = $options['auraConsecutivo'];

            if (!isset($options['consecutivos'])) {
                throw new SociosException("No se ha definido el consecutivo de Invoicer");
            }
            $consecutivosId = $options['consecutivos'];
            $consecutivo = EntityManager::get('Consecutivos')->setTransaction($this->_transaction)->findFirst($consecutivosId);
            
            $resumenVenta = $options['resumenVenta'];
            $detalleFacturaSO = $options['detalleFacturasObj'];
            $detalleMovimientoSO = $options['detalleMovimiento'];

            $nombreCompleto = $socios['nombres'].' '.$socios['apellidos'].' / '.$socios['numero_accion'];
            
            //buscamos si existe invoicer
            $factura = EntityManager::get('Invoicer')->setTransaction($this->_transaction)->findFirst("numero='{$facturaSO['numero']}'");
            if (!$factura) {
                $factura = EntityManager::get('Invoicer', true)->setTransaction($this->_transaction);
            }

            $factura->setConsecutivosId($consecutivo->getId());
            $factura->setPrefijo($consecutivo->getPrefijo());
            $factura->setNumero($facturaSO['numero']);
            $factura->setResolucion($consecutivo->getResolucion());
            $factura->setFechaResolucion((string)$consecutivo->getFechaResolucion());
            $factura->setNumeroInicial($consecutivo->getNumeroInicial());
            $factura->setNumeroFinal($consecutivo->getNumeroFinal());
            $factura->setNit($options['nitDocumento']);
            $factura->setNombre($nombreCompleto);
            $factura->setDireccion($socios['direccion_casa']);
            $factura->setNitEntregar($options['nitDocumento']);
            $factura->setNombreEntregar($nombreCompleto);
            $factura->setDireccionEntregar($socios['direccion_casa']);
            $factura->setFechaEmision($facturaSO['fecha_factura']);
            $factura->setFechaVencimiento($facturaSO['fecha_vencimiento']);
            $factura->setNotaFactura($consecutivo->getNotaFactura());
            $factura->setNotaIca($consecutivo->getNotaIca());
            $factura->setVenta16($resumenVenta['16']);
            $factura->setVenta10($resumenVenta['10']);
            $factura->setVenta0($resumenVenta['0']);
            $factura->setIva16($resumenIva['16']);
            $factura->setTotalIco($resumenIco['8']);
            $factura->setIva10($resumenIva['10']);
            $factura->setIva0($resumenIva['0']);
            $factura->setPagos($facturaSO['total_factura']);
            $factura->setTotal($facturaSO['total_factura']);
            
            //$factura->setComprobInve('');
            //$factura->setNumeroInve('');
            
            $factura->setComprobContab($codigoComprob);
            $factura->setNumeroContab($auraConsecutivo);
            $factura->setEstado('A');
            if ($factura->save()==false) {
                foreach ($factura->getMessages() as $message) {
                    throw new Exception('Factura: '.$message->getMessage());
                }
            }

            //Borramos Detalle Invoicer por si hay basura
            $this->_cleanDetalleInvoicer($factura->getId());

            foreach ($detalleMovimientoSO as $detalleMovimiento) {
                $facturaDetalle = EntityManager::get('DetalleInvoicer', true)->setTransaction($this->_transaction);
                $facturaDetalle->setFacturasId($factura->getId());
                $facturaDetalle->setItem($detalleMovimiento['cargos_fijos_id']);
                $facturaDetalle->setDescripcion($detalleMovimiento['descripcion']);
                $facturaDetalle->setCantidad($detalleMovimiento['cantidad']);
                $facturaDetalle->setDescuento($detalleMovimiento['descuento']);
                $facturaDetalle->setValor($detalleMovimiento['valor']);
                $facturaDetalle->setIva($detalleMovimiento['iva']);
                $facturaDetalle->setIco($detalleMovimiento['ico']);
                $total = ($detalleMovimiento['valor']+$detalleMovimiento['iva']+$detalleMovimiento['ico']);
                $facturaDetalle->setTotal($total);
                if ($facturaDetalle->save()==false) {
                    foreach ($facturaDetalle->getMessages() as $message) {
                        throw new Exception('Invoicer-Detalle: '.$message->getMessage().print_r($detalle, true));
                    }
                }
                unset($detalleMovimiento,$facturaDetalle,$total);
            }
            unset($detalleMovimientoSO);
            
            $facturaData = array();
            foreach ($factura->getAttributes() as $field) {
                $facturaData[$field] = $factura->readAttribute($field);
                unset($field);
            }
            $options['facturas'] = $facturaData;
            $options['facturasId'] = $factura->getId();

            unset($socios, $facturaSO, $resumenIva, $consecutivo, $resumenVenta, $detalleFacturaSO, $nombreCompleto);
            return $options;
        }
        catch(Exception $e) {
            throw new Exception("_addFactura: ".$e->getMessage());
        }
    }

    /**
    * Agrega cuota del prestamo a factura
    *
    * @param array $options(
    *    'prestamosSocios',
    *    'amortizacion',
    *    'cargoMes',
    *    'salAntNeto',
    *    'salAntInteres',
    * )
    * @return $financiacionId
    */
    private function _addPrestamo($options)
    {

        $this->_transaction = TransactionManager::getUserTransaction();

        
        try
        {
            $prestamoArray = $options['prestamo'];

            #throw new Exception(print_r($prestamoArray,true));
            $nitEntregarDocumento     = $options['nitDocumento'];
            $facturaId                 = $options['facturasId'];
        
            foreach ($prestamoArray as $prestamo) {

                if (isset($prestamo['total']) && $prestamo['total']) {

                    $financiacion = EntityManager::get('Financiacion', true)->setTransaction($this->_transaction);
                    $financiacion->setFacturaId($facturaId);
                    $financiacion->setDescripcion($prestamo['descripcion']);
                    $financiacion->setValor($prestamo['valor']);
                    $financiacion->setMora($prestamo['mora']);
                    $financiacion->setTotal($prestamo['total']);
                    
                    if ($financiacion->save()==false) {
                        foreach ($financiacion->getMessages() as $message) {
                            throw new SociosException('addPrestamo: '.$message->getMessage());
                        }
                    }

                }
                unset($prestamo,$financiacion);
            }
            unset($prestamoArray,$nitEntregarDocumento,$facturaId);
        }
        catch(Exception $e) {
            throw new Exception('_addPrestamo: '.$e->getMessage());
        }
        
    }

    /**
    * Disable a Invoicer
    *
    * @param array $options(
    *    'facturas'            => array(int,int,....)
    *)
    */
    public function disableInvoicer(&$options)
    {
        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();

            if (isset($options['facturas'])==false) {
                throw new SociosException('Agregue primero id de facturas en anular en index facturas');
            }
            $facturasArray = $options['facturas'];
            
            if ($facturasArray && is_array($facturasArray)==true && count($facturasArray)>0) {
                $inWhere = implode(',', $facturasArray);
                if ($inWhere) {
                    $andWhere = '';
                    //throw new Exception(print_r($options,true));
                    
                    if (isset($options['nits']) && count($options['nits'])) {
                        $andWhere = ' OR (nit IN("'.implode('","', $options['nits']).'") AND fecha_emision="'.$options['fechaFactura'].'")';
                    }
                    $whereStr = 'id IN('.$inWhere.')'.$andWhere;
                    //throw new Exception($whereStr);
                    
                    $facturasObj = EntityManager::get('Invoicer')->setTransaction($this->_transaction)->deleteAll($whereStr);
                }
            }
            return true;
        }
        catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

    }


    /**
    * Add a Invoicer
    *
    * @param array $options(
    *    'items'                 => array
    *    'precios'                 => array
    *    'cantidades'             => array
    *    'descuentos'             => array
    *    'formasPago'             => array
    *    'valoresFormas'         => array
    *    'nitDocumento'             => string
    *    'nitEntregarDocumento'     => string
    *    'fechaVencimiento'         => date
    *    'fechaFactura'            => date
    *)
    */
    public function addInvoicer(&$options)
    {
        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();

            //validar invoicer
            $this->_validateInvoicer($options);

            $totalFactura   = 0;
            $detalles       = array();
            $movimiento     = array();
            $items          = $options['items'];

            $options['detalles'] = $detalles;
            $options['totalFactura'] = $totalFactura;

            //creamos factura
            $options = $this->_addFactura($options);

            //add prestamo
            $this->_addPrestamo($options);
        }
        catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
    * Obtiene el consecutivo actual de la tabla consecutivos de facturacion 
    * 
    * @param array $options
    * @return int $consecutivo
    */
    public function getConsecutivo(&$options)
    {

        try
        {
            $this->_transaction = TransactionManager::getUserTransaction();
            
            //Si deseo el consecutivo actual
            if (!isset($options['facturaId'])) {
                $periodo = SociosCore::getCurrentPeriodoObject();

                $consecutivos = EntityManager::get('Consecutivos')->setTransaction($this->_transaction)->findFirst($periodo->getConsecutivosId());
                $consecutivo = $consecutivos->getNumeroActual();
            } else {
                //Si deseo el consecutivo de una factura en especial
                $facturas = EntityManager::get('Invoicer')->setTransaction($this->_transaction)->findFirst($options['facturaId']);
                if ($facturas==false) {
                    throw new Exception("La factura no existe. '{$options['facturaId']}'");
                }
                $consecutivo = $facturas->getNumero();
            }
            return $consecutivo;
        }
        catch(Exception $e) {
            throw new Exception('getConsecutivo: '.$e->getMessage());
        }
    }

    /**
    * Cambia el consecutivo actual de la tabla consecutivos de facturacion 
    * 
    * @param array $options
    * @return int $consecutivo
    */
    public function setConsecutivoFactura($consecutivo)
    {

        try
        {
            $periodo = SociosCore::getCurrentPeriodoObject();

            $consecutivos = EntityManager::get('Consecutivos')->setTransaction($this->_transaction)->findFirst($periodo->getConsecutivosId());
            $consecutivos->setNumeroActual($consecutivo);
            if (!$consecutivos->save()) {
                foreach ($consecutivos->getMessages() as $message) {
                    throw new Exception('save: '.$message->getMessage());
                }
            }
            return $consecutivo;
        }
        catch(Exception $e) {
            throw new Exception('setConsecutivo: '.$e->getMessage());
        }
    }

    ///////////////////////
    // Saldos a favor
    ///////////////////////
    /*
    Se utilza en el cierre de periodo cuando un socio tiene un saldo a favor lo pasan a un cuenta diferent e de sostenimineto y luego al cerrar periodo pasa a otra cuenta para facturar
    */
    public function pasarSaldosAFavor()
    {
        $this->_transaction = TransactionManager::getUserTransaction();
        
        $periodo = SociosCore::getCurrentPeriodo();

        $ctaSaldoAFavor = SociosCore::getCuentaSaldoAFavor();
        $ctaPasarSaldoAFavor = SociosCore::getPasarCuentaSaldoAFavor();

        //validamos si las cuentas son aceptables para cartera
        SociosCore::checkCuentaCartera($ctaSaldoAFavor);
        SociosCore::checkCuentaCartera($ctaPasarSaldoAFavor);

        //validamos si se ha definido el comprobante de saldo a favor
        $comprobSaldoAFavor = SociosCore::checkComprobSaldoAFavor();

        try
        {
            //Buscamos saldos a favor en cartera de la cuenta de saldo a favor
            $carteraObj = EntityManager::get('Cartera')->setTransaction($this->_transaction)->find("cuenta='$ctaSaldoAFavor' and saldo<0");
    
            //Nota todos los abonos son debitos y debemos crear un credito para matarlo y hacer otro debito a la otra cuenta
            $movements = array();
            foreach ($carteraObj as $cartera) {

                $movi = EntityManager::get("Movi")->setTransaction($this->_transaction)->findFirst("cuenta='{$cartera->getCuenta()}' AND nit='{$cartera->getNit()}' AND tipo_doc='{$cartera->getTipoDoc()}' AND numero_doc='{$cartera->getNumeroDoc()}'");

                if (!$movi) {
                    throw new Exception("No existe movimiento del saldo en cartera. (cuenta='{$cartera->getCuenta()}' AND nit='{$cartera->getNit()}' AND tipo_doc='{$cartera->getTipoDoc()}' AND numero_doc='{$cartera->getNumeroDoc()}')");
                }

                $debCre = "D";
                if ($movi->getDebCre()=='D') {
                    $debCre = "C";
                }

                $movements[$cuentaContable] = array(
                    'Descripcion'    => "CIERRE PERIODO '$periodo' SALDO A FAVOR",
                    'Nit'            => $cartera->getNit(),
                    'CentroCosto'    => $cartera->getCentroCosto(),
                    'Cuenta'        => $cartera->getCuenta(),
                    'Valor'            => $cartera->getValor(),
                    'BaseGrab'        => 0,
                    'TipoDocumento' => $cartera->getTipoDoc(),
                    'NumeroDocumento' => $cartera->getNumeroDoc(),
                    'FechaVence'    => $cartera->getFVence(),
                    'DebCre'        => $debCre,
                    'debug'            => true
                );

                unset($cartera);
            }
            unset($carteraObj);

            //throw new Exception($periodo);
                
            if (count($movements)>0) {

                $datosClub = EntityManager::get("DatosClub")->setTransaction($this->_transaction)->findFirst();
                
                $aura = new Aura($comprobSaldoAFavor, 0, $datosClub->getFCierre());
                
                foreach ($movements as $movement) {
                    $aura->addMovement($movement);
                    unset($movement);
                }

                if ($aura->save()==true) {

                    //Registro de comprobante saldo a favor
                    $auraConsecutivo = $aura->getConsecutivo($comprobSaldoAFavor);

                    $saldoafavor = EntityManager::get("Saldoafavor", true);
                    $saldoafavor->setTransaction($this->_transaction);
                    $saldoafavor->setPeriodo($periodo);
                    $saldoafavor->setComprob($comprobSaldoAFavor);
                    $saldoafavor->setNumero($auraConsecutivo);

                    if ($saldoafavor->save()==false) {
                        foreach ($saldoafavor->getMessages() as $message) {
                            throw new SociosException('save: '.$message->getMessage());
                        }
                    }

                }

            }
            unset($movements);
                
        }
        catch (SociosException $e) {
            throw new Exception('SaldoAFavorAura: '.$e->getMessage());
        }
    }


    /**
    * Borra las facturas de invoicer
    */
    public function _cleanInvoicer($config)
    {
        if (!isset($config['fechaFactura']) && $config['fechaFactura']) {
            throw new SociosException("Es necesaria la fecha de factura para borrar de invoicer");
        }
        if (!isset($config['facturaNumero']) && $config['facturaNumero']>0) {
            throw new SociosException("Es necesario el número de la factura para borrar de invoicer");
        }
        
        //Buscamos facturas de invoicer con el mismo numero de la factura de socios
        $invoicerObj = EntityManager::get('Invoicer')->setTransaction($this->_transaction)->find("numero='{$config['facturaNumero']}'");
        foreach ($invoicerObj as $invoicer) {
            //Borrar detalle de invoicer
            $this->_cleanDetalleInvoicer($invoicer->getId());

            //borramos invoicer
            $status = $invoicer->setTransaction($this->_transaction)->delete();
            if (!$status) {
                foreach ($invoicer->getMessages() as $message) {
                    throw new SociosException("Invoicer: ".$message->getMessage());
                }
            }

            unset($invoicer);
        }
        unset($invoicerObj);

        return true;
    }

    public function _cleanDetalleInvoicer($facturasId)
    {
        //buscamos el detalle del invoicer de la factura
        $detalleInvoicerObj = EntityManager::get('DetalleInvoicer')->setTransaction($this->_transaction)->find("facturas_id='$facturasId'");
        foreach ($detalleInvoicerObj as $detalleInvoicer) {
            //Borramos detalle
            $status2 = $detalleInvoicer->setTransaction($this->_transaction)->delete();
            if (!$status2) {
                foreach ($detalleInvoicer->getMessages() as $message) {
                    throw new SociosException("DetalleInvoicer: ".$message->getMessage());
                }
            }
            unset($detalleInvoicer);
        }
        unset($detalleInvoicerObj);

        return true;
    }

    /**
    * limpia el detalle de la factura a partir de su ID
    */
    public function limpiarDetalleFactura($facturaId)
    {
        if (!$facturaId) {
            throw new SociosException("Debe indicar el id de la factura a borrar su detalle");
        }

        $detalleFacturaObj = EntityManager::get('DetalleFactura')->setTransaction($this->_transaction)->find("factura_id='$facturaId'");
        foreach ($detalleFacturaObj as $detalleFactura) {
            $detalleFactura->setTransaction($this->_transaction);
            $detalleFactura->delete();
            
            unset($detalleFactura);
        }
        unset($detalleFacturaObj);
        
        return true;
    }
}
