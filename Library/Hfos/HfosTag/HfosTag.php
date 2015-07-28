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
 * HfosTag
 *
 * Tag especificos del HFOS
 *
 */
class HfosTag extends Tag {

	/**
 	 * Imprime un titulo y un ícono
 	 *
 	 * @access 	public
 	 * @param 	mixed $params
 	 * @return 	string
 	 * @static
 	 */
	static public function iconTitle($icon, $title){
		return '<table class="hyTitle"><tr><td>'.Tag::image('backoffice/icons/'.$icon).'</td><td><h2>'.$title.'</h2></td></tr></table>';
	}

	/**
 	 * Componente para capturar cuentas contables
 	 *
 	 * @access 	public
 	 * @param 	mixed $params
 	 * @return 	string
 	 * @static
 	 */
	static public function cuentaField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		$nombreCuenta = '';
		if($value!=''){
			$value = Filter::bring($value, 'cuentas');
			$cuenta = BackCacher::getCuenta($value);
			if($cuenta!=false){
				$nombreCuenta = $cuenta->getNombre();
			} else {
				$nombreCuenta = 'NO EXISTE LA CUENTA';
			}
		}
		$code = '<table cellspacing="0" class="cuentaCompleter">
			<tr>
				<td>'.Tag::numericField(array($params[0], 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($params[0].'_det', 'size' => 35, 'class' => 'cuentaDetalle', 'value' => $nombreCuenta, 'placeholder' => 'Buscar por nombre')).'</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addCuentaCompleter("'.$params[0].'")</script>';
		return $code;
	}

	/**
 	 * Componente para capturar niif contables
 	 *
 	 * @access 	public
 	 * @param 	mixed $params
 	 * @return 	string
 	 * @static
 	 */
	static public function niifField($params)
	{
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		$nombreNiif = '';
		if ($value) {
			$value  = Filter::bring($value, 'niif');
			$niif = BackCacher::getNiif($value);
			if ($niif != false) {
				$nombreNiif = $niif->getNombre();
			} else {
				$nombreNiif = 'NO EXISTE LA CUENTA';
			}
		}
		$code = '<table cellspacing="0" class="cuentaCompleter">
			<tr>
				<td>'.Tag::numericField(array($params[0], 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($params[0].'_det', 'size' => 35, 'class' => 'cuentaDetalle', 'value' => $nombreNiif, 'placeholder' => 'Buscar por nombre')).'</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addNiifCompleter("'.$params[0].'")</script>';
		return $code;
	}

	/**
 	 * Componente para capturar terceros
 	 *
 	 * @access 	public
 	 * @param 	mixed $params
 	 * @return 	string
 	 * @static
 	 */
	static public function terceroField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		$nombreTercero = '';
		if($value!=''){
			$value = Filter::bring($value, 'terceros');
			$nit = BackCacher::getTercero($value);
			if($nit!=false){
				$nombreTercero = $nit->getNombre();
			} else {
				$nombreTercero = 'NO EXISTE EL TERCERO';
			}
		}
		if(isset($params['create'])){
			if($params['create']){
				$create = 'true';
			} else {
				$create = 'false';
			}
		} else {
			$create = 'false';
		}
		$code = '<table cellspacing="0" class="terceroCompleter">
			<tr>
				<td>'.Tag::numericField(array($params[0], 'size' => '17', 'maxlength' => 17, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($params[0].'_det', 'size' => 35, 'class' => 'terceroDetalle', 'value' => $nombreTercero)).'</td>';
			if($create=='true'){
				$code.= '<td><input type="button" class="crearNit" id="'.$params[0].'_create" value="Crear"/></td>';
			}
			$code.= '</tr>
		</table>
		<script type="text/javascript">HfosCommon.addTerceroCompleter("'.$params[0].'", '.$create.')</script>';
		return $code;
	}

	/**
 	 * Componente para capturar items
 	 *
 	 * @access 	public
 	 * @param 	mixed $params
 	 * @return 	string
 	 * @static
 	 */
	static public function itemField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		$nombreItem = '';
		if($value!=''){
			$value = Filter::bring($value, 'int');
			$inve = BackCacher::getItem($value);
			if($inve!=false){
				$nombreItem = $inve->getDescripcion();
			} else {
				$nombreItem = 'NO EXISTE EL ITEM';
			}
		}
		$code = '<table cellspacing="0" class="itemCompleter">
			<tr>
				<td>'.Tag::numericField(array($params[0], 'size' => '7', 'maxlength' => 12, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($params[0].'_det', 'size' => 35, 'class' => 'itemDetalle', 'value' => $nombreItem, 'placeholder' => 'Buscar por nombre')).'</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addItemCompleter("'.$params[0].'")</script>';
		return $code;
	}

	/**
 	 * Componente para capturar activos fijos
 	 *
 	 * @access 	public
 	 * @param 	mixed $params
 	 * @return 	string
 	 * @static
 	 */
	static public function activoField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		$nombreActivo = '';
		if($value!=''){
			$value = Filter::bring($value, 'int');
			$nit = BackCacher::getActivo($value);
			if($nit!=false){
				$nombreActivo = $nit->getNombre();
			} else {
				$nombreActivo = 'NO EXISTE EL ACTIVO FIJO';
			}
		}
		$code = '<table cellspacing="0" class="itemCompleter">
			<tr>
				<td>'.Tag::numericField(array($params[0], 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($params[0].'_det', 'size' => 35, 'class' => 'itemDetalle', 'value' => $nombreActivo)).'</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addActivoCompleter("'.$params[0].'")</script>';
		return $code;
	}

 /**
  *
  * Metodo que genera un tag de autocomplete para diferidos
  */
  static public function diferidosField($params){
    $numberArguments = func_num_args();
    $params = Utils::getParams(func_get_args(), $numberArguments);
    if(!isset($params[0])) {
      $params[0] = $params['id'];
    }
    if(!isset($params['name'])||!$params['name']){
      $params['name'] = $params[0];
    }
    if(isset($params['value'])){
      $value = $params['value'];
      unset($params['value']);
    } else {
      $value = self::getValueFromAction($params[0]);
    }
    $nombreDiferido = '';
    if($value!=''){
      $value = Filter::bring($value, 'int');
      $nit = BackCacher::getActivo($value);
      if($nit!=false){
        $nombreDiferido = $nit->getNombre();
      } else {
        $nombreDiferido = 'NO EXISTE EL DIFERIDO';
      }
    }
    $code = '<table cellspacing="0" class="itemCompleter">
      <tr>
        <td>'.Tag::numericField(array($params[0], 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
        <td>'.Tag::textField(array($params[0].'_det', 'size' => 35, 'class' => 'itemDetalle', 'value' => $nombreDiferido)).'</td>
      </tr>
    </table>
    <script type="text/javascript">HfosCommon.addDiferidosCompleter("'.$params[0].'")</script>';
    return $code;
  }

	/**
 	 * Componente para capturar ciudades
 	 *
 	 * @access 	public
 	 * @param 	mixed $params
 	 * @return 	string
 	 * @static
 	 */
	static public function locationField($params){
		$numberArguments = func_num_args();
		$params = Utils::getParams(func_get_args(), $numberArguments);
		if(!isset($params[0])) {
			$params[0] = $params['id'];
		}
		if(!isset($params['name'])||!$params['name']){
			$params['name'] = $params[0];
		}
		if(isset($params['value'])){
			$value = $params['value'];
			unset($params['value']);
		} else {
			$value = self::getValueFromAction($params[0]);
		}
		$nombreCiudad = '';
		if($value!=''){
			$value = Filter::bring($value, 'int');
			$location = BackCacher::getLocation($value);
			if($nit!=false){
				$nombreCiudad= $nit->getName();
			} else {
				$nombreCiudad = 'NO EXISTE LA CIUDAD';
			}
		}
		$code = Tag::hiddenField(array($params[0], 'value' => $value)).' '.Tag::textField(array($params[0].'_det', 'size' => 30, 'value' => $nombreCiudad));
		$code.='<div id="'.$params[0].'_choices" class="autocomplete"></div>';
		$code.='<script type="text/javascript">HfosCommon.addLocationCompleter("'.$params[0].'")</script>';
		return $code;
	}

	/**
	 * Crea un selector de tipo de reporte
	 *
	 * @param boolean $initialize
	 * @param boolean $showScreen
	 */
	public static function reportTypeTag($initialize=true, $showScreen=true, $options=false){
		$code = '<div class="hyReportType">
			<table align="center" class="hyReportTable" width="100%" cellspacing="0" cellpadding="0">';
				if($showScreen){
					$code.='<tr>
						<td width="40%" align="center">'.Tag::image('backoffice/screen.png').'</td>
						<td>Pantalla</td>
					</tr>';
				}
				$code.='<tr>
					<td align="center">'.Tag::image('backoffice/firefox.png').'</td>
					<td>HTML</td>
				</tr>
				<tr>
					<td align="center">'.Tag::image('backoffice/excel.png').'</td>
					<td>Excel</td>
				</tr>
				<tr>
					<td align="center">'.Tag::image('backoffice/pdf.png').'</td>
					<td>PDF</td>
				</tr>
				<tr>
					<td align="center">'.Tag::image('backoffice/txt.png').'</td>
					<td>Texto</td>
				</tr>
				<tr>
					<td align="center">'.Tag::image('backoffice/csv.png').'</td>
					<td>CSV</td>
				</tr>
			</table>
			<div class="hyChReportType" style="display:none">
				<table width="100%">
					<tr>
						<td align="right"><label for="reportType">Tipo Salida</label></td>
						<td>
							<select name="reportType" class="reportType">';
								if($showScreen){
									$code.='<option value="screen">Pantalla</option>';
								}
								if (!$options) {
									$code.='<option value="html">HTML</option>
									<option value="excel">Excel</option>
									<option value="pdf">PDF</option>
									<option value="text">Texto</option>
									<option value="csv">CSV</option>';
								} else {
									if (is_array($options)) {
										if (isset($options['showHtml']) && $options['showHtml']) {
											$code.='<option value="html">HTML</option>';
										}
										if (isset($options['showExcel']) && $options['showExcel']) {
											$code.='<option value="excel">Excel</option>';
										}
										if (isset($options['showPdf']) && $options['showPdf']) {
											$code.='<option value="pdf">PDF</option>';
										}
										if (isset($options['showText']) && $options['showText']) {
											$code.='<option value="text">Texto</option>';
										}
										if (isset($options['showCsv']) && $options['showCsv']) {
											$code.='<option value="csv">CSV</option>';
										}
									}
								}
								$code.='
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>';
		if($initialize==true){
			$code.='<script type="text/javascript">HfosReportType.observeReportType();</script>';
		}
		return $code;
	}

	/**
	 * Funcion socia de makeAclThree que genera el html interno
	 *
	 * @param array $accessList: Listado de permisos de aplicacion y sus opciones
	 * @param array $menu: Es la opcion del menu que se esta generando
	 * @return $code: return html
	 */
	protected function renderDependend($accessList, $menu=Array()){
		$code = "";
		if(!is_array($menu) || !is_array($accessList)){
			$code .= "var menu empty";
			return $code;
		}
		$code .= '<ul>';
		$code .= '<li class="resource-access"><input type="checkbox" name="access[]" value="/index" />'.
				$menu['title'].'<div class="spacer"> / </div>'.
				'<div class="action-desc">'.$menu['description'].'</div>'.
				'<br>';
				// Si existe optiones dentro del menu actual
				if(is_array($menu["options"])){
					$code .= '<ul>';
					//recorremos lso controladores que tiene esa opcion ene le menu
					foreach($menu["options"] as $controller){
						if(!isset($accessList[$controller])){
							$code .= 'No existe '.$controller.' en accesslist';
							continue;
						}
						$code .= '
						<li class="resource-access">
							<input type="checkbox" name="access[]" value="'.$controller.'/index" />'.
							$accessList[$controller]["description"];


						if(is_array($accessList[$controller]['actions'])){
							$code .= '<ul class="action-group">';
							foreach($accessList[$controller]['actions'] as $action => $name){
								if($action!='index'){
									$code .= '
									<li class="action-access">
										<input type="checkbox" name="access[]" value="'.$controller.'/'.$action.'" />'.
										$name["description"].' '.$accessList[$controller]["description"].
										'<br/>
									</li>';
								}
							}
							$code .= '</ul>';
						}
						$code .= '</li>';
					}
					$code .= '</ul>';
				}
			$code .= '</li>';
		$code .= '</ul>';


		return $code;
	}

	/**
	 *
	 * Metodo estatico que genera un arbol segun el acl.php de una aplicacion como IN/CO/NO
	 * @param Array $config: debe tener la siguiente onfiguración
	 * Array(
	 * 	-appsName: Es el nombre de la apliacion
	 * 	-menuDisposition: Es el array que esta en el acl.php que maneja las opciones del menu que  puede ver
	 *  -accessList: Son los permisos que hay dentro de acl.php
	 * )
	 */
	public static function makeAclThree($config){

		$code = "";

		if(!isset($config["appsName"])){
			$code .= '<b>Asignar variable $appsName</b>';
		}
		else{
			$appsName = $config["appsName"];
		}

		if(!isset($config["menuDisposition"])){
			$code .= '<b>Asignar variable $menuDisposition</b>';
		}
		else{
			$menuDisposition = $config["menuDisposition"];
		}

		if(!isset($config["accessList"])){
			$code .= '<b>Asignar variable $accessList</b>';
		}
		else{
			$accessList = $config["accessList"];
		}

		/**
		 * Si se obtuvo los array de ACL segun identity/security/rules
		 */
		if(isset($menuDisposition) && isset($accessList)){
			$dependend=array();
			$code .= '<ul>';
			$code .= '<li class="resource-access"><input type="checkbox" name="access[]" value="/index" />'.
			$appsName.'<div class="spacer"> / </div>'.
					'<div class="action-desc">Ingresar a la aplicaci&oacute;n </div>'.
					'<br>';

			foreach($menuDisposition as $k =>$menu){
				$code .= '<li><div>';
				$code .= $this->renderDependend($accessList, $menu);
				$code .= '</div></li>';
			}

			return $code;
		}
	}

	/**
	 * Crea un combo box con los prefijos de facturación usados en la aplicación
	 *
	 * @param	string $name
	 * @return	string
	 */
	public static function prefijoFactura($name){
		$prefijos = EntityManager::get('Factura')->find("estado='A'", 'group: prefac', 'columsn: prefac');
		return Tag::select($name, $prefijos, 'using: prefac,prefac');
	}


	/**
	 * MEtood que genera un campo especial de Hyperform en el una view
	 *
	 * @param array $component(
	 *     type : Comprob, Centro, Cuenta, Socio, Tercero, etc..
	 *     name : nombre campo
	 *     classForm: Clase de form
	 * )
	 */
	public static function addHyperFormSpecialField($component=array()){
	    $name = $component['name'];
        $className = $component['type'].'HyperComponent';
		if(class_exists($className, false)==false){
			$path = 'Library/Hfos/HyperForm/Components/'.ucfirst($component['type']).'.php';
			if(file_exists($path)){
				require_once $path;
			} else {
				throw new HyperFormException("No existe el componente de HyperForm llamado '".$component['type']."'");
			}
		}
		$html = call_user_func_array(array($className, 'build'), array($name, $component, null, $component['classForm']));

		return $html;
	}

	/**
	 * Metodo que genera html de iframe para upload images
	 *
	 * @return string html
	 */
	public static function uploadImages($value=''){
	    if($value && $value!='@'){
	        Session::setData('imgUploaded',$value);
	    }

	    $html = '
		<div id="iframe_container">
            <iframe src="'.Utils::getKumbiaUrl(Router::getController().'/uploadField').'" frameborder="0" style="height:250px;width:400px;">
            </iframe>
        </div>
		';
		return $html;
	}

	/**
	 * Metodo que contiene lo q se vera dentro del iframe
	 *
	 * @return string html
	 */
	public static function uploadField($img=''){
		$html = '
		<form name="iform" id="iform" action="upload" method="post" enctype="multipart/form-data">
			<input id="file" type="file" name="image" onChange="document.getElementById(\'iform\').submit();" /><br>
            		<span style="font-size:11px; color:#666666;">only gif, png, jpg files.</span>
			<input type="hidden" value="" name="div_id" />
		</form>
		';
		$imgUploaded = Session::getData('imgUploaded');
		if($imgUploaded){
		    $html .= '<br/>'.Tag::image(array($imgUploaded,'width'=> '120', 'onclick'=>'window.open("'.$imgUploaded.'")'));
		}
		return $html;
	}
}
