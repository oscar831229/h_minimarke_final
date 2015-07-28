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
 * Formato_ChequeController
 *
 * Permite montar los formatos de cheques
 *
 */
class Formato_ChequeController extends ApplicationController {

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
		$chequeras = array();
		foreach ($this->Chequeras->find(array('conditions'=>'estado="A"')) as $chequera )
		{
			$cuentaBancos = $chequera->getCuentasBancos();
			if ($cuentaBancos!=false) {
				$chequeras[$chequera->getId()] = $cuentaBancos->getDescripcion().' '.$chequera->getNumeroInicial().'-'.$chequera->getNumeroFinal();
			}
		}
		$this->setParamToView('message', 'Seleccione una chequera para configurar su formato de impresión');
		$this->setParamToView('chequeras', $chequeras);
	}

	public function getFormatoAction()
	{
		$this->setResponse('view');

		$chequeraId = $this->getPostParam('chequeraId', 'int');

		$formatoCheque = $this->FormatoCheque->findFirst("chequeras_id=$chequeraId");

		if($formatoCheque!=false){
			$items = array(
				'ano', 'mes', 'dia', 'valor', 'tercero', 'suma',
				'numero', 'nota', 'cuenta', 'detalle', 'debito',
				'credito', 'valor_movi', 'empresa', 'num_cheque',
				'cuenta_bancaria'
			);
			foreach($items as $item){
				Tag::displayTo($item.'X', $formatoCheque->readAttribute('p_'.$item));
				Tag::displayTo($item.'Y', $formatoCheque->readAttribute('r_'.$item));
			}
			Tag::displayTo('medida', $formatoCheque->getMedida());
		}

	}

	//vous, pouvez, faire, ce, que, vous, voulez
	public function guardarAction()
	{

		$this->setResponse('json');

		$chequeraId = $this->getPostParam('chequeraId', 'int');
		$medida = $this->getPostParam('medida', 'alpha');

		$formatoCheque = $this->FormatoCheque->findFirst("chequeras_id=$chequeraId");
		if($formatoCheque==false){
			$formatoCheque = new FormatoCheque();
			$formatoCheque->setChequerasId($chequeraId);
		}
		$formatoCheque->setMedida($medida);

		$items = array(
			'ano', 'mes', 'dia', 'valor', 'tercero', 'suma',
			'numero', 'nota', 'cuenta', 'detalle', 'debito',
			'credito', 'valor_movi', 'empresa', 'num_cheque',
			'cuenta_bancaria'
		);
		foreach ($items as $item)
		{
			$fieldName = $item;
			$formatoCheque->writeAttribute('p_'.$item, $this->getPostParam($item.'X', 'double'));
			$formatoCheque->writeAttribute('r_'.$item, $this->getPostParam($item.'Y', 'double'));
		}

		if ($formatoCheque->save()==false) {
			foreach ($formatoCheque->getMessages() as $message)
			{
				return array(
					'status' => 'FAILED',
					'message' => $message->getMessage().print_R($_POST, true)
				);
			}
		}

		return array(
			'status' => 'OK'
		);
	}

	/**
	* Copia el formato de una chequera a otra
	*/
	public function copiarAction()
	{
		$this->setResponse('json');

		$chequeraOrigenId = $this->getPostParam('chequeraOrigenId', 'int');
		$chequeraDestinoId = $this->getPostParam('chequeraDestinoId', 'int');

		if ($chequeraOrigenId && $chequeraDestinoId) {

			if ($chequeraOrigenId!=$chequeraDestinoId) {

				try
				{
					$transaction = TransactionManager::getUserTransaction();

					$chequeraOrigen = $this->FormatoCheque->setTransaction($transaction)->findFirst(array('conditions' => "chequeras_id=$chequeraOrigenId"));
					if ($chequeraOrigen==false) {
						throw new Exception("El origen no existe ($chequeraOrigenId)", true);
					}

					$chequeraDestino = $this->FormatoCheque->findFirst(array('conditions' => "chequeras_id=$chequeraDestinoId"));
					if ($chequeraDestino!=false) {
						$this->FormatoCheque->setTransaction($transaction)->delete(array('conditions' => "chequeras_id=$chequeraDestinoId"));
					}


					#Creamos neuvo registro en base a cheque origen
					$formatoCheque = new FormatoCheque();
					$formatoCheque->setTransaction($transaction);

					foreach ($formatoCheque->getAttributes() as $field)
					{
						$formatoCheque->writeAttribute($field, $chequeraOrigen->readAttribute($field));
					}
					$formatoCheque->setId(NULL);
					$formatoCheque->setChequerasId($chequeraDestinoId);

					if ($formatoCheque->save()==false) {
						foreach ($formatoCheque->getMessages() as $message)
						{
							throw new Exception($message->getMessage(), true);
						}
					} else {
						$transaction->commit();
						return array(
							'status' 	=> 'OK',
							'message' 	=> 'Se copió el formato correctamente'
						);
					}
				}
				catch (Exception $e){
					return array(
						'status' 	=> 'FAILED',
						'message' 	=> $e->getMessage()
					);
				}

			} else {
				return array(
					'status' 	=> 'FAILED',
					'message' 	=> 'El origen y destino deben ser distintos'
				);
			}

		} else {
			return array(
				'status' 	=> 'FAILED',
				'message' 	=> 'Debe seleccionar origen y destino a copiar'
			);
		}

	}

	/**
	* Muestra ventana para selccionar los formato para copiar
	*/
	public function selectFormatoAction()
	{
		$chequeras = array();
		foreach ($this->Chequeras->find() as $chequera )
		{
			$cuentaBancos = $chequera->getCuentasBancos();
			if ($cuentaBancos!=false) {
				$chequeras[$chequera->getId()] = $cuentaBancos->getDescripcion().' '.$chequera->getNumeroInicial().'-'.$chequera->getNumeroFinal();
			}
		}
		$this->setParamToView('chequeras', $chequeras);
	}
}
