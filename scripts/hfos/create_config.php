<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Scripts
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

require 'public/index.config.php';
require KEF_ABS_PATH.'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require KEF_ABS_PATH.'Library/Kumbia/Session/Session.php';
require KEF_ABS_PATH.'Library/Kumbia/Autoload.php';

/**
 * CreateConfig
 *
 * Crea archivos de configuración faltantes en las aplicaciones
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright 	BH-TECK Inc. 2009-2012
 * @license 	New BSD License
 * @version 	$Id$
 */
class CreateConfig extends Script {

	public function __construct(){

		$data = array(
			'default' => array(
				'config' => "[application]\nmode = production\nname = Root\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[entities]\nautoInitialize = Off\n\n[collector]\nprobability = 75\n\n[hfos]\nidentity = hfos_identity",
				'environment' => "[development]database.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol\ndatabase.charset = utf8\ndatabase.collate = utf8_spanish_ci\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol\ndatabase.charset = utf8\ndatabase.collate = utf8_spanish_ci"
			),
			'contab' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol\ndatabase.charset = utf8\ndatabase.collate = utf8_spanish_ci\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol",
				'config' => "[application]\nmode = production\nname = Contabilidad\ncode = CO\nicon = document-library.png\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[collector]\nprobability = 75\n\n[entities]\nautoInitialize = Off\ncharset = utf8\ncollate = utf8_spanish_ci\n\n[hfos]\nidentity = hfos_identity",
				'naming' => "[contab.session]\nhost = 127.0.0.1\nuri = contab/session\nprotocol = Flux\n[contab.aura]\nhost = 127.0.0.1\nuri = contab/aura\nprotocol = Flux\n[invoicer.invoicing]\nhost = 127.0.0.1\nuri = invoicer/invoicing\nprotocol = Flux\n[invoicer.session]\nhost = 127.0.0.1\nuri = invoicer/session\nprotocol = Flux\n[sequences.sequences]\nhost = 127.0.0.1\nuri = sequences/sequences\nprotocol = Flux\n[sequences.session]\nhost = 127.0.0.1\nuri = sequences/session\nprotocol = Flux\n[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux"
			),
			'identity' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = hfos_identity\ndatabase.charset = utf8\ndatabase.collate = utf8_spanish_ci\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = hfos_identity",
				'config' => "[application]\nmode = production\nname = Administración\ncode = IM\nicon = administrative-docs.png\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[entities]\nautoInitialize = Off\n\n[hfos]\nuniqueid = hfos\nhostname = hfos\nback_db = ramocol\nfront_db = hotel2\npos_db = pos\ncharset = utf8\ncollate = utf8_spanish_ci\nidentity = hfos_identity",
				'naming' => "[contab.session]\nhost = 127.0.0.1\nuri = contab/session\nprotocol = Flux\n\n[contab.aura]\nhost = 127.0.0.1\nuri = contab/aura\nprotocol = Flux\n\n[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux\n\n[inve.session]\nhost = 127.0.0.1\nuri = inve/session\nprotocol = Flux\n\n[inve.tatico]\nhost = 127.0.0.1\nuri = inve/tatico\nprotocol = Flux\n"
			),
			'inve' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol\ndatabase.charset = utf8\ndatabase.collate = utf8_spanish_ci\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol",
				'config' => "[application]\nmode = production\nname = Inventarios\ncode = IN\nicon = shipping.png\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[collector]\nprobability = 75\n\n[entities]\nautoInitialize = Off\ncharset = utf8\ncollate = utf8_spanish_ci\n\n[hfos]\nuniqueid = hfos\nhostname = hfos\nback_db = ramocol\nfront_db = hotel2\npos_db = pos\nidentity = hfos_identity",
				'naming' => "[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux\n\n\n[identity.session]\nhost = 127.0.0.1\nuri = identity/session\nprotocol = Flux\n\n[identity.delivery]\nhost = 127.0.0.1\nuri = identity/delivery\nprotocol = Flux\n\n\n[contab.session]\nhost = 127.0.0.1\nuri = contab/session\nprotocol = Flux\n\n[contab.aura]\nhost = 127.0.0.1\nuri = contab/aura\nprotocol = Flux"
			),
			'nomina' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol\ndatabase.charset = utf8\ndatabase.collate = utf8_spanish_ci\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol",
				'config' => "[application]\nmode = production\nname = Nomina\ncode = NO\nicon = suppliers.png\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[collector]\nprobability = 75\n\n[entities]\nautoInitialize = Off\ncharset = utf8\ncollate = utf8_spanish_ci",
				'naming' => "[contab.session]\nhost = 127.0.0.1\nuri = contab/session\nprotocol = Flux\n\n[contab.aura]\nhost = 127.0.0.1\nuri = contab/aura\nprotocol = Flux\n\n[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux"
			),
			'invoicer' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol\ndatabase.charset = utf8\ndatabase.collate = utf8_spanish_ci\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol",
				'config' => "[application]\nmode = development\nname = \"Facturador\"\ncode = \"FC\"\nicon = \"document-library.png\"\ndbdate = YYYY-MM-DD\ndebug = On\n\n[collector]\nprobability = 75\n\n\n[entities]\nautoInitialize = Off\n\n[hfos]\nhostname = localhost\nback_db = ramocol\nfront_db = hotel2\npos_db = pos\nsocios = hfos_socios\ncharset = utf8\ncollate = utf8_spanish_ci",
				'naming' => "[contab.session]\nhost = 127.0.0.1\nuri = contab/session\nprotocol = Flux\n\n[contab.aura]\nhost = 127.0.0.1\nuri = contab/aura\nprotocol = Flux\n\n[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux"
			),
			'pos2' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = pos\ndatabase.charset = utf8\ndatabase.collate = utf8_spanish_ci\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = pos",
				'config' => "[application]\nmode = production\nname = \"PUNTO DE VENTA\"\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[collector]\nprobability = 75\n\n[entities]\nautoInitialize = Off\ncharset = utf8\ncollate = utf8_spanish_ci\n\n[hfos]\nidentity = hfos_identity",
				'naming' => "[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux\n\n\n[identity.session]\nhost = 127.0.0.1\nuri = identity/session\nprotocol = Flux\n\n[identity.delivery]\nhost = 127.0.0.1\nuri = identity/delivery\nprotocol = Flux\n\n\n[contab.session]\nhost = 127.0.0.1\nuri = contab/session\nprotocol = Flux\n\n[contab.aura]\nhost = 127.0.0.1\nuri = contab/aura\nprotocol = Flux",
				'app' => "[pos]\nhotel = hotel2\nramocol = ramocol\npos = pos\ninterpos = On\nprinting_type = client\nonline_inve = On\ncompany_exention = Off\nback_version = 6.0\nis_club = Off"
			),
			'sequences' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = ramocol",
				'config' => "[application]\nmode = production\nname = Consecutivos\ncode = CN\nicon = document-library.png\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[collector]\nprobability = 75\n\n[entities]\nautoInitialize = Off",
				'naming' => "[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux"
			),
			'tpc' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = hfos_tc\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = hfos_tc",
				'config' => "[application]\nmode = production\nname = Tiempo Compartido\ncode = TC\nicon = shipping.png\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[collector]\nprobability = 75\n\n[entities]\nautoInitialize = Off\n\n[hfos]\nuniqueid = hfos\nhostname = hfos\nback_db = ramocol\nfront_db = hotel2\npos_db = pos\n",
				'naming' => "[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux\n\n[identity.session]\nhost = 127.0.0.1\nuri = identity/session\nprotocol = Flux\n\n[identity.delivery]\nhost = 127.0.0.1\nuri = identity/delivery\nprotocol = Flux\n\n\n[contab.session]\nhost = 127.0.0.1\nuri = contab/session\nprotocol = Flux\n\n[contab.aura]\nhost = 127.0.0.1\nuri = contab/aura\nprotocol = Flux"
			),
			'socios' => array(
				'environment' => "[development]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = hfos_socios\n\n[production]\ndatabase.type = mysql\ndatabase.host = localhost\ndatabase.username = root\ndatabase.password = hea101\ndatabase.name = hfos_socios",
				'config' => "[application]\nmode = production\nname = Socios\ncode = SO\nicon = shipping.png\ndbdate = YYYY-MM-DD\ndebug = Off\n\n[collector]\nprobability = 75\n\n[entities]\nautoInitialize = Off\n\n[hfos]\nuniqueid = hfos\nhostname = hfos\nback_db = ramocol\nfront_db = hotel2\npos_db = pos\nsocios = hfos_socios\nsocios_ip = 127.0.0.1\nidentity = hfos_identity",
				'naming' => "[identity.auth]\nhost = 127.0.0.1\nuri = identity/auth\nprotocol = Flux\n\n[identity.session]\nhost = 127.0.0.1\nuri = identity/session\nprotocol = Flux\n\n[identity.delivery]\nhost = 127.0.0.1\nuri = identity/delivery\nprotocol = Flux\n\n\n[contab.session]\nhost = 127.0.0.1\nuri = contab/session\nprotocol = Flux\n\n[contab.aura]\nhost = 127.0.0.1\nuri = contab/aura\nprotocol = Flux\n\n[invoicer.session]\nhost = 127.0.0.1\nuri = invoicer/session\nprotocol = Flux\n\n[invoicer.invoicing]\nhost = 127.0.0.1\nuri = invoicer/invoicing\nprotocol = Flux"
			)		
		);

		foreach ($data as $app => $configs)
		{
			foreach ($configs as $file => $content)
			{
				$path = 'apps/'.$app.'/config/'.$file.'.ini';
				if (!file_exists($path)) {
					echo 'Creating '.$path.'...', PHP_EOL;
					file_put_contents($path, $content.PHP_EOL);
				}
				if (!is_dir('apps/'.$app.'/logs')) {
					@mkdir('apps/'.$app.'/logs');
				}
			}
		}

	}

}


try 
{
	$script = new CreateConfig();
}
catch(CoreException $e){
	echo get_class($e).' : '.$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	echo 'Exception : '.$e->getMessage()."\n";
}
