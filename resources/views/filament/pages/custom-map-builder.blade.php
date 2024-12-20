<x-filament-panels::page>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.8.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.8.0/mapbox-gl.js"></script>

    <h1 class="text-2xl font-bold mb-4 text-blue-800">Property Location Selection</h1>
    
    <div id="map" style="height: 600px; border: 2px solid #3B82F6; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>

    <form id="locationForm" method="POST" action="{{ route('save-location') }}" class="mt-4 space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="property_id" class="block font-bold mb-1 text-gray-700">Select Property</label>
                <select name="property_id" id="property_id" class="form-select w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                    <option value="">Select a Property</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
                <p id="property-error" class="text-red-500 text-sm mt-1 hidden" style="color:red;">Please select a property</p>
            </div>
            
            <div>
                <label for="location_name" class="block font-bold mb-1 text-gray-700">Location Name/Title</label>
                <input type="text" id="location_name" name="location_name" 
                       class="form-input w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200" 
                       placeholder="Enter location name (optional)">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="latitude" class="block font-bold mb-1 text-gray-700">Latitude</label>
                <input type="text" id="latitude" name="latitude" 
                       class="form-input w-full bg-gray-100 cursor-not-allowed" 
                       readonly>
                <p id="location-error" class="text-red-500 text-sm mt-1 hidden" style="color:red;">Please set a location by dragging the pin</p>
            </div>
            <div>
                <label for="longitude" class="block font-bold mb-1 text-gray-700">Longitude</label>
                <input type="text" id="longitude" name="longitude" 
                       class="form-input w-full bg-gray-100 cursor-not-allowed" 
                       readonly>
            </div>
        </div>

        <button type="submit" class="w-full hover:bg-red-600 text-white py-3 px-6 rounded-lg shadow-lg transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-300 ease-in-out text-xl font-semibold" style="background-color: #3b82f6;">
            Save Location
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Agusan del Sur focused coordinates
            const AGUSAN_DEL_SUR_CENTER = [125.50, 8.30];

            mapboxgl.accessToken = '{{ env('MAPBOX_API_KEY') }}';

            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: AGUSAN_DEL_SUR_CENTER,
                zoom: 10
            });

            // Add advanced map controls
            map.addControl(new mapboxgl.NavigationControl());
            map.addControl(new mapboxgl.FullscreenControl());

            // Custom marker with improved visibility
            const markerElement = document.createElement('div');
            markerElement.innerHTML = `
                <svg width="40" height="40" viewBox="0 0 24 24" fill="#FF4136" stroke="#FFFFFF" stroke-width="2">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
            `;
            markerElement.style.cursor = 'pointer';
            markerElement.style.width = '40px';
            markerElement.style.height = '40px';

            const marker = new mapboxgl.Marker({ 
                element: markerElement,
                draggable: true,
            })
            .setLngLat(AGUSAN_DEL_SUR_CENTER)
            .addTo(map);

            // Add popup for marker interaction
            const popup = new mapboxgl.Popup({ offset: 25 });
            marker.getElement().addEventListener('mouseenter', () => {
                popup.setLngLat(marker.getLngLat())
                    .setHTML('<p class="text-sm font-bold">Drag to set location</p>')
                    .addTo(map);
            });

            marker.getElement().addEventListener('mouseleave', () => {
                popup.remove();
            });

            marker.on('dragend', () => {
                const lngLat = marker.getLngLat();
                document.getElementById('latitude').value = lngLat.lat.toFixed(6);
                document.getElementById('longitude').value = lngLat.lng.toFixed(6);
                
                // Hide location error if location is set
                document.getElementById('location-error').classList.add('hidden');
                popup.remove();
            });

            // Form submission handling
            const form = document.getElementById('locationForm');
            const propertySelect = document.getElementById('property_id');
            const propertyError = document.getElementById('property-error');
            const locationError = document.getElementById('location-error');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset previous error states
                propertyError.classList.add('hidden');
                locationError.classList.add('hidden');
                
                // Validate property selection
                const selectedProperty = propertySelect.value;
                const latitude = document.getElementById('latitude').value;
                const longitude = document.getElementById('longitude').value;

                let isValid = true;

                // Check if property is selected
                if (!selectedProperty) {
                    propertyError.classList.remove('hidden');
                    isValid = false;
                }

                // Check if location is set
                if (!latitude || !longitude) {
                    locationError.classList.remove('hidden');
                    isValid = false;
                }

                // If validation passes, submit the form
                if (isValid) {
                    alert('Form is being submitted. Location will be saved.');
                    this.submit();
                }
            });
        });
    </script>
</x-filament-panels::page>