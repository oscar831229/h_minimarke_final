<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class SpoolController extends ApplicationController
{
	private function _changeStatus($account)
	{
		$account->send_kitchen = 'S';
		$account->cantidad_cocina = $account->cantidad;
		if ($account->save() == false) {
			foreach ($account->getMessages() as $message) {
				$transaction->rollback($message->getMessage());
			}
		}
	}

	public function getAction()
	{
		$this->setResponse("view");
		try {

			$transaction = TransactionManager::getUserTransaction();

			$prints = array();
			$this->Account->setTransaction($transaction);
			if ($this->Account->count("send_kitchen='N'")) {

				$imp = array();
				$replaceWords = array(' DE ', ' CON ', ' EL ', ' LOS ', ' A ', ' LA ');

				$comandas = array();
				$accounts = $this->Account->findForUpdate("send_kitchen='N'");
				foreach($accounts as $account){
					$comandas[$account->comanda] = true;
				}

				$comandas = array_keys($comandas);
				foreach ($accounts as $account) {

					$accountMaster = $this->AccountMaster->findFirst($account->account_master_id);
					if ($accountMaster == false) {
						$transaction->rollback('No existe el pedido');
					}

					$usuario = $this->UsuariosPos->findFirst($accountMaster->usuarios_id);
					if ($usuario == false) {
						$transaction->rollback('No existe el usuario que tomó el pedido');
					}

					$clienteNombre = '';
					$accountCuenta = $this->AccountCuentas->findFirst("account_master_id={$account->account_master_id} AND estado = 'A'");
					if ($accountCuenta) {
						$clienteNombre = "Cliente: " . $accountCuenta->clientes_nombre . "\n";
					}

					$salonMesa = $account->getSalonMesas();
					if ($salonMesa == false) {
						$this->_changeStatus($account);
						continue;
					}

					$salon = $accountMaster->getSalon();
					if ($salon == false) {
						$this->_changeStatus($account);
						continue;
					}

					$menuItem = $account->getMenusItems();
					if ($menuItem==false) {
						$this->_changeStatus($account);
						continue;
					}

					$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='{$salon->id}' AND menus_items_id='{$account->menus_items_id}'");
					if ($salonMenusItems == false) {
						$this->_changeStatus($account);
						continue;
					}

					if ($account->estado == 'S') {
						$estado = 'SIN ATENDER';
					} else {
						if ($account->estado == 'A') {
							$estado = 'ATENDIDA';
						} else {
							$estado = 'CANCELADA';
						}
					}

					$cantidadSinEnviar = $account->cantidad-$account->cantidad_cocina;

					$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='{$salon->id}' AND menus_items_id='{$account->menus_items_id}'");
					if ($salonMenusItems==false) {
						$this->_changeStatus($account);
						continue;
					}

					$nombre = str_ireplace($replaceWords, ' ', $menuItem->nombre);
					$salonNombre = '';
					$salonNombreP = explode(' ', $salon->nombre);
					foreach ($salonNombreP as $part) {
						$salonNombre .= substr($part, 0, 7) . ' ';
					}

					$mesero = substr($usuario->nombre, 0, 10);
					if (!isset($imp[$salonMenusItems->printers_id])) {
						$imp[$salonMenusItems->printers_id] = array();
					}
					if (!isset($imp[$salonMenusItems->printers_id]['P'])) {
						$imp[$salonMenusItems->printers_id]['P'] = array();
					}
					if (!isset($imp[$salonMenusItems->printers_id]['C'])) {
						$imp[$salonMenusItems->printers_id]['C'] = array();
					}

					$printer = $this->Printers->findFirst($salonMenusItems->printers_id);
					if ($printer != false) {
						$impresora = $printer->nombre;
					} else {
						$impresora = 'NO EXISTE LA IMPRESORA ' . $salonMenusItems->printers_id;
					}

					$modifierData = '';
					foreach ($account->getAccountModifiers() as $accountModifier) {
						$modifier = $this->Modifiers->find($accountModifier->modifiers_id);
						$modifierData .= " > " . $modifier->nombre . "\n";
					}
					if ($modifierData != "") {
						$modifierData.= "\n";
					}
					if ($salonMenusItems->printers_id != $salonMenusItems->printers_id2) {
						if ($account->note) {
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$this->SalonMesas->numero][] = $clienteNombre . "[P] A: $salonNombre  M: {$salonMesa->numero} A:{$account->asiento}\n{$account->comanda}:{$nombre} C: $cantidadSinEnviar\n$modifierData\nNota: {$account->note}";
							$imp[$salonMenusItems->printers_id2]['C'][$account->estado][$this->SalonMesas->numero][] = $clienteNombre . "[C] A: $salonNombre  M: {$salonMesa->numero} A:{$account->asiento}\n{$account->comanda}:{$nombre} C: $cantidadSinEnviar P: $impresora\n$modifierData\nNota: {$account->note}";
						} else {
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$this->SalonMesas->numero][] = $clienteNombre . "[P] A: $salonNombre  M: {$salonMesa->numero} A:{$account->asiento}\n{$account->comanda}:{$nombre} C: $cantidadSinEnviar\n$modifierData";
							$imp[$salonMenusItems->printers_id2]['C'][$account->estado][$this->SalonMesas->numero][] = $clienteNombre . "[C] A: $salonNombre  M: {$salonMesa->numero} A:{$account->asiento}\n{$account->comanda}:{$nombre} C: $cantidadSinEnviar P: $impresora\n$modifierData";
						}
					} else {
						if ($account->note) {
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$this->SalonMesas->numero][] = $clienteNombre . "[P] A: $salonNombre  M: {$salonMesa->numero} A:{$account->asiento}\n{$account->comanda}:{$nombre} C: $cantidadSinEnviar\n$modifierData";
						} else {
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$this->SalonMesas->numero][] = $clienteNombre . "[P] A: $salonNombre  M: {$salonMesa->numero} A:{$account->asiento}\n{$account->comanda}:{$nombre} C: $cantidadSinEnviar\n$modifierData\nNota: {$account->note}";
						}
					}

					$this->_changeStatus($account);
				}

				$datos = $this->Datos->findFirst();

				foreach ($imp as $printer => $imt) {

					$printer = $this->Printers->findFirst($printer);
					if (!$printer) {
						continue;
					}

					$data = "======================================\n";
					$data.= $datos->getNombreHotel() . "\n";
					$data.= "F: " . Date::now() . " I: ";
					$data.= $printer->nombre . "\nU: {$accountMaster->nombre}\n";

					$tm = 0;
					$kk = 0;
					$jj = count($imt);
					foreach ($imt as $type => $ime) {

						$j = count($ime);

						if ($j > 0) {
							if ($kk != $jj) {
								if ($type == 'P') {
									$data .= "--------- P R O D U C C I O N --------\n";
								} else {
									$data .= "------- C O N F I R M A C I O N ------\n";
								}
							}
						}

						$k = 0;
						if ($j > 0) {
							foreach ($ime as $estado => $imm) {
								if ($estado == 'A') {
									$data .= "+++++++++++ A T E N D I D O ++++++++++\n";
								}
								if ($estado == 'S') {
									$data .= "+++++++++ S I N  A T E N D E R +++++++\n";
								}
								if ($estado == 'C') {
									$data .= "+++++++++ C A N C E L A D O S ++++++++\n";
								}
								foreach ($imm as $nmesa => $im) {
									foreach ($im as $line) {
										$data .= $line."\n";
										$tm++;
									}
									$k++;
									if ($k != $j) {
										$data.= "- - - - - - - - - - - - - - - - - - - \n";
									}
								}
							}
						}
						$kk++;
					}

					if ($tm < 15) {
						for ($i = $tm; $i <= 9; $i++) {
							$data .= "\n";
						}
					}
					$data .= "======================================\n";
					$data .= "\n";

					$prints[] = array(
						'printer' => $printer->ubicacion,
						'data'    => $data,
						'uniqid'  => uniqid("", true)
					);

				}
			}

			echo json_encode($prints);

			$transaction->commit();
		} catch (TransactionFailed $e) {
			Flash::error($e->getMessage());
		}
	}

}