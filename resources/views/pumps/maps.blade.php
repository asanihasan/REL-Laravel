@extends('layouts.app')

@section('title', 'Pump Fleet Map')

@section('main_class', 'w-full')

@section('content')
    <div id="map" class="w-full h-[calc(100vh-140px)] z-10 bg-gray-900"></div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <style>
        /* Custom styles to make the clustering look good on a dark map */
        .marker-cluster-small { background-color: rgba(220, 38, 38, 0.6); }
        .marker-cluster-small div { background-color: rgba(185, 28, 28, 0.9); color: white; }
        
        .marker-cluster-medium { background-color: rgba(220, 38, 38, 0.5); }
        .marker-cluster-medium div { background-color: rgba(153, 27, 27, 0.9); color: white; }

        .marker-cluster-large { background-color: rgba(220, 38, 38, 0.4); }
        .marker-cluster-large div { background-color: rgba(127, 29, 29, 0.9); color: white; }

        /* Smooth out the Leaflet popup to match Tailwind styling */
        .leaflet-popup-content-wrapper {
            background-color: #1f2937; /* Tailwind gray-800 */
            color: #f3f4f6; /* Tailwind gray-100 */
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        }
        .leaflet-popup-tip {
            background-color: #1f2937;
        }
        .leaflet-popup-content {
            margin: 0; 
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Fetch the data we injected from the Controller
            const pumpData = @json($pumpsWithLocations);

            // 2. Initialize the map (Default center to Indonesia before data loads)
            const map = L.map('map').setView([-2.5489, 118.0149], 5);

            // 3. Add the Dark Theme Map Tiles (CartoDB Dark Matter)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // 4. Initialize the Cluster Group
            const markers = L.markerClusterGroup({
                chunkedLoading: true,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true
            });

            // 5. Loop through the data and create markers
            pumpData.forEach(pump => {
                // Ensure the pump has coordinates before trying to plot it
                if (pump.latitude && pump.longitude) {
                    
                    // Create the marker
                    const marker = L.marker([pump.latitude, pump.longitude]);

                    // Determine status color for the popup
                    const statusColor = pump.status === 'online' ? 'text-green-400' : 'text-red-500';
                    const statusDot = pump.status === 'online' ? 'bg-green-500' : 'bg-red-600';

                    // Build the HTML for the popup window using Tailwind classes
                    const popupContent = `
                        <div class="p-4 w-60">
                            <div class="flex justify-between items-center border-b border-gray-600 pb-2 mb-3">
                                <h3 class="font-bold text-lg truncate pr-2" title="${pump.name || 'Unnamed Pump'}">
                                    ${pump.name || 'Unnamed Pump'}
                                </h3>
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full ${statusDot} opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 ${statusDot}"></span>
                                </span>
                            </div>
                            
                            <div class="space-y-1 text-sm text-gray-300">
                                <p><strong class="text-gray-400">ID:</strong> ${pump.id}</p>
                                <p><strong class="text-gray-400">Status:</strong> <span class="${statusColor} font-bold uppercase">${pump.status}</span></p>
                                <p><strong class="text-gray-400">Location:</strong> ${pump.latitude}, ${pump.longitude}</p>
                            </div>

                            <div class="mt-4">
                                <a href="/pumps/${pump.id}" class="block w-full text-center bg-red-800 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium transition duration-150">
                                    View Telemetry
                                </a>
                            </div>
                        </div>
                    `;

                    // Bind the popup to the marker and add it to our cluster group
                    marker.bindPopup(popupContent);
                    markers.addLayer(marker);
                }
            });

            // 6. Add the fully populated cluster group to the map
            map.addLayer(markers);

            // 7. Auto-zoom and center the map to fit all the markers perfectly
            if (pumpData.length > 0) {
                map.fitBounds(markers.getBounds(), { padding: [50, 50] });
            }
        });
    </script>
@endsection