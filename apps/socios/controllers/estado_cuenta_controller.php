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
Core::importFromLibrary('Hfos/Socios', 'SociosEstadoCuenta.php');

/**
 * Estado_CuentaController
 *
 * Controlador de generacion de estados de cuenta por socio
 *
 */
class Estado_CuentaController extends ApplicationController
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

        $periodo = SociosCore::getCurrentPeriodo();
        $ano = substr($periodo, 0, 4);
        $mes = substr($periodo, 4, 2);

        $fechaIniStr = "$ano-$mes-01";
        $fechaIniDate = new Date($fechaIniStr);
        $fechaIniDate->toLastDayOfMonth();

        $fechaFinDate = clone $fechaIniDate;
        $fechaFinDate->addDays(15);

        Tag::displayTo("fechaIni", $fechaIniDate->getDate());
        Tag::displayTo("fechaFin", $fechaFinDate->getDate());

        $this->setParamToView('mes', $periodo);
        $this->setParamToView('message', 'De click en Imprimir Estado de Cuenta');
    }

    /**
     * Metodo que genera los estados de cuenta
     *
     */
    public function generarAction($reemplazar = true)
    {
        $this->setResponse('json');

        try {

            $transaction = TransactionManager::getUserTransaction();

            $sociosId = $this->getPostParam('sociosId', 'int');
            $fechaIni = $this->getPostParam('fechaIni', 'date');
            $fechaFin = $this->getPostParam('fechaFin', 'date');

            $fechaIniDate = new Date($fechaIni);
            $periodo = $fechaIniDate->getPeriod();

            //$reportType = $this->getPostParam('reportType', 'alpha');
            $reportType = 'pdf';

            $config = array(
                'reportType'=> $reportType,
                'sociosId'  => $sociosId,
                'periodo'   => $periodo,
                'fechaIni'  => $fechaIni,
                'fechaFin'  => $fechaFin,
                'reemplaza' => $reemplazar
            );

            if (isset($sociosId) && $sociosId) {
                $config['showDebug'] = true;
            }

            //Generamos factura
            $sociosEstadoCuenta = new SociosEstadoCuenta();
            $sociosEstadoCuenta->estadoCuenta($config);

            return array(
                'status'  => 'OK',
                'message' => 'El estado de cuenta fue generado correctamente'
            );

        } catch (SociosException $e) {
            return array(
                'status'  => 'FAILED',
                'message' => $e->getMessage()
            );
        } catch (Exception $e) {
            return array(
                'status'  => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Metodo que imprime los estado de cuenta
     *
     */
    public function reporteAction()
    {
        $this->setResponse('json');

        try {

            $transaction = TransactionManager::getUserTransaction();

            $sociosId = $this->getPostParam('sociosId', 'int');
            $fechaIni = $this->getPostParam('fechaIni', 'date');
            $fechaFin = $this->getPostParam('fechaFin', 'date');

            $fechaIniDate = new Date($fechaIni);
            $periodo = $fechaIniDate->getPeriod();

            //$reportType = $this->getPostParam('reportType', 'alpha');
            $reportType = 'pdf';

            $config = array(
                'reportType'=> $reportType,
                'sociosId'  => $sociosId,
                'periodo'   => $periodo,
                'fechaIni'  => $fechaIni,
                'fechaFin'  => $fechaFin
            );

            if (isset($sociosId) && $sociosId) {
                $config['showDebug'] = true;
            }

            //Generamos factura
            $sociosReports = new SociosReports();
            $sociosReports->printEstadoCuenta($config);

            if (isset($config['file']) && $config['file']==false) {
                throw new Exception("No hay datos a mostrar", 1);
            }

            return array(
                'status'    => 'OK',
                'message'   => 'El PDF del estado de cuenta fue generado correctamente',
                'file'      => $config['file']
            );

        } catch (SociosException $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        } catch (Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage()
            );
        }
    }

    /**
    * Verifica si existe estados de cuenta en rano de fechas y socios
    */
    public function verificarAction()
    {
        $this->setResponse('json');

        try {

            $transaction = TransactionManager::getUserTransaction();

            $sociosId = $this->getPostParam('sociosId', 'int');
            $fechaIni = $this->getPostParam('fechaIni', 'date');
            $fechaFin = $this->getPostParam('fechaFin', 'date');

            $query = "fecha>='$fechaIni' AND fecha<='$fechaFin'";

            if ($sociosId>0) {
                $query .= " AND socios_id='$sociosId'";
            }

            $estadoCuenta = EntityManager::get('EstadoCuenta')->find($query);

            return array(
                'status'    => 'OK',
                'message'   => 'Se contro los siguientes estados de cuenta',
                'count'     => count($estadoCuenta)
            );

        } catch (SociosException $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage().$e->getTrace()
            );
        } catch (Exception $e) {
            return array(
                'status' => 'FAILED',
                'message' => $e->getMessage().$e->getTrace()
            );
        }
    }

    public function sendAction()
    {

    }
}
