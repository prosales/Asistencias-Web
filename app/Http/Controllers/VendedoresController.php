<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Vendedores;
use App\Telefonos;
use Carbon\Carbon;

class VendedoresController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode =    200;

    public function index()
    {
        try
        {
            $this->records     =   Vendedores::with("sucursal")->get();
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
                $Registro = Vendedores::create
                ([
                    'nombre'            =>  $request->input( 'nombre' ),
                    'codigo'            =>  $request->input( 'codigo' ),
                    'codigo_empleado'   =>  $request->input( 'codigo_empleado', '' ),
                    'telefono'          =>  $request->input( 'telefono' ),
                    'id_sucursal'       =>  $request->input( 'id_sucursal' ),
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
            $registro =   Vendedores::find($id);
            if($registro)
            {
                $registro->sucursal;
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
                $record                             =   Vendedores::find($id);
                $record->nombre                     =   $request->input( 'nombre', $record->nombre );
                $record->codigo                     =   $request->input( 'codigo', $record->codigo );
                $record->telefono                   =   $request->input( 'telefono', $record->telefono );
                $record->id_sucursal                =   $request->input( 'id_sucursal', $record->id_sucursal );
                $record->usuario                    =   $request->input( 'usuario', $record->usuario );
                $record->password                   =   $request->input( 'password', $record->password );
                
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
            $this->result       =   Vendedores::destroy($id);
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

    public function agregar_telefono(Request $request)
    {
        try
        {
            $Registro   =   DB::transaction(function() use ($request)
            {
                $validar = Telefonos::whereRaw("numero = ?", [$request->input("numero")])->first();
                if(!$validar)
                {
                    $Registro = Telefonos::create
                    ([
                        'id_vendedor'        =>  $request->input( 'id' ),
                        'numero'             =>  $request->input( 'numero' ),
                    ]);
                    if( !$Registro )            {throw new \Exception('Registro no se creo');}
                    else                        {return $Registro;}
                }
                else
                {
                    throw new \Exception('El numero ingresado ya existe');
                }
            });

            $this->records  =   $Registro;
            $this->message  =   "Registro creado";
            $this->result   =   true;
            $this->statusCode       =   200;
        }
        catch(\Exception $e)
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

    public function eliminar_telefono($id)
    {
        try
        {
            $this->result       =   Telefonos::destroy($id);
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
                $vendedor = Vendedores::whereRaw("usuario = ? AND password = ?", [$usuario, $password])->first();
                if($vendedor)
                {
                    $this->records      =   $vendedor;
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

}
