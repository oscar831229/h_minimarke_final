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
 * ReservasController
 *
 * Controlador de Estados Civiles
 *
 */
class ReservasController extends HyperFormController {

	public static $_config = array(
		'model'			=> 'Reservas',
		'plural'		=> 'Reservas',
		'single'		=> 'Reserva',
		'genre'			=> 'M',
		'tabName'		=> 'Reservas',
		'preferedOrder'	=> 'apellidos ASC',
		'icon'			=> 'hire-me.png',
		'ignoreButtons'	=> array('import'),
		'fields' => array(
			'id' => array(
				'single'	=> 'Código',
				'type'		=> 'text',
				'size'		=> 6,
				'maxlength' => 6,
				'primary'	=> true,
				'readOnly'	=> true,
				'auto'		=> true,
				'filters'	=> array('int')
			),
			'numero_contrato' => array(
				'single'	=> 'Numero Reserva',
				'type'		=> 'text',
				'size'		=> 20,
				'maxlength'	=> 20,
				'notNull'	=> true,
				'readOnly'	=> true,
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
				'conditionsOnCreate' => 'estado="A"',
				'detail'	=> 'nombre',
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
				//'notBrowse' => true,
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
				'single'	=> 'Correo Electronico',
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
			'estados_civiles_id' => array(
				'single'	=> 'Estado Civil',
				'type'		=> 'relation',
				'relation' 	=> 'EstadosCiviles',
				'fieldRelation' => 'id',
				'conditionsOnCreate' => 'estado="A"',
				'detail'	=> 'nombre',
				'maxlength'	=> 20,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('int')
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
				'single'	=> 'Enviar Correspondencia',
				'type'		=> 'closed-domain',
				'size'		=> 1,
				'maxlength' => 1,
				'values'	=> array(
					'S' => 'Si',
					'N' => 'No'
				),
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'notNull'	=> true,
				'filters'	=> array('onechar')
			),
			'tipo_socios_id' => array(
				'single'	=> 'Tipo de Socio',
				'type'		=> 'relation',
				'relation'	=> 'TipoSocios',
				'fieldRelation' => 'id',
				'detail'	=> 'nombre',
				'conditionsOnCreate' => 'estado="A"',
				'maxlength'	=> 20,
				'notNull'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('int')
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
				'relation'	=> 'EstadoReservas',
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
				'conditionsOnCreateOnCreate' => 'estado="A"',
				'notBrowse'	=> true,
				'readOnly'	=> true,
				'filters'	=> array('int')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	/**
	 * Metodo que carga cosas al crear una nueva reserva
	 */
	public function beforeNew(){
		$this->setPostParam('fecha_compra', date('Y-m-d'));
	}

	public function getDatosAction(){
		$this->setResponse('json');
		$reservaId = $this->getPostParam('codigo', 'int');
		if($reservaId>0){
			$reservas = $this->Reservas->findFirst($reservaId);
			if($reservas==false){
				return array(
					'status' => 'FAILED',
					'message' => 'La reserva no existe'
				);
			} else {
				if($reservas->getEstadoContrato()!='A'){
					return array(
						'status' => 'FAILED',
						'message' => 'La reserva ya fue activada o está anulada'
					);
				} else {
					$data = array();
					foreach($reservas->getAttributes() as $attribute){
						$data[$attribute] = $reservas->readAttribute($attribute);
					}
					return array(
						'status' => 'OK',
						'data' => $data
					);
				}
			}
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'La reserva no existe'
			);
		}
	}

	/**
	 * Solicita el formato de desitir una reserva
	 *
	 */
	public function getDesistirAction(){
		$this->setResponse('view');
		$controller = $this->getControllerName();
		Tag::displayTo('fecha',date('Y-m-d'));
		$reservaId = $this->getPostParam('reservaId', 'int');
		$tipoDesistimiento = $this->EstadoDesistimiento->find(array('order'=>'nombre ASC'));
		$motivoDesistimiento = $this->MotivoDesistimiento->find(array('conditions'=>'estado="A"', 'order'=>'nombre ASC'));
		$this->setParamToView('controller', $controller);
		$this->setParamToView('reservaId', $reservaId);
		$this->setParamToView('tipoDesistimiento', $tipoDesistimiento);
		$this->setParamToView('motivoDesistimiento', $motivoDesistimiento);
	}

	/**
	 * Genera desitimiento de una reserva
	 *
	 */
	public function desistirAction(){
		$this->setResponse('json');

		try{
			$transaction = TransactionManager::getUserTransaction();

			$reservaId = $this->getPostParam('reservaId','int');
			$estado = $this->getPostParam('estado','alpha');
			$fecha = $this->getPostParam('fecha','date');
			$motivoDesistimiento = $this->getPostParam('motivo_desistimiento','int');

			//Cambiamos numero de contrato a un contrato
			$reservasDesistimientos = new ReservasDesistimientos();
			$reservasDesistimientos->setTransaction($transaction);
			$reservasDesistimientos->setReservasId($reservaId);
			$reservasDesistimientos->setFecha($fecha);
			$reservasDesistimientos->setMotivoDesistimientoId($motivoDesistimiento);
			$reservasDesistimientos->setEstadoDesistimientoId($estado);
			if($reservasDesistimientos->save()==false){
				foreach ($reservasDesistimientos->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$reservas = $this->Reservas->findFirst($reservaId);
			if($reservas == false){
				$transaction->rollback('La reserva no existe');
			}
			$reservas->setEstadoContrato('AAA');//Anulado
			$reservas->setEstadoMovimiento($estado);
			if($reservas->save()==false){
				foreach ($reservas->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			//Commit
			$transaction->commit();
			//Return
			return array(
				'status' => 'OK',
				'message' => 'La reserva fue desistida'
			);
		}
		catch (TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}


}
