<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\TuristicPlace;

class TurusticPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar carpetas anteriores (opcional)
        Storage::disk('public')->deleteDirectory('portadas');
        Storage::disk('public')->deleteDirectory('clima');
        Storage::disk('public')->deleteDirectory('caracteristicas');
        Storage::disk('public')->deleteDirectory('flora');
        Storage::disk('public')->deleteDirectory('infraestructura');

        // Crear las carpetas en storage/app/public
        Storage::disk('public')->makeDirectory('portadas');
        Storage::disk('public')->makeDirectory('clima');
        Storage::disk('public')->makeDirectory('caracteristicas');
        Storage::disk('public')->makeDirectory('flora');
        Storage::disk('public')->makeDirectory('infraestructura');

        $sitios = [
            [
                'name' => 'Parque Regional Natural Ucumarí',
                'slogan' => 'Biodiversidad en el corazón del eje cafetero',
                'description' => 'El Parque Regional Natural Ucumarí es la puerta de entrada a la selva nublada de los Andes Occidentales. Con una extensión de 4,590 hectáreas, alberga más de 400 especies de aves y es hogar del oso de anteojos.',
                'localization' => 'Pereira, Risaralda',
                'lat' => 4.72260972,
                'lng' => -75.54074393,
                'Weather' => 'Variable entre 8°C a 18°C debido a la altitud',
                'features' => 'Senderos ecológicos, cascadas, observación de fauna',
                'flora' => 'Orquídeas, helechos arborescentes, robles, palmas de cera',
                'estructure' => 'Centro de visitantes, miradores, senderos señalizados',
                'tips' => 'Llevar ropa impermeable y calzado resistente.',
                'contact_info' => 'Tel: (6) 3135220 - Email: info@parqueucumari.org',
                'open_days' => json_encode(['Lunes' => false, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/cover-ucumari.jpg', 'dest' => 'portadas/cover-ucumari.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-ucumari.jpg', 'dest' => 'clima/clima-ucumari.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracterisitcas-ucumari.jpg', 'dest' => 'caracteristicas/caracterisitcas-ucumari.jpg'],
                    'flora_img' => ['src' => 'flora/Fauna-FloraUcumari.jpg', 'dest' => 'flora/Fauna-FloraUcumari.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-ucumari.jpg', 'dest' => 'infraestructura/estructura-ucumari.jpg'],
                ],
            ],
            [
                'name' => 'Termales de Santa Rosa de Cabal',
                'slogan' => 'Aguas termales entre la naturaleza del eje cafetero',
                'description' => 'Complejo de termas naturales con piscinas y cascadas, ideal para relajarse.',
                'localization' => 'Santa Rosa de Cabal, Risaralda',
                'lat' => 4.86400017,
                'lng' => -75.61011736,
                'Weather' => 'Templado entre 18°C y 25°C',
                'features' => 'Piscinas termales, masajes, alojamiento',
                'flora' => 'Palmas, helechos y bromelias',
                'estructure' => 'Piscinas termales, vestieres, restaurante',
                'tips' => 'Llevar toalla y bloqueador.',
                'contact_info' => 'Tel: (6) 3145000',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/cover-termales.jpg', 'dest' => 'portadas/cover-termales.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-termales.jpg', 'dest' => 'clima/clima-termales.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-termales.jpg', 'dest' => 'caracteristicas/caracteristicas-termales.jpg'],
                    'flora_img' => ['src' => 'flora/flora-termales.webp', 'dest' => 'flora/flora-termales.webp'],
                    'estructure_img' => ['src' => 'estructura/estructura-termales.jpg', 'dest' => 'infraestructura/estructura-termales.jpg'],
                ],
            ],
            [
                'name' => 'Santuario de Fauna y Flora Otún Quimbaya',
                'slogan' => 'Tesoro escondido del bosque húmedo tropical',
                'description' => 'Área protegida dedicada a la conservación de especies endémicas y avistamiento de aves.',
                'localization' => 'Pereira-Santa Rosa de Cabal, Risaralda',
                'lat' => 4.72290483,
                'lng' => -75.57653384,
                'Weather' => 'Frío entre 11°C y 20°C',
                'features' => 'Senderos, cascadas, avistamiento de aves',
                'flora' => 'Orquídeas, helechos y musgos',
                'estructure' => 'Centro de visitantes y senderos marcados',
                'tips' => 'Llevar ropa impermeable y prismáticos.',
                'contact_info' => 'Tel: (6) 3140000',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/cover-otun.jpg', 'dest' => 'portadas/cover-otun.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-otun.jpg', 'dest' => 'clima/clima-otun.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-otun.jpg', 'dest' => 'caracteristicas/caracteristicas-otun.jpg'],
                    'flora_img' => ['src' => 'flora/flora-otun.jpg', 'dest' => 'flora/flora-otun.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-otun.jpg', 'dest' => 'infraestructura/estructura-otun.jpg'],
                ],
            ],
            [
                'name' => 'Laguna de Otún',
                'slogan' => 'Espejo de agua en la montaña risaraldense',
                'description' => 'Laguna alpina localizada en los páramos, rodeada de frailejones y vegetación de altura.',
                'localization' => 'Páramo de Santa Rosa de Cabal, Risaralda',
                'lat' => 4.49633909,
                'lng' => -75.55057526,
                'Weather' => 'Muy frío entre 2°C y 8°C',
                'features' => 'Laguna, senderos de montaña, fotografía de paisaje',
                'flora' => 'Frailejones, musgos y líquenes',
                'estructure' => 'Miradores y senderos de trekking',
                'tips' => 'Ropa térmica y buen calzado. Contratar guía.',
                'contact_info' => 'Contacto con guías locales',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/portada-laguna-otun.jpg', 'dest' => 'portadas/portada-laguna-otun.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-laguna-otun.jpg', 'dest' => 'clima/clima-laguna-otun.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-laguna-otun.jpg', 'dest' => 'caracteristicas/caracteristicas-laguna-otun.jpg'],
                    'flora_img' => ['src' => 'flora/flora-Laguna-del-Otun.jpg', 'dest' => 'flora/flora-Laguna-del-Otun.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-laguna-del-otun.jpg', 'dest' => 'infraestructura/estructura-laguna-del-otun.jpg'],
                ],
            ],
            [
                'name' => 'Reserva Natural Bosque de Yotoco',
                'slogan' => 'Bosque seco tropical en peligro de extinción',
                'description' => 'Reserva natural dedicada a la conservación del bosque seco tropical y programas de educación ambiental.',
                'localization' => 'Belén de Umbría, Risaralda',
                'lat' =>  5.2437857,
                'lng' =>-75.82634926,
                'Weather' => 'Cálido entre 24°C y 28°C',
                'features' => 'Senderos interpretativos y reforestación',
                'flora' => 'Ceibo, roble y plantas medicinales',
                'estructure' => 'Centro de visitantes y aula ambiental',
                'tips' => 'Ideal para familias y actividades educativas.',
                'contact_info' => 'Tel: (6) 3125000',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/portada-yotoco.jpg', 'dest' => 'portadas/portada-yotoco.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-yotoco.avif', 'dest' => 'clima/clima-yotoco.avif'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-yotoco.jpg', 'dest' => 'caracteristicas/caracteristicas-yotoco.jpg'],
                    'flora_img' => ['src' => 'flora/fauna-yotoco.jpg', 'dest' => 'flora/fauna-yotoco.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-yotoco.jpg', 'dest' => 'infraestructura/estructura-yotoco.jpg'],
                ],
            ],
            // Nuevos sitios añadidos por solicitud
            [
                'name' => 'Mistrató',
                'slogan' => 'Historia, tradiciones y paisajes de altura',
                'description' => 'Mistrató, municipio de la región occidental de Risaralda, es un lugar de paisaje montañoso salpicado por ríos cristalinos, veredas con arquitectura tradicional y una comunidad orgullosa de sus tradiciones culturales. Sus caminos rurales conducen a miradores naturales desde donde se dominan valles y pequeñas quebradas; la gastronomía local y las artesanías hacen del pueblo un destino que combina tranquilidad y autenticidad. Es un sitio ideal para turismo comunitario, caminatas suaves y para quienes buscan contacto directo con la naturaleza y la cultura risaraldense.',
                'localization' => 'Mistrató, Risaralda',
                'lat' =>  5.40684375,
                'lng' => -75.96839905,
                'Weather' => 'Clima templado a frío según la altitud, entre 12°C y 22°C',
                'features' => 'Miradores, recorridos rurales, ríos y pesca artesanal',
                'flora' => 'Bosque altoandino, helechos, arbustos nativos y cultivos tradicionales',
                'estructure' => 'Plaza principal, senderos locales y cabañas de turismo rural',
                'tips' => 'Respetar las rutas comunitarias, contratar guías locales y probar la comida tradicional.',
                'contact_info' => 'Contactos: Alcaldía de Mistrató y oficinas de turismo local',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/cover-mistrato.jpg', 'dest' => 'portadas/cover-mistrato.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-mistrato.jpg', 'dest' => 'clima/clima-mistrato.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-mistrato.jpg', 'dest' => 'caracteristicas/caracteristicas-mistrato.jpg'],
                    'flora_img' => ['src' => 'flora/flora-mistrato.jpg', 'dest' => 'flora/flora-mistrato.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-mistrato.jpg', 'dest' => 'infraestructura/estructura-mistrato.jpg'],
                ],
            ],
            [
                'name' => 'Chorros de Don Lolo',
                'slogan' => 'Cascadas y pozos escondidos en Santa Rosa de Cabal',
                'description' => 'Los Chorros de Don Lolo son una serie de cascadas y pozos naturales de aguas limpias y refrescantes, rodeados por vegetación exuberante y senderos que permiten el contacto directo con la flora y fauna local. El lugar conserva un ambiente rústico y tranquilo, ideal para exploración, fotografía y baños en pozos naturales. La experiencia se completa con senderos que conducen a miradores y a pequeñas pozas donde se aprecia la fuerza del agua en medio de la montaña.',
                'localization' => 'Santa Rosa de Cabal, Risaralda',
                'lat' => 4.88654328,
                'lng' =>  -75.57261229,
                'Weather' => 'Templado-húmedo entre 16°C y 22°C',
                'features' => 'Cascadas, pozos naturales, senderos cortos para caminatas',
                'flora' => 'Bosque húmedo, helechos, bromelias y vegetación riparia',
                'estructure' => 'Senderos rústicos y puntos de observación naturales',
                'tips' => 'Evitar nadar en épocas de fuertes lluvias; respetar la señalización y no dejar residuos.',
                'contact_info' => 'Información: Oficina de turismo de Santa Rosa de Cabal',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/cover-donlolo.jpg', 'dest' => 'portada/cover-donlolo.jpg'],
                    'Weather_img' => ['src' => 'clima/climadonlolo.jpg', 'dest' => 'clima/clima-donlolo.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-donlolo.jpg', 'dest' => 'caracteristicas/caracteristicas-donlolo.jpg'],
                    'flora_img' => ['src' => 'flora/flora-donlolo.jpg', 'dest' => 'flora/flora-donlolo.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-donlolo.jpg', 'dest' => 'estructura/estructura-donlolo.jpg'],
                ],
            ],
            [
                'name' => 'Parque Consotá',
                'slogan' => 'Un pulmón verde urbano y centro de esparcimiento en Pereira',
                'description' => 'El Parque Consotá es un extenso espacio verde en Pereira diseñado para el entretenimiento y la conexión con la naturaleza dentro de la ciudad. Cuenta con amplias praderas, zonas infantiles, senderos sombreados y áreas deportivas. Sus jardines y cuerpos de agua lo convierten en un punto de encuentro para familias, deportistas y visitantes que buscan aire libre sin salir de la ciudad. Además, el parque acoge eventos culturales y ferias locales que muestran la riqueza cultural de la región.',
                'localization' => 'Pereira, Risaralda',
                'lat' => 4.79637914,
                'lng' =>  -75.80700517,
                'Weather' => 'Templado entre 18°C y 26°C',
                'features' => 'Áreas verdes, canchas deportivas, zonas de picnic, eventos culturales',
                'flora' => 'Áreas ajardinadas, árboles ornamentales y palmas',
                'estructure' => 'Caminos pavimentados, zonas de descanso y kioscos',
                'tips' => 'Visitar en horas de la mañana o tarde para evitar el calor; aprovechar las actividades programadas.',
                'contact_info' => 'Información en la Alcaldía de Pereira y oficinas de cultura.',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/portada-consota.jpg', 'dest' => 'portada/portada-consota.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-consota.jpg', 'dest' => 'clima/clima-consota.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-consota.jpg', 'dest' => 'caracteristicas/caracteristicas-consota.jpg'],
                    'flora_img' => ['src' => 'flora/flora-consota.jpg', 'dest' => 'flora/flora-consota.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-consota.jpg', 'dest' => 'infraestructura/estructura-consota.jpg'],
                ],
            ],
            [
                'name' => 'Jardín Botánico UTP',
                'slogan' => 'Conservación, conocimiento y espacios verdes en la UTP',
                'description' => 'El Jardín Botánico de la Universidad Tecnológica de Pereira es un espacio dedicado a la conservación, la investigación y la educación ambiental. Alberga colecciones de especies nativas y exóticas, senderos didácticos y áreas de observación que permiten entender la diversidad vegetal de la región. Es un recurso valioso para estudiantes, investigadores y visitantes que desean aprender sobre botánica y disfrutar de un entorno calmado y pedagógico.',
                'localization' => 'Pereira, Risaralda (UTP)',
                'lat' => 4.79366355,
                'lng' => -75.68746448,
                'Weather' => 'Templado entre 18°C y 24°C',
                'features' => 'Colecciones botánicas, senderos interpretativos y actividades educativas',
                'flora' => 'Especies nativas, jardines temáticos y plantas ornamentales',
                'estructure' => 'Senderos, señalización educativa y áreas de aula al aire libre',
                'tips' => 'Ideal para visitas educativas; respetar las áreas protegidas y no remover plantas.',
                'contact_info' => 'UTP - Departamento de Biología y Jardín Botánico',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => false, 'Domingo' => false]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/portada-UTP.jpg', 'dest' => 'portada/portada-UTP.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-utp.jpg', 'dest' => 'clima/clima-utp.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-utp.jpg', 'dest' => 'caracteristicas/caracteristicas-utp.jpg'],
                    'flora_img' => ['src' => 'flora/flora-utp.jpg', 'dest' => 'flora/flora-utp.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-utp.jpg', 'dest' => 'estructura/estructura-utp.jpg'],
                ],
            ],
            [
                'name' => 'Parque Nacional Natural Tatamá',
                'slogan' => 'Biodiversidad andina y refugio de especies únicas',
                'description' => 'El Parque Nacional Natural Tatamá es un área de gran valor biológico que protege bosques húmedos montanos y selva nublada, con altitudes que generan una amplia variedad de ecosistemas y microclimas. Es famoso por su avifauna, incluyendo especies endémicas y migratorias, y por su compleja red de ríos y quebradas que alimentan las cuencas locales. Las expediciones al Tatamá requieren planificación y, en muchos casos, el acompañamiento de guías especializados debido a la topografía y la sensibilidad del entorno.',
                'localization' => 'Serranía de los Tatamá, Risaralda / Chocó',
                'lat' =>  5.05760140,
                'lng' =>  -76.12486839,
                'Weather' => 'Frío a templado según altitud, entre 6°C y 18°C',
                'features' => 'Observación de aves, bosques nublados, rutas de trekking de alta montaña',
                'flora' => 'Bosque nublado con orquídeas, bromelias y árboles milenarios',
                'estructure' => 'Zonas de acampe permitidas, senderos primarios y zonas de conservación',
                'tips' => 'Planificar con antelación, respetar reglamentos del parque y llevar equipo de montaña.',
                'contact_info' => 'Parques Nacionales Naturales de Colombia - Coordinación Tatamá',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/portada-tatama.jpg', 'dest' => 'portadas/portada-tatama.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-tatama.jpg', 'dest' => 'clima/clima-tatama.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-tatama.jpg', 'dest' => 'caracteristicas/caracteristicas-tatama.jpg'],
                    'flora_img' => ['src' => 'flora/flora-tatama.jpg', 'dest' => 'flora/flora-tatama.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-tatama.jpg', 'dest' => 'infraestructura/estructura-tatama.jpg'],
                ],
            ],
            [
                'name' => 'Pueblo Rico',
                'slogan' => 'Puerta al occidente risaraldense y destino de aventuras',
                'description' => 'Pueblo Rico es un municipio de transición entre las montañas y las selvas del occidente colombiano, con paisajes que alternan entre montañas, ríos y valles. Es reconocido por su riqueza cultural, comunidades locales que mantienen tradiciones y por ofrecer experiencias de turismo de naturaleza, como senderismo, avistamiento de aves y recorridos por cultivos tradicionales. Las rutas hacia Pueblo Rico permiten explorar ambientes variados y disfrutar de la hospitalidad local.',
                'localization' => 'Pueblo Rico, Risaralda',
                'lat' => 5.22703306,
                'lng' => -76.02953196,
                'Weather' => 'Variable según altitud, entre 16°C y 26°C',
                'features' => 'Senderismo, ríos de aguas claras, turismo comunitario',
                'flora' => 'Bosque húmedo, café y cultivos agroforestales',
                'estructure' => 'Puntos de hospedaje rural, rutas guiadas y centros comunitarios',
                'tips' => 'Contratar guías locales, respetar tradiciones y apoyar la economía local.',
                'contact_info' => 'Oficina de turismo municipal y asociaciones locales de guías',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/cover-pueblo-rico.jpg', 'dest' => 'portadas/cover-pueblo-rico.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-pueblo-rico.jpg', 'dest' => 'clima/clima-pueblo-rico.jpg'],
                    'features_img' => ['src' => 'caracteristicas/pueblorico-caracteristicas.jpg', 'dest' => 'caracteristicas/pueblorico-caracteristicas.jpg'],
                    'flora_img' => ['src' => 'flora/flora-pueblo-rico.jpg', 'dest' => 'flora/flora-pueblo-rico.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-pueblo-rico.jpg', 'dest' => 'infraestructura/estructura-pueblo-rico.jpg'],
                ],
            ],
            [
                'name' => 'Parque Nacional Natural Los Nevados',
                'slogan' => 'Paisajes de alta montaña, páramos y volcanes',
                'description' => 'El Parque Nacional Natural Los Nevados protege ecosistemas de páramo, bosques andinos y volcanes icónicos que dominan el paisaje central colombiano. Es un destino para trekking de alta montaña, observación de paisajes glaciales y estudio de ecosistemas frágiles. Los visitantes encontrarán lagunas de alta montaña, formación de rocas volcánicas y una biodiversidad adaptada a condiciones extremas. Las rutas más populares requieren buena condición física y planificación por los cambios climáticos bruscos.',
                'localization' => 'Eje central de los Andes (regiones de Caldas, Risaralda, Tolima y Quindío)',
                'lat' => 4.8000,
                'lng' => -75.4000,
                'Weather' => 'Frío de páramo entre -2°C y 12°C según la cota',
                'features' => 'Páramos, miradores, lagunas de alta montaña y volcanes',
                'flora' => 'Frailejones, musgos, líquenes y vegetación de páramo',
                'estructure' => 'Senderos autorizados, centros de interpretación y zonas de acampe reguladas',
                'tips' => 'Equipamiento de montaña, ropa térmica, contratar guías autorizados y respetar las normas de Parques Nacionales.',
                'contact_info' => 'Parques Nacionales Naturales de Colombia - Administración Los Nevados',
                'open_days' => json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => true,
                'user_id' => 1,
                'terminos' => true,
                'politicas' => true,
                'images' => [
                    'cover' => ['src' => 'portada/portada-nevados.jpg', 'dest' => 'portada/portada-nevados.jpg'],
                    'Weather_img' => ['src' => 'clima/clima-nevado.jpg', 'dest' => 'clima/clima-nevado.jpg'],
                    'features_img' => ['src' => 'caracteristicas/caracteristicas-nevado.jpg', 'dest' => 'caracteristicas/caracteristicas-nevado.jpg'],
                    'flora_img' => ['src' => 'flora/flora-nevados.jpg', 'dest' => 'flora/flora-nevados.jpg'],
                    'estructure_img' => ['src' => 'estructura/estructura-nevado.jpg', 'dest' => 'estructura/estructura-nevado.jpg'],
                ],
            ],
                // Sitios solicitados: Marsella, Reserva Barbas Bremen, Los Guayacanes, Gran Reserva Wabanta
                [
                    'name' => 'Ecohotel Los Lagos',
                    'slogan' => 'Ecohotel en el Paisaje Cultural Cafetero - Senderismo, Aves y Relax en la Naturaleza',
                    'description' => 'Ecohotel rural pionero en ecoturismo (mas de 33 anos), enfocado en alojamiento sostenible rodeado de naturaleza, con senderos ecologicos, avistamiento de aves y relax en un entorno verde del corazon del Paisaje Cultural Cafetero. Ideal para desconectarse, caminar y disfrutar la tranquilidad del bosque y la biodiversidad.',
                    'localization' => 'Via Pereira - Marsella, km 28, Marsella, Risaralda',
                    'lat' => 4.93423964,
                    'lng' => -75.73705316,
                    'Weather' => 'Clima templado de montana entre 18 y 24 C, noches frescas',
                    'features' => 'Enfoque 100% ecoturistico; sendero ecologico de 3200 m; avistamiento de aves; actividades guiadas; capacidad para grupos hasta 120; pet friendly en algunas areas',
                    'flora' => 'Bosque nativo andino y paisaje cultural cafetero con palmas, arboles y vegetacion endemica',
                    'estructure' => '24 habitaciones tipo cabana con banos privados y agua caliente; restaurante; senderos y zonas comunes',
                    'tips' => 'Llevar ropa comoda y zapatos de senderismo; binoculares para avistamiento; reservar con anticipacion',
                    'contact_info' => null,
                    'open_days' => json_encode(['Lunes' => true,'Martes' => true,'Miercoles' => true,'Jueves' => true,'Viernes' => true,'Sabado' => true,'Domingo' => true]),
                    'opening_status' => true,
                    'user_id' => 1,
                    'terminos' => true,
                    'politicas' => true,
                    'images' => [
                        'cover' => ['src' => 'portada/marsella-portada.jpg', 'dest' => 'portadas/marsella-portada.jpg'],
                        'Weather_img' => ['src' => 'clima/clima-marsella.jpg', 'dest' => 'clima/clima-marsella.jpg'],
                        'features_img' => ['src' => 'caracteristicas/caracteristicas-marsella.jpg', 'dest' => 'caracteristicas/caracteristicas-marsella.jpg'],
                        'flora_img' => ['src' => 'flora/fauna-marsella.jpg', 'dest' => 'flora/fauna-marsella.jpg'],
                        'estructure_img' => ['src' => 'estructura/estuctrura-marsella.jpg', 'dest' => 'infraestructura/estuctrura-marsella.jpg'],
                    ],
                ],
                [
                    'name' => 'Reserva Natural Barbas Bremen',
                    'slogan' => 'Paraíso de biodiversidad en el Eje Cafetero',
                    'description' => 'Area protegida de bosque humedo andino con el canon del rio Barbas, ideal para ecoturismo, avistamiento de monos aulladores, aves y caminatas en selva virgen. Oasis de conservacion muy cerca de Pereira.',
                    'localization' => 'Cuenca del rio Barbas, acceso desde Pereira (via a Filandia km 17 aprox.)',
                    'lat' => 4.67018927,
                    'lng' => -75.62134266,
                    'Weather' => 'Templado-humedo de montana entre 18 y 22 C, niebla frecuente',
                    'features' => 'Bosque humedo andino preservado; canon fluvial; senderos; avistamiento de monos aulladores; mas de 250 especies de aves',
                    'flora' => 'Arboles altos, epifitas, orquideas, helechos y vegetacion densa',
                    'estructure' => 'Senderos rusticos trazados; acceso por caminos rurales; recorridos guiados recomendados',
                    'tips' => 'Visitar con guia local; llevar calzado antideslizante y repelente; ir temprano en la manana',
                    'contact_info' => null,
                    'open_days' => json_encode(['Lunes' => false,'Martes' => true,'Miercoles' => true,'Jueves' => true,'Viernes' => true,'Sabado' => true,'Domingo' => true]),
                    'opening_status' => true,
                    'user_id' => 1,
                    'terminos' => true,
                    'politicas' => true,
                    'images' => [
                        'cover' => ['src' => 'portada/pereira-portada.jpg', 'dest' => 'portadas/pereira-portada.jpg'],
                        'Weather_img' => ['src' => 'clima/clima-pereira.jpg', 'dest' => 'clima/clima-pereira.jpg'],
                        'features_img' => ['src' => 'caracteristicas/caracteristicas-pereira.jpg', 'dest' => 'caracteristicas/caracteristicas-pereira.jpg'],
                        'flora_img' => ['src' => 'flora/flora-pereira.jpg', 'dest' => 'flora/flora-pereira.jpg'],
                        'estructure_img' => ['src' => 'estructura/estructura-pereira.jpg', 'dest' => 'infraestructura/estructura-pereira.jpg'],
                    ],
                ],
                [
                    'name' => 'Los Guayacanes Restaurante',
                    'slogan' => 'Cultivamos vida para alegrar tu vida',
                    'description' => 'Restaurante campestre familiar con comida tipica regional, preparado con tradicion en un ambiente acogedor rodeado de jardines y vistas a la montana.',
                    'localization' => 'Sector San Pedro, vereda San Pedro, a 1 km del casco urbano de Quinchia',
                    'lat' =>  5.33897225,
                    'lng' => -75.73058903,
                    'Weather' => 'Templado de montana entre 18 y 22 C',
                    'features' => 'Comida casera tradicional; atencion familiar; ideal para almuerzos y celebraciones',
                    'flora' => 'Jardines con vegetacion local, flores y arboles del Eje Cafetero',
                    'estructure' => 'Espacio sencillo y acogedor, mesas al aire libre o bajo techo, cocina tradicional visible',
                    'tips' => 'Probar la semi paisa o los platos del dia; llevar efectivo; confirmar horarios',
                    'contact_info' => null,
                    'open_days' => json_encode(['Lunes' => true,'Martes' => true,'Miercoles' => true,'Jueves' => true,'Viernes' => true,'Sabado' => true,'Domingo' => true]),
                    'opening_status' => true,
                    'user_id' => 1,
                    'terminos' => true,
                    'politicas' => true,
                    'images' => [
                        'cover' => ['src' => 'portada/portada-quinchia.jpg', 'dest' => 'portadas/portada-quinchia.jpg'],
                        'Weather_img' => ['src' => 'clima/clima-quinchia.jpg', 'dest' => 'clima/clima-quinchia.jpg'],
                        'features_img' => ['src' => 'caracteristicas/caracteristicas-qinchia.jpg', 'dest' => 'caracteristicas/caracteristicas-qinchia.jpg'],
                        'flora_img' => ['src' => 'flora/flora-quinchia.jpg', 'dest' => 'flora/flora-quinchia.jpg'],
                        'estructure_img' => ['src' => 'estructura/estructura-quinchia.jpg', 'dest' => 'infraestructura/estructura-quinchia.jpg'],
                    ],
                ],
                [
                    'name' => 'Gran Reserva Wabanta',
                    'slogan' => 'Conectate con la naturaleza',
                    'description' => 'Reserva natural privada y hospedaje ecologico en la zona amortiguadora del Parque Nacional Natural Tatama. Ofrece experiencias de ecoturismo sostenible, senderismo y avistamiento de aves en bosques de niebla.',
                    'localization' => 'Vereda El Amparo, ingreso por Santuario, Risaralda',
                    'lat' => 5.10710198,
                    'lng' => -75.94182372,
                    'Weather' => 'Fresco de alta montana alrededor de 2100 msnm; temperaturas entre 13 y 18 C; niebla frecuente',
                    'features' => 'Senderos ecologicos guiados; avistamiento de aves; hospedaje y alimentacion incluidos en planes; enfoque en conservacion y educacion ambiental',
                    'flora' => 'Bosque de niebla con helechos arborescentes, epifitas y arboles nativos',
                    'estructure' => 'Hospedaje rural comodo, senderos marcados y areas comunes',
                    'tips' => 'Reservar con anticipacion; ropa en capas, impermeable y botas; visitar en la manana para mejor avistamiento',
                    'contact_info' => null,
                    'open_days' => json_encode(['Lunes' => false,'Martes' => true,'Miercoles' => true,'Jueves' => true,'Viernes' => true,'Sabado' => true,'Domingo' => true]),
                    'opening_status' => true,
                    'user_id' => 1,
                    'terminos' => true,
                    'politicas' => true,
                    'images' => [
                        'cover' => ['src' => 'portada/portada-santuario.jpg', 'dest' => 'portadas/portada-santuario.jpg'],
                        'Weather_img' => ['src' => 'clima/clima-santuario.jpg', 'dest' => 'clima/clima-santuario.jpg'],
                        'features_img' => ['src' => 'caracteristicas/caracteristicas-santuario.jpg', 'dest' => 'caracteristicas/caracteristicas-santuario.jpg'],
                        'flora_img' => ['src' => 'flora/flora-santuario.jpg', 'dest' => 'flora/flora-santuario.jpg'],
                        'estructure_img' => ['src' => 'estructura/estructura-santuario.jpg', 'dest' => 'infraestructura/estructura-santuario.jpg'],
                    ],
                ],

                [
                    'name' => 'Jardín Botánico Alejandro Humboldt',
                    'slogan' => 'El pulmón verde de Marsella',
                    'description' => 'Jardín botánico fundado en 1979 por ambientalistas locales, dedicado a la conservación de flora nativa del Eje Cafetero. Con senderos, colecciones de plantas endémicas, viveros y elementos educativos/tecnológicos sostenibles.',
                    'localization' => 'En el área urbana de Marsella, cerca del parque principal: Carrera 10 Calle 15 (Avenida Villa Rica de Segovia o Calle 14b). Fácil acceso a pie desde el centro del pueblo (a 45 min en carro desde Pereira).',
                    'lat' => 4.93434,
                    'lng' => -75.73685,
                    'Weather' => 'Templado de montaña (~1.500 msnm): 18-22 °C promedio, fresco con brisa constante, niebla matutina frecuente y lluvias posibles (más en épocas húmedas). Ambiente húmedo y refrescante, ideal para caminatas.',
                    'features' => 'Templado de montaña (~1.500 msnm): 18-22 °C promedio, fresco con brisa constante, niebla matutina frecuente y lluvias posibles (más en épocas húmedas). Ambiente húmedo y refrescante, ideal para caminatas.',
                    'flora' => 'Colecciones de plantas nativas, orquídeas, heliconias, helechos, guadua y especies endémicas del Eje Cafetero (mezcla de naturaleza y cultivos). Aves locales (colibríes, especies andinas), mariposas, insectos y peces en la quebrada/represa. Gran biodiversidad en un entorno preservado.',
                    'estructure' => 'Senderos bien trazados, viveros, oficinas administrativas, represa de piedra, puente peatonal, canopy (estructura elevada), torre hidromecánica y panel solar. Espacio funcional con zonas educativas; entrada económica o gratuita en algunos casos. Abierto al público con recorrido autoguiado o con guía opcional.',
                    'tips' => 'Ve de martes a domingo (8:00 a.m. - 5:00 p.m.); reserva para grupos o colegios. Lleva zapatos cómodos (senderos pueden ser húmedos), repelente, agua y cámara. Mejor en mañana para menos calor y mejores fotos.',
                    'contact_info' => 'Teléfonos: +57 314 810 3019 / +57 314 623 4941 / +57 320 626 7805',
                    'open_days' => json_encode(['Lunes' => false,'Martes' => true,'Miercoles' => true,'Jueves' => true,'Viernes' => true,'Sabado' => true,'Domingo' => true]),
                    'opening_status' => true,
                    'user_id' => 1,
                    'terminos' => true,
                    'politicas' => true,
                    'images' => [
                        'cover' => ['src' => 'portada/portada-jardin-Botanico-alejandro.png', 'dest' => 'portadas/portada-jardin-Botanico-alejandro.png'],
                        'Weather_img' => ['src' => 'clima/clima-jardin-alejandro.png', 'dest' => 'clima/clima-jardin-alejandro.png'],
                        'features_img' => ['src' => 'caracteristicas/caracteristicas-jardin-alejandro.png', 'dest' => 'caracteristicas/caracteristicas-jardin-alejandro.png'],
                        'flora_img' => ['src' => 'flora/flora-jardin-alejandro.png', 'dest' => 'flora/flora-jardin-alejandro.png'],
                        'estructure_img' => ['src' => 'estructura/estructura-jardin-alejandro.png', 'dest' => 'infraestructura/estructura-jardin-alejandro.png'],
                     ],


'name' => 'Parque Regional Natural La Marcada',
'slogan' => 'Un refugio natural entre montañas y ríos',
'description' => 'Área protegida de carácter regional ubicada en el departamento de Risaralda, reconocida por sus bosques andinos, fuentes hídricas y alta biodiversidad. Es un espacio clave para la conservación ambiental, la educación ecológica y el ecoturismo responsable, con paisajes naturales ideales para caminatas y contacto directo con la naturaleza.',
'localization' => 'Municipio de Dosquebradas, Risaralda',
'lat' => 4.82634476,
'lng' => -75.60483372,
'Weather' => 'Clima templado a fresco propio del bosque andino; temperaturas entre 16 y 22 C; alta humedad y lluvias frecuentes',
'features' => 'Senderos ecologicos; cascadas y quebradas; observacion de flora y fauna; espacios para educacion ambiental; zonas de descanso natural',
'flora' => 'Bosque andino con arboles nativos, helechos, musgos, epifitas y diversidad de especies vegetales propias de ecosistemas humedos',
'estructure' => 'Senderos señalizados, areas de acceso controlado, puntos de interpretacion ambiental y zonas naturales de recreacion pasiva',
'tips' => 'Llevar calzado comodo e impermeable; usar protector solar y repelente; respetar las normas del area protegida; no dejar residuos',
'contact_info' => null,
'open_days' => json_encode([
    'Lunes' => true,
    'Martes' => true,
    'Miercoles' => true,
    'Jueves' => true,
    'Viernes' => true,
    'Sabado' => true,
    'Domingo' => true
]),
'opening_status' => true,
'user_id' => 1,
'terminos' => true,
'politicas' => true,
'images' => [
    'cover' => ['src' => 'portada/portada-marcada.jpg', 'dest' => 'portadas/portada-marcada.jpg'],
    'Weather_img' => ['src' => 'clima/clima-marcada.jpg', 'dest' => 'clima/clima-marcada.jpg'],
    'features_img' => ['src' => 'caracteristicas/caracteristicas-marcada.jpg', 'dest' => 'caracteristicas/caracteristicas-marcada.jpg'],
    'flora_img' => ['src' => 'flora/flora-marcada.jpg', 'dest' => 'flora/flora-marcada.jpg'],
    'estructure_img' => ['src' => 'estructura/estructura-marcada.jpg', 'dest' => 'estructura/estructura-marcada.jpg'],
],

                    
                ],


        ];

        foreach ($sitios as $s) {
            // Copiar imágenes si existen y mapear campos al array para crear
            $data = [
                'name' => $s['name'],
                'slogan' => $s['slogan'] ?? null,
                'cover' => '',
                'description' => $s['description'] ?? null,
                'localization' => $s['localization'] ?? null,
                'lat' => $s['lat'] ?? 0.0,
                'lng' => $s['lng'] ?? 0.0,
                'Weather' => $s['Weather'] ?? null,
                'Weather_img' => '',
                'features' => $s['features'] ?? null,
                'features_img' => '',
                'flora' => $s['flora'] ?? null,
                'flora_img' => '',
                'estructure' => $s['estructure'] ?? null,
                'estructure_img' => '',
                'tips' => $s['tips'] ?? null,
                'contact_info' => $s['contact_info'] ?? null,
                'open_days' => $s['open_days'] ?? json_encode(['Lunes' => true, 'Martes' => true, 'Miércoles' => true, 'Jueves' => true, 'Viernes' => true, 'Sábado' => true, 'Domingo' => true]),
                'opening_status' => $s['opening_status'] ?? true,
                'user_id' => $s['user_id'] ?? 1,
                'terminos' => $s['terminos'] ?? true,
                'politicas' => $s['politicas'] ?? true,
            ];

            if (!empty($s['images']) && is_array($s['images'])) {
                foreach ($s['images'] as $field => $paths) {
                    $copied = $this->copyImage($paths['src'], $paths['dest'], $field);
                    if ($copied) {
                        $data[$field] = $copied;
                    }
                }
            }

            TuristicPlace::create($data);
        }
    }

    /**
     * Copiar imagen desde public/seeders/images/places a storage/app/public
     * @param string $sourceRelativePath ruta relativa dentro de public/seeders/images/places
     * @param string|null $destRelativePath ruta relativa destino dentro de storage/app/public
     * @return string|null ruta destino o null si no existe
     */
    private function copyImage(string $sourceRelativePath, ?string $destRelativePath = null, ?string $type = null): ?string
    {
        // Si source está vacío, retornar null sin error
        if (empty($sourceRelativePath)) {
            return null;
        }

        $basePublic = public_path('seeders/images/places/');
        $sourcePath = $basePublic . $sourceRelativePath;

        // If exact file exists, use it
        if (!File::exists($sourcePath)) {
            // 1. Buscar coincidencia aproximada en cualquier subcarpeta
            $basename = pathinfo($sourceRelativePath, PATHINFO_BASENAME);
            $nameNoExt = pathinfo($basename, PATHINFO_FILENAME);

            $found = null;
            $all = File::allFiles($basePublic);
            foreach ($all as $f) {
                $fname = $f->getFilename();
                if (strtolower($fname) === strtolower($basename)) {
                    $found = $f->getRealPath();
                    break;
                }
            }

            if (!$found) {
                // try contains match or alnum match
                $needle = preg_replace('/[^a-z0-9]/', '', strtolower($nameNoExt));
                foreach ($all as $f) {
                    $fname = $f->getFilename();
                    $cmp = preg_replace('/[^a-z0-9]/', '', strtolower(pathinfo($fname, PATHINFO_FILENAME)));
                    if ($needle !== '' && (strpos($cmp, $needle) !== false || strpos($needle, $cmp) !== false)) {
                        $found = $f->getRealPath();
                        break;
                    }
                }
            }

            if (!$found) {
                // try token matching: split requested name and match any significant token
                $tokens = preg_split('/[^a-z0-9]+/', strtolower($nameNoExt));
                foreach ($all as $f) {
                    $cmp = preg_replace('/[^a-z0-9]/', '', strtolower(pathinfo($f->getFilename(), PATHINFO_FILENAME)));
                    foreach ($tokens as $t) {
                        if (strlen($t) >= 3 && strpos($cmp, $t) !== false) {
                            $found = $f->getRealPath();
                            break 2;
                        }
                    }
                }
            }

            if (!$found) {
                // try removing common prefixes like 'cover' or 'portada' from requested name
                $trimmed = preg_replace('/^(cover|portada|imagen|img)[-_]*/', '', strtolower($nameNoExt));
                if ($trimmed !== $nameNoExt) {
                    $needle2 = preg_replace('/[^a-z0-9]/', '', $trimmed);
                    foreach ($all as $f) {
                        $cmp = preg_replace('/[^a-z0-9]/', '', strtolower(pathinfo($f->getFilename(), PATHINFO_FILENAME)));
                        if ($needle2 !== '' && (strpos($cmp, $needle2) !== false || strpos($needle2, $cmp) !== false)) {
                            $found = $f->getRealPath();
                            break;
                        }
                    }
                }
            }

            if ($found) {
                $sourcePath = $found;
            } else {
                // 2. Si no se encuentra, buscar imagen genérica según el tipo
                $generics = [
                    'cover' => 'portada/portada-UTP.jpg',
                    'Weather_img' => 'clima/clima-consota.webp',
                    'features_img' => 'caracteristicas/caracteristicas-laguna-otun.jpg',
                    'flora_img' => 'flora/flora-consota.jpg',
                    'estructure_img' => 'estructura/estructura-mistrato.jpg',
                ];
                if ($type && isset($generics[$type])) {
                    $genericSource = public_path('seeders/images/places/' . $generics[$type]);
                    if (File::exists($genericSource)) {
                        $dest = $destRelativePath ?? $generics[$type];
                        Storage::disk('public')->put($dest, File::get($genericSource));
                        return $dest;
                    }
                }
                echo "⚠️ Imagen no encontrada: {$sourcePath} y no se encontró genérica para tipo {$type}\n";
                return null;
            }
            // Try to find a close match by filename in any subfolder
            $basename = pathinfo($sourceRelativePath, PATHINFO_BASENAME);
            $nameNoExt = pathinfo($basename, PATHINFO_FILENAME);

            $found = null;
            $all = File::allFiles($basePublic);
            foreach ($all as $f) {
                $fname = $f->getFilename();
                if (strtolower($fname) === strtolower($basename)) {
                    $found = $f->getRealPath();
                    break;
                }
            }

            if (!$found) {
                // try contains match or alnum match
                $needle = preg_replace('/[^a-z0-9]/', '', strtolower($nameNoExt));
                foreach ($all as $f) {
                    $fname = $f->getFilename();
                    $cmp = preg_replace('/[^a-z0-9]/', '', strtolower(pathinfo($fname, PATHINFO_FILENAME)));
                    if ($needle !== '' && (strpos($cmp, $needle) !== false || strpos($needle, $cmp) !== false)) {
                        $found = $f->getRealPath();
                        break;
                    }
                }
            }

            if (!$found) {
                // try token matching: split requested name and match any significant token
                $tokens = preg_split('/[^a-z0-9]+/', strtolower($nameNoExt));
                foreach ($all as $f) {
                    $cmp = preg_replace('/[^a-z0-9]/', '', strtolower(pathinfo($f->getFilename(), PATHINFO_FILENAME)));
                    foreach ($tokens as $t) {
                        if (strlen($t) >= 3 && strpos($cmp, $t) !== false) {
                            $found = $f->getRealPath();
                            break 2;
                        }
                    }
                }
            }

            if (!$found) {
                // try removing common prefixes like 'cover' or 'portada' from requested name
                $trimmed = preg_replace('/^(cover|portada|imagen|img)[-_]*/', '', strtolower($nameNoExt));
                if ($trimmed !== $nameNoExt) {
                    $needle2 = preg_replace('/[^a-z0-9]/', '', $trimmed);
                    foreach ($all as $f) {
                        $cmp = preg_replace('/[^a-z0-9]/', '', strtolower(pathinfo($f->getFilename(), PATHINFO_FILENAME)));
                        if ($needle2 !== '' && (strpos($cmp, $needle2) !== false || strpos($needle2, $cmp) !== false)) {
                            $found = $f->getRealPath();
                            break;
                        }
                    }
                }
            }

            if ($found) {
                $sourcePath = $found;
            } else {
                echo "⚠️ Imagen no encontrada: {$basePublic}{$sourceRelativePath}\n";
                return null;
            }

        }

        $dest = $destRelativePath ?? $sourceRelativePath;

        // Ensure destination directory exists in storage
        $destDir = dirname($dest);
        if ($destDir !== '.' && !Storage::disk('public')->exists($destDir)) {
            Storage::disk('public')->makeDirectory($destDir);
        }

        Storage::disk('public')->put($dest, File::get($sourcePath));

        return $dest;
    }
}
