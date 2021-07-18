<?php
    $layout = 'layouts.invitado';
    if(Auth::getUser() instanceof \App\Models\Sistema\Usuario){
        $layout = 'layouts.admin';
    }
    elseif(Auth::getUser() instanceof \App\Models\Sistema\UsuarioWeb){
        $layout = 'layouts.consulta';
    }
    else{
        $layout = 'layouts.invitado';
    }
?>
@extends($layout)

@section('content')
<div class="content-wrapper">
    <section class="content">
        <br>
        <div class="row justify-content-md-center">
            <div class="info-box bg-gradient-danger col-md-8">
                <span class="info-box-icon"><i class="fas fa-exclamation"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">404</span>
                    <span class="info-box-text">No se encontró el elemento solicitado.</span>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('style')
@endpush

@push('scripts')
@endpush
