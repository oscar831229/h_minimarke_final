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

class Movihead extends RcsRecord
{

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $almacen;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $centro_costo;

	/**
	 * @var integer
	 */
	protected $n_pedido;

	/**
	 * @var Date
	 */
	protected $f_vence;

	/**
	 * @var Date
	 */
	protected $f_expira;

	/**
	 * @var Date
	 */
	protected $f_entrega;

	/**
	 * @var string
	 */
	protected $forma_pago;

	/**
	 * @var integer
	 */
	protected $almacen_destino;

	/**
	 * @var integer
	 */
	protected $usuarios_id;

	/**
	 * @var string
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $ivad;

	/**
	 * @var string
	 */
	protected $ivam;

	/**
	 * @var string
	 */
	protected $ica;

	/**
	 * @var string
	 */
	protected $descuento;

	/**
	 * @var string
	 */
	protected $retencion;

	/**
	 * @var string
	 */
	protected $saldo;

	/**
	 * @var integer
	 */
	protected $factura_c;

	/**
	 * @var string
	 */
	protected $nota;

	/**
	 * @var string
	 */
	protected $observaciones;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $total_neto;

	/**
	 * @var string
	 */
	protected $v_total;

	/**
	 * @var integer
	 */
	protected $numero_comprob_contab;

	/**
	 * @var float
	 */
	protected $cree;

	/**
	 * @var float
	 */
	protected $impo;


	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo almacen
	 * @param integer $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param string $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo n_pedido
	 * @param integer $n_pedido
	 */
	public function setNPedido($n_pedido){
		$this->n_pedido = $n_pedido;
	}

	/**
	 * Metodo para establecer el valor del campo f_vence
	 * @param Date $f_vence
	 */
	public function setFVence($f_vence){
		$this->f_vence = $f_vence;
	}

	/**
	 * Metodo para establecer el valor del campo f_expira
	 * @param Date $f_expira
	 */
	public function setFExpira($f_expira){
		$this->f_expira = $f_expira;
	}

	/**
	 * Metodo para establecer el valor del campo f_entrega
	 * @param Date $f_entrega
	 */
	public function setFEntrega($f_entrega){
		$this->f_entrega = $f_entrega;
	}

	/**
	 * Metodo para establecer el valor del campo forma_pago
	 * @param string $forma_pago
	 */
	public function setFormaPago($forma_pago){
		$this->forma_pago = $forma_pago;
	}

	/**
	 * Metodo para establecer el valor del campo almacen_destino
	 * @param integer $almacen_destino
	 */
	public function setAlmacenDestino($almacen_destino){
		$this->almacen_destino = $almacen_destino;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo ivad
	 * @param string $ivad
	 */
	public function setIvad($ivad){
		$this->ivad = $ivad;
	}

	/**
	 * Metodo para establecer el valor del campo ivam
	 * @param string $ivam
	 */
	public function setIvam($ivam){
		$this->ivam = $ivam;
	}

	/**
	 * Metodo para establecer el valor del campo ica
	 * @param string $ica
	 */
	public function setIca($ica){
		$this->ica = $ica;
	}

	/**
	 * Metodo para establecer el valor del campo descuento
	 * @param string $descuento
	 */
	public function setDescuento($descuento){
		$this->descuento = $descuento;
	}

	/**
	 * Metodo para establecer el valor del campo retencion
	 * @param string $retencion
	 */
	public function setRetencion($retencion){
		$this->retencion = $retencion;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}

	/**
	 * Metodo para establecer el valor del campo factura_c
	 * @param integer $factura_c
	 */
	public function setFacturaC($factura_c){
		$this->factura_c = $factura_c;
	}

	/**
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo total_neto
	 * @param string $total_neto
	 */
	public function setTotalNeto($total_neto){
		$this->total_neto = $total_neto;
	}

	/**
	 * Metodo para establecer el valor del campo v_total
	 * @param string $v_total
	 */
	public function setVTotal($v_total){
		$this->v_total = $v_total;
	}

	/**
	 * Metodo para establecer el valor del campo numero_comprob_contab
	 * @param integer $numero_comprob_contab
	 */
	public function setNumeroComprobContab($numero_comprob_contab){
		$this->numero_comprob_contab = $numero_comprob_contab;
	}

	/**
	 * Metodo para establecer el valor del cree
	 * @param float $cree
	 */
	public function setCree($cree){
		$this->cree = $cree;
	}

	/**
	 * Metodo para establecer el valor del impo
	 * @param float $impo
	 */
	public function setImpo($impo){
		$this->impo = $impo;
	}

	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo almacen
	 * @return integer
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return string
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo n_pedido
	 * @return integer
	 */
	public function getNPedido(){
		return $this->n_pedido;
	}

	/**
	 * Devuelve el valor del campo f_vence
	 * @return Date
	 */
	public function getFVence(){
		return new Date($this->f_vence);
	}

	/**
	 * Devuelve el valor del campo f_expira
	 * @return Date
	 */
	public function getFExpira(){
		return new Date($this->f_expira);
	}

	/**
	 * Devuelve el valor del campo f_entrega
	 * @return Date
	 */
	public function getFEntrega(){
		return new Date($this->f_entrega);
	}

	/**
	 * Devuelve el valor del campo forma_pago
	 * @return string
	 */
	public function getFormaPago(){
		return $this->forma_pago;
	}

	/**
	 * Devuelve el valor del campo almacen_destino
	 * @return integer
	 */
	public function getAlmacenDestino(){
		return $this->almacen_destino;
	}

	/**
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return string
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo ivad
	 * @return string
	 */
	public function getIvad(){
		return $this->ivad;
	}

	/**
	 * Devuelve el valor del campo ivam
	 * @return string
	 */
	public function getIvam(){
		return $this->ivam;
	}

	/**
	 * Devuelve el valor del campo ica
	 * @return string
	 */
	public function getIca(){
		return $this->ica;
	}

	/**
	 * Devuelve el valor del campo descuento
	 * @return string
	 */
	public function getDescuento(){
		return $this->descuento;
	}

	/**
	 * Devuelve el valor del campo retencion
	 * @return string
	 */
	public function getRetencion(){
		return $this->retencion;
	}

	/**
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Devuelve el valor del campo factura_c
	 * @return integer
	 */
	public function getFacturaC(){
		return $this->factura_c;
	}

	/**
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo total_neto
	 * @return string
	 */
	public function getTotalNeto(){
		return $this->total_neto;
	}

	/**
	 * Devuelve el valor del campo v_total
	 * @return string
	 */
	public function getVTotal(){
		return $this->v_total;
	}

	/**
	 * Devuelve el valor del campo numero_comprob_contab
	 * @return integer
	 */
	public function getNumeroComprobContab()
	{
		return $this->numero_comprob_contab;
	}

	/**
	 * Devuelve el valor del campo cree
	 * @return float
	 */
	public function getCree(){
		return $this->cree;
	}

	/**
	 * Devuelve el valor del campo impo
	 * @return float
	 */
	public function getImpo(){
		return $this->impo;
	}


	public function initialize(){
		$this->hasMany(array('almacen', 'comprob', 'numero'), 'Movilin', array('almacen', 'comprob', 'numero'));
		$this->belongsTo('nit', 'Nits', 'nit');
		$this->belongsTo('almacen', 'Almacenes', 'codigo');
	}

	public function getAlmacenNombre(){
		if($this->almacen>0){
			$almacen = BackCacher::getAlmacen($this->almacen);
			if($almacen==false){
				return 'SIN ASIGNAR';
			} else {
				return $almacen->getNomAlmacen();
			}
		} else {
			return 'SIN ASIGNAR';
		}
	}

	public function getProveedorNombre(){
		if($this->nit){
			$tercero = BackCacher::getTercero($this->nit);
			if($tercero==false){
				return 'NINGUNO';
			} else {
				return $tercero->getNombre();
			}
		} else {
			return 'NINGUNO';
		}
	}

	public function getAlmacenDestinoNombre(){
		if($this->almacen_destino>0){
			$almacen = BackCacher::getAlmacen($this->almacen_destino);
			if($almacen==false){
				return 'SIN ASIGNAR';
			} else {
				return $almacen->getNomAlmacen();
			}
		} else {
			return 'SIN ASIGNAR';
		}
	}

	public function getEstadoDetalle(){
		if($this->estado){
			switch($this->estado){
				case 'A':
					return 'ABIERTO';
				case 'C':
					return 'CERRADO';
				case 'I':
					return 'ANULADO';
			}
		}
		return 'DESCONOCIDO';
	}

}

