@extends('layouts.app')

@section('title', 'Collection Agents')

@section('content')
<div class="container">
    <h1 class="mb-4">Collection Agent Management</h1>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(Auth::guard('admin')->user()->role->role_name === 'admin' || Auth::guard('admin')->user()->role->permissions->can_add_agent)
        <a href="{{ route('admin.agents.create') }}" class="btn btn-primary mb-3">Add New Agent</a>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agents as $agent)
                <tr>
                    <td>{{ $agent->agent_id }}</td>
                    <td>{{ $agent->full_name }}</td>
                    <td>{{ $agent->phone ?? 'N/A' }}</td>
                    <td>{{ $agent->profile_status }}</td>
                    <td>
                        <a href="{{ route('admin.agents.show', $agent->agent_id) }}" class="btn btn-info btn-sm">View</a>
                        @if(Auth::guard('admin')->user()->role->role_name === 'admin' || (Auth::guard('admin')->user()->role->permissions->can_edit_delete && now()->diffInHours($agent->created_at) < Auth::guard('admin')->user()->role->permissions->edit_delete_time_limit))
                            <a href="{{ route('admin.agents.edit', $agent->agent_id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.agents.destroy', $agent->agent_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No agents found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection