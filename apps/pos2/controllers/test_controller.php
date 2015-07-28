<?php

class TestController extends ApplicationController {

	public function testAction(){

		$prueba = new Prueba();
		$prueba->campo2 = 'valor';
		$prueba->save();


		/*$service = new WebServiceClient("http://localhost/hfos/identity/auth");

		$auth = new Service("identity.auth");
		$identity = $auth->startSession('andres', 'pass');

		echo $service->suma(1, 2);*/

		//SALT

		/*

		F-FRONT
		P-POS
		C-CONTA
		I-INVE
		N-NOMI
		A-AUTH




		if('F0001'){

		}*/

		//echo hash('tiger160,3', '2010-02-16 16:54:23control');


		/*$x = 0;
		$y = "login";

		if($x==$y){
			echo 1;
		} else {
			echo 0;
		}*/

		/*$service = new SoapClient(null, array(
			'uri' => 'http://app-services',
			//'location' => 'http://localhost/hfos/identity/auth',
			'location' => 'http://localhost/soap.php',
			'trace' => true
		));

		echo '<textarea rows=10 cols=120>';
		var_dump($service->startSession());
		echo $service->__getLastResponse();
		echo '</textarea>';

		var_dump($service->startSession(array('login' => 'admin', 'password' => 'control')));
		echo '<textarea rows=10 cols=80>';
		echo $service->__getLastResponse();
		echo '</textarea>';*/

		//echo '<textarea rows=30 cols=120>';
		#$service = new WebServiceClient('http://localhost/hfos/identity/auth');

		#$auth = $this->resolve(array('app' => 'identity', 'uri' => 'identity/auth'));
		#var_dump($auth->startSession(array('login' => 'admin', 'password' => 'control')));

		//echo '</textarea>';


	}

}

