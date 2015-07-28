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
	private function _changeStatus($transaction, $account)
	{
		$account->setTransaction($transaction);
		$account->send_kitchen = 'S';
		$account->cantidad_cocina = $account->cantidad;
		if (!$account->tiempo_final) {
			$account->tiempo_final = Date::getCurrentTime();
		}
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
				$accounts = $this->Account->findForUpdate(array(
					"send_kitchen = 'N'",
					"order" => "account_master_id, asiento"
				));
				foreach ($accounts as $account) {
					$comandas[$account->comanda] = true;
				}

				$this->AccountMaster->setTransaction($transaction);

				$comandas = array_keys($comandas);
				foreach ($accounts as $account) {

					$accountMaster = $this->AccountMaster->findFirst($account->account_master_id);
					if ($accountMaster == false) {
						//$transaction->rollback('No existe el pedido');
						continue;
					}

					if ($accountMaster->send_kitchen != 'Y') {
						//$transaction->rollback('El pedido no esta listo para ser enviado a cocina');
						continue;
					}

					$usuario = $this->UsuariosPos->findFirst($accountMaster->usuarios_id);
					if ($usuario == false) {
						$transaction->rollback('No existe el usuario que tomó el pedido');
					}

					$clienteNombre = '';
					$accountCuenta = $this->AccountCuentas->findFirst("account_master_id={$account->account_master_id} AND estado = 'A'");
					if ($accountCuenta) {
						$clienteNombre = $accountCuenta->clientes_nombre;
					}

					$salonMesa = $account->getSalonMesas();
					if ($salonMesa == false) {
						$this->_changeStatus($transaction, $account);
						continue;
					}

					$salon = $accountMaster->getSalon();
					if ($salon == false) {
						$this->_changeStatus($transaction, $account);
						continue;
					}

					$menuItem = $account->getMenusItems();
					if ($menuItem == false) {
						$this->_changeStatus($transaction, $account);
						continue;
					}

					$menu = $menuItem->getMenus();
					if ($menu === false) {
						$this->_changeStatus($transaction, $account);
						continue;
					}

					$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='{$salon->id}' AND menus_items_id='{$account->menus_items_id}'");
					if ($salonMenusItems == false) {
						$this->_changeStatus($transaction, $account);
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

					$cantidadSinEnviar = $account->cantidad - $account->cantidad_cocina;

					$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='{$salon->id}' AND menus_items_id='{$account->menus_items_id}'");
					if ($salonMenusItems == false) {
						$this->_changeStatus($transaction, $account);
						continue;
					}

					$salonNombre = '';
					$nombre = str_ireplace($replaceWords, ' ', $menuItem->nombre);
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
						if ($modifier) {
							$modifierData .= " > " . $modifier->nombre . "\n";
						}
					}

					if ($modifierData != "") {
						$modifierData .= "\n";
					}

					if ($clienteNombre == 'SIN DEFINIR' || $clienteNombre == 'PARTICULAR' || $clienteNombre == ''|| $clienteNombre == 'CUANTIAS MENORES') {
						$clienteNombre = '';
					} else {
						$clienteNombre = 'Cliente: ' . $clienteNombre . "\n";
					}

					$note  = $clienteNombre . "Grupo: {$menu->nombre}\n";
					$note .= "Mesero: {$accountMaster->nombre}\nMesa: {$salonMesa->numero} Silla: {$account->asiento} Comanda: {$account->comanda} Cant: $cantidadSinEnviar\n";
					$note .= "{$nombre}\n$modifierData";

					if (trim($account->note)) {
						$note .= "Nota: {$account->note}";
					}

					if ($salonMenusItems->printers_id != $salonMenusItems->printers_id2) {
						if ($account->note) {
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$salonMesa->numero][$menu->id][] = $note;
							$imp[$salonMenusItems->printers_id2]['C'][$account->estado][$salonMesa->numero][$menu->id][] = $note;
						} else {
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$salonMesa->numero][$menu->id][] = $note;
							$imp[$salonMenusItems->printers_id2]['C'][$account->estado][$salonMesa->numero][$menu->id][] = $note;
						}
					} else {
						if ($account->note) {
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$salonMesa->numero][$menu->id][] = $note;
						} else {
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$salonMesa->numero][$menu->id][] = $note;
						}
					}

					$this->_changeStatus($transaction, $account);
				}

				$datos = $this->Datos->findFirst();

				foreach ($imp as $printer => $imt) {

					$printer = $this->Printers->findFirst($printer);
					if (!$printer) {
						continue;
					}

					$types = array();
					foreach ($imt as $type => $ime) {
						$types[$type] = null;
					}

					foreach ($imt as $type => $ime) {

						foreach ($ime as $estado => $imm) {

							foreach ($imm as $nmesa => $im) {

								$data = "Fecha: " . Date::now() . "\n";

								if ($type == 'P') {
									$data .= "--------- P R O D U C C I O N --------\n";
								} else {
									$data .= "------- C O N F I R M A C I O N ------\n";
								}

								if ($estado == 'A') {
									$data .= "+++++++++++ A T E N D I D O ++++++++++\n";
								} else {
									if ($estado == 'S') {
										$data .= "+++++++++ S I N  A T E N D E R +++++++\n";
									} else {
										if ($estado == 'C') {
											$data .= "--------- C A N C E L A D O S --------\n";
										}
									}
								}
								$data .= "\n";

								foreach ($im as $ngroup => $img) {
									foreach ($img as $line) {
										$data .= $line . "\n";
									}
								}

								$data .= "--------------------------------------\n";
								$data .= "\n\n\n";
								$data .= chr(0x1B) . chr(0x69);
							}
						}
					}

					$prints[] = array(
						'printer' => $printer->ubicacion,
						'data'    => $data,
						'uniqid'  => uniqid("", true)
					);

				}
			}

			echo json_encode($prints);

			//echo '<font face="Monaco">';
			//echo nl2br(print_r($prints, true));
			//echo '</font>';

			$transaction->commit();
		} catch (TransactionFailed $e) {
			Flash::error($e->getMessage());
		}
	}

}