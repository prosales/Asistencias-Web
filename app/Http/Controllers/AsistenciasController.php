<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Vendedores;
use App\Telefonos;
use App\Asistencias;
use App\Reportes;
use App\ViewVendedores;
use Carbon\Carbon;

class AsistenciasController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode =    200;
    public $ventas = -1;

    public function crear_mensaje(Request $request)
    {
        try
        {
            $nuevoRegistro  =   DB::transaction(function() use ( $request )
                                {
                                    $incidencia = $request->input("incidencia", "");
                                    $telefono = $request->input("telefono");
                                    $numero = $request->input("numero", "");
                                    $monto = $request->input("monto", "");
                                    $archivo = $request->file("foto");
                                    $nombre_archivo = "";

                                    if($archivo)
                                    {
                                        $extension = $archivo->getClientOriginalExtension();
                                        $destino = public_path()."/contenido/";
                                        $nombre_archivo = str_random(20) . "." . $extension;
                                        $se_subio = $archivo->move($destino, $nombre_archivo);
                                        if(!$se_subio)
                                            throw new \Exception("Ocurrio un problema al subir archivo");
                                    }

                                    if( $telefono!="" )
                                    {
                                        $vendedor = Vendedores::where("telefono", $telefono)->first();
                                        if($vendedor)
                                        {
                                            $record                     =   new Reportes;
                                            $record->id_vendedor        =   $vendedor->id;
                                            $record->numero             =   $numero;
                                            $record->monto              =   $monto;
                                            $record->incidencia         =   $incidencia;
                                            $record->foto               =   $nombre_archivo;
                                            $record->created_at         =   Carbon::now("America/Guatemala")->ToDateTimeString();
                                            $record->updated_at         =   Carbon::now("America/Guatemala")->ToDateTimeString();

                                            if($record->save())
                                                return $record;
                                            else
                                                throw new \Exception("Error al crear registro");
                                        }
                                        else
                                        {
                                            throw new \Exception("El número no se encuentra registrado");
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception("El campo telefono es obligatorio");
                                    }
                                                                                
                                });

            $statusCode     =   200;
            $this->message  =   "Registro creado";
            $this->result   =   true;
            $this->records  =   $nuevoRegistro;
        }
        catch(\Exception $e)
        {
            $statusCode =   200;
            $this->message  =   env( "APP_DEBUG" ) ? $e->getMessage() : "Registro no se creo";
            $this->result   =   false;
        }
        finally
        {
            $response = 
            [
                "message"   =>  $this->message,
                "result"    =>  $this->result,
                "records"   =>  $this->records
            ];
            
            return response()->json( $response , $statusCode );
        }
    }

    public function mensajes(Request $request)
    {
        try
        {
            $fecha_inicio = $request->input("fecha_inicio");
            $fecha_fin = $request->input("fecha_fin");

            $where = $fecha_inicio!="" && $fecha_fin!="" ? 
                     "reportes.created_at BETWEEN '".date("Y-m-d", strtotime($fecha_inicio))." 00:00:00' AND '".date("Y-m-d", strtotime($fecha_fin))." 23:59:59'" :
                     "reportes.created_at BETWEEN '".Carbon::now("America/Guatemala")->ToDateString()." 00:00:00' AND '".Carbon::now("America/Guatemala")->ToDateString()." 23:59:59'";

            $statusCode     =   200;
            $this->message  =   "Consultando registros";
            $this->result   =   true;
            $this->records  =   Reportes::select("reportes.*", "vendedores.nombre as nombre_vendedor", "vendedores.telefono" , "vendedores.codigo as codigo_vendedor")
                                                  ->leftJoin("vendedores", "vendedores.id", "=", "reportes.id_vendedor")
                                                  ->whereRaw( $where )->get();          
        }
        catch(\Exception $e)
        {
            $statusCode =   200;
            $this->message  =   env( "APP_DEBUG" ) ? $e->getMessage() : "Registro no se creo";
            $this->result   =   false;
        }
        finally
        {
            $response = 
            [
                "message"   =>  $this->message,
                "result"    =>  $this->result,
                "records"   =>  $this->records
            ];
            
            return response()->json( $response , $statusCode );
        }
    }

    public function crear_asistencia(Request $request)
    {
        try
        {
            $nuevoRegistro  =   DB::transaction(function() use ( $request )
                                {
                                    $control = array(
                                                "1" => "Entrada a PDV",
                                                "2" => "Salida a Almuerzo",
                                                "3" => "Entrada a Almuerzo",
                                                "4" => "Salida a PDV",
                                               );
                                    $fecha_hora_entrada = Carbon::now("America/Guatemala")->toDateTimeString();
                                    $tipo = $request->input("tipo");
                                    $telefono = $request->input("telefono");
                                    $latitud = $request->input("latitud","0");
                                    $longitud = $request->input("longitud","0");
                                    $marcar = $request->input("validar", "0");

                                    if( $tipo!="" && $telefono!="" )
                                    {
                                        if($tipo >= 1 && $tipo <= 4)
                                        {
                                            $vendedor = Vendedores::where("telefono", $telefono)->first();
                                            if($vendedor)
                                            {
                                                $validar = Asistencias::whereRaw("id_vendedor = ? AND tipo = ? AND created_at BETWEEN ? AND ?", [$vendedor->id, $tipo, Carbon::now("America/Guatemala")->toDateString()." 00:00:00", Carbon::now("America/Guatemala")->toDateString()." 23:59:59"])->first();
                                                if(!$validar)
                                                {
                                                    if($tipo > 1)
                                                    {
                                                        $tipo2 = $tipo - 1;
                                                        $validar2 = Asistencias::whereRaw("id_vendedor = ? AND tipo = ?", [$vendedor->id, $tipo2])->first();
                                                        if(!$validar2)
                                                            throw new \Exception("No cuentas con ".$control[$tipo2]);
                                                    }

                                                    if($tipo == 4)
                                                    {
                                                        $this->ventas = Reportes::whereRaw("id_vendedor = ? AND created_at BETWEEN ? AND ?", [$vendedor->id, Carbon::now("America/Guatemala")->toDateString()." 00:00:00", Carbon::now("America/Guatemala")->toDateString()." 23:59:59"])->count();
                                                        if($this->ventas > 0 || $marcar == 1)
                                                        {
                                                            $record                     =   new Asistencias;
                                                            $record->id_vendedor        =   $vendedor->id;
                                                            $record->tipo               =   $tipo;
                                                            $record->entrada            =   $control[$tipo];
                                                            $record->latitud            =   $latitud;
                                                            $record->longitud           =   $longitud;
                                                            $record->created_at         =   Carbon::now("America/Guatemala")->ToDateTimeString();
                                                            $record->updated_at         =   Carbon::now("America/Guatemala")->ToDateTimeString();

                                                            if($record->save())
                                                                return $record;
                                                            else
                                                                throw new \Exception("Error al crear registro");
                                                        }
                                                        else
                                                        {
                                                            throw new \Exception("No cuentas con ventas del día para salir del PDV");
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $record                     =   new Asistencias;
                                                        $record->id_vendedor        =   $vendedor->id;
                                                        $record->tipo               =   $tipo;
                                                        $record->entrada            =   $control[$tipo];
                                                        $record->latitud            =   $latitud;
                                                        $record->longitud           =   $longitud;
                                                        $record->created_at         =   Carbon::now("America/Guatemala")->ToDateTimeString();
                                                        $record->updated_at         =   Carbon::now("America/Guatemala")->ToDateTimeString();

                                                        if($record->save())
                                                            return $record;
                                                        else
                                                            throw new \Exception("Error al crear registro");
                                                    }
                                                }
                                                else
                                                {
                                                    throw new \Exception("Ya cuentas con ".$control[$tipo]);
                                                }
                                            }
                                            else
                                            {
                                                throw new \Exception("El número no se encuentra registrado");
                                            }
                                        }
                                        else
                                        {
                                            throw new \Exception("El tipo de entrada no es válido");
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception("Algunos campos son obligatorios");
                                    }
                                                                                
                                });

            $control = array(
                            "1" => "Entrada a PDV",
                            "2" => "Salida a Almuerzo",
                            "3" => "Entrada a Almuerzo",
                            "4" => "Salida a PDV",
                           );

            $statusCode     =   200;
            $this->message  =   "Marcaje ".$control[$request->input("tipo")]." correcta";
            $this->result   =   true;
            $this->records  =   $nuevoRegistro;
        }
        catch(\Exception $e)
        {
            $statusCode =   200;
            $this->message  =   env( "APP_DEBUG" ) ? $e->getMessage() : "Registro no se creo";
            $this->result   =   false;
        }
        finally
        {
            $response = 
            [
                "message"   =>  $this->message,
                "result"    =>  $this->result,
                "records"   =>  $this->records,
                "ventas"    =>  $this->ventas
            ];
            
            return response()->json( $response , $statusCode );
        }
    }

    public function asistencias(Request $request)
    {
        $entradas_pdv = 0;
        $salidas_almuerzo = 0;
        $entradas_almuerzo = 0;
        $salidas_pdv = 0;
        try
        {
            $fecha_inicio = $request->input("fecha_inicio");
            $fecha_fin = $request->input("fecha_fin");

            $where = $fecha_inicio!="" && $fecha_fin!="" ? 
                     "asistencias.created_at BETWEEN '".date("Y-m-d", strtotime($fecha_inicio))." 00:00:00' AND '".date("Y-m-d", strtotime($fecha_fin))." 23:59:59'" :
                     "asistencias.created_at BETWEEN '".Carbon::now("America/Guatemala")->ToDateString()." 00:00:00' AND '".Carbon::now("America/Guatemala")->ToDateString()." 23:59:59'";

            $statusCode     =   200;
            $this->message  =   "Consultando registros";
            $this->result   =   true;
            $this->records  =   Asistencias::select("asistencias.*", "vendedores.nombre as nombre_vendedor", "vendedores.telefono as telefono")
                                                  ->leftJoin("vendedores", "vendedores.id", "=", "asistencias.id_vendedor")
                                                  ->whereRaw( $where )->get();  
            $entradas_pdv   =   Asistencias::whereRaw( $where." AND tipo = 1" )->count();
            $salidas_almuerzo   =   Asistencias::whereRaw( $where." AND tipo = 2" )->count();
            $entradas_almuerzo  =   Asistencias::whereRaw( $where." AND tipo = 3" )->count();
            $salidas_pdv    =   Asistencias::whereRaw( $where." AND tipo = 4" )->count();
        }
        catch(\Exception $e)
        {
            $statusCode =   200;
            $this->message  =   env( "APP_DEBUG" ) ? $e->getMessage() : "Registro no se creo";
            $this->result   =   false;
        }
        finally
        {
            $response = 
            [
                "message"   =>  $this->message,
                "result"    =>  $this->result,
                "records"   =>  $this->records,
                "entradas_pdv" => $entradas_pdv,
                "salidas_almuerzo" => $salidas_almuerzo,
                "entradas_almuerzo" => $entradas_almuerzo,
                "salidas_pdv" => $salidas_pdv
            ];
            
            return response()->json( $response , $statusCode );
        }
    }

    public function reporte_ventas(Request $request)
    {
        try
        {
            $fecha_inicio = $request->input("fecha_inicio");
            $fecha_fin = $request->input("fecha_fin");

            $where = $fecha_inicio!="" && $fecha_fin!="" ? 
                     "reportes.created_at BETWEEN '".date("Y-m-d", strtotime($fecha_inicio))." 00:00:00' AND '".date("Y-m-d", strtotime($fecha_fin))." 23:59:59'" :
                     "reportes.created_at BETWEEN '".Carbon::now("America/Guatemala")->ToDateString()." 00:00:00' AND '".Carbon::now("America/Guatemala")->ToDateString()." 23:59:59'";

            $statusCode     =   200;
            $this->message  =   "Consultando registros";
            $this->result   =   true;
            $this->records  =   ViewVendedores::select(DB::raw("view_vendedores.*, (SELECT count(id) FROM reportes WHERE reportes.id_vendedor = view_vendedores.id_vendedor AND ".$where.") as ventas"))->get(); 
        }
        catch(\Exception $e)
        {
            $statusCode =   200;
            $this->message  =   env( "APP_DEBUG" ) ? $e->getMessage() : "Ocurrio un problemas al consultar datos";
            $this->result   =   false;
        }
        finally
        {
            $response = 
            [
                "message"   =>  $this->message,
                "result"    =>  $this->result,
                "records"   =>  $this->records
            ];
            
            return response()->json( $response , $statusCode );
        }
    }

    public function reporte_asistencias(Request $request)
    {
    	try
    	{
    		$fecha_inicio = $request->input("fecha_inicio") != "" ? date("Y-m-d", strtotime($request->input("fecha_inicio"))) : Carbon::now("America/Guatemala")->toDateString();
    		$fecha_fin = $request->input("fecha_fin") != "" ? date("Y-m-d", strtotime($request->input("fecha_fin"))) : Carbon::now("America/Guatemala")->toDateString();

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

    		$statusCode = 200;
    		$this->result = true;
    		$this->message = "Registros consultados";
    		$this->records = $asistencias;
    	}
    	catch(\Exception $e)
    	{
    		$statusCode =   200;
            $this->message  =   env( "APP_DEBUG" ) ? $e->getMessage() : "Ocurrio un problemas al consultar datos";
            $this->result   =   false;
    	}
    	finally
    	{
    		$response = 
            [
                "message"   =>  $this->message,
                "result"    =>  $this->result,
                "fechas"	=>	$fechas,
                "records"   =>  $this->records
            ];
            
            return response()->json( $response , $statusCode );
    	}
    }

    public function reporte_marcajes(Request $request)
    {
        try
        {
            $fecha = $request->input("fecha") != "" && $request->input("fecha") != 'undefined' ? date("Y-m-d", strtotime($request->input("fecha"))) : Carbon::now("America/Guatemala")->toDateString();

            $vendedores = ViewVendedores::select(DB::raw("view_vendedores.*, 
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 1 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as entrada_pdv,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 2 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as salida_almuerzo,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 3 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as entrada_almuerzo,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 4 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as salida_pdv
                "))
            ->get(); 

            $statusCode = 200;
            $this->result = true;
            $this->message = "Registros consultados";
            $this->records = $vendedores;
        }
        catch(\Exception $e)
        {
            $statusCode =   200;
            $this->message  =   env( "APP_DEBUG" ) ? $e->getMessage() : "Ocurrio un problemas al consultar datos";
            $this->result   =   false;
        }
        finally
        {
            $response = 
            [
                "message"   =>  $this->message,
                "result"    =>  $this->result,
                "records"   =>  $this->records
            ];
            
            return response()->json( $response , $statusCode );
        }
    }

    public function exportar_ventas(Request $request)
    {
        try
        {
            \Excel::create('reporte_ventas', function($excel) {

                $excel->sheet('Ventas', function($sheet) {

                    $sheet->loadView('reportes.ventas');

                });
                $excel->sheet('Detalle', function($sheet) {

                    $sheet->loadView('reportes.detalle');

                });

            })->export("xlsx");
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }

    public function exportar_asistencias(Request $request)
    {
        try
        {
            \Excel::create('reporte_asistencias', function($excel) {

                $excel->sheet('Asistencias', function($sheet) {

                    $sheet->loadView('reportes.asistencias');

                });

            })->export("xlsx");
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }

    public function exportar_marcajes(Request $request)
    {
        try
        {
            $fecha = $request->input("fecha") != "" && $request->input("fecha") != 'undefined' ? date("Y-m-d", strtotime($request->input("fecha"))) : Carbon::now("America/Guatemala")->toDateString();

            $vendedores = ViewVendedores::select(DB::raw("view_vendedores.*, 
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 1 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as entrada_pdv,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 2 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as salida_almuerzo,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 3 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as entrada_almuerzo,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 4 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as salida_pdv
                "))
            ->get();

            \Excel::create('reporte_marcajes', function($excel) use($vendedores){

                $excel->sheet(date("d.m.Y"), function($sheet) use($vendedores){

                    $sheet->loadView('reportes.marcajes')->with("vendedores",$vendedores);

                });

            })->export("xlsx");
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }

    public function reporte_ventas_vendedor(Request $request)
    {
        try
        {
            $fecha_inicio = $request->input("fecha_inicio");
            $fecha_fin = $request->input("fecha_fin");
            $id_vendedor = $request->input("id_vendedor");

            $where = $fecha_inicio!="" && $fecha_fin!="" ? 
                     "created_at BETWEEN '".date("Y-m-d", strtotime($fecha_inicio))." 00:00:00' AND '".date("Y-m-d", strtotime($fecha_fin))." 23:59:59'" :
                     "created_at BETWEEN '".Carbon::now("America/Guatemala")->ToDateString()." 00:00:00' AND '".Carbon::now("America/Guatemala")->ToDateString()." 23:59:59'";

            $statusCode     =   200;
            $this->message  =   "Consultando registros";
            $this->result   =   true;
            $this->records  =   Reportes::whereRaw("id_vendedor = ? AND ".$where,[$id_vendedor])->get(); 
        }
        catch(\Exception $e)
        {
            $statusCode =   200;
            $this->message  =   env( "APP_DEBUG" ) ? $e->getMessage() : "Ocurrio un problemas al consultar datos";
            $this->result   =   false;
        }
        finally
        {
            $response = 
            [
                "message"   =>  $this->message,
                "result"    =>  $this->result,
                "records"   =>  $this->records
            ];
            
            return response()->json( $response , $statusCode );
        }
    }

}
