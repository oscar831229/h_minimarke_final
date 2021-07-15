<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

require KEF_ABS_PATH.'Library/Hfos/Application/Application.php';
require KEF_ABS_PATH.'Library/Hfos/IdentityManager/IdentityManager.php';
require KEF_ABS_PATH.'Library/Hfos/Gardien/Gardien.php';

/**
 * Hfos_Loader
 *
 * Autoloader para los componentes de Hfos
 *
 */
class Hfos_Loader {

	private static $_classPath = array(
		'Aura' => 'Aura/Aura',
		'AuraException' => 'Aura/Exception',
		'AuraFusion' => 'Aura/Fusion/AuraFusion',
		'AuraUtils' => 'Aura/Utils/AuraUtils',
		'AuraNiif' => 'Aura/AuraNiif',
		'AuraNiifException' => 'Aura/ExceptionNiif',
		'BackCacher' => 'BackCacher/BackCacher',
		'Clara' => 'Clara/Clara',
		'ClaraMacro' => 'Clara/Macro/ClaraMacro',
		'EventLogger' => 'EventLogger/EventLogger',
		'FrontCacher' => 'FrontCacher/FrontCacher',
		'Gardien' => 'Gardien/Gardien',
		'GardienException' => 'Gardien/Exception',
		'HfosConsole' => 'Console/Console',
		'HfosCommandRunner' => 'Console/Runner/CommandRunner',
		'HfosDate' => 'HfosDate/HfosDate',
		'HfosException' => 'HfosException',
		'HfosMail' => 'HfosMail/HfosMail',
		'HfosMailException' => 'HfosMail/Exception',
		'HfosMessage' => 'HfosMail/Message/HfosMessage',
		'HfosTag' => 'HfosTag/HfosTag',
		'HfosTime' => 'HfosTime/HfosTime',
		'HyperForm' => 'HyperForm/HyperForm',
		'HyperFormController' => 'HyperForm/HyperFormController',
		'IdentityManager' => 'IdentityManager/IdentityManager',
		'Invoicing' => 'Invoicing/Invoicing',
		'Names' => 'Names/Names',
		'Rcs' => 'Rcs/Rcs',
		'RcsRecord' => 'Rcs/Record/RcsRecord',
		'ReportBase' => 'ReportBase/ReportBase',
		'Sequences' => 'Sequences/Sequences',
		'SequencesException' => 'Sequences/SequencesException',
		'Settings' => 'Settings/Settings',
		'Socorro' => 'Socorro/Socorro',
		'Tatico' => 'Tatico/Tatico',
		'TaticoKardex' => 'Tatico/Kardex/TaticoKardex',
		'TaticoException' => 'Tatico/Exception',
		'Upgrade' => 'Upgrade/Upgrade',
		'Wepax' => 'Wepax/Wepax'
	);

	public static function autoLoader($className){
		if(isset(self::$_classPath[$className])){
			require_once KEF_ABS_PATH.'Library/Hfos/'.self::$_classPath[$className].'.php';
		}
	}

}

spl_autoload_register(array('Hfos_Loader', 'autoLoader'));
