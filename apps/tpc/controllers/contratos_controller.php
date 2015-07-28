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

Core::importFromLibrary('Hfos/Tpc','TpcContratos.php');
/**
 * ContratosController
 *
 * Controlador de Contratos
 */
class ContratosController extends HyperFormController {

	public static $_config = array(
		'model'		=> 'Socios',
		'plural'	=> 'Contratos',
		'single'	=> 'Contrato',
		'genre'		=> 'M',
		'tabName'	=> 'Contrato',
		'preferedOrder' => 'id DESC',
		'icon'		=> 'hire-me.png',
		'ignoreButtons' => array('import'),
		'fields'	=> array(
			'id'	=> array(
				'single'	=> 'Código',
				'type'		=> 'text',
				'size'		=> 6,
				'maxlength'	=> 6,
				'primary'	=> true,
				'readOnly'	=> true,
				'auto'		=> true,
				'filters'	=> array('int')
			),
			'tipo_contrato_id' => array(
				'single'	=> 'Tipo de Contrato',
				'type'		=> 'relation',
				'relation'	=> 'TipoContrato',
				'fieldRelation' => 'id',
				'detail'	=> 'nombre',
				'conditionsOnCreate' => 'estado="A"',
				'maxlength'	=> 20,
				'notNull'	=> true,
				'filters'	=> array('int')
			),
			'numero_contrato' => array(
				'single'	=> 'Numero Contrato',
				'type'		=> 'text',
				'size'		=> 20,
				'maxlength'	=> 20,
				'notNull'	=> true,
				'readOnly'  => true,
				'filters'	=> array('striptags', 'extraspaces')
			),
			'fecha_compra' => array(
				'single'	=> 'Fecha de Compra',
				'type'		=> 'date',
				'default'	=> '',
				'notNull'	=> true,
				'filters'	=> array('date')
			),
			'tipo_documentos_id' => array(
				'single'	=> 'Tipo de Documento',
				'type'		=> 'relation',
				'relation'	=> 'TipoDocumentos',
				'fieldRelation' => 'id',
				'detail'	=> 'nombre',
				'conditionsOnCreate' => 'estado="A"',
				'maxlength'	=> 20,
				'notNull'	=> true,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('int')
			),
			'identificacion' => array(
				'single'	=> 'Cédula',
				'type'		=> 'int',
				'size'		=> 13,
				'maxlength'	=> 20,
				'notNull'	=> true,
				'filters'	=> array('int')
			),
			'nombres' => array(
				'single'	=> 'Nombres',
				'type'		=> 'text',
				'size'		=> 30,
				'maxlength'	=> 60,
				'notNull'	=> true,
				'filters'	=> array('striptags', 'extraspaces','alpha')
			),
			'apellidos' => array(
				'single'	=> 'Apellidos',
				'type'		=> 'text',
				'size'		=> 30,
				'maxlength'	=> 60,
				'notNull'	=> true,
				'filters'	=> array('striptags', 'extraspaces','alpha')
			),
			'direccion_residencia' => array(
				'single'	=> 'Dirección de Residencia',
				'type'		=> 'text',
				'size'		=> 30,
				'maxlength'	=> 50,
				'notNull'	=> true,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('striptags', 'extraspaces')
			),
			'ciudad_residencia' => array(
				'single'	=> 'Ciudad Residencia',
				'type'		=> 'ciudad',
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'notNull'	=> true,
				'size'		=> 6,
				'maxlength'	=> 10,
				'filters'	=> array('int')
			),
			'telefono_residencia' => array(
				'single'	=> 'Teléfono de Residencia',
				'type'		=> 'int',
				'size'		=> 10,
				'maxlength'	=> 20,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'notNull'	=> true,
				'filters'	=> array('int')
			),
			'correo' => array(
				'single'	=> 'Correo Electrónico',
				'type'		=> 'text',
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'size'		=> 30,
				'maxlength'	=> 60,
				'filters'	=> array('email')
			),
			'celular' => array(
				'single'	=> 'Celular',
				'type'		=> 'int',
				'size'		=> 10,
				'maxlength'	=> 20,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'notNull'	=> true,
				'filters'	=> array('int')
			),
			'empresa' => array(
				'single'	=> 'Empresa',
				'type'		=> 'text',
				'size'		=> 50,
				'maxlength'	=> 100,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('striptags', 'extraspaces','alpha')
			),
			'direccion_trabajo' => array(
				'single'	=> 'Dirección Trabajo',
				'type'		=> 'text',
				'size'		=> 30,
				'maxlength'	=> 50,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('striptags', 'extraspaces')
			),
			'ciudades_id' => array(
				'single'	=> 'Ciudad Trabajo',
				'type'		=> 'ciudad',
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'size'		=> 6,
				'maxlength'	=> 10,
				'filters'	=> array('int')
			),
			'telefono_trabajo' => array(
				'single'	=> 'Teléfono de Trabajo',
				'type'		=> 'int',
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'size'		=> 6,
				'maxlength'	=> 10,
				'filters'	=> array('int')
			),
			'estados_civiles_id' => array(
				'single'	=> 'Estado Civil',
				'type'		=> 'relation',
				'relation'	=> 'EstadosCiviles',
				'fieldRelation' => 'id',
				'detail'	=> 'nombre',
				'conditionsOnCreateOnCreate' => 'estado="A"',
				'maxlength'	=> 20,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('int')
			),
			'profesiones_id' => array(
				'single'	=> 'Profesión',
				'type'		=> 'relation',
				'relation'	=> 'Profesiones',
				'fieldRelation' => 'id',
				'detail'	=> 'nombre',
				'conditionsOnCreate' => 'estado="A"',
				'maxlength'	=> 20,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('int')
			),
			'cargo' => array(
				'single'	=> 'Cargo de Trabajo',
				'type'		=> 'text',
				'size'		=> 50,
				'maxlength'	=> 100,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('striptags', 'extraspaces')
			),
			'envio_correspondencia' => array(
				'single'	=> 'Enviar Correspondencia por E-mail',
				'type'		=> 'closed-domain',
				'size'		=> 1,
				'maxlength'	=> 1,
				'values'	=> array(
					'S' => 'Si',
					'N' => 'No'
				),
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'notNull'	=> true,
				'filters'	=> array('onechar')
			),
			'tipo_socios_id'=> array(
				'single'		=> 'Tipo de Socio',
				'type'			=> 'relation',
				'relation'		=> 'TipoSocios',
				'fieldRelation'	=> 'id',
				'detail'		=> 'nombre',
				'conditionsOnCreate' => 'estado="A"',
				'maxlength'		=> 20,
				'notNull'		=> true,
				'notBrowse'		=> true,
				'filters'		=> array('int')
			),
			'estado_contrato' => array(
				'single'	=> 'Estado de Reserva',
				'type'		=> 'relation',
				'relation'	=> 'EstadoContrato',
				'fieldRelation' => 'codigo',
				'detail'	=> 'nombre',
				'size'		=> 2,
				'maxlength'	=> 2,
				'notNull'	=> true,
				'readOnly'	=> true,
				'filters'	=> array('alpha','striptags')
			),
			'estado_movimiento' => array(
				'single'	=> 'Estado de Movimiento',
				'type'		=> 'relation',
				'relation'	=> 'EstadoMovimiento',
				'fieldRelation' => 'codigo',
				'detail'	=> 'nombre',
				'size'		=> 2,
				'maxlength'	=> 2,
				'notNull'	=> true,
				'readOnly'	=> true,
				'filters'	=> array('alpha','striptags')
			),
			'socios_id' => array(
				'single'	=> 'Contrato asociado',
				'type'		=> 'SocioTc',
				'size'		=> 30,
				'maxlength'	=> 50,
				//'notSearch' => true,
				'notBrowse'	=> true,
				'readOnly'	=> true,
				'filters'	=> array('int')
			)
		),
		'extras' => array(
			0 => array(
				'partial'	=> 'conyuge',
				'tabName'	=> 'Segundo Titular'
			),
			1 => array(
				'partial'	=> 'membresias_socios',
				'tabName'	=> 'Membresia'
			),
			2 => array(
				'partial'	=> 'detalle_cuotas',
				'tabName'	=> 'Cuotas Iniciales'
			),
			3 => array(
				'partial'	=> 'pago_saldo',
				'tabName'	=> 'Pago de Saldo'
			),
			4 => array(
				'partial'	=> 'amortizacion',
				'tabName'	=> 'Amortización'
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	/**
	* Metodo que evalua la busqueda de conyugues y titulares
	*/
	public function beforeSearch(){
		$identificacion = $this->getPostParam('identificacion','int');

		$socios = $this->Socios->findFirst(array('conditions'=>"identificacion='{$identificacion}'"));		
		if($socios==false){
			//posiblemente esta buscando un segundo titular
			$conyuges = $this->Conyuges->findFirst(array('conditions'=>"identificacion='{$identificacion}'"));
			if($conyuges!=false){
				$socios = $this->Socios->findFirst($conyuges->getSociosId());		
				if($socios!=false){
					$this->setPostParam('id', $socios->getId());
					$this->setPostParam('identificacion', $socios->getIdentificacion());
				}
			}
		}
	}

	/**
	 * Metodo que carga cosas al crear una nueva reserva
	 */
	public function beforeNew(){
		$this->setPostParam('fecha_compra', date('Y-m-d'));
		$this->_paramsContratos(array('new' => true));
	}

	/**
	 * Metodo que se ejecuta antes de actualizar un registro
	 */
	public function beforeUpdate($transaction, $record){
		$controllerRequest = ControllerRequest::getInstance();
		//Si es cambio de contrato copiamos lo necesario
		$existeParam = $controllerRequest->isSetRequestParam('nuevo_contrato');
		if($existeParam==true){
			//$transaction->rollback($existeParam);
			$record->setValidar(false);
		}
		return true;
	}

	public function beforeValidationOnCreate($transaction, $record){
		$controllerRequest = ControllerRequest::getInstance();
		//Si es cambio de contrato copiamos lo necesario
		$existeParam = $controllerRequest->isSetRequestParam('nuevo_contrato');
		//$transaction->rollback($existeParam);
		if($existeParam==true){
			$record->setCambioContrato('S');
		}
	}

	/**
	 * Metodo privado que actualiza las demas pestañas de el hyperform de contratos
	 *
	 * @param	ActiveRecordTransaction $transaction
	 * @param	ActiveRecord $record
	 * @return	boolean
	 */
	private function _actualizarSocio($transaction, &$record){
		$controllerRequest = ControllerRequest::getInstance();
		$configActualizarSocio = array(
			'Socios'				=> $record,
			//Si es activar reserva
			'reservas_contrato'		=> $controllerRequest->getParamRequest('reservas_contrato'),
			//Si es cambio de contrato
			'nuevo_contrato'		=> $controllerRequest->getParamRequest('nuevo_contrato', 'int'),
			//Membresias
			'membresias_id'			=> $controllerRequest->getParamRequest('membresias_id', 'int'),
			'temporadas_id'			=> $controllerRequest->getParamRequest('membresias_id', 'int'),
			'capacidad'				=> $controllerRequest->getParamRequest('capacidad', 'int'),
			'puntos_ano'			=> $controllerRequest->getParamRequest('puntos_ano', 'int'),
			'numero_anos'			=> $controllerRequest->getParamRequest('numero_anos', 'int'),
			'valor_total'			=> $controllerRequest->getParamRequest('valor_total', 'double'),
			'cuota_inicial'			=> $controllerRequest->getParamRequest('cuota_inicial', 'double'),
			'derecho_afiliacion_id'	=> $controllerRequest->getParamRequest('derecho_afiliacion_id', 'int'),
			//Detalle cuota
			'hoy'					=> $controllerRequest->getParamRequest('hoy', 'double'),
			'fecha1'				=> $controllerRequest->getParamRequest('fecha1', 'date'),
			'cuota2'				=> $controllerRequest->getParamRequest('cuota2', 'double'),
			'fecha2'				=> $controllerRequest->getParamRequest('fecha2', 'date'),
			'cuota3'				=> $controllerRequest->getParamRequest('cuota3', 'double'),
			'fecha3'				=> $controllerRequest->getParamRequest('fecha3', 'date'),
			//PagoSaldo
			'numero_cuotas'			=> $controllerRequest->getParamRequest('numero_cuotas', 'double'),
			'interes'				=> $controllerRequest->getParamRequest('interes', 'double'),
			'fecha_primera_cuota'	=> $controllerRequest->getParamRequest('fecha_primera_cuota', 'date'),
			'premios_id'			=> $controllerRequest->getParamRequest('premios_id', 'int'),
			'observaciones'			=> $controllerRequest->getParamRequest('observaciones', 'striptags'),
			//Conyuges
			'conyuge_tipo_documentos_id'=> $controllerRequest->getParamRequest('conyuge_tipo_documentos_id', 'int'),
			'conyuge_identificacion'=> $controllerRequest->getParamRequest('conyuge_identificacion', 'int'),
			'conyuge_nombres'		=> $controllerRequest->getParamRequest('conyuge_nombres', 'alpha'),
			'conyuge_apellidos'		=> $controllerRequest->getParamRequest('conyuge_apellidos', 'alpha'),
			'conyuge_fecha_nacimiento'=> $controllerRequest->getParamRequest('conyuge_fecha_nacimiento', 'date'),
			'conyuge_direccion'		=> $controllerRequest->getParamRequest('conyuge_direccion', 'alpha'),
			'conyuge_telefono'		=> $controllerRequest->getParamRequest('conyuge_telefono', 'int'),
			'conyuge_celular'		=> $controllerRequest->getParamRequest('conyuge_celular', 'int'),
			'conyuge_profesiones_id'=> $controllerRequest->getParamRequest('conyuge_profesiones_id', 'int'),
			'conyuge_estados_civiles_id'=> $controllerRequest->getParamRequest('conyuge_estados_civiles_id', 'int'),
		);
		$ret = TpcContratos::actualizarSocio($transaction, $configActualizarSocio);
		
		return $ret;
	}

	/**
	 * Metodo que se ejecuta despúes de actualziar un registro
	 */
	public function afterUpdate($transaction, &$record){
		return $this->_actualizarSocio($transaction, $record);
	}

	/**
	 * Metodo que se ejecuta cuando ya se creo el registro de socios
	 *
	 * @param	ActiveRecordTransaction $transaction
	 * @param	ActiveRecord $record
	 * @return	boolean
	 */
	public function afterInsert($transaction, $record){
		//actualizamos lo demas
		return $this->_actualizarSocio($transaction, $record);
	}

	/**
	 * Metodo que carga la informacion de los partial de socios
	 *
	 * @param array $config(
	 *  edit: true/false
	 *  new:  true/false
	 * )
	 */
	private function _paramsContratos($config){
		if(isset($config['edit']) && $config['edit']==true){
			$derechoAfiliacion	= $this->DerechoAfiliacion->find(array('order'=>'id DESC'));
			$premios			= $this->Premios->find(array('order'=>'nombre ASC'));
			$membresias			= $this->Membresias->find(array('order'=>'nombre ASC'));
			$temporadas			= $this->Temporadas->find(array('order'=>'nombre ASC'));
			$tipoDocumentos		= $this->TipoDocumentos->find(array('order'=>'nombre ASC'));
			$profesiones		= $this->Profesiones->find(array('order'=>'nombre ASC'));
			$estadosCiviles		= $this->EstadosCiviles->find(array('order'=>'nombre ASC'));
		} else {
			$condition			= 'estado="A"';
			$derechoAfiliacion	= $this->DerechoAfiliacion->find(array('conditions'=>$condition, 'order'=>'id DESC'));
			$premios			= $this->Premios->find(array('conditions'=>$condition, 'order'=>'nombre ASC'));
			$membresias			= $this->Membresias->find(array('conditions'=>$condition, 'order'=>'nombre ASC'));
			$temporadas			= $this->Temporadas->find(array('conditions'=>$condition, 'order'=>'nombre ASC'));
			$tipoDocumentos		= $this->TipoDocumentos->find(array('conditions'=>$condition, 'order'=>'nombre ASC'));
			$profesiones		= $this->Profesiones->find(array('conditions'=>$condition, 'order'=>'nombre ASC'));
			$estadosCiviles		= $this->EstadosCiviles->find(array('conditions'=>$condition, 'order'=>'nombre ASC'));
		}
		$derechoAfiliacionArray = array();
		foreach($derechoAfiliacion as $dA){
			$derechoAfiliacionArray[$dA->getId()] = $dA->getTipoContrato()->getNombre().': '.Currency::number($dA->getValor());
		}
		$this->setParamToView('derechoAfiliacion', $derechoAfiliacionArray);
		$this->setParamToView('premios', $premios);
		$this->setParamToView('membresias', $membresias);
		$this->setParamToView('temporadas', $temporadas);
		$this->setParamToView('tipoDocumentos', $tipoDocumentos);
		$this->setParamToView('profesiones', $profesiones);
		$this->setParamToView('estadosCiviles', $estadosCiviles);
	}

	/**
	 * Metodo que carga los datos de un contrato en otros modelos
	 *
	 * @param ActiveRecord $record
	 */
	public function beforeEdit($record){
		$sociosId = $record->getId();
		$this->setParamToView('sociosId', $sociosId);
		//Show data
		$this->_paramsContratos(array('edit'=>true));
		$models = array('MembresiasSocios','DetalleCuota','PagoSaldo', 'conyuges');
		foreach ($models as $model){
			$modelTemp = EntityManager::get($model)->findFirst('socios_id='.$record->getId());
			if($modelTemp){
				foreach($modelTemp->getAttributes() as $field){
					//print "field: $field, value:".$modelTemp->readAttribute($field);
					if($field!='id' && $field != 'socios_id'){
						if($model=='conyuges'){
							$fieldInput = 'conyuge_'.$field;
							Tag::displayTo($fieldInput, $modelTemp->readAttribute($field));
						}else{
							Tag::displayTo($field, $modelTemp->readAttribute($field));
						}
					}
				}
			}
		}

	}

	/**
	 * Metodo que se ejecuta apra calcular los datos de memebresia de un contrato
	 */
	public function getCuotaInicialAction(){
		$this->setResponse('json');
		$valorTotal = $this->getPostParam('valorTotal','double');
		$cuotaInicial = $this->getPostParam('cuotaInicial','double');
		if($valorTotal){
			if(!$cuotaInicial){
				$cuotaInicial = $valorTotal * 0.33;
			}
			$saldoPagar = $valorTotal - $cuotaInicial;

			if($saldoPagar<0){
				return array(
					'status' => 'FAILED',
					'message' => 'El valor total no puede ser negativo'
				);
			}
			return array(
				'status' => 'OK',
				'cuotaInicial' => $cuotaInicial,
				'saldoPagar' => $saldoPagar,
			);
		}else{
			return array(
				'status' => 'FAILED',
				'message' => 'Debe proporcionar un valor a valorTotal'
			);
		}
	}

	public function getPuntosAction(){
		$this->setResponse('json');
		$puntosAno = $this->getPostParam('puntosAno','int');
		$numeroAno = $this->getPostParam('numeroAno','int');
		if($numeroAno > 0 && $puntosAno > 0){
			$totalPuntos = $numeroAno * $puntosAno;
			return array(
				'status' => 'OK',
				'totalPuntos' => $totalPuntos
			);
		}else{
			return array(
				'status' => 'OK',
				'totalPuntos' => 0
			);
		}
	}


	///////////////// COMPONENT SOCIOSTC ///////////////////


	/**
	 * Metodo que se usa en la consulta de auto complete
	 * en las demas partes para consultar un numero de accion
	 * @return string
	 */
	public function getDetalleSocioAction(){
		Core::importFromLibrary('Kumbia/ActionHelpers/Scriptaculous/','Scriptaculous.php');
		$this->setResponse('ajax');
		$numeroContrato = $this->getPostParam('numeroContrato');
		if($numeroContrato){
			//Se indica que la respuesta es AJAX
			$this->setResponse('ajax');
			//Campos del modelo utilizados para crear el resultado
			$fields = array('id', 'numero_contrato');
			//buscamos los registros
			$sociosArray = array();
			$conditions = 'numero_contrato LIKE "'.$numeroContrato.'%" OR nombres LIKE "'.$numeroContrato.'%" OR apellidos LIKE "'.$numeroContrato.'%"';
			$sociosAll = EntityManager::get('Socios')->find($conditions);
			foreach($sociosAll as $socio){
				$sociosArray[$socio->getId()] = utf8_encode($socio->getNumeroContrato()." -> ".$socio->getNombres()." ".$socio->getApellidos());
			}
			//Obtener los paises requeridos
			$sociosBusqueda = Scriptaculous::filter($numeroContrato, $sociosArray);
			//Se genera el HTML a devolver al usuario
			$htmlCode = Scriptaculous::autocomplete($sociosBusqueda);
			$this->renderText($htmlCode);
		}else{
			var_dump($_REQUEST);
		}
	}

	/**
	 * Action para autocomplete de centros por codigo
	 *
	 * @return json
	 */
	public function querySocioAction(){
		$this->setResponse('json');
		$codigoSocio = $this->getQueryParam('socio', 'alpha');
		$socios = EntityManager::get('Socios');
		if(!$codigoSocio){
			return array(
				'existe' => 'N'
			);
		}
		$socio = $socios->findFirst($codigoSocio);
		if($socio!=false){
			return array(
				'status' => 'OK',
				'existe' => 'S',
				'codigo' => $socio->getId(),
				'nombre' => utf8_encode($socio->getNumeroContrato()." - ".$socio->getNombres()." ".$socio->getApellidos())
			);
		}else{
			return array(
				'existe' => 'N'
			);
		}
	}

	public function queryBySociosAction(){
		$this->setResponse('json');
		$codigoSocio = $this->getQueryParam('socio', 'alpha');
		$socio = EntityManager::get('Socios')->findFirst($codigoSocio);
		if($socio!=false){
			return array(
				'status' => 'OK',
				'existe' => 'S',
				'codigo' => $socio->getId(),
				'nombre' => utf8_encode($socio->getNumeroContrato()." - ".$socio->getNombres()." ".$socio->getApellidos())
			);
		}else{
			return array(
				'existe' => 'N'
			);
		}
	}

	public function getNumeroContratoAction(){
		$this->setResponse('json');
		$codigoSocio = $this->getPostParam('socio', 'int');
		$socio = EntityManager::get('Socios')->findFirst($codigoSocio);
		if($socio!=false){
			return array(
				'status' => 'OK',
				'existe' => 'S',
				'codigo' => $socio->getId(),
				'nombre' => utf8_encode($socio->getNumeroContrato()." - ".$socio->getNombres()." ".$socio->getApellidos()),
				'numeroContrato' => $socio->getNumeroContrato()
			);
		}else{
			return array(
				'status' => 'FAILED',
				'existe' => 'N',
				'codigo' => $codigoSocio
			);
		}
	}

	public function queryByNameAction(){
		$this->setResponse('json');
		$response = array();
		$nombre = $this->getPostParam('nombre', 'extraspaces');
		if($nombre!=''){
			$sociosArray = EntityManager::get('Socios')->find(
			'numero_contrato LIKE \''.$nombre.'%\' OR nombres LIKE \''.$nombre.'%\' OR apellidos LIKE \''.$nombre.'%\'',
			'order: numero_contrato', 'limit: 13');
			foreach($sociosArray as $socio){
				$response[] = array(
					'status' => 'OK',
					'value' => $socio->getId(),
					'selectText' => htmlentities($socio->getNumeroContrato()." - ".$socio->getNombres()." ".$socio->getApellidos()),
					'optionText' => htmlentities($socio->getNumeroContrato()." - ".$socio->getNombres()." ".$socio->getApellidos())
				);
			}
		}
		return $response;
	}

	//////////////////// REPORTES ////////////////////////

	/**
	 * Metodo que genera tabla de amortizacion de un contrato
	 *
	 * @return json
	 */
	public function reporteAmortizacionAction(){
		$this->setResponse('json');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$rules = array(
				'sociosId' => array(
					'message' => 'Debe indicar el id del contrato',
					'filter' => 'int'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$sociosId = $this->getPostParam('sociosId', 'int');
			if(!$sociosId){
				$this->addValidationMessage($rules['sociosId']['message'],'valorTotal');
				$transaction->rollback($rules['sociosId']['message']);
			}
			$socios = EntityManager::get('Socios')->findFirst($sociosId);
			if(!$socios->getId()){
				$transaction->rollback('El id de contrato no existe');
			}
			foreach($this->getValidationMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);
		$titulo = new ReportText('TABLA DE AMORTIZACIÓN', array(
			'fontSize' => 16,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		$titulo2 = new ReportText('CÉDULA: '.$socios->getIdentificacion().' CONTRATO: '.$socios->getNumeroContrato(),
		array(
			'fontSize' => 13,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		$titulo3 = new ReportText('NOMBRES: '.$socios->getNombres().' APELLIDOS: '.$socios->getApellidos(),
		array(
			'fontSize' => 13,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		$membresiasSocios = $this->MembresiasSocios->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId));
		if($membresiasSocios==false){
			$transaction->rollback('Reporte Amortización: No existe Memebresia del contrato');
		}
		$valorTotalContrato = Currency::number($membresiasSocios->getValorTotal(),0);
		$valorCuotaInicialContrato = Currency::number($membresiasSocios->getCuotaInicial(),0);
		$valorFinanciacionContrato = Currency::number($membresiasSocios->getSaldoPagar(),0);
		$titulo4 = new ReportText('VALOR TOTAL CONTRATO: '.$valorTotalContrato.' CUOTA INICIAL: '.$valorCuotaInicialContrato.' FINANCIACIÓN: '.$valorFinanciacionContrato,
		array(
			'fontSize' => 13,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		$report->setHeader(array($titulo,$titulo2,$titulo3,$titulo4));
		$report->setDocumentTitle('Tabla de Amortización');
		$report->setColumnHeaders(array(
			'CUOTA',
			'FECHA',
			'CUOTA FIJA',
			'CAPITAL',
			'INTERES',
			'SALDO'
		));
		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));
		$report->setColumnStyle(array(0,1), new ReportStyle(array(
			'textAlign' => 'center',
			'fontSize' => 11
		)));
		$report->setColumnStyle(array(2, 3, 4, 5), new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
		)));
		$report->setColumnFormat(array(2, 3, 4, 5), new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 0
		)));
		$report->setTotalizeColumns(array(2, 3, 4, 5));
		$report->start(true);
		$empresa = $this->Empresa->findFirst();
		$totalValor = 0;
		$totalCapital = 0;
		$totalInteres = 0;
		$totalSaldo = 0;
		$arrayAmortizacionObj = EntityManager::get('Amortizacion')->find(array('conditions'=>'socios_id='.$sociosId));
		foreach($arrayAmortizacionObj as $amortizacion){
			$report->addRow(array(
				$amortizacion->getNumeroCuota(),
				$amortizacion->getFechaCuota(),
				$amortizacion->getValor(),
				$amortizacion->getCapital(),
				$amortizacion->getInteres(),
				$amortizacion->getSaldo()
			));
			$totalValor 	+= $amortizacion->getValor();
			$totalCapital 	+= $amortizacion->getCapital();
			$totalInteres 	+= $amortizacion->getInteres();
			//$totalSaldo 	+= $amortizacion->getSaldo();
		}
		$report->setTotalizeValues(array(
			2 => $totalValor,
			3 => $totalCapital,
			4 => $totalInteres,
			5 => $totalSaldo
		));
		$report->finish();
		$fileName = $report->outputToFile('public/temp/amortizacion');
		return array(
			'status'	=> 'OK',
			'file'		=> 'temp/'.$fileName
		);
	}

	/**
	 * Solicita el formato de impresión del reporte
	 */
	public function getFormatoAction(){
		$this->setResponse('view');
		$controller = $this->getControllerName();
		$sociosId = $this->getPostParam('sociosId', 'int');
		$this->setParamToView('controller', $controller);
		$this->setParamToView('sociosId', $sociosId);
	}

	/**
	 * Metodo que cambia el numero contrato por otro a un contrato activo
	 */
	public function getCambioContratoAction(){
		$tiposContratos = $this->TipoContrato->find(array('conditions'=>'estado="A"', 'order'=>'nombre ASC'));
		$this->setParamToView('tiposContratos',$tiposContratos);
		$sociosId = $this->getPostParam('sociosId','int');
		$this->setParamToView('sociosId',$sociosId);
	}

	/**
	 * Metodo que cambia el numero de contrato desde getCambioContrato
	 */
	public function cambiarContratoAction(){
		$this->setResponse('json');
		Core::importFromLibrary('Hfos/Tpc','Tpc.php');
		try{
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = $this->getPostParam('sociosId', 'int');
			$tipoContratoId = $this->getPostParam('TipoContratoId', 'int');
			//Cambiamos numero de contrato a un contrato
			$socio = $this->Socios->findFirst($sociosId);
			if($socio==false){
				$transaction->rollback('No existe el socio');
			}
			$tipoContrato = $this->TipoContrato->findFirst($tipoContratoId);
			if($tipoContrato == false){
				return array(
					'status' => 'FAILED',
					'message' => 'El tipo de contrato no es valido'
				);
			}
			//Return
			return array(
				'status' => 'OK',
				'sociosId' => $sociosId,
				'tipoContratoId' => $tipoContratoId,
				'tipoContratoNombre' => $tipoContrato->getNombre(),
				'message' => 'Complete la información nueva para el cambio de contrato'
			);
		}
		catch (TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Solicita el formato de desitir un contrato
	 */
	public function getDesistirAction(){
		$this->setResponse('view');
		$controller = $this->getControllerName();
		Tag::displayTo('fecha',date('Y-m-d'));
		$sociosId = $this->getPostParam('sociosId', 'int');
		$tipoDesistimiento = $this->EstadoDesistimiento->find(array('order'=>'nombre ASC'));
		$motivoDesistimiento = $this->MotivoDesistimiento->find(array('conditions'=>'estado="A"', 'order'=>'nombre ASC'));

		$this->setParamToView('controller', $controller);
		$this->setParamToView('sociosId', $sociosId);
		$this->setParamToView('tipoDesistimiento', $tipoDesistimiento);
		$this->setParamToView('motivoDesistimiento', $motivoDesistimiento);
	}

	/**
	 * Genera desitimiento de una reserva
	 */
	public function desistirAction(){
		$this->setResponse('json');
		try{
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = $this->getPostParam('sociosId','int');
			$estado = $this->getPostParam('estado','alpha');
			$fecha = $this->getPostParam('fecha','date');
			$motivoDesistimiento = $this->getPostParam('motivo_desistimiento','int');
			$socios = $this->Socios->findFirst($sociosId);
			if($socios == false){
				$transaction->rollback('El contrato no existe');
			}
			$statusDate = TPC::dateGreaterThan($socios->getFechaCompra(), $fecha);
			if($statusDate==true){
				$transaction->rollback('La fecha de compra del contrato debe ser mayor a la del desistimiento');
			}
			//Cambiamos numero de contrato a un contrato
			$sociosDesistimientos = new SociosDesistimientos();
			$sociosDesistimientos->setTransaction($transaction);
			$sociosDesistimientos->setSociosId($sociosId);
			$sociosDesistimientos->setFecha($fecha);
			$sociosDesistimientos->setMotivoDesistimientoId($motivoDesistimiento);
			$sociosDesistimientos->setEstadoDesistimientoId($estado);
			if($sociosDesistimientos->save()==false){
				foreach ($sociosDesistimientos->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$socios->setValidar(false);
			$socios->setEstadoContrato('AA');//Anulado
			$socios->setEstadoMovimiento($estado);
			if($socios->save()==false){
				foreach ($socios->getMessages() as $message){
					ActiveRecord::disableEvents(false);
					$transaction->rollback($message->getMessage());
				}
			}
			$socios->setValidar(true);
			//Commit
			$transaction->commit();
			//Return
			return array(
				'status' => 'OK',
				'message' => 'El contrato fue desistido'
			);
		}
		catch (TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Realiza retoma de contrato
	 *
	 * @return array json
	 */
	public function retomaAction(){
		$this->setResponse('json');
		try{
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = $this->getPostParam('sociosId','int');
			//Cambiamos numero de contrato a un contrato
			$retoma = new Retoma();
			$retoma->setTransaction($transaction);
			$retoma->setSociosId($sociosId);
			$retoma->setFecha(date('Y-m-d'));
			if($retoma->save()==false){
				foreach ($retoma->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$socios = $this->Socios->findFirst($sociosId);
			if($socios == false){
				$transaction->rollback('El contrato no existe');
			}
			ActiveRecord::disableEvents(true);
			$socios->setEstadoContrato('A');//Activo
			$socios->setEstadoMovimiento('RE');//Retoma
			if($socios->save()==false){
				foreach ($socios->getMessages() as $message){
					ActiveRecord::disableEvents(false);
					$transaction->rollback($message->getMessage());
				}
			}
			ActiveRecord::disableEvents(false);
			//Commit
			$transaction->commit();
			//Return
			return array(
				'status' => 'OK',
				'message' => 'El contrato fue retomado'
			);
		}
		catch (TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Metodo que consulta un contrato y valida si peud eo no hacer una refinanciación
	 *
	 * @return array json
	 */
	public function validarRefinanciarAction(){
		$this->setResponse('json');
		$status = 'OK';
		$message = '';
		$sociosId = $this->getPostParam('sociosId', 'int');
		$pagoSaldo = $this->PagoSaldo->findFirst(array('conditions'=>'socios_id='.$sociosId));
		if($pagoSaldo != false){
			//Si tiene numero de cuotas
			if($pagoSaldo->getNumeroCuotas()>0){
				//asignamso el saldo actual
				$amortizacion = $this->Amortizacion->findFirst(array('conditions'=>'socios_id='.$sociosId.
				' AND estado="D"', 'order'=>'numero_cuota ASC'));
				if($amortizacion != false){
					Tag::displayTo('valorPagado', $amortizacion->getSaldo());
					//Si han pagado las cuotas iniciales
					$detalleCuota = $this->DetalleCuota->findFirst(array('coditions' => 'socios_id='.$sociosId));
					if($detalleCuota->getEstado1()!='P' || $detalleCuota->getEstado2()!='P' || $detalleCuota->getEstado3()!='P'){
						$message = 'No puede refinanciar ya que aun esta pagando cuotas iniciales';
						$status = 'FAILED';
					}
				}else{
					//Si el socios es 100% pagado no puede refinanciar
					if($socios->getEstadoMovimiento()=='P'){
						$estadoMovimiento = $this->EstadoMovimiento->findFirst($socios->getEstadoMovimiento());
						if($estadoMovimiento==false){
							$message = 'El contrato tiene un estado desconocido '.$socios->getEstadoMovimiento();
						}else{
							$message = 'El contrato ya está '.$estadoMovimiento->getNombre();
						}
					}else{
						$message = 'El contrato no tiene amortización generada';
					}
					//Si han pagado las cuotas iniciales
					$detalleCuota = $this->DetalleCuota->findFirst(array('coditions' => 'socios_id='.$sociosId));
					if($detalleCuota->getEstado1()!='P' || $detalleCuota->getEstado2()!='P' || $detalleCuota->getEstado3()!='P'){
						$message = 'No puede refinanciar ya que aun esta pagando cuotas iniciales';
					}
					$status = 'FAILED';
				}
			}else{
				$message	= 'El contrato no dispone de numero de cuotas asignadas';
				$status		= 'FAILED';
			}
		}else{
			$message = 'El contrato no dispone de información en pago saldos';
		}
		return array(
			'status'	=> $status,
			'message'	=> $message
		);
	}

	/**
	 * Solicita el formato de refinanciar un contrato
	 */
	public function getRefinanciarAction(){
		$this->setResponse('view');
		$status = 'OK';
		$controller = $this->getControllerName();
		$sociosId = $this->getPostParam('sociosId', 'int');
		$pagoSaldo = $this->PagoSaldo->findFirst(array('conditions'=>'socios_id='.$sociosId));
		if($pagoSaldo!=false){
			Tag::displayTo('fecha', date('Y-m-d'));
			Tag::displayTo('numeroCuotas', $pagoSaldo->getNumeroCuotas());
			Tag::displayTo('interesCorriente', $pagoSaldo->getInteres());
			Tag::displayTo('fechaPrimeraCuota', $pagoSaldo->getFechaPrimeraCuota());
			
			//Asignamso el saldo actual
			$amortizacion = $this->Amortizacion->findFirst(array('conditions'=>'socios_id='.$sociosId.' AND estado="D"', 'order'=>'numero_cuota ASC'));
			if($amortizacion!=false){
				Tag::displayTo('valorPagado', $amortizacion->getSaldo());
			} else {
				Tag::displayTo('valorPagado', 0);
			}
			//Si han pagado las cuotas iniciales
			$detalleCuota = $this->DetalleCuota->findFirst(array('coditions' => 'socios_id='.$sociosId));
			if($detalleCuota->getEstado1()!='P' || $detalleCuota->getEstado2()!='P' || $detalleCuota->getEstado3()!='P'){
				$status = 'FAILED';
			}
			
		}else{
			$status = 'FAILED';
			$this->setParamToView('messages', 'No hay datos de pago saldo en el contrato '.$sociosId);
		}
		$this->setParamToView('status', $status);
		$this->setParamToView('controller', $controller);
		$this->setParamToView('sociosId', $sociosId);
	}

	/**
	 * Genera refinanciacion de un contrato
	 *
	 */
	public function refinanciarAction(){
		$this->setResponse('json');
		try{
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = $this->getPostParam('sociosId','int');
			$numeroCuotas = $this->getPostParam('numeroCuotas','int');
			$interesCorriente = $this->getPostParam('interesCorriente','double');
			$fechaPrimeraCuota = $this->getPostParam('fechaPrimeraCuota','date');
			$valorPagado = $this->getPostParam('valorPagado','double');
			//Consultamos datos del contrato
			$socios = $this->Socios->findFirst($sociosId);
			//Obtenemos amortización de contrato
			$amortizacionObjOld=$this->Amortizacion->find(array('conditions'=> 'socios_id='.$sociosId));
			//Almacenamos la anterior amortización en refinanciar_amortización
			/*foreach ($amortizacionObjOld as $amortizacion){
				$refinanciarAmortizacion = new RefinanciarAmortizacion();
				$refinanciarAmortizacion->setTransaction($transaction);
				foreach ($amortizacion->getAttributes() as $field){
					if($field!='id'){
						$refinanciarAmortizacion->writeAttribute($field,$amortizacion->readAttribute($field));
					}
				}
				if($refinanciarAmortizacion->save()==false){
					foreach ($refinanciarAmortizacion->getMessages() as $message){
						$transaction->rollback($message->getMessage());
					}
				}
			}*/
			//Alamcenamos los recibos_pagos anteriores
			$controlPagosObjOld = $this->ControlPagos->find(array('conditions'=> 'socios_id='.$sociosId));
			//Copia De control pagos a control_pagosh
			/*foreach ($controlPagosObjOld as $controlPagos){
				$controlPagosh = new ControlPagosh();
				$controlPagosh->setTransaction($transaction);
				foreach ($controlPagos->getAttributes() as $field){
					if($field!='id'){
						$controlPagosh->writeAttribute($field,$controlPagos->readAttribute($field));
					}
				}
				if($controlPagosh->save()==false){
					foreach ($controlPagosh->getMessages() as $message){
						$transaction->rollback($message->getMessage());
					}
				}
			}*/
			//Alamcenamos los recibos_pagos anteriores
			$recibosPagosObjOld = $this->RecibosPagos->find(array('conditions'=> 'socios_id='.$sociosId));
			//Copia De recibos pagos a recibos_pagosh
			/*foreach ($recibosPagosObjOld as $recibosPagos){
				$recibosPagosh = new RecibosPagosh();
				$recibosPagosh->setTransaction($transaction);
				foreach ($recibosPagosh->getAttributes() as $field){
					if($field!='id' && in_array($field,$recibosPagos->getAttributes())==true){
						$recibosPagosh->writeAttribute($field,$recibosPagos->readAttribute($field)); 
					} 
				}
				if($recibosPagosh->save()==false){
					foreach ($recibosPagosh->getMessages() as $message){
						$transaction->rollback($message->getMessage());
					}
				}
			}*/
			//Creamos un registro de refinanciación
			$refinanciar = EntityManager::get('Refinanciar', true)->setTransaction($transaction);
			$refinanciar->setSociosId($sociosId);
			$refinanciar->setValor($valorPagado);
			$refinanciar->setNumeroCuotas($numeroCuotas);
			$refinanciar->setInteres($interesCorriente);
			$refinanciar->setFechaPrimeraCuota($fechaPrimeraCuota);
			if($refinanciar->save()==false){
				foreach ($refinanciar->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			//ahora borramos los pagos hechos
			$controlPagos = EntityManager::get('ControlPagos');
			$controlPagos->setTransaction($transaction);
			$controlPagos->delete(array('conditions'=>'socios_id='.$sociosId));
			//ahora borramos los pagos hechos
			$recibosPagos = EntityManager::get('RecibosPagos');
			$recibosPagos->setTransaction($transaction);
			$recibosPagos->delete(array('conditions'=>'socios_id='.$sociosId));
			//Generamos la nueva amortizacion
			TPC::remplazarAmortizacion($socios,array(
				'fechaInicial'	=> $fechaPrimeraCuota,
				'interes' 		=> $interesCorriente,
				'cuotas'		=> $numeroCuotas,
				'valorTotal'	=> $valorPagado
			),$transaction);
			/*//Ingresamos registro de control pago
			$control_pago = new ControlPagos();
			$control_pago->socios_tpc_id=$id;
			$control_pago->pagado=0;
			$control_pago->dias_pagado=0;
			$control_pago->capital=0;
			$control_pago->interes=0;
			$control_pago->dias_corriente=0;
			$control_pago->mora=0;
			$control_pago->dias_mora=0;
			$control_pago->fecha_pago=$fecha_upago;
			$control_pago->saldo=$this->request("valor_financiar");
			$control_pago->estado='V';
			//$control_pago->recibos_pagos_id=0;
			if(!$control_pago->save()){
				$control_pago->rc=$control_pago->id;
				$control_pago->save();
				$this->SociosTpc->imagen_socio='';
				return $this->route_to("controller: maestro_socios_tpc", "action: membresia");
			}*/
			
			//Copiamos a historia los datos
			$configHistoria = array(
				'sociosId'			=> $socios->getId(),
				'notaHistoria'  	=> array(
					'estado'		=> 'R', //Refinanciación
					'fecha'			=> date('Y-m-d'),
					'observaciones' => 'Se realizó refinanciación de contrato ('.$socios->getNumeroContrato().')',
					'copiarContrato'=> true //copia el contenido del contrato en sus repectivas tabla h(historia)
				),
				'debug'				=> true
			);
			TPC::copiarAHistoria($configHistoria, $transaction);
		
			//Commit
			$transaction->commit();
			//Return
			return array(
				'status'	=> 'OK',
				'message'	=> 'El contrato fue refinanciado'
			);
		}
		catch (TransactionFailed $e){
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
	}

	/**
	 * Metodo que calcula la cuota inicial recomendada para un cambio de contrato segun
	 * lo pagado en capital
	 *
	 * @return json
	 */
	public function getCuotaInicialCambioContratoAction(){
		$this->setResponse('json');
		try{
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = $this->getPostParam('id','int');
			if(!$sociosId){
				$transaction->rollback('El id del contrato es requerido');
			}
			$config = array(
				'SociosId' => $sociosId
			);
			TPC::getCuotaInicialCambioContrato($config, $transaction);
			$saldoPagar			= $config['saldoPagar'];
			$detalleCuotaTotal	= $config['detalleCuotaTotal'];
			$capitalTotal		= $config['capitalTotal'];
			$nuevaCuotaInicial	= $config['nuevaCuotaInicial'];
			if($nuevaCuotaInicial<=0){
				$membresiaSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId));
				if($membresiaSocios==false){
					$transaction->rollback('El contrato no tien definnido un valor de cuotas inciales');
				}
				$nuevaCuotaInicial = LocaleMath::round(($membresiaSocios->getValorTotal() * 0.33),0);
			}
			
			//Return
			return array(
				'status'			=> 'OK',
				'message'			=> 'El valor de cuota inicial recomendado',
				'capitalTotal'		=> $capitalTotal,
				'cuotasIniciales'	=> $detalleCuotaTotal,
				'valor'				=> $nuevaCuotaInicial,
				'saldoPagar'		=> $saldoPagar - $nuevaCuotaInicial
			);
		}catch(TransactionFailed $e){
			return array(
				'status'	=> 'FAILED',
				'message'	=> 'getCuotaInicialCambioContratoAction: '.$e->getMessage()
			);
		}
	}
}
