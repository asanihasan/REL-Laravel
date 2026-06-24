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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Pump</label>
                <select id="pumpFilter" class="w-full select2" multiple="multiple" data-placeholder="Select Pumps...">
                    @foreach($pumps as $pump)
                        <option value="{{ $pump->id }}">{{ $pump->name }} ({{ $pump->id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Alert Type</label>
                <select id="typeFilter" class="w-full select2" multiple="multiple" data-placeholder="Select Alert Types...">
                    @foreach($alertTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 overflow-hidden">
        <table id="alertsTable" class="w-full text-sm text-left text-gray-500 stripe hover" style="width:100%">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3">Timestamp</th>
                    <th class="px-4 py-3">Pump Name</th>
                    <th class="px-4 py-3">Alert Type</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3 text-center">Emailed</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    {{-- Inject jQuery, DataTables, and Select2 --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Initialize Select2
            $('.select2').select2({
                width: '100%'
            });

            // 2. Initialize DataTables with Server-Side Processing
            let table = $('#alertsTable').DataTable({
                processing: true,
                serverSide: true,
                order: [[0, 'desc']], // Default sort: Timestamp, newest first
                pageLength: 25,       // Default rows per page
                ajax: {
                    url: '{{ route('manage.alert.data') }}',
                    type: 'GET',
                    data: function(d) {
                        // Pass current filter values to the backend request
                        d.pumps = $('#pumpFilter').val();
                        d.alert_types = $('#typeFilter').val();
                    }
                },
                columns: [
                    { 
                        data: 'ts', 
                        name: 'ts',
                        render: function(data) {
                            // Basic formatting, you can use moment.js here if you want
                            return `<span class="font-mono text-gray-600">${data}</span>`;
                        }
                    },
                    { 
                        data: 'pump_name', 
                        name: 'pump_name',
                        render: function(data) {
                            return `<span class="font-medium text-gray-900">${data || 'Unknown Pump'}</span>`;
                        }
                    },
                    { 
                        data: 'alert_type', 
                        name: 'alert_type',
                        render: function(data) {
                            // Beautify the alert type (e.g., 'engine_running' -> 'Engine Running')
                            let formatted = data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            
                            // Optional: Color coding based on type
                            let badgeClass = 'bg-gray-100 text-gray-800';
                            if(data.includes('stopped') || data.includes('lost') || data.includes('low')) badgeClass = 'bg-red-100 text-red-800';
                            if(data.includes('running')) badgeClass = 'bg-green-100 text-green-800';
                            
                            return `<span class="px-2 py-1 text-xs font-medium rounded-md ${badgeClass}">${formatted}</span>`;
                        }
                    },
                    { 
                        data: 'description', 
                        name: 'description' 
                    },
                    { 
                        data: 'email', 
                        name: 'email',
                        className: 'text-center',
                        render: function(data) {
                            return data == 1 
                                ? `<span class="inline-block w-2 h-2 rounded-full bg-green-500" title="Sent"></span>` 
                                : `<span class="inline-block w-2 h-2 rounded-full bg-gray-300" title="Not Sent"></span>`;
                        }
                    }
                ]
            });

            // 3. Trigger Table Reload when Filters Change
            $('#pumpFilter, #typeFilter').on('change', function() {
                table.draw();
            });
        });
    </script>
@endsection