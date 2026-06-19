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
                        <th class="p-3">ID</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-mono font-bold text-red-700">{{ $user->id }}</td>
                        <td class="p-3 font-medium">{{ $user->name }}</td>
                        <td class="p-3 text-gray-500">{{ $user->email }}</td>
                        <td class="p-3">
                            <div class="flex items-center space-x-2">
                                <button onclick="openUserModal('{{ $user->id }}')" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                        <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                                    </svg>
                                </button>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Group
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table id="groupsTable" class="display w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="p-3">ID</th>
                        <th class="p-3">Group Name</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($userGroups as $group)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-mono font-bold text-red-700">{{ $group->id }}</td>
                        <td class="p-3 font-medium">{{ $group->name }}</td>
                        <td class="p-3">
                            <div class="flex items-center space-x-2">
                                <button onclick="openGroupModal('{{ $group->id }}')" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                        <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                                    </svg>
                                </button>
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
    <div class="bg-white p-6 rounded-lg w-96 shadow-xl transform transition-all">
        <h2 id="userModalTitle" class="text-lg font-bold mb-4 border-b pb-2">User Modal</h2>
        <div class="py-6 text-center text-gray-500 italic">Empty modal content</div>
        <div class="flex justify-end space-x-2 mt-4">
            <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Close</button>
        </div>
    </div>
</div>

<div id="groupModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-96 shadow-xl transform transition-all">
        <h2 id="groupModalTitle" class="text-lg font-bold mb-4 border-b pb-2">Group Modal</h2>
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
        // Initialize DataTables for both tables
        $('#usersTable').DataTable({ "order": [], "pageLength": 10 });
        $('#groupsTable').DataTable({ "order": [], "pageLength": 10 });
    });

    // --- Tab Switching Logic ---
    function switchTab(tabName) {
        // Hide all content
        $('.tab-content').addClass('hidden');
        // Reset all tab styling
        $('#tab-users, #tab-groups')
            .removeClass('text-red-600 border-red-600')
            .addClass('text-gray-500 border-transparent');
        
        // Show selected content and activate tab styling
        $('#content-' + tabName).removeClass('hidden');
        $('#tab-' + tabName)
            .removeClass('text-gray-500 border-transparent')
            .addClass('text-red-600 border-red-600');
    }

    // --- Modal Logic ---
    function openUserModal(id = null) {
        // If ID exists, it's an edit. Otherwise, it's an add.
        const title = id ? 'Edit User' : 'Add User';
        $('#userModalTitle').text(title);
        $('#userModal').removeClass('hidden');
    }

    function openGroupModal(id = null) {
        const title = id ? 'Edit Group' : 'Add Group';
        $('#groupModalTitle').text(title);
        $('#groupModal').removeClass('hidden');
    }

    function closeModals() {
        $('#userModal, #groupModal').addClass('hidden');
    }

    // Close modal if user clicks outside the white box
    window.onclick = function(event) {
        if (event.target == document.getElementById('userModal') || event.target == document.getElementById('groupModal')) {
            closeModals();
        }
    }
</script>
@endsection
