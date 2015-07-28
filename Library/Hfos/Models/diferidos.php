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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Diferidos extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var integer
	 */
	protected $grupo;

	/**
	 * @var integer
	 */
	protected $centro_costo;

	/**
	 * @var Date
	 */
	protected $fecha_compra;

	/**
	 * @var string
	 */
	protected $valor_compra;

	/**
	 * @var string
	 */
	protected $valor_iva;

	/**
	 * @var integer
	 */
	protected $numero_fac;

	/**
	 * @var integer
	 */
	protected $meses_a_dep;

	/**
	 * @var string
	 */
	protected $proveedor;

	/**
	 * @var integer
	 */
	protected $forma_pago;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo grupo
	 * @param integer $grupo
	 */
	public function setGrupo($grupo){
		$this->grupo = $grupo;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param integer $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_compra
	 * @param Date $fecha_compra
	 */
	public function setFechaCompra($fecha_compra){
		$this->fecha_compra = $fecha_compra;
	}

	/**
	 * Metodo para establecer el valor del campo valor_compra
	 * @param string $valor_compra
	 */
	public function setValorCompra($valor_compra){
		$this->valor_compra = $valor_compra;
	}

	/**
	 * Metodo para establecer el valor del campo valor_iva
	 * @param string $valor_iva
	 */
	public function setValorIva($valor_iva){
		$this->valor_iva = $valor_iva;
	}

	/**
	 * Metodo para establecer el valor del campo numero_fac
	 * @param integer $numero_fac
	 */
	public function setNumeroFac($numero_fac){
		$this->numero_fac = $numero_fac;
	}

	/**
	 * Metodo para establecer el valor del campo meses_a_dep
	 * @param integer $meses_a_dep
	 */
	public function setMesesADep($meses_a_dep){
		$this->meses_a_dep = $meses_a_dep;
	}

	/**
	 * Metodo para establecer el valor del campo proveedor
	 * @param string $proveedor
	 */
	public function setProveedor($proveedor){
		$this->proveedor = $proveedor;
	}

	/**
	 * Metodo para establecer el valor del campo forma_pago
	 * @param integer $forma_pago
	 */
	public function setFormaPago($forma_pago){
		$this->forma_pago = $forma_pago;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo grupo
	 * @return integer
	 */
	public function getGrupo(){
		return $this->grupo;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return integer
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo fecha_compra
	 * @return Date
	 */
	public function getFechaCompra(){
		return new Date($this->fecha_compra);
	}

	/**
	 * Devuelve el valor del campo valor_compra
	 * @return string
	 */
	public function getValorCompra(){
		return $this->valor_compra;
	}

	/**
	 * Devuelve el valor del campo valor_iva
	 * @return string
	 */
	public function getValorIva(){
		return $this->valor_iva;
	}

	/**
	 * Devuelve el valor del campo numero_fac
	 * @return integer
	 */
	public function getNumeroFac(){
		return $this->numero_fac;
	}

	/**
	 * Devuelve el valor del campo meses_a_dep
	 * @return integer
	 */
	public function getMesesADep(){
		return $this->meses_a_dep;
	}

	/**
	 * Devuelve el valor del campo proveedor
	 * @return string
	 */
	public function getProveedor(){
		return $this->proveedor;
	}

	/**
	 * Devuelve el valor del campo forma_pago
	 * @return integer
	 */
	public function getFormaPago(){
		return $this->forma_pago;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	public function initialize(){
		$this->addForeignKey('grupo', 'GruposDiferidos', 'linea', array(
			'message' => 'El grupo del activo diferido no es válido'
		));
		$this->addForeignKey('centro_costo', 'Centros', 'codigo', array(
			'message' => 'El centro de costo indicado no es válido'
		));
		$this->addForeignKey('proveedor', 'Nits', 'nit', array(
			'message' => 'El proveedor indicado no es un tercero válido'
		));
		$this->addForeignKey('forma_pago', 'FormaPago', 'codigo', array(
			'message' => 'La forma de pago indicada no es válida'
		));
	}

}

