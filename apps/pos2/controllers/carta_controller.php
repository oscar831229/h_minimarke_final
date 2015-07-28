<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * CartaController
 *
 * Permite actualizar los items en los ambientes y sus valores
 */
class CartaController extends ApplicationController
{

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
	}


	protected function _process($modelName, $uniqueField)
	{

		$request = ControllerRequest::getInstance();

		$archivo = $request->getParamFile('archivo');
		if ($archivo==false) {
			Flash::error('El archivo no se pudo cargar al servidor');
		} else {

			if (!preg_match('/\.xlsx$/', $archivo->getFileName())) {
				Flash::error('El archivo cargado parece no ser de Microsoft Excel 2007 o superior');
				return;
			}

			try {

				$filePath = KEF_ABS_PATH.'public/temp/c'.time().'.xlsx';
				$archivo->moveFileTo($filePath);

				Core::importFromLibrary('PHPExcel', 'Classes/PHPExcel.php');
				$reader = PHPExcel_IOFactory::createReader('Excel2007');
				$reader->setReadDataOnly(true);
			}
			catch(Exception $e){
				Flash::error('El archivo está corrupto. '.$e->getMessage());
				return;
			}

			$phpExcel = $reader->load($filePath);
			$worksheet = $phpExcel->getActiveSheet();

			$baseModel = new $modelName();
			$attributes = $baseModel->getAttributes();

			$countCache = array();
			$findFirstCache = array();
			try {
				set_time_limit(0);

				foreach($worksheet->getRowIterator() as $line => $row){

					$model = false;
					$numberCells = 0;
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);

					$model = clone $baseModel;

					foreach($cellIterator as $position => $cell){

						if ($position > 0) {
							$value = str_replace(array('"', "'"), '', trim($cell->getCalculatedValue()));
							$model->writeAttribute($attributes[$position], $value);
						}

						$numberCells++;
						unset($cell);
					}

					if ($uniqueField) {
						if (!$this->$modelName->count($uniqueField.'="'.$model->$uniqueField.'"')) {
							if ($model->save()==false) {
								Flash::error('Error grabando registro en la línea '.($line+1).':');
								foreach ($model->getMessages() as $message) {
									Flash::error($message->getMessage());
								}
							} else {
								Flash::success('Registro con '.$uniqueField.'='.$model->$uniqueField.' guardado con éxito');
							}
						} else {
							Flash::notice('Registro con '.$uniqueField.'='.$model->$uniqueField.' ya existía');
						}
					} else {
						if ($model->save()==false) {
							Flash::error('Error grabando registro en la línea '.($line+1).':');
							foreach ($model->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
						} else {
							Flash::success('Registro con '.$model->id.' guardado con éxito');
						}
					}

					$line++;
					unset($cellIterator);
					unset($row);
					unset($model);
				}
			}
			catch(TransactionFailed $e){

			}

			$this->routeTo('action: index');
		}
		//echo '</div>';
	}

	public function indexAction()
	{

	}

	public function subirAction()
	{

		$request = ControllerRequest::getInstance();

		switch ($request->getParamPost('type')) {
			case 'menus-items':
				$this->_process('MenusItems', 'nombre');
				break;
			case 'salon-menus-items':
				$this->_process('SalonMenusItems', null);
				break;
		}
	}

}

