<?php

namespace App\Http\Controllers;

use App\Models\HistoricalAlert;
use App\Models\Pump;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    // 1. Render the initial page with the filter data
    public function index()
    {
        $pumps = Pump::select('id', 'name')->get();
        
        $alertTypes = [
            'engine_running' => 'Engine Running',
            'engine_stopped' => 'Engine Stopped',
            'high_rpm' => 'High RPM',
            'low_rpm' => 'Low RPM',
            'low_fuel_level' => 'Low Fuel Level',
            'location_change' => 'Location Change',
            'modbus_comm_lost' => 'Modbus Comm Lost',
        ];

        return view('manage.alert', compact('pumps', 'alertTypes'));
    }

    // 2. Handle Server-Side DataTables Processing
    public function data(Request $request)
    {
        // Start the base query joining the pumps table
        $query = HistoricalAlert::select([
            'historical_alerts.id', 
            'historical_alerts.ts', 
            'historical_alerts.alert_type', 
            'historical_alerts.description', 
            'historical_alerts.email',
            'pumps.name as pump_name'
        ])->leftJoin('pumps', 'historical_alerts.pump_id', '=', 'pumps.id');

        // Total records before any filtering
        $recordsTotal = HistoricalAlert::count();

        // --- FILTERING ---
        if ($request->has('pumps') && !empty($request->pumps)) {
            $query->whereIn('historical_alerts.pump_id', $request->pumps);
        }
        
        if ($request->has('alert_types') && !empty($request->alert_types)) {
            $query->whereIn('historical_alerts.alert_type', $request->alert_types);
        }

        // --- SEARCHING ---
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('pumps.name', 'like', "%{$searchValue}%")
                  ->orWhere('historical_alerts.description', 'like', "%{$searchValue}%")
                  ->orWhere('historical_alerts.alert_type', 'like', "%{$searchValue}%");
            });
        }

        // Total records after filtering
        $recordsFiltered = $query->count();

        // --- SORTING ---
        // Map frontend column indexes to actual database columns
        $columns = ['historical_alerts.ts', 'pumps.name', 'historical_alerts.alert_type', 'historical_alerts.description', 'historical_alerts.email'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc'); // Default to newest first
        
        if (isset($columns[$orderColumnIndex])) {
            $query->orderBy($columns[$orderColumnIndex], $orderDirection);
        }

        // --- PAGINATION ---
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        
        // Fetch the finalized chunk of data
        $data = $query->offset($start)->limit($length)->get();

        // Format the data exactly as DataTables expects
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
}