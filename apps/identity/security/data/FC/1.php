<?php $acl = array (
  'index' => 
  array (
    'index' => true,
  ),
  'gardien' => 
  array (
    'index' => true,
  ),
  'session' => 
  array (
    'index' => true,
  ),
  'socorro' => 
  array (
    'index' => true,
  ),
  'workspace' => 
  array (
    'index' => true,
    'storeElement' => true,
    'getApplicationState' => true,
  ),
  'upgrade' => 
  array (
    'index' => true,
  ),
  'welcome' => 
  array (
    'index' => true,
  ),
  'pedidos' => 
  array (
    'consultar' => true,
  ),
  'tatico' => 
  array (
    'getPedido' => true,
  ),
  'invoicing' => 
  array (
    'index' => false,
    'onRollback' => true,
    'save' => false,
  ),
  'referencias' => 
  array (
    'queryByItem' => true,
    'queryByName' => true,
  ),
  'conceptos' => 
  array (
    'queryByItem' => true,
    'queryByName' => true,
  ),
  'cuentas' => 
  array (
    'queryByItem' => true,
    'queryByName' => true,
  ),
  'facturas' => 
  array (
    'index' => true,
  ),
  'reimprimir' => 
  array (
    'index' => true,
  ),
  'terceros' => 
  array (
    'crear' => true,
  ),
  'consecutivos' => 
  array (
    'index' => true,
    'save' => true,
    'delete' => true,
    'search' => true,
    'rcs' => true,
  ),
  'lista_precios' => 
  array (
    'index' => false,
    'save' => false,
    'delete' => false,
    'search' => false,
    'rcs' => false,
  ),
  'settings' => 
  array (
    'index' => true,
    'save' => true,
  ),
);