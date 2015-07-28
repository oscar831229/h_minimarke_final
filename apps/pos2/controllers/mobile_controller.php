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

class MobileController extends ApplicationController
{

	public function initialize()
	{
		View::setRenderLevel(View::LEVEL_LAYOUT);
	}

	public function indexAction($id=0)
	{
		/*if($id==0){
			$this->redirect('mobile/index/'.substr(md5(time()), 0, 10));
		}*/
		$this->loadModel('Menus', 'Salon', 'MenusItems', 'SalonMesas', 'SalonMenusItems');
		#if(Browser::isMobile()==false){
		#	return $this->routeTo('controller: appmenu', 'action: index');
		#}
	}

	public function startSessionAction()
	{
		$this->setResponse('json');
		$clave = sha1($this->getPostParam('clave', 'int'));
		$usuario = $this->UsuariosPos->findFirst("clave = '$clave' AND estado='A'");
		Session::setData('usuarios_id', 0);
		if($usuario==false){
			return 0;
		} else {
			Session::set('usuarios_id', $this->UsuariosPos->id);
			Session::set('usuarios_nombre', $this->UsuariosPos->nombre);
			Session::set('auth', $this->UsuariosPos->perfil);
			Session::set('role', $this->UsuariosPos->perfil);
			return 1;
		}
	}

	public function testAction()
	{

	}

	public function getAmbientesAction()
	{
		$this->setResponse('json');
		$salones = array();
		foreach($this->Salon->find(array("order" => "id")) as $salon){
			$salones[] = array(
				$salon->id,
				$salon->nombre
			);
		}
		return $salones;
	}

	public function existsMesaAction($salonId, $numeroMesa)
	{
		$this->setResponse('json');

		$salonId = $this->filter($salonId, 'int');
		$numeroMesa = $this->filter($numeroMesa, 'int');

		$salonMesa = $this->SalonMesas->findFirst(array("salon_id=$salonId AND numero = $numeroMesa", "columns" => "id"));
		if($salonMesa==false){
			return 0;
		} else {
			return $salonMesa->id;
		}
	}

	public function getItemModifiersAction($menuItem)
	{
		$this->setResponse('json');

		$menuItem = $this->filter($menuItem, 'int');

		$modifiers = array();
		foreach($this->MenusItemsModifiers->find("menus_items_id=$menuItem") as $menuModifier){
			$modifier = $this->Modifiers->findFirst($menuModifier->getModifiersId());
			$modifiers[] = array(
				"id" => $menuModifier->getModifiersId(),
				"nombre" => $modifier->nombre
			);
		}
		return $modifiers;
	}

	public function searchCustomerAction()
	{
		$this->setResponse('json');
		$text = $this->getQueryParam("text", 'extraspaces');
		if(is_numeric($text)){
			$habitaciones = $this->Habitacion->find("numhab = '$text'", "order: concat(space(6-length(numhab)), numhab), nombre");
		} else {
			$habitaciones = $this->Habitacion->find("nombre like '%$text%'", "order: concat(space(6-length(numhab)), numhab), nombre");
		}
		$clientes = array();
		foreach($habitaciones as $habitacion){
			$clientes[] = array(
				"id" => $habitacion->id,
				"nombre" => $habitacion->nombre
			);
		}
		return $clientes;
	}

	public function getActiveMenusAction($salonId)
	{
		$this->setResponse('json');

		$salonId = $this->filter($salonId, 'int');

		$db = DbBase::rawConnect();
		$sql = "SELECT DISTINCT menus.id, menus.nombre
		FROM menus,salon_menus_items,menus_items
		WHERE menus_items.id=salon_menus_items.menus_items_id AND
		menus.id = menus_items.menus_id AND
		salon_menus_items.salon_id = $salonId AND
		salon_menus_items.estado = 'A' AND
		menus_items.estado = 'A'";
		$menus = array();
		$q = $db->query($sql);
		while($row = $db->fetchArray($q)){
			$menus[] = array($row[0], $row[1]);
		}
		return $menus;
	}

	public function getMenusItemsAction($menusId, $salonId)
	{
		$this->setResponse('json');

		$menusId = $this->filter($menusId, 'int');
		$salonId = $this->filter($salonId, 'int');

		$db = DbBase::rawConnect();

		$menusItems = array();
		$sql = "SELECT menus_items.id, menus_items.nombre
		FROM menus,salon_menus_items,menus_items
		WHERE menus_items.id=salon_menus_items.menus_items_id AND
		menus.id = menus_items.menus_id AND
		salon_menus_items.salon_id = $salonId AND
		menus_items.menus_id = $menusId AND
		salon_menus_items.estado = 'A' AND
		menus_items.estado = 'A'";
		$menus = array();
		$q = $db->query($sql);
		while($row = $db->fetchArray($q)){
			$menusItems[] = array($row[0], $row[1]);
		}
		return $menusItems;
	}

}
