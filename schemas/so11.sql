SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
update datos_club set version = '1.1';

alter table cargos_fijos change cuenta_contable cuenta_credito varchar(20) not null;
alter table cargos_fijos change cuenta_consolidar cuenta_debito varchar(20) not null;
alter table cargos_fijos drop column naturaleza;
alter table cargos_fijos change cuenta_iva cuenta_iva_deb varchar(20) null;
alter table cargos_fijos add column cuenta_iva_cre varchar(20) null;
alter table cargos_fijos change cuenta_ico cuenta_ico_deb varchar(20) null;
alter table cargos_fijos add column cuenta_ico_cre varchar(20) null;

alter table tipo_socios add column edad_ini int(2) null;
alter table tipo_socios add column edad_fin int(2) null;

CREATE TABLE `cargos_fijos_categoria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo_socios_id` INT NOT NULL,
  `carfijo1` INT NOT NULL,
  `carfijo2` INT NULL,
  `carfijo3` INT NULL,
  `carfijo4` INT NULL,
  `carfijo5` INT NULL,
  `carfijo6` INT NULL,
  `carfijo7` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_fk1` (`tipo_socios_id` ASC)
);

CREATE TABLE `cambio_categoria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fecha` DATETIME NOT NULL,
  `socios_id` INT NOT NULL,
  `tipo_socios_id` INT NOT NULL,
  `descripcion` TEXT NOT NULL,
  PRIMARY KEY (`id`)
);