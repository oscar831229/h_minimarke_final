<?php

require "Library/Kumbia/Core/CoreClassPath/CoreClassPath.php";
require "Library/Kumbia/Autoload.php";
spl_autoload_register("Autoload");

require "Library/Kumbia/BusinessProcess/BusinessProcess.php";

class PedidoProcess extends BusinessProcess {

	/**
	 * Autenticar el usuario
	 *
	 */
	public function autenticarHandler(){
		$operation = new BusinessOperation("hfos/pos2/clave", "autenticar");
		$password = "8cd64781c2534d80953532be0b0f6a02bc7ca9aa";
		$operation->setActionParams(array($password));
		$operation->perform();
		$response = $operation->getResponseXMLData();
		if($response==1){
			$this->setVariable("autorizado", true);
		} else {
			$this->setVariable("autorizado", false);
		}
	}

	/**
	 * Ir al modulo de Mesas
	 *
	 */
	public function irAMesasHandler(){
		$operation = new BusinessOperation("hfos/pos2/mesas");
		$operation->perform();
		$this->assertLocation("mesas/index", $operation->getLocation());
	}

	/**
	 * Escoger una mesa del primer salon
	 *
	 */
	public function escogerMesaHandler(){
		$operation = new BusinessOperation("hfos/pos2/pedido", "add");
		$operation->setActionParams(array(432));
		$operation->perform();
		$this->assertLocation("order/add", $operation->getLocation());
	}

	/**
	 * Consultar el tipo de Comanda solicitado
	 *
	 */
	public function consultarTipoComandaHandler(){
		$operation = new BusinessOperation("hfos/pos2/pedido", "getTipoComanda");
		$operation->perform();
		$tipoComanda = $operation->getResponseXMLData();
		$this->assertDomain($tipoComanda, array("A", "M"));
		$this->setVariable("tipoComanda", $tipoComanda);
	}

	/**
	 * Consulta si una comanda ya existe
	 *
	 */
	public function comandaExisteHandler(){
		$comandaExiste = true;
		while($comandaExiste==true){
			$numeroComanda = mt_rand(0, 10000);
			$this->debug("Intentando con número comanda $numeroComanda\n");
			$operation = new BusinessOperation("hfos/pos2/pedido", "existeComanda");
			$operation->setActionParams(array($numeroComanda));
			$operation->perform();
			$comandaExiste = $operation->getResponseXMLData()=="yes" ? true : false;
			$this->setVariable("numeroComanda", $numeroComanda);
			$this->setVariable("comandaExiste", $comandaExiste);
		}
	}

	/**
	 * Establecer el tipo de comanda
	 *
	 */
	public function establecerComandaHandler(){
		$numeroComanda = $this->getVariable("numeroComanda");
		$operation = new BusinessOperation("hfos/pos2/pedido", "setComanda");
		$operation->setActionParams(array($numeroComanda));
		$operation->perform();

		$operation = new BusinessOperation("hfos/pos2/pedido", "getNumeroComanda");
		$operation->perform();
		$this->assertEquals($numeroComanda, $operation->getResponseXMLData());
	}

	/**
	 * Consulta si requiere personas
	 *
	 */
	public function requiereNumeroPersonasHandler(){
		$operation = new BusinessOperation("hfos/pos2/pedido", "getPidePersonas");
		$operation->perform();
		$this->setVariable("requierePersonas", $operation->getResponseXMLData());
	}

	/**
	 * Establece el numero de personas
	 *
	 */
	public function ingresarPersonasHandler(){
		$numeroAsientos = mt_rand(2, 7);
		$operation = new BusinessOperation("hfos/pos2/pedido", "setNumberAsientos");
		$this->debug("Numero de asientos $numeroAsientos\n");
		$operation->setActionParams(array($numeroAsientos));
		$operation->perform();

		$operation = new BusinessOperation("hfos/pos2/pedido", "getNumberAsientos");
		$operation->perform();
		$this->assertEquals($numeroAsientos, $operation->getResponseXMLData());
		$this->setVariable("numeroPersonas", $numeroAsientos);
	}

	/**
	 * Escoge el menu
	 *
	 */
	public function escogerMenuHandler(){
		$numeroPersonas = $this->getVariable('numeroPersonas');
		$this->debug("Numero Personas: $numeroPersonas\n");
		$maxmenu = mt_rand(10, 20);
		for($codigoMenu=1;$codigoMenu<=$maxmenu;$codigoMenu++){
			$operation = new BusinessOperation("hfos/pos2/pedido", "getMenu");
			$operation->setActionParams(array($codigoMenu));
			$operation->perform();
			$response = $operation->getResponse();
			$menusItems = array();
			foreach($response->getElementsByClass('menuItemButton') as $element){
				$menusItems[] = str_replace('i', '', $element->getAttribute('id'));
			}
			$menuItem = $menusItems[mt_rand(0, count($menusItems)-1)];
			if($menuItem){
				$veces = mt_rand(1, 5);
				for($i=1;$i<=$veces;$i++){
					$asiento = mt_rand(1, $numeroPersonas);
					$operation = new BusinessOperation("hfos/pos2/pedido", "setActiveAsiento");
					$operation->setActionParams(array($asiento));
					$operation->perform();

					$operation = new BusinessOperation("hfos/pos2/pedido", "getActiveAsiento");
					$operation->perform();
					$this->assertEquals($asiento, $operation->getResponseXMLData());

					$operation = new BusinessOperation("hfos/pos2/pedido", "addToList");
					$operation->setActionParams(array($menuItem));
					$operation->perform();
				}
			}
			$this->debug("Seleccionando Menu Item: $menuItem\n");
		}
	}

	/**
	 * Inicializar el proceso de negocio
	 *
	 */
	public function initialize(){
		$processDefinition = ProcessDefinition::parseXMLFile("apps/pos2/test/pedido.xml");
		$processInstance = new ProcessInstance($processDefinition, $this);
		$processInstance->signal();
	}

}

try {
	$pedido = new PedidoProcess();
}
catch(Exception $e){
	print get_class($e).": ".$e->getMessage()."\n";
	/*print str_replace(array(
		'/Applications/MAMP/htdocs/',
		'kef/kumbia-ef/'
	), '', $e->getTraceAsString());*/
}