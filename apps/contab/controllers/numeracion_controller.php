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
 * NumeracionController
 *
 * Numeracion de libros oficiales
 *
 */
class NumeracionController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('message', 'Indique los parámetros de numeración y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		$prefijo = i18n::strtoupper($this->getPostParam('prefijo', 'alpha'));
		$numeroInicial = $this->getPostParam('numeroInicial', 'int');
		$numeroFinal = $this->getPostParam('numeroFinal', 'int');

		if($numeroInicial==0||$numeroFinal==0){
			return array(
				'status' => 'FAILED',
				'message' => 'Indique el número de página inicial y final'
			);
		}

		list($numeroInicial, $numeroFinal) = Utils::sortRange($numeroInicial, $numeroFinal);

		require 'Library/Mpdf/mpdf.php';
		$pdf = new mPDF();
		$pdf->SetDisplayMode('fullpage');
		$pdf->tMargin = 10;
		$pdf->lMargin = 10;

		$html = '<html><body>';
		for($i=$numeroInicial;$i<=$numeroFinal;$i++){
			$html.='<div align="right" class="numero">'.$prefijo.' '.$i.'</div>';
			if($i<$numeroFinal){
				$html.='<pagebreak />';
			}
		}
		$html.='</body></html>';
		$pdf->writeHTML($html);

		$fileName = 'numeracion-'.mt_rand(1, 1000).'.pdf';
		$pdf->Output('public/temp/'.$fileName);

		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);

	}

}