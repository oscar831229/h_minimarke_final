<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * AuraFusion
 *
 * Realiza la consolidación de movimiento en servidores remotos
 */
class AuraFusion extends UserComponent
{

	private $_consolidado;

	public function __construct()
	{

	}

	public function login($serverId)
	{

		$this->_consolidado = $this->Consolidados->findFirst($serverId);
		if ($this->_consolidado==false) {
			throw new AuraException('No se encontró el servidor de consolidado');
		}

		$identity = IdentityManager::getActive();
		if($identity['id']<=0){
			throw new AuraException('No hay un usuario identificado en el sistema');
		}

		$service = $this->getService(array(
			'host' => $this->_consolidado->getServer(),
			'instancePath' => $this->_consolidado->getUri(),
			'uri' => '/session'
		));

		$usuario = $this->Usuarios->findFirst($identity['id']);
		if ($usuario == false) {
			throw new AuraException('No hay un usuario identificado en el sistema');
		}

		try {
			$success = $service->startWithFingerprint($usuario->getLogin(), $usuario->getFingerprint());
			if(!$success){
				throw new AuraException('No se pudo autenticar en el servidor remoto');
			}
		} catch (ServiceConsumerException $e) {
			throw new AuraException('Ocurrió un problema al comunicarse con el servidor remoto '.$e->getMessage());
		}

	}

	public function consolidate($year, $month, $reportType)
	{

		$firstDay = Date::getFirstDayOfMonth($month, $year);
		$lastDay = Date::getLastDayOfMonth($month, $year);

		$service = $this->getService(array(
			'host' => $this->_consolidado->getServer(),
			'instancePath' => $this->_consolidado->getUri(),
			'uri' => '/aura'
		));

		$report = ReportBase::factory($reportType);

  		$titulo = new ReportText('Exportar Movimiento a Consolidado', array(
			'fontSize' => 16,
   			'fontWeight' => 'bold',
   			'textAlign' => 'center'
  		));

  		$report->setHeader(array($titulo));
  		$report->setDocumentTitle('Exportar Movimiento a Consolidado');
  		$report->setColumnHeaders(array(
  			'COMPROBANTE',
  			'LINEA',
  			'MENSAJE'
  		));

		$terceros = array();
		$movis = $this->Movi->find(array("fecha>='$firstDay' AND fecha<='$lastDay'", "group" => "comprob,numero"));
		foreach ($movis as $moviComprob) {
			foreach ($this->Movi->find("comprob='{$moviComprob->comprob}' AND numero='{$moviComprob->numero}'") as $movi) {
				$cuenta = BackCacher::getCuenta($movi->getCuenta());
				if ($cuenta->getPideNit()=='S') {
					$tercero = BackCacher::getTercero($movi->getNit());
					if ($tercero==false) {
						throw new AuraException('El tercero local no existe '.$movi->getNit());
					} else {
						$terceros[] = array(
							'Nit' => $tercero->getNit(),
							'Tipdoc' => $tercero->getTipodoc(),
							'Nombre' => $tercero->getNombre(),
							'Clase' => $tercero->getClase(),
							'Telefono' => $tercero->getTelefono(),
							'Direccion' => $tercero->getDireccion(),
							'Fax' => $tercero->getFax(),
							'Locciu' => $tercero->getLocciu(),
							'Autoret' => $tercero->getAutoret()
						);
					}
				}
			}
		}

		//file_put_contents('a.txt', print_r($terceros, true));
	}

}