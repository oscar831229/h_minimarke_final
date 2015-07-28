 alter table recibos_pagos add column debcre char(1) null default 'C';
 update recibos_pagos set debcre='C' where observaciones like '%CrÃ©dito%' and estado='N';
 update recibos_pagos set debcre='D' where observaciones not like '%CrÃ©dito%' and estado='N';

 alter table recibos_pagosh add column debcre char(1) null default 'C';
 update recibos_pagosh set debcre='C' where observaciones like '%CrÃ©dito%' and estado='N';
 update recibos_pagosh set debcre='D' where observaciones not like '%CrÃ©dito%' and estado='N';

 alter table recibos_pagos add column porc_condonar decimal(5,2) null default 0.00;
 alter table recibos_pagosh add column porc_condonar decimal(5,2) null default 0.00;