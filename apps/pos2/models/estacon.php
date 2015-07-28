<?php

class Estacon extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $front;

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var integer
	 */
	protected $codsal;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $servicio;

	/**
	 * @var string
	 */
	protected $aloja;


	/**
	 * Metodo para establecer el valor del campo front
	 * @param string $front
	 */
	public function setFront($front){
		$this->front = $front;
	}

	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar){
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo codsal
	 * @param integer $codsal
	 */
	public function setCodsal($codsal){
		$this->codsal = $codsal;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo servicio
	 * @param string $servicio
	 */
	public function setServicio($servicio){
		$this->servicio = $servicio;
	}

	/**
	 * Metodo para establecer el valor del campo aloja
	 * @param string $aloja
	 */
	public function setAloja($aloja){
		$this->aloja = $aloja;
	}


	/**
	 * Devuelve el valor del campo front
	 * @return string
	 */
	public function getFront(){
		return $this->front;
	}

	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar(){
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo codsal
	 * @return integer
	 */
	public function getCodsal(){
		return $this->codsal;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return string
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo servicio
	 * @return string
	 */
	public function getServicio(){
		return $this->servicio;
	}

	/**
	 * Devuelve el valor del campo aloja
	 * @return string
	 */
	public function getAloja(){
		return $this->aloja;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if(isset($config->pos->hotel)){
			$this->setSchema($config->pos->hotel);
		} else {
			$this->setSchema('hotel2');
		}
	}

}

