<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar sitios</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    @if($user->role=='operator')
        <h1>Gestión de sitios creados por Operador {{$user->name}}</h1>
    @endif


    @foreach($places as $place)
        <div class="w-full h-2/6 flex flex-row border-2 border-gray-300 mb-4 p-4 items-center" >
            <img class="w-40 h-20 object-cover" src="{{ url('/api/files/' . $place->cover) }}" alt="{{$place->name}}">
            <div>
                <h3>{{$place->name}}</h3>
                <p>Id:{{$place->id}}</p>
                <p>Creador:{{$place->user->name}}</p>
            </div>

            <div class="ml-auto">
                <!-- Botón Editar (GET) -->
                <a href="/Editar_sitio/{{$place->id}}" class="bg-blue-500 text-white px-4 py-2 rounded mr-2 inline-block">
                    Editar
                </a>
                
                <!-- Botón Eliminar (DELETE) -->
                <form action="/Gestion_sitio/{{$place->id}}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded" 
                            onclick="return confirm('¿Estás seguro de eliminar este sitio?')">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    <a href="/Crear_sitio">Añadir sitio</a>
     
</body>
</html>