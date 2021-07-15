alter table prestamos_socios add column cuenta int(10) not null;
alter table prestamos_socios add column cuenta_cruce int(10) not null;
alter table prestamos_socios add column comprob varchar(6) null;
alter table prestamos_socios add column numero int null;

alter table cargos_fijos change cuenta_contable cuenta_contable varchar(20) not null;
alter table cargos_fijos change cuenta_iva cuenta_iva varchar(20) not null;
alter table cargos_fijos change cuenta_consolidar cuenta_consolidar varchar(20) not null;

alter table cargos_fijos add column tercero_fijo varchar(20) null;

alter table socios change identificacion identificacion varchar(20) not null;

CREATE TABLE `suspendidos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `socios_id` int(11) NOT NULL,
  `periodo` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  `observacion` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table detalle_movimiento add column tipo_movi char(1) not null default 'C';

CREATE TABLE `ajuste_saldos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comprob` varchar(5) COLLATE utf8_unicode_ci,
  `numero` int(11) NOT NULL,
  `periodo` int(6) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `ajuste_consumos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prefijo` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero` int(11) NOT NULL,
  `periodo` int(6) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  `socios_id` int(11) NOT NULL,
  `iva` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;        

CREATE TABLE `ajuste_pagos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comprob` varchar(5) COLLATE utf8_unicode_ci,
  `numero` int(11) NOT NULL,
  `periodo` int(6) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `ajuste_prestamos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comprob` varchar(5) COLLATE utf8_unicode_ci,
  `numero` int(11) NOT NULL,
  `periodo` int(6) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table periodo add column fecha_factura varchar(10) null;

CREATE TABLE `tipo_correspondencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(46) COLLATE utf8_unicode_ci NOT NULL,
  `estado` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `correspondencia_socios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `socios_id` int(11) NOT NULL,
  `tipo_correspondencia_id` int(11) NOT NULL,
  `descripcion` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `content` mediumblob NOT NULL,
  `estado` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`),
  KEY `fk_correspondencia_socios_1` (`tipo_correspondencia_id`),
  CONSTRAINT `fk_correspondencia_socios_1` FOREIGN KEY (`tipo_correspondencia_id`) REFERENCES `tipo_correspondencia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numfac` int(11) NOT NULL,
  `periodo` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'P',
  `relay_key` char(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `relaykey` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `relay_key` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*CREATE TABLE `invoicer`.`financiacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factura_id` int(11) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `valor` decimal(12,3) NOT NULL DEFAULT '0.000',
  `mora` decimal(12,3) NOT NULL DEFAULT '0.000',
  `total` decimal(12,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4586 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci*/

alter table socios change celular celular varchar(40) null;
alter table socios change celular_trabajo celular_trabajo varchar(40) null;

alter table estados_socios drop column borra_cargos_fijos;
alter table estados_socios add column accion char(1) not null default 'A';

/*Tabla para el manejo de cargos fijos al asignar un estado*/
create table accion_estados(
	estados_socios_id int not null,
	cargos_fijos_id_ini int not null,
	cargos_fijos_id_fin int not null,
	borrar_cargo_fijo char(1) null,
	primary key(estados_socios_id,cargos_fijos_id_ini)
);

alter table periodo drop column fec_final;

alter table tipo_socios add column cuota_minima decimal(15,0) not null default 0;
alter table tipo_socios add column mora_cuota decimal(5,2) not null default 0;
alter table tipo_socios add column estado char(1) not null default 'A';

CREATE TABLE `novedades_factura` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `socios_id` int(11) NOT NULL,
  `periodo` int(11) NOT NULL,
  `cargos_fijos_id` int(11) NOT NULL,
  `valor` decimal(15,2) NOT NULL DEFAULT '0.00',
  `iva` decimal(15,2) NOT NULL DEFAULT '0.00',
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `registro_porteria` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `socios_id` INT(11) NULL,
  `fecha_registro` TIMESTAMP NOT NULL,
  `fecha_salida` TIMESTAMP NULL,
  `usuario_id` INT(11) NOT NULL,
  `cedula` VARCHAR(15) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `placa` CHAR(7) NULL,
  `boleta` VARCHAR(46) NULL,
  `obsrv` TEXT NULL,
  `tipo` ENUM('S','T','I') NOT NULL DEFAULT 'I', /*Socios,Temporal,Invitado*/
  `estado` CHAR(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`),
  INDEX `busqueda` USING BTREE (`cedula` ASC, `socios_id` ASC, `fecha_registro` ASC, `fecha_salida` ASC, `nombre` ASC, `tipo` ASC, `estado` ASC))
ENGINE = InnoDB COMMENT = 'Registro de personal socios, convenios y temporales\n';

#Arreglando tabla periodo
alter table periodo change fecha_factura dia_factura int(2) null;
alter table periodo add column dias_plazo int(2) null;
alter table periodo drop column id;
alter table periodo drop column usuario_id;
alter table periodo drop column ini_fact;
alter table periodo drop column fin_fact;
alter table periodo add column consecutivos_id int not null;
alter table periodo add primary key(periodo);

CREATE TABLE `consecutivos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `detalle` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `prefijo` char(7) COLLATE utf8_unicode_ci NOT NULL,
  `resolucion` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_resolucion` date NOT NULL,
  `numero_inicial` int(10) unsigned NOT NULL,
  `numero_final` int(10) unsigned NOT NULL,
  `numero_actual` int(10) unsigned NOT NULL,
  `nota_factura` text COLLATE utf8_unicode_ci,
  `nota_ica` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `saldoafavor` (
  `periodo` char(6) COLLATE utf8_unicode_ci NOT NULL,
  `comprob` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `numero` int(10) unsigned NOT NULL,
  PRIMARY KEY (`periodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `facturas_pagos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facturas_id` int(10) unsigned NOT NULL,
  `forma_pago` int(10) unsigned NOT NULL,
  `descripcion` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `facturas_id` (`facturas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `estado_cuenta` (
  `numero` int(10) unsigned NOT NULL,
  `socios_id` INT(11) NULL,
  `fecha` DATE NOT NULL,
  `fecha_saldo` DATE NOT NULL,
  `saldo_ant` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `cargos` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `interes` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `pagos` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d30` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d60` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d90` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d120` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d120m` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `saldo_nuevo` DECIMAL(15,0) NOT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table estado_cuenta add column mora decimal(15,0) NOT NULL;
alter table estado_cuenta add column saldo_nuevo_mora decimal(15,0) NOT NULL;

CREATE TABLE `detalle_estado_cuenta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numero` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `documento` varchar(46) NOT NULL,
  `concepto` text not null,
  `cargos` decimal(15,0) NOT NULL DEFAULT '0.00',
  `abonos` decimal(15,0) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table detalle_estado_cuenta change documento documento varchar(46) not null;
alter table detalle_estado_cuenta change concepto concepto text not null;
drop table `estado_cuenta_detalle`; 

#porcentaje mora despues de fecha de estado de cuenta
alter table socios add column porc_mora_desfecha decimal(5,2) null;

#bugs de invocier evitar duplicidad
alter table invoicer add unique (numero);
alter table detalle_invoicer modify descripcion text not null;
alter table cargos_fijos add column clase_cargo char(1) not null default 'S';#Sostenimiento por defecto

CREATE TABLE `estcarc` (
  `socios_id` INT UNSIGNED NOT NULL,
  `fecha` DATE NOT NULL,
  `saldo_ant` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `cargos` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `interes` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `pagos` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d30` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d60` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d90` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d120` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `d120m` DECIMAL(15,0) NOT NULL DEFAULT 0,
  `saldo_nuevo` DECIMAL(15,0) NOT NULL COMMENT 'Estado de Cuenta Consolidado',
  PRIMARY KEY (`socios_id`, `fecha`),
  CONSTRAINT `socios`
    FOREIGN KEY (`socios_id`)
    REFERENCES `socios` (`socios_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

alter table socios add column consumo_minimo char(1) not null default 'S';
alter table socios add column genera_mora char(1) not null default 'S'; 
alter table socios add column ajuste_sostenimiento char(1) not null default 'S'; 


alter table detalle_movimiento add column ico decimal(10,2) null;
alter table cargos_fijos add column ico decimal(10,2) null default 0;
alter table cargos_fijos add column cuenta_ico varchar(20) null;
alter table novedades_factura add column ico decimal(10,2) null default 0;
alter table cargos_socios add column ico decimal(10,2) null default 0;
alter table factura add column total_ico decimal(12,2) null default 0;
alter table detalle_factura add column ico decimal(10,2) null;
alter table invoicer add column total_ico decimal(12,2) null default 0;
alter table detalle_invoicer add column ico decimal(10,2) null;
alter table estado_cuenta add column fecha date not null;

#Index para agilizar consultas
alter table socios drop index socios_index_1;	
create index socios_index_1 USING BTREE ON socios(socios_id,numero_accion,identificacion,cobra,estados_socios_id);
alter table cargos_socios drop index socios_index_2;
create index socios_index_2 USING BTREE ON cargos_socios(socios_id,fecha,periodo,cargos_fijos_id,estado);
alter table asignacion_cargos drop index socios_index_3;
create index socios_index_3 USING BTREE ON asignacion_cargos(socios_id,cargos_fijos_id,estado);
alter table movimiento drop index socios_index_4;
create index socios_index_4 USING BTREE ON movimiento(socios_id,periodo,factura_id,fecha_at);
alter table detalle_movimiento drop index socios_index_5;
create index socios_index_5 USING BTREE ON detalle_movimiento(socios_id,movimiento_id,fecha,tipo,cargos_socios_id,tipo_documento,tipo_movi,estado);
alter table factura drop index socios_index_6;
create index socios_index_6 USING BTREE ON factura(socios_id,numero,movimiento_id,fecha_factura,periodo,estado,invoicer_id);
alter table detalle_factura drop index socios_index_7;
create index socios_index_7 USING BTREE ON detalle_factura(factura_id);
alter table amortizacion drop index socios_index_8;
create index socios_index_8 USING BTREE ON amortizacion(prestamos_socios_id,estado);
alter table prestamos_socios drop index socios_index_9;
create index socios_index_9 USING BTREE ON prestamos_socios(socios_id,estado);
alter table cargos_fijos drop index socios_index_10;
create index socios_index_10 USING BTREE ON cargos_fijos(nombre,cuenta_contable,cuenta_consolidar,estado,tipo_cargo,clase_cargo);
alter table estado_cuenta drop index socios_index_11;
create index socios_index_11 USING BTREE ON estado_cuenta(numero,socios_id,fecha,fecha_saldo);

alter table invoicer drop index socios_index_11;
create index socios_index_11 USING BTREE ON invoicer(numero,nit,fecha_emision,fecha_vencimiento,estado,comprob_contab,numero_contab);
alter table detalle_invoicer drop index socios_index_12;
create index socios_index_12 USING BTREE ON detalle_invoicer(facturas_id,item);


/*
alter table facturas drop index socios_index_11;
create index socios_index_11 USING BTREE ON facturas(numero,nit,fecha_emision,fecha_vencimiento,estado,comprob_contab,numero_contab);
alter table facturas_detalle drop index socios_index_12;
create index socios_index_12 USING BTREE ON facturas_detalle(facturas_id,item);
*/

#Charsets
#SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
#alter table socios convert to charset utf8 collate utf8_unicode_ci;