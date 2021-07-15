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

class Reporte_RecetaconsolidadoController extends ApplicationController
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

		$report = new Report('Excel');

		$titulo = new ReportText('REPORTE DE RECETA/CONSOLIDADO', array(
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

		$report->setDocumentTitle('Balance de Comprobación');

		$report->setColumnHeaders(array(
            'NO. RECETA',
            'NOMBRE',
            'NO. PERSONAS',
            'COSTO',
            'PRECIO',
            'CANTIDAD',
		));
		
		$query="SELECT * FROM menus_items WHERE tipo_costo='R'";
		$data = $db->fetchAll($query);
		foreach($data as $item)
		{
			$cantidad=0;
			$nombre=0;
			$fac = "SELECT 
						m.nombre AS nombre,
						m.id AS codigo,
						SUM(d.cantidad) AS cantidad
					FROM factura f 
					INNER JOIN detalle_factura d ON(f.prefijo_facturacion=d.prefijo_facturacion 
													AND f.consecutivo_facturacion=d.consecutivo_facturacion)
					INNER JOIN menus_items m ON (m.id=d.menus_items_id)
					WHERE f.fecha BETWEEN '{$fechaInicial}' AND '{$fechaFinal}' AND d.menus_items_id='{$item['codigo_referencia']}'
					GROUP BY m.id, m.nombre";
			$factura = $db->fetchAll($fac);
			if(count($factura)!=0)
			{
				$nombre = $factura[0]['nombre'];
				$cantidad = $factura[0]['cantidad'];
			}
			
			$report->addRow(array(
				$item['codigo_referencia'],
				$nombre,
				'1',
				$item['costo'],
				$item['valor'],
				Currency::number($cantidad, 0),
			));	
		}

		$report->finish();

		$fileName = $report->outputToFile('public/temp/report');

		$this->setParamToView('generated', true);
		$this->setParamToView('fileName', $fileName);

		$this->routeTo("action: index");

	}

	public function interAction()
	{
		new InterfasePOS2();
	}

}