<?php
error_reporting(E_ERROR && E_WARNING);
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

class HyperFormException extends CoreException
{

}

class HyperFormLoadException extends HyperFormException
{

}

/**
 * HyperForm
 *
 * Clase para generar formularios ABM basicos
 *
 */
class HyperForm extends UserComponent
{

	/**
	 * Pantalla de buscar
	 *
	 * @param array $config
	 */
	public static function queryPage($config){

		$linguistics = new Linguistics();
		$request = ControllerRequest::getInstance();

		echo '<div id="', Router::getController(), 'Form" class="hyperForm">

			<div class="hyToolbar">
				<table width="99%">
					<tr>
						<td align="left">
							<table>
								<tr>
									<td align="right" style="display:none">
										<input type="button" class="hyControlButton backButton" value="Volver" title="Volver [F10]"/>
									</td>
								</tr>
							</table>
						</td>
						<td align="right">
							<table cellspacing="0">
								<tr class="hyTrControlButtons">
									<td align="left" class="hyTdLeftControlButton" style="display:none"><input type="button" class="hyControlButton revisionButton" value="Revisiones" title="Revisiones"/></td>
									<td align="left" class="hyTdLeftControlButton" style="display:none"><input type="button" class="hyControlButton deleteButton" value="Eliminar" title="Editar [Suprimir]"/></td>
									<td align="left" class="hyTdLeftControlButton" style="display:none"><input type="button" class="hyControlButton editButton" value="Editar" title="Editar [Control-E]"/></td>
									<td align="left" class="hyTdLeftControlButton" style="display:none"><input type="button" class="hyControlButton loadButton" value="Cargar Archivo"/></td>
									<td align="left" class="hyTdLeftControlButton" style="display:none"><input type="button" class="hyControlButton saveButton" value="Grabar" title="Editar [F7]"/></td>';
									if(!isset($config['ignoreButtons'])||!in_array('import', $config['ignoreButtons'])){
										echo '<td align="left" class="hyTdLeftControlButton"><input type="button" class="hyControlButton importButton" value="Importar" title="Importar [Control-I]"/></td>';
									}
									echo '<td align="left" class="hyTdLeftControlButton"><input type="button" class="hyControlButton newButton" value="Crear ', ucfirst($config['single']), '" title="Crear [Control-N]"/></td>';
									if(isset($config['buttons'])){
										foreach($config['buttons'] as $caption => $button){
											if(isset($button['visible']['index'])){
												echo '<td align="left" class="hyTdLeftControlButton">
													<input type="button" class="hyControlButton ', $button['className'], '" value="', $caption, '" class="searchButton"/>
												</td>';
											}
										}
									}
								echo '<td><div class="hyToolbarSpinner" style="display:none"></div></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>

			<div class="messages">';
			if ($config['genre'] == 'M') {
				echo '<div class="notice">Ingrese un criterio de búsqueda ó presione "Consultar" para ver todos los '.$config['plural'].'</div>';
			} else {
				echo '<div class="notice">Ingrese un criterio de búsqueda ó presione "Consultar" para ver todas las '.$config['plural'].'</div>';
			}
			echo '</div>

			<div class="hySearchDiv hyFormDiv">';

		echo '<table class="hyTitle"><tr>';
		if (isset($config['icon'])) {
			echo '<td>', Tag::image('backoffice/icons/'.$config['icon']), '</td>';
		}
		echo '<td><h2>Consultar ', ucfirst($config['plural']), '</h2></td>
		</tr></table>';

		$controllerName = Router::getController();
		echo '<form autocomplete="off" name="hyperForm" class="hySearchForm" action="', Utils::getKumbiaUrl($controllerName.'/search'), '" method="post">';
		echo '<table align="center" cellspacing="0" cellpadding="0"><tr>';
		foreach($config['fields'] as $name => $component){
			if(isset($component['notSearch'])&&$component['notSearch']){
				continue;
			}
			echo '<tr><td align="right"><label for="', $name, '">', $component['single'], '</label></td><td>';
			switch ($component['type']) {
				case 'text':
					echo Tag::textField(array($name, 'size' => $component['size'], 'maxlength' => $component['maxlength']));
					break;

				case 'textarea':
					echo Tag::textArea(array($name, 'cols' => $component['cols'], 'rows' => $component['rows']));
					break;

				case 'closed-domain':
					if (isset($component['useDummy'])) {
						$useDummy = $component['useDummy'];
					} else {
						$useDummy = 'yes';
					}
					echo Tag::selectStatic(array($name, $component['values'], 'useDummy' => $useDummy));
					break;

				case 'relation':
					echo Tag::select(array(
						$name,
						self::getModel($component['relation'])->find(array('order' => $component['detail'])),
						'using' => $component['fieldRelation'].','.$component['detail'],
						'useDummy' => 'yes'
					));
					break;

				case 'int':
				case 'decimal':
					echo Tag::numericField(array(
						$name,
						'size' => $component['size'],
						'maxlength' => $component['maxlength']
					));
					break;

				case 'date':
					echo Tag::dateField(array(
						$name,
						'default' => $component['default'],
						'class' => 'date-field'
					));
					break;

				default:
					$className = $component['type'] . 'HyperComponent';
					if (class_exists($className, false) == false) {
						$path = KEF_ABS_PATH . 'Library/Hfos/HyperForm/Components/' . ucfirst($component['type']) . '.php';
						if (file_exists($path)) {
							require_once $path;
						} else {
							throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
						}
					}
					echo call_user_func_array(array($className, 'build'), array($name, $component, null, 'hySearchForm'));
			}
			echo '</td></tr>';
		}

		echo '
		<tr>
			<td></td>
			<td colspan="2" align="left">
				<table align="left" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<input type="button" value="Consultar" class="searchButton">
						</td>
						<td>
							<div class="hySpinner" style="display:none"></div>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>

		', HfosTag::reportTypeTag(false), '

		</form>

		</div>

		<div class="hyBrowseDiv hyFormDiv" style="display:none"></div>
		<div class="hyDetailsDiv hyFormDiv" style="display:none"></div>
		<div class="hyEditDiv hyFormDiv" style="display:none"></div>
		<div class="hyRcsDiv hyFormDiv" style="display:none"></div>
		<div class="hyNewDiv hyFormDiv" style="display:none"></div>
		<div class="hyImportDiv hyFormDiv" style="display:none"></div>

		</div>

		<script type="text/javascript">hyOL(HyperFormManager.add.bind(window, "',
			Router::getController(), '", "',
			ucfirst($config['plural']), '", "',
			($config['genre'] == 'F' ? 'la ' : 'el ').ucfirst($config['single']), '", "',
			$config['genre'], '", ';
		if(isset($config['detail'])){
			echo json_encode($config['detail']['fields']);
		} else {
			echo 'null';
		}
		echo ', false));</script>';

	}

	/**
	 * Realiza una búsqueda según los parámetros enviados y devuelve un resultado en JSON
	 * para ser construido en Javascript en el visualizar
	 *
	 * @param	Controller $controller
	 * @param	array $config
	 * @return	string
	 */
	public static function search($controller, $config, $conditions=array())
	{

		$response = ControllerResponse::getInstance();
		$request = ControllerRequest::getInstance();

		foreach ($config['fields'] as $name => $component)
		{
			if($request->isSetPostParam($name)){
				$value = $request->getParamPost($name);
				switch($component['type']){
					case 'int':
					case 'decimal':
						if($value!==''){
							$value = Filter::bring($value, 'double');
							$conditions[] = $name.' = '.$value;
						}
						break;
					case 'text':
					case 'textarea':
						if($value!==''){
							if(strpos($value, '%')===false){
								$value = preg_replace('/[ ]+/', '%', $value);
								$conditions[] = $name.' LIKE \'%'.$value.'%\'';
							} else {
								$conditions[] = $name.' LIKE \''.$value.'\'';
							}
						}
						break;
					case 'date':
						$value = Filter::bring($value, 'date');
						if($value!==''){
							$conditions[] = $name.' = \''.$value.'\'';
						}
						break;
					case 'closed-domain':
						if($value!=='@'){
							$conditions[] = $name.' = \''.$value.'\'';
						}
						break;
					case 'relation':
						if($value!=='@'){
							$conditions[] = $name.' = \''.$value.'\'';
						}
						break;
						//Agregada para campos de movilin en movihead de referencia
					case 'item':
						if($value!==''){
							//$conditions[] = $name.' = \''.$value.'\'';
            			}
            		    break;
					default:
						if($value!==''){
							$conditions[] = $name.' = \''.$value.'\'';
						}
						break;
				}
			}
		}

		$modelName = $config['model'];

		if(count($conditions)>0){
			$results = $controller->$modelName->find(array(join(' AND ', $conditions), 'order' => $config['preferedOrder']));
		} else {
			$results = $controller->$modelName->find(array('order' => $config['preferedOrder']));
		}

		$response->setResponseType(ControllerResponse::RESPONSE_OTHER);
		$response->setResponseAdapter('json');
		View::setRenderLevel(View::LEVEL_NO_RENDER);

		$reportType = $request->getParamPost('reportType', 'alpha');

		$numberResults = count($results);
		if ($numberResults > 0) {

			$charset = Hfos_Application::getAppCharset();
			if($reportType=='screen'){

				$headers = array();
				$preferedOrder = explode(' ', $config['preferedOrder']);
				foreach ($config['fields'] as $name => $component) {

					if (isset($component['notBrowse'])&&$component['notBrowse']) {
						continue;
					}

					if ($name==$preferedOrder[0]) {
						$headers[] = array(
							'ordered' => 'S',
							'type' => $component['type'],
							'name' => $component['single']
						);
					} else {
						$headers[] = array(
							'ordered' => 'N',
							'type' => $component['type'],
							'name' => $component['single']
						);
					}
				}

				$responseResults = array(
					'headers' => $headers
				);

				$data = array();
				$number = 0;
				$primaryKeys = $controller->$modelName->getPrimaryKeyAttributes();

				foreach ($results as $result) {

					$row = array(
						'primary' => array(),
						'data' => array()
					);

					foreach ($config['fields'] as $name => $component) {

						if (isset($component['notBrowse'])&&$component['notBrowse']) {
							continue;
						}

						$field = array(
							'key' => '',
							'value' => ''
						);

						if (isset($component['extraField'])&&$component['extraField']) {
							$value = $controller->getExtraField($name, $result);
						} else {
							$value = $result->readAttribute($name);
						}

						switch ($component['type']) {

							case 'closed-domain':
								$field['key'] = $name;
								if(isset($component['values'][$value])){
									$field['value'] = $component['values'][$value];
								} else {
									if($value===null){
										$field['value'] = '';
									} else {
										$field['value'] = $value;
									}
								}
								break;

							case 'relation':
								if(isset($component['cacher'])){
									$relatedRecord = call_user_func_array($component['cacher'], array($value));
								} else {
									$condition = $component['fieldRelation']." = '".$value."'";
									$relatedRecord = self::getModel($component['relation'])->findFirst($condition);
								}
								$field['key'] = $name;
								$field['value'] = $relatedRecord == false ? '' : $relatedRecord->readAttribute($component['detail']) ;
								break;

							case 'text':
							case 'date':
							case 'int':
							case 'textarea':
								$field['key'] = $name;
								if($value===null){
									$field['value'] = '';
								} else {
									if ($charset == 'latin1') {
										$field['value'] = utf8_encode($value);
									} else {
										$field['value'] = $value;
									}
								}
								break;

							case 'decimal':
								if ($value !== null) {
									if (isset($component['decimals'])) {
										$field['value'] = Currency::number($value, $component['decimals']);
									} else {
										$field['value'] = Currency::number($value, 3);
									}
								} else {
									$field['value'] = $value;
								}
								break;

							default:
								$className = $component['type'] . 'HyperComponent';
								if (class_exists($className, false) == false) {
									$path = KEF_ABS_PATH . 'Library/Hfos/HyperForm/Components/' . ucfirst($component['type']) . '.php';
									if (file_exists($path)) {
										require_once $path;
									} else {
										throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
									}
								}
								$field['value'] = call_user_func_array(array($className, 'getDetail'), array($value));
						}

						$row['data'][] = $field;
					}

					foreach ($primaryKeys as $primaryKey) {
						$row['primary'][] = $primaryKey . '=' . $result->readAttribute($primaryKey);
					}

					$data[] = $row;
					$number++;

					if ($number > 250) {
						break;
					}
				}

				$responseResults['data'] = $data;
				if ($numberResults < 250) {
					if ($numberResults > 1) {
						$message = 'Visualizando ' . $numberResults.' '.$config['plural'];
					} else {
						$message = 'Visualizando un ' . $config['single'];
					}
				} else {
					if ($config['genre'] == 'M') {
						$message = 'Hay ' . $numberResults . ' ' . $config['plural'].', visualizando los primeros 250';
					} else {
						$message = 'Hay ' . $numberResults . ' ' . $config['plural'].', visualizando las primeras 250';
					}
					$numberResults = 250;
				}

				return array(
					'status' 		=> 'OK',
					'type' 			=> $reportType,
					'message' 		=> $message,
					'numberResults' => $numberResults,
					'results' 		=> $responseResults
				);

			} else {
				return self::_makeReport($reportType, $config, $results, $controller);
				/*$url = self::_makeReport($reportType, $config, $results,$controller);
				return array(
					'status' => 'OK',
					'type' => $reportType,
					'url' => $url
				);*/
			}
		} else {
			return array(
				'status' => 'OK',
				'type' => $reportType,
				'numberResults' => $numberResults,
				'message' => 'No se encontraron resultados en la búsqueda'
			);
		}
	}

	/**
	 * Genera un reporte en el formato indicado
	 *
	 * @param	string $reportType
	 * @param	array $config
	 * @param	ActiveRecordResulset $results
	 * @return	array
	 */
	protected static function _makeReport($reportType, $config, $results, $controller)
	{

		$report = ReportBase::factory($reportType);

		//Creacion rapida de CSV
		if ($reportType == "csv") {
			$reportName = Router::getController() . '-' . mt_rand(0, 100000);
			$fileName = $reportName . ".csv";
			//$report->outputToFileCsv('public/temp/' . $fileName, $config["model"], $fileName);
			//return Core::getInstancePath() . 'temp/' . $fileName;
			return $report->outputToFileCsv(
				'public/temp/' . $fileName,
				$config["model"],
				$fileName,
				false,
				false
			);
		}

		//Otros metodos
  		$titulo = new ReportText('REPORTE DE '.i18n::strtoupper($config['plural']), array(
			'fontSize' => 16,
   			'fontWeight' => 'bold',
   			'textAlign' => 'center'
  		));

  		$numberFormat = new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		));

  		$report->setHeader(array($titulo));
  		$report->setDocumentTitle('Reporte de '.$config['plural']);

  		$headers = array();
  		$i = 0;
  		foreach ($config['fields'] as $name => $component)
  		{
  			if(isset($component['notReport'])&&$component['notReport']){
  				unset($config['fields'][$name]);
				continue;
			} else {
				if($component['type']=='decimal'){
					$report->setColumnFormat($i, $numberFormat);
				}
				$i++;
			}
  			$headers[] = i18n::strtoupper($component['single']);
		}

		//Se crea un callback en cada columna para modificaciones especiales
		if(method_exists($controller, 'afterReportHeader')){
		    $headers = $controller->afterReportHeader($headers);
		}
  		$report->setColumnHeaders($headers);

  		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));

		$leftColumn = new ReportStyle(array(
  			'textAlign' => 'left',
  			'fontSize' => 11,
  		));

  		$leftColumnBold = new ReportStyle(array(
  			'textAlign' => 'left',
  			'fontSize' => 11,
  			'fontWeight' => 'bold'
  		));

  		$rightColumn = new ReportStyle(array(
  			'textAlign' => 'right',
  			'fontSize' => 11,
  		));

		$rightColumnBold = new ReportStyle(array(
  			'textAlign' => 'right',
  			'fontSize' => 11,
  			'fontWeight' => 'bold'
  		));

		$report->start(false);

		$numberRows = 0;
		$totalRows = 0;
		$relationCache = array();
		$maxTotalRows = $report->getMaxTotalRows();
		$charset = Hfos_Application::getAppCharset();
		foreach ($results as $result)
		{
			$row = array();
			foreach ($config['fields'] as $name => $component)
			{
				switch($component['type']){
					case 'closed-domain':
						$value = $result->readAttribute($name);
						if(isset($component['values'][$value])){
							$row[] = $component['values'][$value];
						} else {
							if($value===null){
								$row[] = '';
							} else {
								$row[] = $value;
							}
						}
						break;
					case 'relation':
						$value = $result->readAttribute($name);
						$condition = $component['fieldRelation']." = '".$value."'";
						if(!isset($relationCache[$component['relation']][$condition])){
							$relationCache[$component['relation']][$condition] = self::getModel($component['relation'])->findFirst($condition);
						}
						$relatedRecord = $relationCache[$component['relation']][$condition];
						if($relatedRecord==false){
							$row[] = '';
						} else {
							$row[] = $relatedRecord->readAttribute($component['detail']);
						}
						unset($relatedRecord);
						unset($condition);
						break;
					case 'int':
					case 'decimal':
					case 'date':
					case 'text':
					case 'textarea':
						$field['key'] = $name;
						$value = $result->readAttribute($name);
						if($value===null){
							$row[] = '';
						} else {
							if($charset=='latin1'){
								$row[] = utf8_encode($value);
							} else {
								$row[] = $value;
							}
						}
						break;
					default:
						$value = $result->readAttribute($name);
						if($value===null){
							$row[] = '';
						} else {
							$className = $component['type'].'HyperComponent';
							if(class_exists($className, false)==false){
								$path = KEF_ABS_PATH.'Library/Hfos/HyperForm/Components/'.ucfirst($component['type']).'.php';
								if(file_exists($path)){
									require_once $path;
								} else {
									throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
								}
							}
							$row[] = call_user_func_array(array($className, 'getDetail'), array($value));
							unset($className);
						}
				}
				unset($value);
				unset($component);
			}

			//Se crea un callback en cada row para modificaciones especiales
			if(method_exists($controller, 'afterReportRow')){
			    $controller->afterReportRow($row, $config);
			}
			$report->addRow($row);
			if($numberRows>100){
				$numberRows = 0;
				GarbageCollector::collectCycles();
			}
			$numberRows++;
			if($totalRows>$maxTotalRows){
				break;
			}
			$totalRows++;
			unset($row);
			unset($result);
		}

		$report->finish();
		//$reportName = Router::getController().'-'.mt_rand(0, 100000);
		//$fileName = $report->outputToFile(sys_get_temp_dir() . '/' . $reportName);

		//return Core::getInstancePath() . 'out/' . $fileName;
		//
		
		echo $report->getOutput();
	}

	private static function _getRecordDetails($config, $record, $controller)
	{
		try {

			$data = array();
			$charset = Hfos_Application::getAppCharset();
			foreach ($config['fields'] as $attribute => $component)
			{
				if (isset($component['notDetails'])&&$component['notDetails']) {
					continue;
				}
				if (isset($component['extraField'])&&$component['extraField']) {
					if(method_exists($controller, 'getExtraField')){
						$value = $controller->getExtraField($attribute, $record);
					} else {
						$value = $record->readAttribute($attribute);
					}
				} else {
					$value = $record->readAttribute($attribute);
				}
				switch ($config['fields'][$attribute]['type']) {
					case 'closed-domain':
						if (isset($component['values'][$value])) {
							$fieldValue = $component['values'][$value];
						} else {
							if ($value===null) {
								$fieldValue = '';
							} else {
								$fieldValue = $value;
							}
						}
						break;
					case 'relation':
						$entity = EntityManager::getEntityInstance($component['relation']);
						$fieldValue = $entity->findFirst("{$component['fieldRelation']} = '$value'");
						$fieldValue = ($fieldValue == false) ? '' : $fieldValue->readAttribute($component['detail']);
						break;
					case 'decimal':
						$fieldValue = $value;
						if($fieldValue===null){
							$fieldValue = 0;
						} else {
							if(isset($component['decimals'])){
								$fieldValue = Currency::number($fieldValue, $component['decimals']);
							} else {
								$fieldValue = Currency::number($fieldValue, 3);
							}
						}
						break;
					case 'int':
					case 'date':
					case 'text':
					case 'textarea':
						$fieldValue = $value;
						if($fieldValue===null){
							$fieldValue = '';
						} else {
							if($charset=='latin1'){
								$fieldValue = utf8_encode($fieldValue);
							}
						}
						break;
					default:
						$className = $component['type'].'HyperComponent';
						if(class_exists($className, false)==false){
							$path = KEF_ABS_PATH.'Library/Hfos/HyperForm/Components/'.ucfirst($component['type']).'.php';
							if(file_exists($path)){
								require_once $path;
							} else {
								throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
							}
						}
						$fieldValue = (string) call_user_func_array(array($className, 'getDetail'), array($value));
						$fieldValue = utf8_encode($fieldValue);
						break;
				}
				$data[] = array(
					'name' => $attribute,
					'caption' => $config['fields'][$attribute]['single'],
					'value' => $fieldValue
				);
			}
			return $data;
		}
		catch(Exception $e) {
			return array();
		}
	}

	/**
	 * Devuelve un JSON que permite visualizar los detalles de un registro
	 *
	 * @param Controller $controller
	 * @param array $config
	 */
	public static function getRecordDetails($controller, $config, $conditions=array()){

		$linguistics = new Linguistics();
		$response = ControllerResponse::getInstance();
		$request = ControllerRequest::getInstance();

		$response->setResponseType(ControllerResponse::RESPONSE_OTHER);
		$response->setResponseAdapter('json');
		View::setRenderLevel(View::LEVEL_NO_RENDER);

		foreach ($config['fields'] as $name => $component)
		{
			if (isset($component['primary'])&&$component['primary']) {
				if ($request->isSetPostParam($name)) {
					$value = $request->getParamPost($name);
					$conditions[] = $name . ' = \'' . $value . '\'';
				}
			}
			unset($component);
		}

		if (count($conditions) > 0) {
			$modelName = $config['model'];
			$record = $controller->$modelName->findFirst(array(join(' AND ', $conditions)));
			if ($record == false) {
				return array(
					'status' => 'FAILED',
					'message' => 'El registro no existe '.join(' AND ', $conditions)
				);
			}
			$data = self::_getRecordDetails($config, $record, $controller);

			if ($request->isSetPostParam('n')) {
				$number = $request->getParamPost('n', 'int');
				return array(
					'status'  => 'OK',
					'number'  => $number,
					'message' => 'Visualizando '.$linguistics->the($config['single']).' '.($number+1).' de ',
					'data' 	  => $data
				);
			} else {
				return array(
					'status' => 'OK',
					'message' => 'Visualizando '.$linguistics->a($config['single']),
					'data' => $data
				);
			}
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'No se enviaron condiciones de búsqueda'
			);
		}

	}

	/**
	 * Pantalla de crear nuevo
	 *
	 * @param array $config
	 */
	public static function newPage($config){

		$controllerName = Router::getController();
		$request = ControllerRequest::getInstance();

		$modelName = $config['model'];
		$model = EntityManager::getEntityInstance($modelName);
		$notNullAttributes = $model->getNotNullAttributes();
		foreach($notNullAttributes as $attribute){
			if(isset($config['fields'][$attribute])){
				$config['fields'][$attribute]['notNull'] = true;
			}
		}
		echo '<table class="hyTitle"><tr>';
		if(isset($config['icon'])){
			echo '<td>', Tag::image('backoffice/icons/'.$config['icon']), '</td>';
		}
		echo '<td>';
		if($config['genre']=='M'){
			echo '<h2>Crear un ', $config['single'], '</h2>';
		} else {
			echo '<h2>Crear una ', $config['single'], '</h2>';
		}
		echo '</td></tr></table>';

		echo '<form autocomplete="off" name="hyperForm" class="hySaveForm" action="', Utils::getKumbiaUrl($controllerName.'/save'), '" method="post">';
		if(isset($config['detail'])||isset($config['extras'])){
			echo '<div class="tabbed_area"><ul class="tabs">';
			echo '<li><a title="', $config['tabName'],'" class="tab active">', $config['tabName'], '</a></li>';
			if(isset($config['detail'])){
				echo '<li><a title="', $config['detail']['tabName'], '" class="tab">', $config['detail']['tabName'];
			}
			if(isset($config['extras'])){
				foreach($config['extras'] as $tabs){
					echo '<li><a title="', $tabs['tabName'], '" class="tab">', $tabs['tabName'];
				}
			}
			echo '</a></li></ul><div id="', $config['tabName'], '" class="content">';
		}
		echo '<table align="center" cellspacing="0" cellpadding="0"><tr>';
		foreach($config['fields'] as $name => $component){
			if(isset($component['readOnly'])&&$component['readOnly']){
				continue;
			}
			if($name=='id'||isset($component['auto'])){
				continue;
			}
			$className = '';
			if(isset($component['notNull'])&&$component['notNull']){
				$className = 'not-null';
			}
			echo '<tr><td align="right"><label for="', $name, '">', $component['single'], '</label></td><td>';
			switch($component['type']){
				case 'text':
					echo Tag::textField(array(
						$name,
						'size' => $component['size'],
						'maxlength' => $component['maxlength'],
						'class' => $className
					));
					break;
				case 'textarea':
					echo Tag::textArea(array(
						$name,
						'cols' => $component['cols'],
						'rows' => $component['rows'],
						'class' => $className
					));
					break;
				case 'closed-domain':
					if(isset($component['useDummy'])){
						$useDummy = $component['useDummy'];
					} else {
						$useDummy = 'yes';
					}
					echo Tag::selectStatic(array(
						$name,
						$component['values'],
						'useDummy' => $useDummy
					));
					break;
				case 'relation':
					$entity = EntityManager::getEntityInstance($component['relation']);
					if(!isset($component['conditionsOnCreate'])){
						echo Tag::select(array(
							$name,
							$entity->find(array('order' => $component['detail'])),
							'using' => $component['fieldRelation'].','.$component['detail'],
							'useDummy' => 'yes'
						));
					} else {
						echo Tag::select(array(
							$name,
							$entity->find(array($component['conditionsOnCreate'], 'order' => $component['detail'])),
							'using' => $component['fieldRelation'].','.$component['detail'],
							'useDummy' => 'yes'
						));
					}
					break;
				case 'int':
				case 'decimal':
					echo Tag::numericField(array(
						$name,
						'size' => $component['size'],
						'maxlength' => $component['maxlength'],
						'class' => 'numeric'
					));
					break;
				case 'date':
					echo Tag::dateField(array(
						$name,
						'default' => $component['default'],
						'class' => 'date-field'
					));
					break;
				default:
					$className = $component['type'].'HyperComponent';
					if(class_exists($className, false)==false){
						$path = KEF_ABS_PATH.'Library/Hfos/HyperForm/Components/'.ucfirst($component['type']).'.php';
						if(file_exists($path)){
							require_once $path;
						} else {
							throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
						}
					}
					echo call_user_func_array(array($className, 'build'), array($name, $component, null, 'hySaveForm'));
			}
			if(isset($component['notNull'])){
				if($component['notNull']){
					echo '<span class="notNullMark">!</span>';
				}
			}
			echo '</td></tr>';
		}
		if(isset($config['detail'])||isset($config['extras'])){
			echo '</table>';
			echo '</div>';
			if(isset($config['detail'])){
				echo '<div id="', $config['detail']['tabName'], '" class="content" style="display:none;">';
				echo '<div class="hyGridDataDetail" style="display: none;"></div>';
				HyperForm::createDetail($config['detail']);
				echo '</div>';
			}
			if(!isset($config['extras'])){
				echo '</div><table align="center">';
			}
		}
		if(isset($config['extras'])){
			self::_renderExtras($config);
			echo '<table align="center" cellspacing="0" cellpadding="0">';
		}
		echo '</table>
		<input type="hidden" name="hyperAction" value="create"/>
		</form>';
	}

	/**
	 * Genera las pestañas extra que tenga el formulario
	 *
	 * @param array $config
	 */
	private static function _renderExtras($config){
		foreach($config['extras'] as $tab){
			echo '<div id="', $tab['tabName'], '" class="content" style="display:none;">';
			if(!isset($tab['partial'])){
				$numero = 1;
				echo '<table align="center" cellspacing="0" cellpadding="0"><tr>';
				foreach($tab['fields'] as $name => $component){
					$className = '';
					if(isset($component['notNull'])&&$component['notNull']){
						$className = 'not-null';
					}
					if(isset($component['single'])){
						echo '<td align="right"><label for="', $name, '">', $component['single'], '</label></td><td>';
					} else {
						echo '<td align="right"></td><td>';
					}
					switch($component['type']){
						case 'text':
							echo Tag::textField(array(
								$name,
								'size' => $component['size'],
								'maxlength' => $component['maxlength'],
								'class' => $className
							));
							break;
						case 'closed-domain':
							if(isset($component['useDummy'])){
								$useDummy = $component['useDummy'];
							} else {
								$useDummy = 'yes';
							}
							echo Tag::selectStatic(array(
								$name,
								$component['values'],
								'useDummy' => $useDummy
							));
							break;
						case 'relation':
							$entity = EntityManager::getEntityInstance($component['relation']);
							echo Tag::select(array(
								$name,
								$entity->find(array('order' => $component['detail'])),
								'using' => $component['fieldRelation'].','.$component['detail'],
								'useDummy' => 'yes'
							));
							break;
						case 'int':
						case 'decimal':
							echo Tag::numericField(array(
								$name,
								'size' => $component['size'],
								'maxlength' => $component['maxlength'],
								'class' => 'numeric'
							));
							break;
						case 'date':
							echo Tag::dateField(array(
								$name,
								'default' => $component['default'],
								'class' => 'date-field'
							));
							break;
					}
					if(isset($component['notNull'])){
						if($component['notNull']){
							echo '<span class="notNullMark">!</span>';
						}
					}
					echo '</td>';
					if(($numero%2)==0){
						echo '</tr><tr>';
					} else {
						echo '<td width="20"></td>';
					}
					$numero++;
				}
				echo '</tr></table>';
			} else {
				View::renderPartial($tab['partial']);
			}
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Obtiene las condiciones que las que se obtendrá un registro a editar
	 *
	 * @param array $config
	 * @param array $conditions
	 */
	public static function getEditConditions($config, $conditions=array()){
		$request = ControllerRequest::getInstance();
		$model = self::getModel($config['model']);
		$notNullAttributes = $model->getNotNullAttributes();
		foreach($notNullAttributes as $attribute){
			if(isset($config['fields'][$attribute])){
				$config['fields'][$attribute]['notNull'] = true;
			}
		}
		foreach($config['fields'] as $name => $component){
			if(isset($component['primary'])&&$component['primary']){
				if($request->isSetPostParam($name)){
					$value = $request->getParamPost($name, $component['filters']);
					$conditions[] = $name.' = \''.$value.'\' ';
				}
			}
		}
		return $conditions;
	}

	/**
	 * Página de editar un registro
	 *
	 * @param array $config
	 */
	public static function editPage($controller, $config, $conditions=array()){

		$controllerName = Router::getController();
		$conditions = self::getEditConditions($config, $conditions);

		$model = self::getModel($config['model']);
		$record = $model->findFirst(join(' AND ', $conditions));
		if($record==false){
			Flash::error('No existe el registro');
			return;
		}

		if(method_exists($controller, 'beforeEdit')){
			$controller->beforeEdit($record);
		}

		$notNullAttributes = $model->getNotNullAttributes();
		foreach($notNullAttributes as $attribute){
			if(isset($config['fields'][$attribute])){
				$config['fields'][$attribute]['notNull'] = true;
			}
		}

		//Titulo de Edición
		echo '<table class="hyTitle"><tr>';
		if(isset($config['icon'])){
			echo '<td>', Tag::image('backoffice/icons/'.$config['icon']), '</td>';
		}
		echo '<td>';
		if(method_exists($controller, 'getEditTitle')){
			if($config['genre']=='M'){
				$title = 'Editando el '.i18n::ucfirst($config['single']);
			} else {
				$title = 'Editando la '.i18n::ucfirst($config['single']);
			}
			$title = $controller->getEditTitle($title, $record);
		} else {
			if($config['genre']=='M'){
				$title = 'Editando un '.i18n::ucfirst($config['single']);
			} else {
				$title = 'Editando una '.i18n::ucfirst($config['single']);
			}
		}
		echo '<h2>', $title, '</h2></td>';

		echo '</tr></table>';
		echo '<form autocomplete="off" name="hyperForm" class="hySaveForm" action="', Utils::getKumbiaUrl($controllerName.'/save'), '" method="post">';
		if(isset($config['detail'])||isset($config['extras'])){
			echo '<div class="tabbed_area"><ul class="tabs">';
			echo '<li><a title="', $config['tabName'], '" class="tab active">', $config['tabName'], '</a></li>';
			if(isset($config['detail'])){
				echo '<li><a title="', $config['detail']['tabName'], '" class="tab">', $config['detail']['tabName'];
			}
			if(isset($config['extras'])){
				foreach($config['extras'] as $tabs){
					echo '<li><a title="', $tabs['tabName'], '" class="tab">', $tabs['tabName'];
				}
			}
			echo '</a></li></ul><div id="', $config['tabName'], '" class="content">';
		}

		$charset = Hfos_Application::getAppCharset();
		echo '<table align="center" cellspacing="0" cellpadding="0"><tr>';
		foreach($config['fields'] as $name => $component){
			if(isset($component['readOnly'])&&$component['readOnly']){
				if(!isset($component['primary'])||!$component['primary']){
					continue;
				}
			}
			if(isset($component['readOnly'])&&$component['readOnly']){
				$value = $record->readAttribute($name);
				echo Tag::hiddenField($name, array(
					'value' => $value
				));
			} else {
				echo '<tr><td align="right"><label for="', $name, '">', $component['single'], '</label></td><td>';
				if(isset($component['primary'])&&$component['primary']){
					$value = $record->readAttribute($name);
					switch($component['type']){
						case 'closed-domain':
							if(isset($component['values'][$value])){
								echo $component['values'][$value];
							} else {
								echo $value;
							}
							break;
						case 'relation':
							$value = $record->readAttribute($name);
							$fieldValue = self::getModel($component['relation'])->findFirst("{$component['fieldRelation']} = '$value'");
							echo ($fieldValue == false) ? '' : $fieldValue->readAttribute($component['detail']) ;
							break;
						default:
							if($charset=='latin1'){
								echo utf8_encode($value);
							} else {
								echo $value;
							}
							break;
					}
					echo Tag::hiddenField($name, array(
						'value' => $value
					));
				} else {
					$className = '';
					if(isset($component['notNull'])&&$component['notNull']){
						$className = 'not-null';
					}
					if(isset($component['extraField'])&&$component['extraField']){
						$value = $controller->getExtraField($name, $record);
					} else {
						$value = $record->readAttribute($name);
					}
					switch($component['type']){
						case 'text':
							$params = array(
								$name,
								'size' => $component['size'],
								'maxlength' => $component['maxlength'],
								'class' => $className
							);
							if($charset=='latin1'){
								$params['value'] = utf8_encode($value);
							} else {
								$params['value'] = $value;
							}
							echo Tag::textField($params);
							break;
						case 'textarea':
							echo Tag::textArea(array(
								$name,
								'cols' => $component['cols'],
								'rows' => $component['rows'],
								'class' => $className,
								'value' => utf8_encode($value)
							));
							break;
						case 'closed-domain':
							if(isset($component['useDummy'])){
								$useDummy = $component['useDummy'];
							} else {
								$useDummy = 'yes';
							}
							echo Tag::selectStatic(array(
								$name,
								$component['values'],
								'useDummy' => $useDummy,
								'value' => $value
							));
							break;
						case 'relation':
							$entity = EntityManager::getEntityInstance($component['relation']);
							if(!isset($component['conditions'])){
								echo Tag::select(array(
									$name,
									$entity->find(array('order' => $component['detail'])),
									'using' => $component['fieldRelation'].','.$component['detail'],
									'useDummy' => 'yes',
									'value' => $value
								));
							} else {
								echo Tag::select(array(
									$name,
									$entity->find(array($component['conditions'], 'order' => $component['detail'])),
									'using' => $component['fieldRelation'].','.$component['detail'],
									'useDummy' => 'yes',
									'value' => $value
								));
							}
							break;
						case 'int':
						case 'decimal':
							echo Tag::numericField(array(
								$name,
								'size' => $component['size'],
								'maxlength' => $component['maxlength'],
								'value' => $value,
								'class' => 'numeric'
							));
							break;
						case 'date':
							echo Tag::dateField(array(
								$name,
								'default' => $component['default'],
								'class' => 'date-field',
								'value' => $value
							));
							break;
						default:
							$className = $component['type'].'HyperComponent';
							if(class_exists($className, false)==false){
								$path = KEF_ABS_PATH.'Library/Hfos/HyperForm/Components/'.ucfirst($component['type']).'.php';
								if(file_exists($path)){
									require_once $path;
								} else {
									throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
								}
							}
							$value = $value;
							echo call_user_func_array(array($className, 'build'), array($name, $component, $value, 'hySaveForm'));
					}
					if(isset($component['notNull'])){
						if($component['notNull']){
							echo '<span class="notNullMark">!</span>';
						}
					}
				}
				echo '</td></tr>';
			}
		}
		if(isset($config['detail'])||isset($config['extras'])){
			echo '</table></div>';
		}
		if(isset($config['detail'])){
			echo '<div id="',$config['detail']['tabName'],'" class="content" style="display:none;">';
			echo '<div class="hyGridDataDetail" style="display: none;"></div>';

			$data = array();

			if(method_exists($controller, 'getDetail')){
				$details = $controller->getDetail($record);
			} else {
				$conditions = array();
				$model = self::getModel($config['detail']['model']);

				foreach($config['detail']['relation'] as $key => $fk){
					//xD
					if (is_numeric($key)) {
						$key = $fk;
					}

					$conditions[] = "$fk = '{$record->readAttribute($key)}'";
				}
				$details = $model->find(join(' AND ', $conditions));
			}

			foreach ($details as $detail)
			{
				if (method_exists($controller, 'describeDetail')) {
					$data[] = $controller->describeDetail($detail);
					continue;
				}
				$temp = array();
				foreach ($config['detail']['fields'] as $name => $component)
				{
					$temp[$name] = $detail->readAttribute($name);
					unset($component);
				}
				$data[] = $temp;
				unset($detail);
			}

			HyperForm::createDetail($config['detail'], $data);
			echo '</div>';
		}
		if(isset($config['extras'])){
			self::_renderExtras($config);
			echo '<table align="center" cellspacing="0" cellpadding="0">';
		} else {
			echo '</div><table align="center" cellspacing="0" cellpadding="0">';
		}
		echo '</table>
		<input type="hidden" name="hyperAction" value="update"/>
		</form>';
	}

	/**
	 * Genera la salida cuando los eventos reportan fallo
	 *
	 * @param 	ApplicationController $controller
	 * @param	ActiveRecordBase $record
	 * @return	array
	 */
	private static function _failedEvent($controller, $record){
		$messages = $controller->getMessages();
		if(count($messages)){
			foreach($messages as $message){
				return array(
					'status' => 'FAILED',
					'message' => $message
				);
			}
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'event-failed'
			);
		}
	}

	/**
	 * Realiza la acción de Guardar tanto para los formularios de nuevo como de editar.
	 * Recibe por parámetro get las llaves primarias para editar. Una vez terminado el proceso envia el resultado via JSON.
	 *
	 * @param 	ApplicationController $controller
	 * @param	array $config
	 */
	public static function save($controller, $config){

		$linguistic = new Linguistics();
		$locale = Locale::getApplication();
		$request = ControllerRequest::getInstance();
		$response = ControllerResponse::getInstance();

		$response->setResponseType(ControllerResponse::RESPONSE_OTHER);
		$response->setResponseAdapter('json');
		View::setRenderLevel(View::LEVEL_NO_RENDER);

		$modelName = $config['model'];
		if(method_exists($controller, 'getRecordToSave')){
			$record = $controller->getRecordToSave($modelName);
		} else {
			$record = false;
			$conditions = array();
			foreach($config['fields'] as $name => $component){
				if(isset($component['primary'])&&$component['primary']){
					if($request->isSetPostParam($name)){
						if(isset($component['filters'])){
							$value = $request->getParamPost($name, $component['filters']);
						} else {
							$value = $request->getParamPost($name);
						}
						if(isset($component['maxlength'])){
							$value = i18n::substr($value, 0, $component['maxlength']);
						}
						$conditions[] = $name.' = \''.$value.'\'';
					}
				}
			}
			if(count($conditions)>0){
				$conditions = join(' AND ', $conditions);
				$record = $controller->$modelName->findFirst(array($conditions));
			}
		}

		$action = $request->getParamPost('hyperAction', 'alpha');
		if($action=='update'){
			if($record===false){
				return array(
					'status' => 'FAILED',
					'message' => 'El registro a actualizar no fue encontrado, '.$conditions
				);
			}
		} else {
			if($record!==false){
				$message = ucfirst($linguistic->a($config['single'])).' ya existe con ';
				$primaryFields = array();
				foreach($config['fields'] as $name => $component){
					if(isset($component['primary'])&&$component['primary']){
						$primaryFields[] = $component['single'];
					}
				}
				if(count($primaryFields)==1){
					if($linguistic->isFemale($primaryFields[0])){
						$message.= 'esa '.i18n::strtolower($primaryFields[0]);
					} else {
						$message.= 'ese '.i18n::strtolower($primaryFields[0]);
					}
				} else {
					$message.= 'esos '.i18n::strtolower($locale->getConjunction($primaryFields));
				}
				return array(
					'status' => 'FAILED',
					'message' => $message
				);
			}
			$record = new $modelName();
			foreach($config['fields'] as $name => $component){
				if(isset($component['auto'])&&$component['auto']){
					$maxValue = self::getModel($modelName)->maximum($name);
					if(!$maxValue){
						$maxValue = 1;
					} else {
						$maxValue++;
					}
					$record->writeAttribute($name, $maxValue);
				}
			}
		}

		try {
			$charset = Hfos_Application::getAppCharset();
			$transaction = TransactionManager::getUserTransaction();
			$record->setTransaction($transaction);
			foreach($record->getAttributes() as $attribute){
				if($request->isSetPostParam($attribute)){
					$requestValue = $request->getParamPost($attribute);
					if(isset($config['fields'][$attribute]['filters'])){
						$filters = $config['fields'][$attribute]['filters'];
						if($requestValue!==''){
							$type = $config['fields'][$attribute]['type'];
							$valPost = $request->getParamPost($attribute, $filters);
							if($type=='relation'||$type=='closed-domain'){
								if($valPost=='@'){
									$valPost = null;
								} else {
									if(is_array($valPost)){
										$valPost = implode(' ', $valPost);
									}
								}
							} else {
								if($valPost){
									if(is_array($valPost)){
										$valPost = implode(' ', $valPost);
									}
									if($charset=='latin1'){
										$valPost = utf8_decode($valPost);
									}
								}
							}
							$record->writeAttribute($attribute, $valPost);
						}
					} else {
						if(!is_array($requestValue)){
							if($charset=='latin1'){
								$record->writeAttribute($attribute, utf8_decode($requestValue));
							} else {
								$record->writeAttribute($attribute, $requestValue);
							}
						}
					}
				}
			}
			if($record->exists()==false){
				if(method_exists($controller, 'beforeInsert')){
					$response = $controller->beforeInsert($transaction, $record);
					if($response!==true){
						return self::_failedEvent($controller, $record);
					}
				}
			} else {
				if(method_exists($controller, 'beforeUpdate')){
					$response = $controller->beforeUpdate($transaction, $record);
					if($response!==true){
						return self::_failedEvent($controller, $record);
					}
				}
			}
			$saved = $record->save();
			if($saved===true){
				$primaryKey = array();
				$auditPrimaryKey = array();
				foreach($record->getPrimaryKeyAttributes() as $field){
					$primaryKey[] = $field.'='.$record->readAttribute($field);
					if(isset($config['fields'][$field])){
						$auditPrimaryKey[] = $config['fields'][$field]['single'].'='.$record->readAttribute($field);
					}
				}
				$controller->clearMessages();
				if($record->operationWasInsert()==true){
					if(method_exists($controller, 'afterInsert')){
						$success = $controller->afterInsert($transaction, $record);
						if($success!==true){
							return self::_failedEvent($controller, $record);
						}
					}
					new EventLogger('CREÓ '.i18n::strtoupper($linguistic->a($config['single'])).'. '.join(' ', $auditPrimaryKey), 'A');
					$message = 'Se creó correctamente '.$linguistic->the($config['single']);
				} else {
					if(method_exists($controller, 'afterUpdate')){
						$success = $controller->afterUpdate($transaction, $record);
						if($success!==true){
							return self::_failedEvent($controller, $record);
						}
					}
					new EventLogger('ACTUALIZÓ '.i18n::strtoupper($linguistic->a($config['single'])).'. '.join(' ', $auditPrimaryKey), 'A');
					$message = 'Se actualizó correctamente '.$linguistic->the($config['single']);
				}
				$transaction->commit();
				if($record->operationWasInsert()==true){
					return array(
						'status' 	=> 'OK',
						'message' 	=> $message,
						'type' 		=> 'insert',
						'primary' 	=> join('&', $primaryKey),
						'data' 		=> self::_getRecordDetails($config, $record, $controller)
					);
				} else {
					return array(
						'status' 	=> 'OK',
						'message' 	=> $message,
						'type' 		=> 'update',
						'primary' 	=> join('&', $primaryKey),
						'data' 		=> self::_getRecordDetails($config, $record, $controller)
					);
				}
			} else {
				$fields = array();
				$required = array();
				$other = array();
				foreach($record->getMessages() as $message){
					if(!is_array($message->getField())){
						if(isset($config['fields'][$message->getField()])){
							$caption = $config['fields'][$message->getField()]['single'];
						} else {
							$caption = $message->getField();
						}
					} else {
						$fields = $message->getField();
						if(isset($config['fields'][$fields[0]])){
							$caption = $config['fields'][$fields[0]]['single'];
						} else {
							$caption = $message->getField();
						}
					}
					if($message->getType()=='PresenceOf'){
						$required[] = $caption;
					} else {
						$other[] = $message->getMessage();
					}
					$fields[] = $message->getField();
				}
				$message = '';
				if(count($required)>0){
					$message.= ucfirst(i18n::strtolower($linguistic->getConjunction($required)));
					if(count($required)>1){
						$message.=' son requeridos';
					} else {
						if($linguistic->isFemale($required[0])){
							$message.=' es requerida';
						} else {
							$message.=' es requerido';
						}
					}
				} else {
					$message.= join(', ', $other);
				}
				return array(
					'status'  => 'FAILED',
					'message' => $message,
					'fields'  => $fields
				);
			}
		}
		catch(TransactionFailed $e){
			return array(
				'status'  => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	/**
	 * Elimina un registro
	 *
	 * @param	Controller $controller
	 * @param	array $config
	 * @return	array
	 */
	public static function deleteRecord($controller, $config){

		$linguistic = new Linguistics();
		$response = ControllerResponse::getInstance();
		$request = ControllerRequest::getInstance();

		$response->setResponseType(ControllerResponse::RESPONSE_OTHER);
		$response->setResponseAdapter('json');
		View::setRenderLevel(View::LEVEL_NO_RENDER);

		$conditions = array();
		foreach($config['fields'] as $name => $component){
			if(isset($component['primary'])&&$component['primary']){
				if($request->isSetQueryParam($name)){
					$value = $request->getParamQuery($name, $component['filters']);
					$conditions[] = $name.' = \''.$value.'\'';
				}
			}
		}
		if(count($conditions)>0){
			$modelName = $config['model'];
			$record = $controller->$modelName->findFirst(array(join(' AND ', $conditions)));
			if(method_exists($controller, 'beforeDelete')){
				$response = $controller->beforeDelete($record);
				if($response!==true){
					return array(
						'status' => 'FAILED',
						'message' => $response
					);
				}
			}
			if($record==false){
				return array(
					'status' => 'FAILED',
					'message' => 'El registro ya no existe'
				);
			} else {
				try {
					$transaction = TransactionManager::getUserTransaction();
					$record->setTransaction($transaction);
					$auditPrimaryKey = array();
					foreach($record->getPrimaryKeyAttributes() as $field){
						$primaryKey[] = $field.'='.$record->readAttribute($field);
						if(isset($config['fields'][$field])){
							$auditPrimaryKey[] = $config['fields'][$field]['single'].'='.$record->readAttribute($field);
						}
					}
					$deleted = $record->delete();
					if($deleted===true){
						if(isset($config['detail'])){
							$conditions = array();
							foreach($config['detail']['relation'] as $field){
								$conditions[] = $field.'=\''.$record->readAttribute($field).'\'';
							}
							$detailModel = self::getModel($config['detail']['model']);
							$detailModel->setTransaction($transaction);
							foreach($detailModel->find(join(' AND ', $conditions)) as $detailRecord){
								if($detailRecord->delete()==false){
									foreach($detailRecord->getMessages() as $message){
										return array(
											'status' => 'FAILED',
											'message' => $message->getMessage()
										);
									}
								}
							}
						}
						if(method_exists($controller, 'afterDelete')){
							$response = $controller->afterDelete($record);
							if($response!==true){
								return array(
									'status' => 'FAILED',
									'message' => $response
								);
							}
						}
					}
					if($deleted===true){
						new EventLogger('ELIMINÓ '.i18n::strtoupper($linguistic->a($config['single'])).'. '.join(' ', $auditPrimaryKey), 'A');
						if($linguistic->isFemale($config['single'])){
							$message = 'La '.ucfirst($config['single']).' ha sido eliminada correctamente';
						} else {
							$message = 'El '.ucfirst($config['single']).' ha sido eliminado correctamente';
						}
						$transaction->commit();

						return array(
							'status' => 'OK',
							'message' => $message
						);
					} else {
						foreach($record->getMessages() as $message){
							return array(
								'status' => 'FAILED',
								'field' => $message->getField(),
								'message' => $message->getMessage()
							);
						}
					}
				}
				catch(DbConstraintViolationException $e){
					return array(
						'status' => 'FAILED',
						'message' => 'El registro está referenciado'
					);
				}
				catch(TransactionFailed $e){
					return array(
						'status' => 'FAILED',
						'message' => $e->getMessage()
					);
				}
			}
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'Registro no encontrado'.print_r($_GET, true)
			);
		}
	}

    /**
     * Metodo que crea detailForm de Hyperform
     *
     * @param mixed $configDetail: (Array)Contiene la configuracion para crear el DetailForm
     *  Array(
     *  - Fields: Son las pestañas q aparecen en el formulario y su contenido son los campos a mostrar
     *     =>Array(
     *      - preferedOrder: Son las preferencias que se utilizan para ordenar columnas
     *      - notNull : Indica que un campo tiene class not-null para vlaidacion JS
     *      - type: Es el tipo de campo que se ingresa al formulario, pueden ser:
     *          * text
     *          * closed-domain
     *          * relation
     *          * int
     *          * decimal
     *          * date
     *      - size: Es el tamaño del campo
     *      - maxlength: Es el numero maximo de caracteres que puede agregar
     *      - class: asigna una clase css al tag html
     *     )
     *  );
     * @param mixed $data: (Array)Contiene datos que seran mostrados en el formulario
     *  Array(
     *    nombreField => 'valor Actual',
     *      .......
     *  );
     * @return void, print HTML
     */
	public static function createDetail($configDetail, $data=array()){

		$request = ControllerRequest::getInstance();

		echo "<table align='center' cellspacing='0' class='hyGridTable zebraSt sortable'><thead><tr><td></td>";
		foreach($configDetail['fields'] as $name => $component){
			echo "<td class='hyGridField'>";
			$className = '';
			if(isset($component['notNull'])&&$component['notNull']){
				$className = 'not-null';
			}
			switch($component['type']){
				case 'text':
					echo Tag::textField(array($name, 'size' => $component['size'], 'maxlength' => $component['maxlength'], 'class' => $className));
					break;
				case 'closed-domain':
					if(isset($component['useDummy'])){
						$useDummy = $component['useDummy'];
					} else {
						$useDummy = 'yes';
					}
					echo Tag::selectStatic(array($name, $component['values'], 'useDummy' => $useDummy,'class' => $className));
					break;
				case 'relation':
					$entity = EntityManager::getEntityInstance($component['relation']);
					$using = $component['fieldRelation'].','.$component['detail'];
					echo Tag::select(array(
						$name,
						$entity->find(array('order' => $component['detail'])),
						'using' => $using,
						'useDummy' => 'yes',
						'class' => $className
					));
					break;
				case 'int':
				case 'decimal':
					echo Tag::numericField(array(
						$name,
						'size' => $component['size'],
						'maxlength' => $component['maxlength'],
						'class' => $className.' '.$component['type'].'-type',
					));
					break;
				case 'date':
					echo Tag::dateField(array(
						$name,
						'default' => $component['default'],
						'class' => 'date-field'
					));
					break;
				default:
					$className = $component['type'].'HyperComponent';
					if(class_exists($className, false)==false){
						$path = KEF_ABS_PATH.'Library/Hfos/HyperForm/Components/'.ucfirst($component['type']).'.php';
						if(file_exists($path)){
							require_once $path;
						} else {
							throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
						}
					}
					echo call_user_func_array(array($className, 'build'), array($name, $component, null, 'hySaveForm'));
			}
			echo "</td>";
		}
		echo "<td><input type='button' class='hyGridAdd' value='Agregar'/></td></tr><tr><th class='sortasc'></th>";
		foreach($configDetail['fields'] as $name => $component){
			if(isset($configDetail['preferedOrder']) && $configDetail['preferedOrder'] == $name){
				echo "<th class='hyGridComponent sortcol sortasc'>". $component['single']. "</th>";
			} else {
				echo "<th class='hyGridComponent sortcol'>". $component['single']. "</th>";
			}
		}
		echo "<th class='nosort'>&nbsp;</th><th class='nosort'>&nbsp;</th></tr></thead><tr><tbody class='hyGridBodyTable'></tbody></table>";

		if (count($data) > 0) {
			if (!isset($configDetail['dataName'])) {
				$dataName = Router::getController();
			} else {
				$dataName = $configDetail['dataName'];
			}
			$script = 'HyperGridData.'.$dataName.'= [];';

			foreach ($data as $record)
			{
				$script .= "temp = {};\n";
				foreach ($record as $key => $value)
				{
					$script .= "temp." . $key . " = '". addslashes($value) . "'\n";
					unset($value);
				}
				$script .= "HyperGridData." . $dataName . ".push(temp);\n";
				unset($record);
			}
			echo "<script type='text/javascript'>", $script, ";</script>";
		}
	}

	/**
	 * Obtiene las revisiones RCS de un registro
	 *
	 * @param Controller $controller
	 * @param array $config
	 */
	public static function getRecordRcs($controller, $config)
	{

		$entityName = $config['model'];
		$entity = EntityManager::getEntityFromSource($entityName);
		$schema = $entity->getSchema();
		if ($schema == '') {
			$schema = $entity->getConnection()->getDatabaseName();
		}
		$sourceName = $entity->getSource();

		$request = ControllerRequest::getInstance();
		$conditions = array();
		foreach ($entity->getPrimaryKeyAttributes() as $attribute) {
			if ($request->isSetPostParam($attribute)) {
				if (isset($config['fields'][$attribute])) {
					if (isset($config['fields'][$attribute]['filters'])) {
						$conditions[] = $attribute.'=\''.$request->getParamPost($attribute, $config['fields'][$attribute]['filters']).'\'';
					} else {
						Flash::error('El campo "'.$attribute.'" no tiene filtros a aplicar');
						return;
					}
				} else {
					$conditions[] = $attribute.'=\''.$request->getParamPost($attribute, 'alpha').'\'';
				}
			}
		}
		if (count($conditions) > 0) {
			$originalEntity = $entity->findFirst(join(' AND ', $conditions));
			if ($originalEntity == false) {
				Flash::error('El registro original no existe ' . join(' AND ', $conditions));
				return;
			}
		} else {
			Flash::error('El registro original no existe ' . join(' AND ', $conditions));
			return;
		}

		$conditionBase = "{#Revisions}.db='".$schema."' AND {#Revisions}.source='".$sourceName."'";

		$primaryKeys = $entity->getPrimaryKeyAttributes();
		$entities = array('Revisions', 'Usuarios');
		$checksum = $schema.$sourceName;
		foreach($primaryKeys as $attribute){
			$checksum.=$originalEntity->readAttribute($attribute);
		}

		$recordConditions = "{#Revisions}.checksum='".md5($checksum)."'";

		$query = new ActiveRecordJoin(array(
			'count' => '*',
			'entities' => $entities,
			'conditions' => $conditionBase.' AND '.$recordConditions
		));

		$rowcount = $query->getResultSet()->getFirst()->count;
		if ($rowcount == 0) {
			Rcs::beforeCreate($originalEntity);
			Rcs::afterCreate($originalEntity);
			Flash::success('Se creó la revisión base');
		}

		$query = new ActiveRecordJoin(array(
			'fields' => array('{#Revisions}.id', '{#Revisions}.source', '{#Usuarios}.apellidos', '{#Usuarios}.nombres', '{#Revisions}.fecha'),
			'entities' => $entities,
			'conditions' => $recordConditions,
			'order' => '{#Revisions}.fecha DESC'
		));
		$revisions = $query->getResultSet();

		$linguistic = new Linguistics();
		$preferedAttribute = explode(' ', $config['preferedOrder']);
		if($linguistic->isFemale($config['single'])){
			echo '<h2>Revisiones de ', $linguistic->the($config['single']), ': ', $originalEntity->readAttribute($preferedAttribute[0]), '</h2>';
		} else {
			echo '<h2>Revisiones del ', $config['single'], ': ', $originalEntity->readAttribute($preferedAttribute[0]), '</h2>';
		}

		echo '<div id="rcs" class="revision">
		<table width="100%">';
		$number = count($revisions);
		foreach($revisions as $revision){
			$fecha = Date::fromTimestamp($revision->fecha);
			echo '<tr><td valign="top" width="40%">
				<div class="record_master">
					<table>
						<tr>
							<td align="right"><b>Revisión&nbsp;#</b></td>
							<td>', $number, '</td>
						</tr>
						<tr>
							<td align="right"><b>Usuario</b></td>
							<td>', $revision->apellidos.' '.$revision->nombres, '</td>
						</tr>
						<tr>
							<td align="right"><b>Fecha</b></td>
							<td>', $fecha->getLocaleDate(), ', ', date('H:i', $revision->fecha), '</td>
						</tr>
					</table>
				</div>
			</td>
			<td width="60%" valign="top">
				<div class="record_block"><table width="100%" cellspacing="0" cellpadding="2">';
				--$number;
				$row = array();
				foreach(self::getModel('Records')->find("revisions_id='{$revision->id}'") as $record){
					if(isset($config['fields'][$record->getFieldName()])){
						$row[$record->getFieldName()] = array(
							'value' => $record->getValue(),
							'changed' => $record->getChanged()
						);
					}
				}
				foreach ($config['fields'] as $name => $component) {
					if (isset($row[$name])) {
						$value = $row[$name]['value'];
						$changed = $row[$name]['changed'];
					} else {
						$value = '';
						$changed = 'N';
					}
					switch($component['type']){
						case 'closed-domain':
							if(isset($component['values'][$value])){
								$fieldValue = $component['values'][$value];
							} else {
								if($value===null){
									$fieldValue = '';
								} else {
									$fieldValue = $value;
								}
							}
							break;
						case 'relation':
							$entity = EntityManager::getEntityInstance($component['relation']);
							$fieldValue = $entity->findFirst("{$component['fieldRelation']} = '$value'");
							$fieldValue = ($fieldValue == false) ? '' : $fieldValue->readAttribute($component['detail']) ;
							break;
						default:
							$fieldValue = $value;
							break;
					}
					if($changed=='S'){
						echo '<tr class="changed">';
					} else {
						echo '<tr>';
					}
					$caption = $component['single'];
					echo '<td align="right" width="30%"><b>', $caption, '</b></td>
						<td>', $fieldValue, '&nbsp;</td>
					</tr>';
				}
				unset($row);
			echo '</table></div></td></tr>';
		}
		echo '</tr></table></div>';
	}

	/**
	 * Importar desde un archivo
	 *
	 * @param array $config
	 */
	public static function import($controller, $config)
	{

		echo '<table class="hyTitle"><tr>';
		if(isset($config['icon'])){
			echo '<td>', Tag::image('backoffice/icons/'.$config['icon']), '</td>';
		}
		echo '<td><h2>Importar ', $config['plural'], '</h2></td>';
		echo '</tr></table>';

		$linguistic = new Linguistics();
		$controllerName = Router::getController();
		$locale = Locale::getApplication();

		echo '

		<div align="center">
			<div class="subirBar" style="display:none">
				<div class="subirLoadBar" align="center">
					', Tag::image('backoffice/load-bar.gif'), '
					Importando archivo, por favor espere...
				</div>
			</div>
		</div>

		<iframe class="subirFrame" name="', $controllerName, 'SubirFrame" style="display:none"></iframe>

		<table width="100%" class="importTable">
		<tr>
		<td width="50%" valign="top" align="center">

		<div class="subirArchivo" align="center">';
		echo Tag::form(array($controllerName.'/load', 'target' => $controllerName.'SubirFrame', 'class' => 'subirForm', 'enctype'  => 'multipart/form-data'));
		echo '<div align="left">
			<label>Seleccione el archivo</label>
		</div>
		<table align="center" width="100%">
			<tr>
				<td align="center">
					', Tag::fileField('archivo'), '
				</td>
			</tr>
		</table>';
		echo Tag::endForm();
		echo '</div>
		</td>
		<td width="50%">
		<div class="infoBox">Por medio de esta opción puede importar ', $config['plural'], ' desde un archivo de CSV deben estar seprados los campos por "comas".</div>
		<div class="infoBox">Por medio de esta opción puede importar ', $config['plural'], ' desde un archivo de Microsoft Excel 2007 ó superior.</div>';

		echo '<div class="infoBox">Para una correcta importación de los datos, el archivo debe tener las siguientes columnas (Total Columnas: '.count(array_keys($config['fields'])).'):<br/><br/>	';

		$modelName = $config['model'];
		$record = EntityManager::getEntityInstance($modelName);
		foreach ($record->getNotNullAttributes() as $attribute)
		{
			if (isset($config['fields'][$attribute])) {
				$config['fields'][$attribute]['notNull'] = true;
			}
		}

		echo '<table class="importInfo" cellspacing="0">';
		foreach ($config['fields'] as $attribute => $component)
		{
			echo '<tr><td align="right"><label>', $component['single'], '</td><td>';
			switch ($config['fields'][$attribute]['type']) {
				case 'text':
					if (in_array('int', $component['filters'])) {
						echo 'Valor númerico, sin decimales. ';
					} else {
						if (in_array('double', $component['filters'])) {
							echo 'Valor númerico, puede tener decimales. ';
						} else {
							echo 'Texto. ';
						}
					}
					if (isset($component['maxlength'])) {
						echo 'Máximo '.$component['maxlength'].' carácteres. ';
					}
					break;
				case 'int':
					echo 'Valor númerico, sin decimales. ';
					if (isset($component['maxlength'])) {
						echo 'Máximo '.$component['maxlength'].' carácteres. ';
					}
					break;
				case 'decimal':
					echo 'Valor númerico, con decimales. ';
					break;
				case 'closed-domain':
					$posibleValues = array();
					$values = $component['values'];
					foreach ($values as $key => $value)
					{
						$posibleValues[] = $key;
						$posibleValues[] = $value;
					}
					echo 'Solo los valores: '.$locale->getDisjunction($posibleValues).'. ';
					break;
				case 'relation':
					if ($linguistic->isFemale($component['single'])) {
						echo ucfirst($linguistic->a($component['single'])).' válida. ';
					} else {
						echo ucfirst($linguistic->a($component['single'])).' válido. ';
					}
					break;
				case 'date':
					echo 'Una fecha en formato YYYY-MM-DD. ';
					break;
				default:
					$className = $component['type'].'HyperComponent';
					if (class_exists($className, false)==false) {
						$path = KEF_ABS_PATH.'Library/Hfos/HyperForm/Components/'.ucfirst($component['type']).'.php';
						if (file_exists($path)) {
							require_once $path;
						} else {
							throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
						}
					}
					echo call_user_func_array(array($className, 'info'), array()).'. ';
			}
			if (isset($component['notNull'])&&$component['notNull']) {
				echo 'Obligatorio';
			}
			echo '</td></tr>';
		}
		echo '</table></td></tr></table></div>';
	}

	/**
	 * Carga el archivo
	 *
	 */
	public static function load($controller, $config)
	{

		echo Tag::stylesheetLink('backoffice/general');
		View::setRenderLevel(View::LEVEL_LAYOUT);
		echo '<div class="messages">';

		$request = ControllerRequest::getInstance();
		$archivo = $request->getParamFile('archivo');
		if ($archivo==false) {
			Flash::error('El archivo no se pudo cargar al servidor');
		} else {

			if (preg_match('/\.csv$/', $archivo->getFileName())) {
				$db = DbBase::rawConnect();

				$schema = '';
				$config2 = CoreConfig::readEnviroment();

				$schema = '';
				if (isset($config2->database->name)) {
					$schema = $config2->database->name;
				}

				if (isset($config['model'])) {

					$filePath = '/tmp/' . $archivo->getFileName();
					$archivo->moveFileTo($filePath);

					if (!file_exists($filePath)) {
						Flash::error('El archivo no se pudo cargar en tmp: '.$filePath);
						return;
					}

					$modelName = $config['model'];

					$model = EntityManager::getEntityInstance($modelName);
					$modelTableName = $model->getSource();

					//IGNORE
					$query = "LOAD DATA INFILE '/tmp/" . $archivo->getFileName() . "' IGNORE INTO TABLE $schema.$modelTableName
						FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
						LINES TERMINATED BY '\n'";

					try {
						$listQuery = $db->query($query);
					}
					catch(Exception $e) {
						Flash::error($e->getMessage());
						return;
					}
				}
				Flash::success('Se importó correctamente el archivo');
			}

			if (!preg_match('/\.xlsx$/', $archivo->getFileName())) {
				Flash::error('El archivo cargado parece no ser de Microsoft Excel 2007 o superior');
				return;
			}

			try {
				$filePath = KEF_ABS_PATH . 'public/temp/' . time() . '.xlsx';
				$archivo->moveFileTo($filePath);
				Core::importFromLibrary('PHPExcel', 'Classes/PHPExcel.php');
				$reader = PHPExcel_IOFactory::createReader('Excel2007');
				$reader->setReadDataOnly(true);
			}
			catch(Exception $e){
				Flash::error('El archivo está corrupto. '.$e->getMessage());
				return;
			}

			$expectedNumberCells = count($config['fields']);

			$phpExcel = $reader->load($filePath);
			$worksheet = $phpExcel->getActiveSheet();

			$numberField = 0;
			$fields = array();
			$fieldsNames = array();
			foreach ($config['fields'] as $name => $field)
			{
				if ($field['type']=='relation') {
					$field['entity'] = EntityManager::getEntityInstance($field['relation']);
				} else {
					if ($field['type']=='closed-domain') {
						$field['domain'] = array();
						foreach ($field['values'] as $key => $value)
						{
							$field['domain'][$key] = $key;
							$field['domain'][$value] = $key;
						}
					}
				}
				$fields[$numberField] = $field;
				$fieldsNames[$numberField] = $name;
				$numberField++;
			}

			$modelName = $config['model'];
			$countCache = array();
			$findFirstCache = array();
			try {
				set_time_limit(0);
				$line = 1;
				$numberErrors = 0;
				$transaction = TransactionManager::getUserTransaction();
				foreach ($worksheet->getRowIterator() as $row) {
                    $model = false;
					$numberCells = 0;
					$rowErrors = 0;
					$cellIterator = $row->getCellIterator();
                    $debug = "";
					$cellIterator->setIterateOnlyExistingCells(false);
					foreach ($cellIterator as $col => $cell) {
                        if ($numberCells==0) {
							$value = Filter::bring($cell->getCalculatedValue(), $fields[$numberCells]['filters']);
							if ($value=='') {
								continue;
							} else {
								$model = new $modelName();
								$model->setTransaction($transaction);
							}
						}
						if (isset($fieldsNames[$numberCells])) {
							switch ($fieldsNames[$numberCells]['type']) {
								case 'relation':
									$value = $cell->getCalculatedValue();
									if ($value!='') {
										$value = Filter::bring($cell->getValue(), $fieldsNames[$numberCells]['filters']);
										$conditions = "{$fieldsNames[$numberCells]['fieldRelation']}='$value'";
										if (!isset($countCache[$conditions])) {
											$countCache[$conditions] = $fieldsNames[$numberCells]['entity']->count($conditions);
										}
										$exists = $countCache[$conditions];
										unset($conditions);
										if ($exists==false) {
											$value = Filter::bring($cell->getValue(), array('addslaches', 'extraspaces'));
											$conditions = "{$fieldsNames[$numberCells]['detail']}='$value'";
											if (!isset($findFirstCache[$conditions])) {
												$findFirstCache[$conditions] = $fieldsNames[$numberCells]['entity']->findFirst($conditions);
											}
											$relationModel = $findFirstCache[$conditions];
											if ($relationModel!=false) {
												$value = $relationModel->readAttribute($fieldsNames[$numberCells]['fieldRelation']);
											}
											unset($relationModel);
											unset($conditions);
										}
									};
									break;
								case 'closed-domain':
									$value = $cell->getCalculatedValue();
									$value = trim($value);
									if (isset($fieldsNames[$numberCells]['domain'][$value])) {
										$value = $fieldsNames[$numberCells]['domain'][$value];
									} else {
										$value = '';
									}
									break;
								case 'int':
								case 'decimal':
									$value = Filter::bring($cell->getCalculatedValue(), $fieldsNames[$numberCells]['filters']);
									if (isset($fieldsNames[$numberCells]['maxlength'])) {
										if (i18n::strlen($value)>$fieldsNames[$numberCells]['maxlength']) {
											Flash::error('El tamaño máximo permitido para el campo '.$fieldsNames[$numberCells]['single'].' no es el correcto en la línea '.$line);
											$rowErrors++;
											$numberErrors++;
										}
									}
									break;
								case 'date':
									$val = $cell->getCalculatedValue();
									$val = PHPExcel_Style_NumberFormat::toFormattedString($val, "YYYY-MM-DD");
									$value = Filter::bring($val, $fieldsNames[$numberCells]['filters']);
									if (!empty($value)) {
										if (!preg_match('/[0-9]{4}\-[0-9]{2}-[0-9]{2}/', $value)) {
											Flash::error('El formato de fecha para el campo ' . $fieldsNames[$numberCells]['single'] . ' no es el correcto en la línea');
											$rowErrors++;
											$numberErrors++;
										}
									}
									break;
								default:
									$value = utf8_decode(Filter::bring($cell->getCalculatedValue(), $fields[$numberCells]['filters']));
							}
							$debug .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;Field {$fieldsNames[$numberCells]}: $value";
							$model->writeAttribute($fieldsNames[$numberCells], $value);
							unset($value);
						}
						$numberCells++;
						unset($cell);
					}
                    //throw new Exception($debug);
					if ($numberCells != $expectedNumberCells) {
						Flash::error('El número de columnas debe ser ' . $expectedNumberCells . ' (' . $numberCells . '), en la línea ' . $line);
						$numberErrors++;
					} else {
						if ($rowErrors == 0) {
							if ($model != false) {
								if ($model->save() == false) {
									foreach ($model->getMessages() as $message) {
                                        $columnaMal = array_search($message->_field, array_keys($config['fields']));
                                        $columnaMal += 1;
										Flash::error($message->getMessage() . ' en la línea ' . $line . ', columna: ' . $columnaMal);
										$numberErrors++;
									}
								}
							}
						}
					}
					if ($line % 500 == 0) {
						GarbageCollector::collectCycles();
					}
					$line++;
					unset($cellIterator);
					unset($row);
					unset($model);
				}
				if($numberErrors>0){
					$transaction->rollback();
				} else {
					$transaction->commit();
					Flash::success('Se importó correctamente el archivo');
				}
			}
			catch(TransactionFailed $e){

			}
		}
		echo '</div>';

	}

}
