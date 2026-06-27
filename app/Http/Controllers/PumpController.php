<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Import HTTP Client
use Carbon\Carbon;

class PumpController extends Controller
{
    public function index()
    {
        $pumps = Pump::all();
        return view('pumps.index', compact('pumps'));
    }

    public function show($id)
    {
        $pump = Pump::withLocation()->findOrFail($id);
        
        // Fetch history
        $history = DB::table('historical_pumps')
                    ->where('pump_id', $id)
                    ->orderBy('ts', 'desc')
                    ->limit(50)
                    ->get();

        return view('pumps.show', compact('pump', 'history'));
    }

    public function monitor($id)
    {
        $pump = Pump::withLocation()->findOrFail($id);
        
        $history = DB::table('historical_pumps')
                    ->where('pump_id', $id)
                    ->orderBy('ts', 'desc')
                    ->limit(50)
                    ->get();

        // Notice this points to a new blade file!
        return view('pumps.monitor', compact('pump', 'history')); 
    }

    public function update(Request $request, $id)
    {
        $pump = Pump::findOrFail($id);

        $pump->update($request->only(['name', 'location']));
        
        return redirect()->back()->with('success', 'Pump updated successfully');
    }

    public function destroy($id)
    {
        Pump::destroy($id);
        return redirect()->route('pumps.index')->with('success', 'Pump deleted');
    }

    // Handle Remote Control Requests
    public function control(Request $request, $id)
    {
        $action = $request->input('action');
        $value = $request->input('value');
        
        // Get endpoint from .env
        $endpoint = env('CONTROL_ENDPOINT'); 
        
        if (!$endpoint) {
            return response()->json(['message' => 'Control endpoint not configured.'], 500);
        }

        // Construct External URL
        if ($action === 'rpm') {
            // Format: {{endpoint}}/rpm/{{pump_id}}/{{rpm value}}
            $url = "{$endpoint}/rpm/{$id}/{$value}";
        } else {
            // Format: {{endpoint}}/start/{{pump_id}}
            $url = "{$endpoint}/{$action}/{$id}";
        }

        try {
            // Explicitly use GET method for the hardware request
            $response = Http::get($url);
            
            if ($response->successful()) {
                return response()->json(['message' => "Command '{$action}' sent successfully."]);
            } else {
                return response()->json(['message' => 'Device error: ' . $response->status()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to connect to device.'], 500);
        }
    }

    public function data($id)
    {
        // 1. Fetch the single pump, explicitly asking for the coordinates via Left Join
        $pump = Pump::withLocation()->findOrFail($id);

        $pump->append('status');

        return response()->json($pump);
    }

    public function history(Request $request, $id)
    {
        // Default to last 24 hours if no date range is provided
        $start = $request->query('start') 
            ? Carbon::parse($request->query('start')) 
            : Carbon::now()->subDay();
            
        $end = $request->query('end') 
            ? Carbon::parse($request->query('end')) 
            : Carbon::now();

        $history = DB::table('historical_pumps')
            ->where('pump_id', $id)
            ->whereBetween('ts', [$start, $end])
            ->orderBy('ts', 'desc')
            ->get();

        return response()->json($history);
    }

    public function maps()
    {
        // 1. Fetch ALL pumps with their locations attached
        $pumpsWithLocations = Pump::withLocation()->get();

        // 2. Force Laravel to append the custom 'status' attribute to every pump in the collection
        $pumpsWithLocations->each->append('status');

        return view('pumps.maps', compact('pumpsWithLocations'));
    }
}
