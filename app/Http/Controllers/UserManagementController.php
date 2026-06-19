<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    // ==========================================
    // INDEX
    // ==========================================
    public function index()
    {
        // Eager load the group so we don't query the database for every single user row
        $users = User::with('userGroup')->get();
        $userGroups = UserGroup::all();
        
        return view('manage.user', compact('users', 'userGroups'));
    }

    // ==========================================
    // USER METHODS
    // ==========================================
    public function storeUser(Request $request) 
    {
        $request->validate([
            'username'      => 'required|string|max:255|unique:users,username',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255', // Allowed to be empty
            'email'         => 'required|email|max:255|unique:users,email',
            'user_group_id' => 'required|exists:user_groups,id',
            'password'      => 'required|string|min:6',
        ]);

        User::create([
            'username'         => $request->username,
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'email'            => $request->email,
            'user_group_id'    => $request->user_group_id,
            'password'         => Hash::make($request->password), 
            
            // Checkbox logic: returns true if checked, false if not sent
            'engine_running'   => $request->has('engine_running'),
            'engine_stopped'   => $request->has('engine_stopped'),
            'high_rpm'         => $request->has('high_rpm'),
            'low_rpm'          => $request->has('low_rpm'),
            'low_fuel_level'   => $request->has('low_fuel_level'),
            'location_change'  => $request->has('location_change'),
            'modbus_comm_lost' => $request->has('modbus_comm_lost'),
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'username'   => 'required|string|max:255|unique:users,username,' . $id,
            'first_name' => 'required|string|max:255',
            'last_name'  => 'nullable|string|max:255', // Allowed to be empty
            'email'      => 'required|email|max:255|unique:users,email,' . $id,
            'password'   => 'nullable|string|min:6',
        ];

        if ($id != 1) {
            $rules['user_group_id'] = 'required|exists:user_groups,id';
        }

        $request->validate($rules);

        $data = [
            'username'         => $request->username,
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'email'            => $request->email,
            
            // Checkbox logic updates
            'engine_running'   => $request->has('engine_running'),
            'engine_stopped'   => $request->has('engine_stopped'),
            'high_rpm'         => $request->has('high_rpm'),
            'low_rpm'          => $request->has('low_rpm'),
            'low_fuel_level'   => $request->has('low_fuel_level'),
            'location_change'  => $request->has('location_change'),
            'modbus_comm_lost' => $request->has('modbus_comm_lost'),
        ];

        if ($id != 1) {
            $data['user_group_id'] = $request->user_group_id;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        if ($id == 1) {
            return redirect()->back()->with('error', 'The primary administrator account cannot be deleted.');
        }

        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    // ==========================================
    // USER GROUP METHODS
    // ==========================================
    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        UserGroup::create([
            'name'         => $request->name,
            'view'         => $request->has('view'),
            'control'      => $request->has('control'),
            'historical'   => $request->has('historical'),
            'data_manager' => $request->has('data_manager'),
        ]);

        return redirect()->back()->with('success', 'User Group created successfully.');
    }

    public function updateGroup(Request $request, $id)
    {
        $group = UserGroup::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($id == 1) {
            // If it's group 1, ONLY update the name. Ignore the checkbox inputs entirely.
            $group->update([
                'name' => $request->name
            ]);
        } else {
            // For all other groups, update everything
            $group->update([
                'name'         => $request->name,
                'view'         => $request->has('view'),
                'control'      => $request->has('control'),
                'historical'   => $request->has('historical'),
                'data_manager' => $request->has('data_manager'),
            ]);
        }

        return redirect()->back()->with('success', 'User Group updated successfully.');
    }

    public function destroyGroup($id)
    {
        // 1. Block deleting the primary admin group
        if ($id == 1) {
            return redirect()->back()->with('error', 'The primary administrator group cannot be deleted.');
        }

        // 2. Check if any users are assigned to this group
        $group = UserGroup::withCount('users')->findOrFail($id);

        if ($group->users_count > 0) {
            return redirect()->back()->with('error', 'This group cannot be deleted because it still has ' . $group->users_count . ' user(s) assigned to it.');
        }

        // 3. Delete if empty
        $group->delete();
        return redirect()->back()->with('success', 'User Group deleted successfully.');
    }

    public function generateTelegramLink()
    {
        $user = Auth::user();
        
        // Generate a random 32-character token
        $token = Str::random(32);
        
        // Save it to the user's database row
        $user->update([
            'telegram_link_token' => $token
        ]);

        // Pull the username from .env and build the deep link
        $botUsername = env('TELEGRAM_BOT_USERNAME');
        $telegramUrl = "https://t.me/{$botUsername}?start={$token}";

        return redirect()->back()->with('telegramUrl', $telegramUrl);
    }

}
