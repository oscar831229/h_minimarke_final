<?php

class DetalleCuota extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var string
	 */
	protected $hoy;

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
	 * @var boolean
	 */
	protected $validar;


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
	 * Metodo para establecer el valor del campo hoy
	 * @param string $hoy
	 */
	public function setHoy($hoy){
		$this->hoy = $hoy;
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
	 * Metodo para establecer si se valida o no
	 * @param string $validar
	 */
	public function setValidar($validar){
		$this->validar = $validar;
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
	 * Devuelve el valor del campo hoy
	 * @return string
	 */
	public function getHoy(){
		return $this->hoy;
	}

	/**
	 * Devuelve el valor del campo fecha1
	 * @return Date
	 */
	public function getFecha1(){
		if($this->fecha1){
			return new Date($this->fecha1);
		}else{
			return null;
		}
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
	 * Devuelve el valor del campo fecha2
	 * @return Date
	 */
	public function getFecha2(){
		if($this->fecha2){
			return new Date($this->fecha2);
		}else{
			return null;
		}
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
	 * Devuelve el valor del campo fecha3
	 * @return Date
	 */
	public function getFecha3(){
		if($this->fecha3){
			return new Date($this->fecha3);
		}else{
			return null;
		}
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
	 * Metodo para obtiene si se valida o no
	 * @param string $validar
	 */
	public function getValidar(){
		return $this->validar;
	}


	public function beforeCreate(){
		//Asignamos valores inciales al crear el registro
		if(empty($this->estado1)){
			$this->estado1 = 'D';
			$this->hoy_pagado = 0;
		}
		if(empty($this->estado2)){
			$this->cuota2_pagado = 0;
			$this->estado2 = 'D';
		}
		if(empty($this->estado3)){
			$this->cuota3_pagado = 0;
			$this->estado3 = 'D';
		}
	}
	
	public function beforeValidation(){
		$status = true;
		$socio = EntityManager::get('Socios');
		$socio->setConnection($this->getConnection());
		$socio->findFirst($this->socios_id);
		if($socio==false){
			$this->appendMessage(new ActiveRecordMessage('El contrato no existe', 'hoy'));
			return false;
		}
		//$this->appendMessage(new ActiveRecordMessage($socio->getFechaCompra().', '.$this->fecha1.', '.Date::compareDates($socio->getFechaCompra(),$this->fecha1)));return false;

		//Si no se le asigna validar=false entonces valida este bloque
		if($this->validar!=false){
			//validamos cuota1
			if($this->hoy > 0){
				if(!$this->fecha1 || ($this->fecha1 && Date::compareDates($socio->getFechaCompra(),$this->fecha1) == 1 )){
					$this->appendMessage(new ActiveRecordMessage('La fecha del campo "primera cuota" no es válida, debe ser mayor a la de fecha de compra '.$socio->getFechaCompra(), 'fecha1'));
					$status = false;
				}
			}else{
				$this->appendMessage(new ActiveRecordMessage('El campo "primera cuota" es obligatoria en cuotas iniciales', 'hoy'));
				$status = false;
			}
			//validamos cuota2
			if($this->cuota2 > 0){
				if(!$this->fecha2 || ($this->fecha2 && Date::compareDates($this->fecha1,$this->fecha2) == 1 )){
					$this->appendMessage(new ActiveRecordMessage('La fecha del campo "segunda cuota" no es válida, debe ser mayor a la de primera cuota '.$this->fecha1, 'fecha2'));
					$status = false;
				}
			}
			//validamos cuota3
			if($this->cuota3 > 0 ){
				if(!$this->fecha3 || ($this->fecha3 && Date::compareDates($this->fecha2,$this->fecha3) == 1 )){
					$this->appendMessage(new ActiveRecordMessage('La fecha del campo "tercera cuota" no es válida, debe ser mayor a la de segunda cuota '.$this->fecha2, 'fecha3'));
					$status = false;
				}
			}
			$membresiasSocios = EntityManager::get('MembresiasSocios');
			$membresiasSocios->setConnection($this->getConnection());
			$membresiasSocios->findFirst(array('conditions'=>'socios_id='.$this->socios_id));
			if($membresiasSocios == false){
				$this->appendMessage(new ActiveRecordMessage('No existe membresia de contrato para validar cuota inicial', 'hoy'));
				$status = false;
			}
			$membresiaCuotaInicial = $membresiasSocios->getCuotaInicial();
			$totalCuotas = $this->hoy + $this->cuota2 + $this->cuota3;
			if($membresiaCuotaInicial > $totalCuotas){
				$this->appendMessage(new ActiveRecordMessage('La cuota inicial debe ser '.Currency::number($membresiaCuotaInicial), 'hoy'));
				$status = false;
			}
		}
		return $status;
	}

	public function initialize(){
		$this->addForeignKey('socios_id','Socios','id');
		$this->belongsTo('Socios');
	}

}

