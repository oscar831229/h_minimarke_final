<?php

/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category 	Kumbia
 * @package 	Report
 * @subpackage 	Adapters
 * @copyright	Copyright (c) 2008-2010 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @version 	$Id: Csv.php 122 2010-02-11 19:09:18Z gutierrezandresfelipe $
 */

/**
 * CsvReport
 *
 * Adaptador que permite generar reportes en Texto Plano CSV
 *
 * @category 	Kumbia
 * @package 	Report
 * @subpackage 	Adapters
 * @copyright	Copyright (c) 2008-2010 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @abstract
 */
class CsvReport extends ReportAdapter implements ReportInterface {

	/**
	 * Salida HTML
	 *
	 * @var string
	 */
	private $_output;

	/**
	 * Tamaño de texto predeterminado
	 *
	 * @var int
	 * @static
	 */
	private static $_defaultFontSize = 12;


	/**
	 * Fuente de texto predeterminado
	 *
	 * @var int
	 * @static
	 */
	private static $_defaultFontFamily = 'Lucida Console';

	/**
	 * Alto de cada fila
	 *
	 * @var int
	 */
	private $_rowHeight = 0;

	/**
	 * Altura del encabezado
	 *
	 * @var int
	 */
	private $_headerHeight = 0;

	/**
	 * Numero total de paginas del reporte
	 *
	 * @var int
	 */
	private $_totalPages = 0;

	/**
	 * Totales de columnas
	 *
	 * @var array
	 */
	protected $_totalizeValues = array();

	/**
	 * Formatos de Columnas
	 *
	 * @var array
	 */
	private $_columnFormats = array();

	/**
	 * Número de columnas del reporte
	 *
	 * @var int
	 */
	private $_numberColumns = null;

	/**
	 * Indica si el reporte debe ser volcado a disco en cuanto se agregan los datos
	 *
	 * @var unknown_type
	 */
	protected $_implicitFlush = false;

	/**
	 * Handler al archivo temporal donde se volca el reporte
	 *
	 * @var handler
	 */
	private $_tempFile;

	/**
	 * Nombre del archivo temporal donde se volca el reporte
	 *
	 * @var string
	 */
	private $_tempFileName;

	/**
	 * Indica si el volcado del reporte ha sido iniciado
	 *
	 * @var boolean
	 */
	private $_started = false;

	/**
	 * Indica si el volcado del reporte ha sido iniciado
	 *
	 * @var boolean
	 */
	private $_model = null;

	public function _start() {
		throw new Exception('Este reporte no es soportado para CSV por favor intentar con formato de Excel');
	}
	
	public function _finish() {
		throw new Exception('Este reporte no es soportado para CSV por favor intentar con formato de Excel');
	}
	

	/**
	 * Renombra el archivo temporal del volcado al nombre dado por el usuario
	 *
	 * @param	string $path
	 * @return	boolean
	 */
	public function outputToFileCsv($path, $tableName, $fileName, $where=false, $write = true){
			
		$db = DbBase::rawConnect();

		$schema = '';
		$config2 = CoreConfig::readEnviroment();

		$schema = '';
		if (isset($config2->database->name)) {
			$schema = $config2->database->name;
		}
		
		$model = EntityManager::getEntityInstance($tableName);
		$modelTableName = $model->getSource();
		
		//file_put_contents($path, "TEMPORALY");
		
		$sqlWhere = '';
		if ($where) {
			$sqlWhere = "WHERE $where";
		}

		if ($write === true) {
			
			$query = "SELECT * INTO OUTFILE '".KEF_ABS_PATH.$path."'
				FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
				LINES TERMINATED BY '\n'
				FROM $schema.$modelTableName ".$sqlWhere." ORDER BY 1 ASC";
			
			//throw new Exception($query);
			$listQuery = $db->query($query);
			
			if(move_uploaded_file("/tmp/".$fileName, KEF_ABS_PATH.$path)){
				return basename('/'.$path);
			}
		} else {
			$query = "SELECT * FROM $schema.$modelTableName ".$sqlWhere." ORDER BY 1 ASC";
			
			$output = "";
			$attributes = null;
			$listQuery = $db->query($query);
			$db->setFetchMode($db::DB_ASSOC);
			while ($record = $db->fetchArray($listQuery)) {
				$output .= implode("|", array_values($record)) . PHP_EOL;
			}

			header('Content-type: text/plain');
			echo $output;
			exit;
		}
	}

	/**
	 * Devuelve la extension del archivo recomendada
	 *
	 * @return string
	 */
	protected function getFileExtension(){
		return 'csv';
	}

}
