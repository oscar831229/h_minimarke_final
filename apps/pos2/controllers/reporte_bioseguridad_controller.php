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

class Reporte_BioseguridadController extends ApplicationController
{

	public $analisis;

	public function initialize()
	{
		$this->setTemplateAfter("admin_menu");
	}

	public function indexAction()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isGet()) {
			$datos = $this->Datos->findFirst();
			Tag::displayTo('fechaInicial', (string) $datos->readAttribute('fecha'));
			Tag::displayTo('fechaFinal', (string) $datos->readAttribute('fecha'));
		}
	}

	public function procesarAction()
	{
		$this->analisis = array();
		$db = DbBase::rawConnect();
		$formato = $this->getPostParam('formato', 'alpha');
		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');
		
		$Datos = $this->Datos->findFirst();
		$query ="SELECT 
				d.menus_items_id AS codigo,
				d.menus_items_nombre AS nombre,
				f.consecutivo_facturacion  AS factura,
				DATE_FORMAT(f.fecha, '%d/%m/%Y') AS fecha,
				d.cantidad AS cantidad,
				d.valor AS valor_total
				FROM factura f
				INNER JOIN detalle_factura d ON(f.prefijo_facturacion=d.prefijo_facturacion 
												AND f.consecutivo_facturacion=d.consecutivo_facturacion)
				INNER JOIN menus_items m ON (m.id=d.menus_items_id)
				WHERE f.fecha BETWEEN '{$fechaInicial}' AND '{$fechaFinal}'
				AND m.controlado=1
				AND f.estado='A'
				ORDER BY f.fecha ASC ";
		$data = $db->fetchAll($query);
		if(count($data)==0)
		{
			Flash::error("No hay registros para mostrar");
		}else{
			$report = new Report('Excel');

			$titulo = new ReportText('REPORTE DE BIOSEGURIDAD', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2), false, true);

			$report->setColumnHeaders(array(
				'SECCIONAL',
				'TIPO IDENTIFICACIÓN',
				'IDENTIFICACIÓN',
				'RAZÓN SOCIAL',
				'CODIGO PLU',
				'NOMBRE',
				'FACTURA',
				'FECHA',
				'CANTIDAD',
				'VALOR',
			));

			foreach($data as $factura)
			{
				$report->addRow(array(
					'4',
					'31',
					$Datos->getNit(),
					i18n::strtoupper($Datos->getNombreHotel()),
					$factura['codigo'],
					i18n::strtoupper($factura['nombre']),
					$factura['factura'],
					$factura['fecha'],
					$factura['cantidad'],
					Currency::number($factura['valor_total'], 0),
				));
			}

			$report->finish();

			$fileName = $report->outputToFile('public/temp/report');

			$this->setParamToView('generated', true);
			$this->setParamToView('fileName', $fileName);
		}
		
		$this->routeTo("action: index");

	}

	public function interAction()
	{
		new InterfasePOS2();
	}

}
