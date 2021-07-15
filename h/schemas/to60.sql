
ALTER TABLE movi add numfol int unsigned;
ALTER TABLE movi add index `numfol`(numfol);

alter table movi drop index l_movi;
alter table movi add index `l_movi` (comprob, numero, fecha);

delete from saldosc where ano_mes is null;

ALTER TABLE saldosc drop index l_saldosc;
ALTER TABLE saldosc drop primary key;
ALTER TABLE saldosc add primary key(cuenta, ano_mes);

ALTER TABLE saldosp drop index l_saldosp;
ALTER TABLE saldosp drop primary key;
ALTER TABLE saldosp add primary key(cuenta, centro_costo, ano_mes);

ALTER TABLE saldosn drop index l_saldosn;
ALTER TABLE saldosn drop primary key;
ALTER TABLE saldosn add primary key(cuenta, nit, ano_mes);

ALTER TABLE saldosca DROP INDEX l_saldosca;
ALTER TABLE saldosca DROP PRIMARY KEY;
ALTER TABLE saldosca ADD PRIMARY KEY(cuenta, nit, tipo_doc, numero_doc, ano_mes);

ALTER TABLE cartera drop index lv_cartera;
ALTER TABLE cartera drop primary key;
ALTER TABLE cartera add primary key(cuenta, nit, tipo_doc, numero_doc);

DROP TABLE IF EXISTS movitemp;
CREATE TABLE `movitemp` (
  `sid` char(32) NOT NULL,
  `comprob` char(3) NOT NULL,
  `numero` int(11) NOT NULL,
  `consecutivo` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `cuenta` char(12) NOT NULL,
  `nit` varchar(18) DEFAULT NULL,
  `centro_costo` decimal(6,0) DEFAULT NULL,
  `valor` decimal(14,2) NOT NULL,
  `deb_cre` char(1) NOT NULL,
  `descripcion` char(35) DEFAULT NULL,
  `tipo_doc` char(3) DEFAULT NULL,
  `numero_doc` double DEFAULT NULL,
  `base_grab` decimal(14,2) DEFAULT NULL,
  `conciliado` char(1) DEFAULT NULL,
  `f_vence` date DEFAULT NULL,
  `numfol` int(10) unsigned DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `checksum` char(32) NOT NULL,
  PRIMARY KEY (`sid`,`comprob`,`numero`,`consecutivo`),
  KEY `sid` (`sid`,`comprob`,`numero`),
  KEY `sid_2` (`sid`,`comprob`,`numero`,`consecutivo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE lineas drop primary key;
ALTER TABLE lineas modify almacen decimal(2,0) not null;
ALTER TABLE lineas add primary key (almacen,linea);
ALTER TABLE lineas modify nombre varchar(70) not null;

ALTER TABLE producto modify nom_producto char(40) not null;

ALTER TABLE inve modify descripcion varchar(70) not null;
alter table inve add unidad_porcion char(3) after maximo;
alter table inve add index `estado`(estado, descripcion);

alter table inve drop key llave_i;
alter table inve drop key l_inve1;
alter table inve add primary key(item);
alter table inve add index `linea`(linea);
alter table inve add index `estado`(estado);
alter table inve add index `unidad`(unidad);

ALTER TABLE almacenes modify clase_almacen char(1) not null;
alter table almacenes modify centro_costo int unsigned not null;
alter table almacenes modify estado char(1) not null;

CREATE TABLE `configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application` char(2) NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` text NOT NULL,
  `type` char(32) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `application` (`application`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO configuration values (null, "IN", "d_vence", "10", "int", "Dias Vencimiento Pedidos");
INSERT INTO configuration values (null, "CO", "d_movi_limite", 5, "int", "Dias Futuro Fecha Comprobante");
INSERT INTO configuration values (null, "CO", "comprob_cierre", "G11", "comprob", "Comprobante de Cierre");
INSERT INTO configuration values (null, "CO", "comprob_deprec", "D11", "comprob", "Comprobante Depreciacion");
INSERT INTO configuration values (null, "CO", "comprob_amortiz", "CDF", "comprob", "Comprobante Causacion Diferidos");
INSERT INTO configuration values (null, "CO", "comprob_cheque", "CH1", "comprob", "Comprobante Contabilizacion Cheques");
INSERT INTO configuration values (null, "CO", "comprob_ordenes", "O11", "comprob", "Comprobante Ordenes Servicio");
insert into configuration values (null, 'CO', 'comprob_entactivo', 'EAC', 'comprob', 'Comprobante Entradas Activos');

CREATE TABLE `usuarios_centros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `centro_costo` decimal(6,0) NOT NULL,
  `usuario` char(8)  NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=latin1;
INSERT INTO usuarios_centros values (0, "7500", "informix");

ALTER TABLE nits drop index l_nits;
ALTER TABLE nits add primary key(nit);
ALTER TABLE nits modify nit char(20) not null;
ALTER TABLE nits modify nombre varchar(120) not null;
ALTER TABLE nits add tipodoc int unsigned after clase;
ALTER TABLE nits add locciu int unsigned after ciudad;
update nits set ciudad = trim(ciudad);

ALTER TABLE nits add index(clase);
ALTER TABLE nits add index(locciu);

DROP TABLE IF EXISTS tipodoc;
CREATE TABLE `tipodoc` (
  `codigo` int(10) unsigned NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `clase` char(1) DEFAULT NULL,
  `predeterminado` char(1) DEFAULT 'N',
  PRIMARY KEY (`codigo`),
  KEY `clase` (`clase`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DELETE FROM tipodoc;
INSERT INTO `tipodoc` VALUES (13,'CEDULA DE CIUDADANIA','C','S'),(21,'TARJETA DE EXTRANJERIA','E','S'),(22,'CEDULA DE EXTRANJERIA','E','N'),(31,'NIT','A','S'),(41,'PASAPORTE','E','S'),(42,'TIPO DE DOCUMENTO EXTRANJERO','E','N'),(43,'SIN IDENTIFICACION DEL EXTERIOR','A','N');

ALTER TABLE recetap add estado char(1) not null;
update recetap set estado = "A";
ALTER TABLE criterio add primary key (comprob,almacen,numero);

CREATE TABLE `magnitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `unidad_base` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO magnitudes values (null, 'CANTIDAD', '02');
INSERT INTO magnitudes values (null, 'LONGITUD', '28');
INSERT INTO magnitudes values (null, 'PESO', '01');
INSERT INTO magnitudes values (null, 'VOLUMEN', '02');

ALTER TABLE unidad modify codigo char(3) not null;
ALTER TABLE unidad modify nom_unidad varchar(70) not null;
ALTER TABLE unidad add primary key(codigo);
ALTER TABLE unidad add magnitud int;

CREATE TABLE `conversion_unidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unidad` char(3) NOT NULL,
  `unidad_base` char(3) NOT NULL,
  `factor_conversion` decimal(16,8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unidad` (`unidad`),
  KEY `unidad_base` (`unidad_base`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS cheque;
CREATE TABLE `cheque` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chequeras_id` int(10) unsigned NOT NULL,
  `comprob` char(3) NOT NULL,
  `numero` int(10) unsigned NOT NULL,
  `nit` char(20) NOT NULL,
  `numero_cheque` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora` char(8) NOT NULL,
  `fecha_cheque` date NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  `beneficiario` varchar(120) NOT NULL,
  `observaciones` varchar(120) NOT NULL,
  `impreso` char(1) DEFAULT 'N',
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chequeras_id` (`chequeras_id`),
  KEY `comprob` (`comprob`,`numero`),
  KEY `nit` (`nit`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS cuentas_bancos;
CREATE TABLE `cuentas_bancos` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(80) NOT NULL,
  `numero` varchar(30) NOT NULL,
  `banco_id` int(14) unsigned NOT NULL,
  `tipo` char(1) NOT NULL,
  `cuenta` char(12) NOT NULL,
  `centro_costo` int(10) unsigned NOT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `banco_id` (`banco_id`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS chequeras;
CREATE TABLE `chequeras` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `cuentas_bancos_id` int(14) unsigned NOT NULL,
  `numero_inicial` int(10) unsigned NOT NULL,
  `numero_final` int(10) unsigned NOT NULL,
  `numero_actual` int(10) unsigned NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`),
  KEY `cuentas_bancos_id` (`cuentas_bancos_id`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS banco;
CREATE TABLE `banco` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `oficina` varchar(50) NOT NULL,
  `ciudad` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

ALTER TABLE cuentas modify nombre varchar(70) not null;
ALTER TABLE cuentas add index `es_auxiliar`(es_auxiliar);
ALTER TABLE cuentas add index `es_mayor`(es_mayor);

ALTER TABLE activos drop index l_activos;
ALTER TABLE activos add id int unsigned not null primary key auto_increment first;

ALTER TABLE ubicacion drop key l_ubicacion;
ALTER TABLE ubicacion add primary key(codigo);

ALTER TABLE grupos drop index l_grupos;
ALTER TABLE grupos add primary key(linea);
ALTER TABLE grupos modify nombre varchar(50) not null;
alter table grupos modify es_auxiliar char(1) not null default 'S';
ALTER TABLE grupos add index`cta_compra` (cta_compra);
alter table grupos add index`es_auxiliar` (es_auxiliar);

ALTER TABLE activos modify codigo int unsigned not null;
ALTER TABLE activos modify grupo char(10) not null;
ALTER TABLE activos modify centro_costo decimal(6,0) not null;
ALTER TABLE activos modify fecha_compra date not null;
ALTER TABLE activos modify ubicacion int unsigned not null;
ALTER TABLE activos modify responsable char(15) not null;
ALTER TABLE activos modify proveedor char(15) not null;
ALTER TABLE activos modify estado char(1) not null;
ALTER TABLE activos modify meses_a_dep int(4) not null;
ALTER TABLE activos add tipos_activos_id int unsigned after centro_costo;
ALTER TABLE activos add cantidad int unsigned after tipos_activos_id;
ALTER TABLE activos add forma_pago int unsigned after meses_dep;
alter table activos add entrada char(10) default '';
alter table activos add valor_iva decimal(12,2) after valor_compra;

ALTER TABLE activos add index `codigo`(codigo);
ALTER TABLE activos add index `centro_costo`(centro_costo);
ALTER TABLE activos add index `tipos_activos_id`(tipos_activos_id);
ALTER TABLE activos add index `responsable`(responsable);

CREATE TABLE `novact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` int(10) unsigned NOT NULL,
  `usuarios_id` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `novedad` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE cargos modify nom_cargo varchar(70) not null;

ALTER TABLE fondos drop index l_fondos;
ALTER TABLE fondos add primary key(clase, codigo);

ALTER TABLE retencion drop index l_retencion;
ALTER TABLE retencion add primary key(limite_sup);

ALTER TABLE concentro drop index l_concentro;

ALTER TABLE lineaser add primary key(linea);

ALTER TABLE refe drop index l_refe;
ALTER TABLE refe drop index l_refe1;
ALTER TABLE refe add primary key(item);
ALTER TABLE refe add index `item`(item, linea);

update cuentas set tipo = '' where tipo is null;
update cuentas set clase = '' where clase is null;
update cuentas set mayor = '' where mayor is null;
update cuentas set subclase = '' where subclase is null;
update cuentas set auxiliar = '' where auxiliar is null;
update cuentas set subaux = '' where subaux is null;
update cuentas set mayor='', clase='', subclase='' where length(cuenta)=1;

ALTER TABLE centros modify codigo int unsigned not null;
ALTER TABLE centros modify nom_centro varchar(50) not null;
ALTER TABLE centros modify estado char(1) not null;

ALTER TABLE cuentas drop index l_cuentas;
ALTER TABLE cuentas add primary key(cuenta);

ALTER TABLE ubicacion modify codigo int unsigned not null;
ALTER TABLE ubicacion modify nom_ubica varchar(70) not null;

ALTER TABLE diarios modify nombre varchar(70) not null;

CREATE TABLE `tipos_activos` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE movihead add numero_comprob_contab int(11);
UPDATE movihead SET estado = 'C' WHERE estado IS NULL;

ALTER TABLE empresa add version char(7);
alter table empresa1 add id int unsigned not null primary key auto_increment first;

CREATE TABLE `magfor` (
  `codfor` int(10) unsigned NOT NULL,
  `nombre` text NOT NULL,
  `version` int(3) NOT NULL,
  `termen` char(15) DEFAULT NULL,
  `terexti` char(15) DEFAULT NULL,
  `terextf` char(15) DEFAULT NULL,
  `ternom` varchar(120) DEFAULT NULL,
  `minimo` decimal(16,2) DEFAULT NULL,
  `campo` char(5) NOT NULL,
  PRIMARY KEY (`codfor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `magcue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codfor` int(10) unsigned NOT NULL,
  `codigo` int(10) unsigned NOT NULL,
  `campo` char(5) NOT NULL,
  `cueini` char(12) NOT NULL,
  `cuefin` char(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `magcam` (
  `codfor` int(10) unsigned NOT NULL,
  `campo` char(5) NOT NULL,
  `posicion` int(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`codfor`,`campo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `magcod` (
  `codigo` int(10) unsigned NOT NULL,
  `codfor` int(10) unsigned NOT NULL,
  `nombre` varchar(70) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

delete from documentos where nom_documen='' or nom_documen is null;
ALTER TABLE documentos drop index l_documentos;
ALTER TABLE documentos add primary key(codigo);
ALTER TABLE documentos modify nom_documen varchar(70) not null;

ALTER TABLE comcier drop index l_comcier;
ALTER TABLE comcier add primary key(cuentai);
ALTER TABLE comcier add index `cuentaf`(cuentaf);

alter table comcier modify cuentaf char(12) not null;
alter table comcier modify nit char(20) not null;

ALTER TABLE saldos drop key l_saldos;
ALTER TABLE saldos add primary key(item, almacen, ano_mes);

CREATE TABLE `depreciacion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activos_id` int(10) unsigned NOT NULL,
  `ano_mes` char(6) NOT NULL,
  `fecha` date NOT NULL,
  `comprob` char(3) NOT NULL,
  `numero` int(10) unsigned NOT NULL,
  `centro_costo` int(10) unsigned NOT NULL,
  `cta_dev_compras` char(12) NOT NULL,
  `cta_dev_ventas` char(12) NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

 CREATE TABLE `diferidos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(120) NOT NULL,
  `grupo` int(10) unsigned NOT NULL,
  `centro_costo` int(10) unsigned NOT NULL,
  `fecha_compra` date NOT NULL,
  `valor_compra` decimal(16,2) NOT NULL,
  `numero_fac` int(10) unsigned DEFAULT NULL,
  `meses_a_dep` int(4) unsigned NOT NULL,
  `proveedor` char(15) NOT NULL,
  `forma_pago` int(10) unsigned DEFAULT NULL,
  `estado` char(1) NOT NULL,
  `entrada` char(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

 CREATE TABLE `amortizacion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `diferidos_id` int(10) unsigned NOT NULL,
  `ano_mes` char(6) NOT NULL,
  `fecha` date NOT NULL,
  `comprob` char(3) NOT NULL,
  `numero` int(10) unsigned NOT NULL,
  `centro_costo` int(10) unsigned NOT NULL,
  `cta_dev_compras` char(12) NOT NULL,
  `cta_dev_ventas` char(12) NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `pres` (
  `cuenta` char(12) NOT NULL,
  `centro_costo` int(10) unsigned NOT NULL,
  `ano` char(4) NOT NULL,
  `mes` char(2) NOT NULL,
  `pres` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`cuenta`,`centro_costo`,`ano`,`mes`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `permisos_comprob` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(10) NOT NULL,
  `comprob` char(3) NOT NULL,
  `popcion` char(1) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB;

CREATE TABLE `permisos_centros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(10) NOT NULL,
  `centro_id` int(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

alter table forma_pago modify descripcion varchar(50) not null;
alter table forma_pago modify cta_contable char(12) not null;

CREATE TABLE matriz_proveedores(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` char(15) NOT NULL,
  `nit` char(12) NOT NULL,
  `preferencia` int(11) NULL default 0, 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

update movilin set prioridad = 1 where comprob like 'E%';
update movilin set prioridad = 2 where comprob like 'T%';
update movilin set prioridad = 3 where comprob like 'C%';
update movilin set prioridad = 4 where comprob like 'A%';
update movilin set prioridad = 5 where comprob like 'E%';


/*
alter table movilin modify cantidad decimal(10,2);
alter table movilin modify cantidad_rec decimal(10,2);
alter table saldos modify saldo decimal(10,2);
alter table inve modify saldo_actual decimal(10,2);
*/
