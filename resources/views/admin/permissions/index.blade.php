@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="container">
    <h1 class="mb-4">Permissions Management</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Role</th>
                <th>Add Customer</th>
                <th>Add Agent</th>
                <th>Edit/Delete</th>
                <th>Time Limit (Hours)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
            <tr>
                <td>{{ $permission->role->role_name }}</td>
                <td>
                    <input type="checkbox" {{ $permission->can_add_customer ? 'checked' : '' }} disabled>
                </td>
                <td>
                    <input type="checkbox" {{ $permission->can_add_agent ? 'checked' : '' }} disabled>
                </td>
                <td>
                    <input type="checkbox" {{ $permission->can_edit_delete ? 'checked' : '' }} disabled>
                </td>
                <td>{{ $permission->edit_delete_time_limit }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPermissionModal{{ $permission->permission_id }}">Edit</button>
                </td>
            </tr>

            <!-- Edit Permission Modal -->
            <div class="modal fade" id="editPermissionModal{{ $permission->permission_id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Permissions for {{ $permission->role->role_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.permissions.update', $permission->permission_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Add Customer</label>
                                    <input type="checkbox" name="can_add_customer" {{ $permission->can_add_customer ? 'checked' : '' }}>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Add Agent</label>
                                    <input type="checkbox" name="can_add_agent" {{ $permission->can_add_agent ? 'checked' : '' }}>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Edit/Delete</label>
                                    <input type="checkbox" name="can_edit_delete" {{ $permission->can_edit_delete ? 'checked' : '' }}>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Edit/Delete Time Limit (Hours)</label>
                                    <input type="number" class="form-control" name="edit_delete_time_limit" value="{{ $permission->edit_delete_time_limit }}" min="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>
@endsection