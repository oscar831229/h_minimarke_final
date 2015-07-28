<?php

class DetalleCuotaTpc extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_id;

	/**
	 * @var string
	 */
	protected $hoy;

	/**
	 * @var string
	 */
	protected $porcentaje1;

	/**
	 * @var Date
	 */
	protected $fecha1;

	/**
	 * @var string
	 */
	protected $estado1;

	/**
	 * @var string
	 */
	protected $hoy_pagado;

	/**
	 * @var string
	 */
	protected $cuota2;

	/**
	 * @var string
	 */
	protected $porcentaje2;

	/**
	 * @var Date
	 */
	protected $fecha2;

	/**
	 * @var string
	 */
	protected $estado2;

	/**
	 * @var string
	 */
	protected $cuota2_pagado;

	/**
	 * @var string
	 */
	protected $cuota3;

	/**
	 * @var string
	 */
	protected $porcentaje3;

	/**
	 * @var Date
	 */
	protected $fecha3;

	/**
	 * @var string
	 */
	protected $estado3;

	/**
	 * @var string
	 */
	protected $cuota3_pagado;

	/**
	 * @var string
	 */
	protected $cuota4;

	/**
	 * @var string
	 */
	protected $porcentaje4;

	/**
	 * @var Date
	 */
	protected $fecha4;

	/**
	 * @var string
	 */
	protected $cuota4_pagado;

	/**
	 * @var string
	 */
	protected $estado4;


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
	 * Metodo para establecer el valor del campo hoy
	 * @param string $hoy
	 */
	public function setHoy($hoy){
		$this->hoy = $hoy;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje1
	 * @param string $porcentaje1
	 */
	public function setPorcentaje1($porcentaje1){
		$this->porcentaje1 = $porcentaje1;
	}

	/**
	 * Metodo para establecer el valor del campo fecha1
	 * @param Date $fecha1
	 */
	public function setFecha1($fecha1){
		$this->fecha1 = $fecha1;
	}

	/**
	 * Metodo para establecer el valor del campo estado1
	 * @param string $estado1
	 */
	public function setEstado1($estado1){
		$this->estado1 = $estado1;
	}

	/**
	 * Metodo para establecer el valor del campo hoy_pagado
	 * @param string $hoy_pagado
	 */
	public function setHoyPagado($hoy_pagado){
		$this->hoy_pagado = $hoy_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo cuota2
	 * @param string $cuota2
	 */
	public function setCuota2($cuota2){
		$this->cuota2 = $cuota2;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje2
	 * @param string $porcentaje2
	 */
	public function setPorcentaje2($porcentaje2){
		$this->porcentaje2 = $porcentaje2;
	}

	/**
	 * Metodo para establecer el valor del campo fecha2
	 * @param Date $fecha2
	 */
	public function setFecha2($fecha2){
		$this->fecha2 = $fecha2;
	}

	/**
	 * Metodo para establecer el valor del campo estado2
	 * @param string $estado2
	 */
	public function setEstado2($estado2){
		$this->estado2 = $estado2;
	}

	/**
	 * Metodo para establecer el valor del campo cuota2_pagado
	 * @param string $cuota2_pagado
	 */
	public function setCuota2Pagado($cuota2_pagado){
		$this->cuota2_pagado = $cuota2_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo cuota3
	 * @param string $cuota3
	 */
	public function setCuota3($cuota3){
		$this->cuota3 = $cuota3;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje3
	 * @param string $porcentaje3
	 */
	public function setPorcentaje3($porcentaje3){
		$this->porcentaje3 = $porcentaje3;
	}

	/**
	 * Metodo para establecer el valor del campo fecha3
	 * @param Date $fecha3
	 */
	public function setFecha3($fecha3){
		$this->fecha3 = $fecha3;
	}

	/**
	 * Metodo para establecer el valor del campo estado3
	 * @param string $estado3
	 */
	public function setEstado3($estado3){
		$this->estado3 = $estado3;
	}

	/**
	 * Metodo para establecer el valor del campo cuota3_pagado
	 * @param string $cuota3_pagado
	 */
	public function setCuota3Pagado($cuota3_pagado){
		$this->cuota3_pagado = $cuota3_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo cuota4
	 * @param string $cuota4
	 */
	public function setCuota4($cuota4){
		$this->cuota4 = $cuota4;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje4
	 * @param string $porcentaje4
	 */
	public function setPorcentaje4($porcentaje4){
		$this->porcentaje4 = $porcentaje4;
	}

	/**
	 * Metodo para establecer el valor del campo fecha4
	 * @param Date $fecha4
	 */
	public function setFecha4($fecha4){
		$this->fecha4 = $fecha4;
	}

	/**
	 * Metodo para establecer el valor del campo cuota4_pagado
	 * @param string $cuota4_pagado
	 */
	public function setCuota4Pagado($cuota4_pagado){
		$this->cuota4_pagado = $cuota4_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo estado4
	 * @param string $estado4
	 */
	public function setEstado4($estado4){
		$this->estado4 = $estado4;
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
	 * Devuelve el valor del campo hoy
	 * @return string
	 */
	public function getHoy(){
		return $this->hoy;
	}

	/**
	 * Devuelve el valor del campo porcentaje1
	 * @return string
	 */
	public function getPorcentaje1(){
		return $this->porcentaje1;
	}

	/**
	 * Devuelve el valor del campo fecha1
	 * @return Date
	 */
	public function getFecha1(){
		return new Date($this->fecha1);
	}

	/**
	 * Devuelve el valor del campo estado1
	 * @return string
	 */
	public function getEstado1(){
		return $this->estado1;
	}

	/**
	 * Devuelve el valor del campo hoy_pagado
	 * @return string
	 */
	public function getHoyPagado(){
		return $this->hoy_pagado;
	}

	/**
	 * Devuelve el valor del campo cuota2
	 * @return string
	 */
	public function getCuota2(){
		return $this->cuota2;
	}

	/**
	 * Devuelve el valor del campo porcentaje2
	 * @return string
	 */
	public function getPorcentaje2(){
		return $this->porcentaje2;
	}

	/**
	 * Devuelve el valor del campo fecha2
	 * @return Date
	 */
	public function getFecha2(){
		return new Date($this->fecha2);
	}

	/**
	 * Devuelve el valor del campo estado2
	 * @return string
	 */
	public function getEstado2(){
		return $this->estado2;
	}

	/**
	 * Devuelve el valor del campo cuota2_pagado
	 * @return string
	 */
	public function getCuota2Pagado(){
		return $this->cuota2_pagado;
	}

	/**
	 * Devuelve el valor del campo cuota3
	 * @return string
	 */
	public function getCuota3(){
		return $this->cuota3;
	}

	/**
	 * Devuelve el valor del campo porcentaje3
	 * @return string
	 */
	public function getPorcentaje3(){
		return $this->porcentaje3;
	}

	/**
	 * Devuelve el valor del campo fecha3
	 * @return Date
	 */
	public function getFecha3(){
		return new Date($this->fecha3);
	}

	/**
	 * Devuelve el valor del campo estado3
	 * @return string
	 */
	public function getEstado3(){
		return $this->estado3;
	}

	/**
	 * Devuelve el valor del campo cuota3_pagado
	 * @return string
	 */
	public function getCuota3Pagado(){
		return $this->cuota3_pagado;
	}

	/**
	 * Devuelve el valor del campo cuota4
	 * @return string
	 */
	public function getCuota4(){
		return $this->cuota4;
	}

	/**
	 * Devuelve el valor del campo porcentaje4
	 * @return string
	 */
	public function getPorcentaje4(){
		return $this->porcentaje4;
	}

	/**
	 * Devuelve el valor del campo fecha4
	 * @return Date
	 */
	public function getFecha4(){
		return new Date($this->fecha4);
	}

	/**
	 * Devuelve el valor del campo cuota4_pagado
	 * @return string
	 */
	public function getCuota4Pagado(){
		return $this->cuota4_pagado;
	}

	/**
	 * Devuelve el valor del campo estado4
	 * @return string
	 */
	public function getEstado4(){
		return $this->estado4;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema('sociostpc');
		$this->setSource('detalle_cuota');
				
		
		$this->belongsTo('socios_tpc');
	}

}

