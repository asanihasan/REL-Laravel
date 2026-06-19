<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Hash;

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
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'user_group_id' => 'required|exists:user_groups,id',
            'password'      => 'required|string|min:6',
        ]);

        User::create([
            'username'      => $request->username,
            'name'          => $request->name,
            'email'         => $request->email,
            'user_group_id' => $request->user_group_id,
            'password'      => Hash::make($request->password), // Always hash passwords!
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

        public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1. Base validation rules
        $rules = [
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ];

        // 2. Only require user_group_id if it's NOT the primary admin
        if ($id != 1) {
            $rules['user_group_id'] = 'required|exists:user_groups,id';
        }

        $request->validate($rules);

        // 3. Prepare data to update
        $data = [
            'username' => $request->username,
            'name'     => $request->name,
            'email'    => $request->email,
        ];

        // 4. Only update the group ID if it's NOT the primary admin
        if ($id != 1) {
            $data['user_group_id'] = $request->user_group_id;
        }

        // 5. Update password if provided
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
        if ($id == 1) {
            return redirect()->back()->with('error', 'The primary administrator group cannot be deleted.');
        }

        UserGroup::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User Group deleted successfully.');
    }
}
