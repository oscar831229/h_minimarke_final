update empresa set version = '6.1.3';
alter table nits modify ap_aereo decimal(4,2);
alter table grab add usuarios_id int unsigned;
alter table nits modify plazo char(1);
update nits set plazo = 'N' where plazo = '0';
update nits set plazo = 'S' where plazo = '1';
alter table cartera modify centro_costo int unsigned;
alter table hinve modify plazo_reposicion int unsigned;
alter table inve modify plazo_reposicion int unsigned;
alter table movi modify numero_doc int unsigned;
alter table movi00 modify numero_doc int unsigned;
alter table movi99 modify numero_doc int unsigned;

CREATE TABLE `formato_cheque` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chequeras_id` int(10) unsigned NOT NULL,
  `r_ano` int(4) unsigned DEFAULT NULL,
  `p_ano` int(4) unsigned NOT NULL,
  `r_mes` int(4) unsigned NOT NULL,
  `p_mes` int(4) unsigned NOT NULL,
  `r_dia` int(4) unsigned NOT NULL,
  `p_dia` int(4) unsigned NOT NULL,
  `r_valor` int(4) unsigned NOT NULL,
  `p_valor` int(4) unsigned NOT NULL,
  `r_tercero` int(4) unsigned NOT NULL,
  `p_tercero` int(4) unsigned NOT NULL,
  `r_suma` int(4) unsigned NOT NULL,
  `p_suma` int(4) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table formato_cheque add r_contab int(3) not null;
alter table formato_cheque add p_contab int(3) not null;

alter table forma_pago add estado char(1) default 'A';

CREATE TABLE `grupos_diferidos` (
  `linea` char(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `es_auxiliar` char(1) DEFAULT NULL,
  `cta_compra` char(12) DEFAULT NULL,
  `cta_inve` char(12) DEFAULT NULL,
  `cta_ret_compra` char(12) DEFAULT NULL,
  `porc_compra` decimal(4,3) DEFAULT NULL,
  `minimo_ret` decimal(14,2) DEFAULT NULL,
  `cta_dev_ventas` char(12) DEFAULT NULL,
  `cta_dev_compras` char(12) DEFAULT NULL,
  PRIMARY KEY (`linea`),
  KEY `cta_compra` (`cta_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

alter table inve add iva_venta int(4) unsigned after iva;  
alter table inve modify iva int(4) unsigned;
