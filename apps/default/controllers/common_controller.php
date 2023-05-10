<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Common
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * CommonController
 *
 * Servicio para información básica de todas las aplicaciones
 *
 */
class CommonController extends ApplicationController {

	public function getLocationsAction(){
		$this->setResponse('view');

		if($this->getRequestInstance()->isSetQueryParam('short')){
			$shortOutput = true;
		} else {
			$shortOutput = false;
		}

		echo '<ul>';
		$id = $this->getPostParam('id');
		if($id!=""){

			$query = new ActiveRecordJoin(array(
				'entities' => array('Location', 'Zone', 'Territory'),
				'conditions' => "{#Territory}.name = '$id'",
				'fields' => array(
					'id' => 'IF({#Location}.location_id IS NULL, {#Location}.id, {#Location}.location_id)',
					'location' => '{#Location}.name',
					'zone' => '{#Zone}.name',
					'territory' => '{#Territory}.name',
					'iso3166' => '{#Territory}.iso3166'
				),
				'order' => 'rank DESC',
				'limit' => 7
			));

			$n = 0;
			$instancePath = Core::getInstancePath();
			foreach($query->getResultSet() as $row){
				// if (extension_loaded('mbstring')) {
				// 	if (mb_check_encoding($row->location, "UTF-8")) {
				// 		$row->location = utf8_decode($row->location);
				// 	}
				// }

				echo '<li id="'.$row->id.'"><img src="'.$instancePath.'img/flags/'.$row->iso3166.'.gif" style="margin-right:3px">';
				if($shortOutput==false){
					echo $row->location.' / '.$row->zone.' / '.$row->territory;
				} else {
					echo $row->location.' / '.$row->territory;
				}
				echo '</li>';
				$n++;
			}

			$f = 10 - $n;
			$query = new ActiveRecordJoin(array(
				'entities' => array('Location', 'Zone', 'Territory'),
				'conditions' => "{#Location}.name LIKE '$id%'",
				'fields' => array(
					'id' => 'IF({#Location}.location_id IS NULL, {#Location}.id, {#Location}.location_id)',
					'location' => '{#Location}.name',
					'zone' => '{#Zone}.name',
					'territory' => '{#Territory}.name',
					'iso3166' => '{#Territory}.iso3166'
				),
				'order' => 'rank DESC',
				'limit' => $f
			));
			foreach($query->getResultSet() as $row){
				// if (extension_loaded('mbstring')) {
				// 	if (mb_check_encoding($row->location, "UTF-8")) {
				// 		$row->location = utf8_decode($row->location);
				// 	}
				// }
				echo '<li id="'.$row->id.'" lang="'.$row->iso3166.'"><img src="'.$instancePath.'img/flags/'.$row->iso3166.'.gif" style="margin-right:3px">';
				if($shortOutput==false){
					echo $row->location.' / '.$row->zone.' / '.$row->territory;
				} else {
					echo $row->location.' / '.$row->territory;
				}
				echo '</li>';
				$n++;
			}

			if($n<15){
				$f = 15 - $n;
				$query = new ActiveRecordJoin(array(
					'entities' => array('Location', 'Zone', 'Territory'),
					'conditions' => "{#Zone}.name = '$id'",
					'fields' => array(
						'id' => 'IF({#Location}.location_id IS NULL, {#Location}.id, {#Location}.location_id)',
						'location' => '{#Location}.name',
						'zone' => '{#Zone}.name',
						'territory' => '{#Territory}.name',
						'iso3166' => '{#Territory}.iso3166'
					),
					'order' => 'rank DESC',
					'limit' => $f
				));
				foreach($query->getResultSet() as $row){
					// if (extension_loaded('mbstring')) {
					// 	if (mb_check_encoding($row->location, "UTF-8")) {
					// 		$row->location = utf8_decode($row->location);
					// 	}
					// }
					echo '<li id="'.$row->id.'" lang="'.$row->iso3166.'"><img src="'.$instancePath.'img/flags/'.$row->iso3166.'.gif" style="margin-right:3px">';
					if($shortOutput==false){
						echo $row->location.' / '.$row->zone.' / '.$row->territory;
					} else {
						echo $row->location.' / '.$row->territory;
					}
					echo '</li>';
				}
			}
		}
		echo '</ul>';

	}

	public function pruebaAction(){
		$result = $this->Territory->find("name='LOL'");
		foreach($result as $r){
			echo $r;
		}
	}

	public function getCiudadesDianAction(){

		$this->setResponse('view');
		$id = $this->getPostParam('id');

		$ciudades = $this->ciudadesdian->find("nombre_ciudad LIKE '$id%'");

		$response = '<ul>';
		foreach ($ciudades as $key => $ciudad) {
			$response .='<li id="'.$ciudad->id.'">'.
			$ciudad->nombre_pais.' / '.
			$ciudad->nombre_depto.' / '.
			$ciudad->nombre_ciudad.
			'</li>';
		}
		$response .= '</ul>';

		echo $response;

	}

}