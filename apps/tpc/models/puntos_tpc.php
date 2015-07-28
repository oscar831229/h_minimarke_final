<?php

class PuntosTpc extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_id;

	/**
	 * @var integer
	 */
	protected $numero_anos;

	/**
	 * @var integer
	 */
	protected $puntos_anuales;

	/**
	 * @var integer
	 */
	protected $total_puntos;

	/**
	 * @var string
	 */
	protected $valor_punto_venta;

	/**
	 * @var string
	 */
	protected $valor_total_contrato;

	/**
	 * @var string
	 */
	protected $rci;

	/**
	 * @var integer
	 */
	protected $hotel;

	/**
	 * @var string
	 */
	protected $valor_punto_activacion;

	/**
	 * @var string
	 */
	protected $valor_anual_activacion;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_tpc_id
	 * @param integer $socios_tpc_id
	 */
	public function setSociosTpcId($socios_tpc_id){
		$this->socios_tpc_id = $socios_tpc_id;
	}

	/**
	 * Metodo para establecer el valor del campo numero_anos
	 * @param integer $numero_anos
	 */
	public function setNumeroAnos($numero_anos){
		$this->numero_anos = $numero_anos;
	}

	/**
	 * Metodo para establecer el valor del campo puntos_anuales
	 * @param integer $puntos_anuales
	 */
	public function setPuntosAnuales($puntos_anuales){
		$this->puntos_anuales = $puntos_anuales;
	}

	/**
	 * Metodo para establecer el valor del campo total_puntos
	 * @param integer $total_puntos
	 */
	public function setTotalPuntos($total_puntos){
		$this->total_puntos = $total_puntos;
	}

	/**
	 * Metodo para establecer el valor del campo valor_punto_venta
	 * @param string $valor_punto_venta
	 */
	public function setValorPuntoVenta($valor_punto_venta){
		$this->valor_punto_venta = $valor_punto_venta;
	}

	/**
	 * Metodo para establecer el valor del campo valor_total_contrato
	 * @param string $valor_total_contrato
	 */
	public function setValorTotalContrato($valor_total_contrato){
		$this->valor_total_contrato = $valor_total_contrato;
	}

	/**
	 * Metodo para establecer el valor del campo rci
	 * @param string $rci
	 */
	public function setRci($rci){
		$this->rci = $rci;
	}

	/**
	 * Metodo para establecer el valor del campo hotel
	 * @param integer $hotel
	 */
	public function setHotel($hotel){
		$this->hotel = $hotel;
	}

	/**
	 * Metodo para establecer el valor del campo valor_punto_activacion
	 * @param string $valor_punto_activacion
	 */
	public function setValorPuntoActivacion($valor_punto_activacion){
		$this->valor_punto_activacion = $valor_punto_activacion;
	}

	/**
	 * Metodo para establecer el valor del campo valor_anual_activacion
	 * @param string $valor_anual_activacion
	 */
	public function setValorAnualActivacion($valor_anual_activacion){
		$this->valor_anual_activacion = $valor_anual_activacion;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_tpc_id
	 * @return integer
	 */
	public function getSociosTpcId(){
		return $this->socios_tpc_id;
	}

	/**
	 * Devuelve el valor del campo numero_anos
	 * @return integer
	 */
	public function getNumeroAnos(){
		return $this->numero_anos;
	}

	/**
	 * Devuelve el valor del campo puntos_anuales
	 * @return integer
	 */
	public function getPuntosAnuales(){
		return $this->puntos_anuales;
	}

	/**
	 * Devuelve el valor del campo total_puntos
	 * @return integer
	 */
	public function getTotalPuntos(){
		return $this->total_puntos;
	}

	/**
	 * Devuelve el valor del campo valor_punto_venta
	 * @return string
	 */
	public function getValorPuntoVenta(){
		return $this->valor_punto_venta;
	}

	/**
	 * Devuelve el valor del campo valor_total_contrato
	 * @return string
	 */
	public function getValorTotalContrato(){
		return $this->valor_total_contrato;
	}

	/**
	 * Devuelve el valor del campo rci
	 * @return string
	 */
	public function getRci(){
		return $this->rci;
	}

	/**
	 * Devuelve el valor del campo hotel
	 * @return integer
	 */
	public function getHotel(){
		return $this->hotel;
	}

	/**
	 * Devuelve el valor del campo valor_punto_activacion
	 * @return string
	 */
	public function getValorPuntoActivacion(){
		return $this->valor_punto_activacion;
	}

	/**
	 * Devuelve el valor del campo valor_anual_activacion
	 * @return string
	 */
	public function getValorAnualActivacion(){
		return $this->valor_anual_activacion;
	}

}

