<?php

class Retencion extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $limite_sup;

	/**
	 * @var string
	 */
	protected $valor_ret;

	/**
	 * @var string
	 */
	protected $porc_ret;


	/**
	 * Metodo para establecer el valor del campo limite_sup
	 * @param string $limite_sup
	 */
	public function setLimiteSup($limite_sup){
		$this->limite_sup = $limite_sup;
	}

	/**
	 * Metodo para establecer el valor del campo valor_ret
	 * @param string $valor_ret
	 */
	public function setValorRet($valor_ret){
		$this->valor_ret = $valor_ret;
	}

	/**
	 * Metodo para establecer el valor del campo porc_ret
	 * @param string $porc_ret
	 */
	public function setPorcRet($porc_ret){
		$this->porc_ret = $porc_ret;
	}


	/**
	 * Devuelve el valor del campo limite_sup
	 * @return string
	 */
	public function getLimiteSup(){
		return $this->limite_sup;
	}

	/**
	 * Devuelve el valor del campo valor_ret
	 * @return string
	 */
	public function getValorRet(){
		return $this->valor_ret;
	}

	/**
	 * Devuelve el valor del campo porc_ret
	 * @return string
	 */
	public function getPorcRet(){
		return $this->porc_ret;
	}

}

