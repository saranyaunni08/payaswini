@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Customer</h1>
    <form action="{{ route('admin.customers.update', $customer->customer_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $customer->full_name }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $customer->user->email }}" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $customer->phone }}">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Residential Address</label>
            <textarea class="form-control" id="address" name="address">{{ $customer->address }}</textarea>
        </div>

        <!-- Photo Field with Existing Document View -->
        <div class="mb-3">
            <label for="photo" class="form-label">Update Photo (Optional)</label>
            <div class="input-group">
                <input type="file" class="form-control" id="photo" name="photo">
                @php
                    $photoDoc = $customer->documents->firstWhere('document_type', 'photo');
                @endphp
                @if($photoDoc)
                    <a href="{{ Storage::url($photoDoc->file_path) }}" target="_blank" class="btn btn-sm btn-primary ms-2">View Existing Photo</a>
                @endif
            </div>
        </div>

        <!-- Aadhar Field with Existing Document View -->
        <div class="mb-3">
            <label for="aadhar" class="form-label">Update Aadhar (Optional)</label>
            <div class="input-group">
                <input type="file" class="form-control" id="aadhar" name="aadhar">
                @php
                    $aadharDoc = $customer->documents->firstWhere('document_type', 'aadhar');
                @endphp
                @if($aadharDoc)
                    <a href="{{ Storage::url($aadharDoc->file_path) }}" target="_blank" class="btn btn-sm btn-primary ms-2">View Existing Aadhar</a>
                @endif
            </div>
        </div>

        <!-- Passbook Field with Existing Document View -->
        <div class="mb-3">
            <label for="passbook" class="form-label">Update Passbook (Optional)</label>
            <div class="input-group">
                <input type="file" class="form-control" id="passbook" name="passbook">
                @php
                    $passbookDoc = $customer->documents->firstWhere('document_type', 'passbook');
                @endphp
                @if($passbookDoc)
                    <a href="{{ Storage::url($passbookDoc->file_path) }}" target="_blank" class="btn btn-sm btn-primary ms-2">View Existing Passbook</a>
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Customer</button>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection