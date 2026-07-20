<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PortController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-admin');

        $ports = Port::with('country')->paginate(10);
        return view('admin.ports.index', compact('ports'));
    }

    public function create()
    {
        Gate::authorize('manage-admin');

        $countries = Country::all();
        return view('admin.ports.create', compact('countries'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-admin');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'type' => ['nullable', 'string', 'max:255'],
        ]);

        Port::create($request->all());

        return redirect()->route('ports.index')->with('success', 'Pelabuhan berhasil ditambahkan.');
    }

    public function edit(Port $port)
    {
        Gate::authorize('manage-admin');

        $countries = Country::all();
        return view('admin.ports.edit', compact('port', 'countries'));
    }

    public function update(Request $request, Port $port)
    {
        Gate::authorize('manage-admin');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'type' => ['nullable', 'string', 'max:255'],
        ]);

        $port->update($request->all());

        return redirect()->route('ports.index')->with('success', 'Pelabuhan berhasil diperbarui.');
    }

    public function destroy(Port $port)
    {
        Gate::authorize('manage-admin');

        $port->delete();

        return redirect()->route('ports.index')->with('success', 'Pelabuhan berhasil dihapus.');
    }

    public function apiIndex()
    {
        $ports = Port::with('country')->get()->map(function ($port) {
            return [
                'name' => $port->name,
                'country' => $port->country->name ?? 'N/A',
                'lat' => (float)$port->latitude,
                'lng' => (float)$port->longitude,
                'type' => $port->type ?? 'N/A',
            ];
        });

        return response()->json($ports);
    }
}
