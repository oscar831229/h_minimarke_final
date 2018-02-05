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
* @copyright   BH-TECK Inc. 2009-2014
* @version     $Id$
*/

/**
* MovimientoController
*
* Controlador del movimiento
*
*/
class MovimientoController extends ApplicationController
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
		$controllerRequest = ControllerRequest::getInstance();

		if ($controllerRequest->isSetPostParam('clean')) {

			$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
			$numero = $this->getPostParam('numero', 'int');

			if ($numero>0) {

				$tokenId = IdentityManager::getTokenId();
				$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero'";
				$this->Movitemp->deleteAll($conditions);
			}

			Tag::displayTo('codigoComprobante', '');
			Tag::displayTo('numero', '');
		}

		$this->setParamToView('message', 'Ingrese un criterio de búsqueda para consultar movimientos');
		$this->setParamToView('comprobs', $this->Comprob->find(array('order' => 'nom_comprob')));
	}

	public function nuevoAction()
	{

		Session::set('lastAction', 'nuevo');

		$movimientos = array();

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');

		if ($codigoComprobante!='') {

			$comprob = $this->Comprob->findFirst("codigo='$codigoComprobante'");

			if ($comprob==false) {
				Flash::error('El comprobante "'.$codigoComprobante.'" no existe');
				return $this->routeToAction('getDetallesError');
			} else {

				$identity = IdentityManager::getActive();

				if (!Aura::checkPermission($identity['id'], $codigoComprobante, 'A')) {
					Flash::error('No tiene permiso para adicionar comprobantes de "'.$comprob->getNomComprob().'"');
					return $this->routeToAction('getDetallesError');
				}

				/*$maximoNumero = $this->Movi->maximum(array('numero', 'conditions' => "comprob='$codigoComprobante'"))+1;
				$numero = $comprob->getConsecutivo();
				if($numero<$maximoNumero){
				$numero = $maximoNumero;
			}*/

			//Se debe coger el numero de comprobante seleccionado en Báscias tipo-comrobante
			$tipoComprobante = $this->Comprob->findFirst(array('conditions'=>"codigo='$codigoComprobante'"));
			$numero = $tipoComprobante->getConsecutivo();

			//verificamos si no existe ese comprobante con ese numero
			$movi = $this->Movi->findFirst(array('conditions'=>"comprob='$codigoComprobante' AND numero='$numero'"));
			if ($movi!=false) {
				Flash::error('El comprobante "'.$comprob->getNomComprob().'" con numero "'.$numero.'" ya existe, debe cambiar el consecutivo del comprobante');
				return $this->routeToAction('getDetallesError');
			}

		}

		$tokenId = IdentityManager::getTokenId();
		$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND estado='A'";
		
		foreach ($this->Movitemp->find($conditions, 'order: consecutivo ASC') as $movi) {

			$codigoCuenta = $movi->getCuenta();
			$cuenta = BackCacher::getCuenta($codigoCuenta);

			if ($cuenta != false) {

				$movimientos[] = array(
					'numero' => $movi->getConsecutivo(),
					'cuenta' => $codigoCuenta,
					'nombreCuenta' => $cuenta->getNombre(),
					'naturaleza' => $movi->getDebCre(),
					'descripcion' => $movi->getDescripcion(),
					'valor' => $movi->getValor()
				);
			} else {
				Flash::error('La cuenta "'.$codigoCuenta.'" no existe');
			}
		}

	}

	Tag::displayTo('codigoComprobante', $codigoComprobante);
	Tag::displayTo('numero', $numero);
	$fecha = $this->getPostParam('fecha', 'date');
	if($fecha==''){
		Tag::displayTo('fecha', Date::getCurrentDate());
	} else {
		Tag::displayTo('fecha', $fecha);
	}

	if(count($movimientos)>0){
		$this->setParamToView('message', 'Se han cargado automáticamente algunos movimientos sin guardar');
	} else {
		if($codigoComprobante!=''){
			$this->setParamToView('message', 'Ingrese los movimientos contables y haga click en "Guardar"');
		} else {
			$this->setParamToView('message', 'Seleccione el tipo de comprobante a grabar');
		}
	}
	$this->setParamToView('codigoComprobante', $codigoComprobante);
	$this->setParamToView('movimientos', $movimientos);
	$this->setParamToView('comprobs', $this->Comprob->find('order: nom_comprob'));
}

public function validarFechaAction()
{
	$this->setResponse('json');
	try {
		$fecha = $this->getPostParam('fecha', 'date');
		$diasLimite = Settings::get('d_movi_limite');
		if($diasLimite===null){
			$diasLimite = 5;
		}
		$fecha = new Date($fecha);
		$fechaLimite = new Date();
		$fechaLimite->addDays($diasLimite);
		if(Date::isLater($fecha, $fechaLimite)){
			return array(
				'status' => 'FAILED',
				'message' => 'La fecha del comprobante es inválida'
			);
		} else {
			$empresa = $this->Empresa->findFirst();
			if (Date::isEarlier($fecha, $empresa->getFCierrec())) {
				return array(
					'status' => 'FAILED',
					'message' => 'La fecha del comprobante debe estar en el cierre contable actual'
				);
			} else {
				return array(
					'status' => 'OK'
				);
			}
		}
	} catch (DateException $e) {
		return array(
			'status' => 'FAILED',
			'message' => 'La fecha indicada es inválida'
		);
	}
}

public function buscarAction()
{

	$this->setResponse('json');
	$conditions = array();
	$codigoComprob = $this->getPostParam('codigoComprobante', 'comprob');
	$fecha = $this->getPostParam('fecha', 'date');
	$numero = $this->getPostParam('numero', 'int');

	$response = array();
	if ($codigoComprob !== '') {
		$conditions[] = 'comprob = \'' . $codigoComprob . '\'';
	}
	if ($fecha !== '') {
		$conditions[] = 'fecha = \'' . $fecha . '\'';
	}
	if ($numero > 0){
		$conditions[] = 'numero = \'' . $numero . '\'';
	}
	if (count($conditions) > 0) {
		$movis = $this->Movi->find(array(join(' AND ', $conditions), 'columns' => 'comprob,numero,fecha', 'group' => 'comprob,numero,fecha', 'order' => 'fecha desc,numero desc', 'limit' => 50));
	} else {
		$movis = $this->Movi->find(array('columns' => 'comprob,numero,fecha', 'group' => 'comprob,numero,fecha', 'order' => 'fecha desc,numero desc', 'limit' => 50));
	}
	if (count($movis) == 1) {
		$movi = $movis->getFirst();
		$response['number'] = '1';
		$response['key'] = 'codigoComprobante=' . $movi->getComprob() . '&numero=' . $movi->getNumero();
	} else {
		if (count($movis) > 0) {
			$responseResults = array(
				'headers' => array(
					array('name' => 'Comprobante', 'ordered' => 'N'),
					array('name' => 'Número', 'ordered' => 'N', 'type' => 'int'),
					array('name' => 'Fecha', 'ordered' => 'S', 'type' => 'date'),
				)
			);
			$data = array();
			foreach ($movis as $movi) {
				$comprob = BackCacher::getComprob($movi->getComprob());
				if ($comprob != false) {
					$data[] = array(
						'primary' => array('codigoComprobante=' . $movi->getComprob().'&numero='.$movi->getNumero()),
						'data' => array(
							array('key' => 'comprob', 'value' => $movi->getComprob().' / '.$comprob->getNomComprob()),
							array('key' => 'numero', 'value' => $movi->getNumero()),
							array('key' => 'fecha', 'value' => (string) $movi->getFecha())
							)
						);
					}
				}
				$responseResults['data'] = $data;
				$response['numberResults'] = count($responseResults['data']);
				$response['results'] = $responseResults;
				$response['number'] = 'n';
			} else {
				$response['number'] = '0';
			}
		}
		return $response;
	}

	public function leerAction()
	{

		$codigoComprobante = $this->getRequestParam('codigoComprobante', 'comprob');
		$numero = $this->getRequestParam('numero', 'int');

		$comprob = $this->Comprob->findFirst("codigo='$codigoComprobante'");
		if ($comprob == false) {
			Flash::error('No se encontró el comprobante "'.$codigoComprobante.'"');
			return $this->routeToAction('getDetallesError');
		} else {
			$this->setParamToView('nombreComprobante', $comprob->getNomComprob());
		}
		$this->setParamToView('numero', $numero);


		$movimientos = array();
		$movis = $this->Movi->find("comprob='$codigoComprobante' AND numero='$numero'");
		foreach ($movis as $movi) {
			$codigoCuenta = $movi->getCuenta();
			$cuenta = BackCacher::getCuenta($codigoCuenta);
			$movimientos[] = array(
				'cuenta' => $codigoCuenta,
				'nombreCuenta' => $cuenta->getNombre(),
				'naturaleza' => $movi->getDebCre(),
				'descripcion' => $movi->getDescripcion(),
				'valor' => $movi->getValor()
			);
		}
		$this->setParamToView('movimientos', $movimientos);

	}

	public function editarAction()
	{

		Session::set('lastAction', 'editar');

		echo $codigoComprobante = $this->getRequestParam('codigoComprobante', 'alpha');
		$numero = $this->getRequestParam('numero', 'int');

		$comprob = $this->Comprob->findFirst("codigo='$codigoComprobante'");
		if ($comprob==false) {
			Flash::error('No se encontró el comprobante "'.$codigoComprobante.'"');
			return $this->routeToAction('getDetallesError');
		} else {
			$identity = IdentityManager::getActive();
			if(!Aura::checkPermission($identity['id'], $codigoComprobante, 'S')){
				Flash::error('No tiene permiso para consultar comprobantes de "'.$comprob->getNomComprob().'"');
				return $this->routeToAction('getDetallesError');
			}
		}

		$this->setParamToView('nombreComprobante', $comprob->getNomComprob());
		$this->setParamToView('numero', $numero);

		$tokenId = IdentityManager::getTokenId();
		$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero'";
		$this->Movitemp->deleteAll($conditions);

		$consecutivo = 0;
		$fechaComprobante = null;
		$movimientos = array();
		$tokenId = IdentityManager::getTokenId();
		$movis = $this->Movi->find(array(
				"conditions" => "comprob='$codigoComprobante' AND numero='$numero'"
			)
		);
		foreach ($movis as $movi) {
			$cuenta = BackCacher::getCuenta($movi->getCuenta());
			if($cuenta!=false){
				if($fechaComprobante===null){
					$fechaComprobante = $movi->getFecha();
				}
				$movimientos[] = array(
					'numero' => $consecutivo,
					'cuenta' => $movi->getCuenta(),
					'nombreCuenta' => $cuenta->getNombre(),
					'naturaleza' => $movi->getDebCre(),
					'descripcion' => $movi->getDescripcion(),
					'valor' => Utils::dropDecimals($movi->getValor())
				);
				$moviTemp = new Movitemp();
				$moviTemp->setSid($tokenId);
				foreach($movi->getAttributes() as $attribute){
					$moviTemp->writeAttribute($attribute, $movi->readAttribute($attribute));
				}
				$moviTemp->setConsecutivo($consecutivo);
				$moviTemp->setEstado('A');
				$moviTemp->save();
				$consecutivo++;
			} else {
				Flash::error('La cuenta '.$movi->getCuenta().' no existe');
			}
		}
		$this->setParamToView('movimientos', $movimientos);
		$this->setParamToVIew('fechaComprobante', $fechaComprobante);
		$this->setParamToView('message', 'Ubicándose sobre el campo cuenta de cada movimiento puede editar los detalles de este');
	}

	public function getDetallesAction(){

		$this->setResponse('view');

		$tokenId = IdentityManager::getTokenId();
		$codigoComprobante = $this->getQueryParam('codigoComprobante', 'comprob');
		$numero = $this->getQueryParam('numero', 'int');
		$consecutivo = $this->getQueryParam('consecutivo', 'int');
		$codigoCuenta = $this->getQueryParam('cuenta', 'cuentas');

		$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND consecutivo='$consecutivo'";
		$moviTemp = $this->Movitemp->findFirst($conditions);
		if($moviTemp==false){
			$moviTemp = new Movitemp();
			$moviTemp->setEstado('A');
		}

		$cuenta = $this->Cuentas->findFirst(array("cuenta='$codigoCuenta'", 'colums' => 'cuenta,tipo,nombre,pide_nit,pide_centro,pide_fact,pide_base,es_auxiliar'));
		if($cuenta==false){
			Flash::error('La cuenta no existe');
			return $this->routeToAction('getDetallesError');
		} else {
			if($cuenta->getEsAuxiliar()!='S'){
				Flash::error('La cuenta '.$cuenta->getNombre().' no es auxiliar');
				return $this->routeToAction('getDetallesError');
			}
		}

		if($cuenta->getPideNit()=='S'){
			$existeNit = false;
			$numeroNit = '';
			$nombreNit = '';
			if($moviTemp->getNit()){
				$numeroNit = $moviTemp->getNit();
				$nit = $this->Nits->findFirst("nit='{$moviTemp->getNit()}'");
				if($nit!=false){
					$nombreNit = $nit->getNombre();
					$existeNit = true;
				} else {
					Flash::error('El tercero no existe');
				}
			}
			$this->setParamToView('existeNit', $existeNit);
			$this->setParamToView('numeroNit', $numeroNit);
			$this->setParamToView('nombreNit', $nombreNit);
		}

		if($cuenta->getPideFact()=='S'){
			$this->setParamToView('tipoDocumentos', $this->Documentos->find('order: nom_documen'));
		}

		if($cuenta->getPideCentro()=='S'){
			$tipo = $cuenta->getTipo();
			if($tipo>'3'&&$tipo<'8'){
				if(!$moviTemp->getCentroCosto()){
					$rowcounts = $this->Movi->count(array('conditions' => "cuenta='$codigoCuenta'", 'group' => 'cuenta,centro_costo', 'order' => 'rowcount', 'limit' => 1));
					if(count($rowcounts)>0){
						$rowcount = $rowcounts->getFirst();
						Tag::displayTo('centroCosto', $rowcount->centro_costo);
					}
				} else {
					Tag::displayTo('centroCosto', $moviTemp->getCentroCosto());
				}
				$this->setParamToView('centroCostos', $this->Centros->find(array('order' => 'nom_centro')));
			}
		}

		$this->setParamToView('message', 'hello');
		$this->setParamToView('cuenta', $cuenta);
		$this->setParamToView('moviTemp', $moviTemp);

	}

	private function _showErrorPage($message){

	}

	public function getDetallesErrorAction(){

	}

	public function borrarLineasAction(){

		$this->setResponse('json');
		$controllerRequest = ControllerRequest::getInstance();

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');

		$tokenId = IdentityManager::getTokenId();
		$lineas = $this->getPostParam('lineas');
		foreach(explode(',', $lineas) as $consecutivo){
			$consecutivo = $this->filter($consecutivo, 'int');

			$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND consecutivo='$consecutivo'";
			$moviTemp = $this->Movitemp->findFirst($conditions);

			if($moviTemp!=false){
				$moviTemp->setEstado('B');
				if($moviTemp->save()==false){
					return array(
						'status' => 'FAILED',
						'message' => 'No se pudo borrar la línea '.$consecutivo
					);
				}
			} else {
				return array(
					'status' => 'FAILED',
					'message' => 'No existe la línea '.$consecutivo
				);
			}
		}
		return $this->_getSumas($codigoComprobante, $numero);
	}

	/**
	* Copia lineas
	*/
	public function copiarLineasAction()
	{

		$this->setResponse('json');
		$controllerRequest = ControllerRequest::getInstance();

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');

		$tokenId = IdentityManager::getTokenId();
		$lineas = $this->getPostParam('lineas');
		$lineasA = explode(',', $lineas);
		foreach ($lineasA as $consecutivo)
		{
			$consecutivo = $this->filter($consecutivo, 'int');

			$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND consecutivo='$consecutivo'";
			$moviTemp = $this->Movitemp->findFirst($conditions);

			if ($moviTemp!=false) {


				//aumentamos consecutivo
				$moviTemp2 = clone $moviTemp;
				$consecutivo2 = $this->Movitemp->maximum(array("consecutivo","conditions"=>"sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero'"));
				$moviTemp2->setConsecutivo($consecutivo2+1);

				if ($moviTemp2->save()==false) {
					return array(
						'status' => 'FAILED',
						'message' => 'No se pudo copiar la línea '.$consecutivo
					);
				}
			} else {
				return array(
					'status' => 'FAILED',
					'message' => 'No existe la línea '.$consecutivo
				);
			}
		}
		return $this->_getSumas($codigoComprobante, $numero);
	}


	public function getSumasAction(){
		$this->setResponse('json');
		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');
		return $this->_getSumas($codigoComprobante, $numero);
	}

	public function guardarLineaAction()
	{

		$this->setResponse('json');
		$controllerRequest = ControllerRequest::getInstance();

		$codigoComprobante = $this->getQueryParam('codigoComprobante', 'comprob');
		$numero = $this->getQueryParam('numero', 'int');

		$consecutivo = $this->getPostParam('consecutivo', 'int');
		$cuenta = $this->getPostParam('cuenta', 'cuentas');
		$descripcion = $this->getPostParam('descripcion', 'striptags', 'extraspaces', 'upper');
		$valor = $this->getPostParam('valor', 'numeric');
		$naturaleza = $this->getPostParam('naturaleza', 'onechar');

		$tokenId = IdentityManager::getTokenId();

		$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero'";
		$moviTemp = $this->Movitemp->findFirst($conditions);
		if($moviTemp!=false){
			$fechaMovi = (string) $moviTemp->getFecha();
			$fechaVence = (string) $moviTemp->getFVence();
		} else {
			if($controllerRequest->isSetPostParam('fecha')){
				$fechaMovi = $this->getPostParam('fecha', 'date');
				$fechaVence = $this->getPostParam('fechaVence', 'date');
			} else {
				$fechaMovi = Date::getCurrentDate();
				$fechaVence = Date::getCurrentDate();
			}
		}

		$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND consecutivo='$consecutivo'";
		$moviTemp = $this->Movitemp->findFirst($conditions);

		$nit = '';
		if($controllerRequest->isSetPostParam('nit')){
			$nit = $this->getPostParam('nit', 'terceros');
		} else {
			if($moviTemp!=false){
				$nit = $moviTemp->getNit();
			}
		}

		$centroCosto = '';
		if($controllerRequest->isSetPostParam('centroCosto')){
			$centroCosto = $this->getPostParam('centroCosto', 'int');
		} else {
			if($moviTemp!=false){
				$centroCosto = $moviTemp->getCentroCosto();
			}
		}

		$codigoComprobante = $this->getQueryParam('codigoComprobante', 'comprob');
		$numero = $this->getQueryParam('numero', 'int');
		$consecutivo = $this->getPostParam('consecutivo', 'int');

		$movement = array(
			'Fecha' => $fechaMovi,
			'Cuenta' => $cuenta,
			'Descripcion' => $descripcion,
			'Nit' => $nit,
			'CentroCosto' => $centroCosto,
			'Valor' => $valor,
			'DebCre' => $naturaleza
		);

		if($controllerRequest->isSetPostParam('tipoDocumento')){
			$tipoDocumento = $this->getPostParam('tipoDocumento', 'documento');
			$numeroDocumento = $this->getPostParam('numeroDocumento', 'int');
			$fechaVence = $this->getPostParam('fechaVence', 'date');
			$movement['TipoDocumento'] = $tipoDocumento;
			$movement['NumeroDocumento'] = $numeroDocumento;
			$movement['FechaVence'] = $fechaVence;
		} else {
			if($moviTemp!=false){
				$movement['TipoDocumento'] = $moviTemp->getTipoDoc();
				$movement['NumeroDocumento'] = $moviTemp->getNumeroDoc();
				$movement['FechaVence'] = $moviTemp->getFVence();
			}
		}
		if($controllerRequest->isSetPostParam('baseGravable')){
			$baseGravable = $this->getPostParam('baseGravable', 'numeric');
			$movement['BaseGrab'] = $baseGravable;
		} else {
			if($moviTemp!=false){
				$movement['BaseGrab'] = $moviTemp->getBaseGrab();
			}
		}

		$auraException = false;
		try {
			$aura = new Aura($codigoComprobante, $numero);
			$aura->setActiveLine($consecutivo+1);
			$movement = $aura->validate($movement);

			if($moviTemp==false){
				$moviTemp = new Movitemp();
				$moviTemp->setSid($tokenId);
				$moviTemp->setComprob($codigoComprobante);
				$moviTemp->setNumero($numero);
				$moviTemp->setConsecutivo($consecutivo);
				$moviTemp->setFecha($aura->getDefaultFecha());
				$moviTemp->setEstado('A');
			}

			$moviTemp->setCuenta($movement['Cuenta']);
			$moviTemp->setValor($movement['Valor']);
			$moviTemp->setDescripcion($movement['Descripcion']);
			$moviTemp->setNit($movement['Nit']);
			$moviTemp->setCentroCosto($movement['CentroCosto']);
			$moviTemp->setDebCre($movement['DebCre']);
			if($controllerRequest->isSetPostParam('tipoDocumento')){
				$moviTemp->setTipoDoc($tipoDocumento);
				$moviTemp->setNumeroDoc($numeroDocumento);
				$moviTemp->setFVence($fechaVence);
			}
			if($controllerRequest->isSetPostParam('baseGravable')){
				$moviTemp->setBaseGrab($baseGravable);
			}

			if($moviTemp->save()==false){
				foreach($moviTemp->getMessages() as $message){
					return array(
						'status' => 'FAILED',
						'message' => $message->getMessage()
					);
				}
			} else {
				if($auraException){
					return array(
						'status' => 'FAILED',
						'message' => $auraException->getMessage()
					);
				} else {
					$response = $this->_getSumas($codigoComprobante, $numero);
					if($moviTemp->getUpdatedChecksum()!=$moviTemp->getChecksum()){
						$response['changed'] = 1;
					} else {
						$response['changed'] = 0;
					}
					return $response;
				}
			}
		}
		catch(Exception $auraException){
			return array(
				'status' => 'FAILED',
				'message' => $auraException->getMessage()
			);
		}

	}

	private function _getSumas($codigoComprobante, $numero)
	{
		$debitos = 0;
		$creditos = 0;
		$tokenId = IdentityManager::getTokenId();
		$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND estado='A'";
		foreach ($this->Movitemp->find($conditions, 'columns: deb_cre,valor') as $moviTemp) {
			if ($moviTemp->getDebCre() == 'D') {
				$debitos += $moviTemp->getValor();
			} else {
				$creditos += $moviTemp->getValor();
			}
		}
		if (($debitos - $creditos) != 0) {
			$descuadre = true;
		} else {
			$descuadre = false;
		}
		return array(
			'status'     => 'OK',
			'descuadre'  => $descuadre,
			'debitos'    => Currency::number($debitos),
			'creditos'   => Currency::number($creditos),
			'diferencia' => Currency::number($debitos-$creditos)
		);
	}

	public function eliminarAction()
	{
		$this->setResponse('json');
		$controllerRequest = ControllerRequest::getInstance();

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');
		try {
			$aura = new Aura($codigoComprobante, $numero, null, Aura::OP_DELETE);
			$aura->delete();
			return array(
				'status' => 'OK'
			);
		} catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function guardarAction()
	{

		$this->setResponse('json');

		$controllerRequest = ControllerRequest::getInstance();

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');
		$numeroComprob = $numero;

		try {
			set_time_limit(0);

			$tokenId = IdentityManager::getTokenId();

			//Verificamos por tockenId si hay dos personas usando el mismo numero de comprobantes
			if (Session::get('lastAction') == 'nuevo') {

				$conditionsTemp = "comprob='$codigoComprobante' AND numero='$numero' AND estado='A'";
				$conditionsMovi = "comprob='$codigoComprobante' AND numero='$numero'";
				$sIdObj = $this->Movitemp->find($conditionsTemp, 'order: consecutivo ASC', 'group: sid');
				$moviObj = $this->Movi->find($conditionsMovi);
				if (count($sIdObj) > 1 || count($moviObj)) {

					$numeroComprob = $this->Movi->maximum(array('numero', 'conditions' => "comprob='$codigoComprobante'"))+1;

					//Aumentar el consecutivo de movitemp
					$conditionsTemp = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND estado='A'";
					$sIdObj = $this->Movitemp->find($conditionsTemp, 'order: consecutivo ASC', 'group: sid');
					foreach ($sIdObj as $moviTemp)
					{
						$moviTemp->setNumero($numeroComprob);
						$moviTemp->save();
					}

				}
			}

			$aura = new Aura($codigoComprobante, $numeroComprob);


			$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND estado='A'";
			foreach ($this->Movitemp->find($conditions, 'order: consecutivo ASC') as $moviTemp) {
				$aura->addMovement(array(
					'Fecha' => $moviTemp->getFecha(),
					'FechaVence' => $moviTemp->getFVence(),
					'Cuenta' => $moviTemp->getCuenta(),
					'Nit' => $moviTemp->getNit(),
					'CentroCosto' => $moviTemp->getCentroCosto(),
					'Valor' => $moviTemp->getValor(),
					'Descripcion' => $moviTemp->getDescripcion(),
					'TipoDocumento' => $moviTemp->getTipoDoc(),
					'NumeroDocumento' => $moviTemp->getNumeroDoc(),
					'BaseGrab' => $moviTemp->getBaseGrab(),
					'Folio' => $moviTemp->getNumfol(),
					'DebCre' => $moviTemp->getDebCre(),
					'Consecutivo' => $moviTemp->getConsecutivo()
				));
			}

			$aura->save();
			$this->Movitemp->deleteAll($conditions);
			return array(
				'status' => 'OK',
				'message' => "Se guardo el movimiento contable en el comprobante '$codigoComprobante-$numeroComprob'"
			);
		} catch (Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	public function imprimirAction()
	{
		$this->setResponse('view');
		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');
		$this->setParamToView('codigoComprobante', $codigoComprobante);
		$this->setParamToView('numero', $numero);
	}

	public function reporteAction()
	{
		$this->setResponse('json');

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');
		$orden = $this->getPostParam('orden', 'alpha');

		$comprob = $this->Comprob->findFirst("codigo='$codigoComprobante'");
		if ($comprob != false) {

			$movi = $this->Movi->findFirst("comprob='$codigoComprobante' AND numero='$numero'");

			$titulo = new ReportText('MOVIMIENTO CONTABLE ' . $comprob->getNomComprob() . '/' . $numero . ' de ' . $movi->getFecha()->getLocaleDate('medium'), array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));
		}

		$report->setHeader(array($titulo));
		$report->setDocumentTitle('Movimiento Contable '.$codigoComprobante.'-'.$numero);
		$report->setColumnHeaders(array(
			'CÓDIGO',
			'DESCRIPCIÓN',
			'TERCERO',
			'NOMBRE',
			'C. COSTO',
			'DOCUMENTO',
			'DEBITOS',
			'CREDITOS',
			'BASE GRAVABLE',
			'FOLIO'
		));

		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));
		$report->setColumnStyle(0, new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		)));
		$report->setColumnStyle(1, new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		)));
		$report->setColumnStyle(array(2, 4, 6, 7, 8), new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
		)));

		$report->setColumnFormat(array(6, 7, 8), new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		)));

		$report->setTotalizeColumns(array(6, 7));

		$report->start(true);

		$parameters = array("comprob='$codigoComprobante' AND numero='$numero'");
		/*switch ($orden) {
			case 'F':
			$parameters['order'] = "numfol, cuenta, deb_cre, consecutivo ASC";
			break;
			case 'C':
			$parameters['order'] = "cuenta, deb_cre, consecutivo ASC";
			break;
			case 'N':
			$parameters['order'] = "deb_cre, cuenta, consecutivo ASC";
			break;
			case 'S':
			break;
			default:
			$parameters['order'] = "numfol, cuenta, deb_cre, consecutivo ASC";
			break;
		}*/

		$parameters['order'] = "consecutivo ASC";
			

		$debitos = 0;
		$creditos = 0;
		foreach ($this->Movi->find($parameters) as $movi) {
			$cuenta = BackCacher::getCuenta($movi->getCuenta());
			if ($movi->getNumeroDoc()) {
				$numeroDoc = $movi->getTipoDoc() . '-' . $movi->getNumeroDoc();
			} else {
				$numeroDoc = '';
			}
			$movement = array(
				$movi->getCuenta(),
				$movi->getDescripcion(),
				'',
				'',
				$movi->getCentroCosto(),
				$numeroDoc,
				0,
				0,
				$movi->getBaseGrab(),
				$movi->getNumfol()
			);
			if ($cuenta->getPideNit() == 'S'){
				$tercero = BackCacher::getTercero($movi->getNit());
				if ($tercero == false) {
					$nombreTercero = 'NO EXISTE TERCERO';
				} else {
					$nombreTercero = $tercero->getNombre();
				}
				$movement[2] = $movi->getNit();
				$movement[3] = $nombreTercero;
			}
			if($movi->getDebCre()=='D'){
				$movement[6] = $movi->getValor();
			} else {
				$movement[7] = $movi->getValor();
			}
			$report->addRow($movement);
		}

		$report->addSignature(array('Revisado', 'Elaborado'), 2);

		$report->finish();

		$fileName = $report->outputToFile('public/temp/movimiento-'.$codigoComprobante.'-'.$numero);

		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);

	}

	public function copiarAction()
	{
		$this->setResponse('view');

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');

		Tag::displayTo('codigoComprobante', $codigoComprobante);
		Tag::displayTo('fechaComprobante', Date::getCurrentDate());

		$this->setParamToView('codigoComprobante', $codigoComprobante);
		$this->setParamToView('numero', $numero);
		$this->setParamToView('comprobs', $this->Comprob->find('order: nom_comprob'));
	}

	public function prepararCopiaAction()
	{

		$this->setResponse('json');

		$controllerRequest = ControllerRequest::getInstance();

		$comprobanteOrigen = $this->getPostParam('comprobanteOrigen', 'comprob');
		$numeroOrigen = $this->getPostParam('numeroOrigen', 'int');

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$fecha = $this->getPostParam('fechaComprobante', 'date');

		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		if(Date::isEarlier($fecha, $fechaCierre)){
			return array(
				'status' => 'FAILED',
				'message' => 'La fecha del comprobante debe ser mayor al último cierre'
			);
		}

		try {

			$empresa1 = $this->Empresa1->findFirst();
			$tokenId = IdentityManager::getTokenId();

			$conditions = "sid='$tokenId' AND
			comprob='$comprobanteOrigen' AND
			numero='$numeroOrigen' AND
			(cuenta LIKE '2365%' OR cuenta LIKE '2367%')";
			$movimientoRetencion = $this->Movitemp->count($conditions);
			if($movimientoRetencion){
				$fLimiteR = Date::fromFormat(substr($empresa1->getOtros(), 4, 10), 'MM/DD/YYYY');
				if(Date::isEarlier($fecha, $fLimiteR)){
					return array(
						'status' => 'FAILED',
						'message' => 'No se puede usar la fecha, porque es menor al limite de presentación de retención'
					);
				}
			}

			$conditions = "sid='$tokenId' AND
			comprob='$comprobanteOrigen' AND
			numero='$numeroOrigen' AND
			(cuenta LIKE '4%' OR cuenta like '2408%')";
			$movimientoIva = $this->Movitemp->count($conditions);
			if($movimientoIva){
				if (strlen($empresa1->getOtros()) > 20) {
					$fLimiteI = Date::fromFormat(substr($empresa1->getOtros(), 14, 10), 'MM/DD/YYYY');
					if(Date::isEarlier($fecha, $fLimiteI)){
						return array(
							'status' => 'FAILED',
							'message' => 'No se puede usar la fecha, porque es menor al limite de presentación de IVA'
						);
					}
				}
			}
		} catch (DateException $e) {
			return array(
				'status' => 'FAILED',
				'message' => 'La fecha de limite de retención o IVA no es válida por favor verifique '
			);
		}

		$comprob = $this->Comprob->findFirst("codigo='$codigoComprobante'");

		$maximoNumero = $this->Movi->maximum(array('numero', 'conditions' => "comprob='$codigoComprobante'"))+1;
		$numero = $comprob->getConsecutivo();
		if ($numero < $maximoNumero) {

			//Se usa el del comprob siempre//$numero = $maximoNumero;
		}

		$this->Movitemp->deleteAll("sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero'");
		$movis = $this->Movitemp->find("sid='$tokenId' AND comprob='$comprobanteOrigen' AND numero='$numeroOrigen' AND estado='A'");
		//Si no encontro movitemp crearlo si existe movi
		if (!count($movis)) {

			$consecutivo = 0;
			$fechaComprobante = null;
			$movis = $this->Movi->find("comprob='$comprobanteOrigen' AND numero='$numeroOrigen'");
			foreach ($movis as $movi) {
				$cuenta = BackCacher::getCuenta($movi->getCuenta());
				if ($cuenta != false) {
					if($fechaComprobante===null){
						$fechaComprobante = $movi->getFecha();
					}
					$moviTemp = new Movitemp();
					$moviTemp->setSid($tokenId);
					$moviTemp->setConsecutivo($consecutivo);
					foreach($movi->getAttributes() as $attribute){
						$moviTemp->writeAttribute($attribute, $movi->readAttribute($attribute));
					}
					$moviTemp->setEstado('A');
					$moviTemp->save();
					$consecutivo++;
				} else {
					Flash::error('La cuenta '.$movi->getCuenta().' no existe');
				}
			}

			//volvemos a tratar de coger el movitemp
			$movis = $this->Movitemp->find("sid='$tokenId' AND comprob='$comprobanteOrigen' AND numero='$numeroOrigen' AND estado='A'");
		}
		//echo "<br>","sid='$tokenId' AND comprob='$comprobanteOrigen' AND numero='$numeroOrigen' AND estado='A'";
		foreach ($movis as $movi) {
			$moviTemp = new Movitemp();
			$moviTemp->setSid($tokenId);
			foreach($movi->getAttributes() as $attribute){
				$moviTemp->writeAttribute($attribute, $movi->readAttribute($attribute));
			}
			$moviTemp->setComprob($codigoComprobante);
			$moviTemp->setNumero($numero);
			$moviTemp->setFecha($fecha);
			$moviTemp->setEstado('A');
			if($moviTemp->save()==false){
				foreach($moviTemp->getMessages() as $message){
					return array(
						'status' => 'FAILED',
						'message' => 'Error al copiar el Comprobante: '.$message->getMessage()
					);
				}
			}
		}
		$this->Movitemp->deleteAll("sid='$tokenId' AND comprob='$comprobanteOrigen' AND numero='$numeroOrigen'");

		return array(
			'status' => 'OK',
			'key' => 'codigoComprobante='.$codigoComprobante.'&numero='.$numero.'&fecha='.$fecha
		);

	}

	public function cambiarFechaAction(){
		$this->setResponse('view');

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');

		$tokenId = IdentityManager::getTokenId();
		$moviTemp = $this->Movitemp->findFirst("sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero' AND estado='A'");
		if($moviTemp!=false){
			Tag::displayTo('fechaComprobante', (string) $moviTemp->getFecha());
		} else {
			Flash::error('No se encontró el comprobante');
		}

		$this->setParamToView('codigoComprobante', $codigoComprobante);
		$this->setParamToView('numero', $numero);
	}

	public function hacerCambioFechaAction(){

		$this->setResponse('json');

		$controllerRequest = ControllerRequest::getInstance();

		$comprobanteOrigen = $this->getPostParam('comprobanteOrigen', 'comprob');
		$numeroOrigen = $this->getPostParam('numeroOrigen', 'int');

		$fecha = $this->getPostParam('fechaComprobante', 'date');

		try {

			$empresa = $this->Empresa->findFirst();
			$fechaCierre = $empresa->getFCierrec();
			if(Date::isEarlier($fecha, $fechaCierre)){
				return array(
					'status' => 'FAILED',
					'message' => 'La fecha del comprobante debe ser mayor al último cierre'
				);
			}

		}
		catch(DateException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		$tokenId = IdentityManager::getTokenId();
		$movis = $this->Movitemp->find("sid='$tokenId' AND comprob='$comprobanteOrigen' AND numero='$numeroOrigen'");
		foreach($movis as $movi){
			$moviTemp = new Movitemp();
			$moviTemp->setSid($tokenId);
			foreach($movi->getAttributes() as $attribute){
				$moviTemp->writeAttribute($attribute, $movi->readAttribute($attribute));
			}
			$moviTemp->setFecha($fecha);
			$moviTemp->save();
		}

		return array(
			'status' => 'OK'
		);

	}

	public function verCarteraAction(){
		$this->setResponse('view');
		$nitNumero = $this->getPostParam('nit', 'terceros');
		$codigoCuenta = $this->getPostParam('cuenta', 'cuentas');
		$tercero = $this->Nits->findFirst("nit='$nitNumero'");
		if($tercero==false){
			Flash::error('No existe el tercero con documento "'.$nitNumero.'"');
		} else {
			$carteras = $this->Cartera->find("cuenta='$codigoCuenta' AND nit='$nitNumero' AND saldo!=0","order: f_emision ASC");
			if(count($carteras)==0){
				$cuenta = BackCacher::getCuenta($codigoCuenta);
				if($cuenta==false){
					Flash::notice('El tercero no tiene cartera pendiente por pagar ó abonar');
				} else {
					Flash::notice('El tercero no tiene cartera pendiente por pagar ó abonar en la cuenta '.$cuenta->getNombre());
				}
			} else {
				$this->setParamToView('tercero', $tercero);
				$this->setParamToView('carteras', $carteras);
				$this->setParamToView('codigoCuenta', $codigoCuenta);
			}
		}
	}

	public function tomarCarteraAction(){
		$this->setResponse('json');
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isSetPostParam('numeroDoc')){
			$transaction = TransactionManager::getUserTransaction();
			$nitNumero = $this->getPostParam('nit', 'terceros');
			$numeroDoc = $this->getPostParam('numeroDoc');
			$codigoCuenta = $this->getPostParam('cuenta', 'cuentas');
			list($tipoDoc, $numeroDoc) = explode('-', $numeroDoc);
			$tipoDoc = $this->filter($tipoDoc, 'alpha');
			$numeroDoc = $this->filter($numeroDoc, 'int');
			$cartera = $this->Cartera->setTransaction($transaction)->findFirst("nit='$nitNumero' AND cuenta='$codigoCuenta' AND tipo_doc='$tipoDoc' AND numero_doc='$numeroDoc'");
			if($cartera==false){
				return array(
					'status' => 'FAILED',
					'message' => 'No se encontró el documento en cartera'
				);
			} else {
				return array(
					'status' => 'OK',
					'cartera' => array(
						'tipoDoc' => $tipoDoc,
						'numeroDoc' => $numeroDoc,
						'valor' => $cartera->getSaldo()
					)
				);
			}
		} else {
			return array(
				'status' => 'OK'
			);
		}
	}

	public function cleanAction(){
		$this->setResponse('view');
		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');
		if($numero>0){
			$tokenId = IdentityManager::getTokenId();
			$conditions = "sid='$tokenId' AND comprob='$codigoComprobante' AND numero='$numero'";
			$this->Movitemp->deleteAll($conditions);
		}
	}

	public function testAction(){
		$this->setResponse('view');
		$empresa1 = $this->Empresa1->findFirst();
		$fLimiteR = Date::fromFormat(substr($empresa1->getOtros(), 4, 10), 'MM/DD/YYYY');
		$fLimiteI = substr($empresa1->getOtros(), 14, 10);
		echo $fLimiteR;
	}

	public function revisionesAction(){
		$this->setResponse('view');

		$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
		$numero = $this->getPostParam('numero', 'int');

		if ($codigoComprobante && $numero) {
			$grabObj = $this->Grab->find(array('conditions'=>"comprob='$codigoComprobante' AND numero='$numero'"));
			$this->setParamToView('grabObj', $grabObj);
		}
	}
}
