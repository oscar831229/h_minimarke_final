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

class SalonController extends StandardForm
{

	public $scaffold = true;
	public $afterUpdate = 'crearMesas';
	public $afterInsert = 'crearMesas';

	protected function crearMesas()
	{
		$ventaA = $this->getPostParam('fl_venta_a');
		if($ventaA=='A'){
			$salon_id = $this->getRequestParam('fl_id');
			if(!$this->SalonMesas->count("salon_id='{$salon_id}' AND estado<>'N'")){
				$salonm = new SalonMesas();
				$salonm->salon_id = $salon_id;
				$salonm->vpos = 1;
				$salonm->hpos = 1;
				$salonm->numero = 1;
				$salonm->estado = 'N';
				if(!$salonm->save()){
					foreach($salonm->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
			} else {
				Flash::error('No se puede configurar el ambiente como autoservicio, porque hay cuentas abiertas');
			}
		} else {
			if($ventaA=='H'){
				$salonId = $this->getRequestParam('fl_id', 'int');
				try {
					$transaction = TransactionManager::getUserTransaction();
					$salon = $this->Salon->findFirst($salonId);
					if($salon==false){
						Flash::error("No existe el ambiente $salonId");
						$transaction->rollback();
					}
					$this->SalonMesas->setTransaction($transaction);
					if(!$this->SalonMesas->count("salon_id='{$salonId}' AND estado<>'N'")){
						$h = 0;
						$p = 0;
						$pOffset = 0;
						$this->SalonMesas->delete("salon_id='{$salonId}'");
						foreach ($this->Habitaciones->find(array('order' => 'piso, id')) as $habitacion) {
							if ($habitacion->piso) {
								if ($habitacion->piso>$salon->alto_mesas) {
									Flash::error('El alto del ambiente es insuficiente para crear todas las habitaciones');
								}
								if ($p != $habitacion->piso) {
									$p = $habitacion->piso;
									$h = 0;
								}
							}
							$salonm = new SalonMesas();
							$salonm->setTransaction($transaction);
							$salonm->salon_id = $salonId;
							$salonm->vpos = ($p-1)+$pOffset;
							$salonm->hpos = $h;
							$salonm->numero = $habitacion->id;
							$salonm->estado = 'N';
							if($salonm->save()==false){
								foreach($salonm->getMessages() as $message){
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
							$h++;
							if($h>$salon->ancho_mesas){
								$h = 0;
								$pOffset++;
							}
						}
						$transaction->commit();
					} else {
						Flash::error('No se pudieron crear las mesas en el ambiente, porque hay cuentas abiertas');
					}
				} catch (TransactionFailed $e) {
					Flash::error($e->getMessage());
					return false;
				}
			}
		}
	}

	public function initialize(){

		$this->setTemplateAfter('admin_menu');
		$this->setFormCaption('Datos de Ambientes');
		$this->setCaption('usuarios_hotel_id', 'Usuario Interfaz Recepción');
		$this->setCaption('autorizacion', 'Resolución Facturación');
		$this->setCaption('prefijo_facturacion', 'Prefijo Facturación');
		$this->setCaption('fecha_autorizacion', 'Fecha Autorización');
		$this->setCaption('consecutivo_facturacion', 'Consecutivo Facturación');
		$this->setCaption('conceptos_id', 'Concepto Propina Recepción');
		$this->setCaption('propina_automatica', 'Propina Automática');
		$this->setCaption('cierre_pedidos', 'Controlar Pedidos Activos al Cerrar Día');
		$this->setCaption('texto_impresion', 'Texto Impresión');

		$this->setTypeTextarea('texto_propina');
		$this->setTypeTextarea('texto_impresion');

		$this->notBrowse('fecha_autorizacion', 'propina_automatica', 'conceptos_id', 'autorizacion',
		'usuarios_hotel_id', 'tipo_comanda', 'leyenda_propina', 'centro_costo',
		'consecutivo_inicial', 'consecutivo_final', 'consecutivo_facturacion', 'consecutivo_orden',
		'pide_asientos', 'pide_personas', 'texto_propina', 'descarga_online', 'ancho_mesas',
		'alto_mesas', 'porcentaje_servicio', 'consecutivo_comanda');

		$config = CoreConfig::readFromActiveApplication('app.ini');
		if(isset($config->pos->ramocol)){
			$ramocol = $config->pos->ramocol;
		} else {
			$ramocol = 'ramocol';
		}

		$this->setComboDynamic(array(
			'field' => 'centro_costo',
			'detail_field' => 'nom_centro',
			'relation' => $ramocol.'.centros',
			'column_relation' => 'codigo'
		));

		$this->setComboStatic('tipo_comanda', array(
			array('M', 'MANUAL'),
			array('A', 'AUTOMÁTICA')
		));

		$this->setComboStatic('leyenda_propina', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboStatic('propina_automatica', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboStatic('pide_asientos', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboStatic('pide_personas', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboStatic('descarga_online', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboStatic('cierre_pedidos', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboStatic('venta_a', array(
			array('C', 'CUALQUIERA'),
			array('H', 'HABITACIONES'),
			//array('P', 'PARTICULAR'),
			array('A', 'AUTOSERVICIO')
		));

		$this->setComboStatic('estado', array(
			array('A', 'ACTIVO'),
			array('I', 'INACTIVO')
		));

	}

}
