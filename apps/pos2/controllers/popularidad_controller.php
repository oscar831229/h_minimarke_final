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

class PopularidadController extends ApplicationController {

	public $template = "admin_menu";
	public $fecha_inicial = "";
	public $fecha_final = "";

	public function initialize(){
		$this->setTemplateAfter("admin_menu");
	}

	public function indexAction(){
		if(!$this->fecha_inicial){
			$this->fecha_inicial = date("Y-m-d");
		}
		if(!$this->fecha_final){
			$this->fecha_final = date("Y-m-d");
		}
	}

	public function processAction(){
		$fecha_inicial = $this->post("fecha_inicial", "date");
		$fecha_final = $this->post("fecha_final", "date");

		# /dev/sda3 xfs size check2 field cant read superblock /bin/sh

		$db = DbBase::raw_connect();
		$db->drop_table("popularidad");
		$db->create_table("popularidad", array(
			"id" => array(
				"type" => DbBase::TYPE_INTEGER,
				"not_null" => true,
				"primary" => true,
				"auto" => true
			),
			"cantidad" => array(
				"type" => DbBase::TYPE_INTEGER,
				"not_null" => true
			),
			"valor" => array(
				"type" => DbBase::TYPE_DECIMAL,
				"not_null" => true,
				"size" => "22,2"
			),
			"costo" => array(
				"type" => DbBase::TYPE_DECIMAL,
				"not_null" => true,
				"size" => "22,2"
			)
		));

		ActiveRecord::createModel("Popularidad");
		$this->Popularidad = new Popularidad();

		$facturas = $this->Factura->find("fecha >= '$fecha_inicial' AND fecha <= '$fecha_final'");
		foreach($facturas as $factura){
			$detalles = $this->DetalleFactura->find_all_by_consecutivo_facturacion($factura->consecutivo_facturacion);
			foreach($detalles as $detalle){
				if(isset($values[$detalle->menus_items_id])){
					//$values[$detalle->menus_items_id]['number'] =
					//if($this->Popularidad->)
					$popularidad = new Popularidad();
					//$propularidad->
				}
			}
		}
		$this->route_to("action: index");
	}

}

