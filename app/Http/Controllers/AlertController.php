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
        $query = HistoricalAlert::select([
            'historical_alerts.id', 
            'historical_alerts.ts', 
            'historical_alerts.pump_id',     // <--- ADD THIS LINE
            'historical_alerts.alert_type', 
            'historical_alerts.description', 
            'historical_alerts.email',
            'pumps.name as pump_name'
        ])->leftJoin('pumps', 'historical_alerts.pump_id', '=', 'pumps.id');

        $recordsTotal = HistoricalAlert::count();

        // --- FILTERING ---
        if ($request->has('pumps') && !empty($request->pumps)) {
            $query->whereIn('historical_alerts.pump_id', $request->pumps);
        }
        
        if ($request->has('alert_types') && !empty($request->alert_types)) {
            $query->whereIn('historical_alerts.alert_type', $request->alert_types);
        }

        // NEW: Date & Time Range Filtering
        if ($request->has('start_date') && !empty($request->start_date)) {
            // Changed from whereDate to where to respect exact hours/minutes
            $query->where('historical_alerts.ts', '>=', $request->start_date); 
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->where('historical_alerts.ts', '<=', $request->end_date);
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

        $recordsFiltered = $query->count();

        // --- SORTING ---
        $columns = ['historical_alerts.ts', 'pumps.name', 'historical_alerts.alert_type', 'historical_alerts.description', 'historical_alerts.email'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc'); 
        
        if (isset($columns[$orderColumnIndex])) {
            $query->orderBy($columns[$orderColumnIndex], $orderDirection);
        }

        // --- PAGINATION ---
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        
        $data = $query->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
}