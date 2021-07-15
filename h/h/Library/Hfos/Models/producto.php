<?php

class Producto extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_producto;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_producto
	 * @param string $nom_producto
	 */
	public function setNomProducto($nom_producto){
		$this->nom_producto = $nom_producto;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_producto
	 * @return string
	 */
	public function getNomProducto(){
		return $this->nom_producto;
	}

}

