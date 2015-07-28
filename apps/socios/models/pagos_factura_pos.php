<?php

class PagosFacturaPos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $prefijo_facturacion;

	/**
	 * @var integer
	 */
	protected $consecutivo_facturacion;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var integer
	 */
	protected $formas_pago_id;

	/**
	 * @var string
	 */
	protected $pago;

	/**
	 * @var string
	 */
	protected $cargo_plan;

	/**
	 * @var integer
	 */
	protected $habitacion_id;

	/**
	 * @var integer
	 */
	protected $cuenta;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo prefijo_facturacion
	 * @param string $prefijo_facturacion
	 */
	public function setPrefijoFacturacion($prefijo_facturacion){
		$this->prefijo_facturacion = $prefijo_facturacion;
	}

	/**
	 * Metodo para establecer el valor del campo consecutivo_facturacion
	 * @param integer $consecutivo_facturacion
	 */
	public function setConsecutivoFacturacion($consecutivo_facturacion){
		$this->consecutivo_facturacion = $consecutivo_facturacion;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo formas_pago_id
	 * @param integer $formas_pago_id
	 */
	public function setFormasPagoId($formas_pago_id){
		$this->formas_pago_id = $formas_pago_id;
	}

	/**
	 * Metodo para establecer el valor del campo pago
	 * @param string $pago
	 */
	public function setPago($pago){
		$this->pago = $pago;
	}

	/**
	 * Metodo para establecer el valor del campo cargo_plan
	 * @param string $cargo_plan
	 */
	public function setCargoPlan($cargo_plan){
		$this->cargo_plan = $cargo_plan;
	}

	/**
	 * Metodo para establecer el valor del campo habitacion_id
	 * @param integer $habitacion_id
	 */
	public function setHabitacionId($habitacion_id){
		$this->habitacion_id = $habitacion_id;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param integer $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo prefijo_facturacion
	 * @return string
	 */
	public function getPrefijoFacturacion(){
		return $this->prefijo_facturacion;
	}

	/**
	 * Devuelve el valor del campo consecutivo_facturacion
	 * @return integer
	 */
	public function getConsecutivoFacturacion(){
		return $this->consecutivo_facturacion;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo formas_pago_id
	 * @return integer
	 */
	public function getFormasPagoId(){
		return $this->formas_pago_id;
	}

	/**
	 * Devuelve el valor del campo pago
	 * @return string
	 */
	public function getPago(){
		return $this->pago;
	}

	/**
	 * Devuelve el valor del campo cargo_plan
	 * @return string
	 */
	public function getCargoPlan(){
		return $this->cargo_plan;
	}

	/**
	 * Devuelve el valor del campo habitacion_id
	 * @return integer
	 */
	public function getHabitacionId(){
		return $this->habitacion_id;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return integer
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	public function initialize()
	{
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->pos_db)){
			$this->setSchema($config->hfos->pos_db);
		} else {
			$this->setSchema('pos');
		}
		$this->setSource('pagos_factura');
	}

}

