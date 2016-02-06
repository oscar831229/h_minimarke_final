update empresa set version = '6.1.2';
alter table forma_pago add estado char(1) default 'A' not null;
alter table configuration change `type` tipo char(16) not null;
alter table inve add iva_venta int(4) unsigned after iva;
alter table inve modify iva int(3) unsigned;

alter table inve modify precio_venta_m decimal(14,2);
alter table inve modify costo_actual decimal(14,2);
alter table inve modify saldo_actual decimal(14,2);

alter table hfos_identity.aplicaciones add instalada char(1) not null default 'N';
update hfos_identity.aplicaciones set instalada = 'S' where codigo = 'CO';
update hfos_identity.aplicaciones set instalada = 'S' where codigo = 'IN';
update hfos_identity.aplicaciones set instalada = 'S' where codigo = 'IM';

alter table comprob add cta_iva16_venta char(12) after cta_cartera;
alter table comprob add cta_iva10_venta char(12) after cta_iva16_venta;

alter table nits modify ap_aereo decimal(4,2);

alter table nits modify plazo char(1);
update nits set plazo = 'N' where plazo = '0';
update nits set plazo = 'S' where plazo = '1';

update inve set unidad_porcion = unidad where unidad_porcion not in (select codigo from unidad) or unidad_porcion is null;

update nits set nombre = trim(nombre);
update nits set nombre = 'SIN NOMBRE' where nombre = '';
alter table nits modify locciu int unsigned default 0;


/**
 * INVE
 */

alter table recetap change numero_rec numero_rec varchar(10) not null;
