@extends('layouts.app')

@section('title', 'Pump Detail: ' . $pump->name)

@section('content')
<div class="space-y-6">
    
    <!-- Responsive Header -->
    <div id="headerStatusContainer" class="bg-white p-6 rounded-lg shadow-md border-t-4 {{ $pump->status == 'online' ? 'border-green-500' : 'border-red-500' }} transition-colors duration-300">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            
            <!-- Left Side: Title & Info -->
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

            <!-- Right Side: Status (Full width on mobile, right on desktop) -->
            <div class="flex flex-row gap-3 md:flex-col items-center md:items-end justify-between md:justify-start bg-gray-50 md:bg-transparent p-3 md:p-0 rounded-lg">
                <div id="statusBadge" class="inline-flex items-center px-4 py-2 rounded-lg text-white font-bold shadow-sm {{ $pump->status == 'online' ? 'bg-green-600' : 'bg-red-600' }} transition-colors duration-300">
                    <span class="animate-pulse mr-2 text-xl">•</span> 
                    <span id="statusText">{{ ucfirst($pump->status) }}</span>
                </div>
                <div id="lastUpdateText" class="text-xs text-gray-500 font-mono mt-0 md:mt-2">
                    <!-- Initial server time, will be replaced by JS for local timezone -->
                    Updated: {{ $pump->last_update->toIso8601String() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Remote Control Panel -->
    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-600 relative overflow-hidden">
        <!-- Loading Overlay -->
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
            <!-- Basic Controls -->
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

            <!-- RPM Control -->
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Set Target RPM</label>
                <div class="flex gap-2 md:flex-row flex-col">
                    <input type="number" id="rpmInput" placeholder="e.g. 1500" class="flex-grow border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button onclick="setRpm()" class="control-btn bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 px-6 rounded shadow transition hover:shadow-lg whitespace-nowrap">
                        Set RPM
                    </button>
                </div>
            </div>
        </div>
        <div id="controlMessage" class="hidden mt-4 p-3 rounded text-sm font-bold border-l-4"></div>
    </div>

    <!-- 3-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Column 1: Engine & Fuel -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Engine Performance
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded text-center">
                        <span class="block text-xs text-blue-600 font-bold uppercase tracking-wider">RPM</span>
                        <span id="disp_rpm" class="text-2xl font-bold text-gray-800">{{ $pump->rpm }}</span>
                    </div>
                    <div class="bg-blue-50 p-4 rounded text-center">
                        <span class="block text-xs text-blue-600 font-bold uppercase tracking-wider">Load</span>
                        <span class="text-2xl font-bold text-gray-800"><span id="disp_load">{{ $pump->percent_load }}</span>%</span>
                    </div>
                    <div class="col-span-2 flex justify-between border-b pb-2">
                        <span class="text-sm text-gray-500">Engine Hours</span>
                        <span class="font-medium font-mono"><span id="disp_engine_hours">{{ $pump->engine_hours }}</span> h</span>
                    </div>
                    <div class="col-span-2 flex justify-between border-b pb-2">
                        <span class="text-sm text-gray-500">Fuel Rate</span>
                        <span class="font-medium font-mono"><span id="disp_fuel_rate">{{ $pump->fuel_rate }}</span> L/h</span>
                    </div>
                    <div class="col-span-2 mt-2">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Fuel Level</span>
                            <span class="font-bold"><span id="disp_fuel_level_text">{{ $pump->fuel_level }}</span>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div id="disp_fuel_level_bar" class="bg-yellow-500 h-3 rounded-full transition-all duration-1000" style="width: {{ min($pump->fuel_level, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Digital Inputs</h3>
                <div id="digitalInputsContainer" class="grid grid-cols-2 gap-2 text-sm">
                    @if($pump->digital_inputs)
                        @foreach($pump->digital_inputs as $key => $val)
                            @php 
                                $isActive = is_array($val) ? $val['active'] : $val; 
                            @endphp
                            <div class="flex justify-between items-center p-2 rounded {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-gray-50 text-gray-400' }}">
                                <span class="capitalize text-xs font-semibold">{{ str_replace('_', ' ', $key) }}</span>
                                <span class="font-bold text-xs">{{ $isActive ? 'ON' : 'OFF' }}</span>
                            </div>
                        @endforeach
                    @else
                        <span class="text-gray-400 italic">No input data available</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Column 2: Temps & Pressure -->
        <div class="bg-white p-6 rounded-lg shadow-md h-fit">
            <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                Sensors
            </h3>
            <div class="space-y-6">
                <!-- Temps -->
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 tracking-widest">Temperatures (°C)</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span class="text-sm text-gray-600">Coolant</span> <span class="font-mono font-bold" id="disp_coolant_temp">{{ $pump->coolant_temp }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span class="text-sm text-gray-600">Oil</span> <span class="font-mono font-bold" id="disp_oil_temp">{{ $pump->oil_temp }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span class="text-sm text-gray-600">Intake</span> <span class="font-mono font-bold" id="disp_intake_temp">{{ $pump->intake_temp }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span class="text-sm text-gray-600">Pump Body</span> <span class="font-mono font-bold" id="disp_pump_temp">{{ $pump->pump_temp }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span class="text-sm text-gray-600">Gearbox</span> <span class="font-mono font-bold" id="disp_gearbox_temp">{{ $pump->gearbox_temp }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pressures -->
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 tracking-widest pt-4">Pressures (PSI)</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span class="text-sm text-gray-600">Oil Pressure</span> <span class="font-mono font-bold" id="disp_oil_pressure">{{ $pump->oil_pressure }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span class="text-sm text-gray-600">Boost</span> <span class="font-mono font-bold" id="disp_boost_pressure">{{ $pump->boost_pressure }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span class="text-sm text-gray-600">Suction</span> <span class="font-mono font-bold" id="disp_suction_pressure">{{ $pump->suction_pressure }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1 bg-blue-50 px-2 rounded">
                            <span class="text-sm text-blue-800 font-bold">Discharge</span> <span class="font-mono font-bold text-blue-800" id="disp_discharge_pressure">{{ $pump->pump_press2 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 3: Status & Electrical -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Controller Mode</h3>
                <div id="controllerModeContainer" class="space-y-2">
                    @if($pump->auto_manual_status)
                        @foreach($pump->auto_manual_status as $key => $isActive)
                            <div class="flex items-center justify-between p-1">
                                <span class="text-sm capitalize {{ $isActive ? 'text-gray-800 font-bold' : 'text-gray-400' }}">
                                    {{ str_replace('_', ' ', $key) }}
                                </span>
                                <div class="w-3 h-3 rounded-full {{ $isActive ? 'bg-green-500 shadow-sm' : 'bg-gray-200' }}"></div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold border-b pb-3 mb-4 text-gray-700">Electrical</h3>
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

            <div class="bg-white p-6 rounded-lg shadow-md text-xs text-gray-500">
                <div class="flex justify-between py-1 border-b border-gray-100"><span>Firmware:</span> <span class="font-mono" id="disp_firmware">{{ $pump->firmware_version }}</span></div>
                <div class="flex justify-between py-1 border-b border-gray-100"><span>Config #:</span> <span class="font-mono" id="disp_config">{{ $pump->config_number }}</span></div>
                <div class="flex justify-between py-1 border-b border-gray-100"><span>Heartbeat:</span> <span class="font-mono" id="disp_heartbeat">{{ $pump->heartbeat }}</span></div>
                <div class="flex justify-between py-1 pt-2"><span>Asset Tag:</span> <span class="font-mono" id="disp_asset_tag">{{ $pump->asset_tag }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Helper function to format ISO date string to Local Time
    function getLocalTime(isoString) {
        if (!isoString) return 'N/A';
        const date = new Date(isoString);
        return date.toLocaleString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    // 1. Control Panel Logic
    function sendControl(action, value = null) {
        if (!confirm('Are you sure you want to send command: ' + action + (value ? ' ' + value : '') + '?')) return;

        const pumpId = "{{ $pump->id }}";
        const url = `/pumps/${pumpId}/control`;
        const messageBox = $('#controlMessage');
        const loader = $('#controlLoader');
        const buttons = $('.control-btn');

        // Reset UI before sending
        messageBox.addClass('hidden').removeClass('bg-green-100 text-green-800 border-green-500 bg-red-100 text-red-800 border-red-500');
        
        // Show Loader & Disable Buttons
        loader.removeClass('hidden');
        buttons.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: action,
                value: value
            },
            success: function(response) {
                messageBox.html('✅ ' + response.message)
                          .addClass('bg-green-100 text-green-800 border-green-500')
                          .removeClass('hidden');
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error';
                messageBox.html('❌ Error: ' + msg)
                          .addClass('bg-red-100 text-red-800 border-red-500')
                          .removeClass('hidden');
            },
            complete: function() {
                // Hide Loader & Enable Buttons
                loader.addClass('hidden');
                buttons.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
            }
        });
    }

    function setRpm() {
        const rpm = $('#rpmInput').val();
        if (!rpm) {
            alert('Please enter a valid RPM value');
            return;
        }
        sendControl('rpm', rpm);
    }

    // 2. Auto Refresh Logic (Every 1000ms)
    $(document).ready(function() {
        const pumpId = "{{ $pump->id }}";

        // Initial Formatting of Server Time
        const initialTime = "{{ $pump->last_update->toIso8601String() }}";
        $('#lastUpdateText').text('Updated: ' + getLocalTime(initialTime));
        
        setInterval(function() {
            $.ajax({
                url: `/pumps/${pumpId}/data`,
                method: 'GET',
                success: function(data) {
                    // Update Header Status
                    const container = $('#headerStatusContainer');
                    const badge = $('#statusBadge');
                    const text = $('#statusText');
                    
                    if (data.status === 'online') {
                        container.removeClass('border-red-500').addClass('border-green-500');
                        badge.removeClass('bg-red-600').addClass('bg-green-600');
                        text.text('Online');
                    } else {
                        container.removeClass('border-green-500').addClass('border-red-500');
                        badge.removeClass('bg-green-600').addClass('bg-red-600');
                        text.text('Offline');
                    }
                    
                    // Update Timestamp with Local Time Formatting
                    $('#lastUpdateText').text('Updated: ' + getLocalTime(data.last_update));

                    // Update Engine Stats
                    $('#disp_rpm').text(data.rpm);
                    $('#disp_load').text(data.percent_load);
                    $('#disp_engine_hours').text(data.engine_hours);
                    $('#disp_fuel_rate').text(data.fuel_rate);
                    $('#disp_fuel_level_text').text(data.fuel_level);
                    $('#disp_fuel_level_bar').css('width', Math.min(data.fuel_level, 100) + '%');

                    // Update Temps
                    $('#disp_coolant_temp').text(data.coolant_temp);
                    $('#disp_oil_temp').text(data.oil_temp);
                    $('#disp_intake_temp').text(data.intake_temp);
                    $('#disp_pump_temp').text(data.pump_temp);
                    $('#disp_gearbox_temp').text(data.gearbox_temp);

                    // Update Pressures
                    $('#disp_oil_pressure').text(data.oil_pressure);
                    $('#disp_boost_pressure').text(data.boost_pressure);
                    $('#disp_suction_pressure').text(data.suction_pressure);
                    $('#disp_discharge_pressure').text(data.pump_press2);

                    // Update Electrical
                    $('#disp_battery').text(data.battery_potential);
                    $('#disp_system').text(data.electrical_potential);

                    // Update Meta
                    $('#disp_firmware').text(data.firmware_version);
                    $('#disp_config').text(data.config_number);
                    $('#disp_heartbeat').text(data.heartbeat);
                    $('#disp_asset_tag').text(data.asset_tag);

                    // Update Dynamic Lists
                    renderDigitalInputs(data.digital_inputs);
                    renderControllerMode(data.auto_manual_status);
                }
            });
        }, 3000);
    });

    function renderDigitalInputs(inputs) {
        const container = $('#digitalInputsContainer');
        container.empty();
        
        if (!inputs || Object.keys(inputs).length === 0) {
            container.append('<span class="text-gray-400 italic">No input data available</span>');
            return;
        }

        $.each(inputs, function(key, val) {
            let isActive = false;
            // Handle both boolean and object {active: true} formats
            if (typeof val === 'object' && val !== null) {
                isActive = val.active;
            } else {
                isActive = !!val;
            }
            
            const keyName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            const bgClass = isActive ? 'bg-green-100 text-green-800' : 'bg-gray-50 text-gray-400';
            const statusText = isActive ? 'ON' : 'OFF';

            const html = `
                <div class="flex justify-between items-center p-2 rounded ${bgClass}">
                    <span class="capitalize text-xs font-semibold">${keyName}</span>
                    <span class="font-bold text-xs">${statusText}</span>
                </div>
            `;
            container.append(html);
        });
    }

    function renderControllerMode(modes) {
        const container = $('#controllerModeContainer');
        container.empty();

        if (!modes) return;

        $.each(modes, function(key, isActive) {
            const keyName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            const textClass = isActive ? 'text-gray-800 font-bold' : 'text-gray-400';
            const dotClass = isActive ? 'bg-green-500 shadow-sm' : 'bg-gray-200';

            const html = `
                <div class="flex items-center justify-between p-1">
                    <span class="text-sm capitalize ${textClass}">${keyName}</span>
                    <div class="w-3 h-3 rounded-full ${dotClass}"></div>
                </div>
            `;
            container.append(html);
        });
    }
</script>
@endsection