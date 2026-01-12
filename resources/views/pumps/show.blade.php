@extends('layouts.app')

@section('title', 'Pump Detail: ' . $pump->name)

@section('styles')
<!-- DataTables & Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #2563eb !important; color: white !important; border: none; }
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem 0.5rem; }
</style>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Responsive Header (Same as before) -->
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
            </div>
            <div class="flex flex-row gap-3 md:flex-col items-center md:items-end justify-between md:justify-start">
                <div id="statusBadge" class="inline-flex items-center px-4 py-2 rounded-lg text-white font-bold shadow-sm {{ $pump->status == 'online' ? 'bg-green-600' : 'bg-red-600' }}">
                    <span class="animate-pulse mr-2 text-xl">•</span> <span id="statusText">{{ ucfirst($pump->status) }}</span>
                </div>
                <div id="lastUpdateText" class="text-xs text-gray-500 font-mono mt-2">Updated: Loading...</div>
            </div>
        </div>
    </div>

    <!-- Remote Control Panel (Same as before) -->
    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-600 relative overflow-hidden">
        <div id="controlLoader" class="hidden absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-sm transition-all duration-300">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mb-2"></div>
                <span class="text-sm font-bold text-blue-800">Sending...</span>
            </div>
        </div>
        <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Remote Control Panel</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <button onclick="sendControl('start')" class="control-btn bg-green-600 text-white font-bold py-3 rounded shadow">Start</button>
                <button onclick="sendControl('stop')" class="control-btn bg-red-600 text-white font-bold py-3 rounded shadow">Stop</button>
                <button onclick="sendControl('auto')" class="control-btn bg-blue-600 text-white font-bold py-3 rounded shadow">Auto</button>
                <button onclick="sendControl('reset')" class="control-btn bg-gray-600 text-white font-bold py-3 rounded shadow">Reset</button>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg border">
                <div class="flex gap-2">
                    <input type="number" id="rpmInput" placeholder="Target RPM" class="flex-grow border rounded px-3 py-2">
                    <button onclick="setRpm()" class="control-btn bg-blue-800 text-white font-bold py-2 px-6 rounded shadow">Set RPM</button>
                </div>
            </div>
        </div>
        <div id="controlMessage" class="hidden mt-4 p-3 rounded text-sm font-bold border-l-4"></div>
    </div>

    <!-- Real-time Data Grid (Same as before) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Engine Stats</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded text-center">
                    <span class="block text-xs text-blue-600 font-bold uppercase">RPM</span>
                    <span id="disp_rpm" class="text-2xl font-bold text-gray-800">--</span>
                </div>
                <div class="bg-blue-50 p-4 rounded text-center">
                    <span class="block text-xs text-blue-600 font-bold uppercase">Load</span>
                    <span class="text-2xl font-bold text-gray-800"><span id="disp_load">--</span>%</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Temperatures (°C)</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b"><span>Coolant</span> <span id="disp_coolant_temp" class="font-bold">--</span></div>
                <div class="flex justify-between border-b"><span>Oil</span> <span id="disp_oil_temp" class="font-bold">--</span></div>
                <div class="flex justify-between border-b"><span>Pump</span> <span id="disp_pump_temp" class="font-bold">--</span></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Pressures (PSI)</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b"><span>Oil</span> <span id="disp_oil_pressure" class="font-bold">--</span></div>
                <div class="flex justify-between border-b"><span>Suction</span> <span id="disp_suction_pressure" class="font-bold">--</span></div>
                <div class="flex justify-between border-b bg-blue-50 p-1"><span>Discharge</span> <span id="disp_discharge_pressure" class="font-bold">--</span></div>
            </div>
        </div>
    </div>

    <!-- NEW: Historical Data Section -->
    <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-gray-800">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-xl font-bold text-gray-800">Historical Logs</h3>
            
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <input type="text" id="dateRangePicker" class="border rounded-lg px-4 py-2 text-sm w-64 focus:ring-2 focus:ring-blue-500" placeholder="Select Date Range">
                </div>
                <button onclick="loadHistory()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                    Filter
                </button>
                <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export XLS
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="historyTable" class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-bold text-gray-600">Timestamp</th>
                        <th class="px-4 py-3 font-bold text-gray-600">RPM</th>
                        <th class="px-4 py-3 font-bold text-gray-600">Load %</th>
                        <th class="px-4 py-3 font-bold text-gray-600">Fuel L/h</th>
                        <th class="px-4 py-3 font-bold text-gray-600">Coolant °C</th>
                        <th class="px-4 py-3 font-bold text-gray-600">Oil °C</th>
                        <th class="px-4 py-3 font-bold text-gray-600">Oil PSI</th>
                        <th class="px-4 py-3 font-bold text-gray-600">Discharge PSI</th>
                        <th class="px-4 py-3 font-bold text-gray-600">Battery V</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <!-- Data loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Libraries -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    let historyDataTable;
    let flatpickrInstance;

    function getLocalTime(isoString) {
        if (!isoString) return 'N/A';
        const date = new Date(isoString);
        return date.toLocaleString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }

    // Load History Data
    function loadHistory() {
        const range = flatpickrInstance.selectedDates;
        let url = `/pumps/{{ $pump->id }}/history`;
        
        if (range.length === 2) {
            url += `?start=${range[0].toISOString()}&end=${range[1].toISOString()}`;
        }

        if (historyDataTable) {
            historyDataTable.destroy();
        }

        $('#historyTable tbody').html('<tr><td colspan="9" class="text-center py-10">Loading history...</td></tr>');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
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
                            <td class="px-4 py-2 font-bold text-blue-600">${row.pump_press2}</td>
                            <td class="px-4 py-2">${row.battery_potential}</td>
                        </tr>
                    `);
                });

                historyDataTable = $('#historyTable').DataTable({
                    order: [[0, 'desc']],
                    pageLength: 10,
                    language: { searchPlaceholder: "Search logs..." }
                });
            }
        });
    }

    // Export to Excel (Client Side)
    function exportToExcel() {
        const table = document.getElementById("historyTable");
        const wb = XLSX.utils.table_to_book(table, { sheet: "Pump History" });
        XLSX.writeFile(wb, `Pump_{{ $pump->id }}_History_${new Date().getTime()}.xlsx`);
    }

    $(document).ready(function() {
        // Initialize Date Range Picker
        flatpickrInstance = flatpickr("#dateRangePicker", {
            mode: "range",
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: [new Date(Date.now() - 24 * 60 * 60 * 1000), new Date()]
        });

        // Initial History Load
        loadHistory();

        // --- Real-time Auto Refresh (Keep from previous) ---
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
                }
            });
        }, 1000);
    });

    // Control logic (Keep from previous)
    function sendControl(action, value = null) {
        if (!confirm('Send command: ' + action + '?')) return;
        $('#controlLoader').removeClass('hidden');
        $.ajax({
            url: `/pumps/{{ $pump->id }}/control`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', action: action, value: value },
            success: (res) => $('#controlMessage').html('✅ ' + res.message).addClass('bg-green-100 text-green-800').removeClass('hidden'),
            error: (xhr) => $('#controlMessage').html('❌ ' + (xhr.responseJSON?.message || 'Error')).addClass('bg-red-100 text-red-800').removeClass('hidden'),
            complete: () => $('#controlLoader').addClass('hidden')
        });
    }
    function setRpm() { const val = $('#rpmInput').val(); if (val) sendControl('rpm', val); }
</script>
@endsection