<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GuideController extends Controller
{
    public function index(): View
    {
        $guides = Guide::orderBy('date_from', 'desc')->paginate(10);
        
        return view('guides.index', compact('guides'));
    }

    public function create(): View
    {
        $countries = Guide::getCountries();
        $kenyaGuides = Guide::getKenyaGuides();
        
        return view('guides.create', compact('countries', 'kenyaGuides'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'notes' => 'nullable|string',
        ]);

        Guide::create($validated);

        return redirect()->route('guides.index')
            ->with('success', 'Guide assignment created successfully.');
    }

    public function show(Guide $guide): View
    {
        return view('guides.show', compact('guide'));
    }

    public function edit(Guide $guide): View
    {
        $countries = Guide::getCountries();
        $kenyaGuides = Guide::getKenyaGuides();
        
        return view('guides.edit', compact('guide', 'countries', 'kenyaGuides'));
    }

    public function update(Request $request, Guide $guide): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'notes' => 'nullable|string',
        ]);

        $guide->update($validated);

        return redirect()->route('guides.index')
            ->with('success', 'Guide assignment updated successfully.');
    }

    public function destroy(Guide $guide): RedirectResponse
    {
        $guide->delete();

        return redirect()->route('guides.index')
            ->with('success', 'Guide assignment deleted successfully.');
    }
}
