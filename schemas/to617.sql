update empresa set version = '6.1.7';
alter table recetap add preparacion text after costoent;
alter table cuentas drop index `es_auxiliar_2`;
alter table cuentas drop index `es_auxiliar_3`;
alter table cuentas drop index `es_auxiliar_4`;
alter table cuentas drop index `es_mayor_2`;
alter table cuentas drop index `es_mayor_3`;
alter table cuentas drop index `es_mayor_4`;
alter table cuentas add index `pide_centro`(pide_centro);
alter table cuentas add index `pide_fact`(pide_fact);
alter table cuentas add index `pide_nit`(pide_nit);

alter table inve drop index `estado_2`;
alter table inve drop index `estado_3`;
alter table inve drop index `estado_4`;
alter table inve drop index `estado_5`;
alter table inve drop index `estado_6`;

alter table inve drop index `unidad_2`;
alter table inve drop index `unidad_3`;
alter table inve drop index `linea_2`;
alter table inve drop index `linea_3`;

alter table nits drop index `clase_2`;
alter table nits drop index `clase_3`;
alter table nits drop index `clase_4`;

alter table nits drop index `locciu_2`;
alter table nits drop index `locciu_3`;
alter table nits drop index `ix_124_3`;
alter table nits drop index `ix124_3`;
alter table nits add index `nombre`(nombre);

alter table movi drop index `numfol_2`;
alter table movi drop index `numfol_3`;

alter table activos drop index `codigo_2`;
alter table activos drop index `codigo_3`;
alter table activos drop index `codigo_4`;

alter table activos drop index `centro_costo_2`;
alter table activos drop index `centro_costo_3`;
alter table activos drop index `centro_costo_4`;

alter table activos drop index `responsable_2`;
alter table activos drop index `responsable_3`;
alter table activos drop index `responsable_4`;

alter table activos drop index `tipos_activos_id_2`;
alter table activos drop index `tipos_activos_id_3`;
alter table activos drop index `tipos_activos_id_4`;

alter table grupos drop index `cta_compra_2`;
alter table grupos drop index `cta_compra_3`;
alter table grupos drop index `cta_compra_4`;

alter table grupos drop index `es_auxiliar_2`;
alter table grupos drop index `es_auxiliar_3`;


alter table documentos add column cartera char(1) not null default 'S';

CREATE TABLE `reccaj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nit` int(11) NOT NULL,
  `nombre` char(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `direccion` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ciudad` int(11) DEFAULT NULL,
  `telefono` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `comprob` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero` int(11) NOT NULL DEFAULT '1',
  `codusu` int(11) DEFAULT NULL,
  `observaciones` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `rc` int(11) NOT NULL,
  `valor` decimal(13,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `codcaj` (`comprob`),
  KEY `codusu` (`numero`),
  KEY `codven` (`codusu`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE `detalle_reccaj` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `reccaj_id` int(11) NOT NULL,
  `forma_pago_id` int(10) NULL,
  `numero` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `valor` decimal(14,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table formato_cheque add column r_empresa decimal(5,2) not null;
alter table formato_cheque add column p_empresa decimal(5,2) not null;

alter table formato_cheque add column r_num_cheque decimal(5,2) not null;
alter table formato_cheque add column p_num_cheque decimal(5,2) not null;

alter table formato_cheque add column r_cuenta_bancaria decimal(5,2) not null;
alter table formato_cheque add column p_cuenta_bancaria decimal(5,2) not null;

#Del 7% paso al 5%
alter table movih1 add column iva5 decimal(14,2) null;
alter table movih1 add column iva5p decimal(14,2) null;
alter table movih1 add column iva5g decimal(14,2) null;
alter table movih1 add column retiva5 decimal(14,2) null;

alter table regimen_cuentas add cta_iva5d char(12);
alter table regimen_cuentas add cta_iva5r char(12);

#Index para agilizar consultas  
alter table movi drop index contab_1x_index;
create index contab_1_index USING BTREE ON movi(comprob,numero,nit,fecha,deb_cre,cuenta,tipo_doc,numero_doc);
alter table saldosn drop index contab_2_index;
create index contab_2_index USING BTREE ON saldosn(nit,cuenta,ano_mes);
alter table nits drop index contab_3_index;
create index contab_3_index USING BTREE ON nits(nit,nombre);
alter table saldosc drop index contab_4_index;
create index contab_4_index USING BTREE ON saldosc(cuenta,ano_mes);
alter table saldosp drop index contab_5_index;
create index contab_5_index USING BTREE ON saldosp(cuenta,centro_costo,ano_mes);
alter table cartera drop index contab_6_index;
create index contab_6_index USING BTREE ON cartera(cuenta,nit,tipo_doc,numero_doc,f_emision);
alter table reccaj drop index contab_7_index;
create index contab_7_index USING BTREE ON reccaj(nit,comprob,numero,fecha,rc);
alter table saldosca drop index contab_8_index;
create index contab_8_index USING BTREE ON saldosca(cuenta,ano_mes);
alter table movilin drop index contab_9_index;
create index contab_9_index USING BTREE ON movilin(comprob,numero,num_linea,item,almacen_destino,fecha,prioridad);
alter table movihead drop index contab_10_index;
create index contab_10_index USING BTREE ON movihead(nit,comprob,numero,almacen,fecha,estado);
