SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
update empresa set version = '6.1.9';

alter table records drop foreign key `records_ibfk_1`;
alter table inve add rodizio char(1) default 'N';
