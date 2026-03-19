<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $q = $request->input('q');
        $jobs = Job::search($q)->orderByDesc('created_at');
        if ($request->filled('city_id')) {
            $jobs->where('city_id', $request->input('city_id'));
        }
        $jobs = $jobs->paginate(20);
        return view('jobs.index', compact('jobs', 'q'));
    }

    public function show(Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    public function create()
    {
        if (!auth()->user()?->isShopowner() && !auth()->user()?->isSuperadmin()) {
            return redirect()->route('jobs.index')->with('alert', 'Only shop owners can create jobs.');
        }
        $job = new Job();
        return view('jobs.create', compact('job'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()?->isShopowner() && !auth()->user()?->isSuperadmin()) {
            return redirect()->route('jobs.index')->with('alert', 'Only shop owners can create jobs.');
        }
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category'     => 'nullable|string|max:100',
            'company'      => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:255',
            'external_url' => 'nullable|url',
        ]);
        $job = new Job($validated);
        $job->user_id = auth()->id();
        $job->city_id = $job->city_id ?? auth()->user()->shops()->first()?->city_id;
        $job->save();
        return redirect()->route('jobs.show', $job)->with('notice', 'Job created.');
    }

    public function edit(Job $job)
    {
        return view('jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category'     => 'nullable|string|max:100',
            'company'      => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:255',
            'external_url' => 'nullable|url',
        ]);
        $job->update($validated);
        return redirect()->route('jobs.show', $job)->with('notice', 'Job updated.');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.index')->with('notice', 'Job removed.');
    }

    public function apply(Job $job)
    {
        if ($job->external_url) {
            return redirect()->away($job->external_url);
        }
        return view('jobs.apply', compact('job'));
    }

    public function submitApplication(Request $request, Job $job)
    {
        $app = new JobApplication([
            'name'       => $request->input('applicant_name'),
            'email'      => $request->input('applicant_email'),
            'phone'      => $request->input('applicant_phone'),
            'message'    => $request->input('message'),
            'resume_url' => $request->input('resume_url'),
            'job_id'     => $job->id,
        ]);
        if ($app->save()) {
            return redirect()->route('jobs.show', $job)->with('notice', 'Application submitted. The employer will be notified.');
        }
        return back()->with('alert', 'There was a problem submitting your application.')->withInput();
    }
}
