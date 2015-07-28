<?php

class MembresiasSociosh extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $membresias_id;

	/**
	 * @var integer
	 */
	protected $temporadas_id;

	/**
	 * @var integer
	 */
	protected $capacidad;

	/**
	 * @var integer
	 */
	protected $puntos_ano;

	/**
	 * @var integer
	 */
	protected $numero_anos;

	/**
	 * @var integer
	 */
	protected $total_puntos;

	/**
	 * @var string
	 */
	protected $valor_total;

	/**
	 * @var string
	 */
	protected $cuota_inicial;

	/**
	 * @var string
	 */
	protected $saldo_pagar;

	/**
	 * @var integer
	 */
	protected $derecho_afiliacion_id;

	/**
	 * @var string
	 */
	protected $afiliacion_pagado;

	/**
	 * @var string
	 */
	protected $estado_cuoafi;

	/**
	 * @var integer
	 */
	protected $nota_historia_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo membresias_id
	 * @param integer $membresias_id
	 */
	public function setMembresiasId($membresias_id){
		$this->membresias_id = $membresias_id;
	}

	/**
	 * Metodo para establecer el valor del campo temporadas_id
	 * @param integer $temporadas_id
	 */
	public function setTemporadasId($temporadas_id){
		$this->temporadas_id = $temporadas_id;
	}

	/**
	 * Metodo para establecer el valor del campo capacidad
	 * @param integer $capacidad
	 */
	public function setCapacidad($capacidad){
		$this->capacidad = $capacidad;
	}

	/**
	 * Metodo para establecer el valor del campo puntos_ano
	 * @param integer $puntos_ano
	 */
	public function setPuntosAno($puntos_ano){
		$this->puntos_ano = $puntos_ano;
	}

	/**
	 * Metodo para establecer el valor del campo numero_anos
	 * @param integer $numero_anos
	 */
	public function setNumeroAnos($numero_anos){
		$this->numero_anos = $numero_anos;
	}

	/**
	 * Metodo para establecer el valor del campo total_puntos
	 * @param integer $total_puntos
	 */
	public function setTotalPuntos($total_puntos){
		$this->total_puntos = $total_puntos;
	}

	/**
	 * Metodo para establecer el valor del campo valor_total
	 * @param string $valor_total
	 */
	public function setValorTotal($valor_total){
		$this->valor_total = $valor_total;
	}

	/**
	 * Metodo para establecer el valor del campo cuota_inicial
	 * @param string $cuota_inicial
	 */
	public function setCuotaInicial($cuota_inicial){
		$this->cuota_inicial = $cuota_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_pagar
	 * @param string $saldo_pagar
	 */
	public function setSaldoPagar($saldo_pagar){
		$this->saldo_pagar = $saldo_pagar;
	}

	/**
	 * Metodo para establecer el valor del campo derecho_afiliacion_id
	 * @param integer $derecho_afiliacion_id
	 */
	public function setDerechoAfiliacionId($derecho_afiliacion_id){
		$this->derecho_afiliacion_id = $derecho_afiliacion_id;
	}

	/**
	 * Metodo para establecer el valor del campo afiliacion_pagado
	 * @param string $afiliacion_pagado
	 */
	public function setAfiliacionPagado($afiliacion_pagado){
		$this->afiliacion_pagado = $afiliacion_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo estado_cuoafi
	 * @param string $estado_cuoafi
	 */
	public function setEstadoCuoafi($estado_cuoafi){
		$this->estado_cuoafi = $estado_cuoafi;
	}

	/**
	 * Metodo para establecer el valor del campo nota_historia_id
	 * @param integer $nota_historia_id
	 */
	public function setNotaHistoriaId($nota_historia_id){
		$this->nota_historia_id = $nota_historia_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo membresias_id
	 * @return integer
	 */
	public function getMembresiasId(){
		return $this->membresias_id;
	}

	/**
	 * Devuelve el valor del campo temporadas_id
	 * @return integer
	 */
	public function getTemporadasId(){
		return $this->temporadas_id;
	}

	/**
	 * Devuelve el valor del campo capacidad
	 * @return integer
	 */
	public function getCapacidad(){
		return $this->capacidad;
	}

	/**
	 * Devuelve el valor del campo puntos_ano
	 * @return integer
	 */
	public function getPuntosAno(){
		return $this->puntos_ano;
	}

	/**
	 * Devuelve el valor del campo numero_anos
	 * @return integer
	 */
	public function getNumeroAnos(){
		return $this->numero_anos;
	}

	/**
	 * Devuelve el valor del campo total_puntos
	 * @return integer
	 */
	public function getTotalPuntos(){
		return $this->total_puntos;
	}

	/**
	 * Devuelve el valor del campo valor_total
	 * @return string
	 */
	public function getValorTotal(){
		return $this->valor_total;
	}

	/**
	 * Devuelve el valor del campo cuota_inicial
	 * @return string
	 */
	public function getCuotaInicial(){
		return $this->cuota_inicial;
	}

	/**
	 * Devuelve el valor del campo saldo_pagar
	 * @return string
	 */
	public function getSaldoPagar(){
		return $this->saldo_pagar;
	}

	/**
	 * Devuelve el valor del campo derecho_afiliacion_id
	 * @return integer
	 */
	public function getDerechoAfiliacionId(){
		return $this->derecho_afiliacion_id;
	}

	/**
	 * Devuelve el valor del campo afiliacion_pagado
	 * @return string
	 */
	public function getAfiliacionPagado(){
		return $this->afiliacion_pagado;
	}

	/**
	 * Devuelve el valor del campo estado_cuoafi
	 * @return string
	 */
	public function getEstadoCuoafi(){
		return $this->estado_cuoafi;
	}

	/**
	 * Devuelve el valor del campo nota_historia_id
	 * @return integer
	 */
	public function getNotaHistoriaId(){
		return $this->nota_historia_id;
	}

}

