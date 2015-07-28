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
 * TercerosController
 *
 * Controlador de los terceros
 *
 */
class TercerosController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Nits',
		'plural' => 'terceros',
		'single' => 'tercero',
		'genre' => 'M',
		'icon' => 'business-contact.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'nit' => array(
				'single' => 'Documento',
				'type' => 'text',
				'size' => 18,
				'maxlength' => 18,
				'primary' => true,
				'filters' => array('terceros')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'notNull'	=> true,
				'filters' => array('striptags', 'extraspaces')
			),
			'tipodoc' => array(
				'single' => 'Tipo Documento',
				'type' => 'relation',
				'relation' => 'Tipodoc',
				'fieldRelation' => 'codigo',
				'detail' => 'nombre',
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				//'notReport' => true,
				'filters' => array('int')
			),
			'clase' => array(
				'single' => 'Clase',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'C' => 'CLIENTE',
					'A' => 'EMPRESA',
					'E' => 'EXTRANJERO'
				),
				'filters' => array('alpha')
			),
			'direccion' => array(
				'single' => 'Dirección',
				'type' => 'text',
				'size' => 25,
				'maxlength' => 25,
				'notBrowse' => true,
				'notSearch' => true,
				'notNull'	=> true,
				'filters' => array('striptags', 'extraspaces')
			),
			'telefono' => array(
				'single' => 'Teléfonos',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'notBrowse' => true,
				'notSearch' => true,
				'notNull'	=> true,
				'filters' => array('striptags', 'extraspaces')
			),
			'fax' => array(
				'single' => 'Fax',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'notBrowse' => true,
				'notSearch' => true,
				//'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'email' => array(
				'single' => 'Email',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 145,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('striptags')
			),
			'celular' => array(
				'single' => 'Celular',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 145,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'locciu' => array(
				'single' => 'Ciudad',
				'type' => 'ciudad',
				'notBrowse' => true,
				'notNull' => true,
				#'notSearch' => true,
				#'notReport' => true,
				'filters' => array('int')
			),
			'contacto' => array(
				'single' => 'Contacto',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35,
				'notBrowse' => true,
				'notSearch' => true,
				//'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'lista' => array(
				'single' => 'Tipo',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'1' => 'PROVEEDOR',
					'2' => 'CLIENTE',
					'3' => 'EMPLEADO',
					'4' => 'CLIENTE/PROVEEDOR',
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'autoret' => array(
				'single' => 'Autoretenedor',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				//'notReport' => true,
				'filters' => array('onechar')
			),
			'plazo' => array(
				'single' => 'Hortifrutícula',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					1 => 'SI',
					0 => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				//'notReport' => true,
				'filters' => array('onechar')
			),
			'estado_nit' => array(
				'single' => 'Tipo de Régimen',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'NO APLICA',
					'C' => 'COMUN',
					'G' => 'GRAN CONTRIBUYENTE',
					'S' => 'SIMPLIFICADO'
				),
				'notBrowse' => true,
				//'notReport' => true,
				'filters' => array('alpha')
			),
			'resp_iva' => array(
				'single' => 'Responsable IVA',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				//'notReport' => true,
				'filters' => array('alpha')
			),
			'tipo_nit' => array(
				'single' => 'IVA Retenido',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					0 => 'SI',
					1 => 'NO'
				),
				'notBrowse' => true,
				'filters' => array('onechar')
			),
			'ap_aereo' => array(
				'single' => 'Porcentaje de ICA',
				'type' => 'decimal',
				'size' => 5,
				'maxlength' => 5,
				'notSearch' => true,
				//'notReport' => true,
				'filters' => array('double')
			),
			/*'cupo' => array(
				'single' => 'Cupo',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('alpha')
			),*/
			'porce_cree' => array(
                'single' => 'Porcentaje de CREE',
                'type' => 'relation',
                'relation' => 'CuentasCree',
                'fieldRelation' => 'porce',
                'detail' => 'porce',
                'maxlength' => 25,
                'notBrowse' => true,
                'notSearch' => true,
                'notReport' => true,
                'filters' => array('double')
            ),
            'retecompras_id' => array(
				'single' => 'Retención de Compras',
				'type' => 'relation',
				'relation' => 'Retecompras',
				'fieldRelation' => 'codigo',
				'detail' => 'descripcion',
				'maxlength' => 1,
				'notBrowse' => true,
				//'notSearch' => true,
				//'notReport' => true,
				'filters' => array('double')
			),
            'grupo_niif' => array(
                'single' => 'Grupo NIIF',
                'type' => 'int',
                'size' => 2,
                'maxlength' => 2,
                'notNull'	=> false,
                'filters' => array('int')
            ),
		)
	);

	public function initialize()
	{
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function queryByNitAction()
	{
		$this->setResponse('json');
		$numeroNit = $this->getQueryParam('nit', 'terceros');
		$nit = $this->Nits->findFirst("nit='$numeroNit'");
		if($nit==false){
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

	public function queryByNameAction()
	{
		$this->setResponse('json');
		$response = array();
		$nombre = $this->getPostParam('nombre', 'extraspaces');
		if($nombre!=''){
			$nombre = preg_replace('/[ ]+/', '%', $nombre);
			$nits = $this->Nits->find('nombre LIKE \''.$nombre.'%\'', 'order: nombre', 'limit: 13');
			foreach($nits as $nit){
				$response[] = array(
					'value' => $nit->getNit(),
					'selectText' => $nit->getNombre(),
					'optionText' => $nit->getNombre()
				);
			}
		}
		return $response;
	}

	public function crearAction()
	{
		$this->setParamToView('tiposDocumentos', $this->Tipodoc->find(array("clase='C'", "order" => "predeterminado DESC,nombre")));
	}

	/**
	 * Metodo que se usas para nuevas columnas calculadas
	 *
	 * @param array $headers
	 * @return array $headers
	 */
	public function afterReportHeader($headers)
	{
	    $headers[] = 'Fecha Primer Movimiento';
	    $headers[] = 'Fecha Último Movimiento';
	    return $headers;
	}

	/**
	 * Para visualizar la fecha de creacion del tercero
	 *
	 * @param array $row
	 * @param array $config
	 */
	public function afterReportRow(&$row, $config)
	{
		if (isset($config['fields'])) {
			$i = 0;
			$indexLocation = 0;
			$pkIndex = array();
			foreach($config['fields'] as $field=>$fData){
				if(isset($fData['primary']) && $fData['primary']==true){
					$pkIndex[] = $field."='".$row[$i]."'";
				}
				if($field=='locciu'){
					$indexLocation=$i;
				}
				$i++;
			}

			//Buscamos la fecha del primer movimiento de un proveedor
			$fechaInicial = $this->Movi->minimum(array('fecha', 'conditions' => join(' AND ', $pkIndex)));
			$row[] = $fechaInicial;

			//buscamos las fecha del ultimo movimiento
			$fechaFinal = $this->Movi->maximum(array('fecha', 'conditions' => join(' AND ', $pkIndex)));
			$row[] = $fechaFinal;

			//Asignamos el valor Geográfico
			$location = BackCacher::getLocation($row[$indexLocation]);
			if($location==false){
				$row[$indexLocation] = '';
			} else {
				$row[$indexLocation] = utf8_encode($location->getName());
			}
		}
	}

	public function autoLocciuAction()
	{
		try {

			$transaction = TransactionManager::getUserTransaction();

			//default territory
			$territoryId = 135; //Colombia

			$nitsObj = EntityManager::get("Nits")->find();

			$noEncontrados = "";
			foreach ($nitsObj as $nits)
			{

				if (!$nits->getLocciu() && $nits->getCiudad()) {

					$ciudadOriginal = $nits->getCiudad();

					switch ($nits->getCiudad()) {
						case 'BTA':
							$nits->setCiudad('BOGOTA');
							break;
						case '127591':
							$nits->setCiudad('BOGOTA');
							break;
					}

					if (strstr($nits->getCiudad(),"-")) {
						$nameSplit = explode("-",$nits->getCiudad());
						$nits->setCiudad($nameSplit[0]);
					}

					if (is_numeric($nits->getCiudad())) {
						$location = EntityManager::get("Location")->findFirst($nits->getCiudad());
					}else {
						$location = EntityManager::get("Location")->findFirst(array('conditions' => "name = '{$nits->getCiudad()}' AND territory_id=135"));
						if ($location==false) {
							$location = EntityManager::get("Location")->findFirst(array('conditions' => "name LIKE '{$nits->getCiudad()}%' AND territory_id=135"));
							if ($location==false) {

								if ($location==false) {
									$location = EntityManager::get("Location")->findFirst(array('conditions' => "name LIKE '{$nits->getCiudad()}%'"));
								}
							}
						}
					}

					if ($location==false) {
						$noEncontrados .= "<br/> LOCCIU NO ENCONTRADO> ".($nits->getNit().": CiudadTxt: (".$ciudadOriginal."->".$nits->getCiudad().")");

						continue;
					}

					echo "<br/>".($nits->getNit().": CiudadTxt: (".$ciudadOriginal."->".$nits->getCiudad()."), Location: (".$location->getId().", ".$location->getName().")");

					$nits->setLocciu($location->getId());

					$nits->save();
				}

				unset($nits);
			}
			unset($nitsObj);

			echo "<h1>Ciudades No Encontradas</h1>";
			echo $noEncontrados;

			$transaction->commit();
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

}
