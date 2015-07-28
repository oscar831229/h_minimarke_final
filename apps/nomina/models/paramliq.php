<?php

class Paramliq extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $seg;

	/**
	 * @var string
	 */
	protected $ret;

	/**
	 * @var string
	 */
	protected $aux;

	/**
	 * @var string
	 */
	protected $vac_x_ano;

	/**
	 * @var string
	 */
	protected $limite_extras;

	/**
	 * @var string
	 */
	protected $prima_sem;

	/**
	 * @var string
	 */
	protected $dias_n;

	/**
	 * @var string
	 */
	protected $comprob_nom;

	/**
	 * @var string
	 */
	protected $comprob_pro;

	/**
	 * @var string
	 */
	protected $egmfp;

	/**
	 * @var string
	 */
	protected $egmfe;

	/**
	 * @var string
	 */
	protected $ivmp;

	/**
	 * @var string
	 */
	protected $ivme;

	/**
	 * @var string
	 */
	protected $atep;

	/**
	 * @var string
	 */
	protected $minimo;


	/**
	 * Metodo para establecer el valor del campo seg
	 * @param string $seg
	 */
	public function setSeg($seg){
		$this->seg = $seg;
	}

	/**
	 * Metodo para establecer el valor del campo ret
	 * @param string $ret
	 */
	public function setRet($ret){
		$this->ret = $ret;
	}

	/**
	 * Metodo para establecer el valor del campo aux
	 * @param string $aux
	 */
	public function setAux($aux){
		$this->aux = $aux;
	}

	/**
	 * Metodo para establecer el valor del campo vac_x_ano
	 * @param string $vac_x_ano
	 */
	public function setVacXAno($vac_x_ano){
		$this->vac_x_ano = $vac_x_ano;
	}

	/**
	 * Metodo para establecer el valor del campo limite_extras
	 * @param string $limite_extras
	 */
	public function setLimiteExtras($limite_extras){
		$this->limite_extras = $limite_extras;
	}

	/**
	 * Metodo para establecer el valor del campo prima_sem
	 * @param string $prima_sem
	 */
	public function setPrimaSem($prima_sem){
		$this->prima_sem = $prima_sem;
	}

	/**
	 * Metodo para establecer el valor del campo dias_n
	 * @param string $dias_n
	 */
	public function setDiasN($dias_n){
		$this->dias_n = $dias_n;
	}

	/**
	 * Metodo para establecer el valor del campo comprob_nom
	 * @param string $comprob_nom
	 */
	public function setComprobNom($comprob_nom){
		$this->comprob_nom = $comprob_nom;
	}

	/**
	 * Metodo para establecer el valor del campo comprob_pro
	 * @param string $comprob_pro
	 */
	public function setComprobPro($comprob_pro){
		$this->comprob_pro = $comprob_pro;
	}

	/**
	 * Metodo para establecer el valor del campo egmfp
	 * @param string $egmfp
	 */
	public function setEgmfp($egmfp){
		$this->egmfp = $egmfp;
	}

	/**
	 * Metodo para establecer el valor del campo egmfe
	 * @param string $egmfe
	 */
	public function setEgmfe($egmfe){
		$this->egmfe = $egmfe;
	}

	/**
	 * Metodo para establecer el valor del campo ivmp
	 * @param string $ivmp
	 */
	public function setIvmp($ivmp){
		$this->ivmp = $ivmp;
	}

	/**
	 * Metodo para establecer el valor del campo ivme
	 * @param string $ivme
	 */
	public function setIvme($ivme){
		$this->ivme = $ivme;
	}

	/**
	 * Metodo para establecer el valor del campo atep
	 * @param string $atep
	 */
	public function setAtep($atep){
		$this->atep = $atep;
	}

	/**
	 * Metodo para establecer el valor del campo minimo
	 * @param string $minimo
	 */
	public function setMinimo($minimo){
		$this->minimo = $minimo;
	}


	/**
	 * Devuelve el valor del campo seg
	 * @return string
	 */
	public function getSeg(){
		return $this->seg;
	}

	/**
	 * Devuelve el valor del campo ret
	 * @return string
	 */
	public function getRet(){
		return $this->ret;
	}

	/**
	 * Devuelve el valor del campo aux
	 * @return string
	 */
	public function getAux(){
		return $this->aux;
	}

	/**
	 * Devuelve el valor del campo vac_x_ano
	 * @return string
	 */
	public function getVacXAno(){
		return $this->vac_x_ano;
	}

	/**
	 * Devuelve el valor del campo limite_extras
	 * @return string
	 */
	public function getLimiteExtras(){
		return $this->limite_extras;
	}

	/**
	 * Devuelve el valor del campo prima_sem
	 * @return string
	 */
	public function getPrimaSem(){
		return $this->prima_sem;
	}

	/**
	 * Devuelve el valor del campo dias_n
	 * @return string
	 */
	public function getDiasN(){
		return $this->dias_n;
	}

	/**
	 * Devuelve el valor del campo comprob_nom
	 * @return string
	 */
	public function getComprobNom(){
		return $this->comprob_nom;
	}

	/**
	 * Devuelve el valor del campo comprob_pro
	 * @return string
	 */
	public function getComprobPro(){
		return $this->comprob_pro;
	}

	/**
	 * Devuelve el valor del campo egmfp
	 * @return string
	 */
	public function getEgmfp(){
		return $this->egmfp;
	}

	/**
	 * Devuelve el valor del campo egmfe
	 * @return string
	 */
	public function getEgmfe(){
		return $this->egmfe;
	}

	/**
	 * Devuelve el valor del campo ivmp
	 * @return string
	 */
	public function getIvmp(){
		return $this->ivmp;
	}

	/**
	 * Devuelve el valor del campo ivme
	 * @return string
	 */
	public function getIvme(){
		return $this->ivme;
	}

	/**
	 * Devuelve el valor del campo atep
	 * @return string
	 */
	public function getAtep(){
		return $this->atep;
	}

	/**
	 * Devuelve el valor del campo minimo
	 * @return string
	 */
	public function getMinimo(){
		return $this->minimo;
	}

}

