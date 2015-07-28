<?php

class CargosFijos extends ActiveRecord {

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
	protected $valor;

	/**
	 * @var integer
	 */
	protected $cuenta_contable;

	/**
	 * @var string
	 */
	protected $naturaleza;

	/**
	 * @var string
	 */
	protected $cuenta_iva;

	/**
	 * @var string
	 */
	protected $cuenta_consolidar;

	/**
	 * @var string
	 */
	protected $centro_costos;

	/**
	 * @var string
	 */
	protected $centro_costos_iva;

	/**
	 * @var string
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $porcentaje_iva;

	/**
	 * @var string
	 */
	protected $mora;

	/**
	 * @var string
	 */
	protected $tipo_cargo;

	/**
	 * @var string
	 */
	protected $ingreso_tercero;

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
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_contable
	 * @param integer $cuenta_contable
	 */
	public function setCuentaContable($cuenta_contable){
		$this->cuenta_contable = $cuenta_contable;
	}

	/**
	 * Metodo para establecer el valor del campo naturaleza
	 * @param string $naturaleza
	 */
	public function setNaturaleza($naturaleza){
		$this->naturaleza = $naturaleza;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_iva
	 * @param string $cuenta_iva
	 */
	public function setCuentaIva($cuenta_iva){
		$this->cuenta_iva = $cuenta_iva;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_conslidar
	 * @param string $cuenta_consolidar
	 */
	public function setCuentaConsolidar($cuenta_consolidar){
		$this->cuenta_consolidar = $cuenta_consolidar;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costos
	 * @param string $centro_costos
	 */
	public function setCentroCostos($centro_costos){
		$this->centro_costos = $centro_costos;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costos_iva
	 * @param string $centro_costos_iva
	 */
	public function setCentroCostosIva($centro_costos_iva){
		$this->centro_costos_iva = $centro_costos_iva;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje_iva
	 * @param string $porcentaje_iva
	 */
	public function setPorcentajeIva($porcentaje_iva){
		$this->porcentaje_iva = $porcentaje_iva;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param string $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_cargo
	 * @param string $tipo_cargo
	 */
	public function setTipoCargo($tipo_cargo){
		$this->tipo_cargo = $tipo_cargo;
	}

	/**
	 * Metodo para establecer el valor del campo ingreso_tercero
	 * @param string $ingreso_tercero
	 */
	public function setIngresoTercero($ingreso_tercero){
		$this->ingreso_tercero = $ingreso_tercero;
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
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo cuenta_contable
	 * @return integer
	 */
	public function getCuentaContable(){
		return $this->cuenta_contable;
	}

	/**
	 * Devuelve el valor del campo naturaleza
	 * @return string
	 */
	public function getNaturaleza(){
		return $this->naturaleza;
	}

	/**
	 * Devuelve el valor del campo cuenta_iva
	 * @return string
	 */
	public function getCuentaIva(){
		return $this->cuenta_iva;
	}

	/**
	 * Devuelve el valor del campo cuenta_consolidar
	 * @return string
	 */
	public function getCuentaConsolidar(){
		return $this->cuenta_consolidar;
	}

	/**
	 * Devuelve el valor del campo centro_costos
	 * @return string
	 */
	public function getCentroCostos(){
		return $this->centro_costos;
	}

	/**
	 * Devuelve el valor del campo centro_costos_iva
	 * @return string
	 */
	public function getCentroCostosIva(){
		return $this->centro_costos_iva;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return string
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo porcentaje_iva
	 * @return string
	 */
	public function getPorcentajeIva(){
		return $this->porcentaje_iva;
	}

	/**
	 * Devuelve el valor del campo mora
	 * @return string
	 */
	public function getMora(){
		return $this->mora;
	}

	/**
	 * Devuelve el valor del campo tipo_cargo
	 * @return string
	 */
	public function getTipoCargo(){
		return $this->tipo_cargo;
	}

	/**
	 * Devuelve el valor del campo ingreso_tercero
	 * @return string
	 */
	public function getIngresoTercero(){
		return $this->ingreso_tercero;
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
		if(isset($config->hfos->socios)){
			$this->setSchema($config->hfos->socios);
		} else {
			$this->setSchema('hfos_socios');
		}
		$this->belongsTo('id','CargosSocios','cargos_fijos_id');
	}

}

