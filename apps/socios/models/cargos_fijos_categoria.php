<?php

class CargosFijosCategoria extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $tipo_socios_id;

	/**
	 * @var integer
	 */
	protected $carfijo1;

	/**
	 * @var integer
	 */
	protected $carfijo2;

	/**
	 * @var integer
	 */
	protected $carfijo3;

	/**
	 * @var integer
	 */
	protected $carfijo4;

	/**
	 * @var integer
	 */
	protected $carfijo5;

	/**
	 * @var integer
	 */
	protected $carfijo6;

	/**
	 * @var integer
	 */
	protected $carfijo7;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_socios_id
	 * @param integer $tipo_socios_id
	 */
	public function setTipoSociosId($tipo_socios_id){
		$this->tipo_socios_id = $tipo_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo carfijo1
	 * @param integer $carfijo1
	 */
	public function setCarfijo1($carfijo1){
		$this->carfijo1 = $carfijo1;
	}

	/**
	 * Metodo para establecer el valor del campo carfijo2
	 * @param integer $carfijo2
	 */
	public function setCarfijo2($carfijo2){
		$this->carfijo2 = $carfijo2;
	}

	/**
	 * Metodo para establecer el valor del campo carfijo3
	 * @param integer $carfijo3
	 */
	public function setCarfijo3($carfijo3){
		$this->carfijo3 = $carfijo3;
	}

	/**
	 * Metodo para establecer el valor del campo carfijo4
	 * @param integer $carfijo4
	 */
	public function setCarfijo4($carfijo4){
		$this->carfijo4 = $carfijo4;
	}

	/**
	 * Metodo para establecer el valor del campo carfijo5
	 * @param integer $carfijo5
	 */
	public function setCarfijo5($carfijo5){
		$this->carfijo5 = $carfijo5;
	}

	/**
	 * Metodo para establecer el valor del campo carfijo6
	 * @param integer $carfijo6
	 */
	public function setCarfijo6($carfijo6){
		$this->carfijo6 = $carfijo6;
	}

	/**
	 * Metodo para establecer el valor del campo carfijo7
	 * @param integer $carfijo7
	 */
	public function setCarfijo7($carfijo7){
		$this->carfijo7 = $carfijo7;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo tipo_socios_id
	 * @return integer
	 */
	public function getTipoSociosId(){
		return $this->tipo_socios_id;
	}

	/**
	 * Devuelve el valor del campo carfijo1
	 * @return integer
	 */
	public function getCarfijo1(){
		return $this->carfijo1;
	}

	/**
	 * Devuelve el valor del campo carfijo2
	 * @return integer
	 */
	public function getCarfijo2(){
		return $this->carfijo2;
	}

	/**
	 * Devuelve el valor del campo carfijo3
	 * @return integer
	 */
	public function getCarfijo3(){
		return $this->carfijo3;
	}

	/**
	 * Devuelve el valor del campo carfijo4
	 * @return integer
	 */
	public function getCarfijo4(){
		return $this->carfijo4;
	}

	/**
	 * Devuelve el valor del campo carfijo5
	 * @return integer
	 */
	public function getCarfijo5(){
		return $this->carfijo5;
	}

	/**
	 * Devuelve el valor del campo carfijo6
	 * @return integer
	 */
	public function getCarfijo6(){
		return $this->carfijo6;
	}

	/**
	 * Devuelve el valor del campo carfijo7
	 * @return integer
	 */
	public function getCarfijo7(){
		return $this->carfijo7;
	}

}

