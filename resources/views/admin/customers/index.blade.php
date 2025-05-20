@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Customer Management</h1>
    
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

    @if(Auth::guard('admin')->user()->role->role_name === 'admin' || Auth::guard('admin')->user()->role->permissions->can_add_customer)
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mb-3">Add New Customer</a>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Profile Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->customer_id }}</td>
                        <td>{{ $customer->full_name }}</td>
                        <td>{{ $customer->user->email }}</td>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $customer->profile_status == 'approved' ? 'bg-success' : ($customer->profile_status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($customer->profile_status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer->customer_id) }}" class="btn btn-info btn-sm">View</a>
                            @if(Auth::guard('admin')->user()->role->role_name === 'admin' || (Auth::guard('admin')->user()->role->permissions->can_edit_delete && now()->diffInHours($customer->created_at) < Auth::guard('admin')->user()->role->permissions->edit_delete_time_limit))
                                <a href="{{ route('admin.customers.edit', $customer->customer_id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.customers.destroy', $customer->customer_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endif
                            @if(Auth::guard('admin')->user()->role->role_name === 'admin')
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Update Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="{{ route('admin.customers.update-status', $customer->customer_id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="profile_status" value="approved">
                                                <button type="submit" class="dropdown-item">Approved</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.customers.update-status', $customer->customer_id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="profile_status" value="rejected">
                                                <button type="submit" class="dropdown-item">Rejected</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection