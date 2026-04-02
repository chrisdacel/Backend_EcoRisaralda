<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="img/svg" href="./img/inicio_sesion/nature-svgrepo-com.svg">
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Conexión EcoRisaralda</title>
    @vite('resources/css/style_Preferencias.css')
    
</head>
<body>
    <header>
        @if ($errors->has('preferences'))
    <p class="error-message">{{ $errors->first('preferences') }}</p>
@endif
        <h1>Tú decides el camino, elige una opción</h1>
    </header>
    <main id="main_container">
     <form action="/preferencias" method="POST">
         @csrf 
         <div class="preferences-grid"> @foreach ($preferences as $preference) 
        <label class="preference-card"> 
        <input type="checkbox" name="preferences[]" value="{{ $preference->id }}"> <div class="card-content"> 
        <img src="#" alt=""> 
        <p>{{ $preference->name }}</p> 
    </div> 
</label>
 @endforeach 
</div> 
    <div id="botones">
       <input type="submit" value="Enviar" name="enviar">
<a href=""><button id="omitir">Omitir ></button></a>
    </div>
</form>
    </main>
 

</body>
</html>