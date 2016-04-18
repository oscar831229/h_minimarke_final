SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
update empresa set version = '6.2.0';

#Consecutivo de NIIF
alter table comprob add column consecutivo_niif integer not null;

#Tipo de Causacion: (N)ormal, (N)iif o (A)mbos
alter table comprob add column tipo_movi char(1) not null default 'A';

#Revelaciones por cuenta (S)i o (N)o
alter table cuentas add column usa_revelacion char(1) not null default 'N';

create table movibackup like movi;
alter table movibackup add deleted_time date not null;
alter table movibackup add usuarios_id int not null;

create table niif like cuentas;
alter table niif drop column cuenta_niif;

