<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   @vite(['resources/css/app.css', 'resources/js/app.js'])


   

    <title>Crear sitio</title>
</head>
<body>

<main>
    <form action="/Editar_sitio/{{ $place->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') 

        <div>
            <label for="nombre">Nuevo nombre del sitio</label>
            <input  type="text" name="nombre" id="nombre" value="{{$place->name}}">
            @error('nombre')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror


            <label for="slogan">Slogan</label>
            <input type="text" name="slogan" id="slogan" value="{{ $place->slogan }}">
            @error('slogan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <!-- Mostrar imagen actual -->
        @if($place->cover)
            <div class="mb-2">
                <p class="text-sm text-gray-600">Imagen actual:</p>
                <img src="{{ url('/api/files/' . $place->cover) }}" alt="Portada actual" class="w-32 h-32 object-cover rounded">
            </div>
        @endif
        
        <!-- Input para nueva imagen (opcional) -->
        <input type="file" name="portada" id="portada">
        <p class="text-sm text-gray-500">Deja vacío si no quieres cambiar la imagen</p>
        
        @error('portada')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror 
        </div>


        <label for="descripcion">Descripción</label>
        <textarea name="descripcion" id="descripcion">{{$place->description}}</textarea>
        @error('descripcion')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror


        <div>
            <label for="localizacion">Localización</label>
            <textarea name="localizacion" id="localizacion">{{ $place->localization }}</textarea>
            @error('localizacion')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

          <div id="map" class="w-80 h-80"></div>
          <input type="hidden" name="lat" id="lat" value="{{ $place->lat }}">
          <input type="hidden" name="lng" id="lng" value="{{ $place->lng }}">
           @error('lat')
                 <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
          @error('lng')
                 <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    
        </div>


        <div>
             @if($place->Weather_img)
            <div class="mb-2">
                <p class="text-sm text-gray-600">Imagen actual:</p>
                <img src="{{ url('/api/files/' . $place->Weather_img) }}" alt="clima actual" class="w-32 h-32 object-cover rounded">
            </div>
            @endif
        
        <!-- Input para nueva imagen (opcional) -->
        <input type="file" name="clima_img" id="clima_img">
        <p class="text-sm text-gray-500">Deja vacío si no quieres cambiar la imagen</p>
        
        @error('clima_img')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror 

            <label for="clima">Clima</label>
            <textarea name="clima" id="clima">{{ $place->Weather }}</textarea>
            @error('clima')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div>
            <label for="caracteristicas">Características</label>
            <textarea name="caracteristicas" id="caracteristicas">{{$place->features}}</textarea>
            @error('caracteristicas')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <label for="caracteristicas_img">Imagen de características</label>
            @if($place->features_img)
            <div class="mb-2">
                <p class="text-sm text-gray-600">Imagen actual:</p>
                <img src="{{ url('/api/files/' . $place->features_img) }}" alt="caracteristicas_img" class="w-32 h-32 object-cover rounded">
            </div>
        @endif
        
        <!-- Input para nueva imagen (opcional) -->
        <input type="file" name="caracteristicas_img" id="portada">
        <p class="text-sm text-gray-500">Deja vacío si no quieres cambiar la imagen</p>
        
        @error('caracteristicas_img')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror 
        </div>


        <div>
            <label for="flora_img">Imagen de flora</label>
            @if($place->flora_img)
            <div class="mb-2">
                <p class="text-sm text-gray-600">Imagen actual:</p>
                <img src="{{ url('/api/files/' . $place->flora_img) }}" alt="caracteristicas_img" class="w-32 h-32 object-cover rounded">
            </div>
        @endif
        
        <!-- Input para nueva imagen (opcional) -->
        <input type="file" name="flora_img" id="portada">
        <p class="text-sm text-gray-500">Deja vacío si no quieres cambiar la imagen</p>
            @error('flora_img')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <label for="flora">Flora y fauna</label>
            <textarea name="flora" id="flora">{{ $place->flora }}</textarea>
            @error('flora')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div>
          @if($place->estructure_img)
            <div class="mb-2">
                <p class="text-sm text-gray-600">Imagen actual:</p>
                <img src="{{ url('/api/files/' . $place->estructure_img) }}" alt="caracteristicas_img" class="w-32 h-32 object-cover rounded">
            </div>
        @endif
        
        <!-- Input para nueva imagen (opcional) -->
        <input type="file" name="infraestructura_img" id="portada">
        <p class="text-sm text-gray-500">Deja vacío si no quieres cambiar la imagen</p>

            <label for="infraestructura">Infraestructura</label>
            <textarea name="infraestructura" id="infraestructura">{{ $place->estructure }}</textarea>
            @error('infraestructura')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <label for="recomendacion">Recomendaciones</label>
        <textarea name="recomendacion" id="recomendacion">{{ $place->tips }}</textarea>
        @error('recomendacion')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <div class="tags">
            @foreach($preferences as $preference)
                <input
                    type="checkbox"
                    name="preferences[]"
                    value="{{ $preference->id }}"
                    {{ in_array($preference->id, $selectedPreferences) ? 'checked' : '' }}
                >
                <p>{{ $preference->name }}</p>
            @endforeach
            @error('preferences')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


 

        <button type="submit">Finalizar</button>
    </form>
</main>

</body>
</html>
