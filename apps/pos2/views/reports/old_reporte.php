<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

?>
<html>
<head>
<meta http-equiv='Content-type' content='text/html; charset=UTF-8' />
<style type='text/css'>

h3 {
	margin: 0px;
	margin-top: 10px;
	margin-bottom: 2px;
	font-size: 13px;
	font-weight: bold;
}

table {
	border-right: 1px solid #969696;
	border-bottom: 1px solid #969696;
}

td, th {
	font-size: 11px;
	border-left: 1px solid #969696;
	border-top: 1px solid #969696;
	font-family: Verdana;
	padding-left: 5px;
	padding-right: 5px;
}

th {
	background: #eaeaea;
	font-family: Verdana
}

table tr:nth-child(even) {
	background: #fafafa;
}

table tr:nth-child(odd) {
	background: #ffffff;
}

</style>
</head>
<body>

<div style='font-size:12px;font-family:Verdana'>
<?php

$Datos->findFirst();
echo "<b>", $Datos->getNombreCadena(), "</b><br>\n";
echo "<b>", $Datos->getDocumento(), " No. : ", $Datos->getNit(), "</b><br>\n";
echo i18n::strtoupper($Datos->getNombreHotel())."<br>\n";
echo "RESUMEN DE VENTA POR PLATO<br>";
if($fecha_inicial==$fecha_final){
	echo "Fecha: {$fecha_inicial}"."<br>";
} else {
	echo "Fecha: {$fecha_inicial} - {$fecha_final}<br>\n";
}
echo "Cajero: ".strtoupper(Session::getData("usuarios_nombre"))."<br>";

if($salon_id!='@'){
	if($Salon->findFirst($salon_id)){
		echo "Ambiente: {$Salon->nombre}<br>";
	}
} else {
	echo "TODOS LOS AMBIENTES<br>";
}

$tiposItems = array(
	'A' => 'ALIMENTOS',
	'B' => 'BEBIDAS',
	'L' => 'LAVANDERIA',
	'C' => 'CIGARRILLOS',
	'O' => 'OTROS'
);
if($tipoItem!='@'){
	if(in_array($tipoItem, array_keys($tiposItems))){
		echo "Tipo de Items: {$tiposItems[$tipoItem]}<br>";
	} else {
		$tipoItem = "@";
	}
} else {
	echo "TODOS LOS TIPOS DE ITEMS<bR>";
}

?>
<br>
<?php

$tipos = array(
	'H' => 'CARGO A HABITACIÓN',
	'F' => 'FACTURAS',
	'U' => 'FUNCIONARIO',
	'C' => 'CORTESIA',
	'P' => 'PLAN'
);

function getHabitacion($id)
{
	$query = new ActiveRecordJoin(array(
		'entities' => array('Habitacion'),
		'conditions' => "id='$id'"
	));

	$habitaciones = array();
	foreach($query->getResultSet() as $habitacion){
		return $id ." - ".$habitacion->nombre;
	}

	return null;
} 



foreach($tipos as $tipo => $detalle){
	echo '<h3>', $detalle, '</h3>';

?>
<table width="670" cellspacing="0">
	<tr>
		<th>Código</th>
		<th>Grupo Men&uacute;</th>
		<th>Nro. Habitaci&oacute;n/Nombre</th>
		<th>Descripción</th>
		<th>Cantidad</th>
		<th>Porc. Costo</th>
		<th>Costo</th>
		<th>Precio Venta</th>
		<th>Valor Total</th>
	</tr>
<?php

	/*$conditions = "{#AccountMaster}.estado = 'L' AND {#Account}.estado = 'L'";
	if($fecha_inicial==$fecha_final){
		$conditions.= " AND date({#AccountMaster}.hora) = '$fecha_inicial'";
	} else {
		$conditions.= " AND date({#AccountMaster}.hora) >= '$fecha_inicial' AND date({#AccountMaster}.hora) <= '$fecha_final'";
	}

	if($salon_id!='@'){
		$conditions.=" AND {#AccountMaster}.salon_id = '$salon_id'";
	}
	
	if($tipoItem!='@'){
		$conditions.=" AND {#MenusItems}.tipo = '$tipoItem'";
	}
	
	$conditions.=" AND {#AccountCuentas}.tipo_venta = '$tipo' AND {#AccountCuentas}.estado = 'L'";
	$query = new ActiveRecordJoin(array(
		'entities' => array('AccountMaster', 'Account', 'MenusItems', 'Menus', 'AccountCuentas'),
		'conditions' => $conditions,
		'groupFields' => array(
			'menu' => '{#Menus}.nombre',
					  '{#MenusItems}.nombre',
					  '{#MenusItems}.id',
					  '{#AccountCuentas}.habitacion_id'

		),
		'sumatory' => array(
			'{#Account}.cantidad',
			'costo' => '{#MenusItems}.costo*{#Account}.cantidad',
			'valor' => 'if({#Account}.descuento>0, ({#Account}.valor-{#Account}.valor*{#Account}.descuento/100)*{#Account}.cantidad, {#Account}.valor*{#Account}.cantidad)',
			'total' => 'if({#Account}.descuento>0, ({#Account}.total-{#Account}.total*{#Account}.descuento/100)*{#Account}.cantidad, {#Account}.total*{#Account}.cantidad)',
		),
		'order' => array('{#Menus}.nombre', '{#MenusItems}.nombre')
	));*/

	$conditions = "{#Factura}.estado = 'A'";
	if($fecha_inicial==$fecha_final){
		$conditions.= " AND {#Factura}.fecha = '$fecha_inicial'";
	} else {
		$conditions.= " AND {#Factura}.fecha >= '$fecha_inicial' AND {#Factura}.fecha <= '$fecha_final'";
	}

	if($salon_id!='@'){
		$conditions.=" AND {#Factura}.salon_id = '$salon_id'";
	}

	if($tipoItem!='@'){
		$conditions.=" AND {#Factura}.tipo = '$tipoItem'";
	}

	$conditions.=" AND {#Factura}.tipo_venta = '$tipo'";
	$query = new ActiveRecordJoin(array(
		'entities' => array('Factura', 'DetalleFactura', 'MenusItems', 'Menus'),
		'conditions' => $conditions,
		'groupFields' => array(
			'menu' => '{#Menus}.nombre',
					  '{#MenusItems}.nombre',
					  '{#MenusItems}.id',
					  '{#Factura}.habitacion_id'

		),
		'sumatory' => array(
			'{#DetalleFactura}.cantidad',
			'costo' => '{#MenusItems}.costo*{#DetalleFactura}.cantidad',
			'valor' => 'if({#DetalleFactura}.descuento>0, ({#DetalleFactura}.valor-{#DetalleFactura}.valor*{#DetalleFactura}.descuento/100)*{#DetalleFactura}.cantidad, {#DetalleFactura}.valor*{#DetalleFactura}.cantidad)',
			'total' => 'if({#DetalleFactura}.descuento>0, ({#DetalleFactura}.total-{#DetalleFactura}.total*{#DetalleFactura}.descuento/100)*{#DetalleFactura}.cantidad, {#DetalleFactura}.total*{#DetalleFactura}.cantidad)',
		),
		'order' => array('{#Menus}.nombre', '{#MenusItems}.nombre')
	));

	echo"alejo". $query->getSQLQuery();

	$costo = 0;
	$total = 0;
	$valor = 0;
	$cantidad = 0;
	foreach($query->getResultSet() as $item){
		echo "<tr>";
			echo "<td align='left'>", $item->id, "</td>";
			echo "<td align='left'>", $item->menu, "</td>";
			echo "<td align='left'>", ($item->habitacion_id > 0) ? getHabitacion($item->habitacion_id) : "&nbsp;" , "</td>";
			echo "<td align='left'>", $item->nombre, "</td>";
			echo "<td align='right'>", $item->cantidad, "</td>";
			if($item->valor!=0){
				echo "<td align='right'>", (LocaleMath::round($item->costo*100/$item->valor, 2)), "</td>";
			} else {
				echo "<td align='right'>0</td>";
			}
			echo "<td align='right'>", Currency::number($item->costo, 2), "</td>";
			echo "<td align='right'>", Currency::number($item->valor, 2), "</td>";
			echo "<td align='right'>", Currency::number($item->total, 2), "</td>";
		echo "</tr>";
		$costo += $item->costo;
		$valor += $item->valor;
		$total += $item->total;
		$cantidad += $item->cantidad;
	}

?>
<tr>
	<td colspan="3">&nbsp;</td>
	<td align="right"><b><?php echo $cantidad ?></b></td>
	<td align="right">&nbsp;</td>
	<td align="right"><b><?php echo Currency::number($costo, 2) ?></b></td>
	<td align="right"><b><?php echo Currency::number($valor, 2) ?></b></td>
	<td align="right"><b><?php echo Currency::number($total, 2) ?></b></td>
</tr>
</table>

<?php } ?>

</body>
</html>
