<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Usuarios;

class UsuariosController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode =    200;

    public function index()
    {
        try
        {
            $this->records     =   Usuarios::all();
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
                $Registro = Usuarios::create
                ([
                    'nombre'        =>  $request->input( 'nombre' ),
                    'usuario'       =>  $request->input( 'usuario' ),
                    'password'      =>  bcrypt($request->input('password')),
                    'tipo'			=>	$request->input( 'tipo' )
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
            $registro =   Usuarios::find($id);
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
                $record                             =   Usuarios::find($id);
                $record->nombre                     =   $request->input( 'nombre', $record->nombre );
                $record->usuario                    =   $request->input( 'usuario', $record->usuario );
                $record->tipo                    	=   $request->input( 'tipo', $record->tipo );
                if($request->input('password'))
                    $record->password                      =   bcrypt($request->input( 'password'));
                
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
            $this->result       =   Usuarios::destroy($id);
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
        
        $rules = array(
            'usuario'  => 'required', 
            'password' => 'required|min:3'
        );

        
        $validator = \Validator::make($request->all(), $rules);
        $response = [];
        
        if ($validator->fails()) {

         $response = 
            [
                'message'   =>  "Todos los campos son obligatorios",
                'result'    =>  false,
                'records'   => []
            ];
        } else {

            $userdata = array(
                'usuario'     => $request->input('usuario'),
                'password'  => $request->input('password')
            );

            
            if (\Auth::attempt($userdata)) {
                
                $response = 
                [
                    'message'   =>  "Bienvenido",
                    'result'    =>  true,
                    'records'   => \Auth::user()
                ];

            } else {        
                $response = 
                [
                    'message'   =>  "Credenciales incorrectas",
                    'result'    =>  false,
                    'records'   => []
                ];
                
                

            }

        }
        return response()->json($response, $this->statusCode);
    }

}
