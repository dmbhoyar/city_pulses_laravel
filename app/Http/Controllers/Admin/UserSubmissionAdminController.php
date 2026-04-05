<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubmission;
use App\Services\RubyPointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserSubmissionAdminController extends Controller
{
    public function index()
    {
        $submissions = UserSubmission::with('user')->orderByDesc('created_at')->get();
        return view('admin.user_submissions.index', compact('submissions'));
    }

    public function approve($id)
    {
        $submission = UserSubmission::findOrFail($id);
        $submission->status = 'approved';
        $submission->save();
        // Award Ruby Points
        RubyPointsService::awardPoints($submission->user_id, 10, 'User submission approved');
        return redirect()->back()->with('success', __('ui.submission_approved'));
    }

    public function inactivate($id)
    {
        $submission = UserSubmission::findOrFail($id);
        $submission->status = 'inactive';
        $submission->save();
        return redirect()->back()->with('success', __('ui.submission_inactivated'));
    }

    public function reject($id)
    {
        $submission = UserSubmission::findOrFail($id);
        $submission->status = 'rejected';
        $submission->save();
        return redirect()->back()->with('success', __('ui.submission_rejected'));
    }

    public function destroy($id)
    {
        $submission = UserSubmission::findOrFail($id);
        if ($submission->photo) {
            Storage::disk('public')->delete($submission->photo);
        }
        $submission->delete();
        return redirect()->back()->with('success', __('ui.submission_deleted'));
    }
}
