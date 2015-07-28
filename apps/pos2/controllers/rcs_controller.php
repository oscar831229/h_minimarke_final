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

class RcsController extends ApplicationController {

	public function initialize(){
		$this->setTemplateAfter('admin_menu');
	}

	public function indexAction(){

	}

	public function revisionsAction($sourceName, $primaryId){

		if($primaryId==''){
			return $this->routeTo('controller: '.$sourceName);
		}

		require 'Library/Kumbia/Generator/Db/GeneratorDb.php';

		$sourceName = $this->filter($sourceName, 'identifier');
		$primaryId = $this->filter($primaryId, 'int');

		$entity = EntityManager::getEntityFromSource($sourceName);
		$schema = $entity->getSchema();
		if($schema==''){
			$schema = $entity->getConnection()->getDatabaseName();
		}
		$query = new ActiveRecordJoin(array(
			'count' => '*',
			'entities' => array('Records', 'Revisions'),
			'conditions' => "{#Revisions}.db='".$schema."' AND {#Revisions}.source='".$sourceName."' AND  {#Records}.field_name='id' AND {#Records}.value='".$primaryId."'"
		));
		$originalEntity = $entity->findFirst($primaryId);
		if($originalEntity==false){
			return $this->routeTo('controller: '.$sourceName);
		}

		$rowcount = $query->getResultSet()->getFirst()->count;
		if($rowcount==0){
			if($originalEntity==false){
				Flash::error('No se pudo crear la revisión base');
			} else {
				POSRcs::afterUpdate($originalEntity);
				Flash::success('Se creó la revisión base');
			}
		}

		$detalle = get_class($entity);
		foreach($originalEntity->getAttributes() as $attribute){
			if($attribute=='detalle'||$attribute=='nombre'){
				$detalle = $originalEntity->readAttribute($attribute);
				break;
			}
		}
		$this->setParamToView('detalle', $detalle);

		$query = new ActiveRecordJoin(array(
			'fields' => array('{#Revisions}.id', '{#Revisions}.source', '{#UsuariosPos}.nombre', '{#Revisions}.fecha'),
			'entities' => array('Records', 'Revisions', 'UsuariosPos'),
			'conditions' => "{#Revisions}.db='".$schema."' AND {#Revisions}.source='".$sourceName."' AND  {#Records}.field_name='id' AND {#Records}.value='".$primaryId."'",
			'order' => '{#Revisions}.fecha DESC'
		));

		$this->setParamToView('revisions', $query->getResultSet());
		$this->setParamToView('sourceName', $sourceName);

	}

}