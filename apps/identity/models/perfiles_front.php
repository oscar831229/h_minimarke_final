<?php

class PerfilesFront extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codprf;

	/**
	 * @var string
	 */
	protected $detalle;


	/**
	 * Metodo para establecer el valor del campo codprf
	 * @param integer $codprf
	 */
	public function setCodprf($codprf){
		$this->codprf = $codprf;
	}

	/**
	 * Metodo para establecer el valor del campo detalle
	 * @param string $detalle
	 */
	public function setDetalle($detalle){
		$this->detalle = $detalle;
	}


	/**
	 * Devuelve el valor del campo codprf
	 * @return integer
	 */
	public function getCodprf(){
		return $this->codprf;
	}

	/**
	 * Devuelve el valor del campo detalle
	 * @return string
	 */
	public function getDetalle(){
		return $this->detalle;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$identity = CoreConfig::getAppSetting('front_db', 'hfos');
		if($identity){
			$this->setSchema($identity);
		} else {
			$this->setSchema('hotel2');
		}
		$this->setSource('perfil');
	}

}

