@extends('index')
@section('content')
    <div class="row">
        <div class="col-12 text-muted ">
            <p>Hola, {{$user->first_name}}  {{$user->first_lastname}}.</p>
            <p>Emitimos este correo electronico porque recibimos una solicitud de restablecimiento de contraseña para su
                cuenta.
            </p>
            <p>Tiene 10 minutos para poder restablecer su contrasena, despues de ese tiempo el enlace ya no es
                valido.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <a class="btn btn-primary text-center"
               href="{{ env('CLIENT_URL') }}/#/auth/password-reset?token={{$token}}&username={{$user->username}}">
                Restablecer Contraseña</a>
            <p class="text-muted">
                Si no puede acceder, copie la siguiente url:
            </p>
            <p>
                {{env('CLIENT_URL')}}/#/auth/password-reset?token={{$token}}&username={{$user->username}}
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-muted">
            <p>Si no solicito un restablecimiento de contraseña, no se requiere ninguna otra accion.</p>
        </div>
    </div>
@endsection
