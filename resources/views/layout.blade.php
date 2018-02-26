<!DOCTYPE html>
<html lang="en">
<head>
		<link rel="stylesheet" href="/css/app.css">
		<script src="/js/all.js"></script>
	<nav class="navbar navbar-inverse" role="navigation">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand">Just Creative</a>
			</div>
	
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				@if(auth()->check())
				<ul class="nav navbar-nav">
					<li><a href="{!! route('user.show', ['id'=>auth()->user()->id]) !!}">Mis Tareas</a></li>
					@if(auth()->user()->role === 'admin')
					<li><a href="{{route('tareas.index')}}">Administrador de Tareas</a></li>
					@endif
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{auth()->user()->name}} <b class="caret"></b></a>
						<ul class="dropdown-menu">
							@if(auth()->user()->role === 'admin')
							<li><a href="{{route('user.index')}}">Administrador de usuarios</a></li>
							@endif
							<li><a href="/logout">Log Out</a></li>
						</ul>
					</li>
					@endif
				</ul>
			</div><!-- /.navbar-collapse -->
		</div>
	</nav>
</head>
<body>
	<div class="container">
		@yield('content')
	</div>
</body>