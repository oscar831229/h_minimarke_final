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
 * RecetaController
 *
 * Controlador de las recetas estándar
 *
 */
class RecetaController extends ApplicationController {

	private $conditions = '';

	public function initialize(){
		$this->setTemplateAfter("admin_menu");
		$this->setPersistance(true);
	}

	public function indexAction(){
		$this->loadModel('Inve', 'Recetap');
		$this->renderJavascript('new Event.observe(window,"load",function(){new Receta.initializeIndex()})');
	}

	public function buscarAction(){

		$conditions = array();
		$numero = $this->getPostParam('num_receta', 'int');
		$nombre = $this->getPostParam('nombre', 'extraspaces', 'striptags');
		$item = $this->getPostParam('item', 'alpha');
		$subreceta = $this->getPostParam('subreceta', 'alpha');
		$page = $this->getRequestParam('pag','int');
		$page = $page <= 0 ? 1 : $page;

		$controllerRequest = ControllerRequest::getInstance();
		if(!$controllerRequest->isSetQueryParam('pag')){
			if($numero>0){
				$conditions[] = "numero_rec = $numero";
			}
			if($nombre!=""){
				$conditions[] = "nombre LIKE '%$nombre%'";
			}
			if(count($conditions)>0){
				$conditions[] = "almacen = 1";
				$this->conditions = join(' AND ', $conditions);
				$recetas = $this->Recetap->find(array($this->conditions, 'order' => 'nombre'));
			} else {
				if($item != '' || $subreceta != ''){
					return $this->routeTo(array('controller' => 'receta', 'action' => 'findComponent'));
				} else {
					$this->conditions = '';
					$recetas = $this->Recetap->find(array('order' => 'nombre'));
				}
			}
		} else {
			if($this->conditions != ''){
				$recetas = $this->Recetap->find(array($this->conditions, 'order' => 'nombre'));
			} else {
				$recetas = $this->Recetap->find(array('order' => 'nombre'));
			}
		}

		$total = count($recetas);
		if($total>0){
			$this->setParamToView('total_result', $total);
			$recetas = Tag::paginate($recetas,$page,25);
			$inicial = 25 * ($page - 1) + 1;
			$final = $page == $recetas->last ? $total : 25 * $page;
			$this->setParamToView('inicial', $inicial);
			$this->setParamToView('final', $final);
			$this->setParamToView('recetas', $recetas);
			$this->setParamToView('page', $page);
		} else {
			Flash::notice('No se encontraron recetas');
			$this->routeToAction('index');
		}

	}

	public function nuevoAction(){
		$numero_rec = $this->Recetap->maximum('numero_rec')+1;
		Tag::displayTo('numero_rec', sprintf('%04d', $numero_rec));
		Tag::displayTo('almacen', '1');
		$this->loadModel('Menus');
		$this->renderJavascript('new Event.observe(window,"load",function(){new Receta.initialize()})');
	}

	public function editarAction($almacen=0, $numeroReceta=0){
		$almacen = $this->filter($almacen, 'int');
		$numeroReceta = $this->filter($numeroReceta, 'int');
		$recetap = $this->Recetap->findFirst("numero_rec=$numeroReceta AND almacen=$almacen");
		if($recetap==false){
			Flash::error('No existe la receta');
			return $this->routeTo(array('action' => 'index'));
		}
		$attributes = $recetap->getAttributes();
		foreach($attributes as $att){
			Tag::displayTo($att, $recetap->$att);
		}
		$this->setParamToView("foto", $recetap->foto);
		$this->renderJavascript('new Event.observe(window,"load",function(){new Receta.initializeEdit()})');
		Router::setId($almacen.'/'.$numeroReceta);
		$this->setParamToView('numero_rec', $numeroReceta);
		$this->loadModel('Menus');
	}

	public function eliminarAction($almacen=0, $numeroReceta=0){
		$almacen = $this->filter($almacen, 'int');
		$numeroReceta = $this->filter($numeroReceta, 'int');
		$recetap = $this->Recetap->findFirst("numero_rec=$numeroReceta AND almacen=$almacen");
		if($recetap==false){
			Flash::error('No existe la receta');
			return $this->routeTo(array('action' => 'index'));
		}
		if($this->MenusItems->count('codigo_referencia="'.$numeroReceta.'"')<=0){
			if($this->MenusItems->count('codigo_referencia="'.$numeroReceta.'" AND estado="A"')<=0){
				$recetap->update('estado: I');
				Flash::success('La receta fue inactivada satisfactoriamente');
				return $this->routeTo(array('action' => 'index'));
			}
			Flash::error('La receta está asociada a un Item de Menú');
			return $this->routeTo(array('action' => 'index'));
		}
		$this->Recetal->deleteAll('almacen="'.$almacen.'" AND numero_rec="'.$numeroReceta.'"');
		$recetap->delete();
		Flash::success('La receta fue eliminada correctamente');
		return $this->routeTo(array('action' => 'index'));
	}

	public function guardarAction($almacen=1, $numeroReceta=0) {

		$almacen = 1;
		$numeroReceta = $this->filter($numeroReceta, 'int');

		$controllerRequest = $this->getRequestInstance();
		if($controllerRequest->isPost()){

			try {

				$foto = null;
				if(count($_FILES)){
					$foto = '';
					foreach($_FILES as $file){
						if(!$file['error']){
							$hash = md5_file($file['tmp_name']);
							$foto = $hash.'.jpg';
							move_uploaded_file($file['tmp_name'], 'public/img/pos2/recetas/'.$foto);
						}
					}
				}

				$transaction = TransactionManager::getUserTransaction();

				$this->Recetal->setTransaction($transaction);
				$this->Recetap->setTransaction($transaction);

				$recetap = $this->Recetap->findFirst("almacen=1 AND numero_rec=$numeroReceta");
				if($recetap==false){
					$recetap = new Recetap();
					$almacen = 1;
					$recetap->almacen = $almacen;
					$recetap->estado = 'A';
					$numeroReceta = $this->getPostParam('numero_rec', 'alpha');
					$action = 'nuevo';
					$message = 'El registro ha sido ingresado con éxito. Se encuentra editándolo';
				} else {
					$action = 'editar';
					$message = 'El registro ha sido modificado con éxito. Se encuentra editándolo';
				}

				$types = $recetap->getDataTypes();
				$attributes = $recetap->getAttributes();
				foreach($attributes as $attribute){
					if($controllerRequest->isSetPostParam($attribute)){
						$filter = $this->getFilter($types[$attribute]);
						$value = $controllerRequest->getParamPost($attribute, $filter);
						if($attribute!='id'){
							if(is_array($value)){
								$this->renderJavascript('new Event.observe(window,"load",function(){new Receta.showMessage("'.$attribute.'", "error")})');
								$transaction->rollback();
							}
							$recetap->writeAttribute($attribute, $value);
						}
					}
				}

				$recetap->precio_costo = CostoInventario::getCosto($recetap->numero_rec, 'R');
				$recetap->preparacion = $controllerRequest->getParamPost('preparacion');

				if($foto){
					$recetap->foto = $foto;
				} else {
					$recetap->foto = $this->getPostParam('foto');
				}

				if($recetap->save()==false){
					$message = '';
					foreach($recetap->getMessages() as $message){
						$message.= $message->getMessage().'<br />';
					}
					$this->renderJavascript('new Event.observe(window, "load", function(){new Receta.showMessage("'.$message.'", "error")})');
					$transaction->rollback();
				}

				$types = $this->Recetal->getDataTypes();
				$attributes = $this->Recetal->getAttributes();
				foreach($attributes as $attribute){
					if($controllerRequest->isSetPostParam($attribute)){
						$filter = $this->getFilter($types[$attribute]);
						$values[$attribute] = $controllerRequest->getParamPost($attribute, $filter);
					}
				}

				$values['action'] = $controllerRequest->isSetPostParam('action') ? $controllerRequest->getParamPost('action') : array();
				$numRows = count($values['action']);
				for($i=0;$i<$numRows;$i++){
					if($values['action'][$i]=='add'){
						$recetal = $this->Recetal->findFirst('almacen="'.$almacen.'" AND numero_rec="'.$numeroReceta.'" AND item="'.$values['item'][$i].'"');
						if($recetal==false){
							$recetal = new Recetal();
							$recetal->writeAttribute('almacen', $almacen);
							$recetal->writeAttribute('item', $values['item'][$i]);
							$recetal->writeAttribute('numero_rec', $numeroReceta);
						}
						$recetal->setTransaction($transaction);
						foreach($attributes as $attribute){
							if(!in_array($attribute,array('id','almacen','numero_rec')) && $controllerRequest->isSetPostParam($attribute)) {
								$recetal->writeAttribute($attribute, $values[$attribute][$i]);
							}
						}
						if($recetal->tipol=='R'){
							if($recetal->item==$numeroReceta){
								$transaction->rollback('Una receta no puede contenerse a sí misma');
							}
						}
						if($recetal->save()==false){
							$message = '';
							foreach($recetal->getMessages() as $message){
								$message.= $message->getMessage().'<br />';
							}
							$transaction->rollback($message);
						}
					} else {
						$recetal = $this->Recetal->findFirst('almacen="'.$almacen.'" AND numero_rec="'.$numeroReceta.'" AND item="'.$values['item'][$i].'"');
						if($recetal != false) {
							$recetal->setTransaction($transaction);
							$recetal->delete();
						}
					}
				}
				Flash::success($message);
				$transaction->commit();
			}
			catch(TransactionFailed $e){
				Flash::error($e->getMessage());
				if($action == 'editar') {
					return $this->routeToURI(Router::getApplication().'/'.Router::getController().'/editar'.'/'.$almacen.'/'.$numeroReceta);
				}
				return $this->routeTo(array('action' => $action));
			}
		}

		return $this->routeToURI(Router::getApplication().'/'.Router::getController().'/editar'.'/'.$almacen.'/'.$numeroReceta);

	}

	public function getFilter($type){
		if(preg_match('/^int/',$type)){
			return 'int';
		} else {
			if(preg_match('/^decimal/',$type)){
				return 'float';
			} elseif(preg_match('/^text/',$type)){
				return 'striptags';
			} else {
				return 'striptags';
			}
		}
	}

	public function getRecetalAction(){
		$this->setResponse('json');
		$result = array('type' => 'success');
		try {
			$numero_rec = $this->getPostParam('numero_rec','alpha');
			if($numero_rec!=''){
				$recetals = $this->Recetal->find('almacen=1 AND numero_rec="'.$numero_rec.'"');
				$result['data'] = array();
				foreach($recetals as $recetal){
					$linea = array();
					$nombreUnidad = '';
					if($recetal->tipol == 'I'){
						$inve = $this->Inve->findFirst('item="'.$recetal->item.'" AND estado = "A"');
						if($inve != false){
							$unidad = $this->Unidad->findFirst('codigo="'.$inve->getUnidad().'"');
							if($unidad!=false){
								$nombreUnidad = $unidad->nom_unidad;
							}
						} else {
							$nombreUnidad = 'UNID';
						}
					} else {
						$nombreUnidad = 'UNID';
					}
					$costo = 0;
					if($recetal->divisor>0){
						$costo = CostoInventario::getCosto($recetal->item, $recetal->tipol);
						$costo = $costo*$recetal->cantidad/$recetal->divisor;
					}
					$linea['valor']['value'] = $costo;
					$linea['valor']['detail'] = Currency::number($costo);
					foreach($recetal->getAttributes() as $attribute){
						if($attribute!='valor'){
							$linea[$attribute]['value'] = $recetal->readAttribute($attribute);
							$linea[$attribute]['detail'] = $recetal->getDetail($attribute);
						}
					}
					$linea['unidad']['value'] = $nombreUnidad;
					$linea['unidad']['detail'] = $nombreUnidad;
					$result['data'][] = $linea;
				}
			}
		}
		catch(Exception $e){
			$result['type'] = 'exception';
			$result['msg'] = $e->getMessage();
		}
		return $result;
	}

	public function getReferenciasAction()
	{
		$this->setResponse('json');
		$result = array('type' => 'success');
		try {
			$unidad = 'UNID';
			$tipo = $this->getPostParam('tipol','alpha');
			if ($tipo == 'R') {
				$refs = $this->Recetap->find('order: nombre');
				$unidad = 'UNID';
			} else {
				$refs = $this->Inve->find('estado = "A"','order: descripcion');
			}
			if (count($refs) == 0) {
				$result['type'] = 'error';
				$result['msg'] = 'No hay datos';
			} else {
				$result['data'] = array();
			}
			if ($tipo == 'R') {
				$id = 'numero_rec';
				$detalle = 'nombre';
			} else {
				$id = 'item';
				$detalle = 'descripcion';
			}
			foreach ($refs as $ref) {
				$costo = 0;
				$divisor = 1;
				if ($tipo == 'I' && $ref->getUnidad() != '') {
					$unidadModel = $this->Unidad->findFirst('codigo="'.$ref->getUnidad().'"');
					if ($unidadModel) {
						$unidad = utf8_encode(utf8_decode($unidadModel->nom_unidad));
						$magnitudId = (int) $unidadModel->magnitud;
						if ($magnitudId) {
							$magnitudes = $this->Magnitudes->findFirst($magnitudId);
							if ($magnitudes) {
								$divisor = $magnitudes->getDivisor();
							}
						}
					} else {
						$unidad = 'UNID';
					}
				}
				$reg = array('id' => $ref->$id, 'detalle' => utf8_encode($ref->$detalle), 'unidad' => $unidad, 'costo' => $costo, "divisor" => $divisor);
				$result['data'][] = $reg;
			}
		} catch (Exception $e) {
			$result['type'] = 'exception';
			$result['msg'] = $e->getMessage();
			$result['msg'] = $e.'';
		}
		return $result;
	}

	public function getCostoAction()
	{
		$this->setResponse('json');
		$result = array('type' => 'success');
		try{
			$item = $this->getPostParam('item','alpha');
			$tipo = $this->getPostParam('tipol', 'onechar');
			$result['data']['item'] = $item;
			$costo = CostoInventario::getCosto($item, $tipo);
			if($costo == 0) {
				$result['type'] = 'error';
				$result['msg'] = 'No se pudo obtener el costo';
			}
			$result['data']['costo'] = $costo;
		}
		catch(Exception $e){
			$result['type'] = 'exception';
			$result['msg'] = $e->getMessage();
		}
		return $result;
	}

	public function getSalonValoresAction(){
		$this->setResponse('json');
		$result = array('type' => 'success');
		try{
			$item = $this->getPostParam('numero_rec','alpha');
			$datos = array();
			$salones = $this->Salon->find("estado='A'");
			foreach($salones as $salon){
				$menusItem = $this->MenusItems->findFirst('tipo_costo = "R" AND codigo_referencia = "'.$item.'"');
				if($menusItem != false) {
					$salonMenusItems = $this->SalonMenusItems->findFirst('menus_items_id = "'.$menusItem->id.'" AND salon_id = "'.$salon->id.'"');
					if($salonMenusItems != false){
						if($salonMenusItems->valor>0){
							$valor = $salonMenusItems->valor;
						} else {
							$valor = $menusItem->valor;
						}
					} else {
						$valor = $menusItem->valor;
					}
					$datos[$salon->nombre] = $valor;
				}
			}
			$result['data'] = $datos;
		}
		catch(Exception $e){
			$result['type'] = 'exception';
			$result['msg'] = $e->getMessage();
		}
		return $result;
	}

	public function reporteIndividual($numero_rec){
		$this->setResponse('view');
		$recetap = $this->Recetap->findFirst('almacen="1" AND numero_rec="'.$numero_rec.'"');
		if($recetap == false) {
			return;
		}
		$recetal = $this->Recetal->find('almacen="1" AND numero_rec="'.$numero_rec.'"','order: tipol');
		$tipo = '';
		if($recetap->tipo!=''){
			if(is_integer($recetap->tipo)){
				$menu = $this->Menus->findFirst($recetap->tipo);
				if($menu!=false){
					$tipo = $menu->nombre;
				}
			}
		}
		echo "<tr class='cabecera'>","<td width='50%'><b>No. Receta: </b>",$recetap->numero_rec,"</td>","<td><b>Número de Personas: </b>",$recetap->num_personas,"</td>","</tr>";
		echo "<tr class='cabecera'>","<td><b>Nombre: </b>", utf8_encode($recetap->nombre), "</td>","<td><b>Menú: </b>",$tipo,"</td>","</tr>";
		echo "<tr><td colspan='2'><table width='100%' cellspacing='0' class='detalle'>";
		echo "<tr>","<th>Tipo</th>","<th>Código</th>","<th>Nombre</th>","<th>Unidad</th>","<th>Divisor</th>","<th>Cant.</th>","<th>Valor</th>","</tr>";
		$unidades = array();
		foreach($recetal as $recetal){
			if($recetal->tipol=='R'){
				$tipo = 'RECETA';
			} else {
				$tipo = 'REFERENCIA';
			}
			if($recetal->tipol == 'I'){
				$refer = $this->Inve->findFirst('item="'.$recetal->item.'"');
			} else {
				$refer = $this->Recetap->findFirst('almacen=1 AND numero_rec="'.$recetal->item.'"');
			}
			$nombre = '';
			$nombreUnidad = '';
			if($refer != false){
				if($recetal->tipol=='I'){
					if(!isset($unidades[$refer->getUnidad()])){
						$unidad = $this->Unidad->findFirst('codigo="'.$refer->getUnidad().'"');
						$unidades[$refer->getUnidad()] = $unidad;
					} else {
						$unidad = $unidades[$refer->getUnidad()];
					}
					if($unidad!=false){
						$nombreUnidad = $unidad->nom_unidad;
					}
					$nombre = $refer->getDescripcion();
				} else {
					$nombre = $refer->nombre;
					$nombreUnidad = '&nbsp;';
				}
			}
			$costo = 0;
			if($recetal->divisor>0){
				$costo = CostoInventario::getCosto($recetal->item, $recetal->tipol);
				$costo = $costo*$recetal->cantidad/$recetal->divisor;
			}
			echo "<tr><td>", $tipo, "</td>",
				"<td align='right'>", $recetal->item, "</td>",
				"<td>&nbsp;", utf8_encode($nombre), "</td>",
				"<td>", $nombreUnidad, "</td>",
				"<td align='right'>", $recetal->divisor, "</td>",
				"<td align='right'>", $recetal->cantidad, "</td>",
				"<td align='right'>", Currency::number($costo), "</td></tr>";
		}
		$costoReceta = CostoInventario::getCosto($recetap->numero_rec, 'R');
		echo "<tr><td colspan='6' align='right'><b>Total</b></td><td align='right'>", Currency::number($costoReceta), "</td></tr>";
		echo "</table></td></tr>";
		echo "<tr><td colspan='2'>&nbsp;</td></tr>";
	}

	public function reporteAction(){
		Flash::notice('Haga click en "Generar" para listar todas las recetas ó utilice el campo número para un ver una específica');
	}

	public function reporteRecetasAction(){

		set_time_limit(0);

		$controllerRequest = ControllerRequest::getInstance();

		$numero_rec = $controllerRequest->getParamPost('numero_rec', 'int');
		$nombre = $controllerRequest->getParamPost('nombre');

		$conditions = array();
		if($numero_rec!='0'){
			$conditions[] = "numero_rec='".$numero_rec."'";
		}
		if($nombre!=''){
			$conditions[] = "nombre LIKE '%".$nombre."%'";
		}

		if(count($conditions)>0){
			$recetas = $this->Recetap->find(join(' AND ', $conditions), 'order: nombre');
		} else {
			$recetas = $this->Recetap->find('order: nombre');
		}
		foreach($recetas as $recetap){
			$this->reporteIndividual($recetap->numero_rec);
		}

		$this->setParamToView('datos', $this->Datos->findFirst());
	}

	public function analisisAction(){
		Flash::notice('Haga click en "Generar" para generar el análisis de costos de  todas las recetas ó utilice los campos para restringir los resultados');
		$salones = $this->Salon->find("estado='A'", 'order: id');
		$this->setParamToView('salones', $salones);
		$recetas = $this->Recetap->find("estado='A'", 'columns: numero_rec,nombre', 'order: numero_rec');
		$this->setParamToView('recetas', $recetas);

		Tag::displayTo('salonFinal', $this->Salon->maximum('id', 'conditions: estado="A"'));
		Tag::displayTo('recetaFinal', $this->Recetap->maximum('numero_rec', 'conditions: estado="A"'));
	}

	public function analisisCostosAction(){

		set_time_limit(0);

		$controllerRequest = ControllerRequest::getInstance();

		$codigoSalonInicial = $controllerRequest->getParamPost('salonInicial', 'alpha');
		$codigoSalonFinal = $controllerRequest->getParamPost('salonFinal', 'alpha');

		$salonInicial = $this->Salon->findFirst("id='$codigoSalonInicial'");
		$salonFinal = $this->Salon->findFirst("id='$codigoSalonFinal'");
		if($salonInicial==false){
			Flash::error("El salon '$codigoSalonInicial' NO existe.");
			return;
		}
		if($salonFinal==false){
			Flash::error("El salon '$codigoSalonFinal' NO existe.");
			return;
		}

		$codigoRecetaInicial = $controllerRequest->getParamPost('recetaInicial', 'alpha');
		$codigoRecetaFinal = $controllerRequest->getParamPost('recetaFinal', 'alpha');
		$recetaInicial = $this->Recetap->findFirst("numero_rec='$codigoRecetaInicial'");
		$recetaFinal = $this->Recetap->findFirst("numero_rec='$codigoRecetaFinal'");
		if($recetaInicial==false){
			Flash::error("La receta '$codigoRecetaInicial' NO existe.");
			return;
		}
		if($recetaFinal==false){
			Flash::error("La receta '$codigoRecetaFinal' NO existe.");
			return;
		}

		$recetas = $this->Recetap->find("estado='A' AND numero_rec BETWEEN '$codigoRecetaInicial' AND '$codigoRecetaFinal'");
		foreach($recetas as $receta){
			$results[$receta->numero_rec]['valores'] = $this->getValoresReceta($receta, $codigoSalonInicial, $codigoSalonFinal);
			$results[$receta->numero_rec]['nombre'] = $receta->nombre;
		}
		$this->setParamToView('results', $results);
		$this->setParamToView('datos', $this->Datos->findFirst());
		$this->setResponse('view');
	}

	public function getValoresReceta($receta, $codigoSalonInicial, $codigoSalonFinal){
		$salones = $this->Salon->find("estado='A' AND id BETWEEN '$codigoSalonInicial' AND '$codigoSalonFinal'");
		$datos = array();
		foreach($salones as $salon){
			$menusItem = $this->MenusItems->findFirst('tipo_costo = "R" AND codigo_referencia = "'.$receta->numero_rec.'"');
			if($menusItem != false) {
				$salonMenusItems = $this->SalonMenusItems->findFirst('menus_items_id = "'.$menusItem->id.'" AND salon_id = "'.$salon->id.'"');
				if($salonMenusItems != false){
					if($salonMenusItems->valor>0){
						$valor = $salonMenusItems->valor;
					} else {
						$valor = $menusItem->valor;
					}
				} else {
					$valor = $menusItem->valor;
				}
			} else {
				continue;
			}
			$datos[$salon->id]['nombre'] = $salon->nombre;
			$datos[$salon->id]['valor'] = $valor;
			$datos[$salon->id]['porc_costo'] = $receta->porc_costo;
			$datos[$salon->id]['costo'] = $valor * $receta->porc_costo / 100;
			$datos[$salon->id]['porc_utilidad'] = 100 - $receta->porc_costo;
			$datos[$salon->id]['utilidad'] = $valor - $datos[$salon->id]['costo'];
		}
		$datos[0]['valor'] = $receta->precio_venta;
		$datos[0]['porc_costo'] = $receta->porc_costo;
		$datos[0]['costo'] = $receta->precio_venta * $receta->porc_costo / 100;
		$datos[0]['porc_utilidad'] = 100 - $receta->porc_costo;
		$datos[0]['utilidad'] = $receta->precio_venta - $datos[0]['costo'];
		return $datos;
	}

	public function searchByItem($item,$tipol='I'){
		$config = CoreConfig::readFromActiveApplication("app.ini", 'ini');
        if(isset($config->pos->ramocol)){
                $schema = $config->pos->ramocol;
        } else {
                $schema = "ramocol";
        }

        $conditions = '(almacen, numero_rec) IN (SELECT almacen, numero_rec FROM '.$schema.'.recetal WHERE tipol="'.$tipol.'" AND item="'.$item.'")';

		$recetap = $this->Recetap->find($conditions, 'order: nombre');
		return $recetap;
	}

	public function findComponentAction(){
		$item = $this->getPostParam('item','alpha');
		$subreceta = $this->getPostParam('subreceta','alpha');
		if($subreceta==''){
			$inve = $this->Inve->findFirst("item='$item'");
			$recetas = $this->searchByItem($item, 'I');
		} else {
			$recetap = $this->Recetap->findFirst("almacen=1 AND numero_rec='$subreceta'");
			$recetas = $this->searchByItem($subreceta, 'R');
		}
		if(count($recetas)){
			if($subreceta==''){
				Flash::notice('Visualizando recetas que contienen la referencia: '.utf8_encode($inve->getDescripcion()));
			} else {
				Flash::notice('Visualizando recetas que contienen la sub-receta: '.utf8_encode($recetap->nombre));
			}
			$this->setParamToView('recetas', $recetas);
		} else {
			if($subreceta==''){
				Flash::notice('No se encontraron recetas que contentan la referencia: '.utf8_encode($inve->getDescripcion()));
			} else {
				Flash::notice('No se encontraron recetas que contentan la sub-receta: '.utf8_encode($recetap->nombre));
			}
			Tag::displayTo('item', '');
			Tag::displayTo('subreceta', '');
			return $this->routeToAction('index');
		}
		$this->renderJavascript('new Event.observe(window,"load",function(){new Receta.initializeFindComponent()})');
	}

	public function expandAction(){
		$this->setResponse('ajax');
		$components = $this->getPostParam('check');
		$result = array();
		foreach($components as $component){
			$recetap = $this->searchByItem($component,'R');
			$result[$component] = array();
			foreach($recetap as $receta){
				$result[$component][] = array(
					'almacen' => $receta->almacen,
					'numero_rec' => $receta->numero_rec,
					'nombre' => $receta->nombre,
					'num_personas' => $receta->num_personas,
					'precio_costo' => $receta->precio_costo
				);
			}
		}
		ob_clean();
		$this->renderText(json_encode($result));
	}

}
