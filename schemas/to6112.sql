SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
update empresa set version = '6.1.12';

alter table magnitudes add column divisor decimal(7,3) null;

alter table movilin add column descripcion varchar(250) null;

alter table comprob add column consecutivo_niif integer not null default 0;
alter table comprob add column tipo_movi_niif char(1) not null default 'N';

alter table cuentas add column usa_revelacion char(1) not null default 'N';
alter table cuentas modify column porc_retenc decimal(6,3) not null;#solo porcentajes 0-100%
alter table cuentas add column cuenta_niif varchar(16) null;
update cuentas set cuenta_niif = cuenta;


create table recepniif like recep;

create table niif like cuentas;
insert into niif select * from cuentas;
alter table niif drop column cuenta_niif;
alter table niif add column usa_revelacion char(1) null;
update niif set usa_revelacion = 'S';

CREATE TABLE `saldos_niif` (
  `cuenta` char(12) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `ano_mes` decimal(6,0) NOT NULL,
  `debe` decimal(14,2) NOT NULL,
  `haber` decimal(14,2) NOT NULL,
  `saldo` decimal(14,2) NOT NULL,
  `base_grab` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`cuenta`,`nit`,`ano_mes`),
  KEY `l_anon` (`ano_mes`),
  KEY `contab_2_index` (`nit`,`cuenta`,`ano_mes`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

create table saldosn_niif like saldosn;
alter table saldosn_niif add column depre char(1) not null default 'N';

create table saldosc_niif like saldosc;
alter table saldosc_niif add column depre char(1) not null default 'N';

CREATE TABLE `movibackup` (
  `comprob` char(3) NOT NULL,
  `numero` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cuenta` char(12) NOT NULL,
  `nit` varchar(20) DEFAULT NULL,
  `centro_costo` decimal(6,0) DEFAULT NULL,
  `valor` decimal(14,2) NOT NULL,
  `deb_cre` char(1) NOT NULL,
  `descripcion` varchar(240) DEFAULT NULL,
  `tipo_doc` char(3) DEFAULT NULL,
  `numero_doc` int(10) unsigned DEFAULT NULL,
  `base_grab` decimal(14,2) DEFAULT NULL,
  `conciliado` char(1) DEFAULT NULL,
  `f_vence` date DEFAULT NULL,
  `deleted_time` date NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  KEY `contab_1_index` (`comprob`,`numero`,`nit`,`fecha`,`deb_cre`,`cuenta`,`tipo_doc`,`numero_doc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table movitempniif like movitemp;
alter table movitempniif add column cuenta_movi varchar(20) null;
alter table movitempniif add column numero_movi varchar(20) null;

create table movibackupniif like movibackup;
alter table movibackupniif add column cuenta_movi varchar(20);

create table grabniif like grab;
#alter table grabniif add column cuenta_movi varchar(20);

alter table movi_niif drop column nic;
alter table movi_niif drop column nota_nic;
alter table movi_niif drop column comprob_movi;
alter table movi_niif drop column numero_movi;

alter table movi_niif change cuenta_movi cuenta_movi varchar(20) null;
alter table movi_niif change numero_movi numero_movi varchar(20) null;

alter table saldosc add column neto decimal(20,2) null;

create table cartera_niif like cartera;
alter table cartera_niif add column depre_porce int null;
alter table cartera_niif add column depre_meses int null;

CREATE TABLE depre_niif (
  `comprob` VARCHAR(6) NOT NULL,
  `numero` INT NOT NULL,
  `periodo` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  PRIMARY KEY (`comprob`, `numero`)
);

CREATE TABLE `retecompras` (
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(145) COLLATE utf8_unicode_ci NOT NULL,
  `cuenta` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `base_retencion` decimal(13,0) NOT NULL DEFAULT '0',
  `porce_retencion` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `saldos_niif` (
  `cuenta` char(12) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `ano_mes` decimal(6,0) NOT NULL,
  `debe` decimal(14,2) NOT NULL,
  `haber` decimal(14,2) NOT NULL,
  `saldo` decimal(14,2) NOT NULL,
  `base_grab` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`cuenta`,`nit`,`ano_mes`),
  KEY `l_anon` (`ano_mes`),
  KEY `contab_2_index` (`nit`,`cuenta`,`ano_mes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#Remove Unused field
alter table movi drop column createdTime;
alter table movitemp drop column createdTime;
alter table movi_niif drop column createdTime;
alter table movitempniif drop column createdTime;

alter table inve add column rodizio char(1) null default 'N';

alter table nits add column email varchar(200) null;
alter table movi drop column consecutivo;

ALTER TABLE movihead ADD COLUMN prefijo_c VARCHAR(10) NULL AFTER saldo ;
CREATE INDEX movihead_fecha ON movihead(`fecha`);
CREATE INDEX movihead_comprob ON movihead(`comprob`);
CREATE INDEX movihead_almacen ON movihead(`almacen`);
