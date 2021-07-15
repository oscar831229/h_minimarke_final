ALTER TABLE menus_items ADD COLUMN codigo_barras VARCHAR(100) NULL AFTER nombre_pedido;
ALTER TABLE menus_items ADD CONSTRAINT codigo_barras_id UNIQUE (codigo_barras);