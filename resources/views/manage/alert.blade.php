@extends('layouts.app')

@section('title', 'System Alerts')

{{-- Inject DataTables & Select2 CSS specifically for this page --}}
@section('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Make Select2 blend with Tailwind */
        .select2-container .select2-selection--multiple {
            min-height: 42px;
            border-color: #e5e7eb;
            border-radius: 0.5rem;
            padding: 2px 4px;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #3b82f6;
            outline: 0;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            border-radius: 0.375rem;
            padding: 2px 8px;
            margin-top: 6px;
        }
    </style>
@endsection

@section('content')
    <div class="mt-6 mb-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">System Alerts</h2>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div class="md:col-span-1">
                <label class="block text-sm font-bold text-gray-700 mb-2">Filter by Pump</label>
                <select id="pumpFilter" class="select2" multiple="multiple" style="width: 100%;" data-placeholder="Select Pumps...">
                    @foreach($pumps as $pump)
                        <option value="{{ $pump->id }}">{{ $pump->name }} ({{ $pump->id }})</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-bold text-gray-700 mb-2">Filter by Alert Type</label>
                <select id="typeFilter" class="select2" multiple="multiple" style="width: 100%;" data-placeholder="Select Alert Types...">
                    @foreach($alertTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-bold text-gray-700 mb-2">Start Date</label>
                <input type="date" id="startDate" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-bold text-gray-700 mb-2">End Date</label>
                <input type="date" id="endDate" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
            </div>
            
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="alertsTable" class="w-full text-sm text-left whitespace-nowrap stripe hover">
                <thead class="text-xs text-white uppercase bg-gray-800">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Timestamp</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Pump Name</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Alert Type</th>
                        <th class="px-6 py-4 font-semibold tracking-wider w-full">Description</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Emailed</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y divide-gray-200">
                    </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Wait for the entire window to load, bypassing any Laravel Vite 'defer' race conditions
        window.addEventListener('load', function() {
            
            // 1. Initialize Select2
            $('.select2').select2({
                width: 'resolve' // Tells Select2 to inherit the 100% width from the style attribute
            });

            // 2. Initialize DataTables
            let table = $('#alertsTable').DataTable({
                processing: true,
                serverSide: true,
                order: [[0, 'desc']], 
                pageLength: 25,
                ajax: {
                    url: '{{ route('manage.alert.data') }}',
                    type: 'GET',
                    data: function(d) {
                        // Pass current filter values
                        d.pumps = $('#pumpFilter').val();
                        d.alert_types = $('#typeFilter').val();
                        // NEW: Pass date filters
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                    }
                },
                columns: [
                    { 
                        data: 'ts', 
                        name: 'ts',
                        render: function(data) {
                            return `<span class="font-mono text-gray-800">${data}</span>`;
                        }
                    },
                    { 
                        data: 'pump_name', 
                        name: 'pump_name',
                        render: function(data) {
                            return `<span class="font-bold text-gray-900">${data || 'Unknown Pump'}</span>`;
                        }
                    },
                    { 
                        data: 'alert_type', 
                        name: 'alert_type',
                        render: function(data) {
                            let formatted = data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            
                            let badgeClass = 'bg-gray-100 text-gray-800 border-gray-200';
                            if(data.includes('stopped') || data.includes('lost') || data.includes('low')) badgeClass = 'bg-red-50 text-red-700 border-red-200';
                            if(data.includes('running')) badgeClass = 'bg-green-50 text-green-700 border-green-200';
                            
                            return `<span class="px-2.5 py-1 text-xs font-semibold rounded-md border ${badgeClass}">${formatted}</span>`;
                        }
                    },
                    { 
                        data: 'description', 
                        name: 'description',
                        className: 'whitespace-normal min-w-[300px]' // Ensures long descriptions wrap nicely
                    },
                    { 
                        data: 'email', 
                        name: 'email',
                        className: 'text-center',
                        render: function(data) {
                            return data == 1 
                                ? `<span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-600 rounded-full">SENT</span>` 
                                : `<span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-500 bg-gray-100 rounded-full">NO</span>`;
                        }
                    }
                ]
            });

            // 3. Trigger Table Reload when ANY filter changes
            $('#pumpFilter, #typeFilter, #startDate, #endDate').on('change', function() {
                table.draw();
            });
        });
    </script>
@endsection