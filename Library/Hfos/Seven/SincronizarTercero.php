<?php

class sincronizarTercero
{
    private $tercero;

    public function __construct(Tercero $tercero)
    {
        $this->tercero   = $tercero;
        $this->factura   = new Factura;
        $this->servicios = new ServiciosSeven;
    }

    public function sincronizarById($id)
    {
        # Obtener trama de consumo
        try {

            $request_client = $this->tercero->getStructuraTercero($id);

            # Consumo del servicio seven terceros.
            $response = $this->servicios->crearTerceroSeven($request_client);

            # Registar log del proceso
            $tercero_seven = array_merge($request_client, $response);
            
        }catch (Exception $e) {
            $tercero_seven['ter_coda'] = $id;
            $tercero_seven['retorno']  = 1;
            $tercero_seven['txterror'] = $e->getMessage();
        }

        # Crear log terceros
        $this->tercero->crearProcesoTercero($tercero_seven);

    }
    

    /**
     * Sincronización fecha de facturación
     */
    public function sincronizarDateInvoice($fecha_facturacion){

        # Consultar clientes facturados
        $facturas = $this->factura->distinct(array('cedula', 'conditions' => "fecha = '$fecha_facturacion' AND tipo = 'F' AND estado <> 'B'", 'order' => 'cedula'));

        foreach ($facturas as $key => $cedula) {
            try {

                # Valida si ya esta creado en seven
                $sincronizado = $this->tercero->existeSevenTercero($cedula);

                if(!$sincronizado){
                    $cliente_request = $this->sincronizarById($cedula);
                }
                
            }catch (Exception $e) {
                echo "Error: " . $e->getMessage(). ' linea '.$e->getLine();
            }

        }

    }

    public function getInfoTerceros($fecha_facturacion){

        $facturas = $this->factura->distinct(array('cedula', 'conditions' => "fecha = '$fecha_facturacion' AND tipo = 'F' AND estado <> 'B'", 'order' => 'cedula'));
        $clientes = [];
        foreach ($facturas as $key => $cedula) {
            $clientes[] = $this->tercero->getInfoById($cedula);
        }

        return $clientes;

    }
}