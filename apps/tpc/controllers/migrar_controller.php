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

Core::importFromLibrary('Hfos/Tpc','TpcMigration.php');
/**
 * TestsController
 *
 * Controlador de tests de TC
 */
class MigrarController extends ApplicationController {
	
	public function indexAction(){
		$this->setResponse('json');
		try{
			$tpcMigration = new TpcMigration();
			$tpcMigration->main();
			return array('status'=>'SUCCESS');
			}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>'MigrarController: '.print_r($e,true));	
		}
	}

	public function testAction(){
		$this->setResponse('json');
		try{
			$a = LocaleMath::round(343434.98982332,0);
			return array('status'=>'SUCCESS', 'message'=>$a);
		}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>print_r($e,true));	
		}	
	}
}
