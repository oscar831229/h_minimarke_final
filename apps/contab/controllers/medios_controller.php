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
 * @copyright 	BH-TECK Inc. 2009-2013
 * @version		$Id$
 */

/**
 * MediosController
 *
 * Controlador para generar los medios magneticos
 *
 */
class MediosController extends ApplicationController
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

		$fecha = new Date();
		$fecha->diffYears(1);
		Tag::displayTo('fechaInicial', Date::getFirstDayOfYear($fecha->getYear()));
		Tag::displayTo('fechaFinal', Date::getLastDayOfYear($fecha->getYear()));

		$formatos = array();
		foreach ($this->Magfor->find(array('order' => 'codfor')) as $magfor) {
			$formatos[$magfor->getCodFor()] = $magfor->getCodFor() . ' : ' . utf8_encode($magfor->getNombre());
		}
		$this->setParamToView('formatos', $formatos);
		$this->setParamToView('message', 'Seleccione el formato y código para editar los campos y rangos de cuentas asociados');
	}

	private function _getDigitoVerificacion($nit)
	{
		$y = 0;
		$x = 0;
		$dv = 0;
		$z = strlen($nit);
		$nit = trim($nit);
		$vector = array(3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71);
		for ($i = 0; $i < $z; $i++) {
			$y = substr($nit, $i, 1);
			$x += ($y * $vector[$z - $i - 1]);
		}
		$dv = $x % 11;
		if ($dv > 1) {
			return 11 - $dv;
		} else {
			return $dv;
		}
	}

	private function _esEmpresa($nombre)
	{
	  	$empresas = array(
	  		'/ SA$/',
	  		'/ S A$/',
	  		'/ S\. A$/',
	  		'/ S\. A\.$/',
	  		'/ S\.A$/',
	  		'/ S\.A\.$/',
	  		'/ SA\.$/',
	  		'/ LTDA$/',
	  		'/ LTDA\.$/',
	  		'/ S\.AS$/',
	  		'/ S\.A\.S$/',
	  		'/ S\.A\.S\.$/',
	  		'/ SA\.S$/',
	  		'/ SA\.S\.$/',
	  		'/ SAS\.$/',
	  		'/ SAS$/',
	  		'/ EU$/',
	  		'/ E\.U$/',
	  		'/ CA$/',
	  		'/ C\.A$/',
	  		'/ C\.A\.$/',
	  		'/ E\.U\.$/',
	  		'/ EU\.$/',
	  		'/ C\.V\.$/',
	  		'/ C\.V$/',
	  		'/ CV$/',
	  		'/ LLC$/',
	  		'/ LLC\.$/',
	  		'/ INC$/',
	  		'/ INC\.$/',
	  		'/ S\.R\.L\.$/',
	  		'/ S\.R\.L$/',
	  		'/ E\.S\.P\.$/',
	  		'/ SRL$/',
	  		'/ SAC$/',
	  		'/TRAVEL/',
	  		'/VIAJES/',
	  		'/PRODUCTS/',
	  		'/EMPRESA/',
	  		'/UNIVERSIDAD/',
	  		'/TOURS/',
	  		'/INVERSIONES/',
	  		'/INSTITUTO/',
	  		'/FUNDACION/',
	  		'/HOTEL/',
	  		'/EMBAJADA/',
	  		'/GOBERNACION/',
	  		'/INSTITUCION/',
	  		'/ORGANIZACION/',
	  		'/LIMITADA/',
	  		'/ASOCIADOS/',
	  		'/ASOCIACION/',
	  		'/COOPERATIVA/',
	  		'/CONSTRUCTORA/',
	  		'/PAPELERIA/',
	  		'/SALUD/',
	  		'/CONFECCIONES/',
	  		'/PENSIONES/'
	  	);
		$nombre = trim($nombre);
	  	foreach ($empresas as $empresa) {
			if (preg_match($empresa . 'i', $nombre)) {
				return false;
			}
	  	}
	  	if (stripos($nombre, 'EXISTE')) {
	  		return false;
	  	}
	  	return true;
	}

	public function generarAction(){

		$isAjax = $this->getRequestInstance()->isAjax();
		if($isAjax){
			$this->setResponse('json');
		}

		/*$_POST['concepto'] = 1;
		$_POST['magfor'] = '1002';
		$_POST['fechaInicial'] = '2009-01-01';
		$_POST['fechaFinal'] = '2009-12-31';
		$_POST['tipo'] = 'xml';*/

		$codigoFormato = $this->getPostParam('magfor', 'int');
		if($codigoFormato>0){
			$formato = $this->Magfor->findFirst($codigoFormato);
			if ($formato == false) {
				return array(
					'status' => 'FAILED',
					'message' => 'El formato no existe'
				);
			}
			$campos = $this->Magcam->find("codfor='$codigoFormato'");
			if (count($campos)==0) {
				return array(
					'status' => 'FAILED',
					'message' => 'No se han definido los campos que lleva el formato'
				);
			}
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'Indique el formato a generar'
			);
		}

		$concepto = $this->getPostParam('concepto', 'int');
		if($concepto<=0){
			return array(
				'status' => 'FAILED',
				'message' => 'Indique el concepto del formato'
			);
		}

		Core::importFromLibrary('Hfos', 'Names/Names.php');

		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');
		list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);

		$empresa = $this->Empresa->findFirst();
		$terceroHotel = BackCacher::getTercero($empresa->getNit());
		if($terceroHotel==false){
			return array(
				'status' => 'FAILED',
				'message' => 'El hotel debe ser creado como un tercero'
			);
		} else {
			if (strlen($terceroHotel->getDireccion()) < 10) {
				return array(
					'status' => 'FAILED',
					'message' => 'La dirección del hotel es muy corta'
				);
			}
			if ($terceroHotel->getLocciu() <= 0) {
				return array(
					'status' => 'FAILED',
					'message' => 'La ciudad del hotel no es válida'
				);
			}

			$location = BackCacher::getLocation($terceroHotel->getLocciu());
			$codigoDane = sprintf('%05s', $location->getCodigoDane());
			$codigoDepartamentoDefecto = sprintf('%02s', substr($codigoDane, 0, 2));
			$codigoMunicipioDefecto = sprintf('%03s', substr($codigoDane, 2, 3));
			$codigoPaisDefecto = $location->getTerritory()->getCoddas();
		}

		$porTercero = false;
		$camposFormato = array();
		$camposPosicion = array();
		$tieneCampoTotal = false;
		foreach ($campos as $campo) {
			$camposFormato[$campo->getPosicion()] = $campo->getCampo();
			$camposPosicion[$campo->getCampo()] = $campo->getPosicion();
			if (in_array($campo->getCampo(), array('Apl1', 'Apl2', 'Nom1', 'Nom2'))) {
				$porTercero = true;
			} else {
				if ($tieneCampoTotal == false) {
					if ($campo->getCampo() == $formato->getCampo()) {
						$tieneCampoTotal = true;
					}
				}
			}
		}
		if ($tieneCampoTotal == false) {
			return array(
				'status' => 'FAILED',
				'message' => 'No se le definió al formato el campo general de totalización'
			);
		}

		$rangosCuentas = array();
		foreach ($this->Magcue->find("codfor='$codigoFormato'") as $cuentasCodigo) {
			$rangosCuentas[] = array(
				'codigo' => $cuentasCodigo->getCodigo(),
				'campo' => $cuentasCodigo->getCampo(),
				'cuentaInicial' => $cuentasCodigo->getCueini(),
				'cuentaFinal' => $cuentasCodigo->getCuefin()
			);
		}

		//la 1 es naturaleza deb, la 2 cr ,la 3 cr, la 4 cr , la 5 db , la 6 db l

		$datos = array();
		foreach ($rangosCuentas as $rangoCuenta) {

			$campo = $rangoCuenta['campo'];
			$codigo = $rangoCuenta['codigo'];

			$conditions = "fecha>='$fechaInicial' AND fecha<='$fechaFinal' AND cuenta>='{$rangoCuenta['cuentaInicial']}' AND cuenta<='{$rangoCuenta['cuentaFinal']}' AND nit<>'{$empresa->getNit()}'";

			//file_put_contents('a.txt', $conditions.PHP_EOL, FILE_APPEND);

			foreach ($this->Movi->find(array($conditions, 'columns' => 'nit,cuenta,valor,deb_cre,fecha')) as $movi) {
				if ($porTercero) {
					$cuenta = BackCacher::getCuenta($movi->getCuenta());
					if ($cuenta->getPideNit() == 'S') {
						$nit = $movi->getNit();
						if (!isset($datos[$nit][$codigo][$campo])) {
							$datos[$nit][$codigo][$campo] = 0;
						}
						$tipo = substr($movi->getCuenta(), 0, 1);
						switch ($tipo) {
							case '2':
							case '3':
							case '4':
								if ($movi->getDebCre() == 'D') {
									$datos[$nit][$codigo][$campo] -= LocaleMath::round($movi->getValor(), 0);
								} else {
									$datos[$nit][$codigo][$campo] += LocaleMath::round($movi->getValor(), 0);
								}
								break;
							default:
								if ($movi->getDebCre() == 'D') {
									$datos[$nit][$codigo][$campo] += LocaleMath::round($movi->getValor(), 0);
								} else {
									$datos[$nit][$codigo][$campo] -= LocaleMath::round($movi->getValor(), 0);
								}
								break;
						}
						//if ($nit == '811010001') {
						//	file_put_contents('a.txt', $nit."\t".$movi->getCuenta()."\t".$movi->getValor()."\t".$movi->getDebCre()."\t".$movi->getFecha().PHP_EOL, FILE_APPEND);
						//}
					}
				}
				unset($movi);
			}
			unset($rangoCuenta);
			unset($campo);
			unset($codigo);
		}

		file_put_contents('public/temp/medios.txt', print_r($datos, true));

		$campoTotal = $formato->getCampo();
		if ($porTercero == true) {
			$posicionCampo = $camposPosicion[$formato->getCampo()];
			$terceroMenores = $formato->getTermen();
			foreach ($datos as $numeroNit => $datosCodigo) {
				foreach ($datosCodigo as $codigo => $datosCampo) {
					if (isset($datosCampo[$campoTotal])){
						if ($datosCampo[$campoTotal] < $formato->getMinimo()) {
							//echo $numeroNit, ' ', Currency::number($datosCampo[$campoTotal]), ' ', Currency::number($formato->getMinimo()), '<br>';
							if (!isset($datos[$terceroMenores][$codigo])) {
								$datos[$terceroMenores][$codigo] = $datosCampo;
							} else {
								foreach ($datosCampo as $campo => $valor) {
									if (!isset($datos[$terceroMenores][$codigo][$campo])) {
										$datos[$terceroMenores][$codigo][$campo] = $valor;
									} else {
										$datos[$terceroMenores][$codigo][$campo]+=$valor;
									}
								}
							}
							if ($isAjax==false) {
								//echo $numeroNit, ' ', $codigo, '<br>';
							}
							unset($datos[$numeroNit][$codigo]);
						}
					}
				}
			}
		}

		$valorTotal = 0;
		$medios = array();
		$allErrores = array();
		if ($porTercero == true) {
			foreach ($datos as $numeroNit => $datosCodigo){
				$codigoDepartamento = $codigoDepartamentoDefecto;
				$codigoMunicipio = $codigoMunicipioDefecto;
				$codigoPais = $codigoPaisDefecto;
				$nit = BackCacher::getTercero($numeroNit);
				if ($nit == false) {
					$allErrores[] = 'EL TERCERO '.$numeroNit.' NO EXISTE';
					continue;
				} else {
					if ($nit->getTipodoc() != 31) {
						$nombrePartes = Names::getParts($nit->getNombre());
					}
					if ($nit->getLocciu() > 0) {
						$location = BackCacher::getLocation($nit->getLocciu());
						if ($location->getCodigoDane() > 0) {
							$codigoDane = sprintf('%05s', $location->getCodigoDane());
							$codigoDepartamento = sprintf('%02s', substr($codigoDane, 0, 2));
							$codigoMunicipio = sprintf('%03s', substr($codigoDane, 2, 3));
						} else {
							$codigoDepartamento = '';
							$codigoMunicipio = '';
						}
						$codigoPais = $location->getTerritory()->getCoddas();
					}
				}
				foreach ($datosCodigo as $codigo => $datosCampo) {
					$medio = array();
					foreach ($camposFormato as $posicion => $campo) {
						switch($campo){
							case 'Cpt':
								$medio[$posicion] = $codigo;
								break;
							case 'Tdoc':
								if ($nit->getTipodoc() <= 0) {
									$allErrores[] = 'EL TIPO DE DOCUMENTO DEL TERCERO '.$numeroNit.' ES INVÁLIDO';
								}
								if ($nit->getTipodoc() != 31) {
									if ($this->_esEmpresa($nit->getNombre()) != true) {
										$allErrores[] = 'EL TIPO DE DOCUMENTO DE  "' . $nit->getNombre() . '" (' . $numeroNit . ') NO CORRESPONDE A UNA EMPRESA';
									}
								}
								$medio[$posicion] = $nit->getTipodoc();
								break;
							case 'Nid':
								$medio[$posicion] = $nit->getNit();
								break;
							case 'Dv':
								$medio[$posicion] = '';
								if ($nit->getTipodoc() == 31) {
									$medio[$posicion] = $this->_getDigitoVerificacion($nit->getNit());
								}
								break;
							case 'Apl1':
								if ($nit->getTipodoc() != 31) {
									$medio[$posicion] = $nombrePartes['primerApellido'];
								} else {
									$medio[$posicion] = '';
								}
								break;
							case 'Apl2':
								if ($nit->getTipodoc() != 31) {
									$medio[$posicion] = $nombrePartes['segundoApellido'];
								} else {
									$medio[$posicion] = '';
								}
								break;
							case 'Nom1':
								if($nit->getTipodoc() != 31){
									$medio[$posicion] = $nombrePartes['primerNombre'];
								} else {
									$medio[$posicion] = '';
								}
								break;
							case 'Nom2':
								if ($nit->getTipodoc() != 31) {
									$medio[$posicion] = $nombrePartes['segundoNombre'];
								} else {
									$medio[$posicion] = '';
								}
								break;
							case 'Raz':
								if ($nit->getTipodoc() == 31) {
									$medio[$posicion] = $nit->getNombre();
								} else {
									$medio[$posicion] = '';
								}
								break;
							case 'Dir':
								if ($codigoPais==$codigoPaisDefecto) {
									if (strlen($nit->getDireccion()) > 10) {
										$medio[$posicion] = $nit->getDireccion();
									} else {
										$medio[$posicion] = $terceroHotel->getDireccion();
									}
								} else {
									$medio[$posicion] = '';
								}
								break;
							case 'Dpto':
								$medio[$posicion] = $codigoDepartamento;
								break;
							case 'Mun':
								$medio[$posicion] = $codigoMunicipio;
								break;
							case 'Pais':
								$medio[$posicion] = $codigoPais;
								break;
						}
					}
					foreach ($datosCampo as $campo => $valor) {
						if (isset($camposPosicion[$campo])) {
							$posicion = $camposPosicion[$campo];
							$medio[$posicion] = $valor;
							if($campoTotal==$campo){
								$valorTotal+=$valor;
							}
						}
					}
					foreach ($camposFormato as $posicion => $campo) {
						if (!isset($medio[$posicion])) {
							$medio[$posicion] = 0;
						}
					}
					ksort($medio);
					$medios[] = $medio;
				}
			}
		}

		/*if($porTercero>0){
			$lineaMenores = -1;
			$cuantiasMenores = 0;
			$posicionCampo = $camposPosicion[$formato->getCampo()];
			foreach($medios as $linea => $medio){
				if($medio[$posicionCampo]<$formato->getMinimo()){
					$cuantiasMenores+=$medio[$posicionCampo];
					unset($medios[$linea]);
				}
			}
			if($cuantiasMenores>0){
				$terceroMenores = BackCacher::getTercero($formato->getTermen());
				if($terceroMenores==false){
					return array(
						'status' => 'FAILED',
						'message' => 'No se ha configurado el tercero de cuantias menores del formato'
					);
				}
			}
		}*/

		if (count($allErrores) > 0) {

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('INCONSISTENCIAS MEDIOS MAGNÉTICOS FORMATO ' . $codigoFormato, array(
				'fontSize' => 16,
			   	'fontWeight' => 'bold',
			   	'textAlign' => 'center'
			));

			$report->setHeader(array($titulo));
			$report->setDocumentTitle('Incosistencias Medios Magnéticos');
			$report->setColumnHeaders(array(
				'NÚMERO',
				'NOVEDAD'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(1, 2, 3), new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->start(true);

			foreach($allErrores as $numero => $error){
				$report->addRow(array(
					$numero+1,
					$error
				));
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/medios');

			return array(
				'status' => 'FAILED',
				'message' => 'No se pudo generar el formato porque hay inconsistencias en la información',
				'url' => Core::getInstancePath().'temp/'.$fileName
			);

		} else {

			if (!$isAjax) {
				return;
			}

			$fileName = '';
			$tipo = $this->getPostParam('tipo', 'alpha');
			if ($tipo=='excel') {

				Core::importFromLibrary('PHPExcel', 'Classes/PHPExcel.php');

				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
				$cacheSettings = array(
					'memoryCacheSize' => '256MB'
				);
				if(!PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings)){
					return array(
						'status' => 'FAILED',
						'message' => 'No se pudo generar la caché del reporte'
					);
				}

				$locale = Locale::getApplication();
				PHPExcel_Settings::setLocale((string)$locale);

				$excel = new PHPExcel();
				$excel->getProperties()
					->setCreator($empresa->getNombre())
					->setLastModifiedBy($empresa->getNombre())
					->setTitle('Medios Magnéticos Formato ' . $codigoFormato)
					->setSubject('Medios Magnéticos Formato ' .$codigoFormato)
					->setDescription('Medios Magnéticos Formato ' . $codigoFormato)
					->setKeywords('medios magnéticos formato ' . $codigoFormato);

				$worksheet = $excel->setActiveSheetIndex(0);
				if (Browser::isMacOSX()) {
					$worksheet->getSheetView()->setZoomScale(150);
				}

				if(count($medios)>0){
					for($i=0;$i<count($camposFormato);$i++){
						$worksheet->getColumnDimensionByColumn($i)->setAutoSize(true);
					}
					$numeroFila = 1;
					foreach($medios as $row => $linea){
						foreach($linea as $posicion => $valor){
							$worksheet->setCellValueByColumnAndRow($posicion-1, $numeroFila, $valor);
						}
						$numeroFila++;
						unset($linea);
					}
				}

				$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');

				$fileName = 'formato'.$codigoFormato.'.xlsx';
				$writer->save('public/temp/'.$fileName);

			} else {
				if($tipo=='xml'){

					$domDocument = new DOMDocument('1.0', 'ISO-8859-1');
					$masElement = $domDocument->createElement('mas');
					$domDocument->appendChild($masElement);

					$masElement->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
					$masElement->setAttribute('xsi:noNamespaceSchemaLocation', "../xsd/1001.xsd");

					$cabElement = new DOMElement('Cab');
					$masElement->appendChild($cabElement);

					$fecha = new Date();
					$fechaEnvio = $fecha->getISO8601Date(false);

					$fecha->diffYears(1);
					$cabElement->appendChild(new DOMElement('Ano', $fecha->getYear()));

					$cabElement->appendChild(new DOMElement('Codcpt', 1));
					$cabElement->appendChild(new DOMElement('Formato', $codigoFormato));
					$cabElement->appendChild(new DOMElement('Version', $formato->getVersion()));
					$cabElement->appendChild(new DOMElement('FecEnvio', $fechaEnvio));
					$cabElement->appendChild(new DOMElement('FecInicial', $fechaInicial));
					$cabElement->appendChild(new DOMElement('FecFinal', $fechaFinal));
					$cabElement->appendChild(new DOMElement('CantReg', count($medios)));

					foreach ($medios as $medio) {
						$medioElement = new DOMElement('pagos');
						$masElement->appendChild($medioElement);
						foreach ($camposFormato as $posicion => $campo) {
							$medioElement->setAttribute(strtolower($campo), utf8_encode($medio[$posicion]));
						}
					}

					$cabElement->appendChild(new DOMElement('ValorTotal', $valorTotal));

					$fileName = 'Dmuisca_' . sprintf('%02s', $concepto) . sprintf('%05s', $codigoFormato).sprintf('%02s', $formato->getVersion()).$fecha->getYear().'00000001.xml';
					$domDocument->save('public/temp/' . $fileName);

				}
			}

			return array(
				'status' => 'OK',
				'message' => 'Se generó el formato correctamente',
				'url' => Core::getInstancePath().'temp/'.$fileName
			);

		}

	}

}
