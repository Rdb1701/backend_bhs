<x-filament-widgets::widget>
    <x-filament::section>
        <div id="map" style="height: 500px; width: 100%; border: 1px solid #ccc;"></div>

        <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
        <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const token = '{{ env('MAPBOX_API_KEY') }}';
                mapboxgl.accessToken = token;
                
                try {
                    const map = new mapboxgl.Map({
                        container: 'map',
                        style: 'mapbox://styles/mapbox/streets-v11',
                        center: [125.6, 8.5],
                        zoom: 5
                    });

                    const locations = @json($this->getLocations());
                    const bounds = new mapboxgl.LngLatBounds();

                    locations.forEach(location => {
                        bounds.extend([location.longitude, location.latitude]);
                        
                        const popupContent = `
                            <strong>${location.property.name}</strong><br>
                            Price: ₱${location.property.price}<br>
                            Status: ${location.property.status}<br>
                            Type: ${location.property.room_type}
                        `;

                        new mapboxgl.Marker()
                            .setLngLat([location.longitude, location.latitude])
                            .setPopup(new mapboxgl.Popup().setHTML(popupContent))
                            .addTo(map);
                    });

                    map.fitBounds(bounds, {
                        padding: 50,
                        maxZoom: 15
                    });
                } catch (error) {
                    console.error('Map error:', error);
                }
            });
        </script>
    </x-filament::section>
</x-filament-widgets::widget>