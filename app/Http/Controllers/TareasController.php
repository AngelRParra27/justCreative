<?php

namespace App\Http\Controllers;

use App\tareas;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TareasController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('admin', ['except' => ['update', 'show']]);
    }

    
    public function index()
    {
        $usuarios = User::all();
        $tareas = tareas::with('usuario')->get();
        
        foreach($tareas as $tarea){
            if($tarea->status == 0) $tarea->status = 'Cancelada';
            if($tarea->status == 1) $tarea->status = 'Pendiente';
            if($tarea->status == 2) $tarea->status = 'En proceso';
            if($tarea->status == 3) $tarea->status = 'Finalizada';
        }
        return view('tareas.index', compact('usuarios', 'tareas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tareas = tareas::create($request->all());
        return $tareas;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\tareas  $tareas
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       $tarea = tareas::find($id);
       if($tarea->status == 0) $tarea->status = 'Cancelada';
       if($tarea->status == 1) $tarea->status = 'Pendiente';
       if($tarea->status == 2) $tarea->status = 'En proceso';
       if($tarea->status == 3) $tarea->status = 'Finalizada';
       return $tarea;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\tareas  $tareas
     * @return \Illuminate\Http\Response
     */
    public function edit(tareas $tareas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\tareas  $tareas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $now= Carbon::now('CST');
        if($request->status != 0){
             $tarea = tareas::find($id);
             $tarea->status = $request->status;
             if($request->status == 3)
             {
                $tarea->finalized_at = $now;
             }else{
                $tarea->finalized_at = null;
             }
             $tarea->save();
             return ($tarea);
        }
        $tarea = tareas::find($id);
        $tarea->nombre = $request->nombre;
        $tarea->descripcion = $request->descripcion;
        $tarea->user_id = $request->user_id;
        $tarea->save();
        return ($tarea);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\tareas  $tareas
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        tareas::destroy($id);
    }
}
