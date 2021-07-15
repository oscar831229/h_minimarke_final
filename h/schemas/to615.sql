update empresa set version = '6.1.5';
alter table hfos_identity.usuarios add fingerprint char(48) after clave_corta;
alter table hfos_identity.usuarios add index `fingerprint`(fingerprint);
alter table formato_cheque add medida char(2) not null default 'PS' after chequeras_id;
