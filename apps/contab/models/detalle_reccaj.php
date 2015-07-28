<?php

class DetalleReccaj extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $reccaj_id;

	/**
	 * @var integer
	 */
	protected $forma_pago_id;

	/**
	 * @var string
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $valor;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo reccaj_id
	 * @param integer $reccaj_id
	 */
	public function setReccajId($reccaj_id){
		$this->reccaj_id = $reccaj_id;
	}

	/**
	 * Metodo para establecer el valor del campo forma_pago_id
	 * @param integer $forma_pago_id
	 */
	public function setFormaPagoId($forma_pago_id){
		$this->forma_pago_id = $forma_pago_id;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param string $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo reccaj_id
	 * @return integer
	 */
	public function getReccajId(){
		return $this->reccaj_id;
	}

	/**
	 * Devuelve el valor del campo forma_pago_id
	 * @return integer
	 */
	public function getFormaPagoId(){
		return $this->forma_pago_id;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return string
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	public function initialize(){
		$this->belongsTo('reccaj_id','Reccaj','id');
		//$this->belongsTo('forma_pago_id','FormaPago','codigo');
		$this->addForeignKey('reccaj_id', 'Reccaj', 'id', array(
			'message' => 'El id recibo de caja no es válido'
		));
		/*$this->addForeignKey('forma_pago_id', 'FormaPago', 'codigo', array(
			'message' => 'El id forma de pago no es válido ('.$this->forma_pago_id.')'
		));*/
	}

}

