@extends('layouts.app')

@section('title', 'Pump Detail: ' . $pump->name)

@section('content')
<!-- External Styles for History Section -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #1e3a8a !important; color: white !important; border: none; border-radius: 4px; }
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem 0.5rem; margin-bottom: 1rem; }
</style>

<div class="space-y-6">
    
    <!-- Responsive Header -->
    <div id="headerStatusContainer" class="bg-white p-6 rounded-lg shadow-md border-t-4 {{ $pump->status == 'online' ? 'border-green-500' : 'border-red-500' }} transition-colors duration-300">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            
            <div class="flex-grow">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $pump->name }}</h1>
                    <span class="px-2 py-1 bg-gray-100 text-xs font-mono rounded border text-gray-600">{{ $pump->id }}</span>
                </div>
                <p class="text-gray-500 mt-2 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    {{ $pump->location }}
                </p>
                
                <div class="flex items-center gap-4 mt-3">
                    <div class="flex items-center gap-1.5" title="Network Connection">
                        <div id="dot_network" class="w-2.5 h-2.5 rounded-full {{ strtolower($pump->connection ?? '') == 'online' ? 'bg-green-500' : 'bg-red-500' }} transition-colors duration-300"></div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Network</span>
                    </div>
                    <div class="flex items-center gap-1.5" title="Modbus Connection">
                        <div id="dot_modbus" class="w-2.5 h-2.5 rounded-full {{ $pump->modbus_status ? 'bg-green-500' : 'bg-red-500' }} transition-colors duration-300"></div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Modbus</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-row gap-3 md:flex-col items-center md:items-end justify-between md:justify-start bg-gray-50 md:bg-transparent p-3 md:p-0 rounded-lg">
                <div id="statusBadge" class="inline-flex items-center px-4 py-2 rounded-lg text-white font-bold shadow-sm {{ $pump->status == 'online' ? 'bg-green-600' : 'bg-red-600' }} transition-colors duration-300">
                    <span class="animate-pulse mr-2 text-xl">•</span> 
                    <span id="statusText">{{ ucfirst($pump->status) }}</span>
                </div>
                <div id="lastUpdateText" class="text-xs text-gray-500 font-mono mt-0 md:mt-2">
                    Updated: Loading...
                </div>
            </div>
        </div>
    </div>

    <!-- Remote Control Panel -->
    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-600 relative overflow-hidden">
        <div id="controlLoader" class="hidden absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-sm transition-all duration-300">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mb-2"></div>
                <span class="text-sm font-bold text-blue-800 animate-pulse">Sending Command...</span>
            </div>
        </div>

        <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Remote Control Panel
        </h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <button onclick="sendControl('start')" class="control-btn bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded shadow transition transform active:scale-95 flex justify-center items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Start
                </button>
                <button onclick="sendControl('stop')" class="control-btn bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded shadow transition transform active:scale-95 flex justify-center items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                    Stop
                </button>
                <button onclick="sendControl('auto')" class="control-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded shadow transition transform active:scale-95 flex justify-center items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Auto
                </button>
                <button onclick="sendControl('reset')" class="control-btn bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded shadow transition transform active:scale-95 flex justify-center items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Reset
                </button>
            </div>

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Set Target RPM</label>
                <div class="flex gap-2 md:flex-row flex-col">
                    <input type="number" id="rpmInput" placeholder="800 - 2000" min="800" max="2000" class="flex-grow border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button onclick="setRpm()" class="control-btn bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 px-6 rounded shadow transition hover:shadow-lg whitespace-nowrap">Set RPM</button>
                </div>
            </div>
        </div>
        <div id="controlMessage" class="hidden mt-4 p-3 rounded text-sm font-bold border-l-4"></div>
    </div>

    <!-- Real-time Data Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Engine</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded text-center">
                        <span class="block text-xs text-blue-600 font-bold uppercase tracking-wider">RPM</span>
                        <span id="disp_rpm" class="text-2xl font-bold text-gray-800">{{ $pump->rpm }}</span>
                    </div>
                    <div class="bg-blue-50 p-4 rounded text-center">
                        <span class="block text-xs text-blue-600 font-bold uppercase tracking-wider">Load</span>
                        <span class="text-2xl font-bold text-gray-800"><span id="disp_load">{{ $pump->percent_load }}</span>%</span>
                    </div>
                    <div class="col-span-2 flex justify-between border-b pb-0">
                        <span class="text-sm text-gray-500">Engine Hours</span>
                        <span class="font-medium font-mono"><span id="disp_engine_hours">{{ $pump->engine_hours }}</span> h</span>
                    </div>
                    <div class="col-span-2 flex justify-between border-b pb-0">
                        <span class="text-sm text-gray-500">Engine Coolant Temp</span>
                        <span class="font-medium font-mono"><span id="disp_engine_hours">{{ $pump->coolant_temp }}</span> °C</span>
                    </div>
                    <div class="col-span-2 flex justify-between border-b pb-0">
                        <span class="text-sm text-gray-500">Fuel Rate</span>
                        <span class="font-medium font-mono"><span id="disp_fuel_rate">{{ $pump->fuel_rate }}</span> L/h</span>
                    </div>
                    <div class="col-span-2 flex justify-between border-b pb-0">
                        <span class="text-sm text-gray-500">Fuel Level</span>
                        <span class="font-medium font-mono"><span id="disp_fuel_rate">{{ $pump->fuel_level }}</span> %</span>
                    </div>
                    <div class="col-span-2 flex justify-between border-b pb-0">
                        <span class="text-sm text-gray-500">Oil Pressure</span>
                        <span class="font-medium font-mono"><span id="disp_fuel_rate">{{ $pump->oil_pressure }}</span> PSI</span>
                    </div>
                    <div class="col-span-2 flex justify-between border-b pb-0">
                        <span class="text-sm text-gray-500">Battery Voltage</span>
                        <span class="font-medium font-mono"><span id="disp_fuel_rate">{{ $pump->battery_potential }}</span> V</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md h-fit">
            <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Sensors</h3>
            <div class="space-y-6">
                <div class="bg-blue-50 p-4 rounded text-center">
                    <span class="block text-xs text-blue-600 font-bold uppercase tracking-wider">FLOW</span>
                    <span id="disp_rpm" class="text-2xl font-bold text-gray-800">{{ $pump->flow }}</span>
                </div>
                <!-- <div> -->
                    <!-- <h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Temperatures (°C)</h4> -->
                    <!-- <div class="space-y-2"> -->
                        <div class="flex justify-between border-b border-gray-100"><span>Coolant</span> <span class="font-bold" id="disp_coolant_temp">{{ $pump->coolant_temp }}</span></div>
                        <!-- <div class="flex justify-between border-b border-gray-100"><span>Oil</span> <span class="font-bold" id="disp_oil_temp">{{ $pump->oil_temp }}</span></div>
                        <div class="flex justify-between border-b border-gray-100"><span>Pump</span> <span class="font-bold" id="disp_pump_temp">{{ $pump->pump_temp }}</span></div> -->
                        <div class="flex justify-between border-b border-gray-100"><span>Oil</span> <span class="font-bold" id=""> - </span></div>
                        <div class="flex justify-between border-b border-gray-100"><span>Pump</span> <span class="font-bold" id=""> - </span></div>
                    <!-- </div> -->
                <!-- </div> -->
                <!-- <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Pressures (PSI)</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between border-b border-gray-100"><span>Oil</span> <span class="font-bold" id="disp_oil_pressure">{{ $pump->oil_pressure }}</span></div>
                        <div class="flex justify-between border-b border-gray-100"><span>Suction</span> <span class="font-bold" id="disp_suction_pressure">{{ $pump->suction_pressure }}</span></div>
                        <div class="flex justify-between border-b border-gray-100 bg-blue-50 px-2 rounded font-bold"><span>Discharge</span> <span class="text-blue-700" id="disp_discharge_pressure">{{ $pump->pump_press2 }}</span></div>
                    </div>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Flow Rate (L/s)</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between border-b border-gray-100 bg-green-50 px-2 rounded font-bold">
                            <span>Flow</span> 
                            <span class="text-green-700" id="disp_flow">{{ $pump->pressure_or_flow['flow'] ?? 0 }}</span>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">System</h3>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="bg-gray-50 p-3 rounded border">
                        <span class="block text-xs text-gray-500 uppercase">Battery</span>
                        <span class="text-lg font-mono font-bold text-gray-800"><span id="disp_battery">{{ $pump->battery_potential }}</span> V</span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded border">
                        <span class="block text-xs text-gray-500 uppercase">System</span>
                        <span class="text-lg font-mono font-bold text-gray-800"><span id="disp_system">{{ $pump->electrical_potential }}</span> V</span>
                    </div>
                </div>
            </div>
            <div id="controllerModeContainer" class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Controller Mode</h3>
                <!-- Content handled by JS -->
            </div>
        </div>
    </div>

    <!-- Historical Logs Section -->
    <div class="bg-white p-5 rounded-lg shadow-md border-t-4 border-gray-800 mt-2">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-3">
            <h3 class="text-xl font-bold text-gray-800">Historical Logs</h3>
            <div class="flex flex-wrap items-center gap-2">
                <input type="text" id="dateRangePicker" class="border rounded px-3 py-1 text-sm w-56 focus:ring-2 focus:ring-blue-500" placeholder="Select Range">
                <button onclick="loadHistory()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-bold">Filter</button>
                <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-bold">Export XLS</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="historyTable" class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th>Timestamp</th>
                        <th>Eng Hours Mode</th>
                        <th>Coolant°C</th>
                        <th>Load%</th>
                        <th>Fuel Rate</th>
                        <th>Fuel Level%</th>
                        <th>Pressure</th>
                        <th>Flow</th>
                        <th>Discharge</th>
                        <th>Suction</th>
                        <th>Dam Level</th>
                        <th>Fault Code</th>
                        <th>Voltage</th>
                    </tr>
                </thead>
                <tbody class="divide-y"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    let historyDataTable;
    let flatpickrInstance;
    let currentHistoryData = []; // Full raw data for export

    /**
     * Helper: Convert Database UTC String to Browser Local String
     * Appends " UTC" to ensure JS treats the input correctly regardless of browser locale
     */
    function getLocalTime(isoString) {
        if (!isoString) return 'N/A';
        // If string lacks 'Z' or offset, append ' UTC' so constructor treats it as UTC
        let utcString = isoString;
        if (!utcString.includes('Z') && !utcString.includes('+')) {
            utcString += ' UTC';
        }
        const date = new Date(utcString);
        return date.toLocaleString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }

    /**
     * Helper: Format Local Date to UTC string for Backend Query
     * This ensures the backend (Carbon) receives UTC values for SQL querying.
     */
    function formatToUTC(date) {
        const pad = (n) => n.toString().padStart(2, '0');
        return `${date.getUTCFullYear()}-${pad(date.getUTCMonth() + 1)}-${pad(date.getUTCDate())} ${pad(date.getUTCHours())}:${pad(date.getUTCMinutes())}:${pad(date.getUTCSeconds())}`;
    }

    // --- 1. Historical Logic ---
    function loadHistory() {
        const range = flatpickrInstance.selectedDates;
        let url = `/pumps/{{ $pump->id }}/history`;
        
        if (range.length > 0) {
            let startDate = new Date(range[0]);
            let endDate = range[1] ? new Date(range[1]) : new Date(range[0]);

            if (startDate.toDateString() === endDate.toDateString()) {
                startDate.setHours(0, 0, 0, 0);
                endDate.setHours(23, 59, 59, 999);
            }
            url += `?start=${encodeURIComponent(formatToUTC(startDate))}&end=${encodeURIComponent(formatToUTC(endDate))}`;
        }

        if (historyDataTable) historyDataTable.destroy();
        $('#historyTable tbody').html('<tr><td colspan="9" class="text-center py-10">Loading history logs...</td></tr>');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                currentHistoryData = data; 
                const tbody = $('#historyTable tbody').empty();
                
                data.forEach(row => {
                    // Logic to parse auto/manual mode
                    let autoMode = "-";
                    if (row.auto_manual_status) {
                        const status = typeof row.auto_manual_status === 'string' ? JSON.parse(row.auto_manual_status) : row.auto_manual_status;
                        if (status.auto) autoMode = "Auto";
                        else if (status.manual) autoMode = "Manual";
                    }

                    // Handle nested pressure/flow object
                    const pf = typeof row.pressure_or_flow === 'string' ? JSON.parse(row.pressure_or_flow) : row.pressure_or_flow;

                    tbody.append(`
                        <tr>
                            <td class="px-4 py-2 font-mono whitespace-nowrap">${getLocalTime(row.ts)}</td>
                            <td class="px-4 py-2">${autoMode}</td>
                            <td class="px-4 py-2">${row.coolant_temp ?? '-'}</td>
                            <td class="px-4 py-2">${row.percent_load ?? '-'}</td>
                            <td class="px-4 py-2">${row.fuel_rate ?? '-'}</td>
                            <td class="px-4 py-2">${row.fuel_level ?? '-'}</td>
                            <td class="px-4 py-2">${pf?.pressure ?? '-'}</td>
                            <td class="px-4 py-2">${pf?.flow ?? '-'}</td>
                            <td class="px-4 py-2">${row.pump_press2 ?? '-'}</td>
                            <td class="px-4 py-2">${row.suction_pressure ?? '-'}</td>
                            <td class="px-4 py-2">${row.dam_level ?? '-'}</td>
                            <td class="px-4 py-2 font-mono text-red-600">${row.fault_code ?? '-'}</td>
                            <td class="px-4 py-2">${row.battery_potential ?? '-'}</td>
                        </tr>
                    `);
                });
                
                historyDataTable = $('#historyTable').DataTable({ 
                    order: [[0, 'desc']], 
                    pageLength: 10,
                    scrollX: true // Since we added many columns, this is now necessary
                });
            },
            error: function() {
                $('#historyTable tbody').html('<tr><td colspan="9" class="text-center py-10 text-red-600">Failed to load historical data.</td></tr>');
            }
        });
    }

    function exportToExcel() {
        if (!currentHistoryData || currentHistoryData.length === 0) {
            alert("No data available to export. Please filter some results first.");
            return;
        }

        const exportRows = currentHistoryData.map(row => {
            const status = typeof row.auto_manual_status === 'string' ? JSON.parse(row.auto_manual_status) : row.auto_manual_status;
            const pf = typeof row.pressure_or_flow === 'string' ? JSON.parse(row.pressure_or_flow) : row.pressure_or_flow;
            
            return {
                'Timestamp': getLocalTime(row.ts),
                'Eng Hours Mode': status.auto ? 'Auto' : (status.manual ? 'Manual' : '-'),
                'Coolant°C': row.coolant_temp,
                'Load (%)': row.percent_load,
                'Fuel Rate': row.fuel_rate,
                'Fuel Level (%)': row.fuel_level,
                'Pressure': pf?.pressure,
                'Flow': pf?.flow,
                'Discharge': row.pump_press2,
                'Suction': row.suction_pressure,
                'Dam Level': row.dam_level,
                'Fault Code': row.fault_code,
                'Voltage': row.battery_potential
            };
        });


        const ws = XLSX.utils.json_to_sheet(exportRows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Pump History");
        XLSX.writeFile(wb, `Pump_{{ $pump->id }}_Log_${new Date().getTime()}.xlsx`);
    }

    // --- 2. Control Logic ---
    function sendControl(action, value = null) {
        if (!confirm('Send command: ' + action + '?')) return;
        const loader = $('#controlLoader').removeClass('hidden');
        $.ajax({
            url: `/pumps/{{ $pump->id }}/control`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', action: action, value: value },
            success: (res) => $('#controlMessage').html('✅ ' + res.message).addClass('bg-green-100 text-green-800').removeClass('hidden'),
            error: (xhr) => $('#controlMessage').html('❌ ' + (xhr.responseJSON?.message || 'Error')).addClass('bg-red-100 text-red-800').removeClass('hidden'),
            complete: () => loader.addClass('hidden')
        });
    }

    function setRpm() {
        const rpmValue = $('#rpmInput').val();
        
        // Convert to a number for accurate comparison
        const rpm = Number(rpmValue);
    
        if (rpmValue === "") {
            alert("Please enter an RPM value.");
            return;
        }
    
        // Check if the value is within the 800 - 2000 range
        if (rpm >= 800 && rpm <= 2000) {
            sendControl('rpm', rpm);
        } else {
            alert("Warning: RPM must be between 800 and 2000.");
        }
    }


    // --- 3. Initialize ---
    $(document).ready(function() {
        flatpickrInstance = flatpickr("#dateRangePicker", {
            mode: "range", 
            enableTime: true, 
            dateFormat: "Y-m-d H:i",
            defaultDate: [new Date(Date.now() - 24 * 60 * 60 * 1000), new Date()]
        });
        
        loadHistory();

        // Real-time Dashboard Update (1000ms)
        setInterval(function() {
            $.ajax({
                url: `/pumps/{{ $pump->id }}/data`,
                method: 'GET',
                success: function(data) {
                    const isOnline = data.status === 'online';
                    $('#headerStatusContainer').toggleClass('border-green-500', isOnline).toggleClass('border-red-500', !isOnline);
                    $('#statusBadge').toggleClass('bg-green-600', isOnline).toggleClass('bg-red-600', !isOnline);
                    $('#statusText').text(isOnline ? 'Online' : 'Offline');
                    $('#lastUpdateText').text('Updated: ' + getLocalTime(data.last_update));
                    
                    $('#disp_rpm').text(data.rpm);
                    $('#disp_load').text(data.percent_load);
                    $('#disp_coolant_temp').text(data.coolant_temp);
                    $('#disp_oil_temp').text(data.oil_temp);
                    $('#disp_pump_temp').text(data.pump_temp);
                    $('#disp_oil_pressure').text(data.oil_pressure);
                    $('#disp_suction_pressure').text(data.suction_pressure);
                    $('#disp_discharge_pressure').text(data.pump_press2);
                    $('#disp_flow').text(data.pressure_or_flow?.flow ?? '0');
                    $('#disp_battery').text(data.battery_potential);
                    $('#disp_system').text(data.electrical_potential);
                    $('#disp_engine_hours').text(data.engine_hours);
                    $('#disp_fuel_rate').text(data.fuel_rate);
                    $('#disp_fuel_level_text').text(data.fuel_level);
                    $('#disp_fuel_level_bar').css('width', Math.min(data.fuel_level, 100) + '%');
                    $('#dot_network').removeClass('bg-green-500 bg-red-500').addClass((data.connection || '').toLowerCase() === 'online' ? 'bg-green-500' : 'bg-red-500');
                    $('#dot_modbus').removeClass('bg-green-500 bg-red-500').addClass(isOnline ? 'bg-green-500' : 'bg-red-500');
                    
                    renderDigitalInputs(data.digital_inputs);
                    renderControllerMode(data.auto_manual_status);
                }
            });
        }, 1000);
    });

    function renderDigitalInputs(inputs) {
        const c = $('#digitalInputsContainer').empty();
        $.each(inputs || {}, (k, v) => {
            if (k === 'remote_start' || k === 'remote_stop') {
                const active = v.active;
                const mode = v.mode;
                c.append(`<div class="flex justify-between p-2 rounded ${active ? 'bg-green-100 text-green-800' : 'bg-gray-50 text-gray-400'}"><span class="capitalize text-xs font-semibold">${k.replace(/_/g, ' ')}</span><span class="font-bold text-xs">${mode}</span></div>`);
            }
        });
        $.each(inputs || {}, (k, v) => {
            if (k !== 'remote_start' && k !== 'remote_stop') {
                const active = typeof v === 'object' ? v.active : v;
                const mode = typeof v === 'object' ? v.mode : "";
                c.append(`<div class="flex justify-between p-2 rounded ${active ? 'bg-green-100 text-green-800' : 'bg-gray-50 text-gray-400'}"><span class="capitalize text-xs font-semibold">${k.replace(/_/g, ' ')}</span><span class="font-bold text-xs">${mode}</span></div>`);
            }
        });
    }

    function renderControllerMode(modes) {
        const c = $('#controllerModeContainer').find('.mode-item').remove().end();
        $.each(modes || {}, (k, active) => {
            c.append(`<div class="mode-item flex items-center justify-between p-1"><span class="text-sm capitalize ${active ? 'text-gray-800 font-bold' : 'text-gray-400'}">${k.replace(/_/g, ' ')}</span><div class="w-3 h-3 rounded-full ${active ? 'bg-green-500 shadow-sm' : 'bg-gray-200'}"></div></div>`);
        });
    }
</script>
@endsection
