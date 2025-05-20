@extends('layouts.app')

@section('title', 'Edit Collection Agent')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Collection Agent</h1>
    <form action="{{ route('admin.agents.update', $agent->agent_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $agent->full_name }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $agent->user->email }}" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $agent->phone }}">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address">{{ $agent->address }}</textarea>
        </div>
        <div class="mb-3">
            <label for="profile_status" class="form-label">Profile Status</label>
            <select class="form-control" id="profile_status" name="profile_status" required>
                <option value="pending" {{ $agent->profile_status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $agent->profile_status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $agent->profile_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Update Photo</label>
            <input type="file" class="form-control" id="photo" name="photo">
        </div>
        <div class="mb-3">
            <label for="aadhar" class="form-label">Update Aadhar Card</label>
            <input type="file" class="form-control" id="aadhar" name="aadhar">
        </div>
        <div class="mb-3">
            <label for="passbook" class="form-label">Update Bank Passbook</label>
            <input type="file" class="form-control" id="passbook" name="passbook">
        </div>
        <button type="submit" class="btn btn-primary">Update Agent</button>
        <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection