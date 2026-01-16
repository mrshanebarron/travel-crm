<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Vendor::class);

        return view('vendors.index');
    }

    public function create()
    {
        $this->authorize('create', Vendor::class);
        $categories = Vendor::CATEGORIES;
        return view('vendors.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Vendor::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(Vendor::CATEGORIES)),
            'country' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Vendor::create($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    public function show(Vendor $vendor)
    {
        $this->authorize('view', $vendor);
        return view('vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        $this->authorize('update', $vendor);
        $categories = Vendor::CATEGORIES;
        return view('vendors.edit', compact('vendor', 'categories'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $this->authorize('update', $vendor);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(Vendor::CATEGORIES)),
            'country' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $vendor->update($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $this->authorize('delete', $vendor);

        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
