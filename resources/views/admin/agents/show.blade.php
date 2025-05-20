@extends('layouts.app')

@section('title', 'View Collection Agent')

@section('content')
<div class="container">
    <h1 class="mb-4">Collection Agent Details</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $agent->full_name }}</h5>
            <p><strong>Email:</strong> {{ $agent->user->email }}</p>
            <p><strong>Phone:</strong> {{ $agent->phone ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $agent->address ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ $agent->profile_status }}</p>

            <h6 class="mt-4">Documents</h6>
            @if($agent->documents->isEmpty())
                <p>No documents uploaded.</p>
            @else
                <ul>
                    @foreach($agent->documents as $doc)
                        <li>{{ ucfirst($doc->document_type) }}: <a href="{{ Storage::url($doc->file_path) }}" target="_blank">View</a></li>
                    @endforeach
                </ul>
            @endif

            <h6 class="mt-4">Assigned Loans</h6>
            @if($agent->loans->isEmpty())
                <p>No loans assigned.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agent->loans as $loan)
                            <tr>
                                <td>{{ $loan->loan_id }}</td>
                                <td>{{ $loan->customer->full_name }}</td>
                                <td>{{ $loan->amount }}</td>
                                <td>{{ $loan->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary mt-3">Back to List</a>
        </div>
    </div>
</div>
@endsection