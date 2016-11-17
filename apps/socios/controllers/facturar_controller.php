<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

Core::importFromLibrary('Hfos/Socios','SociosCore.php');
Core::importFromLibrary('Hfos/Socios','SociosReports.php');
Core::importFromLibrary('Hfos/Socios','SociosEstadoCuenta.php');

/**
 * FacturarController
 *
 * Controlador de generacion de facturas mensuales
 *
 */
class FacturarController extends HyperFormController
{
	static protected $_config = array(
		'model' => 'Invoicer',
		'plural' => 'facturas generadas',
		'single' => 'factura Generada',
		'genre' => 'F',
		'tabName' => 'Invoicer',
		'preferedOrder' => 'numero DESC',
		'icon' => 'cheque.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'id' => array(
				'primary' => true,
				'single' => 'Código',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'primary' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'consecutivos_id' => array(
				'single' => 'Consecutivo',
				'type' => 'relation',
				'relation' => 'Consecutivos',
				'fieldRelation' => 'id',
				'detail' => 'detalle',
				'filters' => array('int')
			),
			'prefijo' => array(
				'single' => 'Prefijo',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('upper')
			),
			'numero' => array(
				'single' => 'Número',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'filters' => array('int')
			),
			'nit' => array(
				'single' => 'Tercero',
				'type' => 'tercero',
				'size' => 10,
				'maxlength' => 14,
				'notNull' => true,
				'filters' => array('terceros')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'direccion' => array(
				'single' => 'Dirección',
				'type' => 'text',
				'size' => 25,
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'fecha_emision' => array(
				'single' => 'Fecha de Emisión',
				'type' => 'date',
				'default' => '',
				//'notBrowse' => true,
				//'notSearch' => true,
				'filters' => array('date')
			),
			'fecha_vencimiento' => array(
				'single' => 'Fecha de Entrega',
				'type' => 'date',
				'default' => '',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('date')
			),
			'nota_factura' => array(
				'single' => 'Nota de Factura',
				'type' => 'textarea',
				'rows' => 2,
				'cols' => 40,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags')
			),
			'nota_ica' => array(
				'single' => 'Nota de Ica',
				'type' => 'textarea',
				'rows' => 2,
				'cols' => 40,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags')
			),
			'venta16' => array(
				'single' => 'Base Gravable',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notNull' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int')
			),
			'venta10' => array(
				'single' => 'Ingresos a Terceros',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notNull' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int')
			),
			'venta10' => array(
				'single' => 'Base No Gravable',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notNull' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int')
			),
			'iva16' => array(
				'single' => 'Iva 16',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notNull' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int')
			),
			'comprob_contab' => array(
				'single' => 'Comprobante Contable',
				'type' => 'comprob',
				'size' => 10,
				'maxlength' => 14,
				'notNull' => true,
				'readOnly' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('comprob')
			),
			'numero_contab' => array(
				'single' => 'Número de Comprobante Contable',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'readOnly' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'Activa',
					'I' => 'Inactiva'
				),
				'filters' => array('onechar')
			)
		),
		'detail' => array(
			//invoicer(id) a -> detalle invoicer(facturas_id)
			'relation' => array('id'=>'facturas_id'),
			'model' => 'DetalleInvoicer',
			'tabName' => 'Detalle',
			'fields' => array(
				'item' => array(
					'single' => 'Cargo Fijo',
					'type' => 'text',
					'notNull' => true,
					'size' => 7,
					'maxlength' => 15,
					'filters' => array('upper')
				),
				'descripcion' => array(
					'single' => 'Descripción',
					'type' => 'text',
					'notNull' => true,
					'size' => 46,
					'maxlength' => 250,
					'filters' => array('upper')
				),
				'valor' => array(
					'single' => 'Valor',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 12,
					'maxlength' => 15,
					'filters' => array('float')
				),
				'iva' => array(
					'single' => 'Iva',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 12,
					'maxlength' => 15,
					'filters' => array('float')
				),
				'total' => array(
					'single' => 'Total',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 12,
					'maxlength' => 15,
					'filters' => array('float')
				),
			),
			'keys' => array(
				'unique_index' => array(
					'item'
				)
			)
		),
	);

	public function initialize()
	{
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	/**
	 * Metodo que visualiza el formato a imprimir
	 */
	public function getFormatoAction()
	{
		$this->setResponse('view');
		$controller = $this->getControllerName();
		$this->setParamToView('controller', $controller);
		$controllerRequest = ControllerRequest::getInstance();
		$sociosId = $controllerRequest->getParamPost('sociosId', 'int');
		if ($sociosId>0) {
			$this->setParamToView('sociosId', $sociosId);
		}
	}

	public function erroresAction()
	{

	}

	/**
	 * Generar facturas mensuales a socios
	 *
	 */
	public function generarAction()
	{
		$this->setResponse('json');

		set_time_limit(0);

		try {

			$debug=true;
			$transaction = TransactionManager::getUserTransaction();

			//periodo actual
			$periodo = SociosCore::getCurrentPeriodo();

			//variables de generación de facturas
			$fechaFactura = $this->getPostParam('dateIni','date');
			$fechaVencimiento = $this->getPostParam('dateFin','date');
			$sostenimiento = $this->getPostParam('sostenimiento');
			$administracion = $this->getPostParam('administracion');
			$novedades = $this->getPostParam('novedades');
			$consumoMinimo = $this->getPostParam('consumoMinimo');
			$interesesMora = $this->getPostParam('interesesMora');
			$ajusteSostenimiento = $this->getPostParam('ajusteSostenimiento');


			$sociosFactura = new SociosFactura();
			SociosEstadoCuenta::checkPeriod($periodo);

			//Recalculamos movimientos
			$configMovi = array(
				'periodo' => $periodo,
				'fechaFactura' => $fechaFactura,
				'fechaVencimiento' => $fechaVencimiento,
				'g_sostenimiento' => $sostenimiento,
				'g_administracion' => $administracion,
				'g_novedades' => $novedades,
				'g_consumoMinimo' => $consumoMinimo,
				'g_interesesMora' => $interesesMora,
				'g_ajusteSostenimiento' => $ajusteSostenimiento
			);

			//Agregamos a configuracion datos estaticos de configuracion
			$sociosFactura->addConfigDefault($configMovi);

			//Borramos todo moviineto de esa fecha asi se evitan socios que se inactivas y quedo basura.
			$sociosFactura->cleanMovimientoSocios($configMovi);

			//preparamos movimientos a factura
			$sociosFactura->generarCargosSocios($configMovi);
			$sociosFactura->generarMovimiento($configMovi);

			//Buscamos socios
			$sociosObj = $this->Socios->find(array(
				"columns"=>'socios_id',
				'order'=>'CAST(numero_accion AS SIGNED) ASC'
			));
			foreach ($sociosObj as $socios)
			{

				$sociosId = $socios->getSociosId();

				//Crea la(s) factura(s)
				$configFactura = array(
					'periodo'	=> $periodo,
					'sociosId'	=> $sociosId,
					'fechaFactura' => $fechaFactura,
					'fechaVencimiento' => $fechaVencimiento,
					'g_sostenimiento' => $sostenimiento,
					'g_administracion' => $administracion,
					'g_novedades' => $novedades,
					'g_consumoMinimo' => $consumoMinimo,
					'g_interesesMora' => $interesesMora
				);

				$sociosFactura->generarFactura($configFactura);

				unset($socios);
			}

			unset($sociosObj);

			$transaction->commit();

			return array(
				'status' => 'OK',
				'message' => 'La facturación fue generada exitosamente en la fecha "'.$fechaFactura.'".'
			);

		}
		catch(SociosException $e) {
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
		catch(Exception $e) {
			return array(
				'status'	=> 'FAILED',
				'message'	=> "generarAction: " . $e->getMessage()
			);
		}
	}

	/**
	 * Metodo que imprime la(s) factura(s)
	 */
	public function reporteFacturaAction()
	{
		$this->setResponse('json');

		set_time_limit(0);

		try
		{
			$transaction = TransactionManager::getUserTransaction();

			$sociosId = $this->getPostParam('sociosId', 'int');
			$fechaFactura = $this->getPostParam('dateIni','date');

			if (!$fechaFactura) {
				return array(
					'status' => 'FAILED',
					'message' => 'Es necesario dar la fecha de la factura'
				);
			}

			$config = array(
				'reportType' => 'pdf',
				'fechaFactura' => $fechaFactura,
			);

			//Generamos factura
			SociosReports::factura($config, $transaction);
			return array(
				'status'	=> 'OK',
				'file'		=> $config['file'],
				'message' 	=> 'Se genero correctamente las facturas del periodo'
			);
		}
		catch(SociosException $e) {
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
		catch(Exception $e) {
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
	}

	/**
	 * Recorre facturas generadas en el periodo actual y borra su movimiento contable
	 */
	public function borrarAction()
	{
		$this->setResponse('json');

		set_time_limit(0);

		try
		{

			$transaction = TransactionManager::getUserTransaction();

			//fecha de factura
			$fechaFactura = $this->getPostParam('dateIni','date');
			$date = new Date($fechaFactura);
			//periodo actual
                        $periodo = SociosCore::getCurrentPeriodo();

			if (intval($date->getPeriod())<intval($periodo)) {
				throw new SociosException("No se puede borrar periodos ya cerrados");
			}

			$sociosIdArray = array();
			$nitsArray = array();
			$facturaObj = EntityManager::get("Factura")->find(array("conditions"=>"fecha_factura='$fechaFactura'",'columns'=>'socios_id'));
			foreach ($facturaObj as $factura)
			{
				$socio = BackCacher::getSocios($factura->getSociosId());
				if ($socio) {
					$sociosIdArray[]=$factura->getSociosId();
					$nitsArray[]=$socio->getIdentificacion();
				}
				unset($socio,$factura);
			}
			unset($facturaObj);

			$config = array(
				'nits'		=> $nitsArray,
				'facturas' 	=> $sociosIdArray,
				'fechaFactura' 	=> $fechaFactura,
				'showDebug' => true
			);

			$sociosFactura = new SociosFactura();
			$status = $sociosFactura->anularFacturasPeriodo($config);

			$transaction->commit();

			return array(
				'status' => 'OK',
				'message' => 'Se borraron las facturas de la fecha "'.$fechaFactura.'" con su movimiento contable.'
			);
		}
		catch(SociosException $e) {
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
		catch(Exception $e) {
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
	}

	/**
	* Consulta las facturas a enviar por correo
	*/
	public function sendCorreoAction()
	{
		$this->setResponse('view');

		try
		{
    	    Core::importFromLibrary('Hfos/Socios','SociosCore.php');
			Core::importFromLibrary('Hfos/Socios','SociosReports.php');

			$periodo = SociosCore::getCurrentPeriodo();

			//Buscamos Facturas y comparamos si existe en delivery
			$sociosReports = new SociosReports();
			$sociosReports->checkDelivery();

			//Buscamos las Facturas pendientes por enviar pendientes
			$deliveryObj = $this->Delivery->find("estado='P' AND periodo='$periodo'");
            $periodoObj = $this->Periodo->find();

			$this->setParamToView('deliveryObj',$deliveryObj);
			$this->setParamToView('periodo',$periodo);
            $this->setParamToView('periodoObj',$periodoObj);
		} catch(Exception $e) {
            Flash::error($e->getMessage());
        }
	}

    /**
     * Muestra un html con el listado de facturas y sus correos a enviar
     * @param int $periodoAct
     */
    public function showFacturasToSendAction($periodoAct=0)
    {
        $this->setResponse('view');

        try {
            //$periodoAct = $this->getRequestParam("periodoAct", "int");
            if (!$periodoAct) {
                echo Flash::warning("No se ha definido el periodo a mostrar");
            }
            $periodo = SociosCore::getCurrentPeriodoObject($periodoAct);
            if (!$periodo) {
                echo Flash::warning("No existe un periodo creado en Periodos '$periodoAct'");
            }
            $facturaObj = EntityManager::get('Factura')->find("periodo='$periodoAct'");
            if (!count($facturaObj)) {
                throw new SociosException("No existen facturas en el periodo '$periodoAct'");
            }

            $template = file_get_contents(KEF_ABS_PATH . "/apps/socios/views/facturar/listToSend.html");

            $i=0;
            $content = "";
            foreach ($facturaObj as $factura)
            {
                $i++;
                $numfac = $factura->getNumero();
                $socios = BackCacher::getSocios($factura->getSociosId());
                if (!$socios) {
                    $content .= Flash::error("No existe el socio con id ".$factura->getSociosId());
                    continue;
                }
                //Correos
                if (!$socios->getCorreo1()) {
                    continue;
                }
                $correo = array();
                $correo[$socios->getCorreo1()] = $socios->getCorreo1();
                if ($socios->getCorreo2()) {
                    $correo[$socios->getCorreo2()] = $socios->getCorreo2();
                }
                if ($socios->getCorreo3()) {
                    $correo[$socios->getCorreo3()] = $socios->getCorreo3();
                }
                $content .= '<tr>
					<td align="right">' . $i . '</td>
					<td align="left">' . $socios->getNumeroAccion() . '/' . $socios->getNombres() . ' ' . $socios->getApellidos() . '</td>
					<td align="center">' . $numfac . '</td>
					<td align="center">' . implode(',<br>',$correo) . '</td>
					<td align="center"><input type="checkbox" name="numfac[]" class="mailCheck" value="' . $numfac . '" checked></td>
				</tr>';

                unset($numfac, $correo, $factura, $socios);
            }
            unset($facturaObj);

            $template = str_replace("[__contentToSend__]", $content, $template);

            echo $template;

        } catch (Exception $e) {
            echo Flash::error($e->getMessage());
        }
    }

	/**
	* Consulta las facturas a enviar por correo
	*/
	public function sendDeliveryAction()
	{
		$this->setResponse('view');

		try
		{
			$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
			if (!isset($config->hfos->socios_ip)) {
				throw new Exception('No se ha configurado en config.ini la ip del servidor de socios');
			}

			$server = $config->hfos->socios_ip;

			$configSend = Settings::get('send_factura', 'SO');
			if (!$configSend) {
				throw new Exception("No se ha configurado el 'Resumen de Correo de Factura' en configuración para enviar al correo del socio.");
			}

			$numfacArray = $this->getPostParam('numfac', 'int');
			if (!count($numfacArray)) {
				throw new Exception("Debe elegir facturas a enviar", 1);
			}

            //throw new Exception(count($numfacArray));

            $transaction = TransactionManager::getUserTransaction();

			Core::importFromLibrary('Hfos/Socios','SociosReports.php');
			Core::importFromLibrary('Hfos/Delivery','Delivery.php');

			$periodo = SociosCore::getCurrentPeriodo();
			$sociosReports = new SociosReports();
			$delivery = new DeliveryHfos();

			//$temp = array($numfacArray[0]);

			//foreach ($temp as $numfac)
			foreach ($numfacArray as $numfac)
			{
				$factura = EntityManager::get('Factura')->findFirst("numero='$numfac'");
				if (!$factura) {
					throw new Exception("La factura Nro. '$numfac' no existe", 1);
				}
				$socios = BackCacher::getSocios($factura->getSociosId());
				if (!$socios) {
					throw new Exception("El socio con id '{$factura->getSociosId()}' no existe", 1);
				}

				if (!$socios->getCorreo1()) {
					continue;
				}

				$config = array('SociosId' => $factura->getSociosId(), 'periodo' => $periodo, 'fechaFactura' => $factura->getFechaFactura());
				$filePdf = $sociosReports->factura($config, $transaction);
				$message = $configSend.' Descargalo <a href="http://'.$server.'/s/public/temp/'.$filePdf.'">aqui</a>';
                //throw new Exception(print_r($config, true));
				$status = $delivery->send(
					0,
					array($socios->getCorreo1(), $socios->getNombres().' '.$socios->getApellidos()),
					'Factura Periodo '.$periodo,
					$message, $from='cartera@clubpayande.com',
					array('attachments' => array(KEF_ABS_PATH.'/public/temp/'.$filePdf))
				);

				if (!$status) {
					throw new Exception('Factura no enviada: '.$delivery->getLastError());
				}

				if ($socios->getCorreo2()) {
					$status = $delivery->send(
						0,
						array($socios->getCorreo2(), $socios->getNombres().' '.$socios->getApellidos()),
						'Factura Periodo '.$periodo,
						$message, $from='cartera@clubpayande.com',
						array('attachments' => array(KEF_ABS_PATH.'/public/temp/'.$filePdf))
					);
				}

				//$status = $delivery->send(0, array('carvajaldiazeduar@gmail.com', 'Eduar Carvajal'), 'Factura Periodo '.$periodo, 'Estimado Socios, estamos enviando un link de su factura a su correo. Link: http://'.$server.'/s/public/temp/'.$filePdf, $from='cartera@clubpayande.com', $extra);
				if (!$status) {
					throw new Exception('Factura no enviada: '.$delivery->getLastError());
				}
			}
			$transaction->commit();
			echo Flash::success('Las facturas fueron enviadas');
		}
		catch(Exception $e) {
			Flash::error($e->getMessage());
		}
	}

	/**
	* Selecciona las fecha a generar la factura
	*/
	public function selectperiodoAction($personal=false)
	{
		$this->setResponse('view');
		$perodoActual = SociosCore::getCurrentPeriodo();

		$year = substr($perodoActual, 0, 4);
		$month = substr($perodoActual, 4, 2);

		$dateIni = "$year-$month-01";
		$dateIniObj = new Date($dateIni);
		$dateIniObj->toLastDayOfMonth();
		$dateFinObj = clone $dateIniObj;
		$dateFinObj->addDays(15);

		Tag::displayTo('dateIni',$dateIniObj->getDate());
		Tag::displayTo('dateFin',$dateFinObj->getDate());

		//Personal
		$this->setParamToView('personal', $personal);
	}

	/**
	* Selecciona las fecha de facturas a borrar/imprimir
	*/
	public function selectfechaAction($type=false,$personal=false)
	{
		$this->setResponse('view');

		$facturasObj = EntityManager::get('Factura')->find(array("conditions"=>"1=1","columns"=>"fecha_factura","group"=>"fecha_factura",'order'=>'fecha_factura DESC'));

		$this->setParamToView('facturasObj',$facturasObj);

		$title = "????";
		switch ($type) {
			case 'P':
				$title = "Selecion de fecha a imprimir todas las facturas";
				break;
			case 'D':
				$title = "Selecion de fecha a borrar todas las facturas";
				break;
			case 'G':
				$title = "Selecion de fecha a generar cargos mensuales de todas las facturas a generar";
				break;
		}

		$this->setParamToView('title',$title);

		//Personal
		$this->setParamToView('personal', $personal);
	}

	public function afterInsert($transaction, $record)
	{
		return $this->_saveDetail($transaction, $record);
	}

	public function afterUpdate($transaction, $record)
	{
		return $this->_saveDetail($transaction, $record);
	}

	public function _saveDetail($transaction, $record)
	{
		try
		{
			$request = $this->getRequestInstance();

			if($request->isSetRequestParam('item')){
				$item = $request->getParamPost('item', 'alpha');
			} else {
				$item = array();
			}
			if($request->isSetRequestParam('descripcion')){
				$descripcion = $request->getParamPost('descripcion', 'upper');
			} else {
				$descripcion = array();
			}
			if($request->isSetRequestParam('valor')){
				$valor = $request->getParamPost('valor', 'float');
			} else {
				$valor = array();
			}
			if($request->isSetRequestParam('iva')){
				$iva = $request->getParamPost('iva', 'double');
			} else {
				$iva = array();
			}
			if($request->isSetRequestParam('action')){
				$action = $request->getParamPost('action', 'alpha');
			} else {
				$action = array();
			}

			$addDetail = array();
			$removeDetail = array();

			$detalleInvoicer = EntityManager::get('DetalleInvoicer')->setTransaction($transaction)->deleteAll("facturas_id='{$record->getId()}'");

			for ($i=0;$i<count($item);$i++)
			{
				if (isset($action[$i])) {
					if ($action[$i]=='add') {


						$detalleInvoicer = new DetalleInvoicer();
						$detalleInvoicer->setTransaction($transaction);
						$detalleInvoicer->setFacturasId($record->getId());
						$detalleInvoicer->setItem($item[$i]);
						$detalleInvoicer->setDescripcion($descripcion[$i]);
						$detalleInvoicer->setCantidad(1);
						$detalleInvoicer->setDescuento(0);
						$detalleInvoicer->setValor($valor[$i]);
						$detalleInvoicer->setIva($iva[$i]);
						$detalleInvoicer->setTotal($valor[$i]+$iva[$i]);

						if(!$detalleInvoicer->save()) {
							foreach ($detalleInvoicer->getMessages() as $msg)
							{
								throw new Exception("detalleInvoicer:".$msg->getMessage());
							}
						}
					}
				}
			}

			return true;
		}
		catch (Exception $e) {
			$this->appendMessage($e->getMessage());
			return false;
		}

	}

	public function beforeDelete() {
		$this->setResponse('json');
		return 'No se puede borrar una factura por este medio';
	}
}
