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
Core::importFromLibrary('Hfos/Socios', 'SociosFactura.php');
/**
 * SociosReports
 *
 * Clase central que controla procesos internos de reportes de Socios
 *
 */
class SociosReports extends UserComponent
{

    /**
    * @var ActiveRecordTransaction
    */
    private $_transaction;

    /**
     * Lista de bloques de texto a remplazar en templates 
     * @var array
     */
    private $_listaBloques = array();

    /**
     * Constructor de clase
     */
    public function __construct()
    {
        $this->_transaction = TransactionManager::getUserTransaction();
    }

    /**
     * Obtiene la información de un prestamo para ser representado en factura
     * @param  [type] $config      [description]
     * @param  [type] $transaction [description]
     * @return void
     */
    public static function getPrestamoData(&$config, $transaction)
    {
        if (isset($config['SociosId'])==false || $config['SociosId']<=0) {
            throw new SociosException('El id el socio es requerido');
        }

        $config['valorFinanciacion'] = 0.00;
        $config['valorCuota'] = 0.00;
        $prestamosSocios = EntityManager::get('PrestamosSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$config['SociosId'].' AND estado="D"'));
        if ($prestamosSocios!=false) {
            $config['valorFinanciacion'] = $prestamosSocios->getValorFinanciacion();
            //Buscamos estado de capital
            $amortizacion = EntityManager::get('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions'=>'prestamos_socios_id='.$prestamosSocios->getId().' AND estado="D"', 'order'=>'fecha_cuota ASC'));
            if ($amortizacion!=false) {
                $config['valorCuota'] = $amortizacion->getValor();
            }
        }
    }

    /**
     * Metodo que genera la factura mensual de socios(s)
     * @param  [type] $config      [description]
     * @param  [type] $transaction [description]
     * @return [type]              [description]
     */
    public static function factura(&$config, $transaction)
    {
        set_time_limit(0);

        try {
            Core::importFromLibrary('Hfos/Invoicing', 'InvoicingReport.php');
            Core::importFromLibrary('Mpdf', 'mpdf.php');
            Core::importFromLibrary('Hfos/Socios', 'SociosFactura.php');

            $options = array('apps'=>'SO', 'fechaFactura'=>$config['fechaFactura']);

            //Add default config
            $sociosFactura = new SociosFactura();
            $sociosFactura->addConfigDefault($options);

            if (isset($config['SociosId'])) {
                //facturas en el periodo de un socio
                $factura = EntityManager::get('factura')->findFirst(array('conditions'=> 'socios_id='.$config['SociosId']." AND fecha_factura='{$config['fechaFactura']}' AND estado<>'I'"));
                if ($factura==false) {
                    throw new SociosException('No existe una factura generada a el socio en la fecha "'.$config['fechaFactura'].'".');
                }
                $options['facturas'] = array($factura->getNumero());
                $options['fechaFactura'] = $factura->getFechaFactura();
            } else {
                //all in periodo
                $options['facturas'] = array();
                $conditions = array("columns"=>"numero, fecha_factura", 'conditions'=> "fecha_factura='{$config['fechaFactura']}' AND estado='D'");
                $facturaObj = EntityManager::get('factura')->find($conditions);
                foreach ($facturaObj as $factura) {
                    $options['facturas'][] = $factura->getNumero();
                    $options['fechaFactura'] = $factura->getFechaFactura();
                    unset($factura);
                }
                unset($facturaObj);
                //throw new Exception(count($options['facturas']));
            }
            if (isset($config['SociosId'])) {
                $socios = BackCacher::getSocios($config['SociosId']);
                $options['nit'] = $socios->getIdentificacion();
            }

            $sociosReports = new SociosReports();
            $fileName = $sociosReports->getInvoicerPrint($options);

            $config['file'] = 'public/temp/'.$fileName;
            //echo file_get_contents($config['file']);
            return $fileName;
        } catch (Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    /**
     * Genera el reporte de socios suspendidos en el period actual
     * @return string $fileName
     */
    public function getReportSuspendidos()
    {
        try {
            $this->_transaction = TransactionManager::getUserTransaction();

            $periodo = SociosCore::getCurrentPeriodo();

            $controllerRequest = ControllerRequest::getInstance();
            $reportType = $controllerRequest->getParamPost('reportType', 'alpha');
            $report = ReportBase::factory($reportType);

            $titulo = new ReportText('REPORTE DE SOCIOS SUSPENDIDOS', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo));

            $report->setDocumentTitle('REPORTE DE SOCIOS SUSPENDIDOS');
            $report->setColumnHeaders(array(
                'CODIGO',
                'NUMERO DE ACCION',
                'IDENTIFICACION',
                'NOMBRES',
                'APELLIDOS',
                'PERIODO'
            ));

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));

            $report->setColumnStyle(array(0, 1,2,3,4,5), new ReportStyle(array(
                'textAlign' => 'center',
                'fontSize' => 11
            )));

            $report->start(true);

            $suspendidosObj = EntityManager::get('Suspendidos')->setTransaction($this->_transaction)->find(array("periodo='$periodo'"));
            foreach ($suspendidosObj as $suspendidos) {
                $socios = BackCacher::getSocios($suspendidos->getSociosId());
                if ($socios!=false) {
                    $report->addRow(array(
                        $socios->getSociosId(),
                        $socios->getNumeroAccion(),
                        $socios->getIdentificacion(),
                        $socios->getNombres(),
                        $socios->getApellidos(),
                        $periodo
                    ));
                }
                unset($socios, $suspendidos);
            }
            unset($suspendidosObj);

            $report->finish();
            $fileName = $report->outputToFile('public/temp/proyeccion');

            return $fileName;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Genera un formato en Excel con los pagos a realizar segun saldos actuale sde cartera
     * @return string $fileName
     */
    public function formatoAjustePagos()
    {
        try {
            $this->_transaction = TransactionManager::getUserTransaction();

            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');

            $periodo = SociosCore::getCurrentPeriodo();

            $controllerRequest = ControllerRequest::getInstance();

            //$reportType = $controllerRequest->getParamPost('reportType', 'alpha');
            //$report = ReportBase::factory($reportType);

            $report = ReportBase::factory('excel');

            $report->setDocumentTitle('FORMATO DE AJUSTE PAGOS '.$periodo);
            $report->setColumnHeaders(array(
                'NUMERO DE ACCION',
                'IDENTIFICACION',
                'COMPROBANTE',
                'CUENTA',
                'FECHA',
                'VALOR',
                'NUMERO FACTURA'
            ));

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));

            $report->setColumnStyle(array(0,2,4,5), new ReportStyle(array(
                'textAlign' => 'left',
                'fontSize' => 11
            )));

            $report->setColumnStyle(array(1,3,5,6), new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11
            )));

            $report->start(true);

            $db = DbBase::rawConnect();

            $schema = '';
            $config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
            if (isset($config->hfos->back_db)) {
                $schema = $config->hfos->back_db;
            } else {
                $schema ='ramocol';
            }

            $schemaSocios = '';
            if (isset($config->hfos->socios)) {
                $schemaSocios = $config->hfos->socios;
            } else {
                $schemaSocios = 'hfos_socios';
            }

            $query = "
                select
                    s.numero_accion,s.identificacion, 'AJU',c.cuenta,
                    date_format(c.f_emision, '%m/%d/%Y') as fecha,c.saldo,
                    c.numero_doc
                from
                    $schemaSocios.socios as s
                    LEFT JOIN $schema.cartera as c
                        ON c.tipo_doc = 'CXS'
                        AND c.nit = s.identificacion
                        AND c.saldo > 0
                        AND c.cuenta IN (
                            select cuenta_consolidar from $schemaSocios.cargos_fijos
                            group by cuenta_consolidar
                        )
                    LEFT JOIN $schemaSocios.factura as f
                        ON YEAR(c.f_emision) = YEAR(f.fecha_factura)
                        AND MONTH(c.f_emision) = MONTH(f.fecha_factura)
                        AND f.socios_id = s.socios_id
                where
                    s.estados_socios_id = 1 AND c.saldo > 0
                order by
                    s.numero_accion,c.numero_doc,c.cuenta
            ";

            $listQuery = $db->query($query);
            while ($listQueryRow = $db->fetchArray($listQuery)) {
                if ($listQueryRow[0]) {
                    $report->addRow(array(
                        $listQueryRow[0],
                        $listQueryRow[1],
                        $listQueryRow[2],
                        $listQueryRow[3],
                        $listQueryRow[4],
                        $listQueryRow[5],
                        $listQueryRow[6]
                    ));
                }
            }

            $report->finish();
            $rand = rand();
            $fileName = $report->outputToFile('public/temp/formato_ajuste_pagos');

            return $fileName;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Verifica si las facturas del periodo actual estan en la tabla delivery para enviar a correos
     * @return void
     */
    public function checkDelivery()
    {
        try {
            Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');

            $periodo = SociosCore::getCurrentPeriodo();

            $transaction = TransactionManager::getUserTransaction();

            $deleteAll = EntityManager::get('Delivery')->setTransaction($transaction)->deleteAll("periodo='$periodo'");

            $sociosObj = EntityManager::get('Socios')->find('envia_correo="S"');
            foreach ($sociosObj as $socios) {
                $facturaObj = EntityManager::get('Factura')->find("socios_id='{$socios->getSociosId()}' AND periodo='$periodo'");
                foreach ($facturaObj as $factura) {
                    $delivery = EntityManager::get('Delivery')->setTransaction($transaction)->findFirst("numfac='{$factura->getNumero()}' AND periodo='$periodo'");
                    if ($delivery) {
                        continue;
                    }

                    $delivery = EntityManager::get('Delivery', true)->setTransaction($transaction);
                    $delivery->setNumfac($factura->getNumero());
                    $delivery->setPeriodo($periodo);
                    $delivery->setRelayKey('P');
                    $delivery->setFecha(date('Y-m-d'));
                    $delivery->setEstado('P');//Pendiente por enviar
                    if ($delivery->save() == false) {
                        foreach ($delivery->getMessages() as $message) {
                            throw new Exception("Delivery: ".$message->getMessage());
                        }
                    }

                    unset($factura);
                }
                unset($socios, $facturaObj);
            }
            unset($sociosObj);

            $transaction->commit();
        } catch (Exception $e) {
            throw new Exception("checkDelivery: ".$e->getMessage());
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////                                           FACTURA
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
    * Metodo que genera el encabezado del invocier
    *
    * @param array $options
    * @return string $html
    */
    private function _addHeader(&$options)
    {

        //variables
        $terceroFactura = $options['terceroFactura'];
        $terceroEmpresa = $options['terceroEmpresa'];
        $empresa = $options['empresa'];
        $factura = $options['factura'];

        //Coge fecha
        $factura2Socios = EntityManager::get('Factura')->findFirst("numero='{$factura->getNumero()}'");
        $factura->setFechaEmision($factura2Socios->getFechaFactura());

        ///////////////////////////////////////////////////////////////////////////////////////////
        ///                                 DATOS CLUB
        //////////////////////////////////////////////////////////////////////////////////////////
        //Solo se carga una vez ya que siempre es la misma
        if (!isset($this->_listaBloques['[__logo__]'])) {
            //Logo
            $srcLogo = 'http://'.$_SERVER['SERVER_NAME'].''.Core::getInstancePath().'img/backoffice/logo.png';
            $logo = '<img src="'.$srcLogo.'" alt="BackOffice Logo" width="100" />';
            if (isset($options['showLogo']) && $options['showLogo']==false) {
                $logo = '';
            }
            $this->_listaBloques['[__logo__]'] = $logo;

            //Nit
            $empresa = $options['empresa'];
            $nit = $empresa->getNit();
            if (isset($options['showNit']) && $options['showNit']==false) {
                $nit = '';
            }
            $nits = EntityManager::get('Nits')->findFirst(array('conditions'=>'nit="'.$nit.'"'));
            if ($nits==false) {
                throw new SociosException('El nit de la empresa no existe en terceros');
            }
            $this->_listaBloques['[__nit-club__]'] = number_format((float) $nit, 0, ", ", ".");

            //Razon Social
            $razonSocial = $nits->getNombre();
            if (isset($options['showRazonSocial']) && $options['showRazonSocial']==false) {
                $razonSocial = '';
            }
            $config['razonSocial'] = $razonSocial;
            $this->_listaBloques['[__razon-social__]'] = $razonSocial;

            //Ciudad
            $locciu = $nits->getLocciu();
            $location = BackCacher::getLocation($locciu);
            if ($location==false) {
                throw new SociosException('El locciu de empresa no existe en location');
            }
            //$ciudad = utf8_encode($location->getName().' / '.$location->getZone()->getName());
            //$ciudad = ($location->getName().' / '.$location->getZone()->getName());
            $ciudad = $location->getName();
            if (isset($options['showCiudad']) && $options['showCiudad']==false) {
                $ciudad = '';
            }
            $this->_listaBloques['[__ciudad-club__]'] = $ciudad;

            //Direccion
            $direccion = $terceroEmpresa->getDireccion();
            if (isset($options['showDireccion']) && $options['showDireccion']==false) {
                $direccion = '';
            }
            $this->_listaBloques['[__direccion-club__]'] = $direccion;

            //Telefono
            $telefono = $terceroEmpresa->getTelefono();
            if (isset($options['showTelefono']) && $options['showTelefono']==false) {
                $telefono = '';
            }
            $this->_listaBloques['[__telefono-club__]'] = $telefono;

            //Datos staticos de factura
            $this->_listaBloques['[__resolucion-factura__]'] = $factura->getResolucion();
            $this->_listaBloques['[__fecha-resolucion-factura__]'] = $factura->getFechaResolucion()->getLocaleDate('medium');
            $this->_listaBloques['[__prefijo-resolucion-factura__]'] = $factura->getPrefijo();
            $this->_listaBloques['[__nro-inicial-factura__]'] = $factura->getNumeroInicial();
            $this->_listaBloques['[__nro-final-factura__]'] = $factura->getNumeroFinal();
            $this->_listaBloques['[__periodo-factura__]'] = $factura->getFechaEmision()->getPeriod();
            $this->_listaBloques['[__fecha-factura__]'] = $factura->getFechaEmision()->getDate();
            $this->_listaBloques['[__fecha-vence-factura__]'] = $factura->getFechaVencimiento()->getDate();

            unset($srcLogo, $logo, $empresa, $nit, $nits, $ciudad, $telefono, $direccion);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////
        ///                                 DATOS SOCIOS
        //////////////////////////////////////////////////////////////////////////////////////////
        
        //Validacion de nit en socios
        $identificacion = trim($factura->getNit());
        $socios = EntityManager::get('Socios')->findFirst(array("identificacion='{$identificacion}'"));
        if ($socios==false) {
            throw new SociosException("No existe un socio con nit '{$identificacion}'");
        }
        $this->_listaBloques['[__nit-socio__]'] = $factura->getNit();
        $this->_listaBloques['[__nombre-socio__]'] = ($socios->getNombres()).' '.($socios->getApellidos());
        $this->_listaBloques['[__accion-socio__]'] = $socios->getNumeroAccion();
        
        //Ciudad
        $ciudad = 'Sin definir';
        $locciu = $socios->getCiudadCasa();
        $location = BackCacher::getLocation($locciu);
        if ($location==false) {
            $ciudad = $location->getName();
            if (isset($options['showCiudad']) && $options['showCiudad']==false) {
                $ciudad = 'Sin definir';
            }
        }
        $this->_listaBloques['[__ciudad-empresa__]'] = $ciudad;

        //Nota factura
        $notaFactura = $factura->getNotaFactura();
        if (isset($options['showNotaFactura']) && $options['showNotaFactura']==false) {
            $notaFactura = '';
        }
        $this->_listaBloques['[__nota-factura__]'] = $notaFactura;

        //Nota Ica
        $notaIca = $factura->getNotaIca();
        if (isset($options['showNotaIca']) && $options['showNotaIca']==false) {
            $notaIca = '';
        }
        $this->_listaBloques['[__nota-ica__]'] = $notaIca;

        //Si muetsra o no direccion y telefono
        $showDirTelFactura = Settings::get('show_dir_tel_factura', 'SO');
        if (!$showDirTelFactura) {
            throw new SociosException('No se ha definido si se desea ver o no la dirección y teléfono del socio en la factura');
        }

        $direccionEnvio = $socios->getDireccionCasa();
        if ($socios->getDireccionCorrespondencia()=='T') {
            $direccionEnvio = $socios->getDireccionTrabajo();
        }
        $this->_listaBloques['[__direccion-socio__]'] = $direccionEnvio;

        $more = '';
        if ($showDirTelFactura=='S') {
            $more = '
            <tr>
                <td align="left"><b>Dirección:</b> '.utf8_encode($direccionEnvio).'</td>
                <td align="left"><b>Teléfono:</b> '.utf8_encode($socios->getTelefonoCasa()).'</td>
            </tr>';
        }
        $this->_listaBloques['[__more-socio__]'] = $more;

        $options['numeroFactura'] = $factura->getNumero();
        $options['numeroAccion'] = $socios->getNumeroAccion();

        $this->_listaBloques['[__nro-factura__]'] = $factura->getNumero();

        unset($terceroFactura, $terceroEmpresa, $factura, $empresa, $identificacion, $socios, $razonSocial);
    }

    /**
    * Return seprator html
    * @return string $html
    */
    private function _addSeparator()
    {
        $html = '
            <div class="paragraph">
                <table cellspacing="0" cellpadding="0" width="100%" align="center">
                    <tr>
                        <td style="background:#ababab;height: 10px;" width="40%"></td>
                        <td style="background:#dadada;height: 10px;" width="30%"></td>
                        <td style="background:#eaeaea;height: 10px;" width="30%"></td>
                    </tr>
                </table>
            </div>
        ';

        return $html;
    }

    private function _getAlign($alignCode = '')
    {
        $align='';
        switch ($alignCode) {
            case 'C':
                $align = 'center';
                break;

            case 'R':
                $align = 'right';
                break;

            default:
                $align = 'left';
                break;
        }

        return $align;
    }

    /**
    * Metodo que genera el encabezado del invocier
    *
    * @param array $options(
    *     'headers'     => array( // type'=> 'int/money/string', 'align=>'C/L/R' (center/Left/right)
    *        array('field'=>'codigo', 'name'=>'Código Ref.'    , 'type'=>'string'    , 'align'=>'L'),
    *        array('field'=>'descripcion', 'name'=>'Descripción'    , 'type'=>'string'    , 'align'=>'L'),
    *        array('field'=>'cant', 'name'=>'Cantidad'    , 'type'=>'int'        , 'align'=>'R'),
    *        array('field'=>'valUni', 'name'=>'Valor Uni.'    , 'type'=>'money'    , 'align'=>'R', 'totalizer' => true),
    *        array('field'=>'valTot', 'name'=>'Valor Total'    , 'type'=>'money'    , 'align'=>'R', 'totalizer' => true),
    *        array('field'=>'descuento', 'name'=>'% Desc.'        , 'type'=>'decimal'    , 'align'=>'R', 'width'=>'10%', 'totalizer' => true)
    *    ),
    *    'rows'        => array(
    *        array(1, 'uno', 'A', ...),
    *        array(2, 'dos', 'I', ...),
    *        ...
    *     )
    * @return string $html
    */
    private function _addContent(&$options, $facturaId)
    {

        //validamos opciones de content
        if (!isset($options['headers']) || !count($options['headers'])) {
            throw new SociosException('_addContent: Es necesario ingresar el index headers en opciones de content');
        }
        $headers = $options['headers'];

        //$facturaId = $options['facturaId'];

        if (!isset($options['rows'][$facturaId]) || !count($options['rows'][$facturaId])) {
            throw new SociosException('_addContent: Es necesario ingresar el index rows['.$facturaId.'] en opciones de content/ '.print_r($options['rows'], true).', '.print_r($options['headers'], true));
        }
        $rows = $options['rows'][$facturaId];

        $html = '<div class="paragraph">
            <table class="resumen-factura" cellspacing="0" cellpadding="0" width="95%" align="center">
                <tr>';

        foreach ($headers as $head) {
            $width = '';
            if (isset($head['width'])) {
                $width = ' width="'.$head['width'].'"';
            }
            $html .= '<th '.$width.'>'.$head['name'].'</th>'.PHP_EOL;
        }

        $html .= '</tr>';

        $totalizer[$facturaId] = array();
        /**
        *    'codigo'         => $detalle->getItem(),
        *    'descripcion'     => $detalle->getDescripcion(),
        *    'cant'             => $detalle->getCantidad(),
        *    'valUni'         => $detalle->getValor(),
        *    'valTot'         => $detalle->getTotal(),
        *    'descuento'     => $detalle->getDescuento()
        */
        foreach ($rows as $row) {
            $html .= '<tr>';

            //generamos las rows de contenido
            foreach ($headers as $head) {

                //attributes
                $width='';
                if (isset($head['width'])) {
                    $width=' width="'.$head['width'].'"';
                }

                $align='left';
                $value = $row[$head['field']];
                $valueOri = $value;

                if (isset($head['pk']) && $head['pk']==true && !$valueOri) {
                    continue;
                }

                if (isset($head['type'])) {

                    //validamos align
                    if (isset($head['align'])) {
                        $align = $this->_getAlign($head['align']);
                    }

                    //define by type
                    switch ($head['type']) {

                        case 'int':
                            $align='right';
                            $value = number_format((float) $value, 0, ", ", ".");
                            break;

                        case 'decimal':
                            $align='right';
                            $value = number_format((float) $value, 0, ", ", ".");
                            break;

                        case 'money':
                            $align='right';
                            $value = number_format((float) $value, 0, ", ", ".");
                            break;
                    }

                }

                $html .= '<td align="'.$align.'" '.$width.'>'.$value.'</td>'.PHP_EOL;

                //Totalizer
                if (isset($head['totalizer']) && $head['totalizer']!=false) {
                    $totalizer[$facturaId][$head['field']] += $valueOri;
                }

            }
            unset($row);
            $html .= '</tr>';
        }

        //TOTALES
        $html.='<tr>';
        foreach ($options['headers'] as $head) {

            if (isset($head['totalizer']) && $head['totalizer']!=false) {

                $align = 'right';
                $value = $totalizer[$facturaId][$head['field']];

                if (isset($head['type'])) {

                    //define by type
                    switch ($head['type']) {

                        case 'int':
                            $value = number_format((float) $value, 0, ", ", ".");
                            break;

                        case 'decimal':
                            $value = number_format((float) $value, 0, ", ", ".");
                            break;

                        case 'money':
                            $value = number_format((float) $value, 0, ", ", ".");
                            break;
                    }

                }

                $html .= '<td align="right">'.$value.'</td>'.PHP_EOL;

            } else {

                //$html .= '<td align="right">&nbsp;</td>'.PHP_EOL;
                $html .= '<td align="center"><b>TOTALES</b></td>'.PHP_EOL;

            }
            unset($head);
        }
        $html .= '</tr>';

        //FIN CONTENT
        $html.='</table>
        </div>';

        return $html;
    }

    /**
     * Crea el contenido de la factura a mostrar 
     * 
     * @param  [type] $options   [description]
     * @param  [type] $facturaId [description]
     * @return [type]            [description]
     */
    private function _makeFacturaContent(&$options, $facturaId)
    {
        gc_enable();

        $facturaId = $options['facturaId'];

        $factura = EntityManager::get('Invoicer')->findFirst(array('conditions'=>'numero='.$facturaId));
        if ($factura==false) {
            $factura = EntityManager::get('Invoicer')->findFirst(array('conditions'=>"nit='{$options['nit']}' AND fecha_emision='{$options['fechaFactura']}'"));
            if ($factura==false) {
                throw new SociosException("La factura con numero '$facturaId' no existe");
            }
        }
        $options['factura'] = $factura;

        $empresa = $options['empresa'];

        $terceroEmpresa = BackCacher::getTercero($empresa->getNit());
        if ($terceroEmpresa==false) {
            throw new SociosException("La empresa no existe como un tercero");
        }
        $options['terceroEmpresa'] = $terceroEmpresa;

        $terceroFactura = BackCacher::getTercero($factura->getNit());
        if ($terceroFactura==false) {
            throw new SociosException("El tercero al que se generó la factura no existe '{$factura->getNit()}'");
        }
        $options['terceroFactura'] = $terceroFactura;

        $numeroFactura = $factura->getPrefijo().sprintf('%07s', $factura->getNumero());

        if (!isset($options['rows'][$facturaId])) {
            $options['rows'][$facturaId] = array();
        }
        
        //CARGA DATOS POR DEFECTO
        $this->_addHeader($options);

        $contentHtml = "";

        if (isset($options['apps']) && $options['apps'] == 'SO') {
            //Socios
            $options['headers'] = array(
                //array('field'=>'codigo', 'name'=>'Código C. Fijo'    , 'type'=>'string'    , 'align'=>'R', 'width'=>'15%', 'pk'=>true),
                array('field'=>'descripcion', 'name'=>'Descripción', 'width'=>'40%', 'type'=>'string'    , 'align'=>'L'),
                array('field'=>'valUni', 'name'=>'Valor Uni.', 'type'=>'money', 'align'=>'R', 'totalizer' => true),
                array('field'=>'valIva', 'name'=>'Valor IVA', 'type'=>'money', 'align'=>'R', 'totalizer' => true, 'width'=>'70'),
                array('field'=>'valIco', 'name'=>'Valor ICO', 'type'=>'money', 'align'=>'R', 'totalizer' => true, 'width'=>'70'),
                array('field'=>'valTot', 'name'=>'Valor Total', 'type'=>'money', 'align'=>'R', 'totalizer' => true)
            );

            $showSaldoAnteriorFactura = $options['showSaldoAnteriorFactura'];

            $showPos = $options['showPos'];

            $showConsumoMinimo = $options['showConsumoMinimo'];

            $cargoFijoCM = $options['cargoFijoCM'];

            $cargoFijoCMObj = BackCacher::getCargosFijos($cargoFijoCM);
            if (!$cargoFijoCMObj) {
                throw new SociosException("No existe el cargo fijo del consumo minimo");
            }

            $total = 0;
            $i=0;
            $detalleInvoicer = EntityManager::get('DetalleInvoicer')->find("facturas_id='{$factura->getId()}'");
            //throw new Exception(count($detalleInvoicer));
            foreach ($detalleInvoicer as $detalle) {

                //No mostrar saldo anterior
                if ($showSaldoAnteriorFactura!='S' && strstr($detalle->getDescripcion(), 'SALDO PERIODO')==true) {
                    continue;
                }

                //No mostrar Punto de Venta
                if ($showPos!='S' && strstr($detalle->getDescripcion(), 'PUNTO DE VENTA')==true) {
                    continue;
                }
                //throw new Exception($showConsumoMinimo);
                //No mostrar consumo minimo
                /*if ($showConsumoMinimo!='S') {
                    if (strstr($detalle->getDescripcion(), $cargoFijoCMObj->getNombre())==true) {
                        continue;
                    }
                }*/

                $options['rows'][$facturaId][] = array(
                    //'codigo'         => $detalle->getItem(),
                    'descripcion'    => utf8_encode($detalle->getDescripcion()),
                    'valUni'         => LocaleMath::round($detalle->getValor()),
                    'valIva'         => LocaleMath::round($detalle->getIva()),
                    'valIco'         => LocaleMath::round($detalle->getIco()),
                    'valTot'         => LocaleMath::round($detalle->getTotal())
                );

                $total += $detalle->getTotal();
                unset($detalle);

                //Garbage Collection
                if ($i>100) {
                    gc_collect_cycles();
                    $i=0;
                }
                $i++;
            }

        } else {

            //Facturador
            $options['headers'] = array(
                //array('field'=>'codigo', 'name'=>'Código Ref.'    , 'type'=>'string'    , 'align'=>'R', 'width'=>'15%', 'pk'=>true),
                array('field'=>'descripcion', 'name'=>'Descripción'    , 'type'=>'string'    , 'align'=>'L'),
                array('field'=>'valUni', 'name'=>'Valor Uni.'    , 'type'=>'money'    , 'align'=>'R', 'totalizer' => true),
                array('field'=>'valTot', 'name'=>'Valor Total'    , 'type'=>'money'    , 'align'=>'R', 'totalizer' => true),
                array('field'=>'descuento', 'name'=>'% Desc.'        , 'type'=>'decimal'    , 'align'=>'R', 'width'=>'10%', 'totalizer' => true)
            );

            $i=0;
            $total = 0;
            foreach ($this->DetalleInvoicer->find("facturas_id='{$factura->getId()}'") as $detalle) {
                $options['rows'][$facturaId][] = array(
                    //'codigo'         => $detalle->getItem(),
                    'descripcion'     => utf8_encode($detalle->getDescripcion()),
                    'valUni'         => $detalle->getValor(),
                    'valTot'         => $detalle->getTotal(),
                    'descuento'     => $detalle->getDescuento()
                );
                $total += $detalle->getTotal();
                unset($detalle);

                //Garbage Collection
                if ($i>100) {
                    gc_collect_cycles();
                    $i=0;
                }
                $i++;
            }

        }

        //añade el contenido
        if (isset($options['rows'][$facturaId]) && count($options['rows'][$facturaId])>0) {
            $contentHtml .= $this->_addContent($options, $facturaId);
        }
        
        $options['totalFactura'] = $total;

        $options2 = array();

        //Verificamos si hay financiacion
        $showFinanciacion = $options['showFinanciacion'];

        //Verificamos si mostramos recargo con mora
        $showRecargoMora = $options['showRecargoMora'];

        $options['totalFacturado'] = $total;

        $fechaFactura = new Date($factura->getFechaEmision());
        $periodoSocios = EntityManager::get('Periodo')->findFirst(array('conditions'=>"periodo='{$fechaFactura->getPeriod()}'"));
        if ($periodoSocios==false) {
            throw new SociosException("No hay un periodo que este registrando la fecha de la factura actual '{$factura->getFechaEmision()}'", 1);
        }
        $moraPeriodo = $periodoSocios->getInteresesMora();
        $options['Mora'] = ($options['totalFacturado'] * $moraPeriodo / 100);
        $options['totalFacturadoConMora'] = $options['Mora'] + $options['totalFacturado'];

        //Mostrar financiacion
        if ($showFinanciacion=='S') {

            $financiacionObj = EntityManager::get('Financiacion')->find(array('conditions'=>'factura_id='.$factura->getId()));

            foreach ($financiacionObj as $financiacion) {

                $options2['headers'] = array(
                    array('field'=>'descripcion', 'name'=>'Descripción', 'type'=>'text', 'align'=>'L'),
                    array('field'=>'valor', 'name'=>'Valor', 'type'=>'money', 'align'=>'R', 'totalizer' => true),
                    array('field'=>'mora', 'name'=>'Mora', 'type'=>'money', 'align'=>'R', 'totalizer' => true),
                    array('field'=>'total', 'name'=>'Total', 'type'=>'money', 'align'=>'R', 'totalizer' => true),
                );

                $options2['rows'][$facturaId][] = array(
                    'descripcion'   => $financiacion->getDescripcion(),
                    'valor'         => LocaleMath::round($financiacion->getValor()),
                    'mora'          => LocaleMath::round($financiacion->getMora()),
                    'total'         => LocaleMath::round($financiacion->getTotal())
                );

                $options['totalFacturado'] += LocaleMath::round($financiacion->getTotal());
                $options['valorFinanciacion'] += LocaleMath::round($financiacion->getTotal());

                unset($financiacion);
            }
            unset($financiacionObj);

            if (isset($options2['headers'])) {
                if (isset($options['rows'][$facturaId]) && count($options['rows'][$facturaId])>0) {
                    $contentHtml .= $this->_addSeparator();
                }

                $contentHtml .= $this->_addContent($options2, $facturaId);
            }

        }

        $options['totalFacturaConMora'] = $options['Mora'] + $options['totalFactura'];

        $formasPago = array();

        foreach ($this->FacturasPagos->find("facturas_id='{$factura->getId()}'") as $facturaPago) {
            $formasPago[] = $facturaPago->getDescripcion();
            unset($facturaPago);
        }

        if (count($formasPago)>0) {
            $locale = Locale::getApplication();
            $formasPago = 'Formas de Pago: '.$locale->getConjunction($formasPago);
        } else {
            $formasPago = 'Forma de Pago: '.$formasPago[0];
        }

        //Agregando contenido de datos
        $this->_listaBloques['[__contentHtml__]'] = $contentHtml;

        //RESUMEN FACTURA
        $resumen = '';
        $resumenSO = Settings::get('resumen_factura', 'SO');

        if ($resumenSO) {
            $resumen = '<b>Mensaje:</b><br/>'.$resumenSO;
        }
        $this->_listaBloques['[__resumen-factura__]'] = $resumen;


        //RESUMEN PIE PAGINA DE FACTURA
        $resumenPie = '';
        $resumenPieSO = Settings::get('resumen_factura_pie', 'SO');

        if ($resumenPieSO) {
            $resumenPie = $resumenPieSO;
        }
        $this->_listaBloques['[__resumen-pie-factura__]'] = $resumenPie;

        //Content Data
        $this->_listaBloques['[__base-gravable-factura__]'] = number_format((float) $factura->getVenta16(), 0, ", ", ".");
        $this->_listaBloques['[__ingreso-tercero-factura__]'] = number_format((float) $factura->getVenta10(), 0, ", ", ".");
        $this->_listaBloques['[__base-no-gravable-factura__]'] = number_format((float) $factura->getVenta0(), 0, ", ", ".");
        $this->_listaBloques['[__iva-16-factura__]'] = number_format((float) $factura->getIva16(), 0, ", ", ".");
        $this->_listaBloques['[__ico-8-factura__]'] = number_format((float) $factura->getTotalIco(), 0, ", ", ".");
        $this->_listaBloques['[__total-factura__]'] = number_format((float) LocaleMath::round($options['totalFacturado'], 0), 0, ", ", ".");

        //CONTENIDO DE RECARGO DE MORA
        $contentRecargoMora = "";
        if ($showRecargoMora=='S') {
            $contentRecargoMora .= '
            <tr>
                <td align="right" ><b>Total Con Mora</b></td>
                <td align="right" >'.number_format((float) LocaleMath::round($options['totalFacturadoConMora'], 0), 0, ", ", ".").'</td>
            </tr>';

            if ($options['totalFactura']!=$options['totalFacturado']) {
                $contentRecargoMora .= '<tr>
                    <td align="right" ><b>Total A Pagar</b></td>
                    <td align="right" >'.number_format((float) LocaleMath::round($options['totalFactura'], 0), 0, ", ", ".").'</td>
                </tr>
                <tr>
                    <td align="right" ><b>Total A Pagar Con Mora</b></td>
                    <td align="right" >'.number_format((float) LocaleMath::round($options['totalFacturadoConMora']+$options['valorFinanciacion'], 0), 0, ", ", ".").'</td>
                </tr>';
            }
        }
        $this->_listaBloques['[__content-recargo-mora__]'] = $contentRecargoMora;
        unset($contentRecargoMora);

        //Verificamos si mostramos recargo con mora
        $showCupoPago = $options['showCupoPago'];

        //CUPON DE PAGO
        $contentCuponPago = "";
        if ($showCupoPago=='S') {
            //SEPARATOR
            $contentCuponPago .= $this->_addSeparator();

            ////////////////
            //CUPO DE PAGO
            /////////////////

            $recargoDay = new Date($factura->getFechaVencimiento()->getDate());
            $recargoDay->addDays(1);
            $this->_listaBloques['[__fecha-recargo-factura__]'] = $recargoDay->getDate();

            $contentCuponPago .= '
            <div style="width:100%;" >
                <table width="100%"  class="resumen-factura2" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td align="center"><b>CUPON DE PAGO</b></td>
                    </tr>
                </table>
                <table width="100%" class="resumen-factura2" cellspacing="0" cellpadding="0" >
                    <tr>
                        <td width="25%"><b>Numero Interno de Documento:</b></td>
                        <td width="25%">[__nro-factura__]</td>
                        <td><b>D/Social:</b></td>
                        <td>[__accion-socio__]</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><b>Nombre:</b></td>
                        <td>[__nombre-socio__]</td>
                    </tr>
                </table>
                <table width="100%" class="resumen-factura2" cellspacing="0" cellpadding="0" >
                    <tr>
                        <td colspan="2">
                            <table border="0" width="50%" class="resumen-factura2" cellspacing="0" cellpadding="0" >
                                <tr>
                                    <td align="center"><b>FORMA DE PAGO</b></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" class="resumen-factura" cellspacing="0" cellpadding="0" >
                                            <tr>
                                                <td width="70"><b>Cd. Banco</b></td>
                                                <td width="200"><b>No. Cuenta Cheque</b></td>
                                                <td width="80"><b>Valor</b></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center"><b>EFECTIVO</b></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>d
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td colspan="2">
                            <table border="0" width="50%" class="resumen-factura2" cellspacing="0" cellpadding="0" >
                                <tr>
                                    <td align="center"><b>FECHA</b></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" class="resumen-factura2" cellspacing="0" cellpadding="0" >
                                            <tr>
                                                <td><b>Periodo Facturado:</b></td>
                                                <td>[__periodo-factura__]</td>
                                            </tr>
                                            <tr>
                                                <td><b>Fecha de Factura:</b></td>
                                                <td>[__fecha-factura__]</td>
                                            </tr>
                                            <tr>
                                                <td><b>Fecha Limite de Pago:</b></td>
                                                <td>[__fecha-vence-factura__]</td>
                                            </tr>
                                            <tr>
                                                <td><b>TOTAL A PAGAR ANTES DE:</b></td>
                                                <td>[__total-factura__]</td>
                                            </tr>';
            if ($showRecargoMora=='S') {
                $contentCuponPago .= '
                <tr>
                    <td><b>PAGUE CON RECARGO DESDE:</b></td>
                    <td>[__fecha-recargo-factura__]</td>
                </tr>';
            }
            $contentCuponPago .= '
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            ';
        }
        $this->_listaBloques['[__content-cupon-pago__]'] = $contentCuponPago;
        unset($contentCuponPago);

        gc_disable();
    }

    /**
     * Imprimie las facturas
     *
     * @param array $options
     * @throws SociosException
     * @return string $fileName
     */
    public function getInvoicerPrint(&$options)
    {
        gc_enable();

        Core::importFromLibrary('Mpdf', 'mpdf.php');
        //Core::importFromLibrary('Wkpdf', 'WkHtmlToPdf.php');
        
        //Si no se envia nada busque y genere ttodas la facturas
        if (!isset($options['facturas']) || $options['facturas']<=0 || !is_array($options['facturas'])) {

            $options['facturas'] = array();
            $facturasObj = EntityManager::get('Invoicer')->setTransaction($this->_transaction)->find(array("columns"=>"numero", "conditions"=>"fecha='{$options['fechaIni']}'"));

            foreach ($facturasObj as $factura) {
                $options['facturas'][] = $factura->getNumero();
                unset($factura);
            }
            unset($facturasObj);
        }
        $facturas = $options['facturas'];

        $empresa = BackCacher::getEmpresa();
        if ($empresa==false) {
            throw new SociosException("No existe la empresa que generó la factura");
        }
        $options['empresa'] = $empresa;

        $pdf = new mPDF('utf-8', 'letter');
        $pdf->tMargin = 10;
        $pdf->lMargin = 10;

        //CSS AND HTML BODY
        $html = "";
        $n=0;
        //throw new SociosException(print_r($facturas, true));
        
        //OBTENEMOS TEMPLATES
        $headTemplate = file_get_contents("apps/socios/views/facturar/head_factura.html");
        $bodyTemplate = file_get_contents("apps/socios/views/facturar/factura.html");
        $bottomTemplate = file_get_contents("apps/socios/views/facturar/bottom_factura.html");
            
        $i=0;
        $html = $headTemplate;
        $countFacturas = count($facturas);
        foreach ($facturas as $facturaId) {

            if (!$facturaId) {
                throw new SociosException('Error no hay $facturaId: '.$facturaId);
            }

            $factura = EntityManager::get('Invoicer')->setTransaction($this->_transaction)->findFirst("numero='$facturaId'");

            if ($factura==false) {
                if (isset($options['showDebug']) && $options['showDebug']==true) {
                    throw new SociosException("No existe la factura con numero ".$facturaId);
                } else {
                    continue;
                }
            }

            $options['facturaId'] = $facturaId;

            //generamos body de reporte (factura) a listaBloque
            $this->_makeFacturaContent($options, $facturaId);
            $html .= $this->replaceListaBloques($bodyTemplate);

            if ($countFacturas>1) {
                $html .= '<pagebreak />';
            }

            unset($options['rows'][$facturaId], $facturaId);

            $n++;

            if ($i>100) {
                gc_collect_cycles();
                $i=0;
            }
            $i++;
        }

        if ($n>1) {
            $numeroFactura = 'periodo';
        } else {
            $numeroFactura = $options['facturaId'];
        }
        unset($options);

        $html .= $bottomTemplate;

        //UTF8
        $html = mb_convert_encoding($html, mb_detect_encoding($html), 'UTF-8');

        $fileName = false;
        if (!empty($html)) {
            $path = KEF_ABS_PATH . "/public/temp/";
            $fileName = 'factura-'.$numeroFactura.'-'.mt_rand(1000, 9999).'.pdf';
            $filePath = $path . $fileName . ".html";
            file_put_contents($filePath, $html);
            //$pdf->writeHTML($html);
            //$pdf->Output('public/temp/'.$fileName);

            //P4ML JAVA
            $command ="java -jar " . KEF_ABS_PATH . "/Library/P4ML/pd4ml.jar file:" . $filePath . " " . $path . $fileName;
            //throw new Exception($command);
            exec($command, $output);
            if ($output) {
                echo $output;
            }
        }

        gc_disable();

        return $fileName;

    }

    /**
     * Remplaza en un template los bloques que se hallan cargado
     * @param  STRING $template Template de factura
     * @return STRING Template ya remplazado
     */
    public function replaceListaBloques($template)
    {
        foreach ($this->_listaBloques as $bloque => $value) {
            if (!mb_check_encoding($value, 'UTF-8')) {
                $value = utf8_encode($value);
            }
            $template = str_replace($bloque, $value, $template);
            unset($bloque, $value);
        }
        return $template;
    }

    /**
    * Genera el reporte de socios suspendidos en el period actual
    */
    public function printEstadoCuenta(&$config)
    {
        ini_set('memory_limit', '-1');
        
        try {
            $transaction = TransactionManager::getUserTransaction();

            //Agregando configuracion constante inicial
            $sociosEstadoCuenta = new SociosEstadoCuenta();
            $sociosEstadoCuenta->addConfigDefault($config);

            $html = $this->_printEstadoCuenta($config);

            if (isset($config['reportType'])) {

                //Solo PDF
                if ($config['reportType']=='pdf') {
                    /*Core::importFromLibrary('Mpdf', 'mpdf.php');
                    $pdf = new mPDF('utf-8', 'letter');
                    $pdf->SetDisplayMode('fullpage');
                    $pdf->tMargin = 10;
                    $pdf->lMargin = 10;

                    $fileName = false;
                    if (!empty($html)) {
                        $pdf->debug=true;
                        $pdf->writeHTML($html);
                        $fileName = 'estado_cuenta-'.mt_rand(1000, 9999).'.pdf';
                        unset($html);
                        $pdf->Output('public/temp/'.$fileName);
                    }
                    */
                    $path = KEF_ABS_PATH . "/public/temp/";
                    $fileName = 'estadoCuenta-'.mt_rand(1000, 9999).'.pdf';
                    $filePath = $path . $fileName . ".html";
                    file_put_contents($filePath, $html);

                    //P4ML JAVA
                    $command ="java -jar " . KEF_ABS_PATH . "/Library/P4ML/pd4ml.jar file:" . $filePath . " " . $path . $fileName;
                    //throw new Exception($command);
                    exec($command, $output);
                } else {
                    if ($config['reportType']=='excel') {
                        $fileName = 'estado_cuenta-'.mt_rand(1000, 9999).'.xls';
                        header("Content-Type: application/vnd.ms-excel");
                        header("Content-Disposition: attachment; filename='$fileName'");
                        header("Pragma: no-cache");
                        header("Expires: 0");
                        echo $html;
                        exit;
                    } else {
                        if ($config['reportType']=='html') {
                            $fileName = 'estado_cuenta-'.mt_rand(1000, 9999).'.html';
                            file_put_contents(KEF_ABS_PATH."/public/temp/".$fileName, $html);
                            unset($html);
                        }
                    }
                }
            }

            /*$myLog->log("Fin Logger ".date("Y-m-d H:i:s"), Logger::DEBUG);

            //Se guarda al log
            $myLog->commit();

            //Cierra el Log
            $myLog->close();*/

            $config['file'] = 'public/temp/'.$fileName;

            return $config;
        } catch (Exception $e) {
            throw new Exception($e->getMessage()/*.'trace: '.print_r($e, true)*/);
        }
    }

    /**
     * Imprime estados de cuneta ya generados
     * 
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    private function _printEstadoCuenta(&$config)
    {

        try {
            //gc_enable();

            $html = '';
            $totales = array();
            $transaction = TransactionManager::getUserTransaction();
            $datosClub = EntityManager::get('DatosClub')->findFirst();
            $consecutivo = EntityManager::get('Consecutivos')->findFirst();

            $periodoObj = EntityManager::get('Periodo')->findFirst("periodo='".$config["periodo"]."'");
            if (!$periodoObj || !$periodoObj->getDiaFactura()) {
                throw new SociosException("no se ha definido el dia de facturacion del periodo '".$config["periodo"]."'");
            }
            //Dias de vencimiento de factura
            $diaVenc = $periodoObj->getDiasPlazo();
            if (!$diaVenc) {
                throw new SociosException("No se ha definido los dias de vencimiento del periodo actual");
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
            //$fechaCorte = "$year-$month-".$day;
            $fechaCorte = $config['fechaIni'];

            if (!isset($config['sociosId']) || $config['sociosId']<=0) {
                $estadoCuentaObj = EntityManager::get('EstadoCuenta')->find("fecha='$fechaCorte'", "order: numero");/*, "limit: 10"*/
            } else {
                $estadoCuentaObj = EntityManager::get('EstadoCuenta')->find("fecha='$fechaCorte' AND socios_id='{$config['sociosId']}'", "order: numero");
            }
            $dateFechaCorte = new Date($fechaCorte);

            $dateFechaLimite = new Date($fechaCorte);
            $dateFechaLimite->addDays($diaVenc);
            //$dateFechaLimite = new Date($config['fechaFin']);

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

            //Logo
            $srcLogo = 'http://'.$_SERVER['SERVER_NAME'].''.Core::getInstancePath().'img/backoffice/logo.png';
            //$srcLogo = 'img/backoffice/logo.png';
            $logo = '<img src="'.$srcLogo.'" alt="BackOffice Logo" width="100" />';

            //TEMPLATES
            $headTemplate = file_get_contents("apps/socios/views/estado_cuenta/head_estado_cuenta.html");
            $bodyTemplate = file_get_contents("apps/socios/views/estado_cuenta/estado_cuenta.html");
            $consolidadoTemplate = file_get_contents("apps/socios/views/estado_cuenta/estado_cuenta_consolidado.html");
            $bottomTemplate = file_get_contents("apps/socios/views/facturar/bottom_factura.html");

            //BLOQUES INICIALES
            $this->_listaBloques = array(
                '[__logo__]' => $logo,
                '[__nit-club__]' => number_format((float) $datosClub->getNit(), 0, ", ", "."),
                '[__nombre-club__]' => $datosClub->getNombre(),
                '[__direccion-club__]'=> $datosClub->getDireccion(),
                '[__nota-factura__]'=> $consecutivo->getNotaFactura(),
                '[__nota-ica__]'=> $consecutivo->getNotaIca(),
            );
            unset($logo, $datosClub, $consecutivo);

            //INICIAMOS REPORTE
            $html = $headTemplate;

            ///RECORREMOS SOCIOS A GENERAR
            $i=0;
            foreach ($estadoCuentaObj as $estadoCuenta) {

                $sociosId = $estadoCuenta->getSociosId();
                $socios = BackCacher::getSocios($sociosId);
                if (!$socios) {
                    if (isset($config['sociosId']) && $config['sociosId']>0) {
                        throw new SociosException("El socios con id '$sociosId' no existe en maestro de socios");
                    } else {
                        continue;
                    }
                }

                $location = BackCacher::getLocation($socios->getCiudadCasa());
                if (!$location) {
                    throw new SociosException('No se ha asignado la ciudad de la casa en el maestro de socios');
                }
                
                $ciudad = $location->getName();
                
                if (!mb_check_encoding($ciudad, 'UTF-8')) {
                    $ciudad = utf8_encode($ciudad);
                }
                
                $direccionEnvio = $socios->getDireccionCasa();
                if ($socios->getDireccionCorrespondencia()=='T') {
                    $direccionEnvio = $socios->getDireccionTrabajo();
                }

                $contentMoviHtml = "";

                $saldoMora = 0;
                $totalCargos = 0;
                $totalAbonos = 0;
                $estadoCuentaDetalleArray = array();

                //Buscamos detalle de estado de cuenta
                $detalleEstadoCuentaObj = EntityManager::get('DetalleEstadoCuenta')->find("numero='{$estadoCuenta->getNumero()}'");

                //haciendo espacios
                $stylecss = "";
                if (count($detalleEstadoCuentaObj)>20) {
                    $stylecss = " style='font-size: 10px;'";
                }

                foreach ($detalleEstadoCuentaObj as $detalleEstadoCuenta) {

                    $fecha = $detalleEstadoCuenta->getFecha();
                    $documento = $detalleEstadoCuenta->getDocumento();
                    $concepto = $detalleEstadoCuenta->getConcepto();
                    $cargo = $detalleEstadoCuenta->getCargos();
                    $abono = $detalleEstadoCuenta->getAbonos();

                    //totales
                    $totalCargos += $cargo;
                    $totalAbonos += $abono;

                    $valorCargo = number_format((float) $cargo, 0, ", ", ".");
                    $valorAbono = number_format((float) $abono, 0, ", ", ".");

                    //movimiento principal
                    $contentMoviHtml .= "
                    <tr>
                        <td align='center' $stylecss>
                            $fecha
                        </td>
                        <td align='center' $stylecss>
                            $documento
                        </td>
                        <td align='left' $stylecss>
                            ".strip_tags($concepto)."
                        </td>
                        <td align='right' $stylecss>
                            <strong>".$valorCargo."</strong>
                        </td>
                        <td align='right' $stylecss>
                            <strong>".$valorAbono."</strong>
                        </td>
                    </tr>
                    ";
                    unset($fecha, $documento, $concepto, $cargo, $abono, $valorCargo, $valorAbono, $detalleEstadoCuenta);
                }

                if (!isset($config['consolidado']) || $config['consolidado']!=true) {
                    if (count($detalleEstadoCuentaObj)<=21) {
                        for ($i=1; $i<=(21-count($detalleEstadoCuentaObj)); $i++) {
                            $contentMoviHtml .= "
                            <tr>
                                <td align='center'>
                                    &nbsp;
                                </td>
                                <td align='center'>
                                    &nbsp;
                                </td>
                                <td align='left'>
                                    &nbsp;
                                </td>
                                <td align='right'>
                                    &nbsp;
                                </td>
                                <td align='right'>
                                    &nbsp;
                                </td>
                            </tr>
                            ";
                        }
                    }
                }

                /**
                 * Aqui se asignan los bloques de texto para remplazar enla plantilla
                 * @var array
                 */
                $this->_listaBloques['[__nro-estado-cuenta__]'] = $estadoCuenta->getNumero();
                $this->_listaBloques['[__nombre-socio__]'] = $socios->getNombres() . " " . $socios->getApellidos();
                $this->_listaBloques['[__accion-socio__]'] = $socios->getNumeroAccion();
                $this->_listaBloques['[__fecha-corte__]'] = $dateFechaCorte->getDate();
                $this->_listaBloques['[__direccion-socio__]'] = $direccionEnvio;
                $this->_listaBloques['[__ciudad-socio__]'] = $ciudad;
                $this->_listaBloques['[__pague-hasta__]'] = $dateFechaLimite->getDate();
                $this->_listaBloques['[__contentMoviHtml__]'] = $contentMoviHtml;
                $this->_listaBloques['[__saldo-anterior__]'] = number_format((float) $estadoCuenta->getSaldoAnt(), 0, ", ", ".");
                $this->_listaBloques['[__saldo-en-mora__]'] = number_format((float) $estadoCuenta->getMora(), 0, ", ", ".");
                $this->_listaBloques['[__total-cargos__]'] = number_format((float) $estadoCuenta->getCargos(), 0, ", ", ".");
                $this->_listaBloques['[__total-abonos__]'] = number_format((float) $estadoCuenta->getPagos(), 0, ", ", ".");
                $this->_listaBloques['[__saldo-nuevo__]'] = number_format((float) $estadoCuenta->getSaldoNuevo(), 0, ", ", ".");
                $this->_listaBloques['[__resumen__]'] = $resumen;
                $this->_listaBloques['[__resumen-recaudo__]'] = $resumenRecaudo;

                $html .= $this->replaceListaBloques($bodyTemplate);

                unset($estadoCuenta, $socios, $direccionEnvio, $ciudad, $contentMoviHtml, $location, $detalleEstadoCuentaObj);

                /*if ($i>100) {
                    gc_collect_cycles();
                    $i = 0;
                }
                $i++;*/
            }

            ////////////////////////
            //TOTALES CONSOLIDADOS
            ////////////////////////

            $countTotales = count($estadoCuentaObj);

            $totalFinal = array(
                'saldoAnterior' => 0,
                'totalCargos' => 0,
                'totalAbonos' => 0,
                'valorAPagar' => 0,
                'valorAPagarMora' => 0
            );

            $contentMoviConsolHtml = '';
            $i=0;
            foreach ($estadoCuentaObj as $estadoCuenta) {

                $socios = BackCacher::getSocios($estadoCuenta->getSociosId());
                $saldoAnt = $estadoCuenta->getSaldoAnt();
                $cargos = $estadoCuenta->getCargos();
                $abonos = $estadoCuenta->getPagos();
                $saldoNuevo = $estadoCuenta->getSaldoNuevo();
                $saldoNuevoMora = $estadoCuenta->getSaldoNuevoMora();

                $cssTd = "tableLineBottom tableLineRight tableLineTop";
                $contentMoviConsolHtml .= "
                    <tr class='$cssTd' align='center'>
                        <td align='center' class='border2' align='center'>
                            " . $socios->getNumeroAccion() . "
                        </td>
                        <td align='right' class='$cssTd' align='center'>
                            " . number_format((float) $socios->getIdentificacion(), 0, ", ", ".") . "
                        </td>
                        <td align='center' class='border2' align='center'>
                            " . $socios->getNombres() . " " . $socios->getApellidos() . "
                        </td>
                        <td align='right' class='$cssTd' align='center'>
                            " . number_format((float) $saldoAnt, 0, ", ", ".") . "
                        </td>
                        <td align='right' class='$cssTd' align='center'>
                            " . number_format((float) $cargos, 0, ", ", ".") . "
                        </td>
                        <td align='right' class='$cssTd' align='center'>
                            " . number_format((float) $abonos, 0, ", ", ".") . "
                        </td>
                        <td align='right' class='$cssTd' align='center'>
                            " . number_format((float) $saldoNuevo, 0, ", ", ".") . "
                        </td>
                        <td align='right' class='$cssTd' align='center'>
                            " . number_format((float) $saldoNuevoMora, 0, ", ", ".") . "
                        </td>
                    </tr>
                ";

                $totalFinal['saldoAnterior'] += $saldoAnt;
                $totalFinal['totalCargos'] += $cargos;
                $totalFinal['totalAbonos'] += $abonos;
                $totalFinal['valorAPagar'] += $saldoNuevo;
                $totalFinal['valorAPagarMora'] += $saldoNuevoMora;

                unset($estadoCuenta, $saldoAnt, $cargos, $abonos, $saldoNuevo, $saldoNuevoMora, $socios);

                /*if ($i>100) {
                    gc_collect_cycles();
                    $i = 0;
                }
                $i++;*/
            }

            unset($estadoCuentaObj);

            $contentMoviConsolHtml .= "
                <tr class='border2' align='center'>
                    <td align='center' colspan='3' class='border2' align='center'>
                        #" . $countTotales . "
                    </td>
                    <td align='right' class='border2' align='center'>
                        " . number_format((float) $totalFinal['saldoAnterior'], 0, ", ", ".") . "
                    </td>
                    <td align='right' class='border2' align='center'>
                        " . number_format((float) $totalFinal['totalCargos'], 0, ", ", ".") . "
                    </td>
                    <td align='right' class='border2' align='center'>
                        " . number_format((float) $totalFinal['totalAbonos'], 0, ", ", ".") . "
                    </td>
                    <td align='right' class='border2' align='center'>
                        " . number_format((float) $totalFinal['valorAPagar'], 0, ", ", ".") . "
                    </td>
                    <td align='right' class='border2' align='center'>
                        " . number_format((float) $totalFinal['valorAPagarMora'], 0, ", ", ".") . "
                    </td>
                </tr>";
            unset($contentMovi);

            //Agregamos consolidado
            $this->_listaBloques['__estadoCuentaConsolidado__'] = $contentMoviConsolHtml;
            unset($contentMoviConsolHtml);

            $html .= $this->replaceListaBloques($consolidadoTemplate);
            $html .= $bottomTemplate;

            unset($headTemplate, $bodyTemplate, $consolidadoTemplate, $bottomTemplate);

            //gc_disable();

            return $html;
        } catch (Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    ////////////////////////////////
    //Estado de cuenta Consolidado
    ////////////////////////////////
    /**
    * Genera el reporte de estado de cuenta ya generado consolidado con pagos y nuevo saldo
    */
    public function estadoCuentaConsolidado($config)
    {
        try {
            $this->_transaction = TransactionManager::getUserTransaction();
            Core::importFromLibrary('Hfos/Socios', 'SociosEstadoCuenta.php');

            if (!$config['fecha']) {
                throw new SociosException("No se ha definido la fecha generar consolidado");
            }
            $fecha = $config['fecha'];
            $fechaDate = new Date($fecha);
            $fechaPeriodo = $fechaDate->getPeriod();

            //verificamos si puede reparar el estado de cuenta
            $periodo = SociosCore::getCurrentPeriodoObject($fechaPeriodo);
            $saveEstadoCuentaFlag = true;
            if ($periodo->getCierre()=='S') {
                $saveEstadoCuentaFlag = false; 
            }

            if (!$config['reportType']) {
                throw new SociosException("No se ha definido el tipo de salida a generar consolidado");
            }
            $reportType = $config['reportType'];

            $report = ReportBase::factory($reportType);

            $titulo = new ReportText('ESTADO DE CUENTA CONSOLIDADO CON PAGOS', array(
                'fontSize' => 16,
                'fontWeight' => 'bold',
                'textAlign' => 'center'
            ));

            $report->setHeader(array($titulo));

            $report->setDocumentTitle('Estado de cuenta consolidado con pagos');
            $report->setColumnHeaders(array(
                'ESTADO DE CUENTA',//0
                'DERECHO',//1
                'CC/NIT',//2
                'NOMBRE',//3
                'FECHA',//4
                'SALDO ANTERIOR',//5
                'SALDO ACTUAL',//6
                'PAGOS DB',//7
                'PAGOS CR',//8
                'NUEVO SALDO'//9
            ));

            $report->setCellHeaderStyle(new ReportStyle(array(
                'textAlign' => 'center',
                'backgroundColor' => '#eaeaea'
            )));

            $report->setColumnStyle(array(0, 1, 2, 3, 4, 18), new ReportStyle(array(
                'textAlign' => 'center',
                'fontSize' => 11
            )));

            $report->setColumnStyle(array(5, 6, 7, 8, 9), new ReportStyle(array(
                'textAlign' => 'right',
                'fontSize' => 11
            )));

            $report->setColumnFormat(array(5, 6, 7, 8, 9), new ReportFormat(array(
                'type' => 'Number',
                'decimals' => 0
            )));

            $report->start(true);

            $totales = array();
            $totales['saldoAnt'] = 0;
            $totales['saldoNuevo'] = 0;
            $totales['pagos2'] = 0;
            $totales['pagos3'] = 0;
            $totales['saldo'] = 0;

            //obtenemos los pagos del mes de la fecha los pagos del periodo
            $fechaObj = new Date($fecha);
            //$consumos = SociosCore::getConsumosPeriodo($fechaObj->getPeriod());
            $pagos = SociosCore::getPagosPeriodo($fechaObj->getPeriod());
            $ajustes = SociosCore::getAjustesPeriodo($fechaObj->getPeriod());

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
            //throw new Exception($comprobsPagosAs);


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

            //Socios
            $sociosObj = EntityManager::get('Socios')->find(array("cobra='S'", 'column'=>'socios_id,cobra', 'order'=>"CAST(numero_accion AS SIGNED) ASC"));

            $sociosEstadoCuenta = new SociosEstadoCuenta();

            $i = 0;
            $num = 0;
            foreach ($sociosObj as $socios) {
                //Buscamos Consolidados
                $estadoCuenta = EntityManager::get('EstadoCuenta')->findFirst(array("fecha='$fecha' AND socios_id='{$socios->getSociosId()}'", 'order'=>'numero ASC'));

                if (!$estadoCuenta) {
                    continue;
                }

                $nit = $socios->getIdentificacion();

                //Pagos
                $pagoC = 0;
                $pagoD = 0;
                if (isset($pagos[$nit])) {
                    //if ($nit=='2868524') {     throw new SociosException($pagos[$nit]['valor']['C']);    }
                    $pagoC = $pagos[$nit]['valor']['C'];
                    $pagoD = $pagos[$nit]['valor']['D'];
                }

                //Ajustes
                if (isset($ajustes[$nit])) {
                    $pagoC += $ajustes[$nit]['valor']['C'];
                    $pagoD += $ajustes[$nit]['valor']['D'];
                }

                $saldoNuevo = (float) $estadoCuenta->getSaldoNuevo();

                $saldo = (float) $saldoNuevo + $pagoD - $pagoC;

                //$contentMovi = $this->getContentMovi($dateFechaCorte->getPeriod(), $socios, $options);
                $contentMovi = $sociosEstadoCuenta->getContentMovi($fecha, $socios, $options);

                //Revisamos los dias de cartera
                /*$datos = array(
                    'sociosId'    => $socios->getSociosId(),
                    'fecha' => $fecha,
                    'fechaSaldo' => $estadoCuenta->getFechaSaldo(),
                    'accion' =>  $socios->getNumeroAccion(),
                    'identificacion' =>  $socios->getIdentificacion(),
                    'nombre' =>  $socios->getNombres()." ".$socios->getApellidos(),
                    'saldoAnterior' => $estadoCuenta->getSaldoAnt(),
                    'totalCargos' => $estadoCuenta->getCargos(),
                    'totalAbonos' => $estadoCuenta->getPagos(),
                    'valorAPagar' => $estadoCuenta->getSaldoNuevo(),
                    'valorAPagarMora' => $estadoCuenta->getSaldoNuevoMora(),
                    'contentMovi' => $contentMovi
                );

                //Guardamos en Tabla Estado de Cartera Consolidado
                if ($saveEstadoCuentaFlag) {
                    $estadoCuenta = $sociosEstadoCuenta->_saveEstadoCuentaConsolidado($datos);
                }*/

                //ROW
                $report->addRow(array(
                    $estadoCuenta->getNumero(),//0
                    $socios->getNumeroAccion(),//1
                    $nit,//2
                    $socios->getNombres()." ".$socios->getApellidos(),//3
                    $fecha,//4
                    $estadoCuenta->getSaldoAnt(),//5
                    $estadoCuenta->getSaldoNuevo(),//6
                    $pagoD,//7
                    $pagoC,//8
                    $saldo,//9
                ));

                //TOTALES
                $totales['saldoAnt'] += (float) $estadoCuenta->getSaldoAnt();
                $totales['saldoNuevo'] += (float) $estadoCuenta->getSaldoNuevo();
                $totales['pagos2'] += (float) $pagoD;
                $totales['pagos3'] += (float) $pagoC;
                $totales['saldo'] += (float) $saldo;

                $num++;

                unset($estadoCuenta, $socios, $nit, $pagoC, $pagoD, $saldoNuevo, $saldo, $consumosDescStr, $contentMovi, $datos, $totalConsumos);

                $i++;
            }
            unset($sociosObj);

            //ROW TOTAL
            $report->addRow(array(
                "#".$num,//0
                "",//1
                "",//2
                "",//3
                "",//4
                $totales['saldoAnt'],
                $totales['saldoNuevo'],
                $totales['pagos2'],
                $totales['pagos3'],
                $totales['saldo'],
            ));

            unset($estadoCuentaObj);

            $report->finish();
            $fileName = $report->outputToFile('public/temp/estado_cuenta_consolidado');
            
            return $fileName;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
