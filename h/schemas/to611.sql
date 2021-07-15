update empresa set version = '6.1.1';
alter table saldos drop column consumo;
alter table saldos drop column v_ventas;
alter table saldos drop column v_consumo;
alter table saldos drop column fisico;
alter table saldos drop column ubicacion;
