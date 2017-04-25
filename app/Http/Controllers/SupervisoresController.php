<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Supervisores;
use App\ViewVendedores;
use Carbon\Carbon;

class SupervisoresController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode =    200;

    public function index()
    {
        try
        {
            $this->records     =   Supervisores::all();
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
                $Registro = Supervisores::create
                ([
                    'nombre'            =>  $request->input( 'nombre' ),
                    'telefono'          =>  $request->input( 'telefono' ),
                    'usuario'           =>  $request->input( 'usuario' ),
                    'password'          =>  $request->input( 'password' )
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
            $registro =   Supervisores::find($id);
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
                $record                             =   Supervisores::find($id);
                $record->nombre                     =   $request->input( 'nombre', $record->nombre );
                $record->telefono                   =   $request->input( 'telefono', $record->telefono );
                $record->usuario                    =   $request->input( 'usuario', $record->usuario );
                $record->password                   =   $request->input( 'password', $record->password );
                $record->estado                     =   $request->input( 'estado', $record->estado );
                
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
            $this->result       =   Supervisores::destroy($id);
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

    public function login(Request $request)
    {
        try
        {
            $usuario = $request->input("usuario");
            $password = $request->input("password");

            if($usuario!="" && $password!="")
            {
                $registro = Supervisores::whereRaw("usuario = ? AND password = ?", [$usuario, $password])->first();
                if($registro)
                {
                    $this->records      =   $registro;
                    $this->result       =   true;
                    $this->message      =   "Bienvenido";
                    $this->statusCode   =   200;
                }
                else
                {
                    $this->result       =   false;
                    $this->message      =   "El usuario o password incorrecto";
                    $this->statusCode   =   200;
                }
            }
            else
            {
                $this->result       =   false;
                $this->message      =   "El usuario y password es obligatorio";
                $this->statusCode   =   200;
            }
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

    public function entradas_vendedores(Request $request)
    {
        try
        {
            $fecha = Carbon::now("America/Guatemala")->toDateString();
            
            $registros = ViewVendedores::select(DB::raw("view_vendedores.*, 
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 1 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as entrada_pdv,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 2 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as salida_almuerzo,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 3 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as entrada_almuerzo,
                (SELECT count(id) FROM asistencias WHERE asistencias.id_vendedor = view_vendedores.id_vendedor AND tipo = 4 AND created_at BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59') as salida_pdv
                "))
            ->where("id_supervisor", $request->input("id_supervisor"))
            ->get();

            $this->records = $registros;
            $this->result = true;
            $this->message = "Consulta exitosa";
            $this->statusCode = 200;
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
