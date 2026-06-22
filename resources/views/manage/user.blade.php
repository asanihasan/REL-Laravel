@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
    </div>

    <div class="flex space-x-6 mb-6 border-b border-gray-200">
        <button onclick="switchTab('users')" id="tab-users" class="px-4 py-3 flex items-center gap-2 border-b-2 border-red-600 text-red-600 font-semibold transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            Users
        </button>
        <button onclick="switchTab('groups')" id="tab-groups" class="px-4 py-3 flex items-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-red-600 font-semibold transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            User Groups
        </button>
    </div>

    <div id="users-content">
        <div class="mb-4 flex justify-end">
            <button onclick="openUserModal()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition font-medium flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Add User
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
               <table id="usersTable" class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-3 text-sm font-semibold text-gray-600">Username</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Name</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Email</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Telegram</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Group</th>
                        <th class="p-3 text-sm font-semibold text-gray-600 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3" data-sort="{{ $user->created_at }}">
                            <div class="font-medium text-gray-800">{{ $user->username }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                            </div>
                        </td>
                        <td class="p-3">{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td class="p-3 text-gray-600">{{ $user->email }}</td>
                        <td class="p-3">
                            @if($user->telegram_id)
                                <div class="text-sm text-gray-800 font-medium">{{ $user->telegram_id }}</div>
                                <button onclick="openTelegramModal({{ $user->id }})" class="text-xs px-2 py-1 mt-1 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded border transition">
                                    Update ID
                                </button>
                            @else
                                <button onclick="openTelegramModal({{ $user->id }})" class="text-xs px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded border border-blue-200 transition">
                                    Add ID
                                </button>
                            @endif
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 bg-red-50 text-red-700 text-xs rounded-full font-medium">
                                {{ $user->userGroup->name ?? 'No Group' }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="openUserModal({{ $user->toJson() }})" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" /></svg>
                                </button>
                                
                                @if($user->id != 1)
                                <form action="/users/{{ $user->id }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded transition" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="groups-content" class="hidden">
        <div class="mb-4 flex justify-end">
            <button onclick="openGroupModal()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition font-medium flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Add Group
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table id="groupsTable" class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-3 text-sm font-semibold text-gray-600">Group Name</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Permissions</th>
                        <th class="p-3 text-sm font-semibold text-gray-600 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($userGroups as $group)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-800" data-sort="{{ $group->created_at }}">{{ $group->name }}</td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-1">
                                @if($group->view) <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">View</span> @endif
                                @if($group->control) <span class="px-2 py-1 bg-green-50 text-green-700 text-xs rounded-full">Control</span> @endif
                                @if($group->historical) <span class="px-2 py-1 bg-purple-50 text-purple-700 text-xs rounded-full">Historical</span> @endif
                                @if($group->data_manager) <span class="px-2 py-1 bg-yellow-50 text-yellow-700 text-xs rounded-full">Data Manager</span> @endif
                            </div>
                        </td>
                        <td class="p-3">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="openGroupModal({{ $group->toJson() }})" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" /></svg>
                                </button>
                                
                                @if($group->id != 1)
                                <form action="/user-groups/{{ $group->id }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded transition" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 id="userModalTitle" class="text-lg font-bold text-gray-800">Add User</h3>
            <button onclick="closeModal('userModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="userForm" method="POST" action="/users" class="px-6 py-4 max-h-[80vh] overflow-y-auto">
            @csrf
            <input type="hidden" name="_method" id="userFormMethod" value="POST">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="modalUsername" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" id="modalFirstName" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                    <input type="text" name="last_name" id="modalLastName" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="modalEmail" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">User Group</label>
                <select name="user_group_id" id="modalGroupId" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
                    <option value="">Select a group...</option>
                    @foreach($userGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 mb-4">
                <h4 class="font-semibold text-gray-700 border-b pb-2 mb-3">Alert Preferences</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 p-4 rounded border border-gray-200">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="engine_running" id="modalAlertEngineRunning" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                        <span class="text-sm text-gray-700">Engine Running</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="engine_stopped" id="modalAlertEngineStopped" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                        <span class="text-sm text-gray-700">Engine Stopped</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="high_rpm" id="modalAlertHighRpm" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                        <span class="text-sm text-gray-700">High RPM</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="low_rpm" id="modalAlertLowRpm" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                        <span class="text-sm text-gray-700">Low RPM</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="low_fuel_level" id="modalAlertLowFuel" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                        <span class="text-sm text-gray-700">Low Fuel Level</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="location_change" id="modalAlertLocation" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                        <span class="text-sm text-gray-700">Location Change</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="modbus_comm_lost" id="modalAlertModbus" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                        <span class="text-sm text-gray-700">Modbus Comm Lost</span>
                    </label>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="modalPassword" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500">
                <p id="passwordHint" class="text-xs text-gray-500 mt-1"></p>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeModal('userModal')" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Save User</button>
            </div>
        </form>
    </div>
</div>

<div id="groupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 id="groupModalTitle" class="text-lg font-bold text-gray-800">Add Group</h3>
            <button onclick="closeModal('groupModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="groupForm" method="POST" action="/user-groups" class="px-6 py-4">
            @csrf
            <input type="hidden" name="_method" id="groupFormMethod" value="POST">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                <input type="text" name="name" id="modalGroupName" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>
            
            <div class="space-y-3 mb-6 bg-gray-50 p-4 rounded border" id="groupCheckboxesWrapper">
                <h4 class="font-medium text-sm text-gray-700 border-b pb-1">Permissions</h4>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="view" id="modalGroupView" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                    <span class="text-sm text-gray-700">View Data</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="control" id="modalGroupControl" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                    <span class="text-sm text-gray-700">Control Pumps</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="historical" id="modalGroupHistorical" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                    <span class="text-sm text-gray-700">View Historical</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="data_manager" id="modalGroupDataManager" class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                    <span class="text-sm text-gray-700">Data Manager</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('groupModal')" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Save Group</button>
            </div>
        </form>
    </div>
</div>

<div id="telegramModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-sm mx-4 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Link Telegram</h3>
            <button onclick="closeModal('telegramModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6 flex flex-col items-center text-center">
            <p class="text-sm text-gray-600 mb-6">Scan this QR code with your phone camera, or copy the link below.</p>

            <div id="qrLoading" class="my-8 text-gray-500 animate-pulse">Generating secure link...</div>

            <img id="qrImage" src="" alt="Telegram QR Code" class="hidden w-48 h-48 border rounded shadow-sm mb-6">

            <div id="qrLinkContainer" class="hidden w-full flex items-center justify-center space-x-2 bg-gray-50 p-3 rounded border break-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <a href="#" id="qrLink" target="_blank" class="text-sm text-blue-600 hover:underline font-medium truncate"></a>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Initialize Users Table
        $('#usersTable').DataTable({
            paging: false,
            info: false, // <-- THIS REMOVES THE BOTTOM TEXT
            order: [[0, 'desc']], 
            columnDefs: [
                { orderable: false, targets: 5 } 
            ]
        });

        // Initialize Groups Table
        $('#groupsTable').DataTable({
            paging: false,
            info: false, // <-- THIS REMOVES THE BOTTOM TEXT
            order: [[0, 'desc']], 
            columnDefs: [
                { orderable: false, targets: 2 } 
            ]
        });
    });
    
    function switchTab(tab) {
        if (tab === 'users') {
            $('#users-content').removeClass('hidden');
            $('#groups-content').addClass('hidden');
            
            $('#tab-users').addClass('border-red-600 text-red-600').removeClass('border-transparent text-gray-500');
            $('#tab-groups').removeClass('border-red-600 text-red-600').addClass('border-transparent text-gray-500');
        } else {
            $('#groups-content').removeClass('hidden');
            $('#users-content').addClass('hidden');
            
            $('#tab-groups').addClass('border-red-600 text-red-600').removeClass('border-transparent text-gray-500');
            $('#tab-users').removeClass('border-red-600 text-red-600').addClass('border-transparent text-gray-500');
        }
    }

    function closeModal(id) {
        $('#' + id).addClass('hidden');
    }

    function openUserModal(user = null) {
        $('#userForm')[0].reset();
        $('#modalGroupId').prop('disabled', false);

        if (user) {
            $('#userModalTitle').text('Edit User');
            $('#userForm').attr('action', '/users/' + user.id);
            $('#userFormMethod').val('PUT');
            $('#passwordHint').text('Leave blank to keep current password');
            $('#modalPassword').removeAttr('required');
            
            $('#modalUsername').val(user.username);
            $('#modalFirstName').val(user.first_name);
            $('#modalLastName').val(user.last_name);
            $('#modalEmail').val(user.email);
            $('#modalGroupId').val(user.user_group_id);
            
            // Checkboxes
            $('#modalAlertEngineRunning').prop('checked', user.engine_running);
            $('#modalAlertEngineStopped').prop('checked', user.engine_stopped);
            $('#modalAlertHighRpm').prop('checked', user.high_rpm);
            $('#modalAlertLowRpm').prop('checked', user.low_rpm);
            $('#modalAlertLowFuel').prop('checked', user.low_fuel_level);
            $('#modalAlertLocation').prop('checked', user.location_change);
            $('#modalAlertModbus').prop('checked', user.modbus_comm_lost);

            if (user.id == 1) {
                $('#modalGroupId').prop('disabled', true);
            }
        } else {
            $('#userModalTitle').text('Add User');
            $('#userForm').attr('action', '/users');
            $('#userFormMethod').val('POST');
            $('#passwordHint').text('Password is required');
            $('#modalPassword').attr('required', 'required');
        }
        $('#userModal').removeClass('hidden');
    }

    function openGroupModal(group = null) {
        $('#groupForm')[0].reset();

        if (group) {
            $('#groupModalTitle').text('Edit Group');
            $('#groupForm').attr('action', '/user-groups/' + group.id);
            $('#groupFormMethod').val('PUT');
            $('#modalGroupName').val(group.name);
            
            if (group.id == 1) {
                $('#groupCheckboxesWrapper').addClass('hidden');
            } else {
                $('#groupCheckboxesWrapper').removeClass('hidden');
                $('#modalGroupView').prop('checked', group.view);
                $('#modalGroupControl').prop('checked', group.control);
                $('#modalGroupHistorical').prop('checked', group.historical);
                $('#modalGroupDataManager').prop('checked', group.data_manager);
            }
        } else {
            $('#groupModalTitle').text('Add Group');
            $('#groupForm').attr('action', '/user-groups');
            $('#groupFormMethod').val('POST');
            $('#groupCheckboxesWrapper').removeClass('hidden'); 
        }
        $('#groupModal').removeClass('hidden');
    }

    function openTelegramModal(userId) {
        // Reset and show modal
        $('#telegramModal').removeClass('hidden');
        $('#qrImage, #qrLinkContainer').addClass('hidden');
        $('#qrLoading').removeClass('hidden').text('Generating secure link...');

        // AJAX request to generate the token
        $.post('/users/' + userId + '/telegram-link', {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if(response.url) {
                $('#qrLoading').addClass('hidden');
                
                // Use a free public API to instantly generate the QR code image from the URL
                $('#qrImage').attr('src', 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(response.url)).removeClass('hidden');
                
                // Set the link text and href
                $('#qrLink').attr('href', response.url).text(response.url);
                $('#qrLinkContainer').removeClass('hidden');
            }
        }).fail(function() {
            $('#qrLoading').text('Error generating link. Please try again.');
        });
    }
</script>
@endsection
