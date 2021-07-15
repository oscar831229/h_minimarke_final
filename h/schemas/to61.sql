alter table configuration modify value text;
alter table movihead add observaciones text after nota;
alter table almacenes modify codigo int unsigned not null;

alter table movihead modify almacen int unsigned not null;
alter table movihead modify almacen_destino int unsigned;

alter table saldos modify costo decimal(16,2);
alter table movilin modify valor decimal(16,2);

alter table movihead add usuarios_id int unsigned not null after almacen_destino;
update movihead set usuarios_id = 1 where usuarios_id is null;

DROP TABLE IF EXISTS `criterios`;
CREATE TABLE `criterios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(70) NOT NULL,
  `puntaje` int(3) unsigned NOT NULL,
  `descripcion` text,
  `tipo` char(1) NOT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

INSERT INTO `criterios` VALUES (1,'CALIDAD',10,'Mide la calidad del Productos o Servicios en estudio','O','A'),
(2,'PRECIO',10,'Precio al cual se ofrece el Producto o Servicio que se desea analizar. Notese que este es un criterio inverso, a mayor precio menor puntuacion. Este criterio es calculado por el sistema','P','A'),
(3,'NIVEL DE INSPECCION',10,'Se mide con la informacion que se tenga del las inspecciones que sobre el Producto o Servicio realiza el proveedor.','O','A'),
(4,'APOYO TECNICO',10,'Mide la calidad y cantidad de apoyo tecnico que ofrece el proveedor.','O','A'),
(5,'REFERENCIAS',10,'Se mide a traves de las referencias que presente al proveedor de acuerdo a las exigencias realizadas, pudiendo ser estas de indole comercial o financieras, pero siendo las mas importantes las que puedan dar sus clientes.','O','A'),
(6,'DIVERSIDAD DE PRODUCTOS',10,'Se refiere a la gama de productos que ofrece el proveedor, pero con la salvedad que la importancia del mismo no esta centrada en el mayor numero de productos sino que los mismos esten en consonancia al producto que se esta evaluando.','O','A'),
(7,'RECURSOS',10,'Se mide por los recursos de los cuales dispone el proveedor','O','A'),
(8,'MANEJO DE SOLICITUDES',10,'Con este criterio se debe evaluar el como maneja los pedidos de los clientes el proveedor.','O','A'),
(9,'NIVELES DE INVENTARIO',10,'Se mide si los niveles de inventario del producto que esta siendo evaluado que maneja el proveedor, responden a la demanda habitual que se tiene del mismo','O','A'),
(10,'ATENCION AL CLIENTE',10,'Por medio de este criterio se mide los niveles de atencion a los clientes que puede ofrecer el proveedor.','O','A'),
(11,'MANEJO DE RECLAMACIONES',10,'Se mide la capacidad y disponibilidad que tiene el proveedor de atender las reclamaciones que puedan surgir.','O','A'),
(12,'FRECUENCIA',10,'Mas que importar el numero de despachos que hace el proveedor, interesa medir si su frecuencia de despacho habitual, o que ofrecen es acorde con las necesidades que se tienen para el producto que esta siendo evaluado.','O','A'),
(13,'COSTOS DE LAS ENTREGAS',10,'Con este criterio se miden todos los costos directos e indirectos que pueden ser atribuidos al sistema de entregas del proveedor, ya sea que los cargue el directamente o que sean imputados por otros medios.','O','A'),
(14,'FLEXIBILIDAD',10,'Este criterio evalua lo flexible, en lo que a las entregas se refiere, que puede ser el proveedor ante cualquier necesidad que se tenga con respecto al producto a evaluar','O','A'),
(15,'FLOTA/TRANSPORTE',10,'Se mide no la cantidad de vehiculos que tiene la flota sino la capacidad disponible de la misma','O','A'),
(16,'MANEJO DE TECNOLOGIAS',10,'Con este criterio se medira el uso adecuado de las nuevas tecnologias de la informacion que hace el proveedor, igualmente se hace extensivo a tecnologias en los procesos productivos y logisticos','O','A'),
(17,'PROCESOS EN LINEA',10,'Se mide la capacidad que tiene el proveedor de prestar sus servicios con la ayuda de las nuevas tecnologias de la comunicacion','O','A'),
(18,'USO DE NUEVAS TECNOLOGIAS',10,'Mide que uso hace de las nuevas tecnologia el proveedor','O','A'),
(19,'COMUNICACIONES',10,'Con este ultimo criterio se mide cuan eficiente se espera que sean las comunicaciones con el proveedor, basados, principalmente, en los canales de comunicacion que el mismo tiene capacidad de manejar','O','A');

CREATE TABLE `criterios_proveedores` (
  `comprob` char(3) NOT NULL,
  `numero` int(10) unsigned NOT NULL,
  `almacen` int(10) unsigned NOT NULL,
  `nit` char(20) NOT NULL,
  `criterios_id` int(10) unsigned NOT NULL,
  `puntaje` int(5) unsigned NOT NULL,
  PRIMARY KEY (`comprob`,`numero`,`almacen`,`nit`,`criterios_id`),
  KEY `nit` (`nit`,`criterios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

alter table inve drop column minimo;
alter table inve drop column maximo;

CREATE TABLE `inve_stocks` (
  `item` char(12) NOT NULL,
  `almacen` int(10) unsigned NOT NULL,
  `minimo` decimal(10,2) NOT NULL,
  `maximo` decimal(10,2) NOT NULL,
  PRIMARY KEY (`item`,`almacen`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

alter table almacenes drop column almacenista;
alter table almacenes add usuarios_id int unsigned not null after clase_almacen;
update almacenes set usuarios_id = 1 where usuarios_id is null;

alter table movihead drop index l_movihead;
alter table movihead add primary key(comprob, almacen, numero);

alter table movilin add id int unsigned not null primary key auto_increment first;
alter table forma_pago modify codigo int unsigned not null;

alter table movihead add total_neto decimal(14,2) after estado;

alter table movihead drop column vendedor;
alter table movihead drop column solicita;
alter table movihead drop column autoriza;

update movihead set estado ='C' where fecha < (select f_cierrei from empresa);

alter table cuentas_bancos drop column es_sucursal;
alter table cuentas_bancos drop column transferencia;

alter table user_session add usuarios_id int unsigned not null after id;

truncate movitemp;

DROP TABLE hfos_workspace.elements;
DROP TABLE hfos_workspace.recent_items;
DROP TABLE hfos_workspace.user_session;
CREATE TABLE hfos_workspace.user_session (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(10) unsigned NOT NULL,
  `app_code` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `token` char(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `ping_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuarios_id` (`usuarios_id`,`app_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

alter table forma_pago drop index l_forma_pago;
alter table forma_pago add primary key(codigo);
alter table forma_pago modify descripcion varchar(70) not null;
alter table forma_pago modify cta_contable char(12) not null;

alter table centros drop key l_centros;
alter table centros add primary key(codigo);
alter table centros modify codigo int unsigned not null;
alter table centros modify nom_centro varchar(50) not null;
alter table centros modify estado char(1) not null;

alter table lineas drop index l_lineas;
alter table lineas add primary key (almacen, linea);
alter table lineas modify almacen int unsigned not null;
alter table lineas modify nombre varchar(50) not null;
alter table lineas modify es_auxiliar char(1) not null;

ALTER TABLE unidad drop index l_unidad;
ALTER TABLE unidad add primary key(codigo);
ALTER TABLE unidad modify codigo char(3) not null;
ALTER TABLE unidad modify nom_unidad varchar(70) not null;
ALTER TABLE unidad add magnitud int;
alter table unidad modify magnitud int unsigned not null;

alter table inve modify unidad char(3) not null;
alter table inve modify estado char(1) not null;
alter table empresa modify nit char(20) not null;

alter table movihead modify nit char(20);
