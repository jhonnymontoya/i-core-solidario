@component('mail::layout')
    @php
        $entidad = Auth::getSession()->get('entidad');
		if (!$entidad) {
			$url = env('APP_URL');
			$imagen = secure_asset('img/logos/ICore_96x96.png');
		}
		else {
			$url = $entidad->pagina_web;
			$imagen = $entidad->categoriaImagenes[0]->pivot->nombre;
			$imagen = secure_asset('storage/entidad/' . $imagen);
		}
    @endphp
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => $url])
            <img src="{{ $imagen }}">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ "I-Core." }}. Todos los derechos reservados.
        @endcomponent
    @endslot
@endcomponent
