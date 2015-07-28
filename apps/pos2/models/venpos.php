<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Venpos extends ActiveRecord
{

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $salon_id;

	/**
	 * @var string
	 */
	protected $prefac;

	/**
	 * @var integer
	 */
	protected $numfac;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var integer
	 */
	protected $menus_items_id;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $impo;

	/**
	 * @var string
	 */
	protected $valser;

	/**
	 * @var string
	 */
	protected $total;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo salon_id
	 * @param integer $salon_id
	 */
	public function setSalonId($salon_id)
	{
		$this->salon_id = $salon_id;
	}

	/**
	 * Metodo para establecer el valor del campo prefac
	 * @param string $prefac
	 */
	public function setPrefac($prefac)
	{
		$this->prefac = $prefac;
	}

	/**
	 * Metodo para establecer el valor del campo numfac
	 * @param integer $numfac
	 */
	public function setNumfac($numfac)
	{
		$this->numfac = $numfac;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha)
	{
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo cedula
	 * @param string $cedula
	 */
	public function setCedula($cedula)
	{
		$this->cedula = $cedula;
	}

	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar)
	{
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo menus_items_id
	 * @param integer $menus_items_id
	 */
	public function setMenusItemsId($menus_items_id)
	{
		$this->menus_items_id = $menus_items_id;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor)
	{
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $iva
	 */
	public function setIva($iva)
	{
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $impo
	 */
	public function setImpo($impo)
	{
		$this->impo = $impo;
	}

	/**
	 * Metodo para establecer el valor del campo valser
	 * @param string $valser
	 */
	public function setValser($valser)
	{
		$this->valser = $valser;
	}

	/**
	 * Metodo para establecer el valor del campo total
	 * @param string $total
	 */
	public function setTotal($total)
	{
		$this->total = $total;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo salon_id
	 * @return integer
	 */
	public function getSalonId(){
		return $this->salon_id;
	}

	/**
	 * Devuelve el valor del campo prefac
	 * @return string
	 */
	public function getPrefac()
	{
		return $this->prefac;
	}

	/**
	 * Devuelve el valor del campo numfac
	 * @return integer
	 */
	public function getNumfac()
	{
		return $this->numfac;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha()
	{
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo cedula
	 * @return string
	 */
	public function getCedula()
	{
		return $this->cedula;
	}

	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar()
	{
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo menus_items_id
	 * @return integer
	 */
	public function getMenusItemsId()
	{
		return $this->menus_items_id;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor()
	{
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return string
	 */
	public function getIva()
	{
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo valser
	 * @return string
	 */
	public function getValser()
	{
		return $this->valser;
	}

	/**
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize()
	{
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if (isset($config->pos->hotel)) {
			$this->setSchema($config->pos->hotel);
		} else {
			$this->setSchema('hotel2');
		}
	}

}

