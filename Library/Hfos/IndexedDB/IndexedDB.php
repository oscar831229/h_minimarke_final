<?php

class IndexedDB {

	private $_sources = array();

	private static function _getSources(){
		$appCode = CoreConfig::getAppSetting('code');
		if(!isset(self::$_sources[$appCode])){
			require KEF_ABS_PATH.'Library/Hfos/IndexedDB/sources/'.$appCode.'.php';
			self::$_sources[$appName] = $sources;
		}
		return self::$_sources[$appName];
	}

	public static function search(){

	}

}