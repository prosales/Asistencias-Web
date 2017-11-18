<?php

	use App\Http\Requests;
	use App\Reportes;
	use Carbon\Carbon;

	$fecha_inicio = Request::input("fecha_inicio");
    $fecha_fin = Request::input("fecha_fin");

    $where = $fecha_inicio!="" && $fecha_fin!="" ? 
             "reportes.created_at BETWEEN '".date("Y-m-d", strtotime($fecha_inicio))." 00:00:00' AND '".date("Y-m-d", strtotime($fecha_fin))." 23:59:59'" :
             "reportes.created_at BETWEEN '".Carbon::now("America/Guatemala")->ToDateString()." 00:00:00' AND '".Carbon::now("America/Guatemala")->ToDateString()." 23:59:59'";

    $records  =   Reportes::select("reportes.*")->whereRaw($where)->with("vendedor")
         				    ->leftJoin("vendedores", "vendedores.id", "=", "reportes.id_vendedor")
         				    ->whereRaw("vendedores.estado = 1")
         				    ->orderBy("vendedores.codigo_empleado","ASC")
         				    ->get();

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
			<th>FECHA VENTA</th>
			<th>NUMERO VENDIDO</th>
			<th>MONTO</th>
			<th>INCIDENCIA</th>
			<th>FOTO</th>
		</tr>
	</thead>
	<tbody>
	@foreach($records as $item)
	<?php  if($item->vendedor==null){dd($item);} ?>
		<tr>
			<td>{{$item->vendedor->cemp}}</td>
			<td>{{$item->vendedor->codigo_pdv}}</td>
			<td>{{$item->vendedor->codigo_vendedor}}</td>
			<td>{{$item->vendedor->tienda}}</td>
			<td>{{$item->vendedor->nombre_vendedor}}</td>
			<td>{{$item->vendedor->numero_staff}}</td>
			<td>{{$item->vendedor->supervisor}}</td>
			<td>{{$item->vendedor->cadena}}</td>
			<td>{{$item->vendedor->canal}}</td>
			<td>{{$item->vendedor->departamento}}</td>
			<td>{{date("d-m-Y", strtotime($item->created_at))}}</td>
			<td>{{$item->numero}}</td>
			<td>{{$item->monto}}</td>
			<td>{{$item->incidencia == 0 ? 'No existe incidencia' : $item->incidencia == 1 ? 'NM' : $item->incidencia == 2 ? 'NN' : $item->incidencia ? 'NP' : ''}}</td>
			<td><a href="http://190.151.129.244/asistencias/public/contenido/{{$item->foto}}" >http://190.151.129.244/asistencias/public/contenido/{{$item->foto}}</a></td>
		</tr>
	@endforeach
	</tbody>
</table>