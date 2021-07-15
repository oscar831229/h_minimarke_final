update empresa set version = '6.1.4';
alter table formato_cheque drop column r_contab;
alter table formato_cheque drop column p_contab;
ALTER TABLE `formato_cheque` ADD COLUMN `r_numero` INT(3) NOT NULL  AFTER `p_suma` , 
	ADD COLUMN `p_numero` INT(3) NOT NULL  AFTER `r_numero` , 
	ADD COLUMN `r_cuenta` INT(3) NOT NULL  AFTER `p_numero` , 
	ADD COLUMN `p_cuenta` INT(3) NOT NULL  AFTER `r_cuenta` , 
	ADD COLUMN `r_detalle` INT(3) NOT NULL  AFTER `p_cuenta` , 
	ADD COLUMN `p_detalle` INT(3) NOT NULL  AFTER `r_detalle` , 
	ADD COLUMN `r_debito` INT(3) NOT NULL  AFTER `p_detalle` , 
	ADD COLUMN `p_debito` INT(3) NOT NULL  AFTER `r_debito` , 
	ADD COLUMN `r_credito` INT(3) NOT NULL  AFTER `p_debito` , 
	ADD COLUMN `p_credito` INT(3) NOT NULL  AFTER `r_credito` , 
	ADD COLUMN `r_valor_movi` INT(3) NOT NULL  AFTER `p_credito` , 
	ADD COLUMN `p_valor_movi` INT(3) NOT NULL  AFTER `r_valor_movi` ;

ALTER TABLE `formato_cheque` CHANGE COLUMN `r_ano` `r_ano` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_ano` `p_ano` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_mes` `r_mes` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_mes` `p_mes` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_dia` `r_dia` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_dia` `p_dia` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_valor` `r_valor` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_valor` `p_valor` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_tercero` `r_tercero` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_tercero` `p_tercero` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_suma` `r_suma` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_suma` `p_suma` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_numero` `r_numero` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_numero` `p_numero` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_cuenta` `r_cuenta` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_cuenta` `p_cuenta` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_detalle` `r_detalle` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_detalle` `p_detalle` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_debito` `r_debito` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_debito` `p_debito` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_credito` `r_credito` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_credito` `p_credito` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `r_valor_movi` `r_valor_movi` DECIMAL(5,2) NOT NULL  , 
	CHANGE COLUMN `p_valor_movi` `p_valor_movi` DECIMAL(5,2) NOT NULL  ;

ALTER TABLE `formato_cheque` ADD COLUMN `r_nota` DECIMAL(5,2) NOT NULL  AFTER `p_numero`, 
	ADD COLUMN `p_nota` DECIMAL(5,2) NOT NULL  AFTER `r_nota` ;
