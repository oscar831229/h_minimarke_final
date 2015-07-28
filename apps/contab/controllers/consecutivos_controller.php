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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * ConsecutivosController
 *
 * Control de Consecutivos
 *
 */
class ConsecutivosController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{

		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addDays(1);

		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		$this->setParamToView('comprobs', $this->Comprob->find('order: nom_comprob'));

		$this->setParamToView('message', 'Usando este modulo puede consultar las inconsistencias del movimiento');
	}

	public function generarAction()
	{

		$this->setResponse('json');
		try {
			$codigoComprob = $this->getPostParam('codigoComprobante', 'comprob');

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			$numeroInicial = $this->getPostParam('numeroInicial', 'int');
			$numeroFinal = $this->getPostParam('numeroFinal', 'int');

			$conditions = array();
			if ($codigoComprob != '') {
				$conditions[] = "comprob='$codigoComprob'";
			}
			if ($numeroInicial > 0) {
				$conditions[] = "numero>='$numeroInicial' AND numero<='$numeroFinal'";
			}
			if ($fechaInicial != '') {
				$conditions[] = "fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
			}

			if (count($conditions)) {
				$movis = $this->Movi->find(array(join(' AND ', $conditions), 'order' => 'comprob, numero', 'columns' => 'comprob,numero', 'group' => 'comprob,numero'));
			} else {
				$movis = $this->Movi->find(array('order' => 'comprob, numero', 'columns' => 'comprob,numero', 'group' => 'comprob,numero'));
			}

			$soloDescuadrados = $this->getPostParam('soloDescuadrados', 'alpha');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('VALIDACIÓN INTEGRIDAD DEL MOVIMIENTO', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Validación Integridad del Movimiento');
			$report->setColumnHeaders(array(
				'COMPROBANTE',
				'NÚMERO',
				'NOVEDAD'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(1, 2, 3), new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->start(true);

			$lastComprob = "";
			$lastNumero = 0;
			foreach ($movis as $movi) {

				if ($lastComprob!='') {
					if ($lastComprob == $movi->getComprob()) {
						if ($movi->getNumero() != ($lastNumero + 1)) {
							$comprob = BackCacher::getComprob($movi->getComprob());
							$report->addRow(array(
								$movi->getComprob() . '/' . $comprob->getNomComprob(),
								$lastNumero + 1,
								'Falta el consecutivo ' . ($lastNumero + 1)
							));
						}
					}
				}

				try {
					$messages = Aura::validateComprob($movi->getComprob(), $movi->getNumero());
					if (count($messages)) {
						foreach ($messages as $message) {
							$comprob = BackCacher::getComprob($movi->getComprob());
							$report->addRow(array(
								$movi->getComprob() . '/' . $comprob->getNomComprob(),
								$movi->getNumero(),
								$message
							));
						}
					} else {
						if ($soloDescuadrados == 'N') {
							$comprob = BackCacher::getComprob($movi->getComprob());
							$report->addRow(array(
								$movi->getComprob() . '/' . $comprob->getNomComprob(),
								$movi->getNumero(),
								'Ninguna'
							));
						}
					}
				} catch (AuraException $e) {
					$comprob = BackCacher::getComprob($movi->getComprob());
					$report->addRow(array(
						$movi->getComprob().'/'.$comprob->getNomComprob(),
						$movi->getNumero(),
						$e->getMessage()
					));
				}
				$lastComprob = $movi->getComprob();
				$lastNumero = $movi->getNumero();
				unset($movi);
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/control');

			return array(
				'status' => 'OK',
				'file' => 'temp/' . $fileName
			);

		} catch (Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}
