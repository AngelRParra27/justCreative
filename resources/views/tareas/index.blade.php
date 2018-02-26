@extends('layout')
@section('content')
<style>
  .pull-right .dropdown-menu:after {
        left: auto;
      right: 13px;
  }
  .pull-right .dropdown-menu {
      left: auto;
      right: 0;
  }
</style>
<h1>Tareas</h1>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading ">
      <div class="form-inline text-right">
      <button id="addTask" class="left-block btn btn-primary pull-left">Nueva Tarea</button>
      <label for="">Usuarios</label>
      <select class="form-control" name="" id="users">
        <option selected="true" disabled="disabled">Seleccionar Usuario</option>
        @foreach($usuarios as $usuario)
            <option value="{{$usuario->id}}">{{$usuario->name}}</option>
        @endforeach
      </select>
      <label class="" for="finalizado"><input id="finalizado" type="checkbox" aria-label="test">  Finalizado</label>
      <label class="" for="proceso"><input id="proceso" type="checkbox" aria-label="test">  En proceso  </label>  
      <label class="" for="pendiente"><input id="pendiente" type="checkbox" aria-label="test">  Pendiente</label>
      </div>     
  </div>
  <table class="table">
  	<thead>
  		<th>Tarea</th>
  		<th>Usuario asignado</th>
  		<th>Status</th>
  		<th></th>
  	</thead>
  	<tbody>
  		@foreach($tareas as $tarea)
  			<tr id="{{$tarea->id}}" data-status="{{$tarea->status}}">
  				<td>{{$tarea->nombre}}</td>
          <td style="display: none;" >{{$tarea->descripcion}}</td>
  				<td>{{$tarea->usuario->name}}</td>
  				<td style="display: none;">{{$tarea->user_id}}</td>
  				<td>{{$tarea->status}}</td>
  				<td>
  					<button id="edit" data-id="{{$tarea->id}}" type="button" class="btn btn-default" aria-label="Left Align">
 						 <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
					</button>
					<button id="delete" type="button" class="btn btn-default" aria-label="Left Align">
 						 <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
					</button>
  				</td>
  			</tr>
  		@endforeach
  	</tbody>
  </table>
  </div>
  
{{--Modal para ver detalles--}}
<div class="modal fade" id="detailsModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="tituloDetalles">Detalles</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <h3 id="nombre">Nombre Test</h3>
          </div>
          <div class="col-sm-12">
            <h4>Descripcion</h4>
            <textarea disabled="" class="form-control" id="descripcion" rows="5"> </textarea>
          </div>
          <div class="col-sm-6">
            <h4>Fecha de crecion</h4>
            <p id="fecha"> </p>
          </div>
          <div class="col-sm-6" id="finalizacion">
            <h4>Fecha de finalizacion</h4>
            <p id="fechaFin"></p>
          </div>
          <div class="col-sm-6" id="tiempoProceso">
            <h4>Tiempo en proceso</h4>
            <p id="tiempo"> </p>
          </div>
          <div class="col-sm-6" id="tiempoF">
            <h4>Finalizada en</h4>
            <p id="horas"> </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{--Modal para editar o agregar tarea--}}
<div class="modal fade" id="taskModal">
	<div class="modal-dialog modal-m">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="titulo">Nueva Tarea</h4>
			</div>
			<div class="modal-body">
				<meta name="csrf-token" content="{{ csrf_token() }}">
				<input type="hidden" id="data-id" value="">
				<div class="row">
					<div class="form-group col-sm-6">
						<label for="name">Name</label>
						<input type="text" class="form-control" name="name" id="name">
					</div>
					<div class="form-group col-sm-6">
						<label for="usuarios" class="control-label">Usuario</label>
						<select id="usuarios" class="form-control" name="usarios">
                				@foreach($usuarios as $usuario)
                					<option value="{{$usuario->id}}">{{$usuario->name}}</option>
                				@endforeach
            			</select>
					</div>
					<div class="form-group col-sm-12">
						<label for="name">Descripcion</label>
						<textarea rows="5" class="form-control" name="descripcion" id="descripciont"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="saveTask">Save</button>
			</div>
				
		</div>
	</div>
</div>

<div class="modal fade" id="deleteModal"> 
  <div class="modal-dialog modal-s">  
    <div class="modal-content"> 
      <div class="modal-header"> 
        Eliminar
      </div>
      <div class="modal-body">  
        <input type="hidden" id="task-id" value="">
        <h3>¿Estas seguro que quieres eliminar esta tarea?</h3>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="deleteTask">Si</button>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function(){
    $('#proceso').prop('checked', true);
    $('#finalizado').prop('checked', true);
    $('#pendiente').prop('checked', true);
    rowStatus();
  })
 
//////carga datos completos de una tarea y los muestra en modal///
  $(document).on('dblclick', 'tr', function(){
    $('#tiempoProceso').hide();
    $.ajax({
      type: 'GET',
      url: "{!! url('tareas') !!}"  + "/" + $(this).attr('id'),
      success: function(data){
        var id= data.id; 
        var date1 = new Date(data.created_at)
        var date2 = new Date(data.finalized_at)
        var today = new Date();
        var hours = Math.round(Math.abs(date1.getTime() - date2.getTime())  / 36e5);
        var tiempo = Math.round(Math.abs(date1.getTime() - today.getTime())  / 36e5);
        $('#nombre').text(data.nombre);
        $('#descripcion').text(data.descripcion);
        $('#fecha').text(date1)
        $('#status').text(data.status)
        $('#aceptar').data('id', id);
        if(data.finalized_at != null){
          $('#fechaFin').text(date2);
          $('#finalizacion').show();
          $('#tiempoF').show();
          $('#horas').text(hours + ' horas.');
          $('#tiempoProceso').hide();
        }else{
          $('#finalizacion').hide();
          $('#tiempoF').hide();
        }
        if(data.status == 'En proceso'){
          $('#tiempo').text(tiempo + 'horas.')
          $('#tiempoProceso').show()
        }
        $('#detailsModal').modal('show')
      }
    })
  })
	////abre modal para agregar tarea////
	$(document).on('click', '#addTask', function(){
		$('#editTask').attr('id', 'saveTask');
		$('#taskModal').modal('show');
	});
  ///agrega una tarea
	$(document).on('click', '#saveTask', function(){
		$.ajaxSetup({
   		 headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   		 }
    	});
    	$.ajax({
    		type: 'POST',
    		url: "{!! url('tareas') !!}",
    		data:{
    			'nombre': $('#name').val(),
    			'descripcion': $('#descripciont').val(),
    			'user_id': $('#usuarios :selected').val(),
    		},
    		success: function(data){
    			$('tbody').append(
            '<tr id="'+data.id+'">' + 
            '<td>' + data.nombre + '</td>' +
            '<td style="display: none;">' + data.descripcion + '</td>' +
            '<td>' + $('#usuarios :selected').text() + '</td>' +
            '<td>' + 'Pendiente' + '</td>' +
            '<td>' + '<button id="edit" data-id="' +data.id+ '" type="button" class="btn btn-default" aria-label="Left Align">' +
                            '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>' +
                     '</button>' +
                      '<button id="delete" type="button" class="btn btn-default" aria-label="Left Align">' +
                            '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>' +
                      '</button>' +
            '</tr>'
            )
    		}
    	})
	})
	/////Abre modal para editar tarea////
	$(document).on('click', '#edit', function(e){
    e.preventDefault();
    e.stopPropagation();
		var row = $(this).closest('tr');
		$('#name').val(row.find('td:eq(0)').text());
		$('#descripciont').val(row.find('td:eq(1)').text());
		$('#usuarios option[value="'+ row.find('td:eq(2)').text() +'"]').attr('selected', 'selected');
		$('#saveTask').attr('id', 'editTask');
		$('#data-id').val($(this).data('id'));
		$('#taskModal').modal('show');
	})
//////Edita una tarea///
	$(document).on('click', '#editTask', function(){
		$.ajaxSetup({
   		 headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   		 }
    	});
    	$.ajax({
    		type: 'PUT',
    		url: "{!! url('tareas') !!}"  + "/" + $('#data-id').val(),
    		data:{
    			'nombre': $('#name').val(),
    			'descripcion': $('#descripciont').val(),
    			'user_id': $('#usuarios :selected').val(),
    		},
    		success: function(data){
    			$('#' + data.id).find('td:eq(0)').text(data.nombre);	
    			$('#' + data.id).find('td:eq(1)').text(data.descripcion);
    			$('#' + data.id).find('td:eq(2)').text($('#usuarios :selected').text());
    			$('#taskModal').modal('hide');
    		}
    	})
	})
//////Cambie el color de las linas dependiendo el status de esta//
  function rowStatus(){
    $('tbody tr').each(function(){
      if($(this).data('status') == 'En proceso'){
        $(this).addClass('warning');
      }
      if($(this).data('status') == 'Finalizada'){
        $(this).addClass('success');
      }
      if($(this).data('status') == 'Pendiente'){
        $(this).addClass('pendiente');
      }
    })
  }

  //Filtrados por status//
   $(document).on('click', '#finalizado', function(){
    if($(this).is(':checked')){
      $('.success').show();

    }else{
      $('.success').hide(); 
    }
  })

  $(document).on('click', '#proceso', function(){
    if($(this).is(':checked')){
      $('.warning ').show();

    }else{
      $('.warning').hide(); 
    }
  })
  $(document).on('click', '#pendiente', function(){
    if($(this).is(':checked')){
      $('.pendiente ').show();

    }else{
      $('.pendiente').hide(); 
    }
  })
  //////filtrado por usuario/////
  $(document).on('change', '#users', function(){
    $.ajaxSetup({
       headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
      });
    $.ajax({
      type : 'GET',
      url: "{!! url('user') !!}"  + "/" + $('#users :selected').val(),
      success: function(data){
        $('tbody').empty();
        data.tareas.forEach(function(tarea){
          if(tarea.status == '1')tarea.status = 'Pendiente';
          if(tarea.status == '2')tarea.status = 'En proceso';
          if(tarea.status == '3')tarea.status = 'Finalizada';
           $('tbody').append(
                '<tr id="' +tarea.id+ '" data-status="' + tarea.status + '">' +
                '<td>' + tarea.nombre + '</td>' +
                '<td>' + data.name + '</td>' +  
                '<td>' + tarea.status + '</td>' + 
                '<td>' + '<button id="edit" data-id="' +tarea.id+ '" type="button" class="btn btn-default" aria-label="Left Align">' +
                            '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>' +
                         '</button>' +
                         '<button id="delete" type="button" class="btn btn-default" aria-label="Left Align">' +
                            '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>' +
                         '</button>' +
                '</tr>'
            );
           rowStatus();
        })
      }
    })
  })

  ///abre modal de confirmacion para eliminar tarea//
  $(document).on('click', '#delete', function(){
    id = $(this).closest('tr').attr('id')
    $('#task-id').val(id);
    $('#deleteModal').modal('show');
  })
  $('#deleteTask').click(function(){
    var id = $('#task-id').val();
     $.ajaxSetup({
       headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
      });
     $.ajax({
      type:'DELETE',
      url: "{!! url('tareas') !!}"  + "/" + id,
      success: function(data){
        console.log(data)
          $('#' + id).remove();
          $('#deleteModal').modal('hide');
      }
     })
  })

</script>
@stop