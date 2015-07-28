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

class ImageHyperComponent extends UserComponent {

	/**
	 * Método que crea un campo autocomplete de referencias
	 *
	 * @param string $name
	 * @param string $component
	 * @param string $value
	 * @param string $context
	 * @return string
	 */
	public static function build($name, $component, $value=null, $context=null){
		$code = HfosTag::uploadImages($value);
		return $code;
	}
	
	/**
	 * Muestra la presentacion de ese tipo especial
	 *
	 * @param string $value
	 * @return string html
	 */
	public static function getDetail($value) {
		$val = explode('/',$value);
		$lastInt = count($val)-1;
		$value = $val[$lastInt];
		return Tag::image($value);
	}
	
	/**
	 * Metodo que coge la imagen a subir y la coloca en el folder de public/img/upload
	 * 
	 * @param  ActiveRecord $model
	 * @param  string $field 
	 * @param  array $file ($_FILES)
	 * @return array
	 */
	public static function moveImageToUploadFolder($model,$field,$files='') {
		$return = array();
		if (!$files) {
			return array(
				'status' => 'ERROR',
				'message' => 'Ingrese una url de la imagen a mover'
			);
		}else{			
			if (file_exists($files['path'])) {
				$bool = move_uploaded_file($url,Core::getInstancePath().'public/img/upload');
				if(!$bool){
					return array(
						'status' => 'ERROR',
						'message' => 'Algo ocurrio al tratar de mover el archivo subido'
					);
				} else {
					//Escribimos en el modelo por el nombre del campo
					
					$urlNew = 'img/upload/'.
					$model->writeAttribute($field,$url);
				}
			} else {
				return array(
					'status' => 'ERROR',
					'message' => 'la imagen no existe en el temporal'
				);
			}
		}
		return array(
			'status' => 'success',
			'message' => 'Se movio la imagen a el servidor'
		);
	}

	public static function info(){
		return 'El documento de un item válido';
	}

}
