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
    <form action="{{ route('crear_sitio') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="nombre">Nombre del sitio</label>
            <input  type="text" name="nombre" id="nombre" value="{{ old('nombre') }}">
            @error('nombre')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror


            <label for="slogan">Slogan</label>
            <input type="text" name="slogan" id="slogan" value="{{ old('slogan') }}">
            @error('slogan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <label for="portada">Imagen de portada</label>
            <input type="file" name="portada" id="portada">
            @error('portada')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <label for="descripcion">Descripción</label>
        <textarea name="descripcion" id="descripcion">{{ old('descripcion') }}</textarea>
        @error('descripcion')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror


        <div>
            <label for="localizacion">Localización</label>
            <textarea name="localizacion" id="localizacion">{{ old('localizacion') }}</textarea>
            @error('localizacion')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

          <div id="map" class="w-80 h-80"></div>
          <input type="hidden" name="lat" id="lat">
          <input type="hidden" name="lng" id="lng">
           @error('lat')
                 <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
          @error('lng')
                 <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    
        </div>


        <div>
            <label for="clima_img">Imagen del clima</label>
            <input type="file" name="clima_img">
            @error('clima_img')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <label for="clima">Clima</label>
            <textarea name="clima" id="clima">{{ old('clima') }}</textarea>
            @error('clima')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div>
            <label for="caracteristicas">Características</label>
            <textarea name="caracteristicas" id="caracteristicas">{{ old('caracteristicas') }}</textarea>
            @error('caracteristicas')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <label for="caracteristicas_img">Imagen de características</label>
            <input type="file" name="caracteristicas_img">
        </div>


        <div>
            <label for="flora_img">Imagen de flora</label>
            <input type="file" name="flora_img">
            @error('flora_img')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <label for="flora">Flora y fauna</label>
            <textarea name="flora" id="flora">{{ old('flora') }}</textarea>
            @error('flora')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div>
            <label for="infraestructura_img">Imagen de infraestructura</label>
            <input type="file" name="infraestructura_img">
            @error('infraestructura_img')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <label for="infraestructura">Infraestructura</label>
            <textarea name="infraestructura" id="infraestructura">{{ old('infraestructura') }}</textarea>
            @error('infraestructura')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <label for="recomendacion">Recomendaciones</label>
        <textarea name="recomendacion" id="recomendacion">{{ old('recomendacion') }}</textarea>
        @error('recomendacion')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror


        <div class="tags">
            @foreach($preferences as $preference)
                
                <input type="checkbox" name="preferences[]" value="{{ $preference->id }}">
                 <p>{{ $preference->name }}</p> 
            
            @endforeach
            @error('preferences')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
         @enderror

        </div>


        <div>
            <label>
                <input type="checkbox" name="terminos">
                Acepto términos
                  @error('terminos')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
            </label>

            <label>
                <input type="checkbox" name="politicas" >
                Acepto políticas
                  @error('politicas')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
            </label>
        </div>

        <button type="submit">Finalizar</button>
    </form>
</main>

</body>
</html>
