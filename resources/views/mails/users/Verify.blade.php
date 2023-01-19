<html lang="es">
<head>
    <link rel="stylesheet" href="{{ asset('css/mails.css') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap');
    </style>
</head>
<body >

    <div style="background-color: #b64a4a; font-family: roboto, Helvetica, sans-serif; 
        color: rgb(255, 255, 255); font-weight: lighter; width: 100%;">
        <div style="padding: 3%;">
            <div style="border: 10px solid rgb(255, 255, 255);"><br>
                <h1 style="text-align: center">Bienvenido a tu plan de desempeño</h1>

                <div style="margin-left: 30px; margin-right: 30px; text-align: justify;">
                    Bienvenido {{$name}}, Ya estas registrado en nuestra pagina web gestion de desempeño y 
                    por mayor seguridad, recuerda que este codigo de verificación fue generado de manera aleatoria.
                </div><br>
            
                <div style="font-size: 18px; text-align: center; width: 15%; margin-left: auto; margin-right: auto;"> 
                    <div style="border: 5px solid rgb(255, 255, 255);">
                        Codigo {{$code}}
                    </div>
                </div><br><br>
            </div>
        </div>
    </div>
</body>
</html>