@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="container">
    <h1 class="mb-4">Add New Customer</h1>
    <form action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address"></textarea>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" class="form-control" id="photo" name="photo">
        </div>
        <div class="mb-3">
            <label for="aadhar" class="form-label">Aadhar Card</label>
            <input type="file" file_path="form-control" id="aadhar" name="aadhar">
        </div>
        <div class="mb-3">
            <label for="passbook" class="form-label">Bank Passbook</label>
            <input type="file" class="form-control" id="passbook" name="passbook">
        </div>
        <button type="submit" class="btn btn-primary">Save Customer</button>
    </form>
</div>
@endsection