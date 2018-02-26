@extends('layout')
@section('content')
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading text-center">Usuarios</div>

  <!-- Table -->
  <table class="table">
	<thead>
		<th class="text-center">Nombre</th>
		<th class="text-center">Correo</th>
		<th class="text-center">Tipo</th>
		<th class="text-center">Actions</th>
	</thead>
	<tbody>
		@foreach($usuarios as $usuario)
			<tr id="{{$usuario->id}}">
				<td class="text-center">{{$usuario->name}}</td>
				<td class="text-center">{{$usuario->email}}</td>
				<td class="text-center">{{$usuario->role}}</td>
				<td class="text-center">
					<button id="edit" data-id="{{$usuario->id}}" type="button" class="btn btn-default" aria-label="Left Align">
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
<button id="addUser" class="center-block btn btn-primary">Add User</button>

{{--Modal para editar o agregar usario--}}
<div class="modal fade" id="userModal">
	<div class="modal-dialog modal-m">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="titulo">Add Contact</h4>
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
						<label for="email">Email</label>
						<input type="email" class="form-control" name="email" id="email">
					</div>
					<div id="passwordDiv" class="form-group col-sm-6">
						<label for="password">Password</label>
						<input type="password" class="form-control" name="password" id="password">
					</div>
					<div class="form-group col-sm-6">
						<label for="tipo">Tipo de usuario</label>
						<select class="form-control" name="tipo" id="tipo">
							<option value="admin">Administrador</option>
							<option value="user">Usuario</option>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="save">Save</button>
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
        <input type="hidden" id="user-id" value="">
        <h3>¿Estas seguro que quieres eliminar este usuario?</h3>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="deleteUser">Si</button>
      </div>
    </div>
  </div>
</div>
<script>
	////abre modal para editar usuario///
	$(document).on('click', '#edit', function(){
		var row = $(this).closest('tr');
		$('#passwordDiv').hide();
		$('#name').val(row.find('td:eq(0)').text());
		$('#email').val(row.find('td:eq(1)').text());
		$('#data-id').val($(this).data('id'));
		$('#userModal').modal('show');
	});
	///abre modal para crear usuario////
	$(document).on('click', '#addUser', function(){
		$('#passwordDiv').show();
		$('#save').attr('id', 'createUser');
		$('#userModal').modal('show');
	});
	///Funcion para crear usuario///
	$(document).on('click', '#createUser', function(){
		$.ajaxSetup({
   		 headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   		 }
    	});
    	$.ajax({
    		type: 'POST',
    		url: "{!! url('user') !!}",
    		data:{
    		   'name': $('#name').val(),
    		   'email': $('#email').val(),
    		   'password': $('#password').val(),
    		   'tipo': $('#tipo :selected').val(),
    		},
    		success: function(data){
    			$('tbody').append(
    					'<tr id="'+data.id+'">' +
    					'<td class="text-center">' + data.name +'</td>' +
    					'<td class="text-center">' + data.email +'</td>' +
    					'<td class="text-center">' + data.role +'</td>' +
    					'<td class="text-center">' + '<button id="edit" data-id="' +data.id+ '" type="button" class="btn btn-default" aria-label="Left Align">' +
                            '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>' +
                         '</button>' +
                         '<button id="delete" type="button" class="btn btn-default" aria-label="Left Align">' +
                            '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>' +
                         '</button>' +
                		 '</tr>'
    				)
    			$('#userModal').modal('hide');

    		},
    		error: function(data){
    			 var errors = data.responseJSON;
    		}
    	})
	});
    ///Funcion para editar usuario///
	$(document).on('click', '#save', function(){
		var id =  $('#data-id').val();
		$.ajaxSetup({
   		 headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   		 }
    	});
    	$.ajax({
    		type:'PUT',
    		url: "{!! url('user') !!}" + "/" + id,
    		data:{
    			'name': $('#name').val(),
    			'email': $('#email').val(),
    			'tipo': $('#tipo :selected').val(),
    		},
    		success: function(data){
    			$('#' + data.id).find('td:eq(0)').text(data.name);
    			$('#' + data.id).find('td:eq(1)').text(data.email);
    			$('#' + data.id).find('td:eq(2)').text($('#tipo :selected').val());
    			$('#userModal').modal('hide');

    		}
    	});
	})
	 ///abre modal de confirmacion para eliminar usuario//
  $(document).on('click', '#delete', function(){
    id = $(this).closest('tr').attr('id')
    $('#user-id').val(id);
    $('#deleteModal').modal('show');
  })
  $('#deleteUser').click(function(){
    var id = $('#user-id').val();
     $.ajaxSetup({
       headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
      });
     $.ajax({
      type:'DELETE',
      url: "{!! url('user') !!}"  + "/" + id,
      success: function(data){
        console.log(data)
          $('#' + id).remove();
          $('#deleteModal').modal('hide');
      }
     })
  })
</script>
@stop