drop table socios;

CREATE VIEW `socios` AS  select *,socios_id as id,CONVERT(if(estados_socios_id=1,'A','X') using utf8) as estado, '' as pais_nacimiento,
'' as pais_expedido, 0 as edad, '' as nacionalidad from `hfos_socios`.`socios` ;

CREATE VIEW ambientes AS select * from payande.ambientes;
CREATE VIEW eventos AS select * from payande.eventos;	
