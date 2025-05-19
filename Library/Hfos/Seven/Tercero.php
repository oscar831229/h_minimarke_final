<?php

class Tercero
{
    private $connection;

    private $homologo_tipdoc = [];

    private $parametros_staticos;
    
    public function __construct()
    {

        $this->clientes     = new Clientes;
        $this->tipdoc       = new tipdoc;
        $this->param_tipdoc = new ParamTipdoc;
        $this->terceroseven = new TercerosSeven;

        $this->parametros_staticos = [
            'emp_codi' => 376,
            'mod_codi' => '0' ,
			'pai_codi' => '169',
			'dep_codi' => '68',
			'mun_codi' => '001',
            'tcl_codi' => '0',
			'cal_codi' => '0',
			'coc_codi' => '2',
			'cim_codi' => '250',
			'arb_csuc' => '0105',
            'dcl_nfax' => '6076434444',
			'arb_clte' => '0' ,
			'cli_inna' => 'S' ,
			'ven_codi' => '1',
			'arb_ccec' => '0' ,
			'lis_codi' => '0',
            'dcl_apar' => '.',
			'dcl_cloc' => '.',
			'cli_anex' => '.',
			'cli_hpra' => '.',
			'cli_fecm' => '0',
			'cli_feca' => '0',
			'cli_audp' => 'N'
        ];
    }

    public function existeSevenTercero($cedula){
        $terceroseven = new TercerosSeven;
        return $terceroseven->findFirst("ter_coda = '$cedula' AND retorno = 0");
    }

    public function getStructuraTercero($id)
    {

        $cliente  = $this->clientes->findFirst("cedula = '$id'");

        if(!$cliente)
            throw new Exception("Cliente con número de identificación {$id} no existe.", 1);

        if(isset($this->homologo_tipdoc[$cliente->tipdoc])){
            $tip_codi = $this->homologo_tipdoc[$cliente->tipdoc];
        }else{
            $homologo = $this->param_tipdoc->findFirst("tipdoc = '$cliente->tipdoc'");
            if(!$homologo){
                throw new Exception("Homologación tipo documento identidad no parametrizado hotel5.param_tipdoc, ({$cliente->tipdoc})", 1);
            }
            $this->homologo_tipdoc[$cliente->tipdoc] = $homologo->tipcodi;
            $tip_codi = $this->homologo_tipdoc[$cliente->tipdoc];
        }

        $digito_verificacion = self::obtenerDigitoVerificacion($cliente->cedula);
		$digito_verificacion = $digito_verificacion == false || $digito_verificacion == '' ? 0 : $digito_verificacion;
        
        $nombres   = $cliente->primer_nombre.' '.$cliente->segundo_nombre;
        $apellidos = $cliente->primer_apellido.' '.$cliente->segundo_apellido;

        $observacion =  $cliente->observacion;
        if ($observacion == null ){
            $observacion='.';
        }

        $data = array_merge($this->parametros_staticos,[
            'tip_codi' => $tip_codi,
            'ter_coda' => $cliente->cedula,
			'cli_dive' => $digito_verificacion,
			'cli_nomb' => trim($nombres),
			'cli_apel' => trim($apellidos),
			'cli_noco' => trim($nombres),
			'cli_coda' => $cliente->cedula,
			'dcl_dire' => trim($cliente->direccion),
			'dcl_ntel' => trim($cliente->telefono1),
			'dcl_mail' => trim($cliente->email),
			'dcl_obse' => $observacion ,
        ]);

        ksort($data);

        return $data;

    }


    public function crearProcesoTercero($tercero){
        $terceroseven = new TercerosSeven;
        $cliente = $terceroseven->findFirst("ter_coda = '{$tercero['ter_coda']}'");
        if(!$cliente){
            $nuevo = new TercerosSeven;
            foreach ($tercero as $key => $value) {
                $nuevo->$key = $value;
            }
            if($nuevo->save()==false){
				foreach($nuevo->getMessages() as $message){
					throw new Exception('Tercero:'.$message->getMessage().' '.print_r($tercero, true));
				}
			}
        }else{

            foreach ($tercero as $key => $value) {
                $cliente->$key = $value;
            }

            if($cliente->save()==false){
				foreach($cliente->getMessages() as $message){
					throw new Exception('Tercero:'.$message->getMessage().' '.print_r($tercero, true));
				}
			}
        }
    }


    public static function obtenerDigitoVerificacion($documento){

        if (!is_numeric($documento)) {
            return false;
        }
     
        $arr = [
            1  => 3, 
            4  => 17, 
            7  => 29, 
            10 => 43, 
            13 => 59, 
            2  => 7, 
            5  => 19, 
            8  => 37, 
            11 => 47, 
            14 => 67, 
            3  => 13, 
            6  => 23, 
            9  => 41, 
            12 => 53, 
            15 => 71
        ];

        $x = 0;
        $y = 0;
        $z = strlen($documento);
        $dv = '';
        
        for ($i=0; $i<$z; $i++) {
            $y = substr($documento, $i, 1);
            $x += ($y*$arr[$z-$i]);
        }
        
        $y = $x%11;
        
        if ($y > 1) {
            $dv = 11-$y;
            return $dv;
        } else {
            $dv = $y;
            return $dv;
        }

    }

    public function getInfoById($cedula){

        $cliente  = $this->clientes->findFirst("cedula = '$cedula'");

        # Validamos si ha sido intefazado
        $terceroseven = $this->terceroseven->findFirst("ter_coda = '$cedula'");
        $estado  = !$terceroseven ? 'SIN ENVIAR' : ($terceroseven->retorno == 0 ? 'ACEPTADO' : 'RECHAZADO');
        $txterror = $terceroseven && $terceroseven->retorno == 1 ? $terceroseven->txterror : '';

        if(!$cliente){
            return [
                'tipo_documento'   => '',
                'numero_documento' => $cedula,
                'primer_nombre'    => '',
                'segundo_nombre'   => '',
                'primer_apellido'  => '',
                'segundo_apellido' => '',
                'estado'           => $estado,
                'txterror'         => $txterror
            ];
        }

        # Tipo documento    
        $tipo_documento = $this->tipdoc->findFirst("tipdoc = '$cliente->tipdoc'");

        return [
            'tipo_documento'   => $tipo_documento->detalle,
            'numero_documento' => $cliente->cedula,
            'primer_nombre'    => $cliente->primer_nombre,
            'segundo_nombre'   => $cliente->segundo_nombre,
            'primer_apellido'  => $cliente->primer_apellido,
            'segundo_apellido' => $cliente->segundo_apellido,
            'estado'           => $estado,
            'txterror'         => $txterror
        ];

    }

} 
