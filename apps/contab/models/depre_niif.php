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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */


class DepreNiif extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $periodo;

	/**
	 * @var integer
	 */
	protected $usuario_id;

	/**
	 * @return string
	 */
	public function getComprob()
	{
		return $this->comprob;
	}

	/**
	 * @param string $comprob
	 */
	public function setComprob($comprob)
	{
		$this->comprob = $comprob;
	}

	/**
	 * @return int
	 */
	public function getNumero()
	{
		return $this->numero;
	}

	/**
	 * @param int $numero
	 */
	public function setNumero($numero)
	{
		$this->numero = $numero;
	}

	/**
	 * @return int
	 */
	public function getPeriodo()
	{
		return $this->periodo;
	}

	/**
	 * @param int $time
	 */
	public function setPeriodo($periodo)
	{
		$this->periodo = $periodo;
	}

	/**
	 * @return int
	 */
	public function getUsuarioId()
	{
		return $this->usuario_id;
	}

	/**
	 * @param int $usuario_id
	 */
	public function setUsuarioId($usuario_id)
	{
		$this->usuario_id = $usuario_id;
	}
}
