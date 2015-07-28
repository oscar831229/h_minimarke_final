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
 * @copyright   BH-TECK Inc. 2009-2010
 * @version     $Id$
 */

/**
 * Asignacion_Cargos_GrupoController
 *
 * Controlador del asignacion de cargos a un tipo de socios
 *
 */
class Asignacion_Cargos_GrupoController extends ApplicationController
{

    public function initialize()
    {
        $controllerRequest = ControllerRequest::getInstance();
        if ($controllerRequest->isAjax()) {
            View::setRenderLevel(View::LEVEL_LAYOUT);
        }
    }

    
    /**
     * Vista principal
     *
     */
    public function indexAction()
    {
        
        $this->setParamToView('message', 'Seleccione el tipo de socio para asignar a todos un cargo fijo');
    }

    /**
     * Cambia el numero de accion de un socio
     *
     * 
     */
    public function asignarAction()
    {
        $this->setResponse('json');

        $tipo = $this->getPostParam('tipo');
        $tipoSociosId = $this->getPostParam('tipo_socios_id', 'int');
        $cargosFijoId = $this->getPostParam('cargos_fijos_id', 'int');
        
        try
        {
            if (!$cargosFijoId) {
                return array(
                    'status' => 'FAILED',
                    'message' => 'El cargo fijo es necesario'
                );
            }
            
            $sociosArray = array();
            $socios = new Socios();
            
            $conditions = '';
            if ($tipoSociosId && $tipoSociosId!='@') {
               $conditions = 'tipo_socios_id=' . $tipoSociosId;
            }
                        
            
            if (!$tipo || $tipo=='@') {
                return array(
                    'status' => 'FAILED',
                    'message' => 'El tipo es necesario'
                );
            } else {
                    
                if ($conditions) {
                    $sociosiObj = $socios->find($conditions);
                } else {
                    $sociosiObj = $socios->find();
                }
                
                switch ($tipo) {
                    case 'N'://Si no tiene cargos fijos asignados
                        foreach ($sociosiObj as $sociosTemp) {
                            $asignacionCargos = EntityManager::get('AsignacionCargos')->findFirst(array("socios_id='{$sociosTemp->getSociosId()}'", 'columns'=>'id'));
                            if ($asignacionCargos==false) {
                                $sociosArray[] = $sociosTemp;
                            }
                            
                            unset($sociosTemp, $asignacionCargos);
                        }
                        break;
                        
                    case 'C': //Si tiene cargos fijos asignados
                        foreach ($sociosiObj as $sociosTemp) {
                            $asignacionCargos = EntityManager::get('AsignacionCargos')->find("socios_id='{$sociosTemp->getSociosId()}'");
                            if (count($asignacionCargos)) {
                                $sociosArray[] = $sociosTemp;
                            }
                            
                            unset($sociosTemp, $asignacionCargos);
                        }
                        break;
                        
                    default:
                        $conditions = '1=1';
                        if ($tipoSociosId) {
                           $conditions = 'tipo_socios_id=' . $tipoSociosId;
                        }
                        $sociosArray = $socios->find($conditions);
                        break;
                }
                
            }

            unset( $conditions);
                        
            
            
            //Busamos los socios con ese tipo de socio
            if (!$sociosArray) {
                $sociosArray = array();
            }
            
            #throw new Exception(count($sociosArray), 1);
        
            $transaction = TransactionManager::getUserTransaction();

            Rcs::disable();
            
            //Recorremos esos socios
            foreach ($sociosArray as $socio) {
                
               //Miramos si el cargo fijo ya lo tiene el socio 
               $asignacionCargos = EntityManager::get('AsignacionCargos')->findFirst('socios_id=' . $socio->getSociosId() . ' AND cargos_fijos_id=' . $cargosFijoId);
               //Si no existe creamos el registro
               if (!$asignacionCargos) {
                   $asignacionCargos = new AsignacionCargos();
                   $asignacionCargos->setTransaction($transaction);
                   $asignacionCargos->setSociosId($socio->getSociosId());
                   $asignacionCargos->setCargosFijosId($cargosFijoId);
                   $asignacionCargos->setEstado('A');
                   if ($asignacionCargos->save()==false) {
                        foreach ($asignacionCargos->getMessages() as $message) {
                            $transaction->rollback('Asignacion Cargos Grupo: '.$message->getMessage());
                        }
                    }
               }
               
               unset($asignacionCargos, $socio);
            }
            
            new EventLogger('Se asigo el cargo fijo '.$cargosFijoId.' al tipo de socio '.$tipoSociosId, 'A', $transaction);
            $transaction->commit();
            
            return array(
                'status' => 'OK',
                'message' => 'Se asigno el cargo fijo a todos los socios de ese tipo exitosamente '.count($sociosArray)
            );
        }
        catch(Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
               
        
    }

    /**
     * Cambia el numero de accion de un socio
     *
     * 
     */
    public function borrarAction()
    {
        $this->setResponse('json');

        $tipo = $this->getPostParam('tipo');
        $tipoSociosId = $this->getPostParam('tipo_socios_id','int');
        $cargosFijoId = $this->getPostParam('cargos_fijos_id','int');
        
        try {
            if (!$cargosFijoId || $cargosFijoId=='@') {
                return array(
                    'status' => 'FAILED',
                    'message' => 'El cargo fijo es necesario'
                );
            }
            
            $sociosArray = array();
            $socios = new Socios();
            
            $conditions = '';
            if ($tipoSociosId && $tipoSociosId!='@') {
               $conditions = 'tipo_socios_id='.$tipoSociosId;
            }
                        
            
            if (!$tipo || $tipo=='@') {
                return array(
                    'status' => 'FAILED',
                    'message' => 'El tipo es necesario'
                );
            } else {
                    
                if ($conditions) {
                    $sociosiObj = $socios->find($conditions);
                } else {
                    $sociosiObj = $socios->find();
                }
                
                switch ($tipo) {
                    case 'N'://Si no tiene cargos fijos asignados
                        foreach ($sociosiObj as $sociosTemp) {
                            $asignacionCargos = EntityManager::get('AsignacionCargos')->findFirst(array("socios_id='{$sociosTemp->getSociosId()}'", 'columns'=>'id'));
                            if ($asignacionCargos==false) {
                                $sociosArray[] = $sociosTemp;
                            }
                            
                            unset($sociosTemp, $asignacionCargos);
                        }
                        break;
                        
                    case 'C': //Si tiene cargos fijos asignados
                        foreach ($sociosiObj as $sociosTemp) {
                            $asignacionCargos = EntityManager::get('AsignacionCargos')->find("socios_id='{$sociosTemp->getSociosId()}'");
                            if (count($asignacionCargos)) {
                                $sociosArray[] = $sociosTemp;
                            }
                            
                            unset($sociosTemp, $asignacionCargos);
                        }
                        break;
                        
                    default:
                        $conditions = '1=1';
                        if ($tipoSociosId) {
                           $conditions = 'tipo_socios_id='.$tipoSociosId;
                        }
                        $sociosArray = $socios->find($conditions);
                        break;
                }
                
            }

            unset( $conditions);
                        
            
            
            //Busamos los socios con ese tipo de socio
            if (!$sociosArray) {
                $sociosArray = array();
            }
            
            #throw new Exception(count($sociosArray), 1);
        
            $transaction = TransactionManager::getUserTransaction();

            //Rcs::disable();
            
            //Recorremos esos socios
            foreach ($sociosArray as $socio)
            {
                
               //Miramos si el cargo fijo ya lo tiene el socio 
               $asignacionCargos = EntityManager::get('AsignacionCargos')->deleteAll('socios_id='.$socio->getSociosId().' AND cargos_fijos_id='.$cargosFijoId);               
               unset($asignacionCargos);
            }
            new EventLogger('Se borro el cargo fijo '.$cargosFijoId.' al tipo de socio '.$tipoSociosId, 'A', $transaction);
            $transaction->commit();
            
            return array(
                'status' => 'OK',
                'message' => 'Se borro el cargo fijo a todos los socios de ese tipo exitosamente '.count($sociosArray)
            );
        }
        catch(Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
               
        
    }

}
