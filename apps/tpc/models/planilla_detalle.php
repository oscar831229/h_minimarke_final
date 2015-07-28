<?php

class PlanillaDetalle extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $planilla_id;

	/**
	 * @var integer
	 */
	protected $recibos_pagos_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo planilla_id
	 * @param integer $planilla_id
	 */
	public function setPlanillaId($planilla_id){
		$this->planilla_id = $planilla_id;
	}

	/**
	 * Metodo para establecer el valor del campo recibos_pagos_id
	 * @param integer $recibos_pagos_id
	 */
	public function setRecibosPagosId($recibos_pagos_id){
		$this->recibos_pagos_id = $recibos_pagos_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo planilla_id
	 * @return integer
	 */
	public function getPlanillaId(){
		return $this->planilla_id;
	}

	/**
	 * Devuelve el valor del campo recibos_pagos_id
	 * @return integer
	 */
	public function getRecibosPagosId(){
		return $this->recibos_pagos_id;
	}

}

