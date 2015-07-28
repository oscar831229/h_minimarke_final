<?php

class ImportarPagos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var integer
	 */
	protected $usuarios_id;

	/**
	 * @var string
	 */
	protected $date_create;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}

	/**
	 * Metodo para establecer el valor del campo date_create
	 * @param string $date_create
	 */
	public function setDateCreate($date_create){
		$this->date_create = $date_create;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		if($this->fecha){
			return new Date($this->fecha);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

	/**
	 * Devuelve el valor del campo date_create
	 * @return string
	 */
	public function getDateCreate(){
		return $this->date_create;
	}

	public function initialize()
	{
		$this->belongsTo("socios_id", "Socios", "socios_id");
		$this->belongsTo("usuarios_id", "Usuarios", "id");
	}

}

