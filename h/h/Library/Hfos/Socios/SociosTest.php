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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

require_once 'SociosException.php';
Core::importFromLibrary('Hfos/Socios','SociosCore.php');
/**
 * Socios
 *
 * Clase componente que controla procesos internos de Socios
 *
 */
class SociosTest extends UserComponent {

	/**
	 * Metodo principal de Test
	 */
	static function main(){
		$transaction = TransactionManager::getUserTransaction();
		//Limpiar totda la BDs
		SociosCore::limpiarBD($transaction);
		//Retorne los ActiveRecords de contratos creados
		$SociosObj = self::crearSociosMain($transaction);
		//success
		$transaction->commit();
		return true;
	}

	/**
	 * Metodo principal de creación de Socios
	 */
	static function crearSociosMain($transaction){
		$configAll = array(
			////////////////////////////
			// SOCIO1 DE PRUEBAS
			////////////////////////////
			array(
				//Numero Acción Consecutivo/Manual
				'numeroAccionManual'		=> 'N',//Consecutivo
				//Socios
				'titularId'					=> null,//Socio titular NULL es el titular
				'numeroAccion'				=> null,
				'fechaIngreso'				=> '2010-01-01',
				'parentescosId'				=> 1,//Titular con derecho
				'tipoDocumentosId'			=> 1,//Cedula
				'identificacion'			=> 1070585456,
				'apellidos'					=> 'Carvajal',
				'nombres'					=> 'Eduar',
				'ciudadExpedido'			=> 127591,
				'ciudadNacimiento'			=> 127591,
				'fechaNacimiento'			=> '1986-06-08',
				'sexo'						=> 'M',
				'direccionCasa'				=> 'Transv 127D Bis #139A-46',
				'ciudadCasa'				=> 127591,
				'telefonoCasa'				=> 6936466,
				'celular'					=> 3014114428,
				'direccionCorrespondencia'	=> 'C',
				'correo1'					=> 'carvajaldiazeduar@gmail.com',
				'tipoSociosId'				=> 1,//Decano
				'enviaCorreo'				=> 'S',
				'cobra'						=> 'S',
				'estadosSociosId'			=> 1,//Estado Activo
				//Estudios
				'estudiosInstitucion'		=> array('Uniminuto'),
				'estudiosCiudadId'			=> array(127591),
				'estudiosFechaGrado'		=> array('2002-10-10'),
				'estudiosTitulo'			=> array('Ing. Sistemas'),
				//Experiencia Laboral
				'expLaboralesEmpresa'		=> array('BHteck Inc.'),
				'expLaboralesDireccion'		=> array('zzzzzzzzzz'),
				'expLaboralesCargo'			=> array('Ing. Sistemas'),
				'expLaboralesTelefono'		=> array('1234567'),
				'expLaboralesFax'			=> array('1234456'),
				'expLaboralesFecha'			=> array('2011-10-10'),
				//Actividades
				'actividades'				=> array(48),//Solo acuaticos
				//Clubes
				'clubId'					=> array(44),
				'clubDesde'					=> array(44=>2000),
				//Cargos Periodicos
				'cargosFijosId'				=> array(2),
				//Otros Socios
				'otrosSociosId'				=> array(),
				'tipoAsociacionSocioId'		=> array(),
				//Validacion contrato
				'validarSocio'				=> array(
					'numeroAccion'	=> '1-1',//1-titular
				)
			),
		);
		//Recorre los contratos creadolos y almacenando su ActiveRecord
		$SociosObj = array();
		foreach($configAll as $config){
			//crear en contrato con base a $config
			$Socios = SociosCore::crearSocio($config, $transaction);
			//almacenamos ActiveRecords creados
			$SociosObj[]= $Socios;
		}
		return $SociosObj;
	}

	

	/**
	 * Metodo que valida los datos del socio
	 */
	static function validarSocio(&$config, $transaction){
		if(isset($config['validarSocio'])==true){
			//Validamos el numero de acción que sea igual
			if(isset($config['numeroAccion'])==true){
				$Socios = $config['Socios'];
				if($config['numeroAccion'] != $Socios->getNumeroAccion()){
					$transaction->rollback('El número de acción no es correcto. Generó('.$Socios->getNumeroAccion().') y debería ser('.$config['numeroAccion'].')');
				}
			}
		}
	}
}
