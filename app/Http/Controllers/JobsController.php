<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\TextTranslationService;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $selectedCityId = null;
        if ($request->filled('city_id')) {
            $selectedCityId = (int) $request->input('city_id');
            $request->session()->put('city_id', $selectedCityId);
        } elseif ($request->session()->has('city_id')) {
            $selectedCityId = (int) $request->session()->get('city_id');
        }

        $q = trim((string) $request->input('q', ''));
        $category = trim((string) $request->input('category', ''));

        $jobsQuery = Job::query()
            ->with('city')
            ->search($q)
            ->orderByDesc('created_at');

        if ($selectedCityId) {
            $jobsQuery->where('city_id', $selectedCityId);
        }

        if ($category !== '') {
            $jobsQuery->where('category', $category);
        }

        $jobs = $jobsQuery->paginate(24)->withQueryString();
        $cities = City::query()->orderBy('name')->get(['id', 'name']);
        $categories = Job::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $categoryLabels = [];

        $locale = app()->getLocale();
        if ($locale !== 'en') {
            $jobs->getCollection()->transform(function ($job) use ($locale) {
                if (is_string($job->title) && trim($job->title) !== '') {
                    $job->title = TextTranslationService::translate($job->title, $locale);
                }

                if (is_string($job->description) && trim($job->description) !== '') {
                    $job->description = TextTranslationService::translate($job->description, $locale);
                }

                if (is_string($job->company) && trim($job->company) !== '') {
                    $job->company = TextTranslationService::translate($job->company, $locale);
                }

                if (is_string($job->location) && trim($job->location) !== '') {
                    $job->location = TextTranslationService::translate($job->location, $locale);
                }

                if (is_string($job->category) && trim($job->category) !== '') {
                    $job->category = TextTranslationService::translate($job->category, $locale);
                }

                return $job;
            });

            foreach ($categories as $categoryName) {
                $categoryLabels[$categoryName] = TextTranslationService::translate((string) $categoryName, $locale);
            }
        }

        $totalJobs = Job::query()->count();
        $cityJobs = $selectedCityId
            ? Job::query()->where('city_id', $selectedCityId)->count()
            : $totalJobs;
        $selectedCityName = optional($cities->firstWhere('id', $selectedCityId))->name;

        return view('jobs.index', compact(
            'jobs',
            'q',
            'category',
            'cities',
            'categories',
            'selectedCityId',
            'selectedCityName',
            'totalJobs',
            'cityJobs',
            'categoryLabels'
        ));
    }

    public function show(Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    public function create()
    {
        $this->ensureSuperadmin();

        $job = new Job();
        $cities = City::query()->orderBy('name')->get(['id', 'name']);

        return view('jobs.create', compact('job', 'cities'));
    }

    public function store(Request $request)
    {
        $this->ensureSuperadmin();

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category'     => 'nullable|string|max:100',
            'company'      => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:255',
            'external_url' => 'nullable|url',
            'city_id'      => 'nullable|exists:cities,id',
        ]);

        $job = new Job($validated);
        $job->user_id = auth()->id();
        $job->city_id = $validated['city_id'] ?? null;
        $job->save();

        return redirect()->route('jobs.show', $job)->with('notice', 'Job created.');
    }

    public function edit(Job $job)
    {
        $this->ensureSuperadmin();

        $cities = City::query()->orderBy('name')->get(['id', 'name']);

        return view('jobs.edit', compact('job', 'cities'));
    }

    public function update(Request $request, Job $job)
    {
        $this->ensureSuperadmin();

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category'     => 'nullable|string|max:100',
            'company'      => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:255',
            'external_url' => 'nullable|url',
            'city_id'      => 'nullable|exists:cities,id',
        ]);
        $job->update($validated);

        return redirect()->route('jobs.show', $job)->with('notice', 'Job updated.');
    }

    public function destroy(Job $job)
    {
        $this->ensureSuperadmin();

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

    private function ensureSuperadmin(): void
    {
        $user = auth()->user();

        if (!$user || !$user->isSuperadmin()) {
            abort(403, 'Only superadmin can manage jobs from this page.');
        }
    }
}
