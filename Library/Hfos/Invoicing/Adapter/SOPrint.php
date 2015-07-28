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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

Core::importFromLibrary('Hfos/Invoicing','InvoicingException.php');

/**
* Class InvoicingReport thats make the reports of invoicer
*
* Print Invoicers
* 
*/
set_time_limit(0);

		
class InvoicingSOPrint extends UserComponent {

	/**
	* @var ActiveRecordTransaction
	*/
	private $_transaction;


	public function __construct(){
		require_once 'Library/Mpdf/mpdf.php';
		$this->_transaction = TransactionManager::getUserTransaction();
	}


	private function _mainIni(){
		$html = '<html lang="es-CO">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
			<title>Factura de Venta '.$numeroFactura.'</title>
			<style type="text/css">
			body {
				font-family: Helvetica;
			}
			.datos-wepax {
				color: #AFAEAD;
			}
			.numero-factura {
				font-size: 20px;
			}
			.datos-consec {
				font-size: 10px;
				padding: 15px;
			}
			.resumen-factura {
				border-bottom: 1px solid #ababab;
				border-right: 1px solid #ababab;
			}
			.resumen-factura th {
				border-top: 1px solid #ababab;
				border-left: 1px solid #ababab;
				padding: 3px;
				background: #fafafa;
				font-size: 12px;
			}
			.resumen-factura td {
				border-top: 1px solid #ababab;
				border-left: 1px solid #ababab;
				padding: 3px;
				font-size: 12px;
			}
			.resumen-factura2 {
				border-bottom: 0px solid #ababab;
				border-right: 0px solid #ababab;
			}
			.resumen-factura2 th {
				border-top: 0px solid #ababab;
				border-left: 0px solid #ababab;
				padding: 3px;
				background: #fafafa;
				font-size: 12px;
			}
			.resumen-factura2 td {
				border-top: 0px solid #ababab;
				border-left: 0px solid #ababab;
				padding: 3px;
				font-size: 12px;
			}
			.paragraph {
				padding: 5px;
			}
			.resumen-factura td.total-label {
				font-size: 18px;
			}
			.resumen-factura td.total-factura {
				font-size: 18px;
			}
			.firma {
				text-align: center;
				font-size: 11px;
				color: #AFAEAD;
			}
			.resumen-mail th {
				font-size: 11px;
			}
			.resumen-mail td {
				font-size: 11px;
			}
			.traspaso {
				font-size: 11px;
			}
			</style>
		</head>
		<body>';
		
		return $html;
	}

	/**
	* Metodo que genera el encabezado del invocier
	*
	* @param array $options
	* @return string $html
	*/
	private function _addHeader(&$options){
		
		//variables
		$terceroFactura = $options['terceroFactura'];
		$terceroEmpresa = $options['terceroEmpresa'];
		$empresa = $options['empresa'];
		$factura = $options['factura'];

		//Logo
		$srcLogo = 'http://'.$_SERVER['SERVER_NAME'].''.Core::getInstancePath().'img/backoffice/logo.png';
		$logo = '<img src="'.$srcLogo.'" alt="BackOffice Logo" width="100" />';
		if(isset($options['showLogo']) && $options['showLogo']==false){
			$logo = '';
		}

		//Nit
		$empresa = $options['empresa'];
		$nit = $empresa->getNit();
		if(isset($options['showNit']) && $options['showNit']==false){
			$nit = '';
		}
		$nits = EntityManager::get('Nits')->findFirst(array('conditions'=>'nit="'.$nit.'"'));
		if($nits==false){
			throw new InvoicingException('El nit de la empresa no existe en terceros');
		}

		//Validacion de nit en socios
		$identificacion = trim($factura->getNit()); 
		$socios = EntityManager::get('Socios')->findFirst(array("identificacion='{$identificacion}'"));
		if ($socios==false) {
			throw new InvoicingException("No existe un socio con nit '{$identificacion}'");
		}
		$options['nombreSocios'] = utf8_encode($socios->getNombres()).' '.utf8_encode($socios->getApellidos());			

		//Razon Social
		$razonSocial = $nits->getNombre();
		if(isset($options['showRazonSocial']) && $options['showRazonSocial']==false){
			$razonSocial = '';
		}

		//Direccion
		$direccion = $terceroEmpresa->getDireccion();
		if(isset($options['showDireccion']) && $options['showDireccion']==false){
			$direccion = '';
		}

		//Telefono
		$telefono = $terceroEmpresa->getTelefono();
		if(isset($options['showTelefono']) && $options['showTelefono']==false){
			$telefono = '';
		}

		//Ciudad
		$locciu = $nits->getLocciu();
		$location = BackCacher::getLocation($locciu);
		if($location==false){
			throw new InvoicingException('El locciu de empresa no existe en location');
		}
		//$ciudad = utf8_encode($location->getName().' / '.$location->getZone()->getName());
		$ciudad = ($location->getName().' / '.$location->getZone()->getName());
		if(isset($options['showCiudad']) && $options['showCiudad']==false){
			$ciudad = '';
		}
		$options['ciudad'] = $ciudad;

		//Nota factura
		$notaFactura = $factura->getNotaFactura();
		if(isset($options['showNotaFactura']) && $options['showNotaFactura']==false){
			$notaFactura = '';
		}
		$options['notaFactura'] = $notaFactura;

		//Nota Ica
		$notaIca = $factura->getNotaIca();
		if(isset($options['showNotaIca']) && $options['showNotaIca']==false){
			$notaIca = '';
		}
		$options['notaIca'] = $notaIca;

		//Si muetsra o no direccion y telefono
		$showDirTelFactura = Settings::get('show_dir_tel_factura', 'SO');
		if (!$showDirTelFactura) {
			throw new InvoicingException('No se ha definido si se desea ver o no la dirección y teléfono del socio en la factura');
		}

		$more = '';
		if ($showDirTelFactura=='S') {
			$more = '
			<tr>
				<td align="left"><b>Dirección:</b> '.utf8_encode($socios->getDireccionCasa()).'</td>
			</tr>
			<tr>
				<td align="left"><b>Teléfono:</b> '.utf8_encode($socios->getTelefonoCasa()).'</td>
			</tr>';
		}

		$options['numeroFactura'] = $factura->getNumero();
		$options['numeroAccion'] = $socios->getNumeroAccion();
		
		$html = '<div class="paragraph">
			<table width="100%" class="resumen-factura2" cellspacing="0" cellpadding="0">
				<tr>
					<td align="right" class="datos-empresa" colspan="2">
						<h1>Factura de Venta Nro.<span style="color:red">'.$factura->getNumero().'</span> </h1>
					</td>
				</tr>
				<tr>
					<td>
						'.$logo.'
					</td>
					<td align="right">
						<b>Factura de Venta</b>
						<div class="firma">
							CONSECUTIVO AUTORIZADO FACTURACIÓN POR COMPUTADOR SEGUN RESOLUCIÓN No. '.$factura->getResolucion().'
							DEL '.$factura->getFechaResolucion()->getLocaleDate('medium').' NUMERACIÓN AUTORIZADA
							'.$factura->getPrefijo().sprintf('%07s', $factura->getNumeroInicial()).' AL '.$factura->getPrefijo().sprintf('%07s', $factura->getNumeroFinal()).'</b>
						</div>

						<br/>
						<b>Periodo Facturado:</b> '.$factura->getFechaEmision()->getPeriod().'<br/>
						<b>Fecha de Factura:</b> '.$factura->getFechaEmision()->getDate().'<br/>
						<b>Fecha Limite de Pago:</b> '.$factura->getFechaVencimiento()->getDate().'

					</td>
				</tr>
				<tr>
					<td>
						<table align="left" class="resumen-factura2" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left"><b>Nombre:</b> '.$options['nombreSocios'].'</td>
							</tr>
							<tr>
								<td align="left"><b>NIT/Cedula:</b>'.$factura->getNit().'</td>
							</tr>
							<tr>
								<td align="left"><b>Derecho-Social:</b>'.$options['numeroAccion'].'</td>
							</tr>
							'.$more.'
						</table>
					</td>
					<td align="right" class="datos-empresa">
						<b>'.$razonSocial.'</b><br/>
						<b>Nit: '.$nit.'</b><br/>
						'.$notaFactura.'<br/>
						'.$notaIca.'<br/>
						Dirección: '.$direccion.'<br/>
						Teléfono: '.$telefono.', 
						Ciudad: '.$ciudad.'<br/>
					</td>
				</tr>
			</table>
		</div>
		';

		return $html;
	}


	/**
	* Return seprator html
	* @return string $html
	*/
	private function _addSeparator(){
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

	private function _getAlign($alignCode=''){
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
	* 	'headers' 	=> array( // type'=> 'int/money/string', 'align=>'C/L/R' (center/Left/right)
	*		array('field'=>'codigo', 'name'=>'Código Ref.'	, 'type'=>'string'	, 'align'=>'L'),
	*		array('field'=>'descripcion', 'name'=>'Descripción'	, 'type'=>'string'	, 'align'=>'L'),
	*		array('field'=>'cant', 'name'=>'Cantidad'	, 'type'=>'int'		, 'align'=>'R'),
	*		array('field'=>'valUni', 'name'=>'Valor Uni.'	, 'type'=>'money'	, 'align'=>'R', 'totalizer' => true),
	*		array('field'=>'valTot', 'name'=>'Valor Total'	, 'type'=>'money'	, 'align'=>'R', 'totalizer' => true),
	*		array('field'=>'descuento', 'name'=>'% Desc.'		, 'type'=>'decimal'	, 'align'=>'R', 'width'=>'10%', 'totalizer' => true)
	*	),
	*	'rows'		=> array(
	*		array(1, 'uno', 'A', ...),
	*		array(2, 'dos', 'I', ...),
	*		...
	* 	)
	* @return string $html
	*/
	private function _addContent(&$options, $facturaId){

		//validamos opciones de content
		if(!isset($options['headers']) || !count($options['headers'])){
			throw new InvoicingException('_addContent: Es necesario ingresar el index headers en opciones de content');
		}
		$headers = $options['headers'];
		
		//$facturaId = $options['facturaId'];

		if(!isset($options['rows'][$facturaId]) || !count($options['rows'][$facturaId])){
			throw new InvoicingException('_addContent: Es necesario ingresar el index rows['.$facturaId.'] en opciones de content/ '.print_r($options['rows'],true).', '.print_r($options['headers'],true));
		}
		$rows = $options['rows'][$facturaId];


		$html = '<div class="paragraph">
			<table class="resumen-factura" cellspacing="0" cellpadding="0" width="95%" align="center">
				<tr>';
		
		foreach($headers as $head){
			$width = '';
			if(isset($head['width'])){
				$width = ' width="'.$head['width'].'"';
			}
			$html .= '<th '.$width.'>'.$head['name'].'</th>'.PHP_EOL;
		}
						
		$html .= '</tr>';

		$totalizer[$facturaId] = array();
		/**
		*	'codigo' 		=> $detalle->getItem(),
		*	'descripcion' 	=> $detalle->getDescripcion(),
		*	'cant' 			=> $detalle->getCantidad(),
		*	'valUni' 		=> $detalle->getValor(),
		*	'valTot' 		=> $detalle->getTotal(),
		*	'descuento' 	=> $detalle->getDescuento()
		*/
		foreach($rows as $row){
			$html .= '<tr>';

			//generamos las rows de contenido
			foreach($headers as $head){

				//attributes
				$width='';
				if(isset($head['width'])){
					$width=' width="'.$head['width'].'"';
				}
				
				$align='left';
				$value = $row[$head['field']];
				$valueOri = $value;

				if(isset($head['pk']) && $head['pk']==true && !$valueOri){
					continue;
				}

				if(isset($head['type'])){
					
					//validamos align
					if(isset($head['align'])){
						$align = $this->_getAlign($head['align']);
					}
					
					//define by type
					switch($head['type']){
						
						case 'int':
							$align='right';
							$value = Currency::number($value, 0);
							break;

						case 'decimal':
							$align='right';
							$value = Currency::number($value);
							break;

						case 'money':
							$align='right';
							$value = Currency::money($value);
							break;
					}

				}

				$html .= '<td align="'.$align.'" '.$width.'>'.$value.'</td>'.PHP_EOL;

				//Totalizer
				if(isset($head['totalizer']) && $head['totalizer']!=false){
					$totalizer[$facturaId][$head['field']] += $valueOri;
				}

			}
			
		}		

		
		//TOTALES
		$html.='<tr>';
		foreach($options['headers'] as $head){

			if(isset($head['totalizer']) && $head['totalizer']!=false){
				
				$align = 'right';
				$value = $totalizer[$facturaId][$head['field']];

				if(isset($head['type'])){
				
					//define by type
					switch($head['type']){
						
						case 'int':
							$value = Currency::number($value, 0);
							break;

						case 'decimal':
							$value = Currency::number($value);
							break;

						case 'money':
							$value = Currency::money($value);
							break;
					}
				
				}

				$html .= '<td align="right">'.$value.'</td>'.PHP_EOL;				
			
			} else {

				$html .= '<td align="right">&nbsp;</td>'.PHP_EOL;

			}

		}
		$html .= '</tr>';

		//FIN CONTENT
		$html.='</table>
		</div>';

		return $html;
	}

	
	private function _makeFacturaContent(&$options, $facturaId){

		//throw new InvoicingException($facturaId);

		/*if(!isset($options['facturaId']) || $options['facturaId']<=0){
			throw new InvoicingException("La factura con ID $facturaId es necesaria");
		}
		*/

		$facturaId = $options['facturaId'];

		$factura = $this->Facturas->findFirst(array('conditions'=>'id='.$facturaId));
		if($factura==false){
			$factura = $this->Facturas->findFirst(array('conditions'=>"nit='{$options['nit']}' AND fecha_emision='{$options['fechaFactura']}'"));
			if($factura==false){
				throw new InvoicingException("La factura con ID $facturaId no existe");
			}
		}
		$options['factura'] = $factura;

		$empresa = $options['empresa'];

		$terceroEmpresa = BackCacher::getTercero($empresa->getNit());
		if($terceroEmpresa==false){
			throw new InvoicingException("La empresa no existe como un tercero");
		}
		$options['terceroEmpresa'] = $terceroEmpresa;

		$terceroFactura = BackCacher::getTercero($factura->getNit());
		if($terceroFactura==false){
			throw new InvoicingException("El tercero al que se generó la factura no existe");
		}
		$options['terceroFactura'] = $terceroFactura;

		$numeroFactura = $factura->getPrefijo().sprintf('%07s', $factura->getNumero());

		if(!isset($options['rows'][$facturaId])){
			$options['rows'][$facturaId] = array();	
		}

		$html .= '	
			<!--HEADER-->
			'.$this->_addHeader($options).'

			<!--SEPARATOR-->
			'.$this->_addSeparator().'

			<!--CONTENT-->';
			//if(!isset($options['headers'])){

				if(isset($options['apps']) && $options['apps']=='SO'){

					//Socios
					$options['headers'] = array(
						//array('field'=>'codigo', 'name'=>'Código C. Fijo'	, 'type'=>'string'	, 'align'=>'R', 'width'=>'15%', 'pk'=>true),
						array('field'=>'descripcion', 'name'=>'Descripción', 'width'=>'40%'	, 'type'=>'string'	, 'align'=>'L'),
						array('field'=>'valUni', 'name'=>'Valor Uni.'	, 'type'=>'money'	, 'align'=>'R', 'totalizer' => true),
						array('field'=>'valIva', 'name'=>'Valor IVA'	, 'type'=>'money'	, 'align'=>'R', 'totalizer' => true),
						array('field'=>'valTot', 'name'=>'Valor Total'	, 'type'=>'money'	, 'align'=>'R', 'totalizer' => true)
					);

					$total = 0;
					foreach ($this->FacturasDetalle->find("facturas_id='$facturaId'") as $detalle)
					{
						$options['rows'][$facturaId][] = array(
							//'codigo' 		=> $detalle->getItem(),
							'descripcion' 	=> utf8_encode($detalle->getDescripcion()),
							'valUni' 		=> LocaleMath::round( $detalle->getValor()),
							'valIva' 		=> LocaleMath::round($detalle->getIva()),
							'valTot' 		=> LocaleMath::round($detalle->getTotal())
						);
						$total += $detalle->getTotal();
					}

				} else {

					//Facturador
					$options['headers'] = array(
						//array('field'=>'codigo', 'name'=>'Código Ref.'	, 'type'=>'string'	, 'align'=>'R', 'width'=>'15%', 'pk'=>true),
						array('field'=>'descripcion', 'name'=>'Descripción'	, 'type'=>'string'	, 'align'=>'L'),
						array('field'=>'valUni', 'name'=>'Valor Uni.'	, 'type'=>'money'	, 'align'=>'R', 'totalizer' => true),
						array('field'=>'valTot', 'name'=>'Valor Total'	, 'type'=>'money'	, 'align'=>'R', 'totalizer' => true),
						array('field'=>'descuento', 'name'=>'% Desc.'		, 'type'=>'decimal'	, 'align'=>'R', 'width'=>'10%', 'totalizer' => true)
					);

					$total = 0;
					foreach ($this->FacturasDetalle->find("facturas_id='$facturaId'") as $detalle)
					{
						$options['rows'][$facturaId][] = array(
							//'codigo' 		=> $detalle->getItem(),
							'descripcion' 	=> utf8_encode($detalle->getDescripcion()),
							'valUni' 		=> $detalle->getValor(),
							'valTot' 		=> $detalle->getTotal(),
							'descuento' 	=> $detalle->getDescuento()
						);
						$total += $detalle->getTotal();
					}

				}
			//}

			//añade el contenido
			if (isset($options['rows'][$facturaId]) && count($options['rows'][$facturaId])>0) {
				$html .= $this->_addContent($options, $facturaId);
			}

			$options['totalFactura'] = $total;

			$options2 = array();

			//Verificamos si hay financiacion
			$showFinanciacion = Settings::get('show_financiacion_socios', 'SO');
			if (!$showFinanciacion) {
				throw new InvoicingException('No se ha configurado si se desea ver o no la financiación en la factura en configuración');
			}

			//Verificamos si mostramos recargo con mora
			$showRecargoMora = Settings::get('show_recargo_mora', 'SO');
			if (!$showFinanciacion) {
				throw new InvoicingException('No se ha configurado si se desea ver o no el regargo por mora en la factura en configuración');
			}
			
			$options['totalFacturado'] = $total;
			$periodoSocios = EntityManager::get('Periodo')->findFirst(array('conditions'=>"fecha_factura='{$factura->getFechaEmision()}'"));
			if ($periodoSocios==false) {
				throw new InvoicingException("No hay un periodo que este registrando la fecha de la factura actual '{$factura->getFechaEmision()}'", 1);
			}
			$moraPeriodo = $periodoSocios->getInteresesMora();
			$options['Mora'] = ($options['totalFacturado'] * $moraPeriodo / 100);
			$options['totalFacturadoConMora'] = $options['Mora'] + $options['totalFacturado'];

			if ($showFinanciacion=='S') {
				
				$financiacionObj = EntityManager::get('Financiacion')->find(array('conditions'=>'factura_id='.$factura->getId()));
				
				foreach ($financiacionObj as $financiacion) 
				{
					
					$options2['headers'] = array(
						array('field'=>'descripcion', 'name'=>'Descripción', 'type'=>'text', 'align'=>'L'),
						array('field'=>'valor', 'name'=>'Valor', 'type'=>'money', 'align'=>'R', 'totalizer' => true),
						array('field'=>'mora', 'name'=>'Mora', 'type'=>'money', 'align'=>'R', 'totalizer' => true),
						array('field'=>'total', 'name'=>'Total', 'type'=>'money', 'align'=>'R', 'totalizer' => true),
					);
	
					$options2['rows'][$facturaId][] = array(
						'descripcion' 	=> $financiacion->getDescripcion(),
						'valor' 		=> LocaleMath::round($financiacion->getValor()),
						'mora' 			=> LocaleMath::round($financiacion->getMora()),
						'total' 		=> LocaleMath::round($financiacion->getTotal())
					);
	
					$options['totalFacturado'] += LocaleMath::round($financiacion->getTotal());
					$options['valorFinanciacion'] += LocaleMath::round($financiacion->getTotal());
					unset($financiacion);
				}
				unset($financiacionObj);
				
				if (isset($options2['headers'])) {
					if (isset($options['rows'][$facturaId]) && count($options['rows'][$facturaId])>0) {
						$html .= $this->_addSeparator();
					}

					$html .= $this->_addContent($options2, $facturaId);
				}
					
			}
			
			$options['totalFacturaConMora'] = $options['Mora'] + $options['totalFactura'];

			$formasPago = array();
			
			foreach($this->FacturasPagos->find("facturas_id='$facturaId'") as $facturaPago)
			{
				$formasPago[] = $facturaPago->getDescripcion();
				unset($facturaPago);
			}

			if(count($formasPago)>0){
				$locale = Locale::getApplication();
				$formasPago = 'Formas de Pago: '.$locale->getConjunction($formasPago);
			} else {
				$formasPago = 'Forma de Pago: '.$formasPago[0];
			}

			$html .= $this->_addSeparator();

			//RESUMEN FACTURA
			$resumen = '';			
			$resumenSO = Settings::get('resumen_factura', 'SO');
			
			if ($resumenSO) {
				$resumen = '<b>Mensaje:</b><br/>'.$resumenSO;
			}

			//RESUMEN PIE PAGINA DE FACTURA
			$resumenPie = '';			
			$resumenPieSO = Settings::get('resumen_factura_pie', 'SO');
			
			if ($resumenPieSO) {
				$resumenPie = $resumenPieSO;
			}

			$html .= '<div class="paragraph">
				<table width="100%">
					<tr>
						<td width="50%" align="left" valign="top" class="traspaso">
							'.$resumen.'
						</td>
						<td width="50%" align="right">
							<table class="resumen-factura" cellspacing="0" cellpadding="0" align="right">
								<tr>
									<td align="right"><b>Venta Gravada 16%</b></td>
									<td align="right">'.Currency::money($factura->getVenta16()).'</td>
								</tr>
								<tr>
									<td align="right"><b>Ingresos Terceros</b></td>
									<td align="right">'.Currency::money($factura->getVenta10()).'</td>
								</tr>
								<tr>
									<td align="right"><b>Venta No Gravada</b></td>
									<td align="right">'.Currency::money($factura->getVenta0()).'</td>
								</tr>
								<tr>
									<td align="right"><b>IVA 16%</b></td>
									<td align="right">'.Currency::money($factura->getIva16()).'</td>
								</tr>
								<tr>
									<td align="right" ><b>Total Facturado</b></td>
									<td align="right" >'.Currency::money(LocaleMath::round($options['totalFacturado'], 0)).'</td>
								</tr>';
								if ($showRecargoMora=='S') {
									$html .= '
									<tr>
										<td align="right" ><b>Total Con Mora</b></td>
										<td align="right" >'.Currency::money(LocaleMath::round($options['totalFacturadoConMora'], 0)).'</td>
									</tr>';
								
									if ($options['totalFactura']!=$options['totalFacturado']){
										$html .= '<tr>
											<td align="right" ><b>Total A Pagar</b></td>
											<td align="right" >'.Currency::money(LocaleMath::round($options['totalFactura'], 0)).'</td>
										</tr>
										<tr>
											<td align="right" ><b>Total A Pagar Con Mora</b></td>
											<td align="right" >'.Currency::money(LocaleMath::round($options['totalFacturadoConMora']+$options['valorFinanciacion'], 0)).'</td>
										</tr>';
									}		
								}
					$html.= '</table>
						</td>
					</tr>
				</table>
			</div>';

			//Verificamos si mostramos recargo con mora
			$showCupoPago = Settings::get('show_cupo_pago', 'SO');
			if (!$showCupoPago) {
				throw new InvoicingException('No se ha configurado si se desea ver o no el cupon de pago en la factura en configuración');
			}

			if ($showCupoPago=='S') {
				//SEPARATOR
				$html .= $this->_addSeparator();

				////////////////
				//CUPO DE PAGO
				/////////////////

				$recargoDay = new Date($factura->getFechaVencimiento()->getDate());
				$recargoDay->addDays(1);

				$html .= '
				<div style="width:100%;" >
					<table width="100%"  class="resumen-factura2" cellspacing="0" cellpadding="0" align="center">
						<tr>
							<td align="center"><b>CUPON DE PAGO</b></td>
						</tr>
					</table>
					<table width="100%" class="resumen-factura2" cellspacing="0" cellpadding="0" >
						<tr>
							<td width="25%"><b>Numero Interno de Documento:</b></td>
							<td width="25%">'.$options['numeroFactura'].'</td>
							<td><b>D/Social:</b></td>
							<td>'.$options['numeroAccion'].'</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><b>Nombre:</b></td>
							<td>'.$options['nombreSocios'].'</td>
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
													<td>'.$factura->getFechaEmision()->getPeriod().'</td>
												</tr>
												<tr>
													<td><b>Fecha de Factura:</b></td> 
													<td>'.$factura->getFechaEmision()->getDate().'</td>
												</tr>
												<tr>
													<td><b>Fecha Limite de Pago:</b></td> 
													<td>'.$factura->getFechaVencimiento()->getDate().'</td>
												</tr>
												<tr>
													<td><b>TOTAL A PAGAR ANTES DE:</b></td>
													<td>'.$factura->getFechaVencimiento()->getDate().'</td>
												</tr>';
											if ($showRecargoMora=='S') {
												$html .= '
												<tr>
													<td><b>PAGUE CON RECARGO DESDE:</b></td>
													<td>'.$recargoDay->getDate().'</td>
												</tr>';
											}
											$html .= '
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

			//RESUMEN PIE DE FACTURA
			$html .= '<div class="firma">
				<br/>
				'.$resumenPie.'
			</div>
			';

			$html .= '<div class="firma">
				<br/>
				IMPRESO POR '.$empresa->getNombre().' (NIT. '.$empresa->getNit().')
			</div>
			';

			return $html;
	}

	private function _mainFin(){
		$html.='</body></html>';

		return $html;
	}

	/**
	* Print a Invoicer
	*
	* @param accary $options
	* @return string $fileName
	*/
	public function getPrint(&$options)
	{
			
		//Si no se envia nada busque y genere ttodas la facturas
		if(!isset($options['facturas']) || $options['facturas']<=0 || !is_array($options['facturas'])){

			$options['facturas'] = array();
			$facturasObj = EntityManager::get('Facturas')->setTransaction($this->_transaction)->find(array('conditions'=>"estado<>'I'"));

			foreach($facturasObj as $factura){
				$options['facturas'][] = $factura->getId();
			}

		}

		$facturas = $options['facturas'];

		$empresa = BackCacher::getEmpresa();
		if($empresa==false){
			throw new InvoicingException("No existe la empresa que generó la factura");
		}
		$options['empresa'] = $empresa;

		$pdf = new mPDF('win-1252', 'letter');
		//$pdf = new mPDF('utf-8', 'letter');
		$pdf->SetDisplayMode('fullpage');
		$pdf->tMargin = 10;
		$pdf->lMargin = 10;

		//CSS AND HTML BODY
		$html = $this->_mainIni();
		$n=0;
		$countFacturas = count($facturas);
		foreach ($facturas as $facturaId)
		{

			$factura = EntityManager::get('Facturas')->setTransaction($this->_transaction)->findFirst($facturaId);

			if($factura==false){
				throw new InvoicingException("No existe la factura con id ".$facturaId);	
			}

			$options['facturaId'] = $facturaId;
			
			if(!$facturaId){
				throw new InvoicingException('Error no hay $facturaId: '.$facturaId);
			}

			//generamos body de reporte (factura)
			$html .= $this->_makeFacturaContent($options, $facturaId);

			if($countFacturas>1){
				$html .= '<pagebreak />';
			}

			unset($option['rows'][$facturaId]);

			$n++;

		} 

		$html .= $this->_mainFin();

		if($n>1){
			$numeroFactura = 'periodo';	
		}else{
			$numeroFactura = $options['facturaId'];
		}
		unset($options);

		//UTF8
		$html = mb_convert_encoding($html, mb_detect_encoding($html), 'UTF-8');

		//iconv latin1 to utf8
		//$html = iconv('latin1', 'UTF-8', $html);

		//mysql
		//ALTER TABLE <table_name> CONVERT TO CHARACTER SET utf8;	

		//out
		//echo $html;
		$fileName = false;
		if (!empty($html)) {
			$pdf->writeHTML($html);
			$fileName = 'factura-'.$numeroFactura.'-'.mt_rand(1000,9999).'.pdf';
			$pdf->Output('public/temp/'.$fileName);
			//readfile('public/temp/'.$fileName);
		}
		return $fileName;
		
	}


}	