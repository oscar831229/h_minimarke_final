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

Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
Core::importFromLibrary('Hfos/Socios', 'SociosReports.php');

/**
 * Estado_Cuenta_ConsolidadoController
 *
 * Controlador de generacion de estados de cuenta por socios consolidado
 *
 */
class Estado_Cuenta_ConsolidadoController extends ApplicationController
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
        $periodoStr = SociosCore::getCurrentPeriodo();
        
        $fechas = EntityManager::get('EstadoCuenta')->find(array('columns'=>'fecha', 'group'=>'fecha', 'order'=>'fecha DESC'));
        $this->setParamToView('fechas', $fechas);

        $this->setParamToView('mes', $periodoStr);
        $this->setParamToView('message', 'De click en Imprimir Estado de Cuenta Consolidado');
    }
    
    /**
     * Metodo que genera la(s) factura(s)
     *
     */
    public function reporteAction($fecha = false, $reportType = false)
    {
        $this->setResponse('json');
        
        try {

            $transaction = TransactionManager::getUserTransaction();
            
            $sociosId = $this->getPostParam('sociosId', 'int');
            if (!$fecha) {
                $fecha = $this->getPostParam('fecha', 'date');
                $reportType = $this->getPostParam('reportType', 'alpha');
            }
            $dateFecha = new Date($fecha);
            
            $config = array(
                'reportType' => $reportType,
                'fecha' => $fecha
            );
            
            //Generamos factura
            $sociosReports = new SociosReports();
            $config['file'] = $sociosReports->estadoCuentaConsolidado($config);

            if (isset($config['file']) && $config['file']==false) {
                throw new Exception("No hay datos a mostrar, debe generar el estado de cuenta primero con la fecha '$fecha'");
            }

            return array(
                'status'    => 'OK',
                'message'   => 'El estado de cuenta consolidado fue generado correctamente',
                'file'      => 'public/temp/'.$config['file']
            );

        } catch (Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage().", trace:".print_r($e, true)
            );
        }
    }
}
