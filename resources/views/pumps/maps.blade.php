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
        
        /* Smooth out the Leaflet tooltip to match Tailwind Dark styling */
        .leaflet-tooltip {
            background-color: #1f2937; /* Tailwind gray-800 */
            color: #f3f4f6; /* Tailwind gray-100 */
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            padding: 0;
        }
        /* Hide the default tiny triangle pointer for a cleaner look */
        .leaflet-tooltip::before, .leaflet-tooltip::after {
            display: none !important;
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
            const map = L.map('map').setView([-1.56, 117.89], 5.75);

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
                    const total = childMarkers.length;
                    
                    // Divide the circle into equal slices based on the number of devices
                    const sliceAngle = 360 / total;
                    
                    // Set a small gap (max 4 degrees)
                    const gapAngle = Math.min(4, sliceAngle * 0.15);

                    let gradientParts = [];
                    
                    // Loop through each device to build its specific segment of the dashed border
                    childMarkers.forEach((marker, index) => {
                        const isOnline = marker.options.pumpStatus === 'online';
                        const color = isOnline ? '#22c55e' : '#ef4444'; // Green or Red

                        const startAngle = index * sliceAngle;
                        const endAngle = startAngle + sliceAngle - gapAngle;

                        // 1. Draw the colored segment for the device
                        gradientParts.push(`${color} ${startAngle}deg ${endAngle}deg`);
                        
                        // 2. Draw the transparent gap right after it
                        gradientParts.push(`transparent ${endAngle}deg ${startAngle + sliceAngle}deg`);
                    });

                    // Combine all the segments into one CSS string
                    const gradient = `conic-gradient(${gradientParts.join(', ')})`;

                    // Return the custom HTML for the cluster
                    return L.divIcon({
                        html: `
                            <div class="relative flex items-center justify-center rounded-full shadow-md" style="width: 44px; height: 44px; background: ${gradient}; padding: 4px;">
                                <div class="flex items-center justify-center w-full h-full bg-blue-300 rounded-full border border-gray-100">
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
                    
                    const isOnline = pump.status === 'online';
                    const statusDot = isOnline ? 'bg-green-500' : 'bg-red-600';
                    const pinFillColor = isOnline ? '#22c55e' : '#ef4444';

                    // Build the custom HTML marker
                    const markerHtml = `
                        <div class="flex items-center justify-center w-full h-full">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${pinFillColor}" class="w-8 h-8 drop-shadow-md stroke-white stroke-2">
                                <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742c1.002-.722 2.607-1.99 3.61-3.376C18.225 16.826 20 14.434 20 11a8 8 0 10-16 0c0 3.434 1.775 5.826 2.78 7.218 1.003 1.387 2.608 2.654 3.61 3.376a16.974 16.974 0 001.144.742zM12 13.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    `;

                    const customIcon = L.divIcon({
                        html: markerHtml,
                        className: 'custom-div-icon',
                        iconSize: [32, 32],      
                        iconAnchor: [16, 32]     
                    });

                    const marker = L.marker([pump.latitude, pump.longitude], { 
                        icon: customIcon,
                        pumpStatus: pump.status 
                    });

                    // Grab the pre-saved location from the database, or provide a fallback
                    const displayLocation = pump.location ? pump.location : 'Unknown Area';

                    // --- HTML Templates ---
                    const generatePopupHtml = () => {
                        const isRunning = pump.auto_manual_status && pump.auto_manual_status.engine_running;
                        const flowRate = pump.pressure_or_flow && pump.pressure_or_flow.flow ? pump.pressure_or_flow.flow : 0;
                        const rpm = pump.rpm || 0;

                        const engineStateHtml = isRunning 
                            ? '<span class="text-green-400 font-medium">Running</span>' 
                            : '<span class="text-gray-500 font-medium">Stopped</span>';

                        return `
                            <div class="p-4 w-60">
                                <div class="flex justify-between items-center border-b border-gray-600 pb-2 mb-3">
                                    <h3 class="font-bold text-lg truncate pr-2 text-gray-100" title="${pump.name || 'Unnamed Pump'}">
                                        ${pump.name || 'Unnamed Pump'}
                                    </h3>
                                    <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full ${statusDot} opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 ${statusDot}"></span>
                                    </span>
                                </div>
                                
                                <div class="space-y-1 text-sm text-gray-300">
                                    <p><strong class="text-gray-400">ID:</strong> ${pump.id}</p>
                                    <p><strong class="text-gray-400">Engine:</strong> ${engineStateHtml}</p>
                                    <p><strong class="text-gray-400">RPM:</strong> <span class="text-gray-200">${rpm}</span></p>
                                    <p><strong class="text-gray-400">Flow:</strong> <span class="text-gray-200">${flowRate} L/s</span></p>
                                    
                                    <p class="whitespace-normal break-words"><strong class="text-gray-400">Location:</strong> 
                                        <span class="text-gray-200 font-medium">${displayLocation}</span>
                                    </p>
                                </div>
                                @if(auth()->user()->hasPermission('view'))
                                <div class="mt-4 flex gap-2 w-full">
                                    <a href="/pumps/${pump.id}" class="w-1/2 text-center bg-gray-800 hover:bg-gray-700 text-white px-2 py-2 rounded-md text-xs font-medium transition duration-150">
                                        Grid View
                                    </a>
                                    <a href="/pumps/${pump.id}/monitor" class="w-1/2 text-center bg-gray-600 hover:bg-gray-500 text-white px-2 py-2 rounded-md text-xs font-medium transition duration-150">
                                        Graph View
                                    </a>
                                </div>
                                @endif
                            </div>
                        `;
                    };

                    const generateTooltipHtml = () => `
                        <div class="p-3 w-48">
                            <div class="flex justify-between items-center border-b border-gray-600 pb-2 mb-3">
                                <h3 class="font-bold text-lg truncate pr-2 text-gray-100" title="${pump.name || 'Unnamed Pump'}">
                                    ${pump.name || 'Unnamed Pump'}
                                </h3>
                                <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full ${statusDot} opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 ${statusDot}"></span>
                                </span>
                            </div>
                            <div class="text-xs text-gray-300 space-y-1">
                                <p class="whitespace-normal break-words"><strong class="text-gray-400">Location:</strong> 
                                    <span class="text-gray-200 font-medium">${displayLocation}</span>
                                </p>
                            </div>
                        </div>
                    `;

                    // Bind directly with the final templates
                    marker.bindPopup(generatePopupHtml(), {
                        minWidth: 240
                    });
                    
                    marker.bindTooltip(generateTooltipHtml(), {
                        direction: 'top',
                        offset: [0, -20], 
                        className: 'custom-leaflet-tooltip'
                    });

                    markers.addLayer(marker);
                }
            });

            // 5. Add clusters to map
            map.addLayer(markers);
        });
    </script>
@endsection