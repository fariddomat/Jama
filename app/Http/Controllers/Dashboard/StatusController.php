<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Status;

class StatusController extends Controller
{

    public function index()
    {
        $statuses = \App\Models\Status::all();
        return view('dashboard.statuses.index', compact('statuses'));
    }

    public function create()
    {
        
        return view('dashboard.statuses.create', compact([],));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $status = \App\Models\Status::create($validated);
        
        return redirect()->route('dashboard.statuses.index')->with('success', 'Status created successfully.');
    }

    public function show($id)
    {
        $status = \App\Models\Status::findOrFail($id);
        
        return view('dashboard.statuses.show', compact('status'));
    }

    public function edit($id)
    {
        $status = \App\Models\Status::findOrFail($id);
        
        return view('dashboard.statuses.edit', compact('status', ));
    }

    public function update(Request $request, $id)
    {
        $status = \App\Models\Status::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $status->update($validated);
        
        return redirect()->route('dashboard.statuses.index')->with('success', 'Status updated successfully.');
    }

        public function destroy($id)
    {
        $status = \App\Models\Status::findOrFail($id);
        $status->delete();
        return redirect()->route('dashboard.statuses.index')->with('success', 'Status deleted successfully.');
    }
    public function restore($id)
    {
        $status = \App\Models\Status::withTrashed()->findOrFail($id);
        $status->restore();
        return redirect()->route('dashboard.statuses.index')->with('success', 'Status restored successfully.');
    }
}