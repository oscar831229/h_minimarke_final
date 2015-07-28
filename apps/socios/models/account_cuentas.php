<?php

class AccountCuentas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $account_master_id;

	/**
	 * @var integer
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $clientes_cedula;

	/**
	 * @var string
	 */
	protected $clientes_nombre;

	/**
	 * @var integer
	 */
	protected $habitacion_id;

	/**
	 * @var string
	 */
	protected $prefijo;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $nota;

	/**
	 * @var string
	 */
	protected $tipo_venta;

	/**
	 * @var string
	 */
	protected $propina_fija;

	/**
	 * @var string
	 */
	protected $propina;

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
	 * Metodo para establecer el valor del campo account_master_id
	 * @param integer $account_master_id
	 */
	public function setAccountMasterId($account_master_id){
		$this->account_master_id = $account_master_id;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param integer $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo clientes_cedula
	 * @param string $clientes_cedula
	 */
	public function setClientesCedula($clientes_cedula){
		$this->clientes_cedula = $clientes_cedula;
	}

	/**
	 * Metodo para establecer el valor del campo clientes_nombre
	 * @param string $clientes_nombre
	 */
	public function setClientesNombre($clientes_nombre){
		$this->clientes_nombre = $clientes_nombre;
	}

	/**
	 * Metodo para establecer el valor del campo habitacion_id
	 * @param integer $habitacion_id
	 */
	public function setHabitacionId($habitacion_id){
		$this->habitacion_id = $habitacion_id;
	}

	/**
	 * Metodo para establecer el valor del campo prefijo
	 * @param string $prefijo
	 */
	public function setPrefijo($prefijo){
		$this->prefijo = $prefijo;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_venta
	 * @param string $tipo_venta
	 */
	public function setTipoVenta($tipo_venta){
		$this->tipo_venta = $tipo_venta;
	}

	/**
	 * Metodo para establecer el valor del campo propina_fija
	 * @param string $propina_fija
	 */
	public function setPropinaFija($propina_fija){
		$this->propina_fija = $propina_fija;
	}

	/**
	 * Metodo para establecer el valor del campo propina
	 * @param string $propina
	 */
	public function setPropina($propina){
		$this->propina = $propina;
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
	 * Devuelve el valor del campo account_master_id
	 * @return integer
	 */
	public function getAccountMasterId(){
		return $this->account_master_id;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return integer
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo clientes_cedula
	 * @return string
	 */
	public function getClientesCedula(){
		return $this->clientes_cedula;
	}

	/**
	 * Devuelve el valor del campo clientes_nombre
	 * @return string
	 */
	public function getClientesNombre(){
		return $this->clientes_nombre;
	}

	/**
	 * Devuelve el valor del campo habitacion_id
	 * @return integer
	 */
	public function getHabitacionId(){
		return $this->habitacion_id;
	}

	/**
	 * Devuelve el valor del campo prefijo
	 * @return string
	 */
	public function getPrefijo(){
		return $this->prefijo;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

	/**
	 * Devuelve el valor del campo tipo_venta
	 * @return string
	 */
	public function getTipoVenta(){
		return $this->tipo_venta;
	}

	/**
	 * Devuelve el valor del campo propina_fija
	 * @return string
	 */
	public function getPropinaFija(){
		return $this->propina_fija;
	}

	/**
	 * Devuelve el valor del campo propina
	 * @return string
	 */
	public function getPropina(){
		return $this->propina;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function initialize(){
	     $config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->pos_db)){
			$this->setSchema($config->hfos->pos_db);
		} else {
			$this->setSchema('pos');
		}
	}
}

