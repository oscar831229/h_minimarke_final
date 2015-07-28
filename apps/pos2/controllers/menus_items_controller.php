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

class Menus_ItemsController extends StandardForm
{

	public $scaffold = true;
	public $beforeInsert = 'traerCosto';
	public $beforeUpdate = 'traerCosto';
	public $afterUpdate = 'actualizarPrecio';

	public function actualizarPrecio($menuItem)
	{
		$salonms = $this->SalonMenusItems->find("menus_items_id='{$menuItem->id}'");
		foreach($salonms as $salonm){
			if (!$salonm->valor) {
				$salon = $this->Salon->findFirst($salonm->salon_id);
				if ($salon != false) {
					Flash::notice("El ambiente '{$salon->nombre}' no tiene definido el precio de venta para este producto, se utilizará el precio base");
				}
			} else {
				if ($salonm->valor != 0) {
					if ($salonm->valor > $menuItem->valor) {
						$salon = $this->Salon->findFirst($salonm->salon_id);
						Flash::notice("El precio base definido es menor al del item en el ambiente '{$salon->nombre}'");
					}
					if ($salonm->valor < $menuItem->valor) {
						$salon = $this->Salon->findFirst($salonm->salon_id);
						Flash::notice("El precio base definido es mayor al del item en el ambiente '{$salon->nombre}'");
					}
				}
			}
		}
	}

	public function traerCosto($menuItem)
	{
		$menuItem->porcentaje_servicio = 0;
		if ($menuItem->tipo_costo != "N" && $menuItem->codigo_referencia != '@') {
			$costo = new CostoInventario();
			$costo->setVerbose(true);
			if ($menuItem->porcentaje_iva > 0) {
				$precioVenta = $menuItem->valor / (1 + ($menuItem->porcentaje_iva / 100));
			} else {
				$precioVenta = $menuItem->valor / (1 + ($menuItem->porcentaje_impoconsumo / 100));
			}
			$menuItem->costo = $costo->obtenerCosto($menuItem->tipo_costo, $menuItem->nombre,
				$menuItem->codigo_referencia, $menuItem->descontar, $precioVenta);
		}
	}

	public function actualizaCostosAction()
	{
		$costo = new CostoInventario();
		$costo->setVerbose(true);
		$success = 0;
		try {
			set_time_limit(0);
			$transaction = TransactionManager::getUserTransaction();
			$this->MenusItems->setTransaction($transaction);
			foreach ($this->MenusItems->findWithSharedLock(array("estado='A'", "order" => "nombre")) as $menuitem) {
				if ($menuitem->tipo_costo != "N") {
					$changed = false;
					if ($menuitem->codigo_referencia){
						if ($menuitem->porcentaje_iva > 0) {
							$precioVenta = $menuitem->valor / (1 + ($menuitem->porcentaje_iva/100));
						} else {
							$precioVenta = $menuitem->valor / (1 + ($menuitem->porcentaje_impoconsumo/100));
						}
						$valorCosto = $costo->obtenerCosto($menuitem->tipo_costo, $menuitem->nombre, $menuitem->codigo_referencia, $menuitem->descontar, $precioVenta);
						$valorCosto = LocaleMath::round($valorCosto, 2);
						$menuCosto = LocaleMath::round($menuitem->costo, 2);
						if ($valorCosto != $menuCosto) {
							$changed = true;
							$menuitem->costo = $valorCosto;
						}
					} else {
						if ($menuitem->costo != 0) {
							$changed = true;
							$menuitem->costo = 0;
						}
					}
					if ($changed == true) {
						if ($menuitem->save() == false) {
							Flash::error('No se pudo actualizar el item de menú: "'.$menuitem->nombre.'", se generaron los siguientes mensajes:');
							foreach($menuitem->getMessages() as $message){
								Flash::error(' > '.$message->getMessage());
							}
						}
					}
				}
			}
			$transaction->commit();
			echo $costo->getResume();
		}
		catch(TransactionFailed $e){
			Flash::error($e->getMessage());
		}
		Flash::success('Terminó el proceso correctamente');
	}

	public function getDetalleAction()
	{
		$this->setResponse('json');

		$tipo = $this->getQueryParam('tipo_costo', 'onechar');
		$codigo = $this->getQueryParam('codigo', 'alpha');

		$referencia = false;
		if ($tipo == 'R') {
			$referencia = $this->Recetap->findFirst("almacen=1 AND numero_rec='$codigo'");
		} else {
			if ($tipo == 'I') {
				$referencia = $this->Inve->findFirst("item='$codigo'");
			}
		}

		if ($referencia == false) {
			if ($tipo == 'R') {
				return array(
					'status' => 'FAILED',
					'message' => 'NO EXISTE LA RECETA ESTÁNDAR'
				);
			} else {
				if ($tipo == 'I') {
					return array(
						'status' => 'FAILED',
						'message' => 'NO EXISTE LA REFERENCIA'
					);
				} else {
					return array(
						'status' => 'FAILED',
						'message' => ''
					);
				}
			}
		} else {
			if ($tipo == 'R') {
				return array(
					'status' => 'OK',
					'message' => $referencia->nombre
				);
			} else {
				return array(
					'status' => 'OK',
					'message' => $referencia->getDescripcion()
				);
			}
		}
	}

	public function queryReferenciasAction()
	{
		$this->setResponse('view');
		$tipo = $this->getPostParam('tipo_costo', 'onechar');
		$nombre = $this->getPostParam('nombre');
		$nombre = preg_replace('/[ ]+/', '%', $nombre);
		echo '<ul>';
		if ($tipo == 'R') {
			$recetas = $this->Recetap->find(array("almacen=1 AND nombre like '%$nombre%'", "order" => "nombre", "limit" => "10"));
			foreach ($recetas as $receta) {
				echo '<li id="', $receta->numero_rec, '">', utf8_encode($receta->nombre), '</li>';
			}
		} else {
			if ($tipo == 'I') {
				$referencias = $this->Inve->find(array("descripcion LIKE '%$nombre%' AND estado='A'", "order" => "descripcion", "limit" => "10"));
				foreach ($referencias as $referencia) {
					echo '<li id="', $referencia->getItem(), '">', utf8_encode($referencia->getDescripcion()).' : '.$referencia->getItem(), '</li>';
				}
			}
		}
		echo '</ul>';
	}

	public function initialize()
	{

		$this->setTemplateAfter('admin_menu');
		$this->setTitleImage('pos2/soft.png');

		$this->setFormCaption('Items de Menús');
		$this->setCaption('menus_id', 'Menu');
		$this->setCaption('cambio_precio', 'Permite Cambio Precio');
		$this->setCaption('valor', 'Precio Base');
		$this->setCaption('image', 'Imagen');
		//$this->setTypeImage('image');
		$this->setHidden('image', 'nombre_pedido');
		$this->notBrowse('descontar', 'cambio_precio', 'porcentaje_servicio', 'porcentaje_iva', 'porcentaje_impoconsumo', 'tipo');
		$this->notReport('image', 'descontar', 'cambio_precio', 'porcentaje_servicio', 'porcentaje_iva', 'porcentaje_impoconsumo');

		$this->setTextUpper('nombre');

		$this->setComboStatic('tipo', array(
			array('A', 'ALIMENTOS'),
			array('C', 'CIGARRILLOS'),
			array('B', 'BEBIDAS'),
			array('L', 'LAVANDERIA'),
			array('O', 'OTROS')
		));

		$this->setComboStatic('cambio_precio', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboStatic('tipo_costo', array(
			array('N', 'NO DESCARGA'),
			array('I', 'INVENTARIO/REFERENCIA'),
			array('R', 'RECETA ESTÁNDAR')
		));

		$this->setComboStatic('descontar', array(
			array('N', 'NORMAL'),
			array('T', 'TRAGO')
		));

		$this->setComboStatic('cubierto', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboStatic('estado', array(
			array('A', 'ACTIVO'),
			array('I', 'INACTIVO')
		));

		$this->setHelpContext('field: codigo_referencia');

	}

}
