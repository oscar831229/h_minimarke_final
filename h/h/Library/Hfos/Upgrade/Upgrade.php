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

class Upgrade extends UserComponent {

	private $_supportedVersions = array('6.0', '6.1');

	public function __construct()
    {

		Rcs::disable();
		set_time_limit(0);

		EntityManager::setModelsDirectory('Library/Hfos/Models');
		$db = DbBase::rawConnect();
		$backDbName = CoreConfig::getAppSetting('back_db', 'hfos');
		if($backDbName!=''){
			$db->query("USE $backDbName");
		} else {
			$db->query("USE ramocol");
		}
	}

	protected function to60()
    {

		$transaction = TransactionManager::getUserTransaction();

		//Arreglar consecutivos
		/*foreach($this->Comprob->find() as $comprob){
			$numeroMovi = $this->Movi->maximum(array("numero", 'conditions' => "comprob='{$comprob->getCodigo()}'"));
			$numeroMovihead = $this->Movihead->maximum(array("numero", 'conditions' => "comprob='{$comprob->getCodigo()}'"));
			if($numeroMovi>$numeroMovihead){
				$comprob->setConsecutivo($numeroMovi+1);
			} else {
				$comprob->setConsecutivo($numeroMovihead+1);
			}
			if($comprob->save()==false){
				foreach($comprob->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
		}*/

		//Sin ICA
		$this->Nits->updateAll('ap_aereo=0', 'ap_aereo NOT IN (SELECT codigo FROM ica)');
		$this->Nits->updateAll('clase="C"', 'clase NOT IN ("C", "A", "E") OR clase IS NULL');
		$this->Nits->updateAll('nombre = trim(nombre)');
		$this->Nits->updateAll('nombre = replace(nombre, "\'", "")');
		$this->Nits->updateAll('ciudad = replace(ciudad, "\'", "")');

		//Sin Nombre
		foreach($this->Nits->find("nombre IS NULL OR nombre = ''") as $nit){
			$nit->setNombre('SIN NOMBRE');
			if($nit->save()==false){
				foreach($nit->getMessages() as $message){
					$transaction->rollback($nit->getNit().' : '.$message->getMessage());
				}
			}
		}

		//Migrar ciudades
		$this->Nits->updateAll('ciudad="Bogota"', 'ciudad="BTA"');
		$this->Nits->updateAll('ciudad="Bogota"', 'ciudad="BOGOTA-SOACHA"');
		$this->Nits->updateAll('ciudad="Mexico DF"', 'ciudad="CIUDAD DE MEXICO"');
		$this->Nits->updateAll('ciudad="Mexico DF"', 'ciudad="DISTRITO FEDERAL DF"');
		$this->Nits->updateAll('ciudad="Mexico DF"', 'ciudad="DISTRITO FEDERAL"');
		$this->Nits->updateAll('ciudad="Mexico DF"', 'ciudad="DISTRITO EFDERAL"');
		$this->Nits->updateAll('ciudad="Oslo"', 'ciudad="NORUEGA"');
		$this->Nits->updateAll('ciudad="Monteria"', 'ciudad="MONTERM-!A"');
		$this->Nits->updateAll('ciudad="0"', 'ciudad="Sin Definir"');
		$this->Nits->updateAll('ciudad="Bogota"', 'ciudad="BOG"');
		$this->Nits->updateAll('ciudad="Bogota"', 'ciudad="BOGOTS"');
		$this->Nits->updateAll('ciudad="Bogota"', 'ciudad="SANTAFE DE BOGOTA"');
		$this->Nits->updateAll('ciudad="Medellin"', 'ciudad="MED"');
		$this->Nits->updateAll('ciudad="Barranquilla"', 'ciudad="B/QUILLA"');
		$this->Nits->updateAll('ciudad="Barranquilla"', 'ciudad="BQUILLA"');
		$this->Nits->updateAll('ciudad="Brasilia"', 'ciudad="BRASIL"');
		$this->Nits->updateAll('ciudad="Caracas"', 'ciudad="Venezuela"');
		$this->Nits->updateAll('ciudad="Nueva York"', 'ciudad="E.E.U.U"');
		$this->Nits->updateAll('ciudad=0', "ciudad IS NULL OR ciudad='' OR ciudad='0'");
		$this->Nits->updateAll('locciu=0', "ciudad='0'");

		$ciudades = array();
		foreach($this->Nits->find("locciu IS NULL") as $nit){
			$cliente = $this->Clientes->findFirst("cedula='{$nit->getNit()}'");
			if($cliente!=false){
				$nit->setLocciu($cliente->getLocdir());
				if($nit->save()==false){
					foreach($nit->getMessages() as $message){
						$transaction->rollback($nit->getNit().' : '.$message->getMessage());
					}
				}
				unset($cliente);
			} else {
				$empresa = $this->Empresas->findFirst("nit='{$nit->getNit()}'");
				if($empresa!=false){
					$nit->setLocciu($empresa->getLocdir());
					if($nit->save()==false){
						foreach($nit->getMessages() as $message){
							$transaction->rollback($nit->getNit().' : '.$message->getMessage());
						}
					}
					unset($empresa);
				} else {
					if(!isset($ciudades[$nit->getCiudad()])){
						$location = $this->Location->findFirst("name='{$nit->getCiudad()}'", 'order: rank DESC');
						if($location==false){
							$ciudades[$nit->getCiudad()] = -1;
						} else {
							$ciudades[$nit->getCiudad()] = $location->getId();
						}
						unset($location);
					}
					if($ciudades[$nit->getCiudad()]>0){
						$nit->setLocciu($ciudades[$nit->getCiudad()]);
						if($nit->save()==false){
							foreach($nit->getMessages() as $message){
								$transaction->rollback($nit->getNit().' : '.$message->getMessage());
							}
						}
					}
				}
			}
			unset($nit);
		}
		unset($ciudades);
		$this->Nits->updateAll('locciu=0', "locciu IS NULL");

		//Migrar Tipos de Documento
		foreach($this->Nits->find("tipodoc IS NULL OR tipodoc = ''") as $nit){
			if($nit->getTipodoc()==''){
				switch($nit->getClase()){
					case 'C':
						$nit->setTipodoc(13);
						break;
					case 'A':
						$nit->setTipodoc(31);
						break;
					case 'E':
						$nit->setTipodoc(41);
						break;
				}
				if($nit->save()==false){
					foreach($nit->getMessages() as $message){
						$transaction->rollback($nit->getNit().' : '.$message->getMessage());
					}
				}
			}
		}
	}

	public static function dumpDB()
    {
		$db = DbBase::rawConnect();
		$backDbName = CoreConfig::getAppSetting('back_db', 'hfos');
		if(!$backDbName){
			$backDbName = 'ramocol';
		}
		$schema = array();
		$databases = array('hfos_identity', 'hfos_workspace', 'hfos_rcs', 'hfos_audit', $backDbName);
		foreach($databases as $database){
			$db->query("USE `$database`");
			$tables = $db->listTables();
			foreach($tables as $table){
				$createTableCursor = $db->query("SHOW CREATE TABLE `$database`.`$table`");
				while($createTableRow = $db->fetchArray($createTableCursor)){
					if(strpos($createTableRow[1],'DEFINER VIEW')==false){
						$schema[$database][$table] = array(
							'type' => 'TABLE',
							'sql' => preg_replace('/ AUTO_INCREMENT=[0-9]+/', '', $createTableRow[1]),
							'fields' => array()
						);
						$position = strpos($createTableRow[1], '(');
						$endPosition = strpos($createTableRow[1], ') ENGINE');
						$fieldsRaw = substr($createTableRow[1], $position+2, $endPosition-$position-1);
						$fieldsArray = explode(",\n", $fieldsRaw);
						foreach($fieldsArray as $fieldItem){
							if(substr($fieldItem, 0, 5)!='  KEY'&&substr($fieldItem, 0, 5)!='  PRI'&&substr($fieldItem, 0, 5)!='  UNI'&&substr($fieldItem, 0, 7)!='  CONST'){
								if(preg_match('/`(.+)`/', $fieldItem, $matches)){
									$schema[$database][$table]['fields'][$matches[1]] = $fieldItem;
								}
							} else {
								if(substr($fieldItem, 0, 5)=='  KEY'){
									if(preg_match('/`([a-z0-9A-Z_]+)`/', $fieldItem, $matches)){
										$schema[$database][$table]['indexes'][$matches[1]] = str_replace(")\n)", ')', $fieldItem);
									}
								}
							}
						}
					} else {
						$schema[$database][$table] = array(
							'type' => 'VIEW',
							'sql' => $createTableRow[1]
						);
					}
				}
			}
		}
		$version = str_replace('.', '', Hfos_Application::getVersion());
		file_put_contents('Library/Hfos/Upgrade/schema/back/'.$version.'.php', serialize($schema));
	}

	public static function checkVersion()
    {
		$transaction = TransactionManager::getUserTransaction();
		$empresa = EntityManager::get('Empresa')->findFirst();
		#if(!$empresa->getVersion()){
			$upgrader = new Upgrade();
			$upgrader->to60();
		#}
		$empresa->setVersion(Hfos_Application::getVersion());
		$empresa->save();
	}

}
