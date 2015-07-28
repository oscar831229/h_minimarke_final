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

class CostoInventario extends UserComponent
{

	private $_code = '';
	private $_level = 0;
	private $_verbose = false;
	private $_color = '';
	private $_receta;
	private $_warnNoExists = array();

	private static $_costos = array();

	private static $_instance = null;

	private static $_recetas = array();

	private static $_inves = array();

	private static $_unidades = array();

	public function __construct($verbose=true){
		$this->_verbose = $verbose;
		$this->_level = 0;
		if($verbose){
			$this->_code = "<table width='100%' align='left' class='resumenCosto' cellspacing='0'>";
			$this->_color = '#ffffff';
		}
	}

	public function setVerbose($verbose)
	{
		$this->_verbose = $verbose;
	}

	private function _queryCostoInve($codigo)
	{
		if (!isset(self::$_costos[$codigo])) {
			$costo = array();
			if(!isset(self::$_inves[$codigo])){
				$inve = $this->Inve->findFirst("item='$codigo'", "columns: item,descripcion,unidad,volumen");
				self::$_inves[$codigo] = $inve;
			} else {
				$inve = self::$_inves[$codigo];
			}
			if($inve!=false){
				$costo['item'] = $codigo;
				$costo['descripcion'] = $inve->getDescripcion();
				if(!isset(self::$_unidades[$inve->getUnidad()])){
					$unidad = $this->Unidad->findFirst("codigo='{$inve->getUnidad()}'");
					self::$_unidades[$inve->getUnidad()] = $unidad;
				} else {
					$unidad = self::$_unidades[$inve->getUnidad()];
				}
				if($unidad!=false){
					$unidadNombre = $unidad->nom_unidad;
				} else {
					$unidadNombre = "INDEFINIDO";
				}
				$costo['unidad'] = $unidadNombre;
				$costo['volumen'] = $inve->getVolumen();
				$saldo = $this->Saldos->findFirst("item='$codigo' AND almacen=1 AND ano_mes=0", "columns: costo,saldo");
				if($saldo!=false){
					if($saldo->getSaldo()>0){
						$valorCosto = ($saldo->getCosto()/$saldo->getSaldo());
						if($valorCosto<0){
							$valorCosto = 0;
						}
						$costo['valor'] = $valorCosto;
						$costo['tipo'] = 'ALMACEN';
						$costo['numero'] = 1;
						self::$_costos[$codigo] = $costo;
						return $costo;
					}
				}

				$saldos = $this->Saldos->find("item='$codigo' AND almacen<>1 AND ano_mes=0", "columns: almacen,costo,saldo");
				foreach($saldos as $saldo){
					if($saldo->getSaldo()>0){
						$valorCosto = ($saldo->getCosto()/$saldo->getSaldo());
						if($valorCosto<0){
							$valorCosto = 0;
						}
						$costo['valor'] = $valorCosto;
						$costo['tipo'] = 'ALMACEN';
						$costo['numero'] = $saldo->getAlmacen();
						self::$_costos[$codigo] = $costo;
						return $costo;
					}
				}

				$entrada = 'E01';
				$movilin = $this->Movilin->findFirst("comprob='$entrada' AND almacen=1 AND item='{$codigo}' AND cantidad>0", "order: numero DESC", "columns: valor,cantidad,numero");
				if($movilin){
					$valorCosto = ($movilin->getValor()/$movilin->getCantidad());
					if($valorCosto<0){
						$valorCosto = 0;
					}
					$costo['valor'] = $valorCosto;
					$costo['tipo'] = 'ENTRADA';
					$costo['numero'] = $entrada.':'.$movilin->getNumero();
					self::$_costos[$codigo] = $costo;
					return $costo;
				} else {
					$traslado = 'T01';
					$movilin = $this->Movilin->findFirst("comprob='$traslado' AND almacen=1 AND item='{$codigo}' AND cantidad>0", "order: numero DESC", "columns: valor,cantidad,numero");
					if($movilin){
						$valorCosto = ($movilin->getValor()/$movilin->getCantidad());
						if($valorCosto<0){
							$valorCosto = 0;
						}
						$costo['valor'] = $valorCosto;
						$costo['tipo'] = 'TRASLADO';
						$costo['numero'] = $traslado.':'.$movilin->getNumero();
						self::$_costos[$codigo] = $costo;
						return $costo;
					}
				}
				$this->_warnNoExists[$codigo." : ".utf8_encode($inve->getDescripcion())] = 1;
				$costo['valor'] = 0;
				$costo['tipo'] = 'INDEFINIDO';
				$costo['numero'] = '-';
				self::$_costos[$codigo] = $costo;
				return $costo;
			} else {
				self::$_costos[$codigo] = false;
				return false;
			}
		} else {
			return self::$_costos[$codigo];
		}
	}

	public function obtenerCostoInve($codigo, $nombre, $cantidad, $descontar='')
	{
		$costo = $this->_queryCostoInve($codigo);
		if ($costo != false) {
			$valorCosto = $costo['valor']*$cantidad;
			if($descontar=='T'){
				if($costo['volumen']<=0){
					if($this->_verbose){
						Flash::error("No se ha definido el número de tragos para '{$costo['descripcion']}' en el item de menú '$nombre'");
					}
					return 0;
				} else {
					$valorCosto = $valorCosto/$costo['volumen'];
				}
			}
			$valorCosto = LocaleMath::round($valorCosto, 2);
			if($this->_verbose){
				$this->_code.="<tr bgcolor='{$this->_color}'>
					<td>".$codigo."</td>
					<td>".utf8_encode($costo['descripcion'])."</td>
					<td align='right'>".LocaleMath::round($cantidad, 3)."</td>
					<td align='center'>".$costo['unidad']."</td>
					<td>".utf8_encode($this->_receta)."</td>
					<td>".$costo['tipo']."</td>
					<td align='center'>".$costo['numero']."</td>
					<td align='right'>".$valorCosto."</td>
				</tr>";
				if($this->_color=='#ffffff'){
					$this->_color = '#fafafa';
				} else {
					$this->_color = '#ffffff';
				}
			}
			return $valorCosto;
		} else {
			if($this->_verbose){
				Flash::error("No se puede actualizar el costo de '$nombre' porque la referencia '$codigo' no existe");
			}
			return 0;
		}

	}

	public function obtenerCostoReceta($codigo, $nombre)
	{
		$sinRecalcular = false;
		if (!isset(self::$_recetas[$codigo])) {
			$recetap = $this->Recetap->findFirst("almacen = 1 AND numero_rec='{$codigo}'");
			self::$_recetas[$codigo] = $recetap;
		} else {
			$sinRecalcular = true;
			$recetap = self::$_recetas[$codigo];
		}
		if($recetap!=false){
			if ($this->_level > 0){
				if ($recetap->num_personas == 1) {
					$this->_receta = $recetap->nombre . ' / ' . $recetap->num_personas . ' PERSONA';
				} else {
					$this->_receta = $recetap->nombre . ' / ' . $recetap->num_personas . ' PERSONAS';
				}
			} else {
				$this->_receta = "";
			}
			$costoTotal = 0;
			$recetalineas = $this->Recetal->find("almacen = 1 AND numero_rec='{$codigo}'");
			if(count($recetalineas)>0){
				foreach($recetalineas as $recetalinea){
					if($recetalinea->tipol=='R'&&$recetalinea->item==$codigo){
						if($this->_verbose){
							Flash::error("La receta $codigo se contiene a si misma");
						}
						continue;
					}
					$costo = 0;
					if($recetalinea->tipol=='I'){
						if($recetalinea->divisor!=0){
							if($recetalinea->cantidad!=0){
								$costo = $this->obtenerCostoInve($recetalinea->item, $nombre, $recetalinea->cantidad/$recetalinea->divisor);
							} else {
								if($this->_verbose){
									Flash::warning("La cantidad del item '{$recetalinea->item}' en la receta {$recetap->nombre} es 0");
								}
							}
						} else {
							if($this->_verbose){
								Flash::warning("El divisor del item '{$recetalinea->item}' en la receta {$recetap->nombre} es 0");
							}
						}
					} else {
						$this->_level++;
						$costo = $this->obtenerCostoReceta($recetalinea->item, $nombre);
						if($recetalinea->divisor){
							$costo = $costo/$recetalinea->divisor*$recetalinea->cantidad;
						} else {
							if($this->_verbose){
								Flash::warning("El divisor de la receta '{$recetalinea->item}' en la receta {$recetap->nombre} es 0");
							}
						}
						$this->_level--;
					}
					$costoTotal+=$costo;
					if($sinRecalcular==false){
						if($recetalinea->valor!=$costo){
							$recetalinea->valor = $costo;
							if($recetalinea->save()==false){
								foreach($recetalinea->getMessages() as $message){
									if($this->_verbose){
										Flash::error($message->getMessage());
									}
								}
							}
						}
					}
				}
			} else {
				if($this->_verbose){
					Flash::warning("La receta '$nombre' no tiene referencias ó sub-recetas que la conformen");
				}
				return 0;
			}
			if($this->_level>0){
				if($recetap->num_personas==1){
					$this->_receta = $recetap->nombre.' / '.$recetap->num_personas.' PERSONA';
				} else {
					$this->_receta = $recetap->nombre.' / '.$recetap->num_personas.' PERSONAS';
				}
			} else {
				$this->_receta = "";
			}
			$costoTotal = LocaleMath::round($costoTotal, 2);
			if($recetap->num_personas>0){
				if($this->_verbose){
					$this->_code.= "<tr class='itemCostoSubTotal'><td align='right' colspan='7'>COSTO TOTAL DE '".utf8_encode($recetap->nombre)."' PARA {$recetap->num_personas} PERSONA(S):</td><td align='right'>".$costoTotal."</td></tr>";
				}
				$costo = $costoTotal/$recetap->num_personas;
				if($sinRecalcular==false){
					if($costoTotal!=$recetap->precio_costo){
						$recetap->precio_costo = $costoTotal;
						if($recetap->save()==false){
							foreach($recetap->getMessages() as $message){
								if($this->_verbose){
									Flash::error($message->getMessage());
								}
							}
						}
					}
				}
				$this->_receta = '';
				self::$_recetas[$codigo] = $recetap;
				return $costo;
			} else {
				if($this->_verbose){
					Flash::warning("El número de personas de la receta {$recetap->nombre} es 0, se asume 1");
				}
				self::$_recetas[$codigo] = $recetap;
				return $costoTotal;
			}
		} else {
			if($this->_verbose){
				Flash::error("No se puede obtener el costo de '$nombre' porque la receta '$codigo' no existe");
			}
			return 0;
		}
	}

	public function obtenerCosto($tipo, $nombre, $codigo, $descontar, $precioVenta){
		$saldo = 0;
		$costo = 0;
		$this->_level = 0;
		if($this->_verbose){
			if($tipo=='I'){
				if($descontar=='T'){
					$tipov = 'Referencia (Descontar x Trago) ('.$codigo.')';
				} else {
					$tipov = 'Referencia ('.$codigo.')';
				}
			} else {
				if($tipo=='R'){
					$tipov = 'Receta Estándar ('.$codigo.')';
				} else {
					$tipov = '<span style="color:red">SIN DEFINIR</span>';
				}
			}
			$this->_code.= "<tr><td colspan='8'>&nbsp;<br></td></tr>";
			$this->_code.= "<tr><td align='left' colspan='8' class='itemCosto'>Item de Menú: $nombre Tipo: $tipov</td></tr>";
			$this->_code.= "<tr>
				<th>Código</th>
				<th>Nombre</th>
				<th>Cant.</th>
				<th>Unidad</th>
				<th>Sub-Receta</th>
				<th>Origen</th>
				<th>Número</th>
				<th>Costo</th>
			</tr>";
		}
		if($tipo=='I'){
			$costo = $this->obtenerCostoInve($codigo, $nombre, 1, $descontar);
		} else {
			if($tipo=='R'){
				$costo = $this->obtenerCostoReceta($codigo, $nombre);
			}
		}
		if($this->_verbose){
			$this->_code.= "<tr class='itemCostoTotal'><td align='right' colspan='7'><b>COSTO TOTAL PARA 1 PERSONA</b></td><td align='right' bgcolor='#ffffff'>".LocaleMath::round($costo, 2)."</td></tr>";
			$this->_code.= "<tr class='itemCostoTotal'><td align='right' colspan='7'><b>PRECIO VENTA</b></td><td align='right' bgcolor='#ffffff'>".LocaleMath::round($precioVenta, 2)."</td></tr>";
			if($precioVenta>0){
				$this->_code.= "<tr class='itemCostoTotal'><td align='right' colspan='7'><b>PORCENTAJE COSTO</b></td><td align='right' bgcolor='#ffffff'>".LocaleMath::round($costo/$precioVenta*100, 2)."%</td></tr>";
				$this->_code.= "<tr class='itemCostoTotal'><td align='right' colspan='7'><b>PORCENTAJE UTILIDAD</b></td><td align='right' bgcolor='#ffffff'>".LocaleMath::round(100-($costo/$precioVenta*100), 2)."%</td></tr>";
				$this->_code.= "<tr class='itemCostoTotal'><td align='right' colspan='7'><b>UTILIDAD</b></td><td align='right' bgcolor='#ffffff'>".LocaleMath::round($precioVenta-$costo, 2)."</td></tr>";
			} else {
				$this->_code.= "<tr class='itemCostoTotal'><td align='right' colspan='7'><b>PORCENTAJE COSTO</b></td><td align='right' bgcolor='pink'>INDEFINIDO</td></tr>";
				$this->_code.= "<tr class='itemCostoTotal'><td align='right' colspan='7'><b>PORCENTAJE UTILIDAD</b></td><td align='right' bgcolor='pink'>INDEFINIDO</td></tr>";
				$this->_code.= "<tr class='itemCostoTotal'><td align='right' colspan='7'><b>UTILIDAD</b></td><td align='right' bgcolor='pink'>INDEFINIDO</td></tr>";
			}
			$this->_code.= "<tr><td colspan='8'>&nbsp;<br></td></tr>";
		}
		$this->_level = 0;
		return $costo;
	}

	public function getResume(){
		if(count($this->_warnNoExists)){
			Flash::warning("No se pudo obtener el costo de las siguientes referencias ya que no hay ó no ha habido existencias en inventarios:<br>'".join(",<br>", array_keys($this->_warnNoExists))."'");
		}
		$this->_code.= "</table>";
		return $this->_code;
	}

	public static function getCosto($codigo, $tipo){
		if(self::$_instance===null){
			self::$_instance = new self(false);
		}
		return self::$_instance->obtenerCosto($tipo, '', $codigo, '', '', 0);
	}

}
