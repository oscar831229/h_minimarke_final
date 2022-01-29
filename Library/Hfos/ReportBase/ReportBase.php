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

/**
 * ReportBase
 *
 * Clase base para crear todos los reportes del Back-Office
 */
class ReportBase extends Report
{

	protected $_adapter;

	/**
	 * Indica si se ha inicializado el ReportBase
	 *
	 * @var boolean
	 */
	private static $_initialized = false;

	/**
	 * Inicializa el ReportBase
	 *
	 */
	private static function _initialize(){
		if(self::$_initialized==false){
			set_time_limit(0);
			ReportComponent::load(array('Text', 'Style', 'Format', 'RawColumn'));
			self::$_initialized = true;
		}
	}

	/**
	 * Constructor de ReportBase
	 *
	 * @param string $adapter
	 */
	public function __construct($adapter)
	{
		parent::__construct($adapter);
		$this->setPagination(false);
		$this->setRowsPerPage(35);
		$this->_adapter = $adapter;
	}

	/**
	 * Crea un reporte para el Back-Office
	 *
	 * @param string $adapter
	 */
	public static function factory($adapter){
		switch($adapter){
			case 'html':
				$adapter = 'Html';
				break;
			case 'pdf':
				$adapter = 'Pdf';
				break;
			case 'excel':
				$adapter = 'Excel';
				break;
			case 'text':
				$adapter = 'Text';
				break;
			case 'csv':
				$adapter = 'Csv';
				break;
			default:
				$adapter = 'Html';
				break;
		}
		self::_initialize();
		return new self($adapter);
	}

	/**
	 * Establece el encabezado estándar para todos los reportes
	 *
	 * @param array $header
	 */
	public function setHeader($header, $showFechaImpresion=true, $showNit=false){
		$empresa = EntityManager::getEntityInstance('Empresa')->findFirst();

		if($showNit==true){

  			$lengthNit = strlen($empresa->getNit());
			//$nitFormated = number_format(substr($empresa->getNit(), 0, $lengthNit-1),0,'.','.').'-'.substr($empresa->getNit(), $lengthNit-1);
			$nitFormated = number_format((double) substr($empresa->getNit(), 0),0,'.','.');

	  		array_unshift($header, new ReportText('NIT No : '.$nitFormated, array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
	 		)));

	  	}

	  	array_unshift($header, new ReportText($empresa->getNombre(), array(
			'fontSize' => 16,
   			'fontWeight' => 'bold',
   			'textAlign' => 'center'
  		)));


		array_unshift($header, new ReportText('%pageNumber%', array(
			'fontSize' => 9,
			'textAlign' => 'right'
  		)));
  		if ($showFechaImpresion == true) {
	  		$header[] = new ReportText('Fecha Impresión: '.date('Y-m-d h:i a'), array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'right'
	 		));
	 	}
 		parent::setHeader($header);
	}

	/**
	 * Obtiene el número máximo de filas que pueden ser procesadas de
	 * acuerdo a la memoria disponible por proceso
	 *
	 * @return int
	 */
	public function getMaxTotalRows()
	{
		$memoryLimit = (int)ini_get('memory_limit');
		if ($this->_adapter == 'Excel' || $this->_adapter == 'Pdf') {
			return (int) ($memoryLimit * 5500 / 128);
		} else {
			return (int) ($memoryLimit * 20000 / 128);
		}
	}

}
