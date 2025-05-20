<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalCustomers = Customer::count();
        $totalLoans = Loan::count();
        $totalPayments = Payment::sum('amount');
        $customersWithLoans = Customer::whereHas('loans')->with(['loans', 'documents'])->get();


        return view('admin.dashboard', compact('totalCustomers', 'totalLoans',
         'totalPayments', 'customersWithLoans'));
    }
}