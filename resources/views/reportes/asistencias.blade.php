<?php

	use App\Http\Requests;
	use App\ViewVendedores;
	use App\Asistencias;
	use Carbon\Carbon;

	$fecha_inicio = Request::input("fecha_inicio") != "" ? date("Y-m-d", strtotime(Request::input("fecha_inicio"))) : Carbon::now()->toDateString();
	$fecha_fin = Request::input("fecha_fin") != "" ? date("Y-m-d", strtotime(Request::input("fecha_fin"))) : Carbon::now()->toDateString();

	$vendedores = ViewVendedores::all();
	$asistencias = [];
	$fechas = [];

	for($i = $fecha_inicio; $i <= $fecha_fin; $i = date("Y-m-d", strtotime($i ."+ 1 days")))
	{
		array_push($fechas, ["fecha"=>$i]);
	}

	foreach($vendedores as $item)
	{
		$dias = [];
		$vendedor = $item;
		for($i = $fecha_inicio; $i <= $fecha_fin; $i = date("Y-m-d", strtotime($i ."+ 1 days")))
		{
		    $asistio = Asistencias::whereRaw("tipo = 4 AND id_vendedor = ? AND created_at BETWEEN ? AND ?", [$item->id_vendedor, $i." 00:00:00", $i." 23:59:59"])->first();
		    if($asistio)
		    {
		    	$objeto = [ "fecha" => $i, "asistio" => 1 ]; 
		    }
		    else
		    {
		    	$objeto = [ "fecha" => $i, "asistio" => 0 ]; 
		    }
		    array_push($dias, $objeto);
		}
		$vendedor["asistencias"] = $dias;
		array_push($asistencias, $vendedor);
	}
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
			<th>VENTAS</th>
			@foreach($fechas as $item) <th>{{$item["fecha"]}}</th> @endforeach
		</tr>
	</thead>
	<tbody>
	@foreach($asistencias as $item)
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
			@foreach($item["asistencias"] as $asistencia)
			<td>{{$asistencia["asistio"]}}</td>
			@endforeach
		</tr>
	@endforeach
	</tbody>
</table>