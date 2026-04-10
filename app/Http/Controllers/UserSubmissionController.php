<?php

namespace App\Http\Controllers;

use App\Models\UserSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category', 'all');
        $tab = $request->input('tab', 'read');

        $approvedQuery = UserSubmission::where('status', 'approved')
            ->with('user')
            ->orderByDesc('created_at');

        if ($category !== 'all') {
            $approvedQuery->where('type', $category);
        }

        $approved = $approvedQuery->paginate(12)->withQueryString();

        $categoryCounts = UserSubmission::where('status', 'approved')
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        $mySubmissions = collect();
        $myPoints = 0;
        if (Auth::check()) {
            $mySubmissions = UserSubmission::where('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->get();
            $myPoints = $mySubmissions->where('status', 'approved')->count() * 10;
        }

        return view('user_submissions.index', compact(
            'approved', 'categoryCounts', 'mySubmissions',
            'myPoints', 'category', 'tab'
        ));
    }

    public function show(UserSubmission $userSubmission)
    {
        if ($userSubmission->status !== 'approved') {
            abort(404);
        }

        $userSubmission->loadMissing('user');

        return view('user_submissions.show', ['submission' => $userSubmission]);
    }

    public function create()
    {
        return redirect()->route('user_submissions.index', ['tab' => 'submit']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:story,news,blog,analysis,information',
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'photo'   => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user_submissions', 'public');
        }

        UserSubmission::create([
            'user_id' => Auth::id(),
            'type'    => $request->type,
            'title'   => $request->title,
            'content' => $request->content,
            'photo'   => $photoPath,
            'status'  => 'pending',
        ]);

        return redirect()->route('user_submissions.index', ['tab' => 'mine'])
            ->with('success', __('ui.submission_sent'));
    }
}
