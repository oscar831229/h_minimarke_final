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

/**
 * Ambientes_ItemsController
 *
 * Permite actualizar los items en los ambientes y sus valores
 */
class Ambientes_ItemsController extends ApplicationController
{

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
	}

	public function indexAction()
	{
		$this->loadModel('MenusItems');
	}

	public function getMenusItemsAction($menuItemId=0)
	{
		$this->setResponse('view');
		$this->loadModel('Printers', 'Conceptos', 'SalonMenusItems', 'Salon', 'Almacenes', 'Screens');
		$menuItemId = $this->filter($menuItemId, 'int');
		$this->setParamToView('menuItemId', $menuItemId);
		//sleep(10);
	}

	public function guardarAction($id)
	{
		if ($this->getRequestInstance()->isSetPostParam('ambientes')) {

			$id = $this->filter($id, 'int');
			foreach ($this->SalonMenusItems->find("menus_items_id='$id'") as $salonm) {
				$salonm->estado = 'I';
				if ($salonm->save() == false) {
					foreach ($salonm->getMessages() as $message) {
						Flash::error($message->getMessage());
					}
				}
			}

			$number = 0;
			foreach ($this->getPostParam("ambientes") as $ambiente) {

				$salonm = $this->SalonMenusItems->findFirst("salon_id='$ambiente' AND menus_items_id='$id'");
				if (!$salonm) {
					$salonm = new SalonMenusItems();
					$salonm->menus_items_id = $id;
					$salonm->salon_id = $ambiente;
				}

				$salonm->almacen = $this->getPostParam('almacen' . $ambiente, 'int');
				$salonm->valor = $this->getPostParam('precio' . $ambiente, 'float');
				$salonm->descarga = $this->getPostParam('descarga' . $ambiente, 'onechar');
				$salonm->conceptos_id = $this->getPostParam('concepto_recepcion' . $ambiente, 'int');
				$salonm->printers_id = $this->getPostParam('printers' . $ambiente, 'int');
				$salonm->printers_id2 = $this->getPostParam('printers2' . $ambiente, 'int');
				$salonm->screens_id = $this->getPostParam('screens' . $ambiente, 'int');
				$salonm->estado = 'A';
				if ($salonm->save() == false) {

					$salon = $this->Salon->findFirst($ambiente);
					if ($salon != false){
						Flash::error('Se generaron los siguientes errores al activar el item en el ambiente "' . $salon->nombre . '":');
					}

					foreach ($salonm->getMessages() as $message) {
						Flash::error(' > ' . $message->getMessage());
					}
				} else {
					$number++;
				}
			}
			if ($number > 0) {
				Flash::success('Se actualizaron correctamente el item en los ambientes');
			}
			$this->routeTo(array("action" => "index", "id" => $id));
		}
	}

}

