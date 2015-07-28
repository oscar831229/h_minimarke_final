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

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
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
				'message' => 'El comprobante de ventas no está configurado'
			);
		}

		$comprobIngresos = Settings::get('comprob_ingresos');
		if (!$comprobIngresos) {
			return array(
				'status' => 'FAILED',
				'message' => 'El comprobante de ingresos no está configurado'
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

			$data = array(
				sprintf('%013s', substr($movi->getNit(), 0, 13)),
				'000',
				$tipo,
				sprintf('%- 60s', substr($tercero->getNombre(), 0, 60)),
				$contacto,
				sprintf('%- 100s', substr($tercero->getDireccion(), 0, 100)),
				sprintf('%011s', substr($tercero->getTelefono(), 0, 11)),
			);

			$line = join('', $data);
			fwrite($fp,  $line . PHP_EOL);
		}

		return array(
			'status' => 'OK',
			'file' => $fileName
		);

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
			'ciudad' => '0000',
			'zona' => '000',
			'bodega' => '0000',
			'ubicacion' => '000',
			'cantidad' => '000000000000000',
			'tipdoc' => 'R',
    		'comcru' => 'R01',
    		'numcru' => '00000000000',
    		'seccru' => '000',
    		'forpag' => '0000',
    		'codban' => '00'
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
		$data['cuenta'] = sprintf('%010s', substr($movi->getCuenta(), 0, 10));
		$data['fecha'] = $fechaMovi;
		$data['centro'] = '0'.substr($movi->getCentroCosto(), 0, 3);
		$data['subcentro'] = substr($movi->getCentroCosto(), 3, 2);
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
		foreach ($fields as $field) {
			if (isset($data[$field])) {
				$row[] = $data[$field];
			} else {
				$row[] = $field;
			}
		}

		$line = join('', $row);
		fwrite($fp,  $line . PHP_EOL);
	}

}
