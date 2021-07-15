alter table recetap add column foto varchar(150) null;
alter table recetap add column porcentaje_impoconsumo decimal(5,2) null;

#Consumos en POS a SOCIOS
insert into tipo_venta values('S', 'CONSUMO SOCIOS', 'N');