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

class InterfasePOS {

	private $db;
	private $transaction;
	private $movihd = array();
	private $movilind = array();
	private $grab = array();
	private $inve = array();
	private $recetap = array();
	private $reg1c = array();
	private $reg2 = array();
	private $contador = 0;
	private $opcion = 1;
	private $ramocol = "ramocol";
	private $fecha = "";
	private $hora = "";
	private $existe_receta = false;
	private $existe_inve = false;
	private $existe_saldosd = false;
	private $exito = false;
	private $log;

	public function __construct(){
		$config = CoreConfig::readFromActiveApplication("app.ini", 'ini');
		if(isset($config->ramocol)){
			$this->ramocol = $config->ramocol;
		}
		$walmacen = 1;
		$this->transaction = TransactionManager::getUserTransaction();
		$this->db = $this->transaction->getConnection();
		$num = $this->db->fetchOne("SELECT COUNT(*) FROM {$this->ramocol}.interpos WHERE estado = ' '");
		if(!$num[0]){
			return;
		}
		$datetime = $this->db->fetchOne("SELECT current_date AS fecha, current_time AS hora");
		$this->fecha = $datetime['fecha'];
		$this->hora = $datetime['hora'];
		$this->movihd = array();
		$max = $this->db->fetchOne("SELECT MAX(dependencia) FROM {$this->ramocol}.interpos WHERE estado = ' '");
		if(!$max[0]){
			$this->logger("No hay almacenes para cargar");
		} else {
			$walmacen  = $max[0];
			$fecha = $this->db->fetchOne("SELECT f_cierrep + INTERVAL 1 DAY FROM {$this->ramocol}.empresa1 LIMIT 1");
			$this->movihd['fecha'] = $fecha[0];
			if(!$this->movihd['fecha']){
				$this->opcion = 0;
				$this->logger("Fecha de cierre de venta es nula (empresa1)");
			} else {
				$almacen = $this->db->fetchOne("SELECT * FROM {$this->ramocol}.almacenes WHERE codigo = '$walmacen'");
				if($almacen){
					$this->empresa = $this->db->fetchOne("SELECT * FROM {$this->ramocol}.empresa LIMIT 1");
					$alm4c = sprintf("%4s", $this->empresa['centro_costo']);
					$alm4c = substr($alm4c, 0, 2).sprintf("%02s", $walmacen);
					$num = $this->db->fetchOne("SELECT COuNT(*) FROM {$this->ramocol}.centros WHERE codigo = '$alm4c'");
					if(!$num[0]){
						$this->logger("PUNTO DE OPERACION NO EXISTE/CENTRO DE COSTO $alm4c");
					} else {
						$numero_comprob = "P".sprintf("%02s", $walmacen);
						$comprob = $this->db->fetchOne("SELECT * FROM {$this->ramocol}.comprob WHERE codigo = '{$numero_comprob}'");
						if(!$comprob){
							$this->logger("NO SE HA CREADO el comprobante '$numero_comprob'");
						} else {
							$this->factura = "F".sprintf("%02s", $walmacen);
							$this->movihd['ubica'] = sprintf("%02s", $walmacen);
							$contador = 0;
							$this->mostrarPedido();
							$consec = $this->db->fetchOne("SELECT consecutivo FROM {$this->ramocol}.comprob WHERE codigo = '$this->factura'");
							if(!$consec){
								$numerof = 0;
							} else {
								$numerof = $consec[0];
							}
							$numerof = $numerof + 1;
							$this->totalizarPedido();
							$this->actualizarConsecutivoF();
						}
					}
				} else {
					$this->logger("No existe almacen '$walmacen'");
				}
			}
		}
	}

	private function mostrarPedido(){
		$i = 0;
		$this->existe_receta = false;
		$existe_saldosd = false;
		$cursor = $this->db->query("SELECT * FROM {$this->ramocol}.interpos WHERE estado = ' '");
		while($interpos = $this->db->fetchArray($cursor)){
			#print $interpos['codigo'];
			$i++;
			$wdep = sprintf("%4s", $this->empresa['centro_costo']);
			$wdep = substr($wdep, 0, 2).sprintf("%02s", $interpos['dependencia']);
			if($interpos['tipopro']=='I'){
				$sql = "SELECT * FROM {$this->ramocol}.saldosd WHERE item = '{$interpos['codigo']}'
					AND centro_costo = '$wdep' AND dependen = 1 AND ano_mes = 0";
				$this->saldosd = $this->db->fetchOne($sql);
				if(!$this->saldosd){
					$this->saldosd['codigo'] = $interpos['codigo'];
					$this->saldosd['centro_costo'] = $wdep;
					$this->saldosd['dependen'] = 1;
					$this->saldosd['ano_mes'] = 0;
					$existe_saldosd = false;
					$this->saldosd['saldot'] = 0;
					$this->saldosd['saldo'] = 0;
				} else {
					$existe_saldosd = true;
				}
				$sql = "SELECT * FROM {$this->ramocol}.inve WHERE item = '{$interpos['codigo']}'";
				$this->inve = $this->db->fetchOne($sql);
				if(!$this->inve){
					$this->inve['volumen'] = 0;
				}
			} else {
				$interpos['cantidadu'] = 0;
				$sql = "SELECT * FROM {$this->ramocol}.recetap WHERE almacen = 1 AND numero_rec = '{$interpos['codigo']}'";
				$this->recetap = $this->db->fetchOne($sql);
				if($this->recetap){
					$this->existe_receta = true;
				} else {
					$this->existe_receta = false;
				}
			}
			if($interpos['cantidadu']>0){
				if($interpos['cantidadu']>$this->saldosd['saldot']&&$this->inve['volumen']>0){
					$this->saldosd['saldo']--;
					$this->saldosd['saldot'] = $this->saldosd['saldot'] + $this->inve['volumen'];
					if($existe_saldosd){
						$sql = "UPDATE {$this->ramocol}.saldosd SET saldo = {$this->saldosd['saldo']}, saldot = {$this->saldosd['saldot']}
						WHERE item = '{$interpos['codigo']}' AND
						centro_costo = '$wdep' AND dependen = 1 AND ano_mes = 0";
						$this->db->query($sql);
					} else {
						$this->saldosd['item'] = $interpos['codigo'];
						$this->saldosd['centro_costo'] = $wdep;
						$this->saldosd['dependen'] = 1;
						$this->saldosd['ano_mes'] = 0;
						if($this->inve['saldo_actual']>0&&$this->inve['costo_actual']>0){
							$this->saldosd['costo'] = ($this->inve['costo_actual']/$this->inve['saldo_actual'])*$this->saldosd['saldo']+(($this->inve['costo_actual']/$this->inve['saldo_actual'])/$this->inve['volumen'])*$this->saldosd['saldot'];
						} else {
							$sql = "SELECT fecha, cantidad, valor FROM {$this->ramocol}.movilin
							WHERE comprob = 'E01' AND item = '{$this->inve['item']}' ORDER BY fecha DESC";
							$cursor = $this->db->query($sql);
							while($movilin = $this->db->fetchArray($cursor)){
								$wfecha = $movilin['fecha'];
								$wvalor = $movilin['cantidad'];
								$wcosto = $movilin['valor'];
							}
							if($wsaldo>0){
								$this->saldosd['costo'] = ($wcosto/$wsaldo)*$this->saldosd['saldo']+(($wcosto/$wsaldo)/$this->inve['volumen']*$this->saldosd['saldot']);
							} else {
								$this->saldosd['costo'] = 0;
							}
						}
						$sql = "INSERT INTO {$this->ramocol}.saldosd (item, centro_costo, dependen, ano_mes, saldot, saldo, costo)
						VALUES ('{$this->saldosd['item']}', '{$this->saldosd['centro_costo']}', '{$this->saldosd['dependen']}',
						'{$this->saldosd['ano_mes']}', '{$this->saldosd['saldot']}', '{$this->saldosd['saldo']}', '{$this->saldosd['costo']}')";
						$this->db->query($sql);
					}
				}
			}
			$this->movilind['comprob'] = "F".substr($wdep, 0, 2);
			$this->movilind['numero'] = $interpos['numfac'];
			$this->movilind['num_linea'] = $i;
			$this->movilind['fecha'] = $interpos['fecha'];
			$this->movilind['item'] = $interpos['codigo'];
			$this->movilind['centro_costo'] = $wdep;
			$this->movilind['dependen'] = 1;
			if($i==1){
				$this->movihd['comprob'] = $this->movilind['comprob'];
				$this->movihd['numero'] = $this->movilind['numero'];
				$this->movihd['fecha'] = $this->movilind['fecha'];
				$this->movihd['n_pedido'] = 0;
				$this->movihd['ubica'] = substr($wdep, 2, 2);
				$this->movihd['n_mesa'] = 1;
				$this->movihd['n_habita'] = $interpos['n_habita'];
				$this->movihd['c_cajero'] = $interpos['c_cajero'];
				$this->movihd['c_mesero'] = $interpos['c_cajero'];
				$this->movihd['hora'] = $this->hora;
				$this->movihd['nit'] = $interpos['nit'];
				$this->movihd['forma_pago'] = $interpos['forma_pago'];
			}
			$this->movilind['n_comanda'] = $interpos['n_comanda'];
			$this->movilind['cantidad'] = $interpos['cantidad'];
			$this->movilind['cantidad_rec'] = 0;
			$this->movilind['cantidadt'] = $interpos['cantidadu'];
			$this->movilind['cantidadt_rec'] = 0;
			$this->movilind['valor'] = $interpos['valorv'];
			if($interpos['tipopro']=='I'){
				if($this->inve['volumen']>0){
					$wsaldo = $this->saldosd['saldot']/$this->inve['volumen'];
					$wsaldo = $saldo + $this->saldosd['saldo'];
					$wcosto = $this->saldosd['costo']/$wsaldo;
					$wcostot = $wcosto/$this->inve['volumen'];
				} else {
					$wcostot = 0;
					if($this->saldosd['saldo']>0){
						$wcosto = $this->saldosd['costo']/$this->saldosd['saldo'];
					} else {
						$wcosto = 0;
					}
				}
				$this->movilind['costo'] = ($wcosto*$interpos['cantidad'])+($wcostot+$interpos['cantidadu']);
			} else {
				if($this->existe_receta){
					$this->movilind['costo'] = ($this->recetap['precio_costo']/$this->recetap['num_personas'])*$interpos['cantidad'];
				} else {
					$this->movilind['costo'] = 0;
				}
			}
			$this->movilind['porcdesc'] = 0;
			$this->movilind['centro_destino'] = $wdep;
			$this->movilind['dependendes'] = 1;
			$this->movilind['nota'] = $interpos['estado'];
			$this->movilind['prioridad'] = 5;
			$this->movilind['iva'] = 16;
			$this->contador++;
			$this->reg1c[$i]['secuen'] = $i;
			$this->reg1c[$i]['item'] = $this->movilind['item'];
			$this->inve['item'] = $this->movilind['item'];
			$this->leerInve();
			if($this->existe_receta){
				$this->reg1c[$i]['descripcion'] = $this->recetap['nombre'];
			} else {
				$this->reg1c[$i]['descripcion'] = $this->inve['descripcion'];
			}
			if(!isset($this->movilind['cantidadt'])){
				$this->movilind['cantidadt'] = 0;
			}
			if($this->movilind['cantidadt']>0){
				$this->reg1c[$i]['nom_unidad'] = "TRA";
			} else {
				if(isset($this->inve['unidad'])){
					$unidad = $this->db->fetchOne("SELECT nom_unidad FROM {$this->ramocol}.unidad WHERE codigo = '{$this->inve['unidad']}'");
					if(!$unidad){
						$this->reg1c[$i]['nom_unidad'] = "???";
					} else {
						$this->reg1c[$i]['nom_unidad'] = $unidad[0];
					}
				} else {
					$this->reg1c[$i]['nom_unidad'] = "???";
				}
			}
			$this->reg1c[$i]['cantidad'] = $this->movilind['cantidad'];
			$this->reg1c[$i]['cantidadt'] = $this->movilind['cantidadt'];
			$this->reg1c[$i]['valor'] = $this->movilind['valor'];
			$this->reg1c[$i]['porcdesc'] = $this->movilind['porcdesc'];
			$this->reg2[$i]['centro_costo'] = $this->movilind['centro_costo'];
			$this->reg2[$i]['dependen'] = $this->movilind['dependen'];
			$this->reg2[$i]['n_comanda'] = $this->movilind['n_comanda'];
			$this->reg2[$i]['cantidad_rec'] = $this->movilind['cantidad_rec'] ? $this->movilind['cantidad_rec'] : 0;
			$this->reg2[$i]['cantidadt_rec'] = $this->movilind['cantidadt_rec'] ? $this->movilind['cantidadt_rec'] : 0;
			$this->reg2[$i]['costo'] = $this->movilind['costo'];
			$this->reg2[$i]['linea'] = isset($this->movilind['linea']) ? $this->movilind['linea'] : "";
			$this->reg2[$i]['iva'] = $this->movilind['iva'];
		}
		if($i>0){
			//Cajero?
		}
	}

	public function totalizarPedido(){
		$this->movihd['v_total'] = 0;
		$this->movihd['iva'] = 0;
		$this->movihd['ivad'] = 0;
		for($i=1;$i<=$this->contador;$i++){
			if($this->reg1c[$i]['item']&&($this->reg1c[$i]['cantidadt']>0||$this->reg1c[$i]['cantidad']>0)){
				if(!$this->reg1c[$i]['valor']){
					$this->reg1c[$i]['valor'] = 0;
				}
				if(isset($this->reg1c[$i]['iva'])&&$this->reg1c[$i]['iva']>0){
					$wiva = $this->reg1c[$i]['iva']/100;
					$wiva++;
					$debitos = $this->reg1c[$i]['valor'] - ($this->reg1c[$i]['valor']/$wiva);
					$w_valor2 = LocaleMath::round($debitos, 0);
					$debitos = $w_valor2[0];
					if($this->reg1c[$i]['iva']==10){
						 $this->reg1c[$i]['ivad'] =  $this->reg1c[$i]['ivad'] + $debitos;
					} else {
						$this->reg1c[$i]['iva'] =  $this->reg1c[$i]['iva'] + $debitos;
					}
				} else {
					$debitos = 0;
				}
				$this->movihd['v_total'] = $this->movihd['v_total']+($this->reg1c[$i]['valor']-$debitos);
			}
		}
		if(!isset($this->movihd['iva'])){
			$this->movihd['iva'] = 0;
		} else {
			$w_valor2 = LocaleMath::round($this->movihd['iva'], 0);
			$this->movihd['iva'] = $w_valor2[0];
		}
		if(!isset($this->movihd['ivad'])){
			$this->movihd['ivad'] = 0;
		} else {
			$w_valor2 = LocaleMath::round($this->movihd['ivad'], 0);
			$this->movihd['ivad'] = $w_valor2[0];
		}
		$this->movihd['saldo'] = $this->movihd['v_total'] + $this->movihd['iva'] + $this->movihd['ivad'];
	}

	private function actualizarConsecutivoF(){
		if($this->opcion==1){
			$this->db->begin();
			$this->leerConsecutivoF();
			if($this->exito==false){
				$this->transaction->rollback();
			}
		}
	}

	private function leerConsecutivoF(){
		$this->exito = false;
		$this->movihd['comprob'] = $this->factura;
		$numeroi = $this->movihd['numero'];
		$this->movihd['n_pedido'] = $this->movihd['numero'];
		$sql = "SELECT consecutivo, comprob_contab FROM {$this->ramocol}.comprob
			WHERE codigo = '{$this->factura}'";
		$comprob = $this->db->fetchOne($sql);
		if(!$comprob){
			$this->exito = false;
			$this->logger("No existe comprobante '{$this->factura}'");
		} else {
			$this->movihd['numero'] = $comprob['consecutivo'];
			$venta = $comprob['comprob_contab'];
			$this->movihd['numero']++;
			$sql = "UPDATE {$this->ramocol}.comprob SET consecutivo = '{$this->movihd['numero']}' WHERE codigo = '{$this->factura}'";
			$this->db->query($sql);
			$this->exito = true;
			$this->leerGrabarMoviF();
		}
	}

	private function leerGrabarMoviF(){
		$this->movihd['forma_pago'] = 1;
		$this->movihd['hora'] = $this->hora;
		$this->movihd['vendedor'] = 0;
		$this->movihd['factura_c'] = 0;
		$this->movihd['nota'] = "N";
		$this->movilind['comprob'] = $this->movihd['comprob'];
		$this->movilind['numero'] = $this->movihd['numero'];
		$this->movilind['fecha'] = $this->movihd['fecha'];
		$this->movilind['prioridad'] = 5;
		$this->grab['comprob'] = $this->movihd['comprob'];
		$this->grab['numero'] = $this->movihd['numero'];
		$this->grab['accion'] = "A";
		$this->grab['fecha_grab'] = $this->fecha;
		$this->grab['hora_grab'] = $this->fecha;
		$this->grab['codigo_grab'] = "informix";
		$this->grabarGrab();
		if($this->exito==true){
			$ln = 1;
			for($i=1;$i<=384;$i++){
				if(isset($this->reg1c[$i])){
					$this->movilind['num_linea'] = $ln;
					$ln++;
					$this->movilind['centro_costo'] = $this->reg2[$i]['centro_costo'];
					$this->movilind['dependen'] = $this->reg2[$i]['dependen'];
					$this->movilind['n_comanda'] = $this->reg2[$i]['n_comanda'];
					$this->movilind['porcdesc'] = $this->reg1c[$i]['porcdesc'];
					$this->movilind['iva'] = $this->reg2[$i]['iva'];
					$this->recetap['almacen'] = 1;
					$this->recetap['numero_rec'] = $this->reg1c[$i]['item'];
					$this->leerReceta();
					if($this->existe_receta){
						$sql = "SELECT * FROM {$this->ramocol}.recetal WHERE almacen = '{$this->recetap['almacen']}' AND numero_rec = '{$this->recetap['numero_rec']}'";
						$cursor = $this->db->query($sql);
						while($recetal = $this->db->fetchArray($cursor)){
							if($recetal['item']<7000){
								$this->inve['item'] = $recetal['item'];
								$this->movilind['nota'] = "R";
								$this->movilind['item'] = $recetal['item'];
								$this->leerInve();
								if($this->inve['volumen']>0&&$recetal['divisor']>1){
									$this->movilind['cantidadt'] = $recetal['cantidad']*$this->reg1c[$i]['cantidad'];
									$this->movilind['cantidadt_rec'] = $this->movilind['cantidadt'];
									$this->movilind['cantidad'] = 0;
									$this->movilind['cantidad_rec'] = 0;
								} else {
									$this->movilind['cantidad'] = (($recetal['cantidad']/$this->recetap['num_personas'])*$this->reg1c[$i]['cantidad'])/$recetal['divisor'];
									$this->movilind['cantidad_rec'] = $this->movilind['cantidad'];
									$this->movilind['cantidadt'] = 0;
									$this->movilind['cantidadt_rec'] = 0;
								}
								$this->movilind['costo'] = ($recetal['valor']*$recetal['cantidad'])/$this->recetap['num_personas'];
								if($this->recetap['porc_costo']!=0){
									$this->movilind['valor'] = ($this->movilind['costo']/$this->recetap['porc_costo']*100);
								} else {
									$this->movilind['valor'] = 0;
								}
								$this->actualizarSaldosd($i);
								$this->grabarMovilind();
							} else {
								$n_copias = $recetal['cantidad'];
								$sql = "SELECT * FROM {$this->ramocol}.recetap WHERE almacen = '{$recetal['almacen']}' AND
									numero_rec = '{$recetal['item']}'";
								$recetap = $this->db->fetchOne($sql);
								$sql = "SELECT * FROM {$this->ramocol}.recetal WHERE almacen = '{$recetal['almacen']}' AND
									numero_rec = '{$recetal['item']}'";
								$cursor = $this->db->query($sql);
								while($recetal = $this->db->fetchArray($cursor)){
									if($recetal['item']<7000){
										$this->inve['item'] = $recetal['item'];
										$this->movilind['nota'] = "R";
										$this->movilind['item'] = $recetal['item'];
										$this->leerInve();
										if($this->inve['volumen']>0&&$recetal['divisor']>1){
											$this->movilind['cantidadt'] = $recetal['cantidad']*$this->reg1c[$i]['cantidad'];
											$this->movilind['cantidadt_rec'] = $this->movilind['cantidadt'];
											$this->movilind['cantidad'] = 0;
											$this->movilind['cantidad_rec'] = 0;
										} else {
											$this->movilind['cantidad'] = (($recetal['cantidad']/$this->recetap['num_personas'])*$this->reg1c['cantidad'])/$recetal['divisor'];
											$this->movilind['cantidad_rec'] = $this->movilind['cantidad'];
											$this->movilind['cantidadt'] = 0;
											$this->movilind['cantidadt_rec'] = 0;
										}
										$this->movilind['costo'] = ($recetal['valor']*$recetal['cantidad'])/$this->recetap['num_personas'];
										$this->movilind['valor'] = ($this->movilind['costo']/$this->recetap['porc_costo']*100);
										$this->actualizarSaldosd($i);
										$this->grabarMovilind();
									}
								}
								unset($cursor);
							}
						}
					} else {
						$this->movilind['nota'] = "I";
						$this->movilind['item'] = $this->reg1c[$i]['item'];
						$this->inve['item'] = $this->reg1c[$i]['item'];
						$this->leerInve();
						$this->movilind['cantidad'] = $this->reg1c[$i]['cantidad'];
						$this->movilind['cantidadt'] = $this->reg1c[$i]['cantidadt'];
						$this->movilind['cantidad_rec'] = $this->movilind['cantidad_rec'];
						$this->movilind['cantidadt_rec'] = $this->movilind['cantidadt_rec'];
						$this->movilind['valor'] = $this->reg1c[$i]['valor'];
						$this->movilind['costo'] = $this->reg2[$i]['costo'];
						$this->actualizarSaldosd($i);
						$this->grabarMovilind();
					}
					if($this->exito==false){
						break;
					}
				}
			}
		}
		if($this->exito==true){
			$this->grabarMovihd();
			$this->actualizaInterpos();
		}
	}

	private function actualizarSaldosd($i){
		$this->saldosd['item'] = $this->movilind['item'];
		$this->saldosd['dependen'] = $this->movilind['dependen'];
		/*$alm5c1 = sprintf("%04s", $this->reg2[$i]['centro_costo']);
		$alm5c1 = substr($alm5c1, 2, 2);
		if($alm5c1=="12"||$alm5c1=="14"||$alm5c1=="16"){
			if(substr($this->inve['linea'], 0, 2)=="01"){
				$alm5c1 = sprintf("%04s", $this->empresa['centro_costo']);
				$alm5c1 = substr($this->inve['linea'], 0, 2)."12";
			} else {
				if(substr($this->inve['linea'], 0, 2)=="02"){
					$alm5c1 = sprintf("%04s", $this->empresa['centro_costo']);
					$alm5c1 = substr($this->inve['linea'], 0, 2)."14";
				} else {
					if(substr($this->inve['linea'], 0, 2)=="03"){
						$alm5c1 = sprintf("%04s", $this->empresa['centro_costo']);
						$alm5c1 = substr($this->inve['linea'], 0, 2)."16";
					} else {
						$alm5c1 = $this->movilind['centro_costo'];
					}
				}
			}
		} else {
			$alm5c1 = $this->movilind['centro_costo'];
		}*/
		$alm5c1 = $this->movilind['centro_costo'];
		$this->saldosd['centro_costo'] = $alm5c1;
		$this->saldosd['ano_mes'] = 0;
		$this->leerSaldosd();
		$this->actualizaSaldosd();
		if($this->existe_saldosd==false){
			$this->grabarSaldosd();
		} else {
			$this->regrabarSaldosd();
		}
	}

	private function actualizaSaldosd(){
		$this->saldosd['saldo'] = $this->saldosd['saldo'] - $this->movilind['cantidad'];
		$this->saldosd['saldot'] = $this->saldosd['saldot'] - $this->movilind['cantidadt'];
		if($this->saldosd['saldo']==0&&$this->saldosd['saldot']==0){
			$this->saldosd['costo']	= 0;
		} else {
			$this->saldosd['costo']	= $this->saldosd['costo'] - $this->movilind['costo'];
		}
	}

	private function leerInve(){
		$this->existe_inve = false;
		if(!isset($this->inve['item'])){
			$this->inve['item'] = '';
		}
		$sql = "SELECT * FROM {$this->ramocol}.inve WHERE item = '{$this->inve['item']}'";
		$inve = $this->db->fetchOne($sql);
		if(!$inve){
			$this->existe_inve = false;
			$this->recetap['almacen'] = 1;
			$this->recetap['numero_rec'] = $this->inve['item'];
			$this->leerReceta();
			if($this->existe_receta==false){
				$this->inve['descripcion'] = "No existe referencia o receta";
			}
			$this->inve = array();
		} else {
			$this->inve = $inve;
			if($this->inve['estado']=="I"){
				$this->existe_inve = false;
				$this->inve['descripcion'] = "Referencia Inactiva";
			}
		}
	}

	private function leerReceta(){
		$this->existe_receta = true;
		$sql = "SELECT * FROM {$this->ramocol}.recetap WHERE
			almacen = '{$this->recetap['almacen']}' AND
			numero_rec = '{$this->recetap['numero_rec']}'";
		$this->recetap = $this->db->fetchOne($sql);
		if(!$this->recetap){
			$this->existe_receta = false;
		}
	}

	private function leerSaldosd(){
		$this->existe_saldosd = true;
		$saldosdt = $this->saldosd;
		$sql = "SELECT * FROM {$this->ramocol}.saldosd WHERE item = '{$this->saldosd['item']}' AND
		centro_costo = '{$this->saldosd['centro_costo']}' AND
		dependen = '{$this->saldosd['dependen']}' AND
		ano_mes = 0";
		$this->saldosd = $this->db->fetchOne($sql);
		if($this->saldosd==false){
			$this->existe_saldosd = false;
			$this->saldosd['item'] = $saldosdt['item'];
			$this->saldosd['centro_costo'] = $saldosdt['centro_costo'];
			$this->saldosd['dependen'] = $saldosdt['dependen'];
			$this->saldosd['ano_mes'] = 0;
			$this->saldosd['saldot'] = 0;
			$this->saldosd['saldo'] = 0;
			$this->saldosd['costo'] = 0;
		}
	}

	private function grabarGrab(){
		$sql = "";
		$fields = array();
		$values = array();
		foreach($this->grab as $key => $value){
			$fields[] = $key;
			$values[] = "'$value'";
		}
		$sql = "INSERT INTO {$this->ramocol}.grab (".join(",", $fields).") VALUES (".join(",", $values).")";
		$this->db->query($sql);
	}

	private function grabarMovihd(){
		$sql = "";
		$fields = array();
		$values = array();
		foreach($this->movihd as $key => $value){
			$fields[] = $key;
			$values[] = "'$value'";
		}
		$sql = "INSERT INTO {$this->ramocol}.movihd (".join(",", $fields).") VALUES (".join(",", $values).")";
		$this->db->query($sql);
	}

	private function grabarMovilind(){
		$sql = "";
		$fields = array();
		$values = array();
		foreach($this->movilind as $key => $value){
			$fields[] = $key;
			$values[] = "'$value'";
		}
		$sql = "INSERT INTO {$this->ramocol}.movilind (".join(",", $fields).") VALUES (".join(",", $values).")";
		$this->db->query($sql);
	}

	private function grabarSaldosd(){
		$sql = "";
		$fields = array();
		$values = array();
		foreach($this->saldosd as $key => $value){
			$fields[] = $key;
			$values[] = "'$value'";
		}
		$sql = "INSERT INTO {$this->ramocol}.saldosd (".join(",", $fields).") VALUES (".join(",", $values).")";
		$this->db->query($sql);
	}

	private function regrabarSaldosd(){
		$sql = "";
		$fields = array();
		$values = array();
		foreach($this->saldosd as $key => $value){
			if(!is_numeric($key)){
				$values[] = "$key = '$value'";
			}
		}
		$sql = "UPDATE {$this->ramocol}.saldosd SET ".join(",", $values)." WHERE
			item = '{$this->saldosd['item']}' AND
			centro_costo = '{$this->saldosd['centro_costo']}' AND
			dependen = '{$this->saldosd['dependen']}' AND
			ano_mes = 0";
		$this->db->query($sql);
	}

	private function actualizaInterpos(){
		$sql = "UPDATE {$this->ramocol}.interpos SET estado = 'D' WHERE estado = ' '";
		$this->db->query($sql);
	}

	private function logger($msg){
		print $msg;
		if(!$this->log){
			$this->log = new Logger("File", "interpos.txt");
		}
		$this->log->log($msg, Logger::ERROR);
	}

}

