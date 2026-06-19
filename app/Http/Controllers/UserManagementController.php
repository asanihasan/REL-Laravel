<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserGroup; // Note: Ensure you have created this model!

class UserManagementController extends Controller
{
        public function index()
    {
        // Eager load the group so we don't query the database for every single user row
        $users = User::with('userGroup')->get();
        $userGroups = UserGroup::all();
        
        return view('manage.user', compact('users', 'userGroups'));
    }

    // Example of Delete logic blocking user 1:
    public function destroyUser($id)
    {
        if ($id == 1) {
            return redirect()->back()->with('error', 'The primary administrator account cannot be deleted.');
        }

        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    // Example validation inside your User Store/Update function:
    public function storeUser(Request $request) 
    {
        $request->validate([
            'username' => 'required|unique:users,username', // Must be unique in DB
            'email' => 'required|email|unique:users,email', // Must be unique in DB
            // ... other validations
        ]);
        // ... insert
    }

}
