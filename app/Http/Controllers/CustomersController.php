<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomersController extends Controller
{



    public function toggleStatus($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = !$customer->status;
        $customer->save();

        return response()->json(['success' => true, 'status' => $customer->status ? 'Active' : 'Inactive']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return view('pos.list_customer', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pos.add_customer');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:customers,email',
            'phone'      => 'required|string|max:20',
        ]);

        Customer::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'customer added successfully!'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('pos.edit_customer', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:customers,email,' . $id,
            'phone'      => 'required|string|max:20',
        ]);

        $customer->update($request->only(['first_name', 'last_name', 'email', 'phone']));


        return response()->json(['success' => true, 'customer' => $customer]);
        // return redirect()->route('customer.list')->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();


        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'customer deleted successfully!'
            ]);
        }
    }
}
