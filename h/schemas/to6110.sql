SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
update empresa set version = '6.1.10';
	
alter table nits add column email varchar(240) null;
alter table nits add column celular int(10) null;