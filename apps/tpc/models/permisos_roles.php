<?php

class PermisosRoles extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $permiso_id;

	/**
	 * @var integer
	 */
	protected $roles_id;

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
	 * Metodo para establecer el valor del campo permiso_id
	 * @param integer $permiso_id
	 */
	public function setPermisoId($permiso_id){
		$this->permiso_id = $permiso_id;
	}

	/**
	 * Metodo para establecer el valor del campo roles_id
	 * @param integer $roles_id
	 */
	public function setRolesId($roles_id){
		$this->roles_id = $roles_id;
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
	 * Devuelve el valor del campo permiso_id
	 * @return integer
	 */
	public function getPermisoId(){
		return $this->permiso_id;
	}

	/**
	 * Devuelve el valor del campo roles_id
	 * @return integer
	 */
	public function getRolesId(){
		return $this->roles_id;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

