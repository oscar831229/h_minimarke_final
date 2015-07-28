<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author      BH-TECK Inc. 2009-2010
 * @version     $Id$
 */

/**
 * SettingsController
 *
 *
 */
class SettingsController extends ApplicationController
{

    private static $_settings = array(
        'CO' => array(
            'd_movi_limite' => array(
                'type' => 'int',
                'description' => 'Límite de días que puede tener en el futuro un comprobante',
                'filters' => array('int')
            ),
            'comprob_cierre' => array(
                'type' => 'comprob',
                'description' => 'Comprobante de Cierre',
                'filters' => array('comprob')
            ),
            'comprob_entactivo' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Entradas Activos',
                'filters' => array('comprob')
            ),
            'comprob_deprec' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Depreciación',
                'filters' => array('comprob')
            ),
            'comprob_amortiz' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Causación Diferidos',
                'filters' => array('comprob')
            ),
            'comprob_cheque' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Contabilizacion Cheques',
                'filters' => array('comprob')
            ),
            'comprob_ordenes' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Ordenes Servicio',
                'filters' => array('comprob')
            ),
            'comprob_ventas' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Ventas',
                'filters' => array('comprob')
            ),
            'comprob_ingresos' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Ingresos',
                'filters' => array('comprob')
            ),
            'comprob_rc'        => array(
                'description'   => 'Comprobante de recibo de caja',
                'type'          => 'comprob',
                'filters'       => array('alpha')
            ),
        ),
        'IN' => array(
            'd_vence' => array(
                'type' => 'int',
                'description' => 'Días Vencimiento Ordenes/Pedidos',
                'filters' => array('int')
            ),
            'hortifruticula' => array(
                'description' => 'Calcular Retención Hortifrutícula?',
                'type' => 'closed-domain',
                'values' => array(
                    'S' => 'SI',
                    'N' => 'NO'
                ),
                'filters' => array('alpha')
            ),
            'condiciones' => array(
                'description' => 'Mostrar Condiciones Organolépticas?',
                'type' => 'closed-domain',
                'values' => array(
                    'S' => 'SI',
                    'N' => 'NO'
                ),
                'filters' => array('alpha')
            ),
            'ajustes_costo' => array(
                'description' => 'Destino Descargue Ajustes?',
                'type' => 'closed-domain',
                'values' => array(
                    'E' => 'COSTO',
                    'I' => 'GASTO'
                ),
                'filters' => array('alpha')
            ),
            'criterio_puntos' => array(
                'description' => 'Criterios por Puntos?',
                'type' => 'closed-domain',
                'values' => array(
                    'S' => 'SI',
                    'N' => 'NO'
                ),
                'filters' => array('onechar')
            ),
            'base_retencion' => array(
                'type' => 'int',
                'description' => 'Base de Retención',
                'filters' => array('double')
            ),
            'porc_iva_ret' => array(
                'type' => 'int',
                'description' => 'Porcentaje de IVA Retenido',
                'filters' => array('float')
            ),
            'porc_iva_des' => array(
                'type' => 'int',
                'description' => 'Porcentaje de IVA Descontable/Simplificado',
                'filters' => array('float')
            ),
            'usar_retecompras' => array(
                'description' => 'Usar Retención de compra por tercero?',
                'type' => 'closed-domain',
                'values' => array(
                    'S' => 'SI',
                    'N' => 'NO'
                ),
                'filters' => array('onechar')
            ),
            'iva_incluido' => array(
                'description' => 'Compras IVA incluído?',
                'type' => 'closed-domain',
                'values' => array(
                    'S' => 'SI',
                    'N' => 'NO'
                ),
                'filters' => array('onechar')
            ),
            'forma_entradas' => array(
                'description'   => 'Forma Pago Entradas',
                'type'          => 'relation',
                'table'         => 'FormaPago',
                'fieldRelation' => 'codigo',
                'detail'        => 'descripcion',
                'sort'          => 'descripcion',
                'filters'       => array('alpha')
            ),
            'correo_pedidos' => array(
                'description' => 'Correo a enviar pedidos',
                'type' => 'text',
                'size' => 30,
                'maxlength' => 240,
                'filters' => array('email')
            ),
        ),
        'NO' => array(
            'valor_transporte' => array(
                'type' => 'int',
                'description' => 'Valor Subsidio Transporte',
                'filters' => array('double')
            ),
            'aportes_salud' => array(
                'type' => 'int',
                'description' => 'Porcentaje Aportes Salud Empleado',
                'filters' => array('float')
            ),
            'aportes_pension' => array(
                'type' => 'int',
                'description' => 'Porcentaje Aportes Pensión Empleado',
                'filters' => array('float')
            ),
            'valor_uvt' => array(
                'type' => 'int',
                'description' => 'Valor UVT',
                'filters' => array('double')
            ),
        ),
        'FC' => array(
            'comprob_ventas' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Ventas',
                'filters' => array('comprob')
            ),
            'comprob_anula' => array(
                'type' => 'comprob',
                'description' => 'Comprobante Anulación Ventas',
                'filters' => array('comprob')
            ),
            'almacen_venta' => array(
                'type' => 'almacen',
                'description' => 'Almacén Descarga Venta',
                'filters' => array('int')
            ),
            'consecutivo_factura' => array(
                'type' => 'consecutivos',
                'description' => 'Consecutivo Facturación',
                'filters' => array('int')
            ),
            'tipo_precio' => array(
                'description' => 'Tipo Precio',
                'type' => 'closed-domain',
                'values' => array(
                    'P' => 'PORCENTAJE SOBRE COMPRA',
                    'N' => 'PRECIO NETO'
                ),
                'filters' => array('alpha')
            ),
            'porc_venta' => array(
                'type' => 'int',
                'description' => 'Porcentaje Precio Costo/Venta',
                'filters' => array('int')
            )
        ),
        'SO' => array(
            'activeTabs' => true,
            'tabs' => array(
                'Socios' => array(
                    'numero_accion_manual' => array(
                        'description'   => 'Número de Acción Manual',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'S' => 'Si',
                            'N' => 'No'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'autosuspender_usar' => array(
                        'description'   => 'Usar autosuspender?',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'S' => 'Si',
                            'N' => 'No'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'autosuspender_meses' => array(
                        'description'   => 'Auto-cambiar estado en el mes por mora',
                        'type'          => 'int',
                        'filters'       => array('int')
                    ),
                    'autosuspender_estado' => array(
                        'description'   => 'Estado a cambiar de auto-cambio de estado por mora',
                        'type'          => 'relation',
                        'table'         => 'EstadosSocios',
                        'fieldRelation' => 'id',
                        'detail'        => 'nombre',
                        'sort'          => 'nombre',
                        'filters'       => array('int')
                    ),
                    'sync_hotel2' => array(
                        'description'   => 'Syncronizar con Recepci&oacute;n',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'S' => 'Si',
                            'N' => 'No'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'limpiar_cargos_temporales' => array(
                        'description'   => 'Borrar cargos temporales al cerrar periodo?',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'S' => 'Si',
                            'N' => 'No'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'comprobs_pagos' => array(
                        'description'   => 'Comprobantes de Pagos (Sepearados por ",")',
                        'type'          => 'text',
                        'size'          => 46,
                        'maxlength'     => 46,
                        'filters'       => array('upper')
                    ),
                    'contabilizar_convenio' => array(
                        'description'   => 'Crear comprobante a convenio?',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'S' => 'Si',
                            'N' => 'No'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'comprob_convenio' => array(
                        'description'   => 'Comprobante de Convenios',
                        'type'          => 'comprob',
                        'filters'       => array('alpha')
                    ),
                    'tipo_doc_convenio' => array(
                        'description'   => 'Tipo Documento Convenio',
                        'type'          => 'documentos',
                        'filters'       => array('alpha')
                    ),
                ),
                'Calculos' => array(
                    'interes_mora_default' => array(
                        'description'   => 'Interes de Mora por defecto de Periodos',
                        'type'          => 'int',
                        'filters'       => array('float')
                    ),
                    'valor_minimo_mora' => array(
                        'description'   => 'Valor minimo para aplicar mora',
                        'type'          => 'int',
                        'filters'       => array('float')
                    ),
                    'comprob_factura' => array(
                        'description'   => 'Comprobante de Factura',
                        'type'          => 'comprob',
                        'filters'       => array('alpha')
                    ),
                    'tipo_doc' => array(
                        'description'   => 'Tipo Documento Factura',
                        'type'          => 'documentos',
                        'filters'       => array('alpha')
                    ),
                    'pos_formas_pago' => array(
                        'description'   => 'Formas de Pago de Punto de Venta (Sepearados por ",")',
                        'type'          => 'text',
                        'size'          => 46,
                        'maxlength'     => 46,
                        'filters'       => array('upper')
                    ),
                    'cargo_fijo_mora' => array(
                        'description'   => 'Cargo fijo de Mora de Socio',
                        'type'          => 'relation',
                        'table'         => 'CargosFijos',
                        'fieldRelation' => 'id',
                        'detail'        => 'nombre',
                        'sort'          => 'nombre',
                        'filters'       => array('int')
                    ),
                    'cargo_fijo_ajuste' => array(
                        'description'   => 'Cargo fijo de Ajuste de Socio',
                        'type'          => 'relation',
                        'table'         => 'CargosFijos',
                        'fieldRelation' => 'id',
                        'detail'        => 'nombre',
                        'sort'          => 'nombre',
                        'filters'       => array('int')
                    ),
                    'comprob_ajustes' => array(
                        'description'   => 'Comprobante de ajustes',
                        'type'          => 'comprob',
                        'filters'       => array('alpha')
                    ),
                    'comprob_nc' => array(
                        'description'   => 'Comprobantes de notas contables (Sepearados por ",")',
                        'type'          => 'text',
                        'size'          => 46,
                        'maxlength'     => 46,
                        'filters'       => array('upper')
                    ),
                    'cuenta_cruce_pagos' => array(
                        'description'   => 'Cuenta que cruza los ajustes de pagos',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'comprob_financiacion' => array(
                        'description'   => 'Comprobante de Financiación',
                        'type'          => 'comprob',
                        'filters'       => array('alpha')
                    ),
                    'cuenta_financiacion' => array(
                        'description'   => 'Cuenta que asigna deuda a financiar',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'formpag_cartera_socios' => array(
                        'description'   => 'Forma de Pago a Cartera de Socios',
                        'type'          => 'relation',
                        'table'         => 'Forpag',
                        'fieldRelation' => 'forpag',
                        'detail'        => 'detalle',
                        'sort'          => 'detalle',
                        'filters'       => array('int')
                    ),
                    'porc_multa' => array(
                        'description' => 'Porcentaje de Multa (%)',
                        'type'          => 'int',
                        'filters'       => array('int')
                    ),
                    /*'cuenta_multa' => array(
                        'description'   => 'Cuenta multas',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),*/
                    'cuenta_saldo_a_favor' => array(
                        'description'   => 'Cuenta de saldo a favor',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'cuenta_saldo_a_favor_pasar' => array(
                        'description'   => 'Cuenta a pasar el saldo a favor',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'cargo_fijo_consumo_minimo' => array(
                        'description'   => 'Cargo fijo de Consumo Minimo',
                        'type'          => 'relation',
                        'table'         => 'CargosFijos',
                        'fieldRelation' => 'id',
                        'detail'        => 'nombre',
                        'sort'          => 'nombre',
                        'filters'       => array('int')
                    ),
                    'comprob_saldoafavor' => array(
                        'description'   => 'Comprobante de Saldo A favor',
                        'type'          => 'comprob',
                        'filters'       => array('alpha')
                    ),
                    'mover_saldoafavor' => array(
                        'description'   => 'Mover de Saldo A favor al cerrar periodo',
                        'type' => 'closed-domain',
                        'values' => array(
                            'S' => 'SI',
                            'N' => 'NO'
                        ),
                        'filters' => array('alpha')
                    ),
                    'calcular_mora_de' => array(
                        'description'   => 'Se debe calcular la mora de:',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'F' => 'Factura Anterior',
                            'E' => 'Estado de cuenta del mes',
                            'D' => 'Mora diaria por total estado de cuenta'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'cuenta_ajustes_estado_cuenta' => array(
                        'description'   => 'Cuentas de Ajustes (Comodin "%")',
                        'type'          => 'text',
                        'size'          => 46,
                        'maxlength'     => 46,
                        'filters'       => array('upper')
                    ),

                ),
                'Factura' => array(
                    'dia_venc' => array(
                        'description'   => 'Días de vencimiento de factura',
                        'type'          => 'int',
                        'size'          => 2,
                        'filters'       => array('int')
                    ),
                    'resumen_factura' => array(
                        'description'   => 'Resumen de Factura',
                        'type'          => 'textarea',
                        'cols'          => 50,
                        'rows'          => 3,
                        'filters'       => array('striptags')
                    ),
                    'show_consumo_minimo_fac' => array(
                        'description'   => 'Mostrar consumos en Factura?',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'S' => 'Si',
                            'N' => 'No'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'resumen_factura_pie' => array(
                        'description'   => 'Resumen Pie de Pagina de Factura ',
                        'type'          => 'textarea',
                        'cols'          => 50,
                        'rows'          => 3,
                        'filters'       => array('striptags')
                    ),
                    'show_ordenes_pos' => array(
                        'description'   => 'Mostrar vales de consumo POS',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'S' => 'Si',
                            'N' => 'No'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'show_dir_tel_factura' => array(
                        'description'   => 'Mostrar dirección/Teléfeono en Factura',
                        'type'          => 'closed-domain',
                        'values'        => array(
                            'S' => 'Si',
                            'N' => 'No'
                        ),
                        'filters'       => array('alpha')
                    ),
                    'show_financiacion_socios' => array(
                        'description' => 'Ver Financiación en Factura',
                        'type' => 'closed-domain',
                        'values' => array(
                            'S' => 'SI',
                            'N' => 'NO'
                        ),
                        'filters' => array('alpha')
                    ),
                    'show_recargo_mora' => array(
                        'description' => 'Ver Recargo de Mora en Factura',
                        'type' => 'closed-domain',
                        'values' => array(
                            'S' => 'SI',
                            'N' => 'NO'
                        ),
                        'filters' => array('alpha')
                    ),
                    'show_cupo_pago' => array(
                        'description' => 'Ver Cupon de Pago en Factura',
                        'type' => 'closed-domain',
                        'values' => array(
                            'S' => 'SI',
                            'N' => 'NO'
                        ),
                        'filters' => array('alpha')
                    ),
                    'show_saldo_anterior_fac' => array(
                        'description' => 'Mostrar saldo anterior en factura',
                        'type' => 'closed-domain',
                        'values' => array(
                            'S' => 'SI',
                            'N' => 'NO'
                        ),
                        'filters' => array('alpha')
                    ),
                ),
                'Email' => array(
                    'send_factura' => array(
                        'description'   => 'Resumen de Correo de Factura',
                        'type'          => 'textarea',
                        'cols'          => 50,
                        'rows'          => 3,
                        'filters'       => array('striptags')
                    )
                ),
                'Extracto' => array(
                    'send_extracto' => array(
                        'description'   => 'Resumen de Correo de Extracto',
                        'type'          => 'textarea',
                        'cols'          => 50,
                        'rows'          => 3,
                        'filters'       => array('ascii')
                    ),
                    'resumen_extracto' => array(
                        'description'   => 'Resumen de Extracto',
                        'type'          => 'textarea',
                        'cols'          => 50,
                        'rows'          => 3,
                        'filters'       => array('ascii')
                    ),
                    'resumen_recaudo' => array(
                        'description'   => 'Resumen de Recaudo',
                        'type'          => 'textarea',
                        'cols'          => 50,
                        'rows'          => 3,
                        'filters'       => array('ascii')
                    ),
                    'consecutivo_estado_cuenta' => array(
                        'description'   => 'Consecutivo de Estado de Cuenta',
                        'type'          => 'int',
                        'filters'       => array('int')
                    ),
                    'tipo_doc_pos' => array(
                        'description'   => 'Tipo Documento de POS',
                        'type'          => 'documentos',
                        'filters'       => array('alpha')
                    ),
                    'base_porc_mora_desfecha' => array(
                        'description'   => 'Base de calculo de Estado de Cuenta',
                        'type'          => 'int',
                        'filters'       => array('int')
                    ),
                ),
                'Importar Pagos' => array(
                    'importar_pagos_1' => array(
                        'description'   => '#1 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_2' => array(
                        'description'   => '#2 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_3' => array(
                        'description'   => '#3 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_4' => array(
                        'description'   => '#4 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_5' => array(
                        'description'   => '#5 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_6' => array(
                        'description'   => '#6 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_7' => array(
                        'description'   => '#7 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_8' => array(
                        'description'   => '#8 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_9' => array(
                        'description'   => '#9 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'importar_pagos_10' => array(
                        'description'   => '#10 Pago',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'capital_importar_pagos' => array(
                        'description'   => 'Cuenta A Capital',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'contrapartida_importar_pagos' => array(
                        'description'   => 'Cuenta de Cruce',
                        'type'          => 'cuenta',
                        'filters'       => array('cuentas')
                    ),
                    'forma_pago_importar_pagos' => array(
                        'description'   => 'Forma Pago Importar Pagos',
                        'type'          => 'relation',
                        'table'         => 'FormaPago',
                        'fieldRelation' => 'codigo',
                        'detail'        => 'descripcion',
                        'sort'          => 'descripcion',
                        'filters'       => array('alpha')
                    ),
                )
            )
        ),
        'TC' => array(
            'limite_dias_pago' => array(
                'type'          => 'int',
                'description'   => 'Límite de Días de Pago de Factura',
                'filters'       => array('int')
            ),
            'limite_meses_interes' => array(
                'type'          => 'int',
                'description'   => 'Límite de Meses que no aplica interes',
                'filters'       => array('int')
            ),
            'consecutivo_cuenta_cobro' => array(
                'type'          => 'int',
                'description'   => 'Consecutivo de cuenta de cobro',
                'filters'       => array('int')
            ),
            'comprob_rc'        => array(
                'description'   => 'Comprobante de recibo de caja',
                'type'          => 'comprob',
                'filters'       => array('alpha')
            ),
            /*'comprob_tc_derecho_afiliacion' => array(
                'description'   => 'Comprobante de Derecho de Afiliación',
                'type'          => 'comprob',
                'filters'       => array('alpha')
            ),
            'comprob_tc_cuota_inicial' => array(
                'description'   => 'Comprobante de Cuotas Iniciales',
                'type'          => 'comprob',
                'filters'       => array('alpha')
            ),
            'comprob_tc_financiacion' => array(
                'description'   => 'Comprobante de Financiación',
                'type'          => 'comprob',
                'filters'       => array('alpha')
            ),
            'comprob_tc_ajustes' => array(
                'description'   => 'Comprobante de Financiación',
                'type'          => 'comprob',
                'filters'       => array('alpha')
            )*/
        )
    );

    public function initialize(){
        $controllerRequest = ControllerRequest::getInstance();
        if($controllerRequest->isAjax()){
            View::setRenderLevel(View::LEVEL_LAYOUT);
        }
        parent::initialize();
    }

    public function indexAction(){
        $this->setResponse('view');


        $code = CoreConfig::getAppSetting('code');
        foreach($this->Configuration->find("application='$code'") as $configuration){
            Tag::displayTo($configuration->getName(), $configuration->getValue());
        }

        $types = array();
        if (isset(self::$_settings[$code]['activeTabs']) && self::$_settings[$code]['activeTabs']==true) {
            foreach (self::$_settings[$code]['tabs'] as $fieldset => $data)
            {
                foreach ($data as $name => $setting)
                {
                    $types[$setting['type']] = true;
                }
            }
        } else {
            foreach (self::$_settings[$code] as $name => $setting)
            {
                $types[$setting['type']] = true;
            }
        }

        $this->setParamToView('code', $code);
        $this->setParamToView('settings', self::$_settings[$code]);

        if(isset($types['comprob'])){
            Settings::setData('comprobs', $this->Comprob->find(array('order' => 'nom_comprob')));
        }
        if(isset($types['centros'])){
            Settings::setData('centros', $this->Centros->find(array('order' => 'nom_centro')));
        }
        if(isset($types['almacen'])){
            Settings::setData('almacenes', $this->Almacenes->find(array('order' => 'nom_almacen')));
        }
        if(isset($types['consecutivos'])){
            Settings::setData('consecutivos', $this->Consecutivos->find(array('order' => 'detalle')));
        }

        $this->setParamToView('message', 'Indique los parámetros de configuración y haga click en "Guardar"');
    }

    public function saveAction()
    {
        $this->setResponse('json');
        $code = CoreConfig::getAppSetting('code');

        $settings = self::$_settings[$code];
        if (isset(self::$_settings[$code]['activeTabs']) && self::$_settings[$code]['activeTabs']==true) {

            $settings = self::$_settings[$code]['tabs'];

            foreach ($settings as $fieldset => $data) {
                foreach ($data as $name => $setting) {
                    $configuration = $this->Configuration->findFirst("application='$code' AND name='$name'");
                    if ($configuration==false) {
                        $configuration = new Configuration();
                        $configuration->setApplication($code);
                        $configuration->setName($name);
                    }
                    $value = $this->getPostParam($name, $setting['filters']);
                    $configuration->setValue($value);
                    $configuration->setTipo($setting['type']);
                    $configuration->setDescription($setting['description']);
                    if ($configuration->save()==false) {
                        foreach ($configuration->getMessages() as $message) {
                            return array(
                                'status' => 'FAILED',
                                'message' => $message->getMessage().' - '.$name
                            );
                        }
                    }
                }
            }
        } else {
            foreach ($settings as $name => $setting) {
                $configuration = $this->Configuration->findFirst("application='$code' AND name='$name'");
                if ($configuration==false) {
                    $configuration = new Configuration();
                    $configuration->setApplication($code);
                    $configuration->setName($name);
                }
                $value = $this->getPostParam($name, $setting['filters']);
                $configuration->setValue($value);
                $configuration->setTipo($setting['type']);
                $configuration->setDescription($setting['description']);
                if ($configuration->save()==false) {
                    foreach ($configuration->getMessages() as $message) {
                        return array(
                            'status' => 'FAILED',
                            'message' => $message->getMessage() . ' - ' . $name
                        );
                    }
                }
            }
        }

        return array(
            'status' => 'OK'
        );
    }

}
