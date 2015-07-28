<?php

class ControlMora extends RcsRecord {

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
	protected $control_pagos_id;

	/**
	 * @var string
	 */
	protected $mora;

	/**
	 * @var integer
	 */
	protected $recibos_pagos_id;

	/**
	 * @var string
	 */
	protected $aplico;


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
	 * Metodo para establecer el valor del campo control_pagos_id
	 * @param integer $control_pagos_id
	 */
	public function setControlPagosId($control_pagos_id){
		$this->control_pagos_id = $control_pagos_id;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param string $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
	}

	/**
	 * Metodo para establecer el valor del campo recibos_pagos_id
	 * @param integer $recibos_pagos_id
	 */
	public function setRecibosPagosId($recibos_pagos_id){
		$this->recibos_pagos_id = $recibos_pagos_id;
	}

	/**
	 * Metodo para establecer el valor del campo aplico
	 * @param string $aplico
	 */
	public function setAplico($aplico){
		$this->aplico = $aplico;
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
	 * Devuelve el valor del campo control_pagos_id
	 * @return integer
	 */
	public function getControlPagosId(){
		return $this->control_pagos_id;
	}

	/**
	 * Devuelve el valor del campo mora
	 * @return string
	 */
	public function getMora(){
		return $this->mora;
	}

	/**
	 * Devuelve el valor del campo recibos_pagos_id
	 * @return integer
	 */
	public function getRecibosPagosId(){
		return $this->recibos_pagos_id;
	}

	/**
	 * Devuelve el valor del campo aplico
	 * @return string
	 */
	public function getAplico(){
		return $this->aplico;
	}

}

