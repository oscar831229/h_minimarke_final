SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
update empresa set version = '6.1.8';

#CREE
ALTER TABLE nits add column porce_cree decimal(5,3) null default 0.000;
ALTER TABLE inve add column prod_trib char(1) not null default 'D';
ALTER TABLE lineas add column impo_gasto varchar(64) null;
ALTER TABLE lineas add column impo_costo varchar(64) null;
ALTER TABLE regimen_cuentas add column cta_iva5v varchar(16) null; 
ALTER TABLE movihead add column cree decimal(15,3) null;
ALTER TABLE movihead add column impo decimal(15,3) null; 
ALTER TABLE movilin add column impo decimal(15,3) null; 
ALTER TABLE movih1 add column cree decimal(15,3) null;
ALTER TABLE movih1 add column impo decimal(15,3) null; 
ALTER TABLE movi change nit nit varchar(20); 
ALTER TABLE recep change nit nit varchar(20); 
ALTER TABLE saldosn change nit nit varchar(20) not null; 

CREATE TABLE `consumos_internos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(64) NOT NULL,
  `cuenta` varchar(64) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `cuentas_cree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `porce` decimal(5,3) NOT NULL,
  `cuenta` varchar(64) NOT NULL,
  `base` decimal(12,2) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `retecompras` (
  `codigo` INT NOT NULL,
  `descripcion` VARCHAR(145) NOT NULL,
  `cuenta` VARCHAR(20) NOT NULL,
  `base_retencion` DECIMAL(13,0) NOT NULL DEFAULT 0,
  `porce_retencion` DECIMAL(5,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table nits change nit nit varchar(20) not null;
alter table cartera change nit nit varchar(20) not null;
alter table saldosn change nit nit varchar(20) not null;
alter table movi change descripcion descripcion varchar(240) null;
alter table reccaj change nit nit varchar(20) not null;
alter table grab change codigo_grab codigo_grab varchar(200);
alter table nits add column retecompras_id int null default 1;
alter table movitemp change descripcion descripcion varchar(200) null;
alter table cuentas add primary key(cuenta);
update cuentas set es_mayor='S' where length(cuenta)=4;

#Charsets
#alter table movihead convert to charset utf8 collate utf8_unicode_ci;
#alter table movilin convert to charset utf8 collate utf8_unicode_ci;
#alter table movi convert to charset utf8 collate utf8_unicode_ci;
#alter table nits convert to charset utf8 collate utf8_unicode_ci;