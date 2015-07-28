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

class UsuariosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Usuarios',
		'plural' => 'usuarios',
		'single' => 'usuario',
		'genre' => 'M',
		'icon' => 'user.png',
		'preferedOrder' => 'apellidos',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'filters' => array('int')
			),
			'sucursal_id' => array(
				'single' => 'Sucursal',
				'type' => 'relation',
				'relation' => 'Sucursal',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'filters' => array('int')
			),
			'login' => array(
				'single' => 'Nombre de Usuario',
				'type' => 'text',
				'size' => 24,
				'maxlength' => 24,
				'filters' => array('striptags', 'extraspaces')
			),
			'apellidos' => array(
				'single' => 'Apellidos',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 50,
				'notDetails' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'nombres' => array(
				'single' => 'Nombres',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 50,
				'notDetails' => true,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'nombre_completo' => array(
				'single' => 'Nombre Completo',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 120,
				'readOnly' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'email' => array(
				'single' => 'E-Mail',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 70,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'genero' => array(
				'single' => 'Genero',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'M' => 'MASCULINO',
					'F' => 'FEMENINO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('alpha')
			),
			'usuarios_front_id' => array(
				'single' => 'Usuario Front',
				'type' => 'relation',
				'relation' => 'UsuariosFront',
				'fieldRelation' => 'codusu',
				'detail' => 'nombre',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'ACTIVO',
					'I' => 'INACTIVO'
				),
				'filters' => array('alpha')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}


	/**
	 *
	 * Action que consulta por id desde un campo autocomplete
	 */
	public function queryByNitAction(){
		$this->setResponse('json');
		$numeroNit = $this->getQueryParam('nit', 'alpha');
		$usuarios = $this->Usuarios->findFirst("id='$numeroNit'");
		if($usuarios==false){
			return array(
				'status' => 'FAILED',
				'message' => 'NO EXISTE EL USUARIO'
			);
		} else {
			return array(
				'status' => 'OK',
				'nombres' => $usuarios->getNombres()
			);
		}
	}

	/**
	 *
	 * Action que consulta por nombres apellidos desde un campos autocomplete
	 */
	public function queryByNameAction(){
		$this->setResponse('json');
		$response = array();
		$nombre = $this->getPostParam('nombre', 'extraspaces');
		if($nombre!=''){
			$usuarios = $this->Usuarios->find('nombres LIKE \''.$nombre.'%\'', 'order: nombres', 'limit: 13');
			foreach($usuarios as $usuario){
				$response[] = array(
					'value' => $usuario->getId(),
					'selectText' => $usuario->getNombres(),
					'optionText' => $usuario->getNombres()
				);
			}
		}
		return $response;
	}

	/**
	 *
	 * Metood que redirecciona a formulario de cambio de datos de cuenta actual logeada
	 */
	public function configAction(){

	}


	/**
	 *
	 * Metodo que guarda los cambios en la cuenta del uusario actual logueado
	 */
	public function cambioClaveAction(){

		$this->setResponse('json');

		$passwordActual = $this->getPostParam('password_actual');
		$passwordNuevo = $this->getPostParam('password_nuevo');
		$passwordNuevoConfirm = $this->getPostParam('password_nuevo_confirm');

		if(!$passwordActual){
			return array(
				'status'  => 'FAILED',
				'message' => 'No digitó la clave actual'
			);
		}

		if(!$passwordNuevo){
			return array(
				'status'  => 'FAILED',
				'message' => 'No digitó la clave nueva'
			);
		}

		//Si clave nueva es menor a 8 carácteres da error
		/*$caracteres = (int) i18n::strlen($passwordNuevo);
		if($caracteres<8){
			return array(
				'status'  => 'FAILED',
				'message' => 'La clave debe tener mínimo 8 caracteres'
			);
		}*/


		if(!$passwordNuevoConfirm){
			return array(
				'status'  => 'FAILED',
				'message' => 'No digitó la confirmación de la clave nueva'
			);
		}

		if($passwordNuevo!=$passwordNuevoConfirm){
			return array(
				'status'  => 'FAILED',
				'message' => 'La clave nueva y la confirmación no coinciden'
			);
		}

		if($passwordNuevo==$passwordActual){
			return array(
				'status'  => 'FAILED',
				'message' => 'La clave nueva debe ser diferente a la clave actual'
			);
		}

		$identity = IdentityManager::getActive();

		$usuario = $this->Usuarios->findFirst("id=".$identity["id"]);
		if($usuario==false){
			return array(
				'status'  => 'FAILED',
				'message' => 'La identidad publica no tiene password'
			);
		}

		$passwordActualCodified = hash('tiger160,3', $usuario->getId().$passwordActual);
		$passwordNuevaCodified = hash('tiger160,3', $usuario->getId().$passwordNuevo);

		//Verificamos si la clave actual es la correcta
		if($passwordActualCodified != $usuario->getClave()){
			return array(
				'status'  => 'FAILED',
				'message' => 'La clave actual no es correcta'
			);
		}

		//Se actualiza la nueva clave en la Base de datos con seguridad transaccional
		try {

			Rcs::disable();

			$transaction = TransactionManager::getUserTransaction();

			$usuario->setClave($passwordNuevaCodified);
			if($usuario->save()==false){
				foreach($usuario->getMessages() as $messages){
					$transaction->rollback($messages->getMessage());
				}
			}

			$transaction->commit();
			Gardien::createUserAcls();
		}
		catch(GardienException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		//Se realizó el cambio correctamente
		return array(
			'status'  => 'OK',
			'message' => 'Se cambió la contraseña correctamente'
		);

	}

}