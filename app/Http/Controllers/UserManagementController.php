<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserGroup; // Note: Ensure you have created this model!

class UserManagementController extends Controller
{
    public function index()
    {
        // Fetch all users
        $users = User::all(); 
        
        // Fetch all user groups (if the model doesn't exist yet, this will throw an error, 
        // so make sure to create the UserGroup model or comment this out temporarily)
        $userGroups = UserGroup::all(); 

        // Return the view and pass the data to it
        return view('manage.user', compact('users', 'userGroups'));
    }
}
