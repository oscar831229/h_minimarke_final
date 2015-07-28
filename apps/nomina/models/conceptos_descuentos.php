<?php

class ConceptosDescuentos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	protected function beforeSave(){
		if($this->estado=='A'){
			if($this->cuenta!=''){
				$cuenta = BackCacher::getCuenta($this->cuenta);
				if($cuenta==false){
					$this->appendMessage(new ActiveRecordMessage('La cuenta contable no existe ó no es auxiliar', 'cuenta'));
					return false;
				} else {
					if($cuenta->getEsAuxiliar()!='S'){
						$this->appendMessage(new ActiveRecordMessage('La cuenta contable no es auxiliar', 'cuenta'));
						return false;
					}
				}
			}
		}
		return true;
	}

}

