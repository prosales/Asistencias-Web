<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use App\Marcas;

class MarcasController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode = 200;

    public function index()
    {
        try
        {
            $this->records = Marcas::all();
            $this->message = "Consulta exitosa";
            $this->result = true;
            $this->statusCode = 200;
        }
        catch(\Exception $e)
        {
            $this->statusCode = 200;
            $this->message = env('APP_DEBUG')?$e->getMessage():'Ocurrio un problema';
            $this->result = false;
        }
        finally
        {
            $respuesta = [
                'message' => $this->message,
                'result' => $this->result,
                'records' => $this->records
            ];

            return response()->json($respuesta, $this->statusCode);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $registro = Marcas::create([
                'nombre' => $request->input('nombre'),
                'es_vpr' => $request->input('es_vpr')
            ]);

            $this->records = $registro;
            $this->message = "Registro creado";
            $this->result = true;
            $this->statusCode = 200;
        }
        catch(\Exception $e)
        {
            $this->statusCode = 200;
            $this->message = env('APP_DEBUG')?$e->getMessage():'Ocurrio un problema al crear registroc';
            $this->result = false;
        }
        finally
        {
            $respuesta = [
                'message' => $this->message,
                'result' => $this->result,
                'records' => $this->records
            ];

            return response()->json($respuesta, $this->statusCode);
        }
    }

    public function show($id)
    {
        try
        {
            $registro =   Marcas::find($id);
            if($registro)
            {
                $this->records = $registro;
                $this->message = "Consulta exitosa";
                $this->result = true;
                $this->statusCode = 200;
            }
            else
            {
                $this->records = [];
                $this->message = "El registro no existe";
                $this->result = false;
                $this->statusCode = 200;
            }
        }
        catch(\Exception $e)
        {
            $this->message = "Registro no existe";
            $this->result = false;
            $this->statusCode = 200;
        }
        finally
        {
            $respuesta = [
                'records'   =>  $this->records,
                'message'   =>  $this->message,
                'result'    =>  $this->result,
            ];
            
            return response()->json($respuesta, $this->statusCode);
        }
    }

    public function update($id, Request $request)
    {
        try
        {
            $registro = Marcas::find($id);
            $registro->nombre = $request->input('nombre', $registro->nombre);
            $registro->es_vpr = $request->input('es_vpr', $registro->es_vpr);
            $registro->save();

            $this->records = $registro;
            $this->message = "Registro creado";
            $this->result = true;
            $this->statusCode = 200;
        }
        catch(\Exception $e)
        {
            $this->statusCode = 200;
            $this->message = env('APP_DEBUG')?$e->getMessage():'Ocurrio un problema al crear registroc';
            $this->result = false;
        }
        finally
        {
            $respuesta = [
                'message' => $this->message,
                'result' => $this->result,
                'records' => $this->records
            ];

            return response()->json($respuesta, $this->statusCode);
        }
    }

    public function destroy($id)
    {
        try
        {
            $this->result = Marcas::destroy($id);
            $this->message = "Eliminado correctamente";
            $this->statusCode = 200;
        }
        catch (\Exception $e)
        {
            $this->statusCode = 200;
            $this->message = env('APP_DEBUG')?$e->getMessage():'Registro no se actualizo';
            $this->result = false;
        }
        finally
        {
            $response = [
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'records'   =>  $this->records
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }
}
