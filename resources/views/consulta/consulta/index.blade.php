@extends('layouts.consulta')

@section('content')
<div class="content-wrapper">
	<section class="content">
		<div class="jumbotron mt-3">
			<h1 class="display-4">Hola, {{ Str::title($socio->tercero->primer_nombre) }}!</h1>
			<p class="lead">Bienvenid{{ ($genero->masculino ? 'o' : 'a') }} a tu oficina web, un espacio diseñado para mantenerte en contacto con tus productos y servicios.</p>
			<hr class="my-4">
			<p>Haz click en una categoría para obtener información adicional.</p>

            @if($modulo->esta_activo)
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-right">
                            @if($modulo->play_store)
                                <a href="{{ $modulo->play_store }}" target="_blank" rel="noreferrer noopener">
                                    <img width="160" src="{{ asset('img/googleplay.png') }}">
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
		</div>

	</section>
</div>
@endsection

@push('style')
@endpush

@push('scripts')
@endpush
