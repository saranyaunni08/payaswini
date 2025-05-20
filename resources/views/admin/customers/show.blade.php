@extends('layouts.app')

@section('title', 'View Customer')

@section('content')
<div class="container my-5">
    <h1 class="text-center mb-4">Customer Details</h1>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">{{ $customer->full_name }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Email Address:</strong> {{ $customer->user->email }}</p>
                    <p><strong>Contact Number:</strong> {{ $customer->phone ?? 'N/A' }}</p>
                    <p><strong>Residential Address:</strong> {{ $customer->address ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Profile Status:</strong> 
                        <span class="badge {{ $customer->profile_status == 'approved' ? 'bg-success' : ($customer->profile_status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst($customer->profile_status) }}
                        </span>
                    </p>
                    @if($customer->profile_status != 'pending' && $customer->approved_by && $customer->approved_at)
                        <p><strong>Approved/Rejected By:</strong> {{ $customer->approvedBy->full_name }}</p>
                        <p><strong>Approved/Rejected At:</strong> {{ \Carbon\Carbon::parse($customer->approved_at)->format('d M Y, H:i') }}</p>
                        @endif
                    <p><strong>Documents Verified:</strong> 
                        <span class="badge {{ $customer->documents->count() >= 3 ? 'bg-success' : 'bg-warning' }}">
                            {{ $customer->documents->count() >= 3 ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
            </div>

            <h5 class="mt-4">Documents</h5>
            @if($customer->documents->isEmpty())
                <p class="text-muted">No documents uploaded.</p>
            @else
                <div class="row">
                    @foreach($customer->documents as $doc)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title">{{ ucfirst($doc->document_type) }}</h6>
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-primary">View Document</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <h5 class="mt-4">Loan Applications</h5>
            @if($customer->loans->isEmpty())
                <p class="text-muted">No loan applications found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Applied On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->loans as $loan)
                                <tr>
                                    <td>{{ $loan->loan_id }}</td>
                                    <td>{{ number_format($loan->amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $loan->status == 'accepted' ? 'bg-success' : ($loan->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $loan->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <h5 class="mt-4">Profile Status Change Log</h5>
            @if($customer->statusLogs->isEmpty())
                <p class="text-muted">No status changes recorded.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Old Status</th>
                                <th>New Status</th>
                                <th>Changed By</th>
                                <th>Changed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->statusLogs as $log)
                                <tr>
                                    <td>{{ $log->old_status ? ucfirst($log->old_status) : 'N/A' }}</td>
                                    <td>{{ ucfirst($log->new_status) }}</td>
                                    <td>{{ $log->changedBy->full_name }}</td>
                                    <td>
                                        {{ $log->changed_at ? $log->changed_at->format('d M Y, H:i') : 'N/A' }}
                                    </td>
                                
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4 text-center">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection