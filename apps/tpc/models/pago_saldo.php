<?php

class PagoSaldo extends ActiveRecord {

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
	protected $numero_cuotas;

	/**
	 * @var decimal
	 */
	protected $interes;

	/**
	 * @var Date
	 */
	protected $fecha_primera_cuota;

	/**
	 * @var integer
	 */
	protected $premios_id;

	/**
	 * @var string
	 */
	protected $observaciones;

	/**
	 * @var boolean
	 */
	protected $validar;

	/**
	 * @var decimal
	 */
	protected $mora;


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
	 * Metodo para establecer el valor del campo numero_cuotas
	 * @param integer $numero_cuotas
	 */
	public function setNumeroCuotas($numero_cuotas){
		$this->numero_cuotas = $numero_cuotas;
	}

	/**
	 * Metodo para establecer el valor del campo interes
	 * @param string $interes
	 */
	public function setInteres($interes){
		$this->interes = $interes;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param decimal $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_primera_cuota
	 * @param Date $fecha_primera_cuota
	 */
	public function setFechaPrimeraCuota($fecha_primera_cuota){
		$this->fecha_primera_cuota = $fecha_primera_cuota;
	}

	/**
	 * Metodo para establecer el valor del campo premios_id
	 * @param integer $premios_id
	 */
	public function setPremiosId($premios_id){
		$this->premios_id = $premios_id;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
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
	 * Devuelve el valor del campo numero_cuotas
	 * @return integer
	 */
	public function getNumeroCuotas(){
		return $this->numero_cuotas;
	}

	/**
	 * Devuelve el valor del campo interes
	 * @return string
	 */
	public function getInteres(){
		return $this->interes;
	}

	/**
	 * Devuelve el valor del campo mora
	 * @param decimal $mora
	 */
	public function getMora(){
		return $this->mora;
	}


	/**
	 * Devuelve el valor del campo fecha_primera_cuota
	 * @return Date
	 */
	public function getFechaPrimeraCuota(){
		return $this->fecha_primera_cuota;
	}

	/**
	 * Devuelve el valor del campo premios_id
	 * @return integer
	 */
	public function getPremiosId(){
		return $this->premios_id;
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	/**
	 * Metodo para obtiene si se valida o no
	 * @param string $validar
	 */
	public function getValidar(){
		return $this->validar;
	}

	public function beforeValidation(){
		$status = true;

		if($this->validar!=false){
		
			$saldoPagar = EntityManager::get('MembresiasSocios');
			$saldoPagar->setConnection($this->getConnection());
			$saldoPagar->findFirst('socios_id='.$this->socios_id);

			if($saldoPagar->getSaldoPagar() > 0 && $this->numero_cuotas <= 0){
				$this->appendMessage(new ActiveRecordMessage('El campo "número de cuotas" es obligatorio en pago de saldo ', 'numero_cuotas'));
				$status = false;
			}
			
			//Si existe numeros de cuotas y interes 0 validar sin pasar
			//el limite de de interes corriente valiod para no usar interes corriente

			$limiteMesesInteres = Settings::get('limite_meses_interes');
			if($this->numero_cuotas > 0 && $this->interes <= 0 && $limiteMesesInteres < $this->numero_cuotas){
				$this->appendMessage(new ActiveRecordMessage('El campo "interes corriente" es obligatorio en pago de saldo '."({$this->numero_cuotas} > 0 && {$this->interes} <= 0 && $limiteMesesInteres < {$this->numero_cuotas})", 'interes'));
				$status = false;
			}
			if(!$this->fecha_primera_cuota && $this->numero_cuotas > 0){
				$this->appendMessage(new ActiveRecordMessage('La fecha del campo "primer pago" es obligatorio en pago de saldo', 'fecha_primera_cuota'));
				$status = false;
			}
			$detalleCuota = EntityManager::get('DetalleCuota');
			$detalleCuota->setConnection($this->getConnection());
			$detalleCuotaObj = $detalleCuota->findFirst(array('conditions'=>'socios_id='.$this->socios_id));
			if($detalleCuotaObj==false){
				$this->appendMessage(new ActiveRecordMessage('El detalle de la cuota del contrato no existe'));
				return false;
			}
			$ultimaFecha = $detalleCuotaObj->getFecha1();
			if($detalleCuotaObj->getCuota2()>0){
				$ultimaFecha = $detalleCuotaObj->getFecha2();
			}
			if($detalleCuotaObj->getCuota3()>0){
				$ultimaFecha = $detalleCuotaObj->getFecha3();
			}
			$dateEarlier = TPC::dateGreaterThan($this->fecha_primera_cuota, $ultimaFecha);
			if($this->fecha_primera_cuota!='0000-00-00' && $ultimaFecha!='0000-00-00'){
				if($this->fecha_primera_cuota!=$ultimaFecha && $dateEarlier==false){
					$this->appendMessage(new ActiveRecordMessage('La fecha del campo "primera pago" debe ser mayor a la última fecha de las cuotas iniciales '.$ultimaFecha));
					$status = false;
				}
			}
		}

		if(!$this->numero_cuotas){
			$this->numero_cuotas = 0;
		}
		if(!$this->interes){
			$this->interes = '0.00';
		}
		if(!$this->mora){
			$this->mora = '0.00';
		}

		return $status;
	}

	public function initialize(){
		$this->addForeignKey('socios_id','Socios','id');
		$this->belongsTo('Socios');
	}

}

