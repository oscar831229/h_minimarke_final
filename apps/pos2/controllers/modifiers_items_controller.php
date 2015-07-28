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

class Modifiers_ItemsController extends ApplicationController {

	public function initialize(){
		$this->setTemplateAfter("admin_menu");
		$this->setPersistance(true);
	}

	public function indexAction(){
		$this->loadModel('MenusItems');
	}

	public function getModifiersAction($menuItemId=0){
		$this->setResponse('view');
		$menuItemId = $this->filter($menuItemId, 'int');
		$modifiers = $this->Modifiers->find();
		$this->setParamToView('modifiers', $modifiers);
		$this->setParamToView('menuItemId', $menuItemId);
		$this->loadModel('MenusItemsModifiers');
	}

	public function saveAction(){
		$menusItemsId = $this->getPostParam('menusItemsId', 'int');
		$this->MenusItemsModifiers->deleteall("menus_items_id='$menusItemsId'");
		$modifiers = $this->getPostParam('modifier');
		if(is_array($modifiers)){
			foreach($modifiers as $modifier){
				$modifier = $this->filter($modifier, 'int');
				if($modifier>0){
					$menuItemModifier = new MenusItemsModifiers();
					$menuItemModifier->setMenusItemsId($menusItemsId);
					$menuItemModifier->setModifiersId($modifier);
					if($menuItemModifier->save()==false){
						foreach($menuItemModifier->getMessages() as $message){
							Flash::error($message->getMessage());
						}
					}
				}
			}
		}
		$this->routeTo('action: index');
	}

}