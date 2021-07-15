create index hotel_index_1 USING BTREE ON factura(prefac,numfac,numfol,fecfac,cedula,estado,saldo);
create index hotel_index_2 USING BTREE ON detfac(prefac,numfac,item,fecha,concepto);
create index hotel_index_3 USING BTREE ON cargos(codcar,descripcion,estado);