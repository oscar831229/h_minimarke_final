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
 * @copyright 	BH-TECK Inc. 2009-2011
 * @version		$Id$
 */

class Facturas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $consecutivos_id;

	/**
	 * @var string
	 */
	protected $prefijo;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $resolucion;

	/**
	 * @var Date
	 */
	protected $fecha_resolucion;

	/**
	 * @var integer
	 */
	protected $numero_inicial;

	/**
	 * @var integer
	 */
	protected $numero_final;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $direccion;

	/**
	 * @var string
	 */
	protected $nit_entregar;

	/**
	 * @var string
	 */
	protected $nombre_entregar;

	/**
	 * @var string
	 */
	protected $direccion_entregar;

	/**
	 * @var Date
	 */
	protected $fecha_emision;

	/**
	 * @var Date
	 */
	protected $fecha_vencimiento;

	/**
	 * @var string
	 */
	protected $nota_factura;

	/**
	 * @var string
	 */
	protected $nota_ica;

	/**
	 * @var string
	 */
	protected $venta16;

	/**
	 * @var string
	 */
	protected $venta10;

	/**
	 * @var string
	 */
	protected $venta0;

	/**
	 * @var string
	 */
	protected $iva10;

	/**
	 * @var string
	 */
	protected $iva16;

	/**
	 * @var string
	 */
	protected $iva0;

	/**
	 * @var string
	 */
	protected $pagos;

	/**
	 * @var string
	 */
	protected $total;

	/**
	 * @var string
	 */
	protected $comprob_inve;

	/**
	 * @var integer
	 */
	protected $numero_inve;

	/**
	 * @var string
	 */
	protected $comprob_contab;

	/**
	 * @var integer
	 */
	protected $numero_contab;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo consecutivos_id
	 * @param integer $consecutivos_id
	 */
	public function setConsecutivosId($consecutivos_id){
		$this->consecutivos_id = $consecutivos_id;
	}

	/**
	 * Metodo para establecer el valor del campo prefijo
	 * @param string $prefijo
	 */
	public function setPrefijo($prefijo){
		$this->prefijo = $prefijo;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo resolucion
	 * @param string $resolucion
	 */
	public function setResolucion($resolucion){
		$this->resolucion = $resolucion;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_resolucion
	 * @param Date $fecha_resolucion
	 */
	public function setFechaResolucion($fecha_resolucion){
		$this->fecha_resolucion = $fecha_resolucion;
	}

	/**
	 * Metodo para establecer el valor del campo numero_inicial
	 * @param integer $numero_inicial
	 */
	public function setNumeroInicial($numero_inicial){
		$this->numero_inicial = $numero_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo numero_final
	 * @param integer $numero_final
	 */
	public function setNumeroFinal($numero_final){
		$this->numero_final = $numero_final;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo direccion
	 * @param string $direccion
	 */
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}

	/**
	 * Metodo para establecer el valor del campo nit_entregar
	 * @param string $nit_entregar
	 */
	public function setNitEntregar($nit_entregar){
		$this->nit_entregar = $nit_entregar;
	}

	/**
	 * Metodo para establecer el valor del campo nombre_entregar
	 * @param string $nombre_entregar
	 */
	public function setNombreEntregar($nombre_entregar){
		$this->nombre_entregar = $nombre_entregar;
	}

	/**
	 * Metodo para establecer el valor del campo direccion_entregar
	 * @param string $direccion_entregar
	 */
	public function setDireccionEntregar($direccion_entregar){
		$this->direccion_entregar = $direccion_entregar;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_emision
	 * @param Date $fecha_emision
	 */
	public function setFechaEmision($fecha_emision){
		$this->fecha_emision = $fecha_emision;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_vencimiento
	 * @param Date $fecha_vencimiento
	 */
	public function setFechaVencimiento($fecha_vencimiento){
		$this->fecha_vencimiento = $fecha_vencimiento;
	}

	/**
	 * Metodo para establecer el valor del campo nota_factura
	 * @param string $nota_factura
	 */
	public function setNotaFactura($nota_factura){
		$this->nota_factura = $nota_factura;
	}

	/**
	 * Metodo para establecer el valor del campo nota_ica
	 * @param string $nota_ica
	 */
	public function setNotaIca($nota_ica){
		$this->nota_ica = $nota_ica;
	}

	/**
	 * Metodo para establecer el valor del campo venta16
	 * @param string $venta16
	 */
	public function setVenta16($venta16){
		$this->venta16 = $venta16;
	}

	/**
	 * Metodo para establecer el valor del campo venta10
	 * @param string $venta10
	 */
	public function setVenta10($venta10){
		$this->venta10 = $venta10;
	}

	/**
	 * Metodo para establecer el valor del campo venta0
	 * @param string $venta0
	 */
	public function setVenta0($venta0){
		$this->venta0 = $venta0;
	}

	/**
	 * Metodo para establecer el valor del campo iva10
	 * @param string $iva10
	 */
	public function setIva10($iva10){
		$this->iva10 = $iva10;
	}

	/**
	 * Metodo para establecer el valor del campo iva16
	 * @param string $iva16
	 */
	public function setIva16($iva16){
		$this->iva16 = $iva16;
	}

	/**
	 * Metodo para establecer el valor del campo iva0
	 * @param string $iva0
	 */
	public function setIva0($iva0){
		$this->iva0 = $iva0;
	}

	/**
	 * Metodo para establecer el valor del campo pagos
	 * @param string $pagos
	 */
	public function setPagos($pagos){
		$this->pagos = $pagos;
	}

	/**
	 * Metodo para establecer el valor del campo total
	 * @param string $total
	 */
	public function setTotal($total){
		$this->total = $total;
	}

	/**
	 * Metodo para establecer el valor del campo comprob_inve
	 * @param string $comprob_inve
	 */
	public function setComprobInve($comprob_inve){
		$this->comprob_inve = $comprob_inve;
	}

	/**
	 * Metodo para establecer el valor del campo numero_inve
	 * @param integer $numero_inve
	 */
	public function setNumeroInve($numero_inve){
		$this->numero_inve = $numero_inve;
	}

	/**
	 * Metodo para establecer el valor del campo comprob_contab
	 * @param string $comprob_contab
	 */
	public function setComprobContab($comprob_contab){
		$this->comprob_contab = $comprob_contab;
	}

	/**
	 * Metodo para establecer el valor del campo numero_contab
	 * @param integer $numero_contab
	 */
	public function setNumeroContab($numero_contab){
		$this->numero_contab = $numero_contab;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo consecutivos_id
	 * @return integer
	 */
	public function getConsecutivosId(){
		return $this->consecutivos_id;
	}

	/**
	 * Devuelve el valor del campo prefijo
	 * @return string
	 */
	public function getPrefijo(){
		return $this->prefijo;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo resolucion
	 * @return string
	 */
	public function getResolucion(){
		return $this->resolucion;
	}

	/**
	 * Devuelve el valor del campo fecha_resolucion
	 * @return Date
	 */
	public function getFechaResolucion(){
		if($this->fecha_resolucion){
			return new Date($this->fecha_resolucion);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo numero_inicial
	 * @return integer
	 */
	public function getNumeroInicial(){
		return $this->numero_inicial;
	}

	/**
	 * Devuelve el valor del campo numero_final
	 * @return integer
	 */
	public function getNumeroFinal(){
		return $this->numero_final;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo direccion
	 * @return string
	 */
	public function getDireccion(){
		return $this->direccion;
	}

	/**
	 * Devuelve el valor del campo nit_entregar
	 * @return string
	 */
	public function getNitEntregar(){
		return $this->nit_entregar;
	}

	/**
	 * Devuelve el valor del campo nombre_entregar
	 * @return string
	 */
	public function getNombreEntregar(){
		return $this->nombre_entregar;
	}

	/**
	 * Devuelve el valor del campo direccion_entregar
	 * @return string
	 */
	public function getDireccionEntregar(){
		return $this->direccion_entregar;
	}

	/**
	 * Devuelve el valor del campo fecha_emision
	 * @return Date
	 */
	public function getFechaEmision(){
		if($this->fecha_emision){
			return new Date($this->fecha_emision);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_vencimiento
	 * @return Date
	 */
	public function getFechaVencimiento(){
		if($this->fecha_vencimiento){
			return new Date($this->fecha_vencimiento);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo nota_factura
	 * @return string
	 */
	public function getNotaFactura(){
		return $this->nota_factura;
	}

	/**
	 * Devuelve el valor del campo nota_ica
	 * @return string
	 */
	public function getNotaIca(){
		return $this->nota_ica;
	}

	/**
	 * Devuelve el valor del campo venta16
	 * @return string
	 */
	public function getVenta16(){
		return $this->venta16;
	}

	/**
	 * Devuelve el valor del campo venta10
	 * @return string
	 */
	public function getVenta10(){
		return $this->venta10;
	}

	/**
	 * Devuelve el valor del campo venta0
	 * @return string
	 */
	public function getVenta0(){
		return $this->venta0;
	}

	/**
	 * Devuelve el valor del campo iva10
	 * @return string
	 */
	public function getIva10(){
		return $this->iva10;
	}

	/**
	 * Devuelve el valor del campo iva16
	 * @return string
	 */
	public function getIva16(){
		return $this->iva16;
	}

	/**
	 * Devuelve el valor del campo iva0
	 * @return string
	 */
	public function getIva0(){
		return $this->iva0;
	}

	/**
	 * Devuelve el valor del campo pagos
	 * @return string
	 */
	public function getPagos(){
		return $this->pagos;
	}

	/**
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * Devuelve el valor del campo comprob_inve
	 * @return string
	 */
	public function getComprobInve(){
		return $this->comprob_inve;
	}

	/**
	 * Devuelve el valor del campo numero_inve
	 * @return integer
	 */
	public function getNumeroInve(){
		return $this->numero_inve;
	}

	/**
	 * Devuelve el valor del campo comprob_contab
	 * @return string
	 */
	public function getComprobContab(){
		return $this->comprob_contab;
	}

	/**
	 * Devuelve el valor del campo numero_contab
	 * @return integer
	 */
	public function getNumeroContab(){
		return $this->numero_contab;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->invoicer)){
			$this->setSchema($config->hfos->invoicer);
		} else {
			$this->setSchema('invoicer');
		}
	}

}

