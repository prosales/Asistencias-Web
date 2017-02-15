<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Sucursales;
use Carbon\Carbon;

class SucursalesController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode =    200;

    public function index()
    {
        try
        {
            $this->records     =   Sucursales::all();
            $this->message     =   "Consulta exitosa";
            $this->result      =   true;
            $this->statusCode  =   200;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se actualizo';
            $this->result       =   false;
        }
        finally
        {
            $IndexRespuesta = 
            [
                'records'   =>  $this->records,
                'message'   =>  $this->message,
                'result'    =>  $this->result,
            ];
            
            return response()->json($IndexRespuesta, $this->statusCode);
        }
    }

    public function store(request $request)
    {
        try
        {
            $Registro   =   DB::transaction(function() use ($request)
            {
                $Registro = Sucursales::create
                ([
                    'nombre'        =>  $request->input( 'nombre' ),
                    'cadena'        =>  $request->input( 'cadena' ),
                    'tienda'        =>  $request->input( 'tienda' ),
                    'direccion'     =>  $request->input( 'direccion' ),
                    'departamento'  =>  $request->input( 'departamento' ),
                    'municipio'     =>  $request->input( 'municipio' ),
                    'region'        =>  $request->input( 'region' ),
                    'supervisor'    =>  $request->input( 'supervisor' ),
                    'hora_entrada'  =>  $request->input( 'hora_entrada' ),
                    'hora_almuerzo' =>  $request->input( 'hora_almuerzo' ),
                    'hora_salida'   =>  $request->input( 'hora_salida' ),
                    'latitud'       =>  $request->input( 'latitud' ),
                    'longitud'      =>  $request->input( 'longitud' )
                ]);

                if( !$Registro )            {throw new \Exception('Registro no se creo');}
                else                        {return $Registro;}
            });
            $this->records      =   $Registro;
            $this->statusCode   =   200;
            $this->message      =   "Registro creado exitosamente";
            $this->result       =   true;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se actualizo';
            $this->result       =   false;
        }
        finally
        {
            $Index = 
            [
                'records'   =>  $this->records,
                'message'   =>  $this->message,
                'result'    =>  $this->result,
            ];
            
            return response()->json($Index, $this->statusCode);
        }
    }

    public function show($id)
    {
        try
        {
            $registro =   Sucursales::find($id);
            if($registro)
            {
                $this->records      =   $registro;
                $this->message      =   "Consulta exitosa";
                $this->result       =   true;
                $this->statusCode   =   200;
            }
            else
            {
                $this->records  =   [];
                $this->message      =   "El registro no existe";
                $this->result       =   false;
                $this->statusCode   =   200;
            }
        }
        catch (\Exception $e)
        {
            $this->message      =   "Registro no existe";
            $this->result       =   false;
            $this->statusCode   =   200;
        }
        finally
        {
            $Resultados = 
            [
                'records'   =>  $this->records,
                'message'   =>  $this->message,
                'result'    =>  $this->result,
            ];
            
            return response()->json($Resultados, $this->statusCode);
        }
    }

    public function update($id, Request $request)
    {
        try
        {
            $Registro   =   DB::transaction(function() use ($request,$id)
            {
                $record                             =   Sucursales::find($id);
                $record->codigo                     =   $request->input( 'codigo', $record->codigo );
                $record->cadena                     =   $request->input( 'cadena', $record->cadena );
                $record->tienda                     =   $request->input( 'tienda', $record->tienda );
                $record->direccion                  =   $request->input( 'direccion', $record->direccion );
                $record->departamento               =   $request->input( 'departamento', $record->departamento );
                $record->municipio                  =   $request->input( 'municipio', $record->municipio );
                $record->region                     =   $request->input( 'region', $record->region );
                $record->supervisor                 =   $request->input( 'supervisor', $record->supervisor );
                $record->hora_entrada               =   $request->input( 'hora_entrada', $record->hora_entrada );
                $record->hora_almuerzo              =   $request->input( 'hora_almuerzo', $record->hora_almuerzo );
                $record->hora_salida                =   $request->input( 'hora_salida', $record->hora_salida );
                $record->latitud                    =   $request->input( 'latitud', $record->latitud );
                $record->longitud                   =   $request->input( 'longitud', $record->longitud );
                
                $record->save();
                return $record;                                 
            });

            $this->records  =   $Registro;
            $this->message  =   "Actualizacion exitosa";
            $this->result   =   true;
            $this->statusCode       =   200;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se actualizo';
            $this->result       =   false;
        }
        finally
        {
            $response = 
            [
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'records'   =>  $this->records
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

    public function destroy($id)
    {
        try
        {
            $this->result       =   Sucursales::destroy($id);
            $this->message      =   "Eliminado correctamente";
            $this->statusCode   =   200;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se actualizo';
            $this->result       =   false;
        }
        finally
        {
            $response = 
            [
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'records'   =>  $this->records
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }


}
