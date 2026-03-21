<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $jobs = Job::query()
            ->with(['city:id,name', 'user:id,email'])
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . strtolower($q) . '%';
                $query->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(company) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(category) LIKE ?', [$like]);
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $cities = City::query()->orderBy('name')->get(['id', 'name']);
        $users = User::query()->orderBy('email')->get(['id', 'email']);

        return view('admin.jobs.index', compact('jobs', 'cities', 'users', 'q'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        Job::create($validated);

        return redirect()->route('admin.jobs.index')->with('notice', 'Job created successfully.');
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs.index')->with('notice', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('notice', 'Job deleted successfully.');
    }
}
