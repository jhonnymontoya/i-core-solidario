@inject('menus', 'App\Helpers\MenuHelper')

<aside class="main-sidebar">
	<section class="sidebar">
		{{-- Sidebar user panel --}}
		<div class="user-panel">
			<div class="pull-left image">
				<img src="{{ asset('storage/avatars/' . (empty(Auth::user()->avatar)?'avatar-160x160.png':Auth::user()->avatar) ) }}" class="img-circle" alt="{{ Auth::user()->nombre_corto }}">
			</div>
			<div class="pull-left info">
				<p>{{ Auth::user()->nombre_corto }}</p>
				@if(Session::has('entidad'))
					<small>
						@if(!empty(Session::get('entidad')->terceroEntidad->sigla))
							{{ str_limit(Session::get('entidad')->terceroEntidad->sigla, 20) }}
						@else
							{{ str_limit(Session::get('entidad')->terceroEntidad->razon_social, 20) }}
						@endif
					</small>
				@endif
			</div>
		</div>
		{{-- Formulario de búsqueda --}}
		{{--<form action="#" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="Buscar...">
				<span class="input-group-btn">
					<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
				</span>
			</div>
		</form>--}}
		{{-- Fin de formulario de búsqueda --}}
		{{-- Menú lateral izquierdo --}}
		<ul class="sidebar-menu">
			<li class="header">MENÚ</li>
			@foreach ($menus->menus() as $menu)
				<li class="treeview{{ $menu->activo?' active':'' }}">
					<a href="{{ $menu->ruta?url($menu->ruta):'#' }}">
						@if ($menu->pre_icon)
							<i class="fa fa-{{ $menu->pre_icon }}"></i> 
						@endif
						<span>{{ $menu->nombre }}</span>
						@if ($menu->hijos->count() > 0)
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						@endif
					</a>
					@if ($menu->hijos->count() > 0)
						<ul class="treeview-menu">
							@foreach ($menu->hijos as $permisoHijo)
								<li class="{{ $permisoHijo->activo?'active':'' }}">
									<a href="{{ $permisoHijo->ruta?url($permisoHijo->ruta):'#' }}">
										@if ($permisoHijo->pre_icon)
											<i class="fa fa-{{ $permisoHijo->pre_icon }}"></i> 
										@endif
										{{ $permisoHijo->nombre }}
										@if ($permisoHijo->hijos->count() > 0)
											<span class="pull-right-container">
												<i class="fa fa-angle-left pull-right"></i>
											</span>
										@endif
									</a>
									@if ($permisoHijo->hijos->count() > 0)
										<ul class="treeview-menu">
											@foreach ($permisoHijo->hijos as $permisoHijo2)
												<li class="{{ $permisoHijo2->activo?'active':'' }}">
													<a href="{{ url($permisoHijo2->ruta) }}">
														@if ($permisoHijo2->pre_icon)
															<i class="fa fa-{{ $permisoHijo2->pre_icon }}"></i> 
														@endif
														{{ $permisoHijo2->nombre }}
														@if ($permisoHijo2->hijos->count() > 0)
															<span class="pull-right-container">
																<i class="fa fa-angle-left pull-right"></i>
															</span>
														@endif
													</a>
												</li>
											@endforeach
										</ul>
									@endif
								</li>
							@endforeach
						</ul>
					@endif
				</li>
			@endforeach
		</ul>
	</section>
</aside>