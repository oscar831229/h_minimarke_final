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
 * Settings
 *
 * Componente para leer/escribir la tabla configuration dependiendo de la aplicación activa
 *
 */
class Settings extends UserComponent
{

	private static $_data = array();

	public static function get($name, $appCode='')
	{
		if (!$appCode) {
			$appCode = CoreConfig::getAppSetting('code');
		}
		$configuration = self::getModel('Configuration')->findFirst("application='$appCode' AND name='$name'");
		if ($configuration==false) {
			return null;
		} else {
			return $configuration->getValue();
		}
	}

	public static function setData($index, $values)
	{
		self::$_data[$index] = $values;
	}

	public static function makeForm($settings)
	{
		////////////////////////
		// Standard Form
		////////////////////////
		echo '<table align="center">';
		foreach ($settings as $name => $setting) {
			echo '<tr>';
			echo '<td align="right"><label for="', $name, '">', $setting['description'] , '</label></td><td>';
			switch($setting['type']){
				case 'text':
					echo Tag::textField(array($name, 'size' => $setting['size'], 'maxlength' => $setting['maxlength']));
					break;
				case 'int':
					echo Tag::numericField(array($name, 'size' => 5));
					break;
				case 'comprob':
					echo Tag::select(array($name, self::$_data['comprobs'], 'using' => 'codigo,nom_comprob', 'useDummy' => 'yes'));
					break;
				case 'centros':
					echo Tag::select(array($name, self::$_data['centros'], 'using' => 'codigo,nom_centro', 'useDummy' => 'yes'));
					break;
				case 'documentos':
					$docs = EntityManager::get('Documentos')->find(array('order'=>'codigo ASC'));
					$values = array();
					foreach ($docs as $doc)
					{
						$values[$doc->getCodigo()] = $doc->getNomDocumen();
					}
					echo Tag::selectStatic(array($name, $values, 'useDummy' => 'yes'));
					break;
				case 'cuenta':
					echo HfosTag::cuentaField(array($name));
					break;
				case 'almacen':
					echo Tag::select(array($name, self::$_data['almacenes'], 'using' => 'codigo,nom_almacen', 'useDummy' => 'yes'));
					break;
				case 'consecutivos':
					echo Tag::select(array($name, self::$_data['consecutivos'], 'using' => 'id,detalle', 'useDummy' => 'yes'));
					break;
				case 'closed-domain':
					echo Tag::selectStatic(array($name, $setting['values'], 'useDummy' => 'yes'));
					break;
				case 'textarea':
					echo Tag::textarea(array($name, 'cols' => $setting['cols'], 'rows' => $setting['rows']));
					break;
				case 'relation':
					$dataTable = EntityManager::get($setting['table'])->find(array("order"=>$setting['sort']));
					echo Tag::select(array($name, $dataTable, 'using' => $setting['fieldRelation'].",".$setting['detail'], 'useDummy' => 'yes'));
					break;
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}

	public static function getForm($settings)
	{
		echo Tag::form(array('settings/save', 'class' => 'settingsForm'));

		if (isset($settings['activeTabs']) && $settings['activeTabs']==true) {
			////////////////////////
			// Form with Tabs
			///////////////////////
			echo '
			<div class="incluir hfosContent">
				<div class="formMain">
					<div class="formContent">
						<div class="formPannel" align="right">';

			foreach ($settings['tabs'] as $fieldset => $data) {
				echo '
				<fieldset class="tabbed">
					<legend>'.$fieldset.'</legend>';

					Settings::makeForm($data);

				echo '</fieldset>';
			}
			echo '		</div>
					</div>
				</div>
			</div>';
		} else {
			////////////////////////
			// Standard Form
			////////////////////////
			Settings::makeForm($settings);
		}
		echo Tag::endForm();
	}

}