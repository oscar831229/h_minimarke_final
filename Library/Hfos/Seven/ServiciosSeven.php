<?php

Core::importFromLibrary('Nusoap', 'nusoap.php');

class ServiciosSeven 
{

    private $dat_inve;

    private $seven_wsdl;

    private $produccion = 'N';

    public function __construct(){

        $this->dat_inve   = new DatInve();
        $this->seven_wsdl = new SevenWsdl();

        $dat_inve = $this->dat_inve->findFirst("cons = 1");

        $this->produccion = $dat_inve->production;
    }

    public function crearTerceroSeven($tercero){

        # Instanciar cliente WS
		$wsdl = $this->getWsdl('SIeWsSec');

		$cliente = new nusoap_client($wsdl,'wsdl');
		$cliente->soap_defencoding = 'UTF-8';
		$cliente->decode_utf8      = false;

		# Capturar respuesta WS
		$resultado  = $cliente->call('SyncFaClien', $tercero);
		$xml_string = simplexml_load_string($resultado['SyncFaClienResult']);

        if(!$xml_string){
            return [
                'retorno'  => 1,
                'txterror' => 'Error en la codificacion xml, respueta servicio SIeWsSec',
                'cli_codi' => NULL,
                'dcl_codd' => NULL,
            ];
        }

        $txterror = $xml_string->FA_CLIEN->RETORNO == 0 ? '' : $xml_string->FA_CLIEN->TXTERROR;
        $cli_codi = $xml_string->FA_CLIEN->RETORNO == 0 ? $xml_string->FA_CLIEN->CLI_CODI : NULL;
        $dcl_codd = $xml_string->FA_CLIEN->RETORNO == 0 ? $xml_string->FA_CLIEN->DCL_CODD : NULL;

        return [
            'retorno'  => (int) $xml_string->FA_CLIEN->RETORNO,
            'txterror' => (string) $txterror,
            'cli_codi' => (int) $cli_codi,
            'dcl_codd' => (int) $dcl_codd,
        ];

    }

    public function getWsdl($code){

        $wsdl = $this->seven_wsdl->findFirst("code = '{$code}'");
        if(!$wsdl)
            throw new Exception("No esta parametrizado el wsdl con código {$code}", 1);

        return $this->produccion == 'S' ? $wsdl->wsdl_production : $wsdl->wsdl_test;
            
    }

}