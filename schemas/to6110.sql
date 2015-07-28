SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
update empresa set version = '6.1.10';

alter table nits add column email varchar(240) null;
alter table nits add column celular int(10) null;

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
