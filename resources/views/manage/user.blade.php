@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
    </div>

    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-6">
            <button id="tab-users" onclick="switchTab('users')" class="text-red-600 border-red-600 border-b-2 py-2 px-1 text-sm font-medium transition-colors">
                Users
            </button>
            <button id="tab-groups" onclick="switchTab('groups')" class="text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 border-b-2 py-2 px-1 text-sm font-medium transition-colors">
                User Groups
            </button>
        </nav>
    </div>
    
    <div id="content-users" class="tab-content">
        <div class="flex justify-end mb-4">
            <button onclick="openUserModal()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition font-medium text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add User
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table id="usersTable" class="display w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="p-3">Username</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Telegram ID</th>
                        <th class="p-3">User Group</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3">
                            <div class="font-medium text-gray-800">{{ $user->username }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $user->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3 text-gray-500">{{ $user->email }}</td>
                        <td class="p-3">
                            @if(is_null($user->telegram_id))
                                <button onclick="openTelegramModal({{ $user->id }})" class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded text-xs transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Telegram ID
                                </button>
                            @else
                                <div class="flex items-center space-x-2">
                                    <span class="font-mono text-gray-600">{{ $user->telegram_id }}</span>
                                    <button onclick="openTelegramModal({{ $user->id }})" class="p-1 bg-gray-100 text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded transition" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" /></svg>
                                    </button>
                                </div>
                            @endif
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">
                                {{ $user->userGroup ? $user->userGroup->name : 'None' }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="flex items-center space-x-2">
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

    <div id="content-groups" class="tab-content hidden">
        <div class="flex justify-end mb-4">
            <button onclick="openGroupModal()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition font-medium text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add Group
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table id="groupsTable" class="display w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="p-3">Group Name</th>
                        <th class="p-3 text-center">View</th>
                        <th class="p-3 text-center">Control</th>
                        <th class="p-3 text-center">Historical</th>
                        <th class="p-3 text-center">Manage Data</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($userGroups as $group)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-medium">{{ $group->name }}</td>
                        <td class="p-3 text-center">
                            @if($group->view) <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> @else - @endif
                        </td>
                        <td class="p-3 text-center">
                            @if($group->control) <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> @else - @endif
                        </td>
                        <td class="p-3 text-center">
                            @if($group->historical) <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> @else - @endif
                        </td>
                        <td class="p-3 text-center">
                            @if($group->data_manager) <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> @else - @endif
                        </td>
                        <td class="p-3">
                            <div class="flex items-center space-x-2">
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

<div id="userModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl transform transition-all">
        <h2 id="userModalTitle" class="text-lg font-bold mb-4 border-b pb-2">User</h2>
        <form id="userForm" method="POST" onsubmit="return validatePasswords()">
            @csrf
            <input type="hidden" name="_method" id="userFormMethod" value="POST">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="modalUsername" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="modalName" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="modalEmail" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">User Group</label>
                <select name="user_group_id" id="modalGroupId" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
                    <option value="" disabled selected>Select a group...</option>
                    @foreach($userGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="modalPassword" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500">
                    <p class="text-[10px] text-gray-400 mt-1" id="passwordHint">Leave blank to keep current</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" id="modalPasswordConfirm" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
            <p id="passwordError" class="text-red-500 text-xs hidden mb-4">Passwords do not match!</p>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Save User</button>
            </div>
        </form>
    </div>
</div>

<div id="groupModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-sm shadow-xl transform transition-all">
        <h2 id="groupModalTitle" class="text-lg font-bold mb-4 border-b pb-2">Group</h2>
        <form id="groupForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="groupFormMethod" value="POST">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                <input type="text" name="name" id="modalGroupName" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>
            
            <div class="space-y-3 mb-6 bg-gray-50 p-4 rounded border" id="groupCheckboxesWrapper">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="view" id="modalGroupView" class="rounded text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700 font-medium">View</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="control" id="modalGroupControl" class="rounded text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700 font-medium">Control</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="historical" id="modalGroupHistorical" class="rounded text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700 font-medium">Historical</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="data_manager" id="modalGroupDataManager" class="rounded text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700 font-medium">Manage Data</span>
                </label>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Save Group</button>
            </div>
        </form>
    </div>
</div>

<div id="telegramModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-96 shadow-xl transform transition-all">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Manage Telegram ID</h2>
        <div class="py-6 text-center text-gray-500 italic">Empty modal content</div>
        <div class="flex justify-end space-x-2 mt-4">
            <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Close</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({ "order": [], "pageLength": 10 });
        $('#groupsTable').DataTable({ "order": [], "pageLength": 10 });
    });

    function switchTab(tabName) {
        $('.tab-content').addClass('hidden');
        $('#tab-users, #tab-groups').removeClass('text-red-600 border-red-600').addClass('text-gray-500 border-transparent');
        $('#content-' + tabName).removeClass('hidden');
        $('#tab-' + tabName).removeClass('text-gray-500 border-transparent').addClass('text-red-600 border-red-600');
    }

    // --- User Form Logic ---
        function openUserModal(user = null) {
        // Reset form completely
        $('#userForm')[0].reset();
        $('#passwordError').addClass('hidden');
        
        // Ensure the dropdown is enabled by default when opening the modal
        $('#modalGroupId').prop('disabled', false);

        if (user) {
            // Edit Mode
            $('#userModalTitle').text('Edit User');
            $('#userForm').attr('action', '/users/' + user.id);
            $('#userFormMethod').val('PUT');
            $('#passwordHint').text('Leave blank to keep current password');
            $('#modalPassword').removeAttr('required');
            
            // Populate Data
            $('#modalUsername').val(user.username);
            $('#modalName').val(user.name);
            $('#modalEmail').val(user.email);
            $('#modalGroupId').val(user.user_group_id);

            // Lock the group selection if it is User 1
            if (user.id == 1) {
                $('#modalGroupId').prop('disabled', true);
            }

        } else {
            // Add Mode
            $('#userModalTitle').text('Add User');
            $('#userForm').attr('action', '/users');
            $('#userFormMethod').val('POST');
            $('#passwordHint').text('Password is required');
            $('#modalPassword').attr('required', 'required');
        }
        $('#userModal').removeClass('hidden');
    }

    function validatePasswords() {
        const pass = $('#modalPassword').val();
        const confirm = $('#modalPasswordConfirm').val();
        
        // If password is typed, it must match confirm password
        if (pass && pass !== confirm) {
            $('#passwordError').removeClass('hidden');
            return false;
        }
        return true;
    }

    // --- Group Form Logic ---
    function openGroupModal(group = null) {
        $('#groupForm')[0].reset();

        if (group) {
            $('#groupModalTitle').text('Edit Group');
            $('#groupForm').attr('action', '/user-groups/' + group.id);
            $('#groupFormMethod').val('PUT');
            $('#modalGroupName').val(group.name);
            
            // Hide checkboxes if ID is 1
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
            $('#groupCheckboxesWrapper').removeClass('hidden'); // Ensure visible for adding
        }
        $('#groupModal').removeClass('hidden');
    }


    // --- Telegram Form Logic ---
    function openTelegramModal(id) {
        $('#telegramModal').removeClass('hidden');
    }

    function closeModals() {
        $('#userModal, #groupModal, #telegramModal').addClass('hidden');
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('userModal') || 
            event.target == document.getElementById('groupModal') || 
            event.target == document.getElementById('telegramModal')) {
            closeModals();
        }
    }
</script>
@endsection
