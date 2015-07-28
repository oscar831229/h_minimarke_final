<?php

class Usuarios extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $sucursal_id;

	/**
	 * @var string
	 */
	protected $login;

	/**
	 * @var string
	 */
	protected $clave;

	/**
	 * @var string
	 */
	protected $clave_corta;

	/**
	 * @var string
	 */
	protected $fingerprint;

	/**
	 * @var string
	 */
	protected $apellidos;

	/**
	 * @var string
	 */
	protected $nombres;

	/**
	 * @var string
	 */
	protected $nombre_completo;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $genero;

	/**
	 * @var integer
	 */
	protected $usuarios_front_id;

	/**
	 * @var integer
	 */
	protected $usuarios_pos_id;

	/**
	 * @var Date
	 */
	protected $clave_fecha;

	/**
	 * @var string
	 */
	protected $creado_at;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo sucursal_id
	 * @param integer $sucursal_id
	 */
	public function setSucursalId($sucursal_id){
		$this->sucursal_id = $sucursal_id;
	}

	/**
	 * Metodo para establecer el valor del campo login
	 * @param string $login
	 */
	public function setLogin($login){
		$this->login = $login;
	}

	/**
	 * Metodo para establecer el valor del campo clave
	 * @param string $clave
	 */
	public function setClave($clave){
		$this->clave = $clave;
	}

	/**
	 * Metodo para establecer el valor del campo clave_corta
	 * @param string $clave_corta
	 */
	public function setClaveCorta($clave_corta){
		$this->clave_corta = $clave_corta;
	}

	/**
	 * Metodo para establecer el valor del campo fingerprint
	 * @param string $fingerprint
	 */
	public function setFingerprint($fingerprint){
		$this->fingerprint = $fingerprint;
	}

	/**
	 * Metodo para establecer el valor del campo apellidos
	 * @param string $apellidos
	 */
	public function setApellidos($apellidos){
		$this->apellidos = $apellidos;
	}

	/**
	 * Metodo para establecer el valor del campo nombres
	 * @param string $nombres
	 */
	public function setNombres($nombres){
		$this->nombres = $nombres;
	}

	/**
	 * Metodo para establecer el valor del campo nombre_completo
	 * @param string $nombre_completo
	 */
	public function setNombreCompleto($nombre_completo){
		$this->nombre_completo = $nombre_completo;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo genero
	 * @param string $genero
	 */
	public function setGenero($genero){
		$this->genero = $genero;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_front_id
	 * @param integer $usuarios_front_id
	 */
	public function setUsuariosFrontId($usuarios_front_id){
		$this->usuarios_front_id = $usuarios_front_id;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_pos_id
	 * @param integer $usuarios_pos_id
	 */
	public function setUsuariosPosId($usuarios_pos_id){
		$this->usuarios_pos_id = $usuarios_pos_id;
	}

	/**
	 * Metodo para establecer el valor del campo clave_fecha
	 * @param Date $clave_fecha
	 */
	public function setClaveFecha($clave_fecha){
		$this->clave_fecha = $clave_fecha;
	}

	/**
	 * Metodo para establecer el valor del campo creado_at
	 * @param string $creado_at
	 */
	public function setCreadoAt($creado_at){
		$this->creado_at = $creado_at;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo sucursal_id
	 * @return integer
	 */
	public function getSucursalId(){
		return $this->sucursal_id;
	}

	/**
	 * Devuelve el valor del campo login
	 * @return string
	 */
	public function getLogin(){
		return $this->login;
	}

	/**
	 * Devuelve el valor del campo clave
	 * @return string
	 */
	public function getClave(){
		return $this->clave;
	}

	/**
	 * Devuelve el valor del campo clave_corta
	 * @return string
	 */
	public function getClaveCorta(){
		return $this->clave_corta;
	}

	/**
	 * Devuelve el valor del campo fingerprint
	 * @return string
	 */
	public function getFingerprint(){
		return $this->fingerprint;
	}

	/**
	 * Devuelve el valor del campo apellidos
	 * @return string
	 */
	public function getApellidos(){
		return $this->apellidos;
	}

	/**
	 * Devuelve el valor del campo nombres
	 * @return string
	 */
	public function getNombres(){
		return $this->nombres;
	}

	/**
	 * Devuelve el valor del campo nombre_completo
	 * @return string
	 */
	public function getNombreCompleto(){
		return $this->nombre_completo;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo genero
	 * @return string
	 */
	public function getGenero(){
		return $this->genero;
	}

	/**
	 * Devuelve el valor del campo usuarios_front_id
	 * @return integer
	 */
	public function getUsuariosFrontId(){
		return $this->usuarios_front_id;
	}

	/**
	 * Devuelve el valor del campo usuarios_pos_id
	 * @return integer
	 */
	public function getUsuariosPosId(){
		return $this->usuarios_pos_id;
	}

	/**
	 * Devuelve el valor del campo clave_fecha
	 * @return Date
	 */
	public function getClaveFecha(){
		if($this->clave_fecha){
			return new Date($this->clave_fecha);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo creado_at
	 * @return string
	 */
	public function getCreadoAt(){
		return $this->creado_at;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function beforeValidation(){
		$this->login = i18n::strtolower(preg_replace('/[^a-z0-9\.A-Z]/', '', $this->login));
		$this->nombre_completo = i18n::strtoupper($this->nombres.' '.$this->apellidos);
	}

	public function beforeValidationOnCreate(){
		$this->clave = '-';
	}

	public function beforeSave(){
		if(!$this->fingerprint){
			$this->fingerprint = strtoupper(hash('tiger192,3', '$HFOS/TIGER@FINGERPRINT-'.$this->login.$this->nombres.'+IDENTITY#'));
		}
	}

	public function afterCreate(){
		Rcs::disable();
		$Usuarios = EntityManager::get('Usuarios');
		$Usuarios->setConnection($this->getConnection());
		$usuario = $Usuarios->findFirst($this->id);
		if($usuario!=false){
			$usuario->setClave(hash('tiger160,3', $usuario->getId().$usuario->getLogin()));
			$usuario->save();
		}
		Rcs::enable();
		parent::afterCreate();
	}

	public function beforeCreate(){
		if(EntityManager::get('Usuarios')->count("login='{$this->login}'")){
			$this->appendMessage(new ActiveRecordMessage('Ya existe un usuario con el nombre de usuario indicado'));
			return false;
		}
		if(strlen($this->login)<8){
			$this->appendMessage(new ActiveRecordMessage('El nombre de usuario debe tener al menos 8 caracteres'));
			return false;
		}
	}

	public function beforeDelete(){
		if($this->countRevisions()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el usuario porque ha utilizado el sistema', 'login'));
			return false;
		}
	}

	public function initialize()
	{
		$identity = CoreConfig::getAppSetting('identity', 'hfos');
		if($identity){
			$this->setSchema($identity);
		} else {
			$this->setSchema('hfos_identity');
		}
		$this->addForeignKey('sucursal_id', 'Sucursal', 'id', array(
			'message' => 'La sucursal indicada no es válida'
		));
		$this->hasMany('id', 'Revisions', 'codusu');
	}

}

