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

class Almacenes extends RcsRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_almacen;

	/**
	 * @var string
	 */
	protected $clase_almacen;

	/**
	 * @var string
	 */
	protected $almacenista;

	/**
	 * @var string
	 */
	protected $centro_costo;

	/**
	 * @var string
	 */
	protected $tipo_alm;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_almacen
	 * @param string $nom_almacen
	 */
	public function setNomAlmacen($nom_almacen){
		$this->nom_almacen = $nom_almacen;
	}

	/**
	 * Metodo para establecer el valor del campo clase_almacen
	 * @param string $clase_almacen
	 */
	public function setClaseAlmacen($clase_almacen){
		$this->clase_almacen = $clase_almacen;
	}

	/**
	 * Metodo para establecer el valor del campo almacenista
	 * @param string $almacenista
	 */
	public function setAlmacenista($almacenista){
		$this->almacenista = $almacenista;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param string $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_alm
	 * @param string $tipo_alm
	 */
	public function setTipoAlm($tipo_alm){
		$this->tipo_alm = $tipo_alm;
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
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_almacen
	 * @return string
	 */
	public function getNomAlmacen(){
		return $this->nom_almacen;
	}

	/**
	 * Devuelve el valor del campo clase_almacen
	 * @return string
	 */
	public function getClaseAlmacen(){
		return $this->clase_almacen;
	}

	/**
	 * Devuelve el valor del campo almacenista
	 * @return string
	 */
	public function getAlmacenista(){
		return $this->almacenista;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return string
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo tipo_alm
	 * @return string
	 */
	public function getTipoAlm(){
		return $this->tipo_alm;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	protected function beforeSave(){
		if($this->centro_costo>0){
			$empresa = EntityManager::get('Empresa')->findFirst();
			if($empresa->getCentroCosto()==$this->centro_costo){
				$this->appendMessage(new ActiveRecordMessage('El almacén no puede tener el centro de costo del hotel', 'centro_costo'));
				return false;
			}
		}
		if($this->codigo!=1){
			$tipoComprobs = array(
				'O' => 'ORDEN DE COMPRA',
				'E' => 'ENTRADAS',
				'C' => 'SALIDAS',
				'T' => 'TRASLADOS',
				'A' => 'AJUSTES',
				'R' => 'TRANSFORMACION',
				'P' => 'PEDIDOS'
			);
			foreach($tipoComprobs as $tipoComprob => $nombre){
				$codigoComprob = $tipoComprob.sprintf('%02s', $this->codigo);
				$comprob = EntityManager::get('Comprob')->findFirst("codigo='$codigoComprob'");
				if($comprob==false){
					$comprob = new Comprob();
					$comprob->setCodigo($codigoComprob);
					$comprob->setConsecutivo(0);
					$codigoComprobBase = $tipoComprob.sprintf('%02s', 1);
					$comprobBase = EntityManager::get('Comprob')->findFirst("codigo='$codigoComprobBase'");
					if($comprobBase==false){
						$diario = EntityManager::get('Diarios')->findFirst();
						if($diario!=false){
							$comprob->setDiario($diario->getCodigo());
						}
					} else {
						$comprob->setDiario($comprobBase->getDiario());
						$comprob->setCtaIva($comprobBase->getCtaIva());
						$comprob->setCtaIvad($comprobBase->getCtaIvad());
						$comprob->setCtaIvam($comprobBase->getCtaIvam());
						$comprob->setCtaCartera($comprobBase->getCtaCartera());
						$comprob->setComprobContab($comprobBase->getComprobContab());
					}
				}
				$comprob->setConnection($this->getConnection());
				$comprob->setNomComprob($nombre.' '.$this->nom_almacen);
				if($comprob->save()==false){
					foreach($comprob->getMessages() as $message){
						$this->appendMessage(new ActiveRecordMessage('Comprobante: '.$message->getMessage(), $message->getField()));
					}
					return false;
				}
			}
		}
		return true;
	}

	protected function afterSave(){
		$lineas = EntityManager::get('Lineas')->find("almacen='1'");
		foreach($lineas as $linea){
			if(EntityManager::get('Lineas')->count("almacen='{$this->codigo}' AND linea='{$linea->getLinea()}'")==0){
				$lineaAlmacen = clone $linea;
				$lineaAlmacen->setAlmacen($this->codigo);
				if($lineaAlmacen->save()==false){
					foreach($lineaAlmacen->getMessages() as $message){
						$this->appendMessage(new ActiveRecordMessage('Línea de Producto: '.$message->getMessage(), $message->getField()));
					}
					return false;
				}
			}
		}
	}

	protected function validation(){
		$this->validate('InclusionIn', array(
			'field' => 'estado',
			'domain' => array('A', 'I'),
			'message' => 'El campo "Estado" debe ser "ACTIVO" ó "INACTIVO"',
			'required' => true
		));
		if($this->validationHasFailed()==true){
			return false;
		}
	}

	protected function beforeDelete(){
		if($this->countMovihead()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el almacén porque tiene movimiento en Inventarios', 'codigo'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('almacen', 'Movihead', 'codigo');

		$this->addForeignKey('centro_costo', 'Centros', 'codigo', array(
			'message' => 'El centro de costo no existe o no es auxiliar'
		));
	}

}

