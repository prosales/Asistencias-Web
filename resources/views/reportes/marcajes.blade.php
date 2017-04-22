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
			<th>ENTRADA PDV</th>
            <th>SALIDA ALMUERZO</th>
            <th>ENTRADA ALMUERZO</th>
            <th>SALIDA PDV</th>
		</tr>
	</thead>
	<tbody>
	@foreach($vendedores as $item)
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
			<td>{{$item->entrada_pdv > 0 ? 'X' : ''}}</td>
			<td>{{$item->salida_almuerzo > 0 ? 'X' : ''}}</td>
			<td>{{$item->entrada_almuerzo > 0 ? 'X' : ''}}</td>
			<td>{{$item->salida_pdv > 0 ? 'X' : ''}}</td>
		</tr>
	@endforeach
	</tbody>
</table>