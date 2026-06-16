@extends('layouts.app')

@section('title', 'Pump Fleet Map')

@section('main_class', 'w-full')

@section('content')
    <div id="map" class="w-full h-[calc(100vh-140px)] z-10 bg-gray-100"></div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <style>
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
        /* Removes the default white square background from custom divIcons */
        .custom-div-icon {
            background: transparent;
            border: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pumpData = @json($pumpsWithLocations);

            // 1. Initialize map
            const map = L.map('map').setView([-2.5489, 118.0149], 5);

            // 2. Light Theme Map Tiles
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // 3. Initialize Cluster Group with Custom Dynamic Icons
            const markers = L.markerClusterGroup({
                chunkedLoading: true,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                
                // This function runs every time a cluster is formed or updated
                iconCreateFunction: function(cluster) {
                    const childMarkers = cluster.getAllChildMarkers();
                    let onlineCount = 0;
                    let offlineCount = 0;

                    // Count the status of all devices inside this specific cluster
                    childMarkers.forEach(marker => {
                        if (marker.options.pumpStatus === 'online') {
                            onlineCount++;
                        } else {
                            offlineCount++;
                        }
                    });

                    const total = childMarkers.length;
                    // Calculate percentage to draw the pie chart border
                    const onlinePercentage = (onlineCount / total) * 100;

                    // Create a CSS conic gradient (Green for online, Red for offline)
                    // e.g., if 50%, it draws green from 0-50%, and red from 50-100%
                    const gradient = `conic-gradient(#22c55e ${onlinePercentage}%, #ef4444 ${onlinePercentage}% 100%)`;

                    // Return the custom HTML for the cluster
                    return L.divIcon({
                        html: `
                            <div class="relative flex items-center justify-center rounded-full shadow-md" style="width: 44px; height: 44px; background: ${gradient}; padding: 4px;">
                                <div class="flex items-center justify-center w-full h-full bg-gray-800 rounded-full border border-gray-900">
                                    <span class="text-white font-bold text-sm">${total}</span>
                                </div>
                            </div>
                        `,
                        className: 'custom-div-icon',
                        iconSize: [44, 44]
                    });
                }
            });

            // 4. Create Individual Markers
            pumpData.forEach(pump => {
                if (pump.latitude && pump.longitude) {
                    
                    // Determine styling for the individual marker
                    const isOnline = pump.status === 'online';
                    const markerColor = isOnline ? 'bg-green-500' : 'bg-red-500';
                    const statusTextClass = isOnline ? 'text-green-600' : 'text-red-600';

                    // Build the custom HTML marker (Dot + Text underneath)
                    const markerHtml = `
                        <div class="flex flex-col items-center justify-center w-full">
                            <div class="w-4 h-4 rounded-full ${markerColor} border-2 border-white shadow-sm ring-2 ring-gray-200"></div>
                            <span class="text-[10px] font-bold text-gray-700 mt-1 bg-white/80 border border-gray-800 px-1.5 py-0.5 rounded shadow-sm whitespace-nowrap">
                                ${pump.name || 'Unnamed'}
                            </span>
                        </div>
                    `;

                    // Register the custom icon
                    const customIcon = L.divIcon({
                        html: markerHtml,
                        className: 'custom-div-icon',
                        iconSize: [80, 40], // Wide enough to hold the text
                        iconAnchor: [40, 8]  // Anchor the center of the colored dot to the exact coordinate
                    });

                    // Create the marker. Notice we are passing "pumpStatus" into the options!
                    // This allows the iconCreateFunction above to read it when clustering.
                    const marker = L.marker([pump.latitude, pump.longitude], { 
                        icon: customIcon,
                        pumpStatus: pump.status 
                    });

                    // Build the Popup HTML
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

                    marker.bindPopup(popupContent);
                    markers.addLayer(marker);
                }
            });

            // 5. Add clusters to map and auto-fit
            map.addLayer(markers);

            if (pumpData.length > 0) {
                map.fitBounds(markers.getBounds(), { padding: [50, 50] });
            }
        });
    </script>
@endsection