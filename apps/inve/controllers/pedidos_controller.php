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

/**
 * PedidosController
 *
 * Controlador de los pedidos
 *
 */
class PedidosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Movihead',
		'plural' => 'pedidos al almacén',
		'single' => 'pedido al almacén',
		'genre' => 'M',
		'tabName' => 'Pedido',
		'preferedOrder' => 'numero',
		'haveDetail' => true,
		'icon' => 'order-149.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'almacen' => array(
				'single' => 'Almacén Solicitud',
				'type' => 'relation',
				'relation' => 'Almacenes',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_almacen',
				'cacher' => array('BackCacher', 'getAlmacen'),
				'primary' => true,
				'filters' => array('int')
			),
			'numero' => array(
				'single' => 'Número',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'primary' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'fecha' => array(
				'single' => 'Fecha',
				'type' => 'date',
				'default' => '',
				'filters' => array('date')
			),
			'almacen_destino' => array(
				'single' => 'Almacén Destino',
				'type' => 'relation',
				'relation' => 'Almacenes',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_almacen',
				'filters' => array('int')
			),
			'nit' => array(
				'single' => 'Tercero Convención',
				'type' => 'tercero',
				'size' => 10,
				'maxlength' => 14,
				'notBrowse' => true,
				'filters' => array('terceros')
			),
			'centro_costo' => array(
				'single' => 'Centro de Costo',
				'type' => 'relation',
				'relation' => 'Centros',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_centro',
				'filters' => array('int')
			),
			'f_vence' => array(
				'single' => 'Fecha de Vencimiento',
				'type' => 'date',
				'notBrowse' => true,
				'default' => '',
				'filters' => array('date')
			),
			'observaciones' => array(
				'single' => 'Observaciones',
				'type' => 'textarea',
				'rows' => 2,
				'cols' => 40,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags')
			),
			'v_total' => array(
				'single' => 'Valor Total',
				'type' => 'decimal',
				'size' => 10,
				'maxlength' => 10,
				'decimals' => 2,
				'notSearch' => true,
				'readOnly' => true,
				'filters' => array('float')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'readOnly' => true,
				'values' => array(
					'A' => 'ABIERTO',
					'C' => 'CERRADO'
				),
				'filters' => array('onechar')
			)
		),
		'detail' => array(
			'relation' => array('comprob', 'numero'),
			'model' => 'Movilin',
			'tabName' => 'Detalle',
			'fields' => array(
                'item' => array(
					'single' => 'Referencia',
					'type' => 'itemReceta',
					'notNull' => true,
					'size' => 8,
					'maxlength' => 15,
					'filters' => array('alpha')
				),
				'unidad' => array(
					'single' => 'Unidad',
					'type' => 'relation',
					'relation' => 'Unidad',
					'fieldRelation' => 'codigo',
					'detail' => 'nom_unidad',
					'size' => 8,
					'maxlength' => 15,
					'filters' => array('alpha')
				),
				'cantidad' => array(
					'single' => 'Cantidad',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 10,
					'maxlength' => 15,
					'filters' => array('float')
				),
				'valor' => array(
					'single' => 'Valor',
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
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function getEditTitle($title, $record){
		return $title.' '.$record->getNumero();
	}

	protected function beforeIndex(){
		Tag::displayTo('almacen', '1');
	}

	protected function beforeNew(){
		$date = new Date();
		Tag::displayTo('almacen', '1');
		Tag::displayTo('forma_pago', '3');
		Tag::displayTo('fecha', $date->getDate());
		$this->loadModel('Configuration');
		$diasVence = Settings::get('d_vence');
		if($diasVence==null){
			$days = 15;
		} else {
			$days = $diasVence;
		}
		Tag::displayTo('f_vence', $date->addDays($days));
	}

	public function saveAction(){
		$this->setResponse('json');
		$request = $this->getRequestInstance();
		$almacen = $request->getParamPost('almacen', 'int');
		$numero = $request->getParamPost('numero', 'int');
		$fecha = $request->getParamPost('fecha', 'date');
		try {
			$comprob = sprintf('P%02s',$almacen);
			$tatico = new Tatico($comprob, $numero, $fecha);
			$movement = array(
				'Comprobante' => $comprob,
				'Fecha' => $fecha,
				'Almacen' => $almacen,
				'Nit' => $request->getParamPost('nit', 'terceros'),
				'FechaVencimiento' => $request->getParamPost('f_vence', 'date'),
				'CentroCosto' => $request->getParamPost('centro_costo', 'int'),
				'NPedido' => $request->getParamPost('n_pedido','int'),
				'AlmacenDestino' => $request->getParamPost('almacen_destino', 'int'),
				'Observaciones' => $request->getParamPost('observaciones', 'striptags')
			);
			$addDetail = array();
			$removeDetail = array();
			$item = $request->isSetRequestParam('item') ? $request->getParamPost('item') : array();
			$cantidad = $request->isSetRequestParam('cantidad') ? $request->getParamPost('cantidad') : array();
			$valor = $request->isSetRequestParam('valor') ? $request->getParamPost('valor') : array();
			$action = $request->isSetRequestParam('action') ? $request->getParamPost('action') : array();
			for($i=0;$i<count($item);$i++){
				if($action[$i]=='add'){
					$addDetail[] = array(
						'Item' => $item[$i],
						'Cantidad' => $cantidad[$i],
						'Valor' => $valor[$i],
					);
				} else {
					if($action[$i]=='del'){
						$removeDetail[] = array(
							'Item' => $item[$i],
							'Cantidad' => $cantidad[$i],
							'Valor' => $valor[$i],
						);
					}
				}
			}
			$movement['Detail'] = $addDetail;
			$movement['removeDetail'] = $removeDetail;
			$tatico->addMovement($movement);
		}
		catch(TaticoException $te){
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage(),
				'code' => $te->getCode()
			);
		}
		$numeros = $tatico->getLastConsecutivos();
		$primaryKey = array(
			'comprob='.$comprob,
			'almacen='.$almacen,
			'numero='.$numeros['inve'],
		);
		return array(
			'status' => 'OK',
			'numero' => $numeros['inve'],
			'message' => 'Se grabó el pedido al almacén con el número: "'.$numeros['inve'].'"',
			'type' => 'insert',
			'primary' => join('&', $primaryKey)
		);
	}

	public function deleteAction(){
		$this->setResponse('json');
		$movement = array();
		$request = ControllerRequest::getInstance();
		if($request->isSetQueryParam('almacen')){
			$movement['Almacen'] = $request->getParamQuery('almacen', 'alpha');
		} else {
			$movement['Almacen'] = 1;
		}
		$movement['Comprobante'] = sprintf("P%02s", $movement['Almacen']);
		if($request->isSetQueryParam('numero')){
			$movement['Numero'] = $request->getParamQuery('numero', 'int');
		} else {
			$movement['Numero'] = 0;
		}
		try {
			$tatico = new Tatico($movement['Comprobante'], $movement['Numero']);
			$tatico->delMovement($movement);
		}
		catch(TaticoException $te){
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage()
			);
		}
		return array(
			'status' => 'OK',
			'message' => 'El Traslado ha sido eliminado correctamente'
		);
	}

	protected function beforeSearch(){
		$request = ControllerRequest::getInstance();
		$almacen = $request->getParamRequest('almacen','alpha');
		if($almacen!=''){
			return array(
				"comprob = 'P".sprintf('%02s',$almacen)."'"
			);
		} else {
			return array(
				"comprob like 'P%'"
			);
		}
	}

	/**
	 * Genera el nombre del item en el detalle
	 *
	 * @param	string $detail
	 * @return	array
	 */
	public function describeDetail($detail){
		$inve = BackCacher::getInve($detail->getItem());
		if($inve==false){
			$descripcion = '';
			$unidad = '';
		} else {
			$descripcion = $inve->getDescripcion();
			$unidad = $inve->getUnidad();
		}
		return array(
			'id' => $detail->getId(),
			'item' => $detail->getItem(),
			'item_det' => $descripcion,
			'unidad' => $unidad,
			'cantidad' => LocaleMath::round($detail->getCantidad(), 3),
			'valor' => LocaleMath::round($detail->getValor(), 2),
			'iva' => $detail->getIva()
		);
	}

	public function consultarAction(){
		$this->setResponse('view');

		$codigoAlmacen = $this->getPostParam('almacen', 'int');
		Tag::displayTo('almacenConsulta', $codigoAlmacen);

		$fecha = new Date();
		$comprob = sprintf('P%02s', $codigoAlmacen);
		$pedidos = $this->Movihead->find(array("comprob='$comprob' AND estado='A'"));
		foreach($pedidos as $pedido){
			if(Date::isEarlier($pedido->getFVence(), $fecha)){
				$pedido->setEstado('C');
				if($pedido->save()==false){
					foreach($pedido->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
			}
		}

		$pedidos = $this->Movihead->find(array("comprob='$comprob' AND estado='A'", "columns" => "almacen,almacen_destino,numero,fecha,estado"));
		$this->setParamToView('pedidos', $pedidos);

		$this->setParamToView('almacenes', $this->Almacenes->find("estado='A'"));
		$this->setParamToView('estados', array(
			'A' => 'ABIERTO',
			'C' => 'CERRADO'
		));
	}

	public function getFechaLimiteAction(){
		$this->setResponse('view');

		$numero = $this->getPostParam('numero', 'int');
		$nombreAlmacen = $this->getPostParam("almacen", "striptags");

		$almacen = $this->Almacenes->findFirst("nom_almacen='$nombreAlmacen'");
		if($almacen==false){
			Flash::error('No existe el almacén asociado');
			return;
		}

		$comprob = sprintf('P%02s', $almacen->getCodigo());
		$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
		if($movihead==false){
			Flash::error('No existe el pedido');
			return;
		}

		$diasVence = Settings::get('d_vence');
		if($diasVence==null){
			$days = 15;
		} else {
			$days = $diasVence;
		}
		$fechaVence = new Date();
		$fechaVence->addDays($diasVence);
		Tag::displayTo('fechaVence', (string) $fechaVence);

		$this->setParamToView('almacen', $almacen->getCodigo());
		$this->setParamToView('numero', $numero);
	}

	public function buscarAction(){
		$this->setResponse('view');

		$codigoAlmacen = $this->getPostParam('almacenConsulta', 'int');
		$estado = $this->getPostParam('estadoPedido', 'onechar');

		$comprob = sprintf('P%02s', $codigoAlmacen);
		$pedidos = $this->Movihead->find(array("comprob='$comprob' AND estado='$estado'", "columns" => "almacen,almacen_destino,numero,fecha,estado"));
		$this->setParamToView('pedidos', $pedidos);

		View::renderPartial('resultados');
	}

	public function doReportAction(){
		$this->setResponse('json');
		try {
			$reportType = $this->getPostParam('reportType', 'alpha');
			$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
			$codigoAlmacen = $this->getPostParam('codigoAlmacen', 'int');
			$numero = $this->getPostParam('numero', 'int');

			$fileUri = Tatico::getPrintUrl($reportType, $codigoComprobante, $codigoAlmacen, $numero);
			return array(
				'status' => 'OK',
				'file' => $fileUri
			);
		}
		catch(TaticoException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function puedeReAbrirAction(){
		$this->setResponse('json');
		$nombreAlmacen = $this->getPostParam("almacen", "striptags");
		$almacen = $this->Almacenes->findFirst("nom_almacen='$nombreAlmacen'");
		if($almacen==false){
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el almacén asociado'
			);
		}
		$numero = $this->getPostParam('numero', 'int');
		$comprob = sprintf('P%02s', $almacen->getCodigo());
		$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
		if($movihead==false){
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el pedido'
			);
		} else {
			if($movihead->getEstado()!="C"){
				return array(
					'status' => 'FAILED',
					'message' => 'El pedido no está cerrado'
				);
			} else {
				$comprob = sprintf('C%02s', $almacen->getCodigo());
				$movihead = $this->Movihead->findFirst("comprob='$comprob' AND n_pedido='$numero'");
				if($movihead!=false){
					return array(
						'status' => 'FAILED',
						'message' => 'No se puede re-abrir porque ya se hizo una salida asociada al almacén '.$movihead->getComprob().'-'.$movihead->getNumero()
					);
				} else {
					$comprob = sprintf('T%02s', $almacen->getCodigo());
					$movihead = $this->Movihead->findFirst("comprob='$comprob' AND n_pedido='$numero'");
					if($movihead!=false){
						return array(
							'status' => 'FAILED',
							'message' => 'No se puede re-abrir porque ya se hizo una traslado asociada al almacén '.$movihead->getComprob().'-'.$movihead->getNumero()
						);
					} else {
						return array(
							'status' => 'OK'
						);
					}
				}
			}
		}
	}

	public function reAbrirAction(){
		$this->setResponse('json');

		try {

			$almacen = $this->getPostParam('almacen', 'int');
			$numero = $this->getPostParam('numero', 'int');
			$fechaVence = $this->getPostParam('fechaVence', 'date');

			$comprob = sprintf('P%02s', $almacen);
			$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
			if($movihead==false){
				return array(
					'status' => 'FAILED',
					'message' => 'No existe la orden de compra'
				);
			}

			$fechaVence = new Date($fechaVence);
			if($fechaVence->isPast()){
				return array(
					'status' => 'FAILED',
					'message' => 'La fecha de vencimiento no puede estar en el pasado'
				);
			}

			$movihead->setFVence((string)$fechaVence);
			$movihead->setEstado('A');
			if($movihead->save()==false){
				foreach($movihead->getMessages() as $message){
					return array(
						'status' => 'FAILED',
						'message' => $message->getMessage()
					);
				}
			}

			return array(
				'status' => 'OK'
			);

		}
		catch(DateException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Enviar por correo irden de compra
	 */
	public function sendAction() 
	{
		$this->setResponse('json');
		try 
		{
			Core::importFromLibrary('Hfos/Delivery', 'Delivery.php');	
			
			$reportType = "pdf";
			$almacen = $this->getPostParam('almacen', 'int');
			$numero = $this->getPostParam('numero', 'int');

			$comprob = sprintf('P%02s', $almacen);
			$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
			if ($movihead==false) {
				return array(
					'status' => 'FAILED',
					'message' => 'No existe la orden de compra'
				);
			}
			//Correo de Pedidos
			$email = Settings::get('correo_pedidos', 'IN');
			if (!$email) {
				throw new Exception("El correo a enviar pedidos no se ha definido en configuración", 1);
			}
			//Encargado de Almacen
			$nombre = "Encargado de Almacen";
			$usuarios = $movihead->getAlmacenes()->getUsuarios();
			if (!$usuarios) {
				$nombre = $usuarios->getNombre();
			}
			//File Name
			$fileUri = Tatico::getPrintUrl($reportType, $movihead->getComprob(), $almacen, $numero);
			//Enviamos correo
			$extra = array($fileUri => KEF_ABS_PATH . "public/" . $fileUri);
			if (!HfosDelivery::sendInvoice($email, $comprob, $comprob, $nombre, $extra, 'pedidosAlmacen')) {
				$error = HfosDelivery::getLastError();
				throw new Exception($error, 1);
			}	
			return array(
				'status' => 'OK',
				'message' => 'Se ha enviado el pedido al correo'
			);
		}
		catch(TaticoException $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
}
