import L from 'leaflet';

// Importación de las imágenes desde Vite:
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Corrección del icono por defecto
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

export default function loadMap() {

    const map = L.map('map').setView([6.2442, -75.5812], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    // Crear el marcador inicial
    let marker = L.marker([6.2442, -75.5812]).addTo(map);

    // Mover el marcador al hacer clic en el mapa
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        console.log('Coordenadas seleccionadas:', e.latlng);
        // Actualizar el campo de localización con las coordenadas
        
         document.getElementById('lat').value = e.latlng.lat.toFixed(8);
    document.getElementById('lng').value = e.latlng.lng.toFixed(8);
    });

}
// Función para VER sitio (mostrar ubicación guardada)
export function showPlaceMap(lat, lng, placeName) {
    const map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    // Marcador fijo en la ubicación del sitio
    const marker = L.marker([lat, lng]).addTo(map);
    
    // Popup con el nombre del sitio
    marker.bindPopup(`<b>${placeName}</b>`).openPopup();
}