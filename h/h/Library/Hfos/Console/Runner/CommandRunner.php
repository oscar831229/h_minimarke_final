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

//php ../../../Lime/lime.php commands.lime > CommandParser.php

class HfosCommandRunnerException extends CoreException
{

}

/**
 * HfosCommandRunner
 *
 * Compiles command into array-code and interprets it
 */
class HfosCommandRunner extends UserComponent
{

	const T_NUMBER = 1;
	const T_STRING = 2;
	const T_EOL = 3;
	const T_SYMBOL = 4;
	const T_VARIABLE = 5;

	const T_GRANT = 19;
	const T_ON = 20;
	const T_COMPROBS = 21;
	const T_TO = 22;
	const T_HELP = 23;
	const T_GC = 24;
	const T_RESTART = 25;
	const T_SERVICE = 26;
	const T_BACKUP = 28;
	const T_SHOW = 29;
	const T_WHOAMI = 30;
	const T_PS = 31;
	const T_UPTIME = 32;
	const T_DATE = 33;
	const T_DISK = 34;
	const T_USAGE = 35;

	const T_AURA = 40;
	const T_TATICO = 41;
	const T_AUTH = 42;

	private static $_reservedWords = array(
		'grant' => 19,
		'on' => 20,
		'comprobs' => 21,
		'to' => 22,
		'help' => 23,
		'gc' => 24,
		'restart' => 25,
		'service' => 26,
		'backup' => 28,
		'show' => 29,
		'whoami' => 30,
		'ps' => 31,
		'uptime' => 32,
		'date' => 33,
		'disk' => 34,
		'usage' => 35,
		'aura' => 40,
		'tatico' => 41,
		'auth' => 42,
	);

	private static $_opCode = array();

	private static $_parser;

	private static $_lineNumber = 0;

	private static function _prepareParser()
	{
		if (self::$_parser === null) {
			Core::importFromLibrary('Lime', 'parse_engine.php');
			Core::importFromLibrary('Hfos', 'Console/Runner/CommandParser.php');
			self::$_parser = new parse_engine(new CommandParser());
		}
	}

	/**
	 * Genera un listado de tokens a parsear
	 *
	 * @param	string $line
	 * @return	array
	 */
	private static function _tokenize($line)
	{
		$tokens = array();
		while (i18n::strlen($line)) {
			$line = trim($line, " \t");
			if(preg_match('/^[0-9]+(\.[0-9]*)?\W/', $line, $regs)){
				$tokens[] = array(self::$_lineNumber, self::T_NUMBER, $regs[0]);
				$line = i18n::substr($line, strlen($regs[0])-1);
			} else {
				if(preg_match('/^"([a-zA-Z0-9_]+)"\W/i', $line, $regs)){
					$tokens[] = array(self::$_lineNumber, self::T_STRING, $regs[1]);
					$line = i18n::substr($line, strlen($regs[0])-1);
				} else {
					if(preg_match('/^([A-Za-zÁÉÍÓÚÑáéíóúñ_0-9]+)\W/i', $line, $regs)){
						$str = i18n::strtolower($regs[1]);
						if(isset(self::$_reservedWords[$str])){
							$tokens[] = array(self::$_lineNumber, self::$_reservedWords[$str], $regs[1]);
						} else {
							$tokens[] = array(self::$_lineNumber, self::T_VARIABLE, $regs[1]);
						}
						$line = i18n::substr($line, strlen($regs[0])-1);
					} else {
						if($line[0]!="\n"){
							$tokens[] = array(self::$_lineNumber, self::T_SYMBOL, $line[0]);
						} else {
							$tokens[] = array(self::$_lineNumber, self::T_EOL, $line[0]);
							self::$_lineNumber++;
						}
						$line = i18n::substr($line, 1);
					}
				}
			}
		}
		//print_r($tokens);
		return $tokens;
	}


	/**
	 * Busca errores sintácticos en un comando
	 *
	 * @param string $stringMacro
	 */
	public static function parse($stringCommand)
	{
		if($stringCommand){
			$stringCommand.="\n";
			self::_prepareParser();
			try {
				self::$_lineNumber = 1;
				self::$_parser->reset();
				foreach(self::_tokenize($stringCommand) as $token){
					switch($token[1]){

						case self::T_AURA:
							self::$_parser->eat('AURA', null);
							break;
						case self::T_TATICO:
							self::$_parser->eat('TATICO', null);
							break;
						case self::T_AUTH:
							self::$_parser->eat('AUTH', null);
							break;

						case self::T_GRANT:
							self::$_parser->eat('GRANT', null);
							break;
						case self::T_ON:
							self::$_parser->eat('ON', null);
							break;
						case self::T_COMPROBS:
							self::$_parser->eat('COMPROBS', null);
							break;
						case self::T_TO:
							self::$_parser->eat('TO', null);
							break;
						case self::T_VARIABLE:
							self::$_parser->eat('VARIABLE', $token[2]);
							break;
						case self::T_GC:
							self::$_parser->eat('GC', null);
							break;
						case self::T_RESTART:
							self::$_parser->eat('RESTART', null);
							break;
						case self::T_SERVICE:
							self::$_parser->eat('SERVICE', null);
							break;
						case self::T_BACKUP:
							self::$_parser->eat('BACKUP', null);
							break;
						case self::T_SHOW:
							self::$_parser->eat('SHOW', null);
							break;
						case self::T_WHOAMI:
							self::$_parser->eat('WHOAMI', null);
							break;
						case self::T_PS:
							self::$_parser->eat('PS', null);
							break;
						case self::T_UPTIME:
							self::$_parser->eat('UPTIME', null);
							break;
						case self::T_DISK:
							self::$_parser->eat('DISK', null);
							break;
						case self::T_USAGE:
							self::$_parser->eat('USAGE', null);
							break;
						case self::T_DATE:
							self::$_parser->eat('DATE', null);
							break;
						case self::T_HELP:
							self::$_parser->eat('HELP', null);
							break;

						case self::T_STRING:
							self::$_parser->eat('STRING', $token[2]);
							break;
						case self::T_NUMBER:
							self::$_parser->eat('NUMBER', doubleval($token[2]));
							break;
						case self::T_SYMBOL:
							self::$_parser->eat("'{$token[2]}'", null);
							break;
						case self::T_EOL:
							self::$_parser->eat('EOL', null);
							break;
						default:
							throw new HfosCommandRunnerException('Undefined token '.print_r($token, true));
					}
				}
				self::$_parser->eat_eof();
			}
			catch(parse_error $e){
				throw new HfosCommandRunnerException($e->getMessage().' at line '.$token[0]);
			}
		}
	}

	public static function setOpCode($code)
	{
		self::$_opCode = $code;
	}

	public static function getOpCode()
	{
		return self::$_opCode;
	}

}