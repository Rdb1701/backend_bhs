<div>
    <!-- Map Container -->
    <div id="map" style="height: 500px; width: 100%; border: 1px solid #ccc; margin-top: 10px;"></div>

    <!-- Mapbox GL JS and CSS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapboxToken = '{{ env('MAPBOX_API_KEY') }}';
            if (!mapboxToken) {
                console.error('Mapbox API Key is missing. Please set MAPBOX_API_KEY in your .env file.');
                return;
            }

            mapboxgl.accessToken = mapboxToken;

            // Default coordinates for Agusan del Sur
            const defaultCoords = [125.86, 8.45]; // Longitude, Latitude

            // Initialize the map
            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: defaultCoords,
                zoom: 12,
            });

            // Add navigation controls
            map.addControl(new mapboxgl.NavigationControl());

            // Create a marker
            const marker = new mapboxgl.Marker({ 
                draggable: true,
                color: "#FF0000"  // Red marker for visibility
            })
            .setLngLat(defaultCoords)
            .addTo(map);

            // Function to update Latitude and Longitude display fields
            function updateCoordinateFields(lng, lat) {
                // Select the display fields
                const latDisplay = document.getElementById('lat-display');
                const longDisplay = document.getElementById('long-display');

                // Update the display
                if (latDisplay && longDisplay) {
                    latDisplay.textContent = `Latitude: ${lat.toFixed(6)}`;
                    longDisplay.textContent = `Longitude: ${lng.toFixed(6)}`;
                }

                // Update the input fields as well for copying
                const latField = document.querySelector('input[name="lat"]');
                const longField = document.querySelector('input[name="long"]');

                if (latField && longField) {
                    latField.value = lat.toFixed(6);
                    longField.value = lng.toFixed(6);
                }
            }

            // Update marker and fields when dragged
            marker.on('dragend', () => {
                const lngLat = marker.getLngLat();
                updateCoordinateFields(lngLat.lng, lngLat.lat);
            });

            // Update marker and fields on map click
            map.on('click', (e) => {
                const { lng, lat } = e.lngLat;
                marker.setLngLat([lng, lat]); // Move marker to the clicked location
                updateCoordinateFields(lng, lat);
            });

            // Try to initialize marker from existing field values if any
            const initialLatField = document.querySelector('input[name="lat"]');
            const initialLongField = document.querySelector('input[name="long"]');

            if (initialLatField && initialLongField) {
                const initialLat = parseFloat(initialLatField.value);
                const initialLng = parseFloat(initialLongField.value);

                if (!isNaN(initialLat) && !isNaN(initialLng)) {
                    marker.setLngLat([initialLng, initialLat]);
                    map.flyTo({ center: [initialLng, initialLat], zoom: 14 });
                    updateCoordinateFields(initialLng, initialLat); // Update the display when initialized
                }
            }

        });
    </script>

    <!-- Coordinates Display -->
    <div style="margin-top: 10px;">
        <p id="lat-display">Latitude: N/A</p>
        <p id="long-display">Longitude: N/A</p>
    </div>
</div>
