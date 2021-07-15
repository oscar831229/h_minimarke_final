<?php 

$subject = 'Orden de Pedido al Almacen en '.JASMIN_SYSTEM_CAPTION.' [FV'.$prefac.sprintf('%06s', $numfac).']';
		$body = '<br/>
Señor<br/>
'.$name.'<br/>
<br/>
<br/>
Enviamos una copia del pedido a alamacen generada en '.JASMIN_SYSTEM_CAPTION.'<br/>
<br/>
El pedido está en formato PDF, para visualizarla es necesario tener instalado Acrobat Reader ó un programa similar, para descargar
la factura haga click en el siguiente enlace:<br/><br/>
%attachment-1%<br/>
<br/><br/>
Cordialmente,<br/>
<br/>
'.JASMIN_SYSTEM_CAPTION.'<br/>
<br/>';