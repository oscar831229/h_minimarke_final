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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * ExcluirController
 *
 * Excluir Movimiento
 *
 */
class ExcluirController extends ApplicationController {

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = new Date($empresa->getFCierrec());
		$fechaCierre->addDays(1);

		$fechaCierre2 = clone $fechaCierre;
		$fechaCierre2->toLastDayOfMonth();

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		Tag::displayTo('fechaIni', $fechaCierre->getDate());
		Tag::displayTo('fechaFin', $fechaCierre2->getDate());

		$this->setParamToView('consolidados', $this->Consolidados->find("estado='A'"));

		$this->setParamToView('message', 'Seleccione el servidor al que desea exportar el movimiento y la fecha');
	}

	public function exportarAction()
	{

		$this->setResponse('json');

		$year = $this->getPost('year', 'int');
		$month = $this->getPost('month', 'int');
		$serverId = $this->getPost('servidorId', 'int');
		$reportType = $this->getPostParam('reportType', 'alpha');

		try {
			$fusion = new AuraFusion();
			$fusion->login($serverId);
			$fusion->consolidate($year, $month, $reportType);
		}
		catch(AuraException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	public function exportarCsvAction()
	{

		$this->setResponse('json');

		$origen   = $this->getPost('origen');
		$fechaIni = $this->getPost('fechaIni', 'date');
		$fechaFin = $this->getPost('fechaFin', 'date');
		$comprob  = $this->getPost('comprob', 'comprob');
		$reportType = "csv";

		try {
			$report = ReportBase::factory($reportType);
			$reportName = Router::getController().'-'.mt_rand(0, 100000);
			$fileName = $reportName.".txt";
			$path = 'public/temp/'.$fileName;

			$where = "fecha>='$fechaIni' AND fecha<='$fechaFin'";
			if ($comprob && $comprob!='@') {
				$where .= " AND comprob='$comprob'";
			}

			$modelName = 'Movi';
			$tableName = 'movi';
			if ($origen == 'N') {
				$tableName = 'movi_niif';
				$modelName = 'MoviNiif';
			}

			$db = DbBase::rawConnect();

			$schema = '';
			$config2 = CoreConfig::readEnviroment();

			$schema = '';
			if (isset($config2->database->name)) {
				$schema = $config2->database->name;
			}

			$model = EntityManager::getEntityInstance($modelName);
			$rows = $model->find($where);
			if (!count($rows)) {
				throw new Exception("No se encontraron registros");
			}
			$modelTableName = $model->getSource();

			//file_put_contents($path, "TEMPORALY");

			$sqlWhere = '';
			if ($where) {
				$sqlWhere = "WHERE $where";
			}
			$query = "
				SELECT comprob,numero,DATE_FORMAT(fecha, \"%m/%d/%Y\"),cuenta,nit,centro_costo,valor,deb_cre,descripcion,tipo_doc,numero_doc,base_grab,conciliado,IF(f_vence IS NULL,DATE_FORMAT(fecha, \"%m/%d/%Y\"),DATE_FORMAT(f_vence, \"%m/%d/%Y\")),numfol INTO OUTFILE '".KEF_ABS_PATH.$path."'   FIELDS TERMINATED BY '|' OPTIONALLY ENCLOSED BY ''   LINES TERMINATED BY '\n'   FROM $schema.$modelTableName ".$sqlWhere;

			//throw new Exception($query);
			$listQuery = $db->query($query);

			move_uploaded_file("/tmp/".$fileName, KEF_ABS_PATH.$path);
			sleep(10);

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

}
