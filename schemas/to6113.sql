SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';



INSERT INTO seven_wsdl(CODE,DESCRIPTION,wsdl_production, wsdl_test) VALUES('SPoBirad', 'Web service bitacora', 'http://172.27.2.13/Seven/WebServicesPoBirad/SPoBirad.asmx?wsdl', 'http://172.27.2.89/Seven/WebServicesPoBirad/SPoBirad.asmx?wsdl' );

CREATE TABLE `interfaz_redeban` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`formas_pago_id` INT(10) NOT NULL,
	`operacion` INT(10) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE pagos_factura ADD redeban VARCHAR(255) null;