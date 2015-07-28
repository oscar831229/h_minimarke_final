SET NAMES 'utf8' COLLATE 'utf8_unicode_ci';
update empresa set version = '6.1.11';

alter table cuentas add column cuenta_niif varchar(16) null;
alter table nits add column grupo_niif int(2) null;

create table movi_niif like movi;
alter table movi_niif add column cuenta_movi varchar(16) not null;
alter table movi_niif add column nic int(2) null;
alter table movi_niif add column nota_nic text null;

create table nic (
	codigo int(2) not null primary key,
	nombre varchar(100) not null,
	estado char(1) not null default 'A'
) ENGINE 'innodb' CHARSET 'utf8' COLLATE 'utf8_unicode_ci';

INSERT INTO nic VALUES (1, "Presentación de estados financieros", "A");
INSERT INTO nic VALUES (2, "Existencias", "A");
INSERT INTO nic VALUES (7, "Estado de flujos de efectivo", "A");
INSERT INTO nic VALUES (8, "Políticas contables, cambios en las estimaciones contables y errores", "A");
INSERT INTO nic VALUES (10, "Hechos posteriores a la fecha del balance", "A");
INSERT INTO nic VALUES (1, "Contratos de construcción", "A");
INSERT INTO nic VALUES (12, "Impuesto sobre las ganancias", "A");
INSERT INTO nic VALUES (14, "Información Financiera por Segmentos", "A");
INSERT INTO nic VALUES (16, "Inmovilizado material", "A");
INSERT INTO nic VALUES (17, "Arrendamientos", "A");
INSERT INTO nic VALUES (18, "Ingresos ordinarios", "A");
INSERT INTO nic VALUES (19, "Retribuciones a los empleados", "A");
INSERT INTO nic VALUES (20, "Contabilización de las subvenciones oficiales e información a revelar sobre ayudas públicas", "A");
INSERT INTO nic VALUES (21, "Efectos de las variaciones en los tipos de cambio de la moneda extranjera", "A");
INSERT INTO nic VALUES (23, "Costes por intereses", "A");
INSERT INTO nic VALUES (24, "Información a revelar sobre partes vinculadas", "A");
INSERT INTO nic VALUES (26, "Contabilización e información financiera sobre planes de prestaciones por retiro", "A");
INSERT INTO nic VALUES (27, "Estados financieros consolidados y separados", "A");
INSERT INTO nic VALUES (28, "Inversiones en entidades asociadas", "A");
INSERT INTO nic VALUES (29, "Información financiera en economías hiperinflacionarias", "A");
INSERT INTO nic VALUES (30, "Información a revelar en los estados financieros de bancos y entidades financieras similares", "A");
INSERT INTO nic VALUES (31, "Participaciones en negocios conjuntos", "A");
INSERT INTO nic VALUES (32, "Instrumentos financieros: Presentación", "A");
INSERT INTO nic VALUES (33, "Ganancias por acción", "A");
INSERT INTO nic VALUES (34, "Información financiera intermedia", "A");
INSERT INTO nic VALUES (36, "Deterioro del valor de los activos", "A");
INSERT INTO nic VALUES (37, "Provisiones, activos y pasivos contingentes", "A");
INSERT INTO nic VALUES (38, "Activos intangibles", "A");
INSERT INTO nic VALUES (39, "Instrumentos financieros: reconocimiento y valoración", "A");
INSERT INTO nic VALUES (40, "Inversiones inmobiliarias", "A");
INSERT INTO nic VALUES (41, "Agricultura", "A");