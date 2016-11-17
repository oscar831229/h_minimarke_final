
<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package     Back-Office
 * @copyright     BH-TECK Inc. 2009-2010
 * @version        $Id$
 */

Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');

/**
 * SociosController
 *
 * Controlador de las Socios de Club
 *
 */
class SociosController extends HyperFormController
{

    static protected $_config = array(
        'model'    => 'Socios',
        'plural'   => 'Socios del club',
        'single'   => 'Socio del club',
        'genre'    => 'M',
        'tabName' => 'Socios',
        'preferedOrder' => 'apellidos ASC',
        'icon' => 'user.png',
        /*'ignoreButtons' => array(
            'import'
        ),*/
        'fields' => array(
            'socios_id' => array(
                'single' => 'Código',
                'type' => 'int',
                'size' => 6,
                'maxlength' => 6,
                'primary' => true,
                'auto' => true,
                'readOnly' => true,
                'filters' => array('int')
            ),
            'titular_id' => array(
                'single' => 'Socio Titular',
                'type' => 'Socio',
                'maxlength' => 20,
                'notBrowse' => true,
                'filters' => array('alpha')
            ),
            'numero_accion' => array(
                'single'    => 'Número de Acción',
                'type'        => 'text',
                'size'        => 10,
                'maxlength'    => 20,
                'notNull'    => true,
                'readOnly'    => true,
                'filters'    => array('striptags')
            ),
            'tipo_documentos_id' => array(
                'single' => 'Tipo de Documento',
                'type' => 'relation',
                'relation' => 'tipoDocumentos',
                'fieldRelation' => 'id',
                'detail' => 'nombre',
                'maxlength' => 20,
                'notNull' => true,
                'notSearch' => true,
                'notBrowse' => true,
                'filters' => array('int')
            ),
            'identificacion' => array(
                'single' => 'Identificación',
                'type' => 'int',
                'size' => 20,
                'maxlength' => 20,
                'notNull' => true,
                'filters' => array('alpha')
            ),
            'fecha_ingreso' => array(
                'single' => 'Fecha de Ingreso',
                'type' => 'date',
                'default' => '',
                'notNull' => true,
                'notSearch' => true,
                'filters' => array('date')
            ),
            /*'fecha_inscripcion' => array(
                'single' => 'Fecha de Inscripcion',
                'type' => 'date',
                'default' => '',
                'filters' => array('date')
            ),
            'tiempo' => array(
                'single' => 'Tiempo',
                'type' => 'int',
                'size' => 10,
                'maxlength' => 10,
                'notSearch' => true,
                'filters' => array('int')
            ),*/
            'parentescos_id' => array(
                'single' => 'Parentescos',
                'type' => 'relation',
                'relation' => 'parentescos',
                'fieldRelation' => 'id',
                'detail' => 'nombre',
                'maxlength' => 20,
                'notNull' => true,
                //'notSearch' => true,
                'notBrowse' => true,
                'filters' => array('int')
            ),
            'nombres' => array(
                'single' => 'Nombres',
                'type' => 'text',
                'size' => 30,
                'maxlength' => 60,
                'notNull' => true,
                'filters' => array('striptags', 'extraspaces')
            ),
            'apellidos' => array(
                'single' => 'Apellidos',
                'type' => 'text',
                'size' => 30,
                'maxlength' => 60,
                'notNull' => true,
                'filters' => array('striptags', 'extraspaces')
            ),
            'ciudad_expedido' => array(
                'single' => 'Ciudad Expedido',
                'type' => 'ciudad',
                'notBrowse' => true,
                'notSearch' => true,
                'notReport' => true,
                'notNull' => true,
                'filters' => array('int')
            ),
            'ciudad_nacimiento' => array(
                'single' => 'Ciudad Nacimiento',
                'type' => 'ciudad',
                'notBrowse' => true,
                'notSearch' => true,
                'notReport' => true,
                'filters' => array('int')
            ),
            'fecha_nacimiento' => array(
                'single' => 'Fecha de Nacimiento',
                'type' => 'date',
                'default' => '',
                'notNull' => true,
                'notSearch' => true,
                'notBrowse' => true,
                'filters' => array('date')
            ),
            'estados_civiles_id' => array(
                'single' => 'Estado Civil',
                'type' => 'relation',
                'relation' => 'estadosCiviles',
                'fieldRelation' => 'id',
                'notSearch' => true,
                'notBrowse' => true,
                'detail' => 'nombre',
                'filters' => array('int')
            ),
            'sexo' => array(
                'single' => 'Genero',
                'type' => 'closed-domain',
                'size' => 1,
                'maxlength' => 1,
                'values' => array(
                    'M' => 'Masculino',
                    'F' => 'Femenino'
                ),
                'notBrowse' => true,
                'notReport' => true,
                'notSearch' => true,
                'notNull' => true,
                'filters' => array('onechar')
            ),
            'direccion_casa' => array(
                'single'    => 'Dirrección de Casa',
                'type'        => 'text',
                'size'        => 30,
                'maxlength'    => 60,
                'notNull'    => true,
                'notSearch'    => true,
                'notBrowse'    => true,
                'filters'    => array('striptags', 'extraspaces')
            ),
            'ciudad_casa' => array(
                'single'    => 'Ciudad de casa',
                'type'        => 'ciudad',
                'notBrowse'    => true,
                'notSearch'    => true,
                'notReport'    => true,
                'notNull'    => true,
                'filters'    => array('int')
            ),
            'telefono_casa' => array(
                'single'    => 'Teléfono de Casa',
                'type'        => 'int',
                'size'        => 20,
                'maxlength'    => 60,
                'notSearch'    => true,
                'notBrowse'    => true,
                'notNull'    => true,
                'filters'    => array('int')
            ),
            'celular' => array(
                'single'    => 'Celular',
                'type'        => 'text',
                'size'        => 20,
                'maxlength'    => 60,
                'notSearch'    => true,
                'notBrowse'    => true,
                'notNull'    => true,
                'filters'    => array('striptags')
            ),
            'direccion_trabajo' => array(
                'single' => 'Dirrección de Trabajo',
                'type' => 'text',
                'size' => 40,
                'notSearch' => true,
                'notBrowse' => true,
                'maxlength' => 80,
                'filters' => array('striptags', 'extraspaces')
            ),
            'ciudad_trabajo' => array(
                'single' => 'Ciudad de trabajo',
                'type' => 'ciudad',
                'notBrowse' => true,
                'notSearch' => true,
                'notReport' => true,
                'notNull' => true,
                'filters' => array('int')
            ),
            'telefono_trabajo' => array(
                'single' => 'Teléfono de Trabajo',
                'type' => 'int',
                'size' => 30,
                'notSearch' => true,
                'notBrowse' => true,
                'maxlength' => 60,
                'filters' => array('int')
            ),
            'fax' => array(
                'single' => 'Fax',
                'type' => 'int',
                'size' => 30,
                'maxlength' => 60,
                'notSearch' => true,
                'notBrowse' => true,
                'filters' => array('int')
            ),
            'celular_trabajo' => array(
                'single' => 'Celular de trabajo',
                'type' => 'text',
                'size' => 40,
                'maxlength' => 80,
                'notSearch' => true,
                'notBrowse' => true,
                'notNull' => true,
                'filters' => array('striptags')
            ),
            'correo_2' => array(
                'single' => 'E-Mail de trabajo',
                'type' => 'text',
                'size' => 40,
                'maxlength' => 70,
                'notBrowse' => true,
                'notSearch' => true,
                'filters' => array('striptags', 'extraspaces')
            ),
            'direccion_correspondencia' => array(
                'single'    => 'Envio de Correspondencia a',
                'type'        => 'closed-domain',
                'size'        => 1,
                'notNull'    => true,
                'maxlength'    => 1,
                'values'    => array(
                    'C'    => 'Casa',
                    'T'    => 'Trabajo'
                ),
                'notBrowse'    => true,
                'notReport'    => true,
                'notSearch'    => true,
                'filters'    => array('onechar')
            ),
            'correo_1' => array(
                'single' => 'E-Mail principal',
                'type' => 'text',
                'size' => 40,
                'maxlength' => 70,
                'notBrowse' => true,
                'notSearch' => true,
                'notNull' => true,
                'filters' => array('striptags', 'extraspaces')
            ),
            'correo_3' => array(
                'single' => 'E-Mail Opcional',
                'type' => 'text',
                'size' => 40,
                'maxlength' => 70,
                'notBrowse' => true,
                'notSearch' => true,
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
            'porc_mora_desfecha' => array(
                'single' => '% Mora Estado cuenta',
                'type' => 'int',
                'size' => 3,
                'notSearch' => true,
                'notBrowse' => true,
                'maxlength' => 3,
                'filters' => array('int')
            ),
            'numero_tarjeta' => array(
                'single' => 'Numero de Tarjeta',
                'type' => 'text',
                'size' => 40,
                'maxlength' => 70,
                'notBrowse' => true,
                //'notSearch' => true,
                'filters' => array('striptags', 'extraspaces')
            ),
            'imprime' => array(
                'single' => 'Imprime Factura',
                'type' => 'closed-domain',
                'size' => 1,
                'maxlength' => 1,
                'notNull' => true,
                'values' => array(
                    'S' => 'Si',
                    'N' => 'No'
                ),
                'notBrowse' => true,
                'notReport' => true,
                'filters' => array('onechar')
            ),
            'envia_correo' => array(
                'single' => 'Enviar Correo',
                'type' => 'closed-domain',
                'size' => 1,
                'maxlength' => 1,
                'notNull' => true,
                'values' => array(
                    'S' => 'Si',
                    'N' => 'No'
                ),
                'notBrowse' => true,
                'notReport' => true,
                //'notSearch' => true,
                'filters' => array('onechar')
            ),
            'estado_front' => array(
                'single' => 'Estado en Front',
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
            'genera_estcue' => array(
                'single' => 'Genera Estado de Cuenta?',
                'type' => 'closed-domain',
                'size' => 1,
                'notNull' => true,
                'maxlength' => 1,
                'values' => array(
                    'S' => 'Si',
                    'N' => 'No'
                ),
                'filters' => array('onechar')
            ),
            'cobra' => array(
                'single' => 'Genera Cobro',
                'type' => 'closed-domain',
                'size' => 1,
                'notNull' => true,
                'maxlength' => 1,
                'values' => array(
                    'S' => 'Si',
                    'N' => 'No'
                ),
                'filters' => array('onechar')
            ),
            'consumo_minimo' => array(
                'single' => 'Aplica consumo minimo?',
                'type' => 'closed-domain',
                'size' => 1,
                'notNull' => true,
                'maxlength' => 1,
                'values' => array(
                    'S' => 'Si',
                    'N' => 'No'
                ),
                'notBrowse' => true,
                //'notReport' => true,
                //'notSearch' => true,
                'filters' => array('onechar')
            ),
            'genera_mora' => array(
                'single' => 'Aplica mora?',
                'type' => 'closed-domain',
                'size' => 1,
                'notNull' => true,
                'maxlength' => 1,
                'values' => array(
                    'S' => 'Si',
                    'N' => 'No'
                ),
                'notBrowse' => true,
                //'notReport' => true,
                //'notSearch' => true,
                'filters' => array('onechar')
            ),
            'ajuste_sostenimiento' => array(
                'single' => 'Aplica ajuste de sostenimiento?',
                'type' => 'closed-domain',
                'size' => 1,
                'notNull' => true,
                'maxlength' => 1,
                'values' => array(
                    'S' => 'Si',
                    'N' => 'No'
                ),
                'notBrowse' => true,
                //'notReport' => true,
                //'notSearch' => true,
                'filters' => array('onechar')
            ),
            'estados_socios_id' => array(
                'single' => 'Estado de Socio',
                'type' => 'relation',
                'relation' => 'EstadosSocios',
                'fieldRelation' => 'id',
                'detail' => 'nombre',
                'notNull' => true,
                'readOnly' => true,
                'filters' => array('int')
            ),
            'nombre_padre' => array(
                'single'    => 'Nombre de Padre',
                'type'        => 'text',
                'size'        => 40,
                'notSearch'    => true,
                'notBrowse'    => true,
                'maxlength'    => 80,
                'filters'    => array('striptags', 'extraspaces')
            ),
            'nombre_madre' => array(
                'single'    => 'Nombre de Madre',
                'type'        => 'text',
                'size'        => 40,
                'notSearch'    => true,
                'notBrowse'    => true,
                'maxlength'    => 80,
                'filters'    => array('striptags', 'extraspaces')
            ),
            'fecha_retiro' => array(
                'single'    => 'Fecha de Retiro',
                'type'        => 'date',
                'default'    => '',
                'notSearch'    => true,
                'notBrowse'    => true,
                'filters'    => array('date')
            ),
            'imagen_socio' => array(
                'single'    => 'Imagen de Socio',
                'type'        => 'image',
                'default'    => '',
                'notBrowse'    => true,
                'notSearch'    => true,
                'filters'    => array('striptags')
            )
        ),
        'extras' => array(
            0 => array(
                'partial' => 'estudios',
                'tabName' => 'Estudios'
            ),
            1 => array(
                'partial' => 'explaboral',
                'tabName' => 'Experiencia Laboral'
            ),
            2 => array(
                'partial' => 'actividades',
                'tabName' => 'Actividades Libres'
            ),
            3 => array(
                'partial' => 'asoclubes',
                'tabName' => 'Clubes'
            ),
            4 => array(
                'partial' => 'cargos_periodicos',
                'tabName' => 'Cargos Fijos Periodicos'
            ),
            5 => array(
                'partial' => 'presentado',
                'tabName' => 'Otros Socios'
            ),
            6 => array(
                'partial' => 'correspondencia',
                'tabName' => 'Correspondencia'
            )
        )
    );
    
    /**
     * Carga info para partials
     *
     */
    public function beforeNew()
    {
        //Cargos Fijos Periodicos
        $cargosFijosPeriodicosArray = $this->CargosFijos->find(array('conditions'=>'tipo_cargo="P"','order'=>'nombre'));
        $this->setParamToView('cargosFijosPeriodicosArray', $cargosFijosPeriodicosArray);
        
        //Actividades Libres
        $hobbiesArray = $this->Hobbies->find(array('order'=>'nombre'));
        $this->setParamToView('hobbiesArray', $hobbiesArray);
        
        //Clubes
        $clubesArray = $this->Clubes->find(array('order'=>'nombre'));
        $this->setParamToView('clubesArray', $clubesArray);
        
        $this->setParamToView('state', 'new');
        Tag::displayTo('estado', 'A');
        return true;
    }
    
    /**
     * Carga los demas datos del socios
     *
     */
    public function beforeEdit()
    {
        $sociosId = $this->getPostParam('socios_id', 'int');
        if ($sociosId) {
            //Estudios
            $estudiosObj = EntityManager::get('Estudios')->find(array('conditions'=>'socios_id='.$sociosId));
            $this->setParamToView('estudiosObj', $estudiosObj);
            
            //Experiencia laborales
            $expLaboralObj = EntityManager::get('Explaboral')->find(array('conditions'=>'socios_id='.$sociosId));
            $this->setParamToView('expLaboralObj', $expLaboralObj);
            
            //Actividades Libres
            $hobbiesArray = $this->Hobbies->find(array('order'=>'nombre'));
            $this->setParamToView('hobbiesArray', $hobbiesArray);
            $actividadesArray = $this->Actividades->find('socios_id='.$sociosId);
            $actividadesArray2 = array();
            foreach ($actividadesArray as $actividad) {
                $actividadesArray2[$actividad->getHobbiesId()]=true;
            }
            $this->setParamToView('actividadesArray', $actividadesArray2);
            
            //Clubes
            $clubesArray = $this->Clubes->find(array('order'=>'nombre'));
            $this->setParamToView('clubesArray', $clubesArray);
            $asoclubesArray = $this->Asoclubes->find('socios_id='.$sociosId);
            $asoclubesArray2 = array();
            foreach ($asoclubesArray as $asoclub) {
                $asoclubesArray2[$asoclub->getClub()]=$asoclub;
            }
            $this->setParamToView('asoclubesArray', $asoclubesArray2);
            
            //Cargos Fijos Periodicos
            $cargosFijosPeriodicosArray = $this->CargosFijos->find(array('conditions'=>'tipo_cargo="P"','order'=>'nombre'));
            $this->setParamToView('cargosFijosPeriodicosArray', $cargosFijosPeriodicosArray);
            $asignacionCargosFijoasArray = $this->AsignacionCargos->find('socios_id='.$sociosId);
            $asignacionCargosFijoasArray2 = array();
            foreach ($asignacionCargosFijoasArray as $asignacionCargoFijo) {
                $asignacionCargosFijoasArray2[$asignacionCargoFijo->getCargosFijosId()]=true;
            }
            $this->setParamToView('asignacionCargosFijosArray', $asignacionCargosFijoasArray2);
            
            //Otros Socios
            $asociacionSocioObj = EntityManager::get('AsociacionSocio')->find(array('conditions'=>'socios_id='.$sociosId));
            $this->setParamToView('asociacionSocioObj', $asociacionSocioObj);
            
            //estado Edit
            $this->setParamToView('state', 'edit');
            
            //Correspondencia
            $correspondenciaSociosObj = $this->CorrespondenciaSocios->find("socios_id='$sociosId'");
            $this->setParamToView('correspondenciaSociosObj', $correspondenciaSociosObj);
        } else {
            return false;
        }
        return true;
    }
    
    /**
     * Metodo privado que crea/actualiza estudios de un socio
     *
     * @param Transaction $transsaction
     * @param ActiveRecord $record
     * @return Boolean
     */
    private function _saveEstudios($record, $transaction)
    {
        $numero = 0;
        
        //Valiables en array de post
        $ids            = $this->getPostParam('estudiosId');
        $instituciones    = $this->getPostParam('estudiosInstitucion');
        $ciudades        = $this->getPostParam('estudiosCiudadId');
        $fechaGrados    = $this->getPostParam('estudiosFechaGrado');
        $titulos        = $this->getPostParam('estudiosTitulo');
        
        //Grabamos estudios
        $configEstudios = array(
            'SociosId'        => $record->getSociosId(),
            'estudiosId'    => $ids,
            'instituciones'    => $instituciones,
            'ciudades'        => $ciudades,
            'fechaGrados'    => $fechaGrados,
            'titulos'        => $titulos
        );
        
        SociosCore::saveEstudios($configEstudios, $transaction);
        return true;
    }
    
    /**
     * Metodo privado que crea/actualiza experiencia laboral de un socio
     *
     * @param Transaction $transsaction
     * @param ActiveRecord $record
     * @return Boolean
     */
    private function _saveExplaboral($transaction, $record)
    {
        //Valiables en array de post
        $expLaboralesId            = $this->getPostParam('expLaboralesId');
        $expLaboralesEmpresa    = $this->getPostParam('expLaboralesEmpresa');
        $expLaboralesDireccion    = $this->getPostParam('expLaboralesDireccion');
        $expLaboralesCargo        = $this->getPostParam('expLaboralesCargo');
        $expLaboralesTelefono    = $this->getPostParam('expLaboralesTelefono');
        $expLaboralesFax        = $this->getPostParam('expLaboralesFax');
        $expLaboralesFecha        = $this->getPostParam('expLaboralesFecha');
        //Grabamos estudios
        $configExpLaboral = array(
            'SociosId'                => $record->getSociosId(),
            'expLaboralesId'        => $expLaboralesId,
            'expLaboralesEmpresa'    => $expLaboralesEmpresa,
            'expLaboralesDireccion'    => $expLaboralesDireccion,
            'expLaboralesCargo'        => $expLaboralesCargo,
            'expLaboralesTelefono'    => $expLaboralesTelefono,
            'expLaboralesFax'        => $expLaboralesFax,
            'expLaboralesFecha'        => $expLaboralesFecha
        );
        SociosCore::saveExpLaboral($configExpLaboral, $transaction);
        return true;
    }
    
    /**
     * Metodo privado que crea/actualiza Actividades libres de un socio
     *
     * @param Transaction $transsaction
     * @param ActiveRecord $record
     * @return Boolean
     */
    private function _saveActividades($transaction, $record)
    {
        //Valiables en array de post
        $actividadesArray = $this->getPostParam('actividades');
        $configActividades = array(
            'SociosId'                => $record->getSociosId(),
            'actividades' => $actividadesArray
        );
        SociosCore::saveActividades($configActividades, $transaction);
        return true;
    }
    
    /**
     * Metodo privado que crea/actualiza clubes de un socio
     *
     * @param Transaction $transsaction
     * @param ActiveRecord $record
     * @return Boolean
     */
    private function _saveClubes($transaction, $record)
    {
        //Valiables en array de post
        $clubesArray = $this->getPostParam('clubId');
        $clubesDesdeArray = $this->getPostParam('clubDesde');
        $configClubes = array(
            'SociosId'        => $record->getSociosId(),
            'clubes'        => $clubesArray,
            'clubesDesde'    => $clubesDesdeArray
        );
        SociosCore::saveClubes($configClubes, $transaction);
        return true;
    }
    
    /**
     * Metodo que crea/edita cargos fijos periodicos del socio
     *
     * @param Transacction $transaction
     * @param ActiveRecord $record
     * @return boolean
     */
    private function _saveCargosFijosPeriodicos($transaction, $record)
    {
        //Valiables en array de post
        $cargosFijosArray = $this->getPostParam('cargosFijosId');
        $configCargosFijos = array(
            'SociosId'        => $record->getSociosId(),
            'cargosFijos'    => $cargosFijosArray
        );
        SociosCore::saveCargosFijos($configCargosFijos, $transaction);
        return true;
    }
    
    /**
     * Crea/actualiza un tercero con base a socios en ramocol
     *
     * @param Transaction $transaction
     * @param Activerecord $record
     * @return boolean
     */
    private function _saveTerceros($transaction, $record)
    {
        SociosCore::saveTerceros($record, $transaction);
        return true;
    }

    /**
     * Crea/actualiza el nit de las facturas si se cambia el nit
     *
     * @param Transaction $transaction
     * @param Activerecord $record
     * @return boolean
     */
    private function _saveNitInvoicer($transaction, $record)
    {
        SociosCore::saveNitInvoicer($record, $transaction);
        return true;
    }
    
    /**
     * Metodo que crea/edita los socios asociados al socio nuevo
     */
    private function _saveOtrosSocios($transaction, $record)
    {
        $configOtrosSocios = array(
            'SociosId'                => $record->getSociosId(),
            'asignacionSocioId'        => $this->getPostParam('asignacionSocioId'),
            'otrosSociosId'            => $this->getPostParam('otrosSociosId'),
            'tipoAsociacionSocioId'    => $this->getPostParam('tipoAsociacionSocioId')
        );

        SociosCore::saveOtrosSocios($configOtrosSocios, $transaction);
        return true;
    }
    
    /**
     * Metodo que actualiza/crea los datos extra de un socio
     *
     * @param transaction $transaction
     * @param Activerecord $record
     * @return boolean
     */
    private function _actualizarSocio($transaction, $record)
    {
        if ($record->getSociosId()>0) {
            //Guardamos los datos de estudios del socio
            $this->_saveEstudios($record, $transaction);
            //Guardamos los datos de experiencia laboral del socio
            $this->_saveExplaboral($transaction, $record);
            //Guardamos los datos de actividades Libres del socio
            $this->_saveActividades($transaction, $record);
            //Guardamos los datos de clubes del socio
            $this->_saveClubes($transaction, $record);
            //Guardamos los datos de cargos fijos periodicos del socio
            $this->_saveCargosFijosPeriodicos($transaction, $record);
            //crea/actualiza terceros en ramocol
            $this->_saveTerceros($transaction, $record);
            //crea/actualiza presentado por
            $this->_saveOtrosSocios($transaction, $record);
            //crea/actualiza facturas
            $this->_saveNitInvoicer($transaction, $record);
        }
        return true;
    }
    
    /**
     * Metodo que se ejecuta cuando ya se creo el registro de socios
     *
     * @param transacion $transaction
     * @param ActiveRecord $record
     * @return boolean
     */
    public function afterInsert($transaction, $record)
    {
        return $this->_actualizarSocio($transaction, $record);
    }
    
    /**
     * Metodo que se ejecuta cuando ya se actualizo el registro de socios
     *
     * @param transacion $transaction
     * @param ActiveRecord $record
     * @return boolean
     */
    public function afterUpdate($transaction, $record)
    {
        return $this->_actualizarSocio($transaction, $record);
    }
    
    private function _beforeSave($transaction, $record)
    {
        //Generamos el numero de accion
        $numeroAccion = SociosCore::makeNumeroAccion($record, $transaction);
        $record->setNumeroAccion($numeroAccion);
        //upload image
        $imgUploaded = Session::getData('imgUploaded');
        if ($imgUploaded) {
            $record->setImagenSocio($imgUploaded);
            Session::setData('imgUploaded', '');
        }
        return true;
    }
    
    public function beforeInsert($transaction, $record)
    {
        $record->setEstadosSociosId(1);//Activo
        return $this->_beforeSave($transaction, $record);
    }
    
    public function beforeUpdate($transaction, $record)
    {
        return $this->_beforeSave($transaction, $record);
    }

    public function initialize()
    {
        $territory = new Territory();
        $territories = $territory->find(array('order'=>'name ASC'));
        $paises = array();
        foreach ($territories as $pais) {
            $paises[$pais->getId()]=$pais->getName();
        }
        //Miramos si es numero de accion manual o no
        $configuration = EntityManager::get('Configuration')->findFirst(array('conditions'=>'application="SO" AND name="numero_accion_manual"'));
        if ($configuration==false) {
            $this->appendMessage(new ActiveRecordMessage('No existe configuración de número de acción manual'));
            unset(self::$_config['fields']['numero_accion']['readOnly']);
        } else {
            //'S' es que si es manual por tanto debe ser un campo editable ene l formulario el número de acción
            if ($configuration->getValue()=='S') {
                if (isset(self::$_config['fields']['numero_accion']['readOnly'])==true) {
                    unset(self::$_config['fields']['numero_accion']['readOnly']);
                }
            }
        }
        parent::setConfig(self::$_config);
        parent::initialize();
    }
    
    
    /**
     * Metodo que se usa en la consulta de auto complete 
     * en las demas partes para consultar un numero de accion
     * @return string
     */
    public function getDetalleSocioAction()
    {
        Core::importFromLibrary('Kumbia/ActionHelpers/Scriptaculous/', 'Scriptaculous.php');
        
        $this->setResponse('ajax');
        $numeroAccion = $this->getPostParam('numeroAccion');
        if ($numeroAccion) {
            //Se indica que la respuesta es AJAX
            $this->setResponse('ajax');
     
            //Campos del modelo utilizados para crear el resultado
            $fields = array('id', 'numero_accion');
            //buscamos los registros
            $sociosAll = EntityManager::get('Socios')->find('numero_accion LIKE "'.$numeroAccion.'%" OR nombres LIKE "'.$numeroAccion.'%" OR apellidos LIKE "'.$numeroAccion.'%"');
            $sociosArray = array();
            foreach ($sociosAll as $socio) {
                $sociosArray[$socio->getSociosId()] = $socio->getNumeroAccion()." -> ".$socio->getNombres()." ".$socio->getApellidos();
            }
            //Obtener los paises requeridos
            $sociosBusqueda = Scriptaculous::filter($numeroAccion, $sociosArray);
            //Se genera el HTML a devolver al usuario
            $htmlCode = Scriptaculous::autocomplete($sociosBusqueda);
            $this->renderText($htmlCode);
        } else {
            var_dump($_REQUEST);
        }
    }
    
    
    
    /**
     * Action para autocomplete de centros por codigo
     *
     * @return json
     */
    public function querySocioAction()
    {
        $this->setResponse('json');
        $codigoSocio = $this->getQueryParam('socio', 'alpha');
        $socios = EntityManager::get('Socios');
        if (!$codigoSocio) {
            return array(
                'existe' => 'N'
            );
        }
        $socio = $socios->findFirst("socios_id='$codigoSocio'");
        if ($socio!=false) {
            return array(
                'status' => 'OK',
                'existe' => 'S',
                'codigo' => $socio->getSociosId(),
                'nombre' => $socio->getNumeroAccion()." - ".$socio->getNombres()." ".$socio->getApellidos()
            );
        } else {
            return array(
                'existe' => 'N'
            );
        }
    }
    
    public function queryBySociosAction()
    {
        $this->setResponse('json');
        $codigoSocio = $this->getQueryParam('socio', 'alpha');
        $socios = EntityManager::get('Socios');
        $socio = $socios->findFirst("socios_id='$codigoSocio'");
        if ($socio!=false) {
            return array(
                'status' => 'OK',
                'existe' => 'S',
                'codigo' => $socio->getSociosId(),
                'nombre' => $socio->getNumeroAccion()." - ".$socio->getNombres()." ".$socio->getApellidos()
            );
        } else {
            return array(
                'existe' => 'N'
            );
        }
    }


    public function queryByNameAction()
    {
        $this->setResponse('json');
        $response = array();
        $nombre = $this->getPostParam('nombre', 'extraspaces');
        if ($nombre!='') {
            $socios = EntityManager::get('Socios');
            $sociosArray = $socios->find('numero_accion LIKE \''.$nombre.'%\' OR nombres LIKE \''.$nombre.'%\' OR apellidos LIKE \''.$nombre.'%\'', 'order: numero_accion', 'limit: 13');
            foreach ($sociosArray as $socio) {
                $response[] = array(
                    'status' => 'OK',
                    'value' => $socio->getSociosId(),
                    'selectText' => $socio->getNumeroAccion()." - ".$socio->getNombres()." ".$socio->getApellidos(),
                    'optionText' => $socio->getNumeroAccion()." - ".$socio->getNombres()." ".$socio->getApellidos()
                );
            }
        }
        return $response;
    }
    
    /**
     * Metodo que obtiene la información del socio por ID
     */
    public function getInfoSociosAction()
    {
        $this->setResponse('json');
        $sociosId = $this->getPostParam('sociosId', 'int');
        $socio = EntityManager::get('Socios')->findFirst(array('conditions'=>"socios_id=$sociosId"));
        if ($socio!=false) {
            $infoSocio = SociosCore::makeAllSocioInfo($socio);
            return array(
                'status'    => 'OK',
                'info'      => $infoSocio
            );
        } else {
            return array(
                'status'    => 'FAILED',
                'message'    => 'El socio no existe'
            );
        }
    }
    
    public function uploadAction()
    {
        $this->setResponse('view');
        if (isset($_FILES['image'])) {
            $file = $_FILES['image'];
            move_uploaded_file($file['tmp_name'], 'public/img/upload/'.$file['name']);
            $this->setPostParam('imgUploaded', $file['name']);
            Session::setData('imgUploaded', 'upload/'.$file['name']);
        }
        Router::routeTo(array("controller" => Router::getController(),"action"=>'uploadField'));
    }

    //Proyeccion
    /**
     * Metodo que se ejecuta apra calcular los datos de memebresia de un contrato
     */
    public function getCuotaInicialAction()
    {
        $this->setResponse('json');
        $valorTotal = $this->getPostParam('valorTotal', 'double');
        $cuotaInicial = $this->getPostParam('cuotaInicial', 'double');
        if ($valorTotal) {
            if (!$cuotaInicial) {
                $cuotaInicial = $valorTotal * 0.0;
            }
            $saldoPagar = $valorTotal - $cuotaInicial;

            if ($saldoPagar<0) {
                return array(
                    'status' => 'FAILED',
                    'message' => 'El valor total no puede ser negativo'
                );
            }
            return array(
                'status' => 'OK',
                'cuotaInicial' => $cuotaInicial,
                'saldoPagar' => $saldoPagar,
            );
        } else {
            return array(
                'status' => 'FAILED',
                'message' => 'Debe proporcionar un valor a valorTotal'
            );
        }
    }

    public function correspondenciaAction()
    {

    }
}
