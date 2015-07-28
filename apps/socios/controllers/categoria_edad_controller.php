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

/**
 * Categoria_EdadController
 *
 * Controlador de Categorias por edades
 *
 */
class Categoria_EdadController extends HyperFormController {

    static protected $_config = array(
        'model' => 'CategoriaEdad',
        'plural' => 'Categoria X Edades',
        'single' => 'Categoria X Edad',
        'genre' => 'M',
        'tabName' => 'CategoriaEdad',
        'preferedOrder' => 'nombre ASC',
        'icon' => 'attibutes.png',
        /*'ignoreButtons' => array(
            'import'
        ),*/
        'fields' => array(
            'id' => array(
                'single' => 'Código',
                'type' => 'text',
                'size' => 6,
                'maxlength' => 6,
                'primary' => true,
                'readOnly' => true,
                'filters' => array('int')
            ),
            'nombre' => array(
                'single' => 'Nombre',
                'type' => 'text',
                'size' => 30,
                'maxlength' => 45,
                'filters' => array('striptags', 'extraspaces')
            ),
            'tipo_socios_id' => array(
                'single' => 'Tipo Socio',
                'type' => 'relation',
                'relation' => 'tipoSocios',
                'fieldRelation' => 'id',
                'detail' => 'nombre',
                'notNull' => true,
                'filters' => array('int')
            ),
            'estados_socios_id' => array(
                'single' => 'Estado de Socio',
                'type' => 'relation',
                'relation' => 'EstadosSocios',
                'fieldRelation' => 'id',
                'detail' => 'nombre',
                'notNull' => true,
                'filters' => array('int')
            ),
            'edad_ini' => array(
                'single' => 'Edad Inicial',
                'type' => 'int',
                'size' => 11,
                'maxlength' => 11,
                'filters' => array('int')
            ),
            'edad_fin' => array(
                'single' => 'Edad Limite',
                'type' => 'int',
                'size' => 11,
                'maxlength' => 11,
                'filters' => array('int')
            ),
            'estado' => array(
                'single' => 'Estado',
                'type' => 'closed-domain',
                'size' => 1,
                'notNull' => true,
                'maxlength' => 1,
                'values' => array(
                    'A' => 'Activo',
                    'I' => 'Inactivo'
                ),
                'filters' => array('onechar')
            ),
        )
    );

    public function initialize(){
        parent::setConfig(self::$_config);
        parent::initialize();
    }
}