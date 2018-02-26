<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tareas extends Model
{
    protected $fillable = [
    	'nombre', 'descripcion', 'status', 'finalized_at', 'user_id',
    ];
    public function usuario(){
    	return $this->belongsTo('App\User', 'user_id', 'id')->withTrashed();
    }
}
