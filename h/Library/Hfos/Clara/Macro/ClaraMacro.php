<?php

class ClaraMacroException extends Exception {

}

class ClaraMacroRunnner {

	private static $_symbolTable = array();

	private static $_insideStatementEval = false;

	private static function _stringCast($claraVal){
		$value = null;
		switch($claraVal['type']){
			case 'string':
				return $claraVal;
			case 'interval':
				$value = $claraVal['value'];
				break;
			case 'number':
				$value = strval($claraVal['value']);
				break;
			default:
				throw new ClaraMacroException('El tipo de valor '.$claraVal['type'].' no es soportado');
		}
		return array('type' => 'string', 'value' => $value);
	}

	private static function _evalLessOp($leftOp, $rightOp){
		if($leftOp['type']!='number'&&$leftOp['type']!='string'){
			$leftOp = self::_stringCast($leftOp);
		}
		if($rightOp['type']!='number'&&$rightOp['type']!='string'){
			$rightOp = self::_stringCast($rightOp);
		}
		return $leftOp['value']<$rightOp['value'];
	}

	private static function _evalGreaterOp($leftOp, $rightOp){
		if($leftOp['type']!='number'&&$leftOp['type']!='string'){
			$leftOp = self::_stringCast($leftOp);
		}
		if($rightOp['type']!='number'&&$rightOp['type']!='string'){
			$rightOp = self::_stringCast($rightOp);
		}
		//echo $leftOp['value'], ' ', $rightOp['value'], '<br/>';
		return $leftOp['value']>$rightOp['value'];
	}

	private static function _evalAdditionOp($leftOp, $rightOp){
		if($leftOp['type']!='number'&&$leftOp['type']!='string'){
			$leftOp = self::_numberCast($leftOp);
		}
		if($rightOp['type']!='number'&&$rightOp['type']!='string'){
			$rightOp = self::_numberCast($rightOp);
		}
		return array('type' => 'number', 'value' => $leftOp['value']+$rightOp['value']);
	}

	private static function _evalSubtractionOp($leftOp, $rightOp){
		if($leftOp['type']!='number'&&$leftOp['type']!='string'){
			$leftOp = self::_numberCast($leftOp);
		}
		if($rightOp['type']!='number'&&$rightOp['type']!='string'){
			$rightOp = self::_numberCast($rightOp);
		}
		return array('type' => 'number', 'value' => $leftOp['value']-$rightOp['value']);
	}

	private static function _evalMultiplicationOp($leftOp, $rightOp){
		if($leftOp['type']!='number'&&$leftOp['type']!='string'){
			$leftOp = self::_numberCast($leftOp);
		}
		if($rightOp['type']!='number'&&$rightOp['type']!='string'){
			$rightOp = self::_numberCast($rightOp);
		}
		return array('type' => 'number', 'value' => $leftOp['value']*$rightOp['value']);
	}

	private static function _evalDivisionOp($leftOp, $rightOp){
		if($leftOp['type']!='number'&&$leftOp['type']!='string'){
			$leftOp = self::_numberCast($leftOp);
		}
		if($rightOp['type']!='number'&&$rightOp['type']!='string'){
			$rightOp = self::_numberCast($rightOp);
		}
		return array('type' => 'number', 'value' => $leftOp['value']/$rightOp['value']);
	}

	private static function _evalCompareOp($leftOp, $rightOp){
		$leftOp = self::_evalExpression($leftOp);
		$rightOp = self::_evalExpression($rightOp);
		return $leftOp['value']==$rightOp['value'];
	}

	private static function _evalBinOp($expression){
		$leftOp = self::_evalExpression($expression[2]);
		$rightOp = self::_evalExpression($expression[3]);
		switch($expression[1]){
			case '<':
				return self::_evalLessOp($leftOp, $rightOp);
				break;
			case '>':
				return self::_evalGreaterOp($leftOp, $rightOp);
				break;
			case '+':
				return self::_evalAdditionOp($leftOp, $rightOp);
				break;
			case '-':
				return self::_evalSubtractionOp($leftOp, $rightOp);
				break;
			case '*':
				return self::_evalMultiplicationOp($leftOp, $rightOp);
				break;
			case '/':
				return self::_evalDivisionOp($leftOp, $rightOp);
				break;
			default:
				throw new ClaraMacroException('El operador binario '.$expression[1].' no está soportado');
		}
	}

	private static function _readSymbol($expression){
		if(isset($expression[2])){
			$symbolName = $expression[1].'.'.$expression[2];
		} else {
			$symbolName = $expression[1];
		}
		if(isset(self::$_symbolTable[$symbolName])){
			return self::$_symbolTable[$symbolName];
		} else {
			if(isset($expression[2])){
				if(!isset(self::$_symbolTable[$expression[1]])){
					throw new ClaraMacroException('La variable '.$expression[1].' no ha sido definida');
				} else {
					throw new ClaraMacroException('La variable '.$expression[1].' no tiene un campo '.$expression[2].' definido');
				}
			} else {
				throw new ClaraMacroException('La variable '.$expression[1].' no ha sido definida');
			}
		}
	}

	private static function _evalPercentValue($expression){
		return array('type' => 'number', 'value' => $expression[1]/100);
	}

	private static function _evalDateInterval($expression){
		$claraVal = self::_evalExpression($expression[1]);
		if($claraVal['type']!='number'){
			$number = doubleval($claraVal['value']);
		} else {
			$number = $claraVal['value'];
		}
		switch($expression[2]){
			case 'ANOS':
				$hours = $number*24*365.25;
				break;
			case 'MESES':
				$hours = $number*24*30.4375;
				break;
			case 'DIAS':
				$hours = $number*24;
				break;
			default:
				throw new ClaraMacroException('El interválo de tiempo '.$expression[2].' no es soportado');
		}
		return array('type' => 'interval', 'value' => $hours);
	}

	private static function _getLiteralValue($expression){
		switch($expression[1]){
			case 'NUM':
				return array('type' => 'number', 'value' => $expression[2]);
			case 'STR':
				return array('type' => 'string', 'value' => $expression[2]);
			default:
				throw new ClaraMacroException('El tipo de valor literal '.$expression[2].' no es soportado');
		}
	}

	private static function _doVariableAssign($expression){
		$variable = $expression[1];
		if(isset($variable[2])){
			$symbolName = $variable[1].'.'.$variable[2];
		} else {
			$symbolName = $variable[1];
		}
		$claraVal = self::_evalExpression($expression[2]);
		self::$_symbolTable[$symbolName] = $claraVal;
		return $claraVal;
	}

	private static function _runEqualExpression($expression){
		if(!self::$_insideStatementEval){
			if($expression[1][0]=='VAR'){
				return self::_doVariableAssign($expression);
			} else {
				return self::_evalCompareOp($expression[1], $expression[2]);
			}
		} else {
			return self::_evalCompareOp($expression[1], $expression[2]);
		}
	}

	private static function _evalExpression($expression){
		$result = null;
		switch($expression[0]){
			case 'OP':
				$result = self::_evalBinOP($expression);
				break;
			case 'EQUAL':
				$result = self::_runEqualExpression($expression);
				break;
			case 'VAR':
				$result = self::_readSymbol($expression);
				break;
			case 'LITERAL':
				$result = self::_getLiteralValue($expression);
				break;
			case 'PERCENT':
				$result = self::_evalPercentValue($expression);
				break;
			case 'INTERVAL':
				$result = self::_evalDateInterval($expression);
				break;
			default:
				throw new ClaraMacroException('El tipo de expresión '.$expression[0].' no es soportado');
				break;
		}
		return $result;
	}

	private static function _runIfStatement($statement){
		self::$_insideStatementEval = true;
		$result = self::_evalExpression($statement[1]);
		self::$_insideStatementEval = false;
		if($result){
			self::_runStatementList($statement[2]);
		} else {
			if(isset($statement[3])){
				self::_runStatementList($statement[3]);
			}
		}
	}

	private static function _runPrintStatement($statement){
		$claraVal = self::_evalExpression($statement[1]);
		echo $claraVal['value'];
	}

	private static function _runStatement($statement){
		switch($statement[0]){
			case 'IF':
				self::_runIfStatement($statement);
				break;
			case 'EXPR':
				self::_evalExpression($statement[1]);
				break;
			case 'PRINT':
				self::_runPrintStatement($statement);
				break;
			default:
				throw new ClaraMacroException('El tipo de sentencia '.$statement[0].' no es soportada');
				break;
		}
	}

	private static function _runStatementList($statementList){
		if($statementList[0]=='STMT_LIST'){
			self::_runStatementList($statementList[1]);
			self::_runStatementList($statementList[2]);
		} else {
			self::_runStatement($statementList);
		}
	}

	public static function setSymbolValue($symbol, $type, $value){
		self::$_symbolTable[$symbol] = array(
			'type' => $type,
			'value' => $value
		);
	}

	public static function getSymbolValue($symbol){
		return self::$_symbolTable[$symbol];
	}

	public static function run($macro){
		self::_runStatementList($macro['macro']);
	}

}

/**
 * ClaraParser
 *
 * Permite parsear formulas y calcular su valor. Las formulas pueden incluir variables y numeros escalares
 */
class ClaraMacro {

	const T_VARIABLE = 0;
	const T_NUMBER = 1;
	const T_SYMBOL = 2;
	const T_IF = 3;
	const T_THEN = 4;
	const T_ELSE = 5;
	const T_END = 6;
	const T_YEAR = 7;
	const T_MONTH = 8;
	const T_DAY = 9;
	const T_HOUR = 10;
	const T_IS = 11;
	const T_EQUAL = 12;
	const T_FROM = 13;
	const T_OF = 14;
	const T_GREATER = 15;
	const T_LESS = 16;
	const T_DIVIDE = 17;
	const T_MULTIPLE = 18;
	const T_POR = 19;
	const T_STRING = 20;
	const T_PRINT = 21;
	const T_PLUS = 22;
	const T_MINUS = 23;
	const T_EOL = 24;

	/**
	 * Palabras reservas
	 *
	 * @var array
	 */
	private static $_reservedWords = array(
		'si' => 3,
		'entonces' => 4,
		'sino' => 5,
		'fin' => 6,
		'anos' => 7,
		'años' => 7,
		'meses' => 8,
		'dias' => 9,
		'días' => 9,
		'horas' => 10,
		'es' => 11,
		'igual' => 12,
		'del' => 13,
		'de' => 14,
		'mayor' => 15,
		'menor' => 16,
		'dividido' => 17,
		'multiplicado' => 18,
		'por' => 19,
		'imprimir' => 21,
		'más' => 22,
		'mas' => 22,
		'menos' => 23
	);

	/**
	 * Palabras de artículos
	 *
	 * @var array
	 */
	private static $_theWords = array(
		'a' => 0,
		'el' => 1,
		'la' => 2,
		'los' => 3,
		'las' => 4,
		'un' => 5,
		'una' => 6,
		'unos' => 7,
		'unas' => 8
	);

	/**
	 * Objeto de Parseo
	 *
	 * @var parse_engine
	 */
	private static $_parser = null;

	/**
	 * Valor calculado por la formula
	 *
	 * @var double
	 */
	private static $_procesedValue = null;

	/**
	 * Número de la línea a ejecutar
	 *
	 * @var int
	 */
	private static $_lineNumber = 1;

	/**
	 * Macro: Arbol sintactico parseado y simbolos estaticos
	 *
	 * @var array
	 */
	private static $_opCode = array(
		'memory' => array(),
		'macro' => array()
	);

	/**
	 * Prepara el parser para ser usado
	 *
	 */
	private static function _prepareParser(){
		if(self::$_parser===null){
			Core::importFromLibrary('Lime', 'parse_engine.php');
			Core::importFromLibrary('Hfos', 'Clara/Macro/MacroParser.php');
			self::$_parser = new parse_engine(new MacroParser());
		}
	}

	/**
	 * Genera un listado de tokens a parsear
	 *
	 * @param	string $line
	 * @return	array
	 */
	private static function _tokenize($line){
		$tokens = array();
		while(i18n::strlen($line)){
			$line = trim($line, " \t");
			if(preg_match('/^[0-9]+(\.[0-9]*)?/', $line, $regs)){
				$tokens[] = array(self::$_lineNumber, self::T_NUMBER, $regs[0]);
				$line = i18n::substr($line, strlen($regs[0]));
			} else {
				if(preg_match('/^"(.*)"/', $line, $regs)){
					$tokens[] = array(self::$_lineNumber, self::T_STRING, $regs[1]);
					$line = i18n::substr($line, strlen($regs[0]));
				} else {
					if(preg_match('/^[A-Za-zÁÉÍÓÚÑáéíóúñ_]+/i', $line, $regs)){
						$regs[0] = i18n::strtolower($regs[0]);
						if(!isset(self::$_theWords[$regs[0]])){
							if(isset(self::$_reservedWords[$regs[0]])){
								$tokens[] = array(self::$_lineNumber, self::$_reservedWords[$regs[0]], $regs[0]);
							} else {
								$tokens[] = array(self::$_lineNumber, self::T_VARIABLE, $regs[0]);
							}
						}
						$line = i18n::substr($line, strlen($regs[0]));
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
		//Debug::add($tokens);
		return $tokens;
	}

	/**
	 * Busca errores sintácticos en una macro de nomina
	 *
	 * @param string $stringMacro
	 */
	public static function parse($stringMacro, $variable=null){
		if($variable!==null){
			$stringMacro = self::findVariables($stringMacro, $variable);
		}
		$stringMacro = str_replace("\r\n", "\n", $stringMacro);
		$stringMacro = preg_replace("/[\n]+/", "\n", $stringMacro);
		$stringMacro = trim($stringMacro);
		if($stringMacro){
			$stringMacro.="\n";
			self::_prepareParser();
			try {
				self::$_lineNumber = 1;
				self::$_parser->reset();
				foreach(self::_tokenize($stringMacro) as $token){
					switch($token[1]){
						case self::T_IF:
							self::$_parser->eat('SI', null);
							break;
						case self::T_THEN:
							self::$_parser->eat('ENTONCES', null);
							break;
						case self::T_ELSE:
							self::$_parser->eat('SINO', null);
							break;
						case self::T_END:
							self::$_parser->eat('FIN', null);
							break;
						case self::T_IS:
							self::$_parser->eat('ES', null);
							break;
						case self::T_EQUAL:
							self::$_parser->eat('IGUAL', null);
							break;
						case self::T_FROM:
							self::$_parser->eat('DEL', null);
							break;
						case self::T_OF:
							self::$_parser->eat('DE', null);
							break;
						case self::T_GREATER:
							self::$_parser->eat('MAYOR', null);
							break;
						case self::T_LESS:
							self::$_parser->eat('MENOR', null);
							break;
						case self::T_DIVIDE:
							self::$_parser->eat('DIVIDIDO', null);
							break;
						case self::T_MULTIPLE:
							self::$_parser->eat('MULTIPLICADO', null);
							break;
						case self::T_POR:
							self::$_parser->eat('POR', null);
							break;
						case self::T_PLUS:
							self::$_parser->eat('MAS', null);
							break;
						case self::T_MINUS:
							self::$_parser->eat('MENOS', null);
							break;
						case self::T_PRINT:
							self::$_parser->eat('IMPRIMIR', null);
							break;
						case self::T_VARIABLE:
							self::$_parser->eat('var', $token[2]);
							break;
						case self::T_STRING:
							self::$_parser->eat('TEXTO', $token[2]);
							break;
						case self::T_NUMBER:
							self::$_parser->eat('NUMERO', doubleval($token[2]));
							break;
						case self::T_SYMBOL:
							self::$_parser->eat("'{$token[2]}'", null);
							break;
						case self::T_YEAR:
							self::$_parser->eat('ANOS', null);
							break;
						case self::T_MONTH:
							self::$_parser->eat('MESES', null);
							break;
						case self::T_EOL:
							self::$_parser->eat('EOL', null);
							break;
						default:
							throw new ClaraMacroException('Token indefinido '.print_r($token, true));
					}
				}
				self::$_parser->eat_eof();
			}
			catch(parse_error $e){
				throw new ClaraMacroException($e->getMessage().' en la línea '.$token[0]);
			}
		}
	}

	public static function findVariables($macro, $variable=null){
		$macro = i18n::toAscii($macro);
		if($variable!=null){
			$variable = i18n::toAscii($variable);
			$validVariable = preg_replace('/[ ]+/', '', $variable);
			$macro = preg_replace('/'.$variable.'/iu', $validVariable, $macro);
		}

		//Incapacidades
		$macro = preg_replace('/incapacidades[ ]+remuneradas/i', 'incapacidadesRemuneradas', $macro);
		$macro = preg_replace('/incapacidades[ ]+no[ ]+remuneradas/i', 'incapacidadesNoRemuneradas', $macro);

		//Licencias
		$macro = preg_replace('/licencias[ ]+remuneradas/i', 'incapacidadesRemuneradas', $macro);
		$macro = preg_replace('/licencias[ ]+no[ ]+remuneradas/i', 'licenciasNoRemuneradas', $macro);

		//Suspensiones
		$macro = preg_replace('/suspensiones[ ]+remuneradas/i', 'suspensionesRemuneradas', $macro);
		$macro = preg_replace('/suspensiones[ ]+no[ ]+remuneradas/i', 'suspensionesNoRemuneradas', $macro);

		$macro = preg_replace('/es[ ]+primera[ ]+quincena/i', 'primeraQuincena', $macro);
		$macro = preg_replace('/es[ ]+la[ ]+primera[ ]+quincena/i', 'primeraQuincena', $macro);
		$macro = preg_replace('/primera[ ]+quincena/i', 'primeraQuincena', $macro);

		$macro = preg_replace('/es[ ]+segunda[ ]+quincena/i', 'segundaQuincena', $macro);
		$macro = preg_replace('/es[ ]+la[ ]+segunda[ ]+quincena/i', 'segundaQuincena', $macro);
		$macro = preg_replace('/segunda[ ]+quincena/i', 'segundaQuincena', $macro);

		$macro = preg_replace('/aportes[ ]+salud[ ]+empleados/i', 'empleados.aportesSalud', $macro);
		$macro = preg_replace('/aportes[ ]+salud[ ]+de[ ]+empleados/i', 'empleados.aportesSalud', $macro);

		$macro = preg_replace('/aportes[ ]+pension[ ]+empleados/i', 'empleados.aportesPension', $macro);
		$macro = preg_replace('/aportes[ ]+pension[ ]+de[ ]+empleados/i', 'empleados.aportesPension', $macro);

		//Valor Hora
		$macro = preg_replace('/valor[ ]+hora[ ]+trabajada/i', 'valorHora', $macro);
		$macro = preg_replace('/valor[ ]+hora/i', 'valorHora', $macro);
		$macro = preg_replace('/valor[ ]+de[ ]+la[ ]+hora/i', 'valorHora', $macro);
		$macro = preg_replace('/valor[ ]+de[ ]+la[ ]+trabajada/i', 'valorHora', $macro);

		//Horas y Dias Trabajados
		$macro = preg_replace('/horas[ ]+trabajadas/i', 'horasTrabajadas', $macro);
		$macro = preg_replace('/dias[ ]+trabajados/i', 'diasTrabajados', $macro);

		//Porcentaje Recargo
		$macro = preg_replace('/porcentaje[ ]+de[ ]+recargo/i', 'porcentajeRecargo', $macro);
		$macro = preg_replace('/porcentaje[ ]+recargo/i', 'porcentajeRecargo', $macro);

		return $macro;
	}

	public static function setMacroCode($macro){
		self::$_opCode['macro'] = $macro;
	}

	public static function getOpCode(){
		return self::$_opCode;
	}

	public static function setSymbolValue($symbol, $type, $value){
		ClaraMacroRunnner::setSymbolValue($symbol, $type, $value);
	}

	public static function getSymbolValue($symbol){
		return ClaraMacroRunnner::getSymbolValue($symbol);
	}

	public static function addStaticSymbol($qualified1, $qualified2=null){
		if($qualified2===null){
			self::$_opCode['memory'][$qualified1] = true;
		} else {
			self::$_opCode['memory'][$qualified1.'.'.$qualified2] = true;
		}
	}

	public static function run(){
		ClaraMacroRunnner::run(self::$_opCode);
	}

}