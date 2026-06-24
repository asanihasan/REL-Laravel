@extends('layouts.app')

@section('title', 'System Alerts')

{{-- Inject DataTables & Select2 CSS specifically for this page --}}
@section('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* The Selected Items (Pills) */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #eff6ff !important; 
            border: 1px solid #bfdbfe !important; 
            color: #1e3a8a !important; 
            border-radius: 0.25rem !important;
            
            /* Add thick padding to the left (28px) so the text is pushed away from the X */
            padding: 4px 8px 4px 28px !important; 
            margin-top: 5px !important;
            font-size: 0.875rem !important; 
            
            /* Required for absolute positioning of the X */
            position: relative !important; 
        }

        /* The "x" remove button on pills */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #1e40af !important; 
            
            /* Pin to the left and take up the exact height of the pill */
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            height: 100% !important;
            
            /* Use flex to perfectly center the text vertically and horizontally */
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            
            border: none !important;
            margin: 0 !important;
            padding: 0 8px !important; /* Defines the clickable area width */
            font-weight: bold !important;
            
            /* Override Tailwind's default line-height */
            line-height: 1 !important;
            transform: none !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background-color: transparent !important;
            color: #ef4444 !important; 
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
                        <option value="{{ $pump->id }}">
                            {{ $pump->name ?: 'PUMP_' . $pump->id }}
                        </option>
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
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Filter by Time Range</label>
                <input type="text" id="dateRangePicker" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border bg-white" placeholder="Select date range...">
            </div>
            
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="overflow-x-auto">
            <table id="alertsTable" class="display w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="p-3">Timestamp</th>
                        <th class="p-3">Pump Name</th>
                        <th class="p-3">Alert Type</th>
                        <th class="p-3">Description</th>
                        <th class="p-3 text-center">Emailed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Wait for the entire window to load, bypassing any Laravel Vite 'defer' race conditions
        window.addEventListener('load', function() {
            
            // 1. Initialize Select2
            $('.select2').select2({
                width: 'resolve' // Tells Select2 to inherit the 100% width from the style attribute
            });

            // 2. Initialize DataTables
            // 1. Initialize Flatpickr
            let flatpickrInstance = flatpickr("#dateRangePicker", {
                mode: "range", 
                enableTime: true, 
                dateFormat: "Y-m-d H:i",
                time_24hr: true, // Optional: formats time as 24-hour (e.g. 14:30)
                defaultDate: [new Date(Date.now() - 24 * 60 * 60 * 1000), new Date()],
                onChange: function(selectedDates, dateStr, instance) {
                    // Only trigger the table reload if TWO dates are selected (start and end)
                    if (selectedDates.length === 2) {
                        table.draw();
                    }
                }
            });

            // ... (Your Select2 Initialization) ...

            // 2. Initialize DataTables
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
                        d.pumps = $('#pumpFilter').val();
                        d.alert_types = $('#typeFilter').val();
                        
                        if (flatpickrInstance.selectedDates.length > 0) {
                            d.start_date = flatpickrInstance.formatDate(flatpickrInstance.selectedDates[0], "Y-m-d H:i:00");
                            if (flatpickrInstance.selectedDates.length === 2) {
                                d.end_date = flatpickrInstance.formatDate(flatpickrInstance.selectedDates[1], "Y-m-d H:i:59");
                            }
                        }
                    }
                },
                // NEW: Inject the hover classes into the <tr> tags generated by AJAX
                createdRow: function(row, data, dataIndex) {
                    $(row).addClass('hover:bg-gray-50 transition');
                },
                columns: [
                    { 
                        data: 'ts', 
                        name: 'ts',
                        className: 'p-3', // Added padding to match template
                        render: function(data) {
                            return `<span class="font-mono text-gray-800">${data}</span>`;
                        }
                    },
                    { 
                        data: 'pump_name', 
                        name: 'pump_name',
                        className: 'p-3 font-medium text-gray-900',
                        render: function(data, type, row) {
                            return data ? data : `PUMP_${row.pump_id}`;
                        }
                    },
                    { 
                        data: 'alert_type', 
                        name: 'alert_type',
                        className: 'p-3',
                        render: function(data) {
                            let formatted = data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            
                            let badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                            if(data.includes('stopped') || data.includes('lost') || data.includes('low')) badgeClass = 'bg-red-50 text-red-700 border-red-200';
                            if(data.includes('running')) badgeClass = 'bg-green-50 text-green-700 border-green-200';
                            
                            return `<span class="px-2.5 py-1 text-xs font-medium rounded-md border ${badgeClass}">${formatted}</span>`;
                        }
                    },
                    { 
                        data: 'description', 
                        name: 'description',
                        className: 'p-3 text-gray-500 whitespace-normal min-w-[300px]'
                    },
                    { 
                        data: 'email', 
                        name: 'email',
                        className: 'p-3 text-center',
                        render: function(data) {
                            return data == 1 
                                ? `<span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-600 rounded-full">SENT</span>` 
                                : `<span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-500 bg-gray-100 rounded-full">NO</span>`;
                        }
                    }
                ]
            });

            // 3. Trigger Table Reload for Select2 (Removed date inputs from this trigger!)
            $('#pumpFilter, #typeFilter').on('change', function() {
                table.draw();
            });
        });
    </script>
@endsection