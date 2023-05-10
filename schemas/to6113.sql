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


-------------------------------------------------------------------
-- FACTURACIÓN ELECTRONICA
-------------------------------------------------------------------
CREATE TABLE `nota_credito` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `prefijo_documento` CHAR(12) DEFAULT NULL,
  `consecutivo_documento` INT(11) NOT NULL DEFAULT '0',
  `tipo` CHAR(2) NOT NULL DEFAULT 'NC',
  `factura_id` INT(11) NOT NULL,
  `prefijo_facturacion` CHAR(12) DEFAULT NULL,
  `consecutivo_facturacion` INT(11) NOT NULL DEFAULT '0',
  `tipo_facturacion` CHAR(2) NOT NULL,
  `fecha_factura` DATE NOT NULL,
  `fecha_ini_nota_credi` DATE NOT NULL,
  `fecha_fin_nota_credi` DATE NOT NULL,
  `numero_inicial` INT(10) UNSIGNED NOT NULL,
  `numero_final` INT(10) UNSIGNED NOT NULL,
  `subtotal` DECIMAL(10,2) NULL DEFAULT '0.00',
  `total_iva` DECIMAL(10,2) NULL DEFAULT '0.00',
  `total_impoconsumo` DECIMAL(10,2) NULL DEFAULT '0.00',
  `propina` DECIMAL(10,2) DEFAULT NULL,
  `total` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `estado` CHAR(1) NOT NULL DEFAULT '',
  `fecha` DATE NOT NULL,
  `hora` CHAR(5) NOT NULL,
  `usuarios_id` INT(11) NOT NULL,
  `usuarios_nombre` CHAR(30) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_nota_creditos` (`prefijo_documento`,`consecutivo_documento`),
  KEY `nota` (`prefijo_documento`,`consecutivo_documento`,`tipo`),
  KEY `estado` (`estado`),
  KEY `consecutivo_documento` (`consecutivo_documento`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `nota_credito_detalle` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nota_credito_id` INT(11) NOT NULL,
  `detalle_factura_id` INT(11) NOT NULL,
  `fecha_factura` DATE NOT NULL,
  `menus_items_id` INT(11) NOT NULL DEFAULT '0',
  `menus_items_nombre` TEXT NOT NULL,
  `porcentaje_iva` INT(3) NOT NULL,
  `porcentaje_impoconsumo` INT(3) DEFAULT '0',
  `cantidad` INT(11) NOT NULL DEFAULT '0',
  `descuento` INT(3) NOT NULL DEFAULT '0',
  `valor` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `iva` DECIMAL(10,2) NOT NULL,
  `impo` DECIMAL(10,2) DEFAULT '0.00',
  `servicio` DECIMAL(10,2) NOT NULL,
  `total` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `detalle_factura_id` (`detalle_factura_id`),
  KEY `menus_items_id` (`menus_items_id`),
  KEY `nota_credito_id` (`nota_credito_id`),
  CONSTRAINT `nota_credito_detalle_ibfk_1` FOREIGN KEY (`nota_credito_id`) REFERENCES `nota_credito` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `nota_credito_pago` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nota_credito_id` INT(11) NOT NULL,
  `formas_pago_id` INT(11) NOT NULL,
  `pagos_factura_id` INT(11) NOT NULL,
  `fecha_factura` DATE NOT NULL,
  `pago` DECIMAL(16,2) DEFAULT NULL,
  `cargo_plan` CHAR(1) DEFAULT NULL,
  `habitacion_id` INT(11) DEFAULT NULL,
  `cuenta` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nota_credito_id` (`nota_credito_id`),
  CONSTRAINT `nota_credito_pago_ibfk_1` FOREIGN KEY (`nota_credito_id`) REFERENCES `nota_credito` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

CREATE TABLE `inveposnc` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nota_credito_id` INT(11) NOT NULL,
  `fecha` DATE NOT NULL,
  `almacen` INT(10) UNSIGNED NOT NULL,
  `centro_costo` INT(10) UNSIGNED NOT NULL,
  `tipo` CHAR(1) NOT NULL,
  `codigo` CHAR(15) NOT NULL,
  `menus_items_id` INT(10) UNSIGNED NOT NULL,
  `cantidad` INT(10) UNSIGNED NOT NULL,
  `cantidadu` INT(10) UNSIGNED NOT NULL,
  `estado` CHAR(1) NOT NULL,
  `invepos_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nota_credito_id` (`nota_credito_id`),
  CONSTRAINT `inveposnc_ibfk_1` FOREIGN KEY (`nota_credito_id`) REFERENCES `nota_credito` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


ALTER TABLE invepos ADD COLUMN account_id INT(11) NULL DEFAULT 0;
ALTER TABLE invepos ADD COLUMN account_modifiers_id INT(11) NULL DEFAULT 0;


CREATE TABLE `datos_carvajal` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ENC_1` VARCHAR(25) NOT NULL,
  `ENC_4` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `ENC_5` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `ENC_9` VARCHAR(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ENC_10` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `ENC_20` INT(11) DEFAULT NULL,
  `EMI_1` INT(11) NOT NULL,
  `TAC_1` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `TIM_1` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `CTS_1` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `estado` VARCHAR(1) COLLATE utf8_unicode_ci NOT NULL,
  `ENC_21` INT(11) DEFAULT NULL,
  `ITE_4` INT(11) DEFAULT NULL,
  `IAE_1` VARCHAR(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `IAE_2` INT(11) DEFAULT NULL,
  `DFE_1` INT(11) DEFAULT NULL,
  `DFE_2` INT(11) DEFAULT NULL,
  `DFE_4` INT(11) DEFAULT NULL,
  `pais` VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `departamento` VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `municipio` VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `direccion` VARCHAR(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GTE_1` VARCHAR(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GTE_2` VARCHAR(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nota_uno` TEXT NULL DEFAULT '',
  `nota_dos` TEXT NULL DEFAULT '',
  UNIQUE KEY `index_enc` (`ENC_1`),
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT  INTO `datos_carvajal`(`ENC_1`,`ENC_4`,`ENC_5`,`ENC_9`,`ENC_10`,`ENC_20`,`EMI_1`,`TAC_1`,`TIM_1`,`CTS_1`,`estado`,`ENC_21`,`ITE_4`,`IAE_1`,`IAE_2`,`DFE_1`,`DFE_2`,`DFE_4`,`pais`,`departamento`,`municipio`,`direccion`,`GTE_1`,`GTE_2`, `nota_uno`, `nota_dos`) VALUES 
('INVOIC','UBL 2.1','DIAN 2.1','01','COP',1,1,'O-23','false','CGEN14','A',10,94,'REF12345',999,68001,68,680003,'COLOMBIA','SANTANDER','BUCARAMANGA','CARRERA 27 N 61-78','01','IVA', 'SOMOS RESPONSABLES DE IVA |SOMOS AUTORRETENEDORES DE ICA EN LOS MUNICIPIOS DE BUCARAMANGA, GIRON, PIEDECUESTA, BARRANCABERMEJA, VELEZ, LEBRIJA. ABSTENERSE DE REALIZAR RETENCION DE ICA EN ESTOS MUNICIPIOS | ACTIVIDAD ECONOMICA 8430 ', 'AGRADECEMOS PARA EFECTUAR EL PAGO DE LAS FACTURAS A CRÉDITO, FAVOR REALIZAR EN NUESTRAS OFICINAS ADMINISTRATIVAS CAJASAN PUERTA DEL SOL CRA 27 N° 61 - 78 BUCARAMANGA, EN EL HOTEL CONSIGNAR A LA CUENTA CORRIENTE DEL BANCO POPULAR Nº 480240266 BANCO BOGOTA CUENTA CORRIENTE N° 184197622 A NOMBRE DE CAJASAN');

INSERT  INTO `datos_carvajal`(`ENC_1`,`ENC_4`,`ENC_5`,`ENC_9`,`ENC_10`,`ENC_20`,`EMI_1`,`TAC_1`,`TIM_1`,`CTS_1`,`estado`,`ENC_21`,`ITE_4`,`IAE_1`,`IAE_2`,`DFE_1`,`DFE_2`,`DFE_4`,`pais`,`departamento`,`municipio`,`direccion`,`GTE_1`,`GTE_2`, `nota_uno`) VALUES 
('NC','UBL 2.1','DIAN 2.1','91','COP',1,1,'O-23','false','CGEN14','A',20,94,'REF12345',999,68001,68,680003,'COLOMBIA','SANTANDER','BUCARAMANGA','CARRERA 27 N 61-78','01','IVA', 'SOMOS RESPONSABLES DE IVA|SOMOS AUTORRETENEDORES DE ICA EN LOS MUNICIPIOS DE BUCARAMANGA, GIRON, PIEDECUESTA, BARRANCABERMEJA, VELEZ, LEBRIJA. ABSTENERSE DE REALIZAR RETENCION DE ICA EN ESTOS MUNICIPIOS|ACTIVIDAD ECONOMICA 8430 ');

-- TENER EN CUENTA EN EL DESPLIEGUE Y PRUEBAS VALIDAR EL PORQUE

DELIMITER $$

ALTER ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `clientes` AS 
	SELECT  `c`.`tipdoc` AS `tipdoc`,`c`.`cedula` AS `id`,`c`.`nombre` AS `nombre`,	`c`.`direccion` AS `direccion`,	`c`.`telefono1` AS `telefono` FROM `hguarigua`.`clientes` `c` 	
  UNION 
  SELECT 	`e`.`tipdoc` AS `tipdoc`,	`e`.`nit` AS `id`,	`e`.`nombre` AS `nombre`,	`e`.`direccion` AS `direccion`,	`e`.`telefono` AS `telefono` FROM `hguarigua`.`empresas` `e`$$

DELIMITER ;


ALTER TABLE invepos ADD COLUMN cantidadnc INT(10) NULL DEFAULT 0;
ALTER TABLE invepos ADD COLUMN cantidadunc INT(10) NULL DEFAULT 0;

ALTER TABLE detalle_factura ADD COLUMN descuento_aplicado DECIMAL(10,2) DEFAULT 0;
ALTER TABLE factura ADD COLUMN fecha_fin_autorizacion DATE NULL AFTER fecha_resolucion;

CREATE TABLE `resolucion_factura` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `salon_id` INT(11) NOT NULL,
  `tipo_factura` CHAR(1) DEFAULT NULL,
  `autorizacion` CHAR(25) DEFAULT NULL,
  `fecha_autorizacion` DATE DEFAULT NULL,
  `fecha_fin_autorizacion` DATE DEFAULT NULL,
  `prefijo_facturacion` CHAR(5) NOT NULL,
  `consecutivo_inicial` INT(10) UNSIGNED NOT NULL,
  `consecutivo_final` INT(10) UNSIGNED NOT NULL,
  `consecutivo_facturacion` INT(10) UNSIGNED NOT NULL,
  `prefi_nota_credi` VARCHAR(5) DEFAULT NULL,
  `fecha_ini_nota_credi` DATE DEFAULT NULL,
  `fecha_fin_nota_credi` DATE DEFAULT NULL,
  `consec_inici_nota_credi` INT(10) DEFAULT NULL,
  `consec_final_nota_credi` INT(10) DEFAULT NULL,
  `consec_nota_credi` INT(10) DEFAULT NULL,
  `estado` CHAR(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE factura ADD COLUMN tipo_factura VARCHAR(1) NULL;
ALTER TABLE factura ADD COLUMN resolucion_factura_id INT(11) NULL;
ALTER TABLE nota_credito ADD COLUMN tipo_nota VARCHAR(1) NULL;
ALTER TABLE account_cuentas MODIFY prefijo CHAR(5) NOT NULL;

ALTER TABLE salon ADD COLUMN factu_elect_monto_desde DECIMAL(10, 2) DEFAULT 0 AFTER autorizacion;