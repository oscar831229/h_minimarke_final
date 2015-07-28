<?php

class ListaPrecios extends RcsRecord {

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $contrato;

	/**
	 * @var string
	 */
	protected $referencia;

	/**
	 * @var string
	 */
	protected $precio_venta;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo contrato
	 * @param string $contrato
	 */
	public function setContrato($contrato){
		$this->contrato = $contrato;
	}

	/**
	 * Metodo para establecer el valor del campo referencia
	 * @param string $referencia
	 */
	public function setReferencia($referencia){
		$this->referencia = $referencia;
	}

	/**
	 * Metodo para establecer el valor del campo precio_venta
	 * @param string $precio_venta
	 */
	public function setPrecioVenta($precio_venta){
		$this->precio_venta = $precio_venta;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo contrato
	 * @return string
	 */
	public function getContrato(){
		return $this->contrato;
	}

	/**
	 * Devuelve el valor del campo referencia
	 * @return string
	 */
	public function getReferencia(){
		return $this->referencia;
	}

	/**
	 * Devuelve el valor del campo precio_venta
	 * @return string
	 */
	public function getPrecioVenta(){
		return $this->precio_venta;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	protected function initialize(){
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->invoicer)){
			$this->setSchema($config->hfos->invoicer);
		} else {
			$this->setSchema('invoicer');
		}
	}
}

