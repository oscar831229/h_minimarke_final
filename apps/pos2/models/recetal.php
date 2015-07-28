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

class Recetal extends ActiveRecord {

	public function initialize(){
		$config = CoreConfig::readFromActiveApplication("app.ini", 'ini');
		if(isset($config->pos->ramocol)){
			$this->setSchema($config->pos->ramocol);
		} else {
			$this->setSchema("ramocol");
		}
	}

	public function getDetail($attribute,$value=''){
		$value = $value == '' ? $this->$attribute : $value;
		switch($attribute){
			case 'tipol':
				return $value == 'I' ? 'REFERENCIA' : 'RECETA';
				break;
			case 'almacen':
				$almacen = new Almacenes();
				$almacen = $almacen->findFirst('codigo="'.$value.'"');
				if($almacen == false){
					return '';
				}
				return $almacen->getNomAlmacen();
				break;
			case 'numero_rec':
				$recetap = new Recetap();
				$almacen = $this->almacen == '' ? '1' : $this->almacen;
				$recetap = $recetap->findFirst('numero_rec="'.$value.'" AND almacen="'.$almacen.'"');
				if($recetap == false) {
					return '';
				}
				return $recetap->nombre;
				break;
			case 'item':
				if($this->tipol=='I'){
					$inve = EntityManager::getEntityInstance('Inve')->findFirst('item="'.$value.'"');
					if($inve==false){
						return 'NO EXISTE REFERENCIA';
					} else {
						return $value.' - '.utf8_encode($inve->getDescripcion());
					}
				} else{
					$recetap = EntityManager::getEntityInstance('Recetap')->findFirst('almacen=1 AND numero_rec="'.$value.'"');
					if($recetap==false){
						return 'NO EXISTE SUB-RECETA';
					} else {
						return $value.' - '.utf8_encode($recetap->nombre);
					}
				}
			case 'opcional':
				return $value == 'S' ? 'SI' : 'NO';
				break;
			case 'divisor':
			case 'cantidad':
				return Currency::number($value);
			case 'valore':
			case 'valor':
				return Currency::number($value);
		}
	}

}
