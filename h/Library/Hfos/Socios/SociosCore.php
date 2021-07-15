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
 * @author         BH-TECK Inc. 2009-2010
 * @version        $Id$
 */

require_once 'SociosException.php';
Core::importFromLibrary('Hfos/Socios', 'SociosFactura.php');
Core::importFromLibrary('Hfos/Socios', 'SociosReports.php');
//Core::importFromLibrary('Hfos/Socios','SociosTest.php');

/**
 * SociosCore
 *
 * Clase central que controla procesos internos de Socios
 *
 */
class SociosCore extends UserComponent
{

    /**
     * Detalle del tipo de Cargos fijos que existen
     * @var array
     */
    public static $_tiposCargosFijos = array(
        'P' => 'Periodico',
        'T' => 'Temporal'
    );

    /**
     * Detalle del clase de Cargos fijos que existen
     * @var array
     */
    public static $_claseCargosFijos = array(
        'S' => 'Sostenimiento',
        'A' => 'Administrativa'
    );

    /**
     * Detalle del movimiento automatico de facturacion que existen
     * @var array
     */
    public static $_tipoMovimientoAutomatico = array(
        'N' => 'Cuota Minima',
        'O' => 'Novedades',
        'T' => 'Ajuste Sostenimiento',
        'M' => 'Intereses de Mora',
        'C' => 'Cargo Fijo Asignado'
    );


    /**
     * Metod que según el 'S'/'N' de Configuración se genera consecutivo automatico o valor de nue,ro de accion
     */
    public static function setNumeroAccionManual(&$config, $transaction)
    {
        if (isset($config['numeroAccionManual'])==true) {
            $configuration = self::getModel('Configuration')->setTransaction($transaction)->findFirst(array('conditions'=>'application="SO" AND name="numero_accion_manual"'));
            switch($config['numeroAccionManual']) {
                case 'S'://Es manual
                    $configuration->setValue('S');
                    break;
                case 'N'://Es automatico
                    $configuration->setValue('N');
                    break;
                default://Error
                    $transaction->rollback('El valor de número de acción solo puede ser S/N');
                    break;
            }
            if ($configuration->save()==false) {
                foreach ($configuration->getMessages() as $message) {
                    $transaction->rollback($message->getMessage());
                }
            }
        }
    }

    /**
     * Metodo que crea un contrato de prueba
     * 
     * @param array $config
     * @param ActiverecordTransaction $transaction
     * 
     * @return ActiveRecord $Socios
     */
    public static function crearSocio(&$config, $transaction)
    {

        //Miramos el tipo de número de acción
        SociosCore::setNumeroAccionManual($config, $transaction);
        
        //Creamos registro de socios
        $Socios = EntityManager::get('Socios', true)->setTransaction($transaction);
        
        if ($config['titularId']) {

        } else {
            $Socios->setSociosId(0);
        }
        
        $Socios->setTitularId($config['titularId']);
        if ($config['fechaIngreso'] && $config['fechaIngreso']!='0000-00-00') {
            $Socios->setFechaIngreso($config['fechaIngreso']);
        } else {
            $Socios->setFechaIngreso('1900-01-01');
        }
        
        if ($config['fechaNacimiento'] && $config['fechaNacimiento']!='0000-00-00') {
            $Socios->setFechaNacimiento($config['fechaNacimiento']);
        } else {
            $Socios->setFechaNacimiento('1900-01-01');
        }
        
        if ($config['imprime']) {
            $Socios->setImprime($config['imprime']);
        } else {
            $Socios->setImprime('S');
        }
        
        $Socios->setParentescosId($config['parentescosId']);
        $Socios->setTipoDocumentosId($config['tipoDocumentosId']);
        $Socios->setIdentificacion($config['identificacion']);
        $Socios->setApellidos($config['apellidos']);
        $Socios->setNombres($config['nombres']);
        $Socios->setCiudadExpedido($config['ciudadExpedido']);
        $Socios->setCiudadNacimiento($config['ciudadNacimiento']);
        $Socios->setSexo($config['sexo']);
        $Socios->setDireccionCasa($config['direccionCasa']);
        $Socios->setCiudadCasa($config['ciudadCasa']);
        $Socios->setTelefonoCasa($config['telefonoCasa']);
        $Socios->setFax($config['fax']);
        $Socios->setDireccionTrabajo($config['direccionTrabajo']);
        $Socios->setTelefonoTrabajo($config['telefonoTrabajo']);
        $Socios->setCelular($config['celular']);
        $Socios->setDireccionCorrespondencia($config['direccionCorrespondencia']);
        $Socios->setCorreo1($config['correo1']);
        $Socios->setCorreo2($config['correo2']);
        $Socios->setTipoSociosId($config['tipoSociosId']);
        $Socios->setEnviaCorreo($config['enviaCorreo']);
        $Socios->setCobra($config['cobra']);
        $Socios->setEstadosSociosId($config['estadosSociosId']);
        
        if (isset($config['estadosCivilesId']) && $config['estadosCivilesId']>0) {
            $Socios->setEstadosCivilesId($config['estadosCivilesId']);
        }
        
        if (isset($config['imagenSocio'])) {
            $Socios->setImagenSocio($config['imagenSocio']);
        }
        
        //Número de acción
        if (isset($config['numeroAccionManual'])==true && $config['numeroAccionManual']=='S') {
            if (isset($config['numeroAccion'])==true && empty($config['numeroAccion'])==false) {
                $Socios->setNumeroAccion($config['numeroAccion']);
            } else {
                $transaction->rollback('Por favor ingresar un valor correcto a número de acción');
            }
        } else {
            $numeroAccion = SociosCore::makeNumeroAccion($Socios, $transaction);
            $Socios->setNumeroAccion($numeroAccion);
        }
        
        //Guardamos
        $Socios->setDebug(true);
        if ($Socios->save()==false) {
            foreach ($Socios->getMessages() as $message) {
                $transaction->rollback($message->getMessage());
            }
        }
        
        if ($Socios!=false) {
            $config['Socios'] = $Socios;
            $config['SociosId'] = $Socios->getSociosId();

            //Aumentamos el consecutivo de socios si es automatico
            SociosCore::aumentarConsecutivoSocios($transaction);

            //Estudios
            if (isset($config['estudiosCiudadId'])) {
                $configEstudios = array(
                    'SociosId'        => $Socios->getSociosId(),
                    'estudiosId'    => null,
                    'instituciones'    => $config['estudiosInstitucion'],
                    'ciudades'        => $config['estudiosCiudadId'],
                    'fechaGrados'    => $config['estudiosFechaGrado'],
                    'titulos'        => $config['estudiosTitulo'],
                );
                SociosCore::saveEstudios($configEstudios, $transaction);
            }

            //Experiencia Laboral
            if (isset($config['expLaboralesEmpresa'])) {
                $configExpLaboral = array(
                    'SociosId'                => $Socios->getSociosId(),
                    'expLaboralesId'        => null,
                    'expLaboralesEmpresa'    => $config['expLaboralesEmpresa'],
                    'expLaboralesDireccion'    => $config['expLaboralesDireccion'],
                    'expLaboralesCargo'        => $config['expLaboralesCargo'],
                    'expLaboralesTelefono'    => $config['expLaboralesTelefono'],
                    'expLaboralesFax'        => $config['expLaboralesFax'],
                    'expLaboralesFecha'        => $config['expLaboralesFecha']
                );
                SociosCore::saveExpLaboral($configExpLaboral, $transaction);
            }
            
            //Actividades
            if (isset($config['actividades'])) {
                $configActividades = array(
                    'SociosId'            => $Socios->getSociosId(),
                    'actividades'        => $config['actividades']
                );
                SociosCore::saveActividades($configActividades, $transaction);
            }

            //Clubes
            if (isset($config['clubId'])) {
                $configClubes = array(
                    'SociosId'        => $Socios->getSociosId(),
                    'clubes'        => $config['clubId'],
                    'clubesDesde'    => $config['clubDesde'],
                );
                SociosCore::saveClubes($configClubes, $transaction);
            }

            //Cargos Periodicos
            if (isset($config['cargosFijosId'])) {
                $configCargosFijos = array(
                    'SociosId'        => $Socios->getSociosId(),
                    'cargosFijos'    => $config['cargosFijosId']
                );
                SociosCore::saveCargosFijos($configCargosFijos, $transaction);
            }

            //Otros Socios
            if (isset($config['otrosSociosId'])) {
                $configOtrosSocios = array(
                    'SociosId'                => $Socios->getSociosId(),
                    'asignacionSocioId'        => null,
                    'otrosSociosId'            => $config['otrosSociosId'],
                    'tipoAsociacionSocioId'    => $config['tipoAsociacionSocioId'],
                );
                SociosCore::saveOtrosSocios($configOtrosSocios, $transaction);
            }
            
            //Porteria
            /*$configPorteriaSocios = array(
                'SociosId'        => $Socios->getSociosId(),
                'socios'        => $Socios
            );
            SociosCore::savePorteria($configPorteriaSocios, $transaction);
            */
            
            //If have numero tarjeta
            if ($config['numeroTarjeta']) {
                $configNumeroTarjeta = array(
                    'SociosId'                => $Socios->getSociosId(),
                    'numeroTarjeta'            => $config['numeroTarjeta'],
                    'formasPagosId'            => $config['formasPagosId'],
                    'fechaExp'                => $config['fechaExp'],
                    'fechaVen'                => $config['fechaVen'],
                    'bancosId'                => $config['bancosId'],
                    'digitoVerificacion'    => $config['digitoVerificacion'],
                    'estado'                => $config['estado'],
                );
                SociosCore::savePagosAutomaticos($configNumeroTarjeta, $transaction);
            }

            //Validación Socio
            SociosTest::validarSocio($config, $transaction);
        }
        return $Socios;
    }

    /**
    * Pagos automaticos
    */
    public static function savePagosAutomaticos($configNumeroTarjeta, $transaction)
    {

        $pagosAutomaticos = new PagosAutomaticos();
        $pagosAutomaticos->setTransaction($transaction);
        $pagosAutomaticos->setSociosId($configNumeroTarjeta['SociosId']);
        $pagosAutomaticos->setNumeroTarjeta($configNumeroTarjeta['numeroTarjeta']);
        $pagosAutomaticos->setFormasPagoId($configNumeroTarjeta['formasPagosId']);
        $pagosAutomaticos->setFechaExp($configNumeroTarjeta['fechaExp']);
        $pagosAutomaticos->setFechaVen($configNumeroTarjeta['fechaVen']);
        $pagosAutomaticos->setBancosId($configNumeroTarjeta['bancosId']);
        $pagosAutomaticos->setDigitoVerificacion($configNumeroTarjeta['digitoVerificacion']);
        $pagosAutomaticos->setEstado($configNumeroTarjeta['estado']);
        
        if ($pagosAutomaticos->save()==false) {
            foreach ($pagosAutomaticos->getMessages() as $message) {
                $transaction->rollback($message->getMessage());
            }
        }
    }

    /**
     * Metodo que limpia la BD de datos
     */
    public static function limpiarBD($transaction)
    {
        //Delete all models
        $models = array('Socios', 'Estudios', 'Explaboral', 'AsociacionSocio', 'Actividades', 'Asoclubes', 'AsignacionCargos', 'CargosSocios', 'Movimiento', 'DetalleMovimiento', 'Factura', 'DetalleFactura', 'pagosAutomaticos');
        ActiveRecord::disableEvents(true);
        
        foreach ($models as $model) {
            $tempModel = self::getModel($model)->setTransaction($transaction);
            if ($tempModel->delete(array('conditions'=>'1=1'))==false) {
                foreach ($tempModel->getMessages() as $message) {
                    $transaction->rollback($message->getMessage());
                }
            }
        }

        //inicialize periodos
        $periodo = self::getModel('periodo')->setTransaction($transaction)->deleteAll();
        $periodo = self::getModel('periodo', true)->setTransaction($transaction);
        $periodo->setPeriodo('201001');
        $periodo->setIniFact(1);
        $periodo->setFinFact(2000);
        $periodo->setCierre('N');
        $periodo->setFacturado('N');
        $periodo->setInteresesMora(2.0);
        
        if ($periodo->save()==false) {
            foreach ($periodo->getMessages() as $message) {
                $transaction->rollback($message->getMessage());
            }
        }
        
        //Initialize autoincremnt of RC y reservas in empresa
        $datosClub = self::getModel('DatosClub')->setTransaction($transaction)->findFirst();
        $datosClub->setTransaction($transaction);
        $datosClub->setNumsoc(0);//incializa consecutivo de Socios
        if ($datosClub->save()==false) {
            foreach ($datosClub->getMessages() as $message) {
                $transaction->rollback($message->getMessage());
            }
        }
    }

    /**
     * Metodo que actualiza la información de estudios de una socio
     * 
     * @param $config = array(
     *     'SociosId'        => $sociosId,
     *     'estudiosId'    => $ids,
     *    'instituciones'    => $instituciones,
     *    'ciudades'        => $ciudades,
     *    'fechaGrados'    => $fechaGrados,
     *    'titulos'        => $titulos
     * )
     * @param $transaction
     */
    public static function saveEstudios(&$config, $transaction)
    {
        //Parametros
        if (isset($config['SociosId'])==false || empty($config['SociosId'])==true) {
            $transaction->rollback('El id del socio es necesario para registrar sus estudios');
        }
        $sociosId        = $config['SociosId'];
        $estudiosId        = $config['estudiosId'];
        $instituciones    = $config['instituciones'];
        $ciudades        = $config['ciudades'];
        $fechaGrados    = $config['fechaGrados'];
        $titulos        = $config['titulos'];

        //Borramos todos los estudion
        $noBorrarIds = '';
        if (is_array($estudiosId)==true && count($estudiosId)>0) {
            $noBorrarIds = ' AND id NOT IN('.implode(', ', $estudiosId).')';
        }
        
        $estudios = EntityManager::get('Estudios')->setTransaction($transaction)->delete(array('conditions'=>'socios_id="'.$sociosId.'"'.$noBorrarIds));
        $numero = 0;
        
        if (is_array($instituciones)==true && count($instituciones)>0) {

            //Recorremos las isntituciones e insertamos los registros
            foreach ($instituciones as $n => $val) {
                $estudios = EntityManager::get('Estudios', true)->setTransaction($transaction);
                $estudios->setSociosId($sociosId);
                $estudios->setInstitucion($instituciones[$numero]);
                $estudios->setCiudad($ciudades[$numero]);
                $estudios->setFechaGrado($fechaGrados[$numero]);
                $estudios->setTitulo($titulos[$numero]);

                if ($estudios->save()==false) {
                    foreach ($estudios->getMessages() as $message) {
                        $transaction->rollback($message->getMessage());
                    }
                }
                $numero++;
            }
        }

    }

    /**
    * Metodo que actualiza la información de la experiencia laboral de un socio
    * 
    * @param $config = array(
    *     'SociosId'                => $record->getSociosId(),
    *    'expLaboralesId'        => $expLaboralesId,
    *    'expLaboralesEmpresa'    => $expLaboralesEmpresa,
    *    'expLaboralesDireccion'    => $expLaboralesDireccion,
    *    'expLaboralesCargo'        => $expLaboralesCargo,
    *    'expLaboralesTelefono'    => $expLaboralesTelefono,
    *    'expLaboralesFax'        => $expLaboralesFax,
    *    'expLaboralesFecha'        => $expLaboralesFecha
    * )
    * @param $transaction
    */
    public static function saveExpLaboral(&$config, $transaction)
    {
        //Parametros
        if (isset($config['SociosId'])==false || empty($config['SociosId'])==true) {
            $transaction->rollback('El id del socio es necesario para registrar sus experiencias laborales');
        }

        $sociosId                = $config['SociosId'];
        $expLaboralesId            = $config['expLaboralesId'];
        $expLaboralesEmpresa    = $config['expLaboralesEmpresa'];
        $expLaboralesDireccion    = $config['expLaboralesDireccion'];
        $expLaboralesCargo        = $config['expLaboralesCargo'];
        $expLaboralesTelefono    = $config['expLaboralesTelefono'];
        $expLaboralesFax        = $config['expLaboralesFax'];
        $expLaboralesFecha        = $config['expLaboralesFecha'];
        
        //Borramos todos los estudion
        $noBorrarIds = '';
        if (is_array($expLaboralesId)==true && count($expLaboralesId)>0) {
            $noBorrarIds = ' AND id NOT IN('.implode(', ', $expLaboralesId).')';
        }
        
        //Borramos registros
        $explaboral = EntityManager::get('Explaboral')->setTransaction($transaction)->deleteAll('socios_id='.$sociosId.$noBorrarIds);
        $numero = 0;
        if (is_array($expLaboralesEmpresa)==true && count($expLaboralesEmpresa)>0) {
            
            //Recorremos las experiencias laborales e insertamos los registros
            foreach ($expLaboralesEmpresa as $n => $val) {
                if (isset($expLaboralesEmpresa[$numero]) && empty($expLaboralesEmpresa[$numero])==false
                    &&
                    isset($expLaboralesDireccion[$numero]) && empty($expLaboralesDireccion[$numero])==false
                    &&
                    isset($expLaboralesCargo[$numero]) && empty($expLaboralesCargo[$numero])==false
                    &&
                    isset($expLaboralesTelefono[$numero]) && empty($expLaboralesTelefono[$numero])==false
                    &&
                    isset($expLaboralesFax[$numero]) && empty($expLaboralesFax[$numero])==false
                    &&
                    isset($expLaboralesFecha[$numero]) && empty($expLaboralesFecha[$numero])==false
                ) {
                    $explaboral = EntityManager::get('Explaboral', true)->setTransaction($transaction);
                    $explaboral->setSociosId($sociosId);
                    $explaboral->setEmpresa($expLaboralesEmpresa[$numero]);
                    $explaboral->setDireccion($expLaboralesDireccion[$numero]);
                    $explaboral->setCargo($expLaboralesCargo[$numero]);
                    $explaboral->setTelefono($expLaboralesTelefono[$numero]);
                    $explaboral->setFax($expLaboralesFax[$numero]);
                    $explaboral->setFecha($expLaboralesFecha[$numero]);

                    if ($explaboral->save()==false) {
                        foreach ($explaboral->getMessages() as $message) {
                            $transaction->rollback($message->getMessage());
                        }
                    }
                }
                $numero++;
            }
        }
    }

    /**
     * Metodo privado que crea/actualiza Actividades libres de un socio
     *
     * @param array = $config(
     *     'SociosId'        => int,
     *     'actividades'    => array(id1, id2, ...)
     * ) 
     * @param $transaction
     */
    public static function saveActividades(&$config, $transaction)
    {
        //Parametros
        if (isset($config['SociosId'])==false || empty($config['SociosId'])==true) {
            $transaction->rollback('El id del socio es necesario para registrar sus actividades');
        }

        $sociosId            = $config['SociosId'];
        $actividadesArray    = array();
        
        if (isset($config['actividades'])==true) {
            $actividadesArray = $config['actividades'];
        }
        
        //Borramos todas las actividades del socio
        $actividades = EntityManager::get('Actividades')->setTransaction($transaction)->delete('socios_id='.$sociosId);
        
        //Recorremos las actividades e insertamos los registros
        if (is_array($actividadesArray)==true && count($actividadesArray)>0) {
            foreach ($actividadesArray as $actividad) {
                $actividades = EntityManager::get('Actividades', true)->setTransaction($transaction);
                $actividades->setSociosId($sociosId);
                $actividades->setHobbiesId($actividad);

                if ($actividades->save()==false) {
                    foreach ($actividades->getMessages() as $message) {
                        $actividades->appendMessage('Actividades: '.$message->getMessage());
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Metodo privado que crea/actualiza clubes que pertenece un socio
     *
     * @param $config array
     * @param $transaction TransactionManager
     * @return bool
     */
    public static function saveClubes(&$config, $transaction)
    {
        //Parametros
        if (isset($config['SociosId'])==false || empty($config['SociosId'])==true) {
            $transaction->rollback('El id del socio es necesario para registrar sus actividades');
        }

        $sociosId     = $config['SociosId'];
        $clubesArray  = array();
        
        if (isset($config['clubes'])==true) {
            $clubesArray = $config['clubes'];
        }
        
        $clubesDesdeArray    = array();
        if (isset($config['clubesDesde'])==true) {
            $clubesDesdeArray = $config['clubesDesde'];
        }
        
        //Borramos todos los clubes asociados al socio
        $asoclubes = EntityManager::get('Asoclubes')->setTransaction($transaction)->delete('socios_id='.$sociosId);
        
        //Recorremos los clubes e insertamos los registros
        if (is_array($clubesArray)==true && count($clubesArray)>0) {
            foreach ($clubesArray as $clubId) {
                $asoclubles = EntityManager::get('Asoclubes', true)->setTransaction($transaction);
                $asoclubles->setSociosId($sociosId);
                $asoclubles->setClub($clubId);
                $asoclubles->setDesde($clubesDesdeArray[$clubId]);

                if ($asoclubles->save()==false) {
                    foreach ($asoclubles->getMessages() as $message) {
                        $transaction->rollback($message->getMessage());
                    }
                }
            }
        }
        return true;
    }

    /**
     * Metodo privado que crea/actualiza los cargos fijos que tiene un socio
     *
     * @param array = $config(
     *     'SociosId'        => int
     *     'cargosFijos'    => array(id1, id2, ...)
     * ) 
     * @param $transaction
     */
    public static function saveCargosFijos(&$config, $transaction)
    {
        //Parametros
        if (isset($config['SociosId'])==false || empty($config['SociosId'])==true) {
            $transaction->rollback('El id del socio es necesario para registrar sus actividades');
        }

        $sociosId            = $config['SociosId'];
        $cagosFijosArray    = array();

        if (isset($config['cargosFijos'])==true) {
            $cagosFijosArray = $config['cargosFijos'];
        }

        //Borramos todos los cargos asignados al socio
        $asignacionCargos = EntityManager::get('AsignacionCargos')->setTransaction($transaction)->delete('socios_id='.$sociosId);
        
        //Recorremos los cargos fijos e insertamos los registros
        if (is_array($cagosFijosArray)==true && count($cagosFijosArray)>0) {
            foreach ($cagosFijosArray as $cargoFijoId) {
                $asignacionCargos = EntityManager::get('AsignacionCargos', true)->setTransaction($transaction);
                $asignacionCargos->setSociosId($sociosId);
                $asignacionCargos->setCargosFijosId($cargoFijoId);
                $asignacionCargos->setEstado('A');//Activo

                if ($asignacionCargos->save()==false) {
                    foreach ($asignacionCargos->getMessages() as $message) {
                        $transaction->rollback($message->getMessage());
                    }
                }
            }
        }
        return true;
    }

    /**
     * Metodo que crea un socios como un tercero en ramocol si no existe en esta
     * @param ActiveRecord->Socios $Socios
     */
    public static function saveNitInvoicer($socios, $transaction)
    {
        if (gettype($socios) != "object") {
            $socios = BackCacher::getSocios($socios);
        }

        $sociosId = $socios->getSociosId();

        //buscamos facturas con socios_id en tabla Factura
        $facturaObj = EntityManager::get('Factura')->find("socios_id='$sociosId'");
        foreach ($facturaObj as $factura) {
            //buscamos invoicer en tabla Invoicer
            $invoicerObj = EntityManager::get('Invoicer')->find("numero='{$factura->getNumero()}'");
            foreach ($invoicerObj as $invoicer) {
                $nitSocios = $socios->getIdentificacion();
                $nitInvo = $invoicer->getNit();
                if ($nitInvo!=$nitSocios) {
                    $invoicer->setNit($nitSocios);
                    $invoicer->setNitEntregar($nitSocios);
                    if ($invoicer->save()==false) {
                        foreach ($invoicer->getMessages() as $msg) {
                            throw new SociosException("InvoicerNit: ".$msg->getMessage());
                            unset($msg);
                        }
                    }
                }
            }
            unset($factura);
        }
        unset($facturaObj);

        return true;
    }

    /**
     * Metodo que crea un socios como un tercero en ramocol si no existe en esta
     * @param ActiveRecord->Socios $Socios
     */
    public static function saveTerceros($socios, $transaction)
    {

        if (gettype($socios) != "object") {
            $socios = BackCacher::getSocios($socios);
        }

        $nit = $socios->getIdentificacion();
        $tercero = EntityManager::get('Nits')->findFirst(array('conditions'=>'nit="'.$nit.'"'));
        $nombre = $socios->getNumeroAccion()."/".$socios->getApellidos().' '.$socios->getNombres();
        //throw new Exception($nombre);
        
        //Rcs::disable();
        if ($tercero==false) {
            $tercero = new Nits();
            $tercero->setNit($nit);
            $tercero->setClase('C');//Cedula
            $tercero->setTipoDoc(13);//Cedula Local
            $tercero->setNombre($nombre);
            $tercero->setDireccion($socios->getDireccionCasa());
            $tercero->setTelefono($socios->getTelefonoCasa());
            if ($tercero->save()==false) {
                foreach ($tercero->getMessages() as $message) {
                    throw new SociosException($message->getMessage());
                }
            }
        } else {
            $tercero->setClase('C');//Cedula
            $tercero->setTipoDoc(13);//Cedula Local
            $tercero->setNombre($nombre);
            if ($tercero->save()==false) {
                foreach ($tercero->getMessages() as $message) {
                    throw new SociosException($message->getMessage());
                }
            }
        }
        //Rcs::enable();

        //Sincroniza recepcion?
        $syncHotel2 = Settings::get('sync_hotel2', 'SO');
        if (!$syncHotel2) {
            throw new SociosException("No se ha definido si desea sincronizar con hotel2");
        }

        //Verificamos en recepcion si existe el socio
        if ($syncHotel2=="S") {
            $observaciones = '';
            $clientes = EntityManager::get('Clientes')->findFirst("cedula='$nit'");
            if (!$clientes) {
                $clientes = new Clientes();
                $clientes->setTipdoc(1);
                $clientes->setCedula($nit);
                $clientes->setLugexp($socios->getCiudadExpedido());
                $clientes->setDireccion($socios->getDireccionCasa());
                $clientes->setTelefono1($socios->getTelefonoCasa());
                $clientes->setEmail($socios->getCorreo1());
                $clientes->setSexo($socios->getSexo());
                $clientes->setTipcre("P");//Tipo de creditro permanente
                $clientes->setCupo(5000000);//5 millones de cupo
                $clientes->setDiaven(90);
                $clientes->setCuepla(date("Y-m-d"));
                $clientes->setExento("N");
                $clientes->setClides("N");
                $clientes->setTipinf("N");
                $clientes->setNumest(0);
                $clientes->setLocnac(0);
                $observaciones = "Se creo por maestro de socios";
            }
            $clientes->setCredito('S');
            $clientes->setCredito('A');

            if ($socios->getCobra()=='S') {
                //$clientes->setCredito('S');
                //$clientes->setEstsis("A");//Activo
                //$observaciones = "Se asigno Credito Si por estar generando estado de cuenta en maestro de socios.";
            } else {
                //$clientes->setCredito('N');
                //$clientes->setEstsis("I");//Inctivo
                //$observaciones = "Se asigno Credito No por no estar generando estado de cuenta en maestro de socios.";
            }
            $identity = IdentityManager::getActive();
            $clientes->setObservacion($clientes->getObservacion().",\n".date("Y-m-d H:i:s")." [{$identity['login']}]- ".$observaciones);

            $clientes->setNombre($socios->getNumeroAccion()." // ".$socios->getNombres()." ".$socios->getApellidos());
            $clientes->setAccion($socios->getNumeroAccion());
            $clientes->setDireccion($socios->getDireccionCasa());

            if (!$clientes->save()) {
                foreach ($clientes->getMessages() as $message) {
                    throw new SociosException($message->getMessage());
                }
            }
        }
        return true;
    }

    /**
     * Metodo que crea/actualiza los socios que presento el presente socio en el club
     * 
     * @param array = $config(
     *     'SociosId'                => $record->getSociosId(),
     *    'asignacionSocioId'        => $this->getPostParam('asignacionSocioId'),
     *    'otrosSociosId'            => $this->getPostParam('otrosSociosId'),
     *    'tipoAsociacionSocioId'    => $this->getPostParam('tipoAsociacionSocioId')
     * ) 
     * @param $transaction
     */
    public static function saveOtrosSocios(&$config, $transaction)
    {
        //Parametros
        if (isset($config['SociosId'])==false || empty($config['SociosId'])==true) {
            $transaction->rollback('El id del socio es necesario para registrar sus socios representantes.');
        }

        $sociosId                = $config['SociosId'];
        $asignacionSocioId        = $config['asignacionSocioId'];
        $otrosSociosId            = $config['otrosSociosId'];
        $tipoAsociacionSocioId    = $config['tipoAsociacionSocioId'];
        
        //Borramos todos los estudion
        $noBorrarIds = '';
        if (is_array($asignacionSocioId)==true && count($asignacionSocioId)>0) {
            $noBorrarIds = ' AND id NOT IN('.implode(', ', $asignacionSocioId).')';
        }
        
        //Borramos registros
        $asociacionSocio = EntityManager::get('AsociacionSocio')->setTransaction($transaction)->deleteAll('socios_id='.$sociosId.$noBorrarIds);
        $numero = 0;
        if (is_array($otrosSociosId)==true && count($otrosSociosId)>0) {
            //Recorremos las experiencias laborales e insertamos los registros
            foreach ($otrosSociosId as $n => $val) {
                if (isset($otrosSociosId[$numero]) && empty($otrosSociosId[$numero])==false
                    &&
                    isset($tipoAsociacionSocioId[$numero]) && empty($tipoAsociacionSocioId[$numero])==false
                ) {
                    $asociacionSocio = EntityManager::get('AsociacionSocio', true)->setTransaction($transaction);
                    $asociacionSocio->setSociosId($sociosId);
                    $asociacionSocio->setOtroSocioId($otrosSociosId[$numero]);
                    $asociacionSocio->setTipoAsociacionSocioId($tipoAsociacionSocioId[$numero]);

                    if ($asociacionSocio->save()==false) {
                        foreach ($asociacionSocio->getMessages() as $message) {
                            $transaction->rollback($message->getMessage());
                        }
                    }
                }
                $numero++;
            }
        }
        return true;
    }

    /**
     * Metodo que crea/actualiza los socios que presento el presente socio en el club en porteria
     * 
     * @param array = $config(
     *     'socios'                => $record,
     * ) 
     * @param $transaction
     */
    public static function savePorteria(&$config, $transaction)
    {

        //Parametros
        if (isset($config['SociosId'])==false || $config['SociosId']==false) {
            $transaction->rollback('El id del socios es necesatio. data: '.print_r($config, true));
        }

        $SociosId = $config['SociosId'];

        $socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($SociosId);

        if ($socios==false) {
            $transaction->rollback('No existe socio en aplicativo de socios');
        }

        //Miramos duplicidad
        $sociosPorteriaObj = EntityManager::get('SociosPorteria')->setTransaction($transaction)->findFirst(array('conditions'=>"numero_accion='{$socios->getNumeroAccion()}'"));
        if (count($sociosPorteriaObj)>1) {
            //Borramos los registros y creamos de nuevo
            $status = EntityManager::get('SociosPorteria')->setTransaction($transaction)->delete(array('conditions'=>"numero_accion='{$socios->getNumeroAccion()}'"));
        }

        //Search by socios_id
        $sociosPorteria = EntityManager::get('SociosPorteria')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id='$SociosId'"));
        if ($sociosPorteria==false) {

            //search by numeroA_ccion and identificacion
            $sociosPorteria = EntityManager::get('SociosPorteria')->setTransaction($transaction)->findFirst(array('conditions'=>"numero_accion='{$socios->getNumeroAccion()}' AND identificacion='{$socios->getIdentificacion()}'"));
            if ($sociosPorteria==false) {
                //New record
                $sociosPorteria = EntityManager::get('SociosPorteria', true)->setTransaction($transaction);
            }

        }

        $sociosPorteria->setNumeroAccion($socios->getNumeroAccion());
        if ($socios->getFechaInscripcion() && $socios->getFechaInscripcion()!='0000-00-00') {
            $sociosPorteria->setFechaInscripcion($socios->getFechaInscripcion());
        }
        if ($socios->getFechaIngreso() && $socios->getFechaIngreso()!='0000-00-00') {
            $sociosPorteria->setFechaIngreso($socios->getFechaIngreso());
        }
        if ($socios->getFechaNacimiento() && $socios->getFechaNacimiento()!='0000-00-00') {
            $sociosPorteria->setFechaNacimiento($socios->getFechaNacimiento());
        }
        if ($socios->getFechaRetiro() && $socios->getFechaRetiro()!='0000-00-00') {
            $sociosPorteria->setFechaRetiro($socios->getFechaRetiro());
        }
        
        $sociosPorteria->setNombres($socios->getNombres());
        $sociosPorteria->setApellidos($socios->getApellidos());
        $sociosPorteria->setIdentificacion($socios->getIdentificacion());
        $sociosPorteria->setTelefonoCasa($socios->getTelefonoCasa());
        $sociosPorteria->setCelular($socios->getCelular());
        $sociosPorteria->setCorreo1($socios->getCorreo1());

        $estadoPorteria = '';
        $estadoId = (int) $socios->getEstadosSociosId();
        if ($estadoId==1) {
            $estadoPorteria = 'A';
        } else {
            if ($estadoId==4) {
                $estadoPorteria = 'T';
            } else {
                if ($estadoId==5) {
                    $estadoPorteria = 'S';
                } else {
                    if ($estadoId==6) {
                        $estadoPorteria = 'V';
                    } else {
                        $estadoPorteria = 'I';
                    }
                }
            }
        }

        $sociosPorteria->setEstado($estadoPorteria);

        $lastId = $sociosPorteria->getId();

        //Socios id
        $sociosPorteria->setSociosId($SociosId);

        if ($sociosPorteria->save()==false) {
            foreach ($sociosPorteria->getMessages() as $message) {
                $transaction->rollback($message->getMessage());
            }
        }

    }

    /**
     * Metodo que genera el número de acción del socio segun si es manual o no
     * 
     * @param ActiveRecord $record
     * @param ActiveRecordTransaction $transaction
     * 
     * @return string $numeroAccion
     */
    public static function makeNumeroAccion($record, $transaction)
    {
        $numeroAccion = '';
        if ($record && $record->getNumeroAccion()!='') {
            $numeroAccion = $record->getNumeroAccion();
        }

        //Miramos si es numero de accion manual o no
        $configuration = EntityManager::get('Configuration')->setTransaction($transaction)->findFirst(array('conditions'=>'application="SO" AND name="numero_accion_manual"'));
        if ($configuration==false) {
            $transaction->rollback('No existe configuración de número de acción manual');
        }
        
        //Si esta vacio el numero de acción y Es numero de ación con consecutivo
        if (empty($numeroAccion)==true && $configuration->getValue()=='N') {
            if ($record->getTitularId() > 0) {
                $titular = EntityManager::get('Socios', true)->setTransaction($transaction)->findFirst($record->getTitularId());
                if ($titular==false) {
                    $transaction->rollback('El titular no existe ');
                }
                $numeroAccion = $titular->getNumeroAccion();
            } else {
                $datosClub = EntityManager::get('DatosClub')->setTransaction($transaction)->findFirst();
                $consecutivo = $datosClub->getNumSoc()+1;
                $numeroAccion = ((int) $record->getTipoSociosId()).'-'.$consecutivo;
            }
        } else {
            if (empty($numeroAccion)==true) {
                $transaction->rollback('El numero de acción esta manual y el numero de accion esta vacio');
            }
        }
        return $numeroAccion;
    }

    /**
     * Metodo que aumenta el consecutivo del socio si es automatico
     * 
     * @param ActiveRecordTransaction $transaction
     */
    public static function aumentarConsecutivoSocios($transaction)
    {
        //Miramos si es numero de accion manual o no
        $configuration = EntityManager::get('Configuration')->findFirst(array('conditions'=>'application="SO" AND name="numero_accion_manual"'));
        if ($configuration==false) {
            $configuration->appendMessage(new TransacctionMessage('No existe configuración de número de acción manual'));
            return false;
        }

        //Si esta vacio el numero de acción y Es numero de ación con consecutivo
        if (empty($numeroAccion)==true && $configuration->getValue()=='N') {
            $datosClub = EntityManager::get('DatosClub')->setTransaction($transaction)->findFirst();
            $consecutivo = $datosClub->getNumSoc()+1;
            $datosClub->setNumSoc($consecutivo);

            if ($datosClub->save()==false) {
                foreach ($datosClub->getMessages() as $message) {
                    $transaction->rollback($message->getMessage());
                }
            }
        }
    }

    /**
     * Metodo que obtiene el periodo actual y si no existe lo genera
     *
     * @param char(6) $periodoChar, default today
     * @return char(6)
     */
    public static function checkPeriodo($periodoChar = false, $transaction = false)
    {
        //Si no asignamos un periodo sacamos el actual
        if (!$periodoChar) {
            $periodoChar = date("Ym");
        }

        //Validamos si existe periodo
        $periodo = EntityManager::get('Periodo')->findFirst(array('conditions'=>"periodo='$periodoChar'"));

        //Si no existe creamos el periodo
        if ($periodo==false) {
            $periodo = SociosCore::makePeriodo($periodoChar, $transaction);
        }
        return $periodo->getPeriodo();
    }

    public static function getCurrentPeriodo()
    {
        $periodo = EntityManager::get('Periodo')->findFirst(array("cierre='N'",'order'=>'periodo ASC'));
        if (!$periodo) {
            throw new Exception("Nos e encontró un periodo abierto actualmente");
        }
        $periodoStr = $periodo->getPeriodo();
        return $periodoStr;
    }

    public static function getCurrentPeriodoObject($periodo=0)
    {
        if ($periodo>0) {
            $periodoObj = EntityManager::get('Periodo')->findFirst($periodo);
        } else {
            $periodoObj = EntityManager::get('Periodo')->findFirst(array("cierre='N'",'order'=>'periodo ASC'));
        }
        return $periodoObj;
    }

    /**
    * Obtiene el la mora del periodo actual
    */
    public static function getCurrentPeriodoMora($periodo=0)
    {
        if ($periodo>0) {
            $periodo = SociosCore::getCurrentPeriodoObject($periodo);
        } else {
            $periodo = SociosCore::getCurrentPeriodoObject();
        }
        $mora = $periodo->getInteresesMora();
        return $mora;
    }
     
     /**
     * Metodo que obtiene el periodo actual y si no existe lo genera
     *
     * @param char(6) $periodoChar, default today
     * @return char(6)
     */
    public static function makePeriodo($periodoChar = false, $transaction = false)
    {
        //Si no asignamos un periodo sacamos el actual
        if (!$periodoChar) {
            $periodoChar = date("Ym");
        }
        
        //Validamos si existe periodo
        $periodo = EntityManager::get('Periodo')->findFirst(array('conditions'=>"periodo='$periodoChar'"));
        
        //Si no existe creamos el periodo
        if ($periodo==false) {
            $periodo = EntityManager::get('Periodo', true);
            if ($transaction!=false) {
                $periodo->setTransaction($transaction);
            }
            //throw new SociosException($periodoChar);
            
            $periodo->setPeriodo($periodoChar);

            //make date
            $periodo->setCierre('N');
            $periodo->setFacturado('N');
            
            //buscamos interes de mora de periodo
            $interesMoraDefault = Settings::get('interes_mora_default', 'SO');
            if (!$interesMoraDefault) {
                throw new SociosException("El porcentaje de mora por defecto para periodo no se ha digitado");
            }
            
            $periodo->setInteresesMora($interesMoraDefault);

            //Consecutivo segun fechas
            $consecutivo = EntityManager::get('Consecutivos')->findFirst("numero_actual<numero_final");
            if (!$consecutivo) {
                throw new SociosException("El no hay consecutivos con numeros disponibles.");
            }
            $periodo->setConsecutivosId($consecutivo->getId());
            
            if ($periodo->save()==false) {
                foreach ($periodo->getMessages() as $message) 
                {
                    throw new SociosException($message->getMessage());
                }
            }
        }
        return $periodo;
    }
    
    /**
     * Metodo que genera toda la información de un socio
     * 
     * @param ActiveRecord $socios
     * 
     * @return array $info
     */
    public static function makeAllSocioInfo($socios) 
    {
        $info = array();
        $models = array('Socios', 'Estudios', 'Explaboral', 'Actividades', 'Asoclubes', 'CargosSocios', 'AsociacionSocio');

        if ($socios!=false) {
            foreach ($models as $model) 
            {
                $modelTemp = EntityManager::get($model)->findFirst(array('conditions'=>'socios_id='.$socios->getSociosId()));

                if ($modelTemp!=false) {
                    $info[$model] = array();
                    foreach ($modelTemp->getAttributes() as $field) 
                    {
                        $info[$model][$field] = $modelTemp->readAttribute($field);
                    }
                }
            }
        }
        return $info;
    }
    
    public static function cambiarEstadoSocio($sociosParam, $estadosSociosId=false) 
    {
        if (!$estadosSociosId) {
            throw new SociosException('No se ha definido que estado se desea cambiar');
        }
        
        if (is_object($sociosParam)) {
            $socios = $sociosParam;
        } else {
            if (is_numeric($sociosParam)) {
                $socios = BackCacher::getSocios($sociosParam);
                if (!$socios) {
                    throw new SociosException('No existe el socio con el id '.$sociosParam);
                }
            }
        }
        
        //Estados de Socios
        $estadosSocios = EntityManager::get('EstadosSocios')->findFirst($estadosSociosId);
        if (!$estadosSocios) {
            throw new SociosException('El estado a asignar no existe');
        }
        
        //Miramos si no genera factura ese socio
        if ($estadosSocios->getAccion()=='I') {
            $socios->setCobra('N');
        }
        
        //Cambiamo el estado al socio
        $socios->setEstadosSociosId($estadosSociosId);
        if ($socios->save()==false) {
            foreach ($socios->getMessages() as $msg)
            {
                throw new SociosException($msg->getMessage());
            }
        }
        
        //Buscamos acciones a tomar segun estado a cambiar
        $accionEstadosObj = EntityManager::get('AccionEstados')->find(array("estados_socios_id='$estadosSociosId'"));
        foreach ($accionEstadosObj as $accionEstados) 
        {
            //realizamos la accion definida
            $asignacionCargosObj = EntityManager::get('AsignacionCargos')->find("socios_id='{$socios->getSociosId()}' AND cargos_fijos_id='{$accionEstados->getCargosFijosIdIni()}'");
            foreach ($asignacionCargosObj as $asignacionCargos)
            {
                if ($accionEstados->getBorrarCargoFijo()=='S') {
                    //Inactivamos los cargos fijos registrados
                    $asignacionCargos->setEstado('I');
                } else {
                    //Asignamos el otro cargo y activamos si se borro previamente
                    $asignacionCargos->setCargosFijosId($accionEstados->getCargosFijosIdFin());
                    $asignacionCargos->setEstado('A');
                }
                
                if ($asignacionCargos->save()==false) {
                    foreach ($socios->getMessages() as $msg)
                    {
                        throw new SociosException($msg->getMessage());
                    }
                }
                unset($asignacionCargos);
            }
            unset($accionEstados,$asignacionCargosObj);
        }
        unset($accionEstadosObj);
    }
    
    /**
     * Obtiene los cargos fijos existentes
     * @return array
     */
    public function getCargosFijos()
    {
        $cargos = array();
        
        $cargosFijosObj = EntityManager::get("CargosFijos")->find("estado='A'"); 
        if (!count($cargosFijosObj)) {
            throw new SociosException('No se ha ingresado cargos fijos al sistema');
        }
        
        foreach ($cargosFijosObj as $cargoFijo)
        {
            $cargos[$cargoFijo->getId()] = SociosCore::modelToArray($cargoFijo);
            unset($cargoFijo);
        }
        unset($cargosFijosObj);
        
        return $cargos;
    }
    
    /**
     * Obtiene los tipo socios existentes
     * @return array
     */
    public function getTipoSocios()
    {
        $tipos = array();
        $tipoSociosObj = EntityManager::get("TipoSocios")->find("estado='A'");
        if (!count($tipoSociosObj)) {
            throw new SociosException('No se ha ingresado tipo de socios al sistema');
        }
        foreach ($tipoSociosObj as $tipoSocio)
        {
            $tipos[$tipoSocio->getId()] = SociosCore::modelToArray($tipoSocio);
            unset($tipoSocio);
        }
        return $tipos;
    }

    /**
     * Obtiene las facturas directas generadas de socios en el POS que no pasan a cartera
     * @param int periodo
     * @param string nit 
     * @return array
     */
    public static function getFacturasDirectasPos($periodo,$nit)
    {
        if (!$nit) {
            throw new SociosException("getFacturasDirectasPos: Debe dar el nit a buscar");
        }
        
        if (!$periodo) {
            throw new SociosException("getFacturasDirectasPos: Debe dar el perido a buscar");
        }
        
        $ano = substr($periodo,0,4);
        $mes = substr($periodo,4,2);

        //Se buscan es el mes anterior no el actual
        $periodoAnterior = SociosCore::subPeriodo($periodo,1);
        $anoAnte = substr($periodoAnterior,0,4);
        $mesAnte = substr($periodoAnterior,4,2);

        $ret = array();
        $facturaObj = EntityManager::get("FacturaPos")->find(array(
            'conditions' => "MONTH(fecha) = '$mes' AND YEAR(fecha) = '$ano' AND cedula = '$nit' AND estado='A' AND tipo_venta='F'",
            'columns' => 'prefijo_facturacion,consecutivo_facturacion,cedula,total,salon_nombre,fecha'
        )); //F son facturas de pos que no pasan a hotel

        foreach ($facturaObj as $factura)
        {
            $ret[] = SociosCore::modelToArray($factura);
            unset($factura);
        }
        unset($facturaObj);
        
        return $ret;
    }

    /**
    * Devulve el total de facturas directas de pos no estan en hotel5 y que fuern enviados a 
    * forma de pago Socios Cartera
    *
    * @param int $periodAtras
    * @param int $sociosId
    * @return double
    */
    public static function getTotalFacturaDPos($periodoAtras, $sociosId)
    {
        $socios = BackCacher::getSocios($sociosId);
        if (!$socios) {
            throw new SociosException("No existe el socio con id '$sociosId'", 1);
        }

        $facturasDirectasPos = SociosCore::getFacturasDirectasPos($periodoAtras, $socios->getIdentificacion());
        unset($socios);

        $posFormasPago = SociosCore::getSettingsValue('pos_formas_pago', 'SO');
        $posFormasPagoArray = explode(",",$posFormasPago);

        $totalFDP = 0;
        //Contamos consumos de Factura Directas de POS
        foreach ($facturasDirectasPos as $facturaPos)
        {
            $pagosFacturaPos = EntityManager::get('PagosFacturaPos')->findFirst("prefijo_facturacion='{$facturaPos->getPrefijoFacturacion()}' AND consecutivo_facturacion='{$facturaPos->getConsecutivoFacturacion()}'","columns: total");
            //42 es a Cartera Socios
            if ($pagosFacturaPos && in_array($pagosFacturaPos->getFormasPagoId(),$posFormasPagoArray)) {
                $totalFDP += $facturaPos['total'];
            }
            unset($pagosFacturaPos);
        }
        unset($facturasDirectasPos);

        return $totalFDP;
    }

    /**
    * Devulve el array de facturas directas de pos no estan en hotel5 y que fuern enviados a 
    * forma de pago Socios Cartera
    *
    * @param int $periodAtras
    * @param int $sociosId
    * @return array
    */
    public static function getFacturaDPos($periodoAtras, $sociosId)
    {
        $socios = BackCacher::getSocios($sociosId);
        if (!$socios) {
            throw new SociosException("No existe el socio con id '$sociosId'", 1);
        }

        $facturasDirectasPos = SociosCore::getFacturasDirectasPos($periodoAtras, $socios->getIdentificacion());
        unset($socios);

        $posFormasPago = SociosCore::getSettingsValue('pos_formas_pago', 'SO');
        $posFormasPagoArray = explode(",",$posFormasPago);

        $ret = array();
        //Contamos consumos de Factura Directas de POS
        foreach ($facturasDirectasPos as $facturaPos)
        {
            $pagosFacturaPos = EntityManager::get('PagosFacturaPos')->findFirst(array(
                'conditions' => "prefijo_facturacion='{$facturaPos['prefijo_facturacion']}' AND consecutivo_facturacion='{$facturaPos['consecutivo_facturacion']}'",
                'columns' => 'prefijo_facturacion,consecutivo_facturacion,formas_pago_id,pago'
            ));

            //42 es a Cartera Socios
            if ($pagosFacturaPos && in_array($pagosFacturaPos->getFormasPagoId(),$posFormasPagoArray)) {
                $ret[] = $facturaPos;
            }
            unset($factura);
        }
        unset($facturasDirectasPos);

        return $ret;
    }

    /**
     * Obtiene las facturas directas generadas de socios
     * @param int periodo
     * @param string nit 
     * @return array
     */
    public function getFacturasDirectas($periodo,$nit)
    {
        if (!$nit) {
            throw new SociosException("getFacturasDirectas: Debe dar el nit a buscar");
        }
        
        if (!$periodo) {
            throw new SociosException("getFacturasDirectas: Debe dar el perido a buscar");
        }
        
        $ano = substr($periodo,0,4);
        $mes = substr($periodo,4,2);

        //Se buscan es el mes anterior no el actual
        $periodoAnterior = SociosCore::subPeriodo($periodo,1);
        $anoAnte = substr($periodoAnterior,0,4);
        $mesAnte = substr($periodoAnterior,4,2);

        $ret = array();
        //$facturaHotelObj = EntityManager::get("FacturasHotel")->find(array("MONTH(fecfac) = '$mes' AND YEAR(fecfac) = '$ano' AND cedula = '$nit' AND estado='A'"));

        $conditionsPos = "cedula='$nit' AND year(fecfac)=$ano AND month(fecfac)=$mes AND saldo>0 AND estado='A'";
        //throw new SociosException($conditionsPos);
        
        $facturaHotelObj = EntityManager::get("FacturasHotel")->find($conditionsPos);

        foreach ($facturaHotelObj as $facturaHotel)
        {
            $ret[(float) $facturaHotel->getNumfac()] = SociosCore::modelToArray($facturaHotel);
            unset($facturaHotel);
        }
        unset($facturaHotelObj);
        //throw new SociosException(print_r($ret,true));
        
        return $ret;
    }
    
    /**
     * Verifica si un recibo de caja tiene una forma de pago
     * @param int $numrec
     * @param int $forpag
     * @return bool
     */
     public function getReccajFP($numrec,$forpag)
     {
         //Buscamos en valcar los Recibos de caja que tiene la factura 
        $detrec = EntityManager::get('Detrec')->findFirst("numrec='$numrec' AND forpag='$forpag'");
        if ($detrec) {
            return true;
        }
        return false;
     }
    
    /**
     * Buscamos en valcar los abonos hechos con una forma de pago
     * @param int $numfol
     * @param int $forpag
     * @return float
     */
     public function getValcarRC($numfol,$forpag)
     {
        $total = 0;
        
        //Buscamos en valcar los Recibos de caja que tiene la factura 
        $valcars = EntityManager::get('Valcar')->find("numfol='$numfol' AND cladoc='RC'");
        foreach ($valcars as $valcar)
        {
            $exists = $this->getReccajFP($valcar->getNumdoc(),$forpag);
            if ($exists) {
                $total += $valcar->getValor();
            }
            unset($valcar);
        }
        unset($valcars);
        
        return $total;
     } 
    
    /**
     * Obtiene las facturas directas generadas de socios que fueron enviadas a cartera 
     * @param int periodo
     * @param string nit 
     * @return array
     */
    public function getFacturasCartera($periodo,$nit)
    {
        try 
        {
            //Buscamos las facturas directas
            $facturas = $this->getFacturasDirectas($periodo,$nit);
            if (!count($facturas)) {
                return array('facturas' => array(), 'total' => 0);
            }
            
            //Buscamos facturas que tienen formade pago de cartera de socios   
            $total = 0;
            $totalIva = 0;
            foreach ($facturas as $numfac => $factura)
            {
                if ($factura['saldo']>0) {
                    $total += $factura['saldo'];
                } else {
                    unset($facturas[$numfac]); 
                }
                unset($factura);
            }
            
            $ret = array('facturas' => $facturas, 'total' => $total);
            
            return $ret;
        }
        catch(Exception $e) {
            throw new SociosException("getFacturasCartera: ".$e->getMessage());
        }
    }
    
    /**
     * Convierte un modelo a array
     * 
     * @param Object $model
     * @return array $modelData 
    */
    public static function modelToArray($model) 
    {
        $modelData = array();
        foreach ($model->getAttributes() as $field) 
        {
            $modelData[$field] = $model->readAttribute($field);
        }
        return $modelData;
    }

    /**
    * Genera la fecha de una factura por su periodo
    * @param int $periodo
    * @return date
    */
    public static function getFechaFactura($periodo)
    {
        if (!$periodo) {
            throw new SociosException("Debe indicar el periodo");
        }

        $periodoObj = EntityManager::get('Periodo')->findFirst($periodo);
        if (!$periodoObj) {
            throw new SociosException("El periodo indicado no existe en el formulario periodos");
        }

        if (!$periodoObj->getDiaFactura()) {
            throw new SociosException("No se ha indicado el dia a facturar en el periodo");
        }


        $year = substr($periodo,0,4);
        $month = substr($periodo,4,2);

        try 
        {
            $date = new Date("$year-$month-{$periodoObj->getDiaFactura()}");
            return $date;
        }
        catch(DateException $e) {
            throw new SociosException("'$year-$month-{$periodoObj->getDiaFactura()}', ".$e->getMessage());
        }
        catch(Exception $e) {
            throw new SociosException($e->getMessage());
        }
    }

    /**
    * Devuelve y valida la cuenta de saldo anterior
    * @return cuentas
    */
    public static function getCuentaSaldoAFavor()
    {
        $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
        if (!$cuentaSaldoAFavor) {
            throw new SociosException("No se ha configurado la cuenta del saldo a favor de pago de socios");
        }

        return $cuentaSaldoAFavor;
    }

    /**
    * Devuelve y valida la cuenta a pasar el saldo anterior
    * @return cuentas
    */
    public static function getPasarCuentaSaldoAFavor()
    {
        $cuentaPasarSaldoAFavor = Settings::get('cuenta_saldo_a_favor_pasar', 'SO');
        if (!$cuentaPasarSaldoAFavor) {
            throw new SociosException("No se ha configurado la cuenta a pasar el saldo a favor de pago de socios");
        }
        return $cuentaPasarSaldoAFavor;
    }

    /**
    * Devuelve y valida la cuenta a pasar el saldo anterior
    * @return cuentas
    */
    public static function checkComprobSaldoAFavor()
    {
        $comprobSaldoAFavor = Settings::get('comprob_saldoafavor', 'SO');
        if (!$comprobSaldoAFavor) {
            throw new SociosException("No se ha configurado el comprobante del saldo a favor para hacer cierre");
        }
        return $comprobSaldoAFavor;
    }

    /**
    * Devuelve y valida la cuenta a pasar el saldo anterior
    * @return cuentas
    */
    public static function getCuentaAjusteEstadoCuenta()
    {
        $cuenta = Settings::get('cuenta_ajustes_estado_cuenta', 'SO');
        if (!$cuenta) {
            throw new SociosException("No se ha configurada la cuenta de ajustes para estado de cuenta");
        }
        return $cuenta;
    }

    /**
    * Devuelve un numero quitando carcateres de fechas
    * @param array $contentMovi
    * @return array
    */
    public static function sortMoviContent($contentMovi)
    {
        usort($contentMovi, 'ordenarContentMovi');
        return $contentMovi;
    }    

    /**
    * Valida que una cuenta pida tercero y cartera
    * @return bool
    */
    public static function checkCuentaCartera($cuentaStr)
    {
        $cuenta = BackCacher::getCuenta($cuentaStr);
        if (!$cuenta) {
            throw new SociosException("La cuenta no existe");
        }
        
        if ($cuenta->getPideNit()!='S') {
            throw new SociosException("La cuenta '$cuentaStr' debe pedir tercero para usarla en cartera");
        }

        if ($cuenta->getPideFact()!='S') {
            throw new SociosException("La cuenta '$cuentaStr' debe pedir documento para usarla en cartera");
        }

        return true; 
    }

    /**
    * Busca los pagos de un periodo
    * @patam $periodo int 201311
    * @retrun array
    */
    public static function getPagosPeriodo($periodo)
    {
        if (!$periodo) {
            throw new SociosException("debe indicar el periodo a consultar pagos");
        }

        $ret = array();
        $year = substr($periodo, 0, 4);
        $month = substr($periodo, 4, 2);

        //comprobante de pagos
        $comprobsPagos = Settings::get('comprobs_pagos', 'SO');
        if (!$comprobsPagos) {
            throw new SociosException("Es necesario dar los comprobantes de pagos en configuración");
        }

        $comprobsPagosA = explode(',', $comprobsPagos);
        $comprobsPagosAs = implode("','", $comprobsPagosA);

        //Cuenta de saldo a favor
        $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
        if (!$cuentaSaldoAFavor) {
            throw new SociosException("No se ha configurado la cuenta de saldo a favor de socios");
        }

        $cuentasAjsEstadoCuenta = SociosCore::getCuentaAjusteEstadoCuenta();

        //$nit='1018406098';
        $conditions = "MONTH(fecha)='$month' AND YEAR(fecha)='$year' AND comprob IN('".$comprobsPagosAs."') AND cuenta LIKE '".$cuentasAjsEstadoCuenta."' OR cuenta = '$cuentaSaldoAFavor'";
        //throw new SociosException($conditions);
        
        $moviObj = EntityManager::get('Movi')->find(array('conditions'=>$conditions));

        foreach ($moviObj as $movi) {
            $nit = $movi->getNit();
            if (!isset($ret[$nit])) {
                $ret[$nit] = array(
                    'valor'=> array('D'=>0, 'C'=>0),
                    'fecha' => array(),
                    'comprob' => array(),
                    'numeroDoc' => array()
                );
            }

            $key = $movi->getComprob()."-".$movi->getNumero();
            $keyDoc = $movi->getTipoDoc()."-".$movi->getNumeroDoc();
            $ret[$nit]['comprob'][$key] = $key;
            $ret[$nit]['fecha'][$key] = $movi->getFecha();
            $ret[$nit]['numeroDoc'][$key] = $keyDoc;
            $ret[$nit]['valor'][$movi->getDebCre()] += $movi->getValor();

            unset($movi,$nit);
        }
        unset($moviObj);

        return $ret;
    }

    /**
    * Busca los pagos de un periodo
    * @param $periodo int 201311
    * @param $nit int 12131321321
    * @retrun array
    */
    public static function getPagosPeriodoXSocio($periodo, $nit)
    {
        if (!$periodo) {
            throw new SociosException("debe indicar el periodo a consultar pagos");
        }

        if (!$nit) {
            throw new SociosException("debe indicar el nit a consultar pagos");
        }
        $tercero = BackCacher::getTercero($nit);
        if (!$tercero) {
            throw new SociosException("el nit '$nit' no existe en terceros");
        }

        $ret = array();
        $year = substr($periodo, 0, 4);
        $month = substr($periodo, 4, 2);

        //comprobante de pagos
        $comprobsPagos = Settings::get('comprobs_pagos', 'SO');
        if (!$comprobsPagos) {
            throw new SociosException("Es necesario dar los comprobantes de pagos en configuración");
        }

        $comprobsPagosA = explode(',', $comprobsPagos);
        $comprobsPagosAs = implode("','", $comprobsPagosA);

        //Cuenta de saldo a favor
        $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
        if (!$cuentaSaldoAFavor) {
            throw new SociosException("No se ha configurado la cuenta de saldo a favor de socios");
        }

        $cuentasAjsEstadoCuenta = SociosCore::getCuentaAjusteEstadoCuenta();

        $conditions = "MONTH(fecha)='$month' AND YEAR(fecha)='$year' AND comprob IN('".$comprobsPagosAs."') AND cuenta LIKE '".$cuentasAjsEstadoCuenta."' OR cuenta = '$cuentaSaldoAFavor' AND nit='$nit'";
        
        $moviObj = EntityManager::get('Movi')->find(array('conditions'=>$conditions));

        foreach ($moviObj as $movi) {
            if (!isset($ret[$nit])) {
                $ret[$nit] = array(
                    'valor'=> array('D'=>0, 'C'=>0),
                    'fecha' => array(),
                    'comprob' => array(),
                    'numeroDoc' => array()
                );
            }

            $key = $movi->getComprob()."-".$movi->getNumero();
            $keyDoc = $movi->getTipoDoc()."-".$movi->getNumeroDoc();
            $ret[$nit]['comprob'][$key] = $key;
            $ret[$nit]['fecha'][$key] = $movi->getFecha();
            $ret[$nit]['numeroDoc'][$key] = $keyDoc;
            $ret[$nit]['valor'][$movi->getDebCre()] += $movi->getValor();

            unset($movi);
        }
        unset($moviObj, $nit);

        return $ret;
    }

    /**
    * Busca los pagos de un periodo
    * @param $periodo int 201311
    * @param $nit int 12131321321
    * @retrun array
    */
    public static function getPagosPeriodoXSocioExists($periodo, $nit)
    {
        if (!$periodo) {
            throw new SociosException("debe indicar el periodo a consultar pagos");
        }

        if (!$nit) {
            throw new SociosException("debe indicar el nit a consultar pagos");
        }
        $tercero = BackCacher::getTercero($nit);
        if (!$tercero) {
            throw new SociosException("el nit '$nit' no existe en terceros");
        }

        $ret = array();
        $year = substr($periodo, 0, 4);
        $month = substr($periodo, 4, 2);

        //comprobante de pagos
        $comprobsPagos = Settings::get('comprobs_pagos', 'SO');
        if (!$comprobsPagos) {
            throw new SociosException("Es necesario dar los comprobantes de pagos en configuración");
        }

        $comprobsPagosA = explode(',', $comprobsPagos);
        $comprobsPagosAs = implode("','", $comprobsPagosA);

        //Cuenta de saldo a favor
        $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
        if (!$cuentaSaldoAFavor) {
            throw new SociosException("No se ha configurado la cuenta de saldo a favor de socios");
        }

        $cuentasAjsEstadoCuenta = SociosCore::getCuentaAjusteEstadoCuenta();

        $conditions = "MONTH(fecha)='$month' AND YEAR(fecha)='$year' AND comprob IN('".$comprobsPagosAs."') AND cuenta LIKE '".$cuentasAjsEstadoCuenta."' OR cuenta = '$cuentaSaldoAFavor' AND nit='$nit'";
        
        $ret = EntityManager::get('Movi')->exists($conditions);

        return $ret;
    }

    /**
    * Busca los pagos de un periodo
    * @patam $periodo int 201311
    * @retrun array
    */
    public static function getAjustesPeriodo($periodo)
    {
        if (!$periodo) {
            throw new SociosException("debe indicar el periodo a consultar pagos");
        }

        $ret = array();
        $year = substr($periodo, 0, 4);
        $month = substr($periodo, 4, 2);

        //Comprobantes de ajustes y nc
        $comprobAjustes = SociosCore::getSettingsValue('comprob_ajustes', 'SO');
        $comprobNc = SociosCore::getSettingsValue('comprob_nc', 'SO');


        $comprobantes = '';
        if ($comprobAjustes) {
            $comprobantes .= "'$comprobAjustes'";
        }

        if ($comprobNc) {
            $comprobNcStr = implode("','", explode(",", $comprobNc));
            $comprobantes .= ",'$comprobNcStr'";
        }

        //Cuenta de saldo a favor
        $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
        if (!$cuentaSaldoAFavor) {
            throw new SociosException("No se ha configurado la cuenta de saldo a favor de socios");
        }
        $cuentasAjsEstadoCuenta = SociosCore::getCuentaAjusteEstadoCuenta();

        $conditions = "MONTH(fecha)='$month' AND YEAR(fecha)='$year' AND comprob IN($comprobantes) AND (cuenta LIKE '".$cuentasAjsEstadoCuenta."' OR cuenta = '$cuentaSaldoAFavor')";
        //throw new SociosException($conditions);
        
        $moviObj = EntityManager::get('Movi')->find(array('conditions'=>$conditions));

        foreach ($moviObj as $movi)
        {
            $nit = $movi->getNit();
            if (!isset($ret[$nit])) {
                $ret[$nit] = array(
                    'valor'=> array('D'=>0,'C'=>0), 
                    'fecha' => array(), 
                    'comprob' => array(), 
                    'numeroDoc' => array()
                );
            }

            $key = $movi->getComprob()."-".$movi->getNumero();
            $keyDoc = $movi->getTipoDoc()."-".$movi->getNumeroDoc();
            $ret[$nit]['comprob'][$key] = $key; 
            $ret[$nit]['fecha'][$key] = $movi->getFecha(); 
            $ret[$nit]['numeroDoc'][$key] = $keyDoc; 
            $ret[$nit]['valor'][$movi->getDebCre()] += $movi->getValor(); 

            unset($movi,$nit);
        }
        unset($moviObj);

        return $ret;
    }

    /**
    * Busca los pagos de un periodo
    * @patam $periodo int 201311
    * @retrun array
    */
    public static function getConsumosPeriodo($periodo)
    {
        if (!$periodo) {
            throw new SociosException("debe indicar el periodo a consultar pagos");
        }

        $ret = array();
        $year = substr($periodo, 0, 4);
        $month = substr($periodo, 4, 2);
        
        $sociosCore = new SociosCore();

        $estadoCuentaObj = EntityManager::get('EstadoCuenta')->find("month(fecha)='$month' AND year(fecha)='$year'");

        foreach ($estadoCuentaObj as $estadoCuenta) 
        {

            $socios = BackCacher::getSocios($estadoCuenta->getSociosId());

            if (!$socios) {
                continue;
            }

            $nit = $socios->getIdentificacion();

            if (!isset($ret[$nit])) {
                $ret[$nit] = array('facturas'=>array(), 'total' => 0);
            }


            ////////////////////////////////////////////////////
            // CONSUMOS DE POS DIRECTOS NO EN HOTEL5
            ////////////////////////////////////////////////////
            $facturasDirectasPos = $sociosCore->getFacturasDirectasPos($periodo, $nit);

            $totalFDP = 0;
            //Contamos consumos de Factura Directas de POS
            foreach ($facturasDirectasPos as $factura)
            {
                $numfac = $factura['prefijo_facturacion']."-".$factura['consecutivo_facturacion'];
                
                if (!isset($ret[$nit]['facturas'][$numfac])) {
                    $ret[$nit]['facturas'][$numfac] = array(
                        'fecha' => $factura['fecha'],
                        'valor' => $factura['total']
                    );
                }

                $totalFDP += $factura['total'];
                unset($factura);
            }

            $ret[$nit]['total'] += $totalFDP;

            ////////////////////////////////////////////////////
            // CONSUMOS DE POS DIRECTOS EN HOTEL5
            ////////////////////////////////////////////////////
            $facturasDirectasHotel = $sociosCore->getFacturasDirectas($periodo, $nit);

            $totalFD = 0;
            $detalleCM = 'CONSUMO MINIMO';
            
            //Contamos consumos de Factura Directas de POS
            foreach ($facturasDirectasHotel as $facturaHotel)
            {
                $numfac = $facturaHotel['prefac']."-".$facturaHotel['numfac'];
                
                if (!isset($ret[$nit]['facturas'][$numfac])) {
                    $ret[$nit]['facturas'][$numfac] = array(
                        'fecha' => $facturaHotel['fecfac'],
                        'valor' => $facturaHotel['total']
                    );
                }

                $totalFD += $facturaHotel['total'];
                unset($facturaHotel,$numfac);
            }
            unset($facturasDirectasHotel);
                
            $ret[$nit]['total'] += $totalFD;

            unset($estadoCuenta);
        }

        return $ret;
    }


    /**
    * Obtenemos el saldo anterior del estado de cuenta
    */
    public static function getSaldoAnteriorEstadoCuenta($periodo, $sociosId)
    {
        $base = 0;

        $socios = BackCacher::getSocios($sociosId);
        if (!$socios) {
            throw new SociosException("getSaldoAnteriorEstadoCuenta: El socios con id '$sociosId' no existe");
        }

        $nit = $socios->getIdentificacion();

        //Comprobantes de pagos
        $comprobsPagos = Settings::get('comprobs_pagos', 'SO');
        if (!$comprobsPagos) {
            throw new SociosException("No se han definido los comprobantes de pagos");
        }
        $comprobsPagosA = explode(',',$comprobsPagos);
        $comprobsPagosAS = "'".implode("','", $comprobsPagosA)."'";

        //TIPOS DE DOCUMENTOS
        $tipoDocSocios = Settings::get('tipo_doc', 'SO');
        if (!$tipoDocSocios) {
            throw new SociosException("No se ha definido el tipo de documento de la facturacion de socios");
        }
        $tipoDocPos = Settings::get('tipo_doc_pos', 'SO');
        if (!$tipoDocPos) {
            throw new SociosException("No se ha definido el tipo de documento de consumos de socios");
        }
        
        //Septiembre
        $periodoAntZ = SociosCore::subPeriodo($periodo,1);
        $year3 = substr($periodoAntZ, 0,4);
        $month3 = substr($periodoAntZ, 4,2);

        //Octubre
        $year2 = substr($periodo, 0,4);
        $month2 = substr($periodo, 4,2);
        $lastDay2 = Date::getLastDayOfMonth($month2, $year3);

        //Noviembre
        $periodoSiguiente = SociosCore::addPeriodo($periodo,1);
        $year = substr($periodoSiguiente, 0,4);
        $month = substr($periodoSiguiente, 4,2);
        $fechaZ = "$year-$month-30";
        $fechaY = "$year-$month-01";

        //Obtenemos saldos anterior si existe estado de cuenta del mes pasado
        $conditionsEstadoCuenta = "fecha<'$year2-$month2-01' AND socios_id='$sociosId'";
        $estadoCuenta = EntityManager::get('EstadoCuenta')->findFirst($conditionsEstadoCuenta,"order: fecha DESC");
        if (!$estadoCuenta) {
            //throw new SociosException("No existe un estado de cuenta del periodo anterior a este socios con id '$sociosId', '$periodo'");
            $base = 0;
        } else {
            $base = $estadoCuenta->getSaldoNuevo();
        }

        //throw new SociosException("EstadoCuenta: ".$base. ", ".$conditionsEstadoCuenta);

        //Buscamos factura de sostenimiento a descontar para mora
        $conditionsF = "fecha_factura='$fechaY' AND socios_id='$sociosId'";
        //throw new SociosException($conditionsF);
        
        $factura = EntityManager::get('Factura')->findFirst($conditionsF);
        if ($factura) {
            /*$detalleFacturaObj = EntityManager::get('DetalleFactura')->find("factura_id='{$factura->getId()}'");
            foreach ($detalleFacturaObj as $detalleFactura)
            {
                $desc = $detalleFactura->getDescripcion();
                if (strstr($desc,"SALDO PERIODO")) {
                    continue;
                }
                if (strstr($desc,"PUNTO DE VENTA")) {
                    continue;
                }

                $total = $detalleFactura->getValor() + $detalleFactura->getIva();
                $base -= $total;    
                unset($detalleFactura, $total, $desc);
            }
            unset($detalleFacturaObj);*/

            $total = $factura->getSalActual();
            $base -= $total;
                
        }

        //throw new SociosException($base);

        //Buscamos pagos del socios en el mes
        $pagos = SociosCore::getAllPagosPeriodoXSocio($periodoSiguiente, $sociosId);
        foreach ($pagos as $pago)
        {
            $base -= $pago['valor'];
            unset($pago);
        }
        //throw new SociosException(print_r($pagos,true));
        unset($pagos);

        //throw new SociosException($base);
        return $base;
    }

    /**
    * Obtiene el valor segun ajuste de sostenimineto por su base parametrisada
    * @param int $sociosId
    * @return array $ret Ej: ['base'=>100,'iva'=>10,'total'=>110]
    */
    public static function getAjusteSostenimiento($sociosId)
    {
        //Base porcentaje Mora
        $basePorcentaje = Settings::get('base_porc_mora_desfecha', 'SO');
        if (!$basePorcentaje) {
            throw new SociosException("No se ha configurado base de procentaje de mora de estado de cuenta");
        }
        
        $socios = BackCacher::getSocios($sociosId);
        if (!$socios) {
            throw new SociosException("getAjusteSostenimiento: El socios con id '$sociosId' no existe");
        }

        $ret = array();
        $ivaBase = 0;
        $total = 0;
        $baseValor = 0;

        if ($socios->getPorcMoraDesfecha()>0) {
            $baseValor = $basePorcentaje * $socios->getPorcMoraDesfecha() / 100;
            $ivaBase = $baseValor * 16 /100;
            $total +=  ($baseValor + $ivaBase);
        }

        $ret['base'] = LocaleMath::round($baseValor,0);
        $ret['iva']  = LocaleMath::round($ivaBase,0);
        $ret['ico']  = 0;
        $ret['total']= LocaleMath::round($total,0);

        //throw new SociosException(print_r($ret,true));
        
        return $ret;
    }

    /**
    * Obtiene un array de pagos realizados incluyendo ajustes
    * @param int $periodo
    * @param int $sociosId
    * @return array $pagos
    */
    public static function getAllPagosPeriodoXSocio($periodo, $sociosId)
    {
        $socios = BackCacher::getSocios($sociosId);
        if (!$socios) {
            throw new SociosException("No existe el socios con id '$sociosId'");
        }

        if (!$periodo) {
            throw new SociosException("Debe indicar el periodo a buscar pagos");
        }

        $year2 = substr($periodo, 0,4);
        $month2 = substr($periodo, 4,2);

        $cuentasAjsEstadoCuenta = SociosCore::getCuentaAjusteEstadoCuenta();

        $comprobAjustes = Settings::get('comprob_ajustes', 'SO');
        if (!$comprobAjustes) {
            throw new SociosException('No se ha configurado el comprobante de ajustes de facturas en configuración');
        }

        $comprobNcStr = '';
        $comprobNc = Settings::get('comprob_nc', 'SO');
        if ($comprobNc) {
            $comprobNcStr = "'".$comprobNc."'";
            $comprobNcStr = ",".str_replace(",", "','", $comprobNcStr);
        }

        //comprobante de pagos
        $comprobsPagos = Settings::get('comprobs_pagos', 'SO');
        if (!$comprobsPagos) {
            throw new SociosException("Es necesario dar los comprobantes de pagos en configuración");
        }

        $comprobsPagosA = explode(',',$comprobsPagos);
        $comprobsPagosAs = implode("','", $comprobsPagosA);

        //Cuenta de saldo a favor
        $cuentaSaldoAFavor = Settings::get('cuenta_saldo_a_favor', 'SO');
        if (!$cuentaSaldoAFavor) {
            throw new SociosException("No se ha configurado la cuenta de saldo a favor de socios");
        }

        $pagos = array();

        //ABONOS
        $conditionsAbonos = "nit='{$socios->getIdentificacion()}' AND deb_cre='C' AND month(fecha)='$month2' AND year(fecha)='$year2' AND comprob IN('$comprobsPagosAs') AND (cuenta LIKE '$cuentasAjsEstadoCuenta%' OR cuenta = '$cuentaSaldoAFavor')";
        
        //throw new SociosException($conditionsAbonos);
        
        $moviObj = EntityManager::get('Movi')->find(array(
            "conditions" => $conditionsAbonos,
            'columns' => 'deb_cre,comprob,numero,fecha,nit,cuenta,tipo_doc,numero_doc,descripcion,valor'
        ));

        foreach ($moviObj as $movi)
        {
            $pagos[] = SociosCore::modelToArray($movi);
            unset($movi);
        }
        unset($moviObj);

        return $pagos;
    }

    /**
     * Calcula la diferencia de dias entre dos fechas
     * @param  [type] $fecha1 [description]
     * @param  [type] $fecha2 [description]
     * @return [type]         [description]
     */
    public static function diffDiasFechas($fechaHoy,$fechaAnterior)
    {
        $date1 = strtotime($fechaAnterior);
        $date2 = strtotime($fechaHoy);
        $dateDiff = $date1 - $date2;
        $days = floor($dateDiff/(60*60*24));
        return abs($days);
    }

    /**
     * Obtiene los dias de una fecha a otra
     * @param  string $cartera    [description]
     * @param  string $fechaSaldo [description]
     * @return int             [description]
     */
    public static function getDays($cartera, $fechaSaldo)
    {
        $date1 = strtotime((string) $fechaSaldo);
        $date2 = strtotime((string) $cartera->getFEmision());
        $dateDiff = $date1 - $date2;
        $days = floor($dateDiff/(60*60*24));        
        return $days;
    }

    /**
    * Determina de un objeto cartera si esta a 30,60,90,120, o mas de 120 días
    * @param ActiveRecord $cartera
    * @param time $fechaSaldo
    * @return string 
    */
    public static function getCarteraTime($cartera, $fechaSaldo)
    {
        $days = SociosCore::getDays($cartera, $fechaSaldo);
        
        $index = '';
        if ($days) {
            if ($days<=30) {
                $index = '30';
            } else {
                if ($days<=60) {
                    $index = '60';
                } else {
                    if ($days<=90) {
                        $index = '90';
                    } else {
                        if ($days<=120) {
                            $index = '120';
                        } else {
                            $index = '120m';
                        }    
                    }        
                }
            }
        }

        return $index;
    }

    /**
    * Obtiene un listado de cuentas que usa los cargos fijos para cartera
    * 
    * @return array
    */
    public static function getCuentasCargosFijos()
    {
        try {
            $cuentas = array();
            $cargosFijosObj = EntityManager::get('CargosFijos')->find(array("columns"=>"cuenta_consolidar","group"=>"cuenta_consolidar"));
            foreach ($cargosFijosObj as $cargosFijos) 
            {
                $cuentas[]= $cargosFijos->getCuentaConsolidar();
            }
            
            return $cuentas;
        }
        catch(Exception $e){
            throw new SociosException($e->getMessage());
        }
        
    }

    /**
    * Obtiene el nombre del los salones que se uso en las ordenes de servicio a un folio 
    * @param int $numfol
    * @return array
    */
    public static function getAmbientesFacturaHotel($numfol=false,$cedula=false)
    {
        if (!$numfol) {
            throw new SociosException("No se ha ingresado un numero de folio");
        }

        if (!$cedula) {
            throw new SociosException("No se ha ingresado una cedula");
        }

        $ambientes = array();

        //Buscamos el valcar de hotel para el detalle de  factura
        //throw new SociosException("numfol='$numfol' AND cladoc='ORD'");
        
        $valcarObj = EntityManager::get('Valcar')->find("numfol='$numfol' AND cladoc='ORD'","group: numdoc","columns: numdoc,codcar");
        foreach ($valcarObj as $valcar)
        {
            //Buscamos ambientes que se genero facturas a folio
            $facturasPos = EntityManager::get('FacturaPos')->find("tipo='O' AND tipo_venta='H' AND consecutivo_facturacion='".$valcar->getNumdoc()."' AND cedula='$cedula'","group: salon_nombre","columns: salon_nombre");

            foreach ($facturasPos as $pos) 
            {
                $ambientes[] = $pos->salon_nombre;
                unset($pos);
            }

            if (!count($facturasPos)) {
                $cargosHotel = EntityManager::get('CargosHotel')->findFirst("codcar='{$valcar->getCodcar()}'","columns: descripcion");
                if ($cargosHotel) {
                    $ambientes[] = $cargosHotel->getDescripcion();
                }
            }

            unset($valcar,$facturasPos);    
        }

        if (!count($valcarObj)) {
            $valcar = EntityManager::get('Valcar')->findFirst("numfol='$numfol'","group: numdoc","columns: numdoc");
        
        }

        unset($valcarObj);

        return $ambientes;
    }

    /**
    * Resta meses al periodo
    */
    public static function subPeriodo($periodo,$numMonth)
    {
        $periodo = $periodo;
        $ano = substr($periodo,0,4);
        $mes = substr($periodo,4,2);
        
        $date = "$ano-$mes-01";
        $newdate = strtotime ( "-$numMonth month" , strtotime ( $date ) ) ;
        $newdate = date ( 'Ym' , $newdate );
        
        return $newdate;
    }

    /**
    * Resta meses al periodo
    */
    public static function addPeriodo($periodo,$numMonth)
    {
        $periodo = $periodo;
        $ano = substr($periodo,0,4);
        $mes = substr($periodo,4,2);
        
        $date = "$ano-$mes-01";
        $newdate = strtotime ( "+$numMonth month" , strtotime ( $date ) ) ;
        $newdate = date ( 'Ym' , $newdate );
        
        return $newdate;
    }

    /*
    * Obtiene el saldo de contabiildad actual
    */
    public static function getSaldoContab($fecha, $nit)
    {
        $saldoContab = 0;

        if (!$fecha) {
            throw new SociosException("No se ha definido la fecha limite de saldo");
        }

        if (!$nit) {
            throw new SociosException("No se ha definido el nit a buscar saldo");
        }

        $tercero = BackCacher::getTercero($nit);
        if (!$tercero) {
            throw new SociosException("No existe el tercero con nit '$nit' en contabilidad");
        }

        //cuentas a buscar
        $cuentaAjustesEstadoCuenta = self::getSettingsValue('cuenta_ajustes_estado_cuenta', 'SO');
        $cuentaSaldoAFavor = self::getSettingsValue('cuenta_saldo_a_favor', 'SO');
        $comprobFactura = self::getSettingsValue('comprob_factura', 'SO');

        //$nit = '1018406098';
        if (Date::isEarlier($fecha,'2014-02-28')) {
            //VERSION VIEJA < 2014-02-28
            $queryBase13 = "nit='$nit' AND fecha<'$fecha' AND cuenta LIKE '$cuentaAjustesEstadoCuenta'";
            $queryBase13C = "nit='$nit' AND fecha='$fecha' AND cuenta LIKE '$cuentaAjustesEstadoCuenta' AND comprob='$comprobFactura'";
            $queryBase28 = "nit='$nit' AND fecha<'$fecha' AND cuenta = '$cuentaSaldoAFavor' ";

            $sum13D = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase13." AND deb_cre='D'");
            $sum13C = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase13." AND deb_cre='C'");
            $sum13CD = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase13C." AND deb_cre='D'");
            $sum13CC = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase13C." AND deb_cre='C'");
            $sum28D = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase28." AND deb_cre='D'");
            $sum28C = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase28." AND deb_cre='C'");
        } else {
            //VERSION NUEVA > feb 28 de 2014
            $queryBase13 = "nit='$nit' AND fecha<='$fecha' AND cuenta LIKE '$cuentaAjustesEstadoCuenta'";
            $queryBase13C = "";
            $queryBase28 = "nit='$nit' AND fecha<='$fecha' AND cuenta = '$cuentaSaldoAFavor' ";

            $sum13D = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase13." AND deb_cre='D'");
            $sum13C = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase13." AND deb_cre='C'");
            $sum13CD = 0;
            $sum13CC = 0;
            $sum28D = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase28." AND deb_cre='D'");
            $sum28C = EntityManager::get('Movi')->sum('column: valor', "conditions: ".$queryBase28." AND deb_cre='C'");
        }
        
        $diff13 = $sum13D - $sum13C;
        $diff13C = $sum13CD - $sum13CC;
        $diff28 = $sum28D - $sum28C;

        $saldoContab = $diff13 + $diff13C + $diff28;
        //throw new SociosException("$saldoContab = $diff13 + $diff13C - abs($diff28);<br>".$queryBase13."<br>".$queryBase13C."<br>".$queryBase28);
        
        return $saldoContab;
    }

    /**
     * Obtiene el valor de settings y si esta vacia saca un alert
     * @param  [type] $field [description]
     * @param  string $app   [description]
     * @return [type]        [description]
     */
    public static function getSettingsValue($field, $app='SO')
    {
        if (!$field) {
            throw new SociosException("No se ha definido el campo a buscar");
        }

        $fieldValue = Settings::get($field, $app);
        if (!$fieldValue) {
            throw new SociosException("No se ha definido el campo '$field' en Configuración");
        }

        return $fieldValue;
    }

}

    function ordenarContentMovi( $a, $b ) {
        return strtotime($a['fecha']) - strtotime($b['fecha']);
    }

?>
