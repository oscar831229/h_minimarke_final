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

create table niif like cuentas;
insert into niif select * from cuentas;
alter table niif drop column cuenta_niif;
alter table niif drop column usa_revelacion;

create table saldos_niif like saldosn;

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
  KEY `contab_1_index` (`comprob`,`numero`,`nit`,`fecha`,`deb_cre`,`cuenta`,`tipo_doc`,`numero_doc`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
