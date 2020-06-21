@inject('menus', 'App\Helpers\MenuHelper')

<aside class="main-sidebar sidebar-light-danger elevation-0">
	<a href="{{ url('/dashboard') }}" class="brand-link">
		<img src="{{ asset('img/I-Core.png') }}" alt="I-Core" class="brand-image img-circle">
		<span class="brand-text font-weight-light">I-Core</span>
	</a>

	<section class="sidebar">
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			<div class="image">
				<img src="{{ asset('storage/avatars/' . (empty(Auth::user()->avatar)?'avatar-160x160.png':Auth::user()->avatar) ) }}" class="img-circle elevation-2" alt="{{ Auth::user()->nombre_corto }}">
			</div>
			<div class="info">
				<a href="{{ url('profile') }}" class="d-block">{{ Auth::user()->nombre_corto }}</a>
			</div>
		</div>

		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				@foreach ($menus->menus() as $menu)
					<li class="nav-item has-treeview {{ $menu->activo?'menu-open':'' }}">
						<a href="{{ $menu->ruta?url($menu->ruta):'#' }}" class="nav-link {{ $menu->activo?'active':'' }}">
							@if ($menu->pre_icon)
								<i class="fas fa-{{ $menu->pre_icon }}"></i> 
							@endif
							<p>
								{{ $menu->nombre }}
								@if ($menu->hijos->count() > 0)
									<i class="right fas fa-angle-left"></i>
								@endif
							</p>
						</a>
						@if ($menu->hijos->count() > 0)
							<ul class="nav nav-treeview {{ $menu->activo?'menu-open':'' }}">
								@foreach ($menu->hijos as $permisoHijo)
									<li class="nav-item">
										<a href="{{ url($permisoHijo->ruta) }}" class="nav-link {{ $permisoHijo->activo?'active':'' }}">
											@if ($permisoHijo->pre_icon)
												<i class="fas fa-{{ $permisoHijo->pre_icon }} nav-icon"></i> 
											@endif
											<p>{{ $permisoHijo->nombre }}</p>
											@if ($permisoHijo->hijos->count() > 0)
												<i class="right fas fa-angle-left"></i>
											@endif
										</a>
										@if ($permisoHijo->hijos->count() > 0)
											<ul class="nav nav-treeview">
												@foreach ($permisoHijo->hijos as $permisoHijo2)
													<li class="nav-item">
														<a href="{{ url($permisoHijo2->ruta) }}" class="nav-link {{ $permisoHijo2->activo?'active':'' }}">
															@if ($permisoHijo2->pre_icon)
																<i class="fas fa-{{ $permisoHijo2->pre_icon }} nav-icon"></i> 
															@endif
															<p>{{ $permisoHijo2->nombre }}</p>
															@if ($permisoHijo2->hijos->count() > 0)
																<i class="right fas fa-angle-left"></i>
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
		</nav>
	</section>
</aside>