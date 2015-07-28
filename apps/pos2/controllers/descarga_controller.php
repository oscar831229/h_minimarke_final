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

class DescargaController extends ApplicationController
{

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
	}

	public function indexAction()
	{
		$datos = $this->Datos->findFirst();
		$fecha = new Date($datos->getFecha());
		$fecha->diffDays(1);
		Tag::displayTo('fecha', $fecha->getDate());
		$this->setParamToView('movilins', $this->Movilin->find(array(
			"comprob like 'C%' AND almacen <> 1",
			'group' => 'comprob, numero, fecha',
			'columns' => 'comprob, numero, fecha',
			'limit' => 20,
			'order' => 'fecha DESC'
		)));
	}

	public function procesoAction()
	{
		$this->cleanTemplateAfter();
		$config = CoreConfig::readFromActiveApplication('app.ini');
		if (!isset($config->pos->back_version)) {
			new InterfasePOS4();
		} else {
			if (version_compare($config->pos->back_version, '6.0', '>=')) {
				$interpos3 = new InterfasePOS4();
				$interpos3->setVerbose(true);
				$interpos3->download();
			} else {
				new InterfasePOS2();
			}
		}
	}

}