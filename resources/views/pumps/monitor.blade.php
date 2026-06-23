@extends('layouts.app')

@section('title', 'Pump Graphical Detail: ' . $pump->name)

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #1e3a8a !important; color: white !important; border: none; border-radius: 4px; }
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem 0.5rem; margin-bottom: 1rem; }
    
    /* Smooth color transition for indicators */
    .indicator-circle { transition: all 0.3s ease; }
</style>

<div class="space-y-3">
    
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

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
        <div class="bg-white p-3 rounded-lg shadow-sm border text-center flex flex-col justify-center">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Oil Pressure</span>
            <div class="text-2xl font-bold text-gray-800"><span id="val_oil_pressure">{{ $pump->oil_pressure ?? 0 }}</span> <span class="text-xs text-gray-500 font-normal">PSI</span></div>
        </div>
        <div class="bg-white p-3 rounded-lg shadow-sm border text-center flex flex-col justify-center">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Battery</span>
            <div class="text-2xl font-bold text-gray-800"><span id="val_battery">{{ $pump->battery_potential ?? 0 }}</span> <span class="text-xs text-gray-500 font-normal">V</span></div>
        </div>
        <div class="bg-white p-3 rounded-lg shadow-sm border text-center flex flex-col justify-center">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Engine Hour</span>
            <div class="text-2xl font-bold text-gray-800"><span id="val_engine_hours">{{ $pump->engine_hours ?? 0 }}</span> <span class="text-xs text-gray-500 font-normal">h</span></div>
        </div>
        <div class="bg-white p-3 rounded-lg shadow-sm border text-center flex flex-col justify-center">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Fuel Rate</span>
            <div class="text-2xl font-bold text-gray-800"><span id="val_fuel_rate">{{ $pump->fuel_rate ?? 0 }}</span> <span class="text-xs text-gray-500 font-normal">L/h</span></div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
        <div class="bg-white py-4 rounded-lg shadow-sm border flex flex-col items-center justify-center gap-3">
            <div id="ind_engine_running" class="indicator-circle w-5 h-5 rounded-full bg-gray-300 ring-4 ring-gray-300/30"></div>
            <span class="text-[11px] uppercase font-bold text-gray-600 text-center">Engine Running</span>
        </div>
        <div class="bg-white py-4 rounded-lg shadow-sm border flex flex-col items-center justify-center gap-3">
            <div id="ind_auto" class="indicator-circle w-5 h-5 rounded-full bg-gray-300 ring-4 ring-gray-300/30"></div>
            <span class="text-[11px] uppercase font-bold text-gray-600 text-center">Auto</span>
        </div>
        <div class="bg-white py-4 rounded-lg shadow-sm border flex flex-col items-center justify-center gap-3">
            <div id="ind_manual" class="indicator-circle w-5 h-5 rounded-full bg-gray-300 ring-4 ring-gray-300/30"></div>
            <span class="text-[11px] uppercase font-bold text-gray-600 text-center">Manual</span>
        </div>
        <div class="bg-white py-4 rounded-lg shadow-sm border flex flex-col items-center justify-center gap-3">
            <div id="ind_warm_up" class="indicator-circle w-5 h-5 rounded-full bg-gray-300 ring-4 ring-gray-300/30"></div>
            <span class="text-[11px] uppercase font-bold text-gray-600 text-center">Warmup</span>
        </div>
        <div class="bg-white py-4 rounded-lg shadow-sm border flex flex-col items-center justify-center gap-3">
            <div id="ind_cool_down" class="indicator-circle w-5 h-5 rounded-full bg-gray-300 ring-4 ring-gray-300/30"></div>
            <span class="text-[11px] uppercase font-bold text-gray-600 text-center">Cooldown</span>
        </div>
        <div class="bg-white py-4 rounded-lg shadow-sm border flex flex-col items-center justify-center gap-3">
            <div id="ind_common_alarm" class="indicator-circle w-5 h-5 rounded-full bg-gray-300 ring-4 ring-gray-300/30"></div>
            <span class="text-[11px] uppercase font-bold text-gray-600 text-center">Common Alarm</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-2">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden flex flex-col">
            <div class="text-center pt-2 text-xs font-bold text-gray-500">RPM</div>
            <div id="gauge_rpm" class="w-full h-40"></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden flex flex-col">
            <div class="text-center pt-2 text-xs font-bold text-gray-500">Engine Speed</div>
            <div id="gauge_engine_speed" class="w-full h-40"></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden flex flex-col">
            <div class="text-center pt-2 text-xs font-bold text-gray-500">Flow</div>
            <div id="gauge_flow" class="w-full h-40"></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden flex flex-col">
            <div class="text-center pt-2 text-xs font-bold text-gray-500">Engine Temp</div>
            <div id="gauge_engine_temp" class="w-full h-40"></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden flex flex-col">
            <div class="text-center pt-2 text-xs font-bold text-gray-500">Coolant Temp</div>
            <div id="gauge_coolant_temp" class="w-full h-40"></div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden flex flex-col">
            <div class="text-center pt-2 text-xs font-bold text-gray-500">Fuel Level</div>
            <div id="gauge_fuel_level" class="w-full h-40"></div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow-md border-l-4 border-blue-600 relative overflow-hidden mt-2">
        <div id="controlLoader" class="hidden absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-sm transition-all duration-300">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mb-2"></div>
                <span class="text-sm font-bold text-blue-800 animate-pulse">Sending Command...</span>
            </div>
        </div>

        <h3 class="text-lg font-bold border-b pb-2 mb-3 text-gray-700 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Remote Control Panel
        </h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-center">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <button onclick="sendControl('start')" class="control-btn bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 text-sm rounded shadow transition transform active:scale-95 flex justify-center items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Start
                </button>
                <button onclick="sendControl('stop')" class="control-btn bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 text-sm rounded shadow transition transform active:scale-95 flex justify-center items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                    Stop
                </button>
                <button onclick="sendControl('auto')" class="control-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 text-sm rounded shadow transition transform active:scale-95 flex justify-center items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Auto
                </button>
                <button onclick="sendControl('reset')" class="control-btn bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-3 text-sm rounded shadow transition transform active:scale-95 flex justify-center items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Reset
                </button>
            </div>

            <div class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Set Target RPM</label>
                <div class="flex gap-2 md:flex-row flex-col">
                    <input type="number" id="rpmInput" placeholder="800 - 2000" min="800" max="2000" class="flex-grow border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button onclick="setRpm()" class="control-btn bg-blue-800 hover:bg-blue-900 text-white text-sm font-bold py-1 px-4 rounded shadow transition hover:shadow-lg whitespace-nowrap">Set</button>
                </div>
            </div>
        </div>
        <div id="controlMessage" class="hidden mt-3 p-2 rounded text-sm font-bold border-l-4"></div>
    </div>

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
                        <th>Timestamp (Local)</th>
                        <th>RPM</th>
                        <th>Load%</th>
                        <th>Fuel L/h</th>
                        <th>Coolant°C</th>
                        <th>Oil°C</th>
                        <th>Oil PSI</th>
                        <th>Discharge PSI</th>
                        <th>BatteryV</th>
                    </tr>
                </thead>
                <tbody class="divide-y"></tbody>
            </table>
        </div>
    </div>

    <div class="bg-white p-5 rounded-lg shadow-md border-t-4 border-blue-600 mt-2">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Location Map</h3>
        
        <div id="pumpMap" class="w-full h-80 rounded border z-[1]"></div>
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
    let currentHistoryData = []; 

    // Charts dictionary
    const charts = {};

    function getLocalTime(isoString) {
        if (!isoString) return 'N/A';
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

    function formatToUTC(date) {
        const pad = (n) => n.toString().padStart(2, '0');
        return `${date.getUTCFullYear()}-${pad(date.getUTCMonth() + 1)}-${pad(date.getUTCDate())} ${pad(date.getUTCHours())}:${pad(date.getUTCMinutes())}:${pad(date.getUTCSeconds())}`;
    }

    // --- 1. Gauge Initialization Helper ---
    // --- 1. Gauge Initialization Helper ---
    function initGauge(domId, min, max, unit) {
        var chartDom = document.getElementById(domId);
        var myChart = echarts.init(chartDom);
        var option = {
            series: [{
                type: 'gauge',
                radius: '90%',          // <-- ADDED: Makes the gauge significantly larger
                center: ['50%', '55%'], // <-- ADDED: Centers it perfectly under your title text
                min: min,
                max: max,
                splitNumber: 2, 
                progress: { show: false }, 
                axisLine: { 
                    lineStyle: { 
                        width: 10,
                        // <-- UPDATED: Light Blue (50%), Medium Blue (85%), Dark Blue (100%)
                        color: [
                            [0.5, '#93fdaa'], 
                            [0.85, '#f1ff72'], 
                            [1, '#ff7b7b']
                        ] 
                    } 
                },
                axisTick: { show: false },
                splitLine: { show: false }, 
                axisLabel: { 
                    distance: 12, 
                    color: '#6b7280', 
                    fontSize: 10,
                    formatter: function(value) {
                        if (value === min || value === max) {
                            return value;
                        }
                        return '';
                    }
                },
                pointer: { 
                    icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z', 
                    length: '15%', 
                    width: 8, 
                    offsetCenter: [0, '-60%'], 
                    itemStyle: { color: '#374151' } 
                },
                anchor: { show: true, showAbove: true, size: 8, itemStyle: { borderWidth: 2 } },
                title: { show: false },
                detail: { 
                    valueAnimation: true, 
                    fontSize: 15, 
                    fontWeight: 'bold',
                    color: '#1f2937',
                    offsetCenter: [0, '70%'], 
                    formatter: '{value} ' + unit 
                },
                data: [{ value: 0 }]
            }]
        };
        myChart.setOption(option);
        charts[domId] = myChart;
    }

    function updateGauge(domId, value) {
        if(charts[domId]) {
            charts[domId].setOption({
                series: [{ data: [{ value: value || 0 }] }]
            });
        }
    }

    // --- 2. Indicator Update Helper ---
    function updateIndicator(domId, status) {
        const el = $('#' + domId);
        // Clean out both color states before applying the new one
        el.removeClass('bg-gray-300 ring-gray-300/30 bg-green-500 ring-green-500/30');
        
        if(status === true || status === 1 || status === "1" || status === "true") {
            el.addClass('bg-green-500 ring-green-500/30');
        } else {
            el.addClass('bg-gray-300 ring-gray-300/30');
        }
    }

    // --- 3. Historical Logic ---
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
                    tbody.append(`
                        <tr>
                            <td class="px-4 py-2 font-mono whitespace-nowrap" data-order="${row.ts}">${getLocalTime(row.ts)}</td>
                            <td class="px-4 py-2">${row.rpm}</td>
                            <td class="px-4 py-2">${row.percent_load}</td>
                            <td class="px-4 py-2">${row.fuel_rate}</td>
                            <td class="px-4 py-2">${row.coolant_temp}</td>
                            <td class="px-4 py-2">${row.oil_temp}</td>
                            <td class="px-4 py-2">${row.oil_pressure}</td>
                            <td class="px-4 py-2 font-bold text-blue-700">${row.pump_press2}</td>
                            <td class="px-4 py-2">${row.battery_potential}</td>
                        </tr>
                    `);
                });
                
                historyDataTable = $('#historyTable').DataTable({ 
                    order: [[0, 'desc']], 
                    pageLength: 10,
                    responsive: true
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

        const exportRows = currentHistoryData.map(row => ({
            'Timestamp (Local)': getLocalTime(row.ts),
            'RPM': row.rpm,
            'Load (%)': row.percent_load,
            'Fuel Rate (L/h)': row.fuel_rate,
            'Coolant Temp (°C)': row.coolant_temp,
            'Oil Temp (°C)': row.oil_temp,
            'Oil Pressure (PSI)': row.oil_pressure,
            'Discharge Pressure (PSI)': row.pump_press2,
            'Battery (V)': row.battery_potential
        }));

        const ws = XLSX.utils.json_to_sheet(exportRows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Pump History");
        XLSX.writeFile(wb, `Pump_{{ $pump->id }}_Log_${new Date().getTime()}.xlsx`);
    }

    // --- 4. Control Logic ---
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
        const rpm = Number(rpmValue);
    
        if (rpmValue === "") {
            alert("Please enter an RPM value.");
            return;
        }
    
        if (rpm >= 800 && rpm <= 2000) {
            sendControl('rpm', rpm);
        } else {
            alert("Warning: RPM must be between 800 and 2000.");
        }
    }

    // --- 5. Initialize & Loop ---
    $(document).ready(function() {
        
        // Initialize Gauges with updated Max limits
        initGauge('gauge_rpm', 0, 4000, '');
        initGauge('gauge_engine_speed', 0, 4000, '');
        initGauge('gauge_flow', 0, 150, 'L/s');
        initGauge('gauge_engine_temp', 0, 150, '°C');
        initGauge('gauge_coolant_temp', 0, 150, '°C');
        initGauge('gauge_fuel_level', 0, 100, '%');

        // Handle window resize for charts
        window.addEventListener('resize', function() {
            Object.values(charts).forEach(chart => chart.resize());
        });

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
                    $('#dot_network').removeClass('bg-green-500 bg-red-500').addClass((data.connection || '').toLowerCase() === 'online' ? 'bg-green-500' : 'bg-red-500');
                    $('#dot_modbus').removeClass('bg-green-500 bg-red-500').addClass(data.modbus_status ? 'bg-green-500' : 'bg-red-500');
                    
                    // Update Row 1
                    $('#val_oil_pressure').text(data.oil_pressure ?? '0');
                    $('#val_battery').text(data.battery_potential ?? '0');
                    $('#val_engine_hours').text(data.engine_hours ?? '0');
                    $('#val_fuel_rate').text(data.fuel_rate ?? '0');

                    // Update Row 2 (Indicators)
                    updateIndicator('ind_engine_running', data.auto_manual_status?.engine_running);
                    updateIndicator('ind_auto', data.auto_manual_status?.auto);
                    updateIndicator('ind_manual', data.auto_manual_status?.manual);
                    updateIndicator('ind_warm_up', data.auto_manual_status?.warm_up);
                    updateIndicator('ind_cool_down', data.auto_manual_status?.cool_down);
                    updateIndicator('ind_common_alarm', data.auto_manual_status?.common_alarm);

                    // Update Row 3 (Gauges)
                    updateGauge('gauge_rpm', data.rpm);
                    updateGauge('gauge_engine_speed', data.engine_speed_mech);
                    updateGauge('gauge_flow', data.pressure_or_flow?.flow);
                    updateGauge('gauge_engine_temp', data.engine_temp_mech);
                    updateGauge('gauge_coolant_temp', data.coolant_temp);
                    updateGauge('gauge_fuel_level', data.fuel_level);
                }
            });
        }, 1000);

        const pumpLat = {{ $pump->latitude ?? 'null' }};
        const pumpLon = {{ $pump->longitude ?? 'null' }};
        const pumpStatus = "{{ $pump->status ?? 'offline' }}";

        const mapContainer = document.getElementById('pumpMap');

        if (pumpLat !== null && pumpLon !== null) {
            // 1. Initialize map centered on the pump
            const map = L.map('pumpMap').setView([pumpLat, pumpLon], 13);

            // 2. Add Light Theme Tiles
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // 3. Create Custom Colored Pin
            const pinColor = (pumpStatus === 'online') ? '#22c55e' : '#ef4444';
            const svgIcon = L.divIcon({
                html: `
                    <div class="flex items-center justify-center w-full h-full">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${pinColor}" class="w-8 h-8 drop-shadow-md stroke-white stroke-2">
                            <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742c1.002-.722 2.607-1.99 3.61-3.376C18.225 16.826 20 14.434 20 11a8 8 0 10-16 0c0 3.434 1.775 5.826 2.78 7.218 1.003 1.387 2.608 2.654 3.61 3.376a16.974 16.974 0 001.144.742zM12 13.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                `,
                className: 'custom-div-icon',
                iconSize: [32, 32],
                iconAnchor: [16, 32] 
            });

            // 4. Add Marker to Map
            L.marker([pumpLat, pumpLon], { icon: svgIcon })
                .addTo(map)
                .bindPopup(`<b>{{ $pump->name }}</b><br>Lat: ${pumpLat}<br>Lon: ${pumpLon}`);
                
        } else {
            // Graceful fallback if no coordinates exist
            mapContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center w-full h-full bg-gray-50 text-gray-400">
                    <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="font-bold">Location data not available</span>
                    <span class="text-xs mt-1">Install GPS module to enable mapping.</span>
                </div>
            `;
        }
    });
</script>
@endsection