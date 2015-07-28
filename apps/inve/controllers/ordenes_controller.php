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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

/**
 * OrdenesController
 *
 * Controlador de las ordenes de servicio
 *
 */
class OrdenesController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Movihead',
		'plural' => 'órdenes de compra',
		'single' => 'orden de compra',
		'genre' => 'F',
		'tabName' => 'Orden',
		'preferedOrder' => 'numero DESC',
		'icon' => 'ordenes.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'almacen' => array(
				'single' => 'Almacén',
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
			'nit' => array(
				'single' => 'Proveedor',
				'cacher' => array('BackCacher', 'getTercero'),
				'type' => 'tercero',
				'size' => 10,
				'maxlength' => 14,
				'filters' => array('terceros')
			),
			'fecha' => array(
				'single' => 'Fecha',
				'type' => 'date',
				'default' => '',
				'filters' => array('date')
			),
			'forma_pago' => array(
				'single' => 'Forma de Pago',
				'type' => 'relation',
				'notBrowse' => true,
				'relation' => 'FormaPago',
				'fieldRelation' => 'codigo',
				'detail' => 'descripcion',
				'filters' => array('alpha')
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
					'A' => 'ABIERTA',
					'C' => 'CERRADA'
				),
				'filters' => array('onechar')
			)
		),
		'detail' => array(
			'relation' => array('comprob', 'numero'),
			'model' => 'Movilin',
			'tabName' => 'Detalle',
			'fields' => array(
                'item'   => array(
					'single'   => 'Referencia',
					'type'     => 'itemReceta',
					'notNull'  => true,
					'size'     => 7,
					'maxlength' => 15,
					'filters'  => array('alpha')
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
					'size' => 7,
					'maxlength' => 15,
					'filters' => array('float')
				),
				'valor' => array(
					'single' => 'Valor',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 10,
					'maxlength' => 15,
					'filters' => array('float')
				),
				'iva' => array(
					'single' => 'Iva',
					'notNull' => true,
					'type' => 'closed-domain',
					'values' => array(
						'0' => '0 %',
						'5' => '5 %',
						'10' => '10 %',
						'16' => '16 %'
					),
					'useDummy' => false,
					'align' => 'right',
					'filters' => array('float')
				)
			),
			'keys' => array(
				'unique_index' => array(
					'item'
				)
			)
		),
		'extras' => array(
			0 => array(
				'partial' => 'criterios',
				'tabName' => 'Criterios',
			),
			1 => array(
				'relation' => array('comprob', 'almacen', 'numero'),
				'model' => 'Movih1',
				'tabName' => 'Totales',
				'fields' => array(
					'iva16r' => array(
						'single' => 'IVA 16% Retenido',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva16d' => array(
						'single' => 'IVA 16% Descontable',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva10r' => array(
						'single' => 'IVA 10% Retenido',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva10d' => array(
						'single' => 'IVA 10% Descontable',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva5r' => array(
						'single' => 'IVA 5% Retenido',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva5d' => array(
						'single' => 'IVA 5% Descontable',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'retencion' => array(
						'single' => 'Retención',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'ica' => array(
						'single' => 'ICA',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'horti' => array(
						'single' => 'Retención Hortifrutícula',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'cree' => array(
						'single' => 'Retención CREE',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'impo' => array(
						'single' => 'IVA Costo/Gasto',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'total_neto' => array(
						'single' => 'Total Orden',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'saldo' => array(
						'single' => 'Total Impuestos',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'spacer3' => array(
						'type' => 'spacer1'
					),
					'total' => array(
						'single' => 'Total a Pagar',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					)
				),
			)
		)
	);

	public function getEditTitle($title, $record) 
	{
		return $title.' '.$record->getNumero();
	}

	protected function beforeNew() 
	{

		$date = new Date();
		Tag::displayTo('almacen', '1');
		Tag::displayTo('forma_pago', '3');
		Tag::displayTo('fecha', $date->getDate());

		$diasVence = Settings::get('d_vence');
		if ($diasVence==null) {
			$days = 15;
		} else {
			$days = $diasVence;
		}
		Tag::displayTo('f_vence', $date->addDays($days));

		//Cargar los criterios que evaluan
		$valores = array();
		$criterioPuntos = Settings::get('criterio_puntos');
		$criterios = $this->Criterios->find(array("estado='A' AND tipo='O'", "order" => "puntaje DESC,nombre"));
		foreach ($criterios as $criterio) {
			if ($criterioPuntos == 'N') {
				$valores[$criterio->getId()] = array('N' => 'NO', 'S' => 'SI');
				Tag::displayTo('criterio'.$criterio->getId(), 'N');
			} else {
				foreach (range(0, $criterio->getPuntaje()) as $valor) {
					$valores[$criterio->getId()][$valor] = $valor;
				}
				$medio = floor($criterio->getPuntaje()/2);
				Tag::displayTo('criterio'.$criterio->getId(), $medio);
			}
		}
		$this->setParamToView('valores', $valores);
		$this->setParamToView('criteriosActivos', $criterios);

		//Valores predeterminados para la pestaña de totales
		Tag::displayTo('iva16r', 0);
		Tag::displayTo('iva16d', 0);
		Tag::displayTo('iva10r', 0);
		Tag::displayTo('iva10d', 0);
		Tag::displayTo('iva5r', 0);
		Tag::displayTo('iva5d', 0);
		Tag::displayTo('retencion', 0);
		Tag::displayTo('ica', 0);
		Tag::displayTo('horti', 0);
		Tag::displayTo('cree', 0);
		Tag::displayTo('impo', 0);
		Tag::displayTo('saldo', 0);
		Tag::displayTo('total_neto', 0);
		Tag::displayTo('v_total', 0);
	}

	public function beforeEdit()
	{

		$request = ControllerRequest::getInstance();
		$almacen = $request->getParamRequest('almacen', 'int');
		$numero = $request->getParamRequest('numero', 'int');
		$comprob = 'O'.sprintf('%02s', $almacen);

		$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
		if ($movihead==false) {
			Flash::error('La orden de compra no existe');
			return false;
		}

		//Cargar criterios
		$valores = array();
		$nit = $movihead->getNit();
		$criterioPuntos = Settings::get('criterio_puntos');
		if ($criterioPuntos=='N') {
			$conditions = "comprob='$comprob' AND numero='$numero' AND almacen='$almacen'";
			$criterioProveedor = $this->Criterio->findFirst($conditions);
			if ($criterioProveedor==false) {
				$criterioProveedor = new Criterio();
			}
		}
		$criterios = $this->Criterios->find(array("estado='A' AND tipo='O'", "order" => 'puntaje DESC,nombre'));
		foreach ($criterios as $criterio) {
			if ($criterioPuntos=='N') {
				$prefijo = strtolower($criterio->getPrefijo());
				if ($criterioProveedor->hasField($prefijo)) {
					Tag::displayTo('criterio'.$criterio->getId(), $criterioProveedor->readAttribute($prefijo));
				} else {
					Tag::displayTo('criterio'.$criterio->getId(), 'N');
				}
				$valores[$criterio->getId()] = array('N' => 'NO', 'S' => 'SI');
			} else {
				foreach (range(0, $criterio->getPuntaje()) as $valor) {
					$valores[$criterio->getId()][$valor] = $valor;
				}
				$conditions = "comprob='$comprob' AND numero='$numero' AND almacen='$almacen' AND nit='$nit' AND criterios_id='{$criterio->getId()}'";
				$criterioProveedor = $this->CriteriosProveedores->findFirst($conditions);
				if ($criterioProveedor==false) {
					$medio = floor($criterio->getPuntaje()/2);
					Tag::displayTo('criterio'.$criterio->getId(), $medio);
				} else {
					Tag::displayTo('criterio'.$criterio->getId(), $criterioProveedor->getPuntaje());
				}
			}
		}
		$this->setParamToView('valores', $valores);
		$this->setParamToView('criteriosActivos', $criterios);

		//print_r($);

		$fields = array(
			'retencion' => 'retencion',
			'ica' => 'ica',
			'iva' => 'iva16d',
			'descuento' => 'iva16r',
			'ivad' => 'iva10d',
			'ivam' => 'iva10r',
			'total_neto' => 'total_neto'
		);
		foreach ($fields as $field => $nameField) {
			Tag::displayTo($nameField, (int) $movihead->readAttribute($field));
		}
		$movih1 = $this->Movih1->findFirst("comprob='$comprob' AND numero='$numero'");
		if ($movih1 != false) {
			$fields = array(
				'iva5' => 'iva5d',
				'retiva5' => 'iva5r',
				'reten1' => 'horti',
				'cree' => 'cree',
				'impo' => 'impo'
			);
			foreach ($fields as $field => $nameField) {
				Tag::displayTo($nameField, (int) $movih1->readAttribute($field));
			}
		}

	}

    /**
     * Metodo que ejecuta el boton Grabar
     *
     */
	public function saveAction() {
		$this->setResponse('json');
		$request = $this->getRequestInstance();
		$almacen = $request->getParamPost('almacen', 'int');
		$numero = $request->getParamPost('numero', 'int');
		$fecha = $request->getParamPost('fecha', 'date');
		try {
			$comprob = sprintf('O%02s',$almacen);
			$tatico = new Tatico($comprob, $numero, $fecha);
			$movement = array(
				'Comprobante' => $comprob,
				'Fecha' => $fecha,
				'Almacen' => $almacen,
				'Nit' => $request->getParamPost('nit', 'terceros'),
				'FechaVencimiento' => $request->getParamPost('f_vence', 'date'),
				'FechaEntrega' => $request->getParamPost('f_vence', 'date'),
				'AlmacenDestino' => $almacen,
				'FormaPago' => $request->getParamPost('forma_pago', 'int'),
				'Observaciones' => $request->getParamPost('observaciones', 'striptags'),
			);
			$addDetail = array();

			$criterios = $this->Criterios->find(array("estado='A' AND tipo='O'"));
			foreach ($criterios as $criterio) 
			{
				$movement['Criterios'][$criterio->getId()] = $request->getParamPost('criterio'.$criterio->getId(), 'alpha');
				unset($criterio);
			}

			$totales = array(
				'Iva16R' => $request->getParamPost('iva16r', 'float'),
				'Iva16D' => $request->getParamPost('iva16d', 'float'),
				'Iva10R' => $request->getParamPost('iva10r', 'float'),
				'Iva10D' => $request->getParamPost('iva10d', 'float'),
				'Iva5R' => $request->getParamPost('iva5r', 'float'),
				'Iva5D' => $request->getParamPost('iva5d', 'float'),
				'Retencion' => $request->getParamPost('retencion', 'float'),
				'Ica' => $request->getParamPost('ica', 'float'),
				'Horti' => $request->getParamPost('horti', 'float'),
				'Cree' => $request->getParamPost('cree', 'float'),
				'Impo' => $request->getParamPost('impo', 'float'),
			);
			$movement['Totales'] = $totales;
			$item = $request->isSetRequestParam('item') ? $request->getParamPost('item') : array();
			$cantidad = $request->isSetRequestParam('cantidad') ? $request->getParamPost('cantidad') : array();
			$valor = $request->isSetRequestParam('valor') ? $request->getParamPost('valor') : array();
			$iva = $request->isSetRequestParam('iva') ? $request->getParamPost('iva') : array();
			$action = $request->isSetRequestParam('action') ? $request->getParamPost('action') : array();
			$numItems = count($item);
			for ($i=0;$i<$numItems;$i++) {
				$addDetail[] = array(
					'Item' => $item[$i],
					'Cantidad' => $cantidad[$i],
					'Valor' => $valor[$i],
					'Iva' => $iva[$i]
				);
			}
			$movement['Detail'] = $addDetail;
			$movement['removeDetail'] = array();
			$tatico->addMovement($movement);
		}
		catch(TaticoException $te) {
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
			'message' => 'Se grabó la orden de compra con el número: "'.$numeros['inve'].'"',
			'type' => 'insert',
			'primary' => join('&', $primaryKey)
		);
	}

	public function deleteAction() 
	{

		$this->setResponse('json');

		$movement = array();
		$request = ControllerRequest::getInstance();
		if ($request->isSetQueryParam('almacen')) {
			$movement['Almacen'] = $request->getParamQuery('almacen', 'alpha');
		} else {
			$movement['Almacen'] = 1;
		}
		$movement['Comprobante'] = sprintf("O%02s", $movement['Almacen']);
		if ($request->isSetQueryParam('numero')) {
			$movement['Numero'] = $request->getParamQuery('numero', 'int');
		} else {
			$movement['Numero'] = 0;
		}
		try {
			$tatico = new Tatico($movement['Comprobante'], $movement['Numero']);
			$tatico->delMovement($movement);
		}
		catch(TaticoException $te) {
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage(),
				'code' => $te->getCode()
			);
		}
		return array(
			'status' => 'OK',
			'message' => 'La orden de compra ha sido eliminada correctamente'
		);
	}

	protected function beforeSearch() 
	{
		$request = ControllerRequest::getInstance();
		$almacen = $request->getParamRequest('almacen', 'int');
		if ($almacen>0) {
			return array(
				"comprob = 'O".sprintf('%02s', $almacen)."'"
			);
		} else {
			return array(
				"comprob LIKE 'O%'"
			);
		}
	}

	/**
	 * Genera el nombre del item en el detalle
	 *
	 * @param	string $detail
	 * @return	array
	 */
	public function describeDetail($detail) 
	{
		$inve = BackCacher::getInve($detail->getItem());
		if ($inve==false) {
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
			'cantidad' => LocaleMath::round($detail->getCantidad(),2),
			'valor' => LocaleMath::round($detail->getValor(),2),
			'iva' => $detail->getIva()
		);
	}

	public function initialize() 
	{
		$hortifruticula = Settings::get('hortifruticula');
		if ($hortifruticula!='S') {
			unset(self::$_config['extras'][1]['fields']['horti']);
			unset(self::$_config['extras'][1]['fields']['spacer0']);
		}
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function puedeReAbrirAction() 
	{
		$this->setResponse('json');
		$nombreAlmacen = $this->getPostParam("almacen", "striptags");
		$almacen = $this->Almacenes->findFirst("nom_almacen='$nombreAlmacen'");
		if ($almacen==false) {
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el almacén asociado'
			);
		}
		$numero = $this->getPostParam('numero', 'int');
		$comprob = sprintf('O%02s', $almacen->getCodigo());
		$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
		if ($movihead==false) {
			return array(
				'status' => 'FAILED',
				'message' => 'No existe la orden de compra'
			);
		} else {
			if ($movihead->getEstado()!="C") {
				return array(
					'status' => 'FAILED',
					'message' => 'La orden de compra no está cerrada'
				);
			} else {
				$comprob = sprintf('E%02s', $almacen->getCodigo());
				$movihead = $this->Movihead->findFirst("comprob='$comprob' AND n_pedido='$numero'");
				if ($movihead==false) {
					return array(
						'status' => 'OK'
					);
				} else {
					return array(
						'status' => 'FAILED',
						'message' => 'No se puede re-abrir porque ya se le hizo la entrada al almacén '.$movihead->getComprob().'-'.$movihead->getNumero()
					);
				}
			}
		}
	}

	public function getFechaLimiteAction() 
	{
		$this->setResponse('view');

		$numero = $this->getPostParam('numero', 'int');
		$nombreAlmacen = $this->getPostParam("almacen", "striptags");

		$almacen = $this->Almacenes->findFirst("nom_almacen='$nombreAlmacen'");
		if ($almacen==false) {
			Flash::error('No existe el almacén asociado');
			return;
		}

		$comprob = sprintf('O%02s', $almacen->getCodigo());
		$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
		if ($movihead==false) {
			Flash::error('No existe la orden de compra');
			return;
		}

		$diasVence = Settings::get('d_vence');
		if ($diasVence==null) {
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

	public function reAbrirAction() 
	{
		$this->setResponse('json');

		try 
		{

			$almacen = $this->getPostParam('almacen', 'int');
			$numero = $this->getPostParam('numero', 'int');
			$fechaVence = $this->getPostParam('fechaVence', 'date');

			$comprob = sprintf('O%02s', $almacen);
			$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
			if ($movihead==false) {
				return array(
					'status' => 'FAILED',
					'message' => 'No existe la orden de compra'
				);
			}

			$fechaVence = new Date($fechaVence);
			if ($fechaVence->isPast()) {
				return array(
					'status' => 'FAILED',
					'message' => 'La fecha de vencimiento no puede estar en el pasado'
				);
			}

			$movihead->setFVence((string)$fechaVence);
			$movihead->setEstado('A');
			if ($movihead->save()==false) {
				foreach ($movihead->getMessages() as $message) 
				{
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
		catch(DateException $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function consultarAction() 
	{
		$this->setResponse('view');

		$codigoAlmacen = $this->getPostParam('almacen', 'int');
		Tag::displayTo('almacenConsulta', $codigoAlmacen);

		$fecha = new Date();
		$comprob = sprintf('O%02s', $codigoAlmacen);
		$ordenes = $this->Movihead->find(array("comprob='$comprob' AND estado='A'"));
		foreach ($ordenes as $orden) 
		{
			if (Date::isEarlier($orden->getFVence(), $fecha)) {
				$orden->setEstado('C');
				if ($orden->save()==false) {
					foreach ($orden->getMessages() as $message) 
					{
						Flash::error($message->getMessage());
						unset($message);
					}
				}
			}
			unset($orden);
		}

		$ordenes = $this->Movihead->find(array("comprob='$comprob' AND estado='A'"));
		$this->setParamToView('ordenes', $ordenes);

		$this->setParamToView('almacenes', $this->Almacenes->find("estado='A'"));
		$this->setParamToView('estados', array(
			'A' => 'ABIERTA',
			'C' => 'CERRADA'
		));
	}

	public function buscarAction() 
	{
		$this->setResponse('view');

		$codigoAlmacen = $this->getPostParam('almacenConsulta', 'int');
		$estado = $this->getPostParam('estadoOrden', 'onechar');

		$comprob = sprintf('O%02s', $codigoAlmacen);
		$pedidos = $this->Movihead->find(array("comprob='$comprob' AND estado='$estado'", "columns" => "almacen,nit,numero,fecha,estado"));
		$this->setParamToView('ordenes', $pedidos);

		View::renderPartial('resultados');
	}

	public function doReportAction() 
	{
		$this->setResponse('json');
		try 
		{
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
		catch(TaticoException $e) {
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

			$comprob = sprintf('O%02s', $almacen);
			$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
			if ($movihead==false) {
				return array(
					'status' => 'FAILED',
					'message' => 'No existe la orden de compra'
				);
			}
			//Tercero
			$tercero = $movihead->getNits();
			if (!$tercero) {
				throw new Exception("El tercero de la orden de compra no existe", 1);
			}
			$nombre = $tercero->getNombre();
			$email = $tercero->getEmail();
			if (!$email) {
				throw new Exception("No se ha definido el email del tercero '{$tercero->getNit()} / $nombre'", 1);
			}
			//File Name
			$fileUri = Tatico::getPrintUrl($reportType, $movihead->getComprob(), $almacen, $numero);
			//Enviamos correo
			$extra = array($fileUri => KEF_ABS_PATH . "public/" . $fileUri);
			if (!HfosDelivery::sendInvoice($email, $comprob, $comprob, $nombre, $extra, 'ordenCompra')) {
				$error = HfosDelivery::getLastError();
				throw new Exception($error, 1);
			}	
			return array(
				'status' => 'OK',
				'message' => 'Se ha enviado el correo al proveedor'
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
