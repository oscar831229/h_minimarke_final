SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';



INSERT INTO seven_wsdl(CODE,DESCRIPTION,wsdl_production, wsdl_test) VALUES('SPoBirad', 'Web service bitacora', 'http://172.27.2.13/Seven/WebServicesPoBirad/SPoBirad.asmx?wsdl', 'http://172.27.2.89/Seven/WebServicesPoBirad/SPoBirad.asmx?wsdl' );

CREATE TABLE `interfaz_redeban` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`formas_pago_id` INT(10) NOT NULL,
	`operacion` INT(10) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE pagos_factura ADD redeban VARCHAR(255) null;


-----------------------------------------------------------------
-- PROCESO COSTEO DE VENTAS DIARIAS SEGUN ITEMS
-----------------------------------------------------------------

CREATE TABLE `historico_costo_menus_items` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`almacen` INT(10) UNSIGNED NOT NULL,
	`menus_items_id` INT(11) NOT NULL,
	`codigo_referencia` CHAR(15) NOT NULL,
	`fecha` DATE NOT NULL,
	`costo` DECIMAL(10,2) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `historico_costo_referencia` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`almacen` INT(10) UNSIGNED NOT NULL,
	`codigo_referencia` CHAR(15) NOT NULL,
	`fecha` DATE NOT NULL,
	`costo` DECIMAL(10,2) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- AJUSTE PROCESO COSTOS
-- DEPURAR REGISTROS DUPLICADOS POS
ALTER TABLE param_ws ADD CONSTRAINT constr_unique_salonid_menuid UNIQUE (salon_id, menu_id);
ALTER TABLE salon_menus_items ADD CONSTRAINT constr_unique_salonmenusitems_salonid_menusitemsid UNIQUE (salon_id, menus_items_id);
ALTER TABLE historico_costo_menus_items ADD CONSTRAINT constr_unique_hiscosmenite_almacen_menusitemsid_fecha UNIQUE (almacen, menus_items_id, fecha);
ALTER TABLE historico_costo_referencia ADD CONSTRAINT constr_unique_hiscosref_almacen_codref_fecha UNIQUE (almacen, codigo_referencia, fecha);
