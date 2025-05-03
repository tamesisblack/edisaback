<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bingo</title>
</head>
<body>
    <p>

        Estimado/a {{ $nombres_jugador }}, <br><br>

        <h5><b>¡BINGO IBEROSTAR!</b></h5><br>

        Se complace en entregarte tu cartilla!<br>

        Pasos para jugar y ganar:<br>

        Conéctate el {{ $Fecha }} a las {{ $Hora }} (Hora Ecuador) a través de <br><br>
            
        {{ 'https://www.bingofun.ml/cartilla_jugador?id_evento='.$id_evento.'*'.$id_jugador.'' }} 
        
        <br> y listo! <br><br>
            
        Prepárate para la diversión.<br><br>

        <b>¡VAMOS A GANAR!</b><br><br>

    </p>
</body>
</html>