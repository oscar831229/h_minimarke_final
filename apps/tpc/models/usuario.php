<?php

class Usuario extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $login;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var integer
	 */
	protected $roles_id;

	/**
	 * @var string
	 */
	protected $correoe;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo login
	 * @param string $login
	 */
	public function setLogin($login){
		$this->login = $login;
	}

	/**
	 * Metodo para establecer el valor del campo password
	 * @param string $password
	 */
	public function setPassword($password){
		$this->password = $password;
	}

	/**
	 * Metodo para establecer el valor del campo roles_id
	 * @param integer $roles_id
	 */
	public function setRolesId($roles_id){
		$this->roles_id = $roles_id;
	}

	/**
	 * Metodo para establecer el valor del campo correoe
	 * @param string $correoe
	 */
	public function setCorreoe($correoe){
		$this->correoe = $correoe;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo login
	 * @return string
	 */
	public function getLogin(){
		return $this->login;
	}

	/**
	 * Devuelve el valor del campo password
	 * @return string
	 */
	public function getPassword(){
		return $this->password;
	}

	/**
	 * Devuelve el valor del campo roles_id
	 * @return integer
	 */
	public function getRolesId(){
		return $this->roles_id;
	}

	/**
	 * Devuelve el valor del campo correoe
	 * @return string
	 */
	public function getCorreoe(){
		return $this->correoe;
	}

}

