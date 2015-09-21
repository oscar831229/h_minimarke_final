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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Interface_SiigoController
 *
 * Libro Auxiliar
 *
 */
class Interface_SiigoController extends ApplicationController
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

		$comprobVentas = Settings::get('comprob_ventas');
		if ($comprobVentas) {
			Tag::displayTo('fecha', (string) $this->Movi->maximum(
				array('fecha', 'conditions' => "comprob = '$comprobVentas'")
			));
		}

		$this->setParamToView('message', 'Indique los parÃ¡metros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');

		$fecha = $this->getPostParam('fecha', 'date');
		if (!$fecha) {
			return array(
				'status' => 'FAILED',
				'message' => 'La fecha es requerida'
			);
		}

		$comprobVentas = Settings::get('comprob_ventas');
		if (!$comprobVentas) {
			return array(
				'status' => 'FAILED',
				'message' => 'El comprobante de ventas no estÃ¡ configurado'
			);
		}

		$comprobIngresos = Settings::get('comprob_ingresos');
		if (!$comprobIngresos) {
			return array(
				'status' => 'FAILED',
				'message' => 'El comprobante de ingresos no estÃ¡ configurado'
			);
		}

		$tipo = $this->getPostParam('tipo', 'onechar');

		if ($tipo == 'F') {
			return $this->_processFacturacion($fecha, $comprobVentas, $comprobIngresos);
		} else {
			return $this->_processTerceros($fecha, $comprobVentas, $comprobIngresos);
		}
	}

	protected function _processTerceros($fecha, $comprobVentas, $comprobIngresos)
	{

		$fechaMovi = substr($fecha, 0, 4).substr($fecha, 5, 2).substr($fecha, 8, 2);
		$fechaFile = substr($fecha, 2, 2).substr($fecha, 5, 2).substr($fecha, 8, 2);

		$fileName = 'temp/TD'.$fechaFile.'.txt';
		$fp = fopen('public/'.$fileName, 'w');

		$contacto = str_repeat(' ', 50);

		$conditions = "(comprob = '$comprobVentas' OR comprob = '$comprobIngresos') AND fecha = '$fecha'";
		foreach ($this->Movi->find(array($conditions, 'order' => 'numero_doc,comprob')) as $movi) {

			if ($movi->getCuenta() == '13050502') {
				continue;
			}

			$cuenta = BackCacher::getCuenta($movi->getCuenta());

			if ($cuenta->getPideNit() != 'S') {
				continue;
			}

			$tercero = BackCacher::getTercero($movi->getNit());

			switch ($tercero->getLista()) {
				case 1:
					$tipo = 'P';
					break;
				case 2:
					$tipo = 'C';
					break;
				case 3:
					$tipo = 'O';
				default:
					$tipo = 'P';
			}

			$locciu = $tercero->getLocciu();
			$tipoDoc = $this->getTipodoc($tercero->getTipodoc());

			$data = array(
				sprintf('%013s', substr($movi->getNit(), 0, 13)),
				'000',
				$tipo,
				sprintf('%- 60s', substr($tercero->getNombre(), 0, 60)),
				sprintf('%- 50s', substr($contacto, 0, 50)),
				sprintf('%- 100s', substr($tercero->getDireccion(), 0, 100)),
				sprintf('%011s', substr($tercero->getTelefono(), 0, 11)),
				sprintf('%011s', '0'),//tel2
				sprintf('%011s', '0'),//tel3
				sprintf('%011s', '0'),//tel4
				sprintf('%011s', '0'),//FAX
				sprintf('%06s', substr($tercero->getApAereo(), 0, 6)),
				sprintf('%- 100s', ''), //email
				'M', //Sexo
				'4',
				$tipoDoc, //tipo documento
				sprintf('%011s', '0'),//Cupo credito
				sprintf('%02s', '1'),//Lista de precios
				sprintf('%04s', '0'),//Codigo del vendor
				sprintf('%04s', substr($this->getCodigoCiudad($locciu), 0, 4)),//Codigo de Ciudad
				sprintf('%011s', '0.00'),//PORCENTAJE DE DESCUENTO
				sprintf('%03s', '0'),//PERIODO DE PAGO
				sprintf('%- 30s', substr($movi->getDescripcion(), 0, 30)),//OBSERVACIÃ“N
				sprintf('%03s', substr($this->getCodigoPais($locciu), 0, 3)),//CÃ“DIGO DEL PAÃS
				'0',//DIGITO VERIFICACION
				'000',//CALIFICACIÃ“N
				sprintf('%05s', 0),//ACTIVIDAD ECONÃ“MICA
				sprintf('%04s', 0),//FORMA DE PAGO
				sprintf('%04s', 0),//COBRADOR
				sprintf('%02s', $this->getTipoPersona($tipoDoc)),//TIPO DE PERSONA
				'N',//DECLARANTE
				'N',//AGENTE RETENEDOR
				$this->getAutorretenedor($tercero->getAutoret()), //AUTORRETENEDOR
				'N',//BENEFICIARIO RETEIVA 60%
				'N',//AGENTE RETENEDOR ICA
				'A',//ESTADO
				'N',//ENTE PUBLICO
				sprintf('%010s', 0),//CODIGO ENTE PUBLICO
				'N',//ES RAZON SOCIAL
				sprintf('%- 15s', ''),//PRIMER NOMBRE
				sprintf('%- 15s', ''),//SEGUNDO NOMBRE
				sprintf('%- 15s', ''),//PRIMER APELLIDO
				sprintf('%- 15s', ''),//SEGUNDO APELLIDO
				sprintf('%- 20s', ''),//NUMERO DE IDENTIFICACION DEL EXTRANJERO
				'000',//RUTA
				sprintf('%- 10s', ''),//REGISTRO
				sprintf('%08s', 0),//FECHA VENCIMIENTO
				sprintf('%08s', 0),//FECHA CUMPLEAÃ‘OS
				'N',//TIPO DE SOCIEDAD
				sprintf('%- 10s', ''),//AUTORIZACION IMPRENTA
				sprintf('%- 11s', ''),//AUTORIZACION CONTRIBUYENTE
				sprintf('%02s', 99),//TIPO CONTRIBUYENTE
				sprintf('%- 50s', ''),//CONTACTO FACTURA
				sprintf('%- 90s', ''),//MAIL CONTACTO FACTURA 
			);

			$line = join('', $data);
			fwrite($fp,  $line . PHP_EOL);
		}

		return array(
			'status' => 'OK',
			'file' => $fileName
		);

	}

	private function getAutorretenedor($autoret)
	{
		if (!$autoret) {
			$autoret = 'N';
		}

		return $autoret;
	}

	private function getTipoPersona($tipo)
	{
		switch ($tipo) {
			case 'E':
				return '02';
				break;

			default:
				return '01';
				break;
		}
		return '01';
	}

	private function getCodigoPais($locciu)
	{
		return '001';
	}

	private function getCodigoCiudad($locciu)
	{
		return '0011';
	}

	private function getTipodoc($tipo)
	{
		$clase = '';
		switch ($tipo) {
			case 'A':
				$clase = 'N';
				break;
			case 'E':
				$clase = 'E';
				break;

			default:
				$clase = 'C';
				break;
		}

		return $clase;
	}

	protected function _processFacturacion($fecha, $comprobVentas, $comprobIngresos)
	{

		$fechaMovi = substr($fecha, 0, 4).substr($fecha, 5, 2).substr($fecha, 8, 2);
		$fechaFile = substr($fecha, 2, 2).substr($fecha, 5, 2).substr($fecha, 8, 2);

		$fileName = 'temp/FD'.$fechaFile.'.txt';
		$fp = fopen('public/'.$fileName, 'w');

		$data = array(
			'tipo' => 'F',
			'codigo' => '003',
			'sucursal' => '000',
			'producto' => '0000000000000',
			'codven' => '0001',
			'ciudad' => '0011',
			'zona' => '000',
			'bodega' => '0000',
			'ubicacion' => '000',
			'cantidad' => '000000000000000',
			'tipdoc' => 'R',
    		'comcru' => 'F01',
    		'numcru' => '0000000000',
    		'seccru' => '001',
    		'forpag' => '0001',
    		'codban' => '30'
		);

		$numeroDoc = null;
		$secuencia = null;

		$conditions = "(comprob = '$comprobVentas' OR comprob = '$comprobIngresos') AND fecha = '$fecha'";
		foreach ($this->Movi->find(array($conditions, 'order' => 'numero_doc,comprob')) as $movi) {
			$this->_processRow($fp, $numeroDoc, $secuencia, $fechaMovi, $data, $movi);
		}

		return array(
			'status' => 'OK',
			'file' => $fileName
		);
	}

	protected function _processRow($fp, &$numeroDoc, &$secuencia, $fechaMovi, $data, $movi)
	{
		if ($movi->getCuenta() == '13050502') {
			return;
		}

		$data['nit'] = sprintf('%013s', substr($movi->getNit(), 0, 13));
		$data['numero'] = sprintf('%011s', $movi->getNumeroDoc());
		//$data['cuenta'] = sprintf('%010s', substr($movi->getCuenta(), 0, 10));
		$data['cuenta'] = str_pad($movi->getCuenta(), 10, '0');
		$data['fecha'] = $fechaMovi;
		//$data['centro'] = sprintf('%03s', substr($movi->getCentroCosto(), 0, 3));
		$data['centro'] = '0100';
		$data['subcentro'] = sprintf('%03s', $movi->getCentroCosto());
		$subcentro = '000';
	        //$factura = $this->Factura->findFirst("numfac='{$movi->getNumeroDoc()}' AND cedula='{$movi->getNit()}'");		
		//if ($factura) {
			//$detfac = $this->Detfac->findFirst("numfac='' AND ");
			//$subcentro = $factura->;
		//}
		//$data['subcentro'] = $subcentro; 
		$data['desc'] = sprintf('%- 50s', substr($movi->getDescripcion(), 0, 50));
		$data['debcre'] = $movi->getDebcre();
		$data['valor'] = sprintf('%013s', (int) $movi->getValor()).'00';
		$data['basret'] = sprintf('%013s', (int) $movi->getBaseGrab()).'00';
		$data['feccru'] = $fechaMovi;

		if ($numeroDoc===null) {
			$numeroDoc = $movi->getNumeroDoc();
			$secuencia = 1;
		} else {
			if ($numeroDoc != $movi->getNumeroDoc()) {
				$numeroDoc = $movi->getNumeroDoc();
				$secuencia = 1;
			}
		}

		if ($numeroDoc) {
			$data['secuencia'] = sprintf('%05s', $secuencia++);
		} else {
			$data['secuencia'] = '00000';
		}

		$this->_writeRow($fp, $data);
	}

	protected function _writeRow($fp, $data)
	{

		$fields = array(
			'tipo',
			'codigo',
			'numero',
			'secuencia',
			'nit',
			'sucursal',
			'cuenta',
			'producto',
			'fecha',
			'centro',
			'subcentro',
			'desc',
			'debcre',
			'valor',
			'basret',
			'codven',
			'ciudad',
			'zona',
			'bodega',
			'ubicacion',
			'cantidad',
			'tipdoc',
			'comcru',
			'numcru',
			'seccru',
			'feccru',
			'forpag',
			'codban',
		);

		$row = array();
		$numcru = "";
		$seccru = "";
	
		foreach ($fields as $field) {

			if (isset($data[$field])) {

				$numcru = $field == "numero" ? $data[$field] : $data[$field];
         	                $seccru = $field == "secuencia" ? $data[$field] : $data[$field];
          
      	                        switch ($field) {
					case "seccru":
							$row[] = substr($data["secuencia"], 2);
						break;
					case "numcru":
							$row[] = $data["numero"];
						break;
					default:
							$row[] = $data[$field];
						break;
				}
                       
			} else {
				$row[] = $field;
			}
		}

		$line = join('', $row);
		fwrite($fp,  $line . PHP_EOL);
	}

}
