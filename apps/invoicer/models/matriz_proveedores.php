<?php

class MatrizProveedores extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $item;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var integer
	 */
	protected $preferencia;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo item
	 * @param string $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo preferencia
	 * @param integer $preferencia
	 */
	public function setPreferencia($preferencia){
		$this->preferencia = $preferencia;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo item
	 * @return string
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo preferencia
	 * @return integer
	 */
	public function getPreferencia(){
		return $this->preferencia;
	}
  
  public function initialize(){
    $this->addForeignKey('nit', 'Nits', 'nit', array(
      'message' => 'El tercero indicado no es válido'
    ));    
    $this->addForeignKey('item', 'Inve', 'item', array(
      'message' => 'La referencia indicada no es válida'
    ));
  }

}

