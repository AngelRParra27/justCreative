@extends('layout')
@section('content')
<style>
	#descripcion{
		box-shadow: none;
		border: 0px;
		background-color: white;
	}
	.pull-right .dropdown-menu:after {
   	    left: auto;
    	right: 13px;
	}
	.pull-right .dropdown-menu {
    	left: auto;
    	right: 0;
	}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<h1>Tareas</h1>
<div class="row">
	<div id="tareas" class="col-sm-12">
		<div class="panel panel-default"  >
  			<div class="panel-heading text-right">
  				<div class="dropdown">
  					<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
  					
  				    <span class="glyphicon glyphicon-chevron-down"></span>
  					</button>
  				    <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
  				    	<li><a><input id="finalizado" type="checkbox" aria-label="test">  Finalizado</a></li>
  				    	<li><a><input id="proceso" type="checkbox" aria-label="test">  En proceso</a></li>
  				    </ul>
  				</div>
  			</div>
  			<div class="panel-body" style="max-height: 700px;overflow-y: scroll">
  				<table class="table table-hover">
  				@if($usuario->tareas->isEmpty())
  						<h5 align="center">Aun no tienes tareas asignadas</h5>
  				@else
  				<thead>
					<th class="text-center"></th>
  					<th class="text-center">Nombre</th>
  				</thead>
  				<tbody>
  					
  					@foreach($usuario->tareas as $tarea)
  						<tr id="{{$tarea->id}}" data-status="{{$tarea->status}}">
  							<td class="text-center"><input id="test" type="checkbox" aria-label="..."></td>
  							<td class="text-center">{{$tarea->nombre}}</td>
  							<td style="display: none;">{{$tarea->user_id}}</td>
  						</tr>
  					@endforeach
  				</tbody>
  				@endif
  			</table>
  			</div>
  			
		</div>
	</div>
	<div id="detalles" class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">Detalles</div>
			<div class="row">
				<div class="container-fluid">
					<div class="col-sm-12">
						<h2 id="nombre">tarea test</h2>
						<hr class="style1">
					</div>
					
					<div class="col-sm-12">
						<h4>Descripción</h4>
						<textarea disabled="" class="form-control" id="descripcion" rows="5"> </textarea>
					</div>
					<div class="col-sm-6">
						<h4>Fecha de creacion</h4>
						<p id=fecha>19 de febrero del 2016</p>
					</div>
					<div id="finalizacion"	class="col-sm-6">
						<h4>Fecha de finalizacion</h4>
						<p id="fechaFin"></p>
					</div>
					<div class="col-sm-6">
						<h4>Estatus</h4>
						<p id="status">Pendiente</p>
					</div>
					<div id="tiempo" class="col-sm-6">
						<h4>Finalizada en</h4>
						<p id="horas"></p>
					</div>
					<div class="col-sm-12">
						<button id="aceptar" data-id="" type="button" class="btn btn-primary" aria-label="Left Align">
 						 <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aceptar
						</button>
						<br><br>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>

<script>
	$(document).ready(function(){
		rowStatus();
		$('#proceso').prop('checked', true);
		$('#finalizacion').hide();
		$('#tiempo').hide();
		$('#detalles').hide();	
	});

	$('tbody tr').on('click', function(){
		$('tbody tr').removeClass('active')
		$('#tareas').removeClass('col col-sm-12').addClass('col col-sm-6');
		$('#detalles').show('3000');
		$(this).closest('tr').addClass('active')
		$.ajax({
			type: 'GET',
			url: "{!! url('tareas') !!}"  + "/" + $(this).closest('tr').attr('id'),
			success: function(data){
				var id= data.id; 
				var date1 = new Date(data.created_at)
				var date2 = new Date(data.finalized_at)
				var hours = Math.round(Math.abs(date1.getTime() - date2.getTime())  / 36e5);
				$('#nombre').text(data.nombre);
				$('#descripcion').text(data.descripcion);
				$('#fecha').text(date1)
				$('#status').text(data.status)
				$('#aceptar').data('id', id);
				if(data.finalized_at != null){
					$('#fechaFin').text(date2);
					$('#finalizacion').show();
					$('#horas').text(hours + ' horas.');
					$('#tiempo').show();
				}else{
					$('#finalizacion').hide();
					$('#tiempo').hide();
				}
				data.status != 'Pendiente' ? $('#aceptar').hide() : $('#aceptar').show()


			}
		})
	})
	$(document).on('click', '#finalizado', function(){
		if($(this).is(':checked')){
			$('.success').show();

		}else{
			$('.success').hide();	
		}
	})

	$(document).on('click', '#proceso', function(){
		if($(this).is(':checked')){
			$('.warning').show();

		}else{
			$('.warning').hide();	
		}
	})

	$(document).on('click', '#test', function(){
		var id = $(this).closest('tr').attr('id');
		if($(this).is(':checked')){
			changeStatus(3, id)

		}else{
			
			changeStatus(2, id)
		}
		
	})

	$(document).on('click', '#aceptar', function(){
		var id = $(this).data('id');
		changeStatus(2, id);
	})

	function rowStatus(){
		$('tbody tr').each(function(){
			if($(this).data('status') == 2){
				$(this).addClass('warning');
			}
			if($(this).data('status') == 3){
				$(this).addClass('success');
				$(this).find('input:checkbox').prop('checked', true);
			}
		})
	}
	function changeStatus(status, id){
		$.ajaxSetup({
   		 headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   		 }
    	});
    	$.ajax({
    		type: 'PUT',
    		url: "{!! url('tareas') !!}"  + "/" + id,
    		data:{
    			'status': status,
    		},
    		success: function(data){
    			if(status == 3){
    				$('#'+data.id).addClass('success').removeClass('warning');
    			}else{
    				$('#'+data.id).addClass('warning').removeClass('success');
    			}
    			$('#aceptar').hide() 
    			console.log(data);
    		}
    	})
	}
</script>


@stop
