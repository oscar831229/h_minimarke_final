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

class Activos extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $grupo;

	/**
	 * @var string
	 */
	protected $centro_costo;

	/**
	 * @var integer
	 */
	protected $tipos_activos_id;

	/**
	 * @var integer
	 */
	protected $cantidad;

	/**
	 * @var Date
	 */
	protected $fecha_compra;

	/**
	 * @var string
	 */
	protected $valor_compra;

	/**
	 * @var string
	 */
	protected $valor_iva;

	/**
	 * @var string
	 */
	protected $numero_fac;

	/**
	 * @var string
	 */
	protected $serie;

	/**
	 * @var string
	 */
	protected $proveedor;

	/**
	 * @var string
	 */
	protected $responsable;

	/**
	 * @var integer
	 */
	protected $ubicacion;

	/**
	 * @var integer
	 */
	protected $meses_a_dep;

	/**
	 * @var string
	 */
	protected $meses_dep;

	/**
	 * @var integer
	 */
	protected $forma_pago;

	/**
	 * @var string
	 */
	protected $dep_acumulada;

	/**
	 * @var string
	 */
	protected $valor_ajus;

	/**
	 * @var string
	 */
	protected $paag_acumulado;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $inventariado;

	/**
	 * @var Date
	 */
	protected $fecha_inv;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo grupo
	 * @param string $grupo
	 */
	public function setGrupo($grupo){
		$this->grupo = $grupo;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param string $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo tipos_activos_id
	 * @param integer $tipos_activos_id
	 */
	public function setTiposActivosId($tipos_activos_id){
		$this->tipos_activos_id = $tipos_activos_id;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad
	 * @param integer $cantidad
	 */
	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_compra
	 * @param Date $fecha_compra
	 */
	public function setFechaCompra($fecha_compra){
		$this->fecha_compra = $fecha_compra;
	}

	/**
	 * Metodo para establecer el valor del campo valor_compra
	 * @param string $valor_compra
	 */
	public function setValorCompra($valor_compra){
		$this->valor_compra = $valor_compra;
	}

	/**
	 * Metodo para establecer el valor del campo valor_iva
	 * @param string $valor_iva
	 */
	public function setValorIva($valor_iva){
		$this->valor_iva = $valor_iva;
	}

	/**
	 * Metodo para establecer el valor del campo numero_fac
	 * @param string $numero_fac
	 */
	public function setNumeroFac($numero_fac){
		$this->numero_fac = $numero_fac;
	}

	/**
	 * Metodo para establecer el valor del campo serie
	 * @param string $serie
	 */
	public function setSerie($serie){
		$this->serie = $serie;
	}

	/**
	 * Metodo para establecer el valor del campo proveedor
	 * @param string $proveedor
	 */
	public function setProveedor($proveedor){
		$this->proveedor = $proveedor;
	}

	/**
	 * Metodo para establecer el valor del campo responsable
	 * @param string $responsable
	 */
	public function setResponsable($responsable){
		$this->responsable = $responsable;
	}

	/**
	 * Metodo para establecer el valor del campo ubicacion
	 * @param integer $ubicacion
	 */
	public function setUbicacion($ubicacion){
		$this->ubicacion = $ubicacion;
	}

	/**
	 * Metodo para establecer el valor del campo meses_a_dep
	 * @param integer $meses_a_dep
	 */
	public function setMesesADep($meses_a_dep){
		$this->meses_a_dep = $meses_a_dep;
	}

	/**
	 * Metodo para establecer el valor del campo meses_dep
	 * @param string $meses_dep
	 */
	public function setMesesDep($meses_dep){
		$this->meses_dep = $meses_dep;
	}

	/**
	 * Metodo para establecer el valor del campo forma_pago
	 * @param integer $forma_pago
	 */
	public function setFormaPago($forma_pago){
		$this->forma_pago = $forma_pago;
	}

	/**
	 * Metodo para establecer el valor del campo dep_acumulada
	 * @param string $dep_acumulada
	 */
	public function setDepAcumulada($dep_acumulada){
		$this->dep_acumulada = $dep_acumulada;
	}

	/**
	 * Metodo para establecer el valor del campo valor_ajus
	 * @param string $valor_ajus
	 */
	public function setValorAjus($valor_ajus){
		$this->valor_ajus = $valor_ajus;
	}

	/**
	 * Metodo para establecer el valor del campo paag_acumulado
	 * @param string $paag_acumulado
	 */
	public function setPaagAcumulado($paag_acumulado){
		$this->paag_acumulado = $paag_acumulado;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo inventariado
	 * @param string $inventariado
	 */
	public function setInventariado($inventariado){
		$this->inventariado = $inventariado;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_inv
	 * @param Date $fecha_inv
	 */
	public function setFechaInv($fecha_inv){
		$this->fecha_inv = $fecha_inv;
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
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo grupo
	 * @return string
	 */
	public function getGrupo(){
		return $this->grupo;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return string
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo tipos_activos_id
	 * @return integer
	 */
	public function getTiposActivosId(){
		return $this->tipos_activos_id;
	}

	/**
	 * Devuelve el valor del campo cantidad
	 * @return integer
	 */
	public function getCantidad(){
		return $this->cantidad;
	}

	/**
	 * Devuelve el valor del campo fecha_compra
	 * @return Date
	 */
	public function getFechaCompra(){
		return new Date($this->fecha_compra);
	}

	/**
	 * Devuelve el valor del campo valor_compra
	 * @return string
	 */
	public function getValorCompra(){
		return $this->valor_compra;
	}

	/**
	 * Devuelve el valor del campo valor_iva
	 * @return string
	 */
	public function getValorIva(){
		return $this->valor_iva;
	}

	/**
	 * Devuelve el valor del campo numero_fac
	 * @return string
	 */
	public function getNumeroFac(){
		return $this->numero_fac;
	}

	/**
	 * Devuelve el valor del campo serie
	 * @return string
	 */
	public function getSerie(){
		return $this->serie;
	}

	/**
	 * Devuelve el valor del campo proveedor
	 * @return string
	 */
	public function getProveedor(){
		return $this->proveedor;
	}

	/**
	 * Devuelve el valor del campo responsable
	 * @return string
	 */
	public function getResponsable(){
		return $this->responsable;
	}

	/**
	 * Devuelve el valor del campo ubicacion
	 * @return integer
	 */
	public function getUbicacion(){
		return $this->ubicacion;
	}

	/**
	 * Devuelve el valor del campo meses_a_dep
	 * @return integer
	 */
	public function getMesesADep(){
		return $this->meses_a_dep;
	}

	/**
	 * Devuelve el valor del campo meses_dep
	 * @return string
	 */
	public function getMesesDep(){
		return $this->meses_dep;
	}

	/**
	 * Devuelve el valor del campo forma_pago
	 * @return integer
	 */
	public function getFormaPago(){
		return $this->forma_pago;
	}

	/**
	 * Devuelve el valor del campo dep_acumulada
	 * @return string
	 */
	public function getDepAcumulada(){
		return $this->dep_acumulada;
	}

	/**
	 * Devuelve el valor del campo valor_ajus
	 * @return string
	 */
	public function getValorAjus(){
		return $this->valor_ajus;
	}

	/**
	 * Devuelve el valor del campo paag_acumulado
	 * @return string
	 */
	public function getPaagAcumulado(){
		return $this->paag_acumulado;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo inventariado
	 * @return string
	 */
	public function getInventariado(){
		return $this->inventariado;
	}

	/**
	 * Devuelve el valor del campo fecha_inv
	 * @return Date
	 */
	public function getFechaInv(){
		return new Date($this->fecha_inv);
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

	public function getDetalleEstado(){
		$estados = array(
			'B' => 'BUENO',
			'R' => 'REGULAR',
			'M' => 'MALO',
			'I' => 'INACTIVO/PARA BAJA'
		);
		if(isset($estados[$this->estado])){
			return $estados[$this->estado];
		} else {
			return $this->estado;
		}
	}

	public function beforeSave(){
		if($this->meses_a_dep>0){
			if(!$this->valor_compra){
				$this->appendMessage(new ActiveRecordMessage('Indique el valor de compra del activo', 'valor_compra'));
				return false;
			}
		}
		if($this->id>0){
			$activoAnterior = BackCacher::getActivo($this->id);
			if($activoAnterior!=false){
				$identity = IdentityManager::getActive();
				if($activoAnterior->getCentroCosto()!=$this->centro_costo){
					$novedad = new Novact();
					$novedad->setConnection($this->getConnection());
					$novedad->setCodigo($this->id);
					$novedad->setUsuariosId($identity['id']);
					$novedad->setFecha(Date::getCurrentDate());
					$novedad->setNovedad('SE TRASLADÓ DEL CENTRO DE COSTO '.$activoAnterior->getCentroCosto().' AL '.$this->centro_costo);
					if($novedad->save()==false){
						foreach($novedad->getMessages() as $message){
							$this->appendMessage(new ActiveRecordMessage('Novedad: '.$message->getMessage(), 'centro_costo'));
						}
						return false;
					}
				}
				if($activoAnterior->getUbicacion()!=$this->ubicacion){
					$novedad = new Novact();
					$novedad->setConnection($this->getConnection());
					$novedad->setCodigo($this->id);
					$novedad->setUsuariosId($identity['id']);
					$novedad->setFecha(Date::getCurrentDate());
					$novedad->setNovedad('SE TRASLADÓ DE LA UBICACIÓN '.$activoAnterior->getUbicacion().' A LA '.$this->ubicacion);
					if($novedad->save()==false){
						foreach($novedad->getMessages() as $message){
							$this->appendMessage(new ActiveRecordMessage('Novedad: '.$message->getMessage(), 'ubicacion'));
						}
						return false;
					}
				}
				if($activoAnterior->getResponsable()!=$this->responsable){
					$novedad = new Novact();
					$novedad->setConnection($this->getConnection());
					$novedad->setCodigo($this->id);
					$novedad->setUsuariosId($identity['id']);
					$novedad->setFecha(Date::getCurrentDate());
					$novedad->setNovedad('SE CAMBIÓ EL RESPONSABLE DE '.$activoAnterior->getResponsable().' AL '.$this->responsable);
					if($novedad->save()==false){
						foreach($novedad->getMessages() as $message){
							$this->appendMessage(new ActiveRecordMessage('Novedad: '.$message->getMessage(), 'responsable'));
						}
						return false;
					}
				}
				if($activoAnterior->getEstado()!=$this->estado){
					$novedad = new Novact();
					$novedad->setConnection($this->getConnection());
					$novedad->setCodigo($this->id);
					$novedad->setUsuariosId($identity['id']);
					$novedad->setFecha(Date::getCurrentDate());
					$novedad->setNovedad('SE CAMBIÓ EL ESTADO DE '.$activoAnterior->getDetalleEstado().' AL '.$this->getDetalleEstado());
					if($novedad->save()==false){
						foreach($novedad->getMessages() as $message){
							$this->appendMessage(new ActiveRecordMessage('Novedad: '.$message->getMessage(), 'responsable'));
						}
						return false;
					}
				}
			}
		}
		if($this->comprob==''){
			$formaPago = EntityManager::get('FormaPago')->findFirst("codigo='{$this->forma_pago}'");
			if($formaPago==false){
				$this->appendMessage(new ActiveRecordMessage('La forma de pago del activo no es válida', 'forma_pago'));
				return false;
			}

			if($this->numero_fac<=0){
				$this->appendMessage(new ActiveRecordMessage('La número de factura del activo no es válido', 'numero_fac'));
				return false;
			}

			try {

				$tercero = BackCacher::getTercero($this->proveedor);
				if($tercero==false){
					$this->appendMessage(new ActiveRecordMessage('El tercero del proveedor no existe', 'proveedor'));
					return false;
				}

				$grupo = EntityManager::get('Grupos')->findFirst("linea='{$this->grupo}'");
				if($grupo==false){
					$this->appendMessage(new ActiveRecordMessage('El grupo del activo no es válida', 'forma_pago'));
					return false;
				}
				if($grupo->getCtaCompra()==''){
					$this->appendMessage(new ActiveRecordMessage('La cuenta de compra en el grupo del activo no es válida', 'grupo'));
					return false;
				}

				$valorCuentaPagar = $this->valor_compra;
				$comprobEntradas = Settings::get('comprob_entactivo');
				$cuentaCartera = $formaPago->getCtaContable();
				$conditions = "cuenta='$cuentaCartera' AND nit='{$this->proveedor}' AND tipo_doc='FXP' and numero_doc='{$this->numero_fac}'";
				$movi = EntityManager::get('Movi')->findFirst($conditions);
				if($movi==false){
					$aura = new Aura($comprobEntradas, 0, null, Aura::OP_CREATE);
				} else {
					$aura = new Aura($comprobEntradas, $movi->getNumero(), null, Aura::OP_UPDATE);
				}

				//Cuenta Entrada
				$aura->appendMovement(array(
					'Cuenta' => $grupo->getCtaCompra(),
					'Nit' => $this->proveedor,
					'CentroCosto' => $this->centro_costo,
					'Descripcion' => 'ENTRADA '.$this->descripcion,
					'Valor' => $this->valor_compra,
					'DebCre' => 'D'
				));

				//Cuenta Iva
				if($this->valor_iva>0){
					$aura->appendMovement(array(
						'Cuenta' => $grupo->getCtaCompra(),
						'Nit' => $this->proveedor,
						'CentroCosto' => $this->centro_costo,
						'Descripcion' => 'IVA '.$this->descripcion,
						'Valor' => $this->valor_iva,
						'DebCre' => 'D'
					));
					$valorCuentaPagar+=$this->valor_iva;
				}

				//Ica
				if($tercero->getApAereo()>0){
					$valorIca = $this->valor_compra*$tercero->getApAereo()/1000;
					$aura->appendMovement(array(
						'Cuenta' => $grupo->getCtaCompra(),
						'Nit' => $this->proveedor,
						'CentroCosto' => $this->centro_costo,
						'Descripcion' => 'ICA '.$tercero->getApAereo().'%',
						'Valor' => $valorIca,
						'DebCre' => 'C'
					));
					$valorCuentaPagar-=$valorIca;
				}

				//Retención
				if($grupo->getPorcCompra()>0){
					if($grupo->getMinimoRet()<=$this->valor_compra){
						if($grupo->getCtaRetCompra()==''){
							$this->appendMessage(new ActiveRecordMessage('La cuenta de retención en la compra en el grupo del activo no es válida', 'grupo'));
							return false;
						}
						$valorRetencion = $this->valor_compra*$grupo->getPorcCompra();
						$aura->appendMovement(array(
							'Cuenta' => $grupo->getCtaRetCompra(),
							'Nit' => $this->proveedor,
							'CentroCosto' => $this->centro_costo,
							'Descripcion' => 'RETENCION '.($grupo->getPorcCompra()*100).'%',
							'Valor' => $valorRetencion,
							'BaseGrab' => $this->valor_compra,
							'DebCre' => 'C'
						));
						$valorCuentaPagar-=$valorRetencion;
					}
				}

				//Cuenta Por Pagar
				$aura->appendMovement(array(
					'Cuenta' => $cuentaCartera,
					'Nit' => $this->proveedor,
					'CentroCosto' => $this->centro_costo,
					'Valor' => $valorCuentaPagar,
					'Descripcion' => 'CUENTA POR PAGAR',
					'DebCre' => 'C',
					'TipoDocumento' => 'FXP',
					'NumeroDocumento' => $this->numero_fac
				));

				//Grabar
				$aura->save();
				$this->comprob = $comprobEntradas;
				$this->numero = $aura->getConsecutivo();

			}
			catch(AuraException $e){
				$this->appendMessage(new ActiveRecordMessage('Entrada: '.$e->getMessage(), 'codigo'));
				return false;
			}
		};
		return true;
	}

	public function afterSave(){
		$identity = IdentityManager::getActive();
		$novedad = new Novact();
		$novedad->setConnection($this->getConnection());
		$novedad->setCodigo($this->id);
		$novedad->setUsuariosId($identity['id']);
		$novedad->setFecha(Date::getCurrentDate());
		$novedad->setNovedad('SE INGRESÓ CON FECHA DE COMPRA '.$this->fecha_compra);
		if($novedad->save()==false){
			foreach($novedad->getMessages() as $message){
				$this->appendMessage(new ActiveRecordMessage('Novedad: '.$message->getMessage(), 'responsable'));
			}
			return false;
		}
		return true;
	}

	public function beforeDelete(){
		if($this->countDepreciacion()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el activos fijo porque ha sido depreciado anteriormente', 'codigo'));
			return false;
		}
	}

	public function initialize(){
		$this->addForeignKey('grupo', 'Grupos', 'linea', array(
			'message' => 'El grupo del activo fijo no es válido'
		));
		$this->addForeignKey('ubicacion', 'Ubicacion', 'codigo', array(
			'message' => 'La ubicación indicada no es válida'
		));
		$this->addForeignKey('centro_costo', 'Centros', 'codigo', array(
			'message' => 'El centro de costo indicado no es válido'
		));
		$this->addForeignKey('proveedor', 'Nits', 'nit', array(
			'message' => 'El proveedor indicado no es un tercero válido'
		));
		$this->addForeignKey('responsable', 'Nits', 'nit', array(
			'message' => 'El responsable indicado no es un tercero válido'
		));
		$this->addForeignKey('tipos_activos_id', 'TiposActivos', 'codigo', array(
			'message' => 'El tipo de activo indicado no es válido'
		));
		$this->addForeignKey('forma_pago', 'FormaPago', 'codigo', array(
			'message' => 'La forma de pago indicada no es válida'
		));
		$this->hasMany('Depreciacion');
	}

}
