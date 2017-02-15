<?php

	use App\Http\Requests;
	use App\ViewVendedores;
	use Carbon\Carbon;

	$fecha_inicio = Request::input("fecha_inicio");
    $fecha_fin = Request::input("fecha_fin");

    $where = $fecha_inicio!="" && $fecha_fin!="" ? 
             "reportes.created_at BETWEEN '".date("Y-m-d", strtotime($fecha_inicio))." 00:00:00' AND '".date("Y-m-d", strtotime($fecha_fin))." 23:59:59'" :
             "reportes.created_at BETWEEN '".Carbon::now("America/Guatemala")->ToDateString()." 00:00:00' AND '".Carbon::now("America/Guatemala")->ToDateString()." 23:59:59'";

    $records  =   ViewVendedores::select(DB::raw("view_vendedores.*, (SELECT count(id) FROM reportes WHERE reportes.id_vendedor = view_vendedores.id_vendedor AND ".$where.") as ventas"))
    							  ->orderBy("cemp", "ASC")->get();

?>

<table>
	<thead>
		<tr>
			<th>CEMP</th>
			<th>COD PDV</th>
			<th>COD VENDEDOR</th>
			<th>TIENDA</th>
			<th>ASESOR</th>
			<th>NUMERO STAFF</th>
			<th>SUPERVISOR</th>
			<th>CADENA</th>
			<th>CANAL</th>
			<th>CIUDAD</th>
			<th>VENTAS</th>
		</tr>
	</thead>
	<tbody>
	@foreach($records as $item)
		<tr>
			<td>{{$item->cemp}}</td>
			<td>{{$item->codigo_pdv}}</td>
			<td>{{$item->codigo_vendedor}}</td>
			<td>{{$item->tienda}}</td>
			<td>{{$item->nombre_vendedor}}</td>
			<td>{{$item->numero_staff}}</td>
			<td>{{$item->supervisor}}</td>
			<td>{{$item->cadena}}</td>
			<td>{{$item->canal}}</td>
			<td>{{$item->departamento}}</td>
			<td>{{$item->ventas}}</td>
		</tr>
	@endforeach
	</tbody>
</table>