<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{env('APP_NAME')}}</title>
    <style>

    </style>
</head>
<body>
<div class="content">
    <div class="row">
        <div class="col-10 offset-1 border">
            <h1 class="text-center bg-secondary text-white">{{env('APP_NAME')}}</h1>
            @yield('content')
            <hr size="3">
            <div class="row">
                <div class="col-12">
                    <footer class="text-muted">
                        <p>Saludos cordiales</p>
                        <p>{{env('APP_SYSTEM')}}</p>
                        <p>
                            (Las tildes han sido omitidas intencionalmente para evitar problemas de lectura).
                        </p>
                        <small>Nota de descargo:</small>
                        <small>La informacion contenida en este correo electronico es confidencial y solo puede ser
                            utilizada
                            por el usuario
                            al cual esta dirigido.</small>
                        <small>Esta informacion no debe ser distribuida ni copiada total o parcialmente por ningun medio
                            sin la autorizacion de la Institucion.
                        </small>
                        <p class="small">Favor no responder este mensaje que ha sido emitido automáticamente por el
                            sistema
                            SIGA-A.</p>
                    </footer>
                </div>
            </div>
            <h1 class="text-center bg-secondary text-white">{{env('APP_NAME')}}</h1>
        </div>
    </div>
</div>
</body>
</html>
