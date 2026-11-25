<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use App\Services\RawDataExportService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RawDataController extends Controller
{
    public function index()
    {
        return view('admin.raw-data.index', [
            'activities' => Activity::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function download(Request $request, RawDataExportService $exporter)
    {
        $validated = $request->validate([
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
        ]);

        $activity = Activity::query()->findOrFail($validated['activity_id']);
        $groups = Group::query()->where('activity_id', $activity->id)->orderBy('name')->orderBy('id')->get();
        if ($groups->isEmpty()) {
            throw ValidationException::withMessages(['activity_id' => 'Activity ini belum memiliki group.']);
        }

        return $exporter->download($activity, $groups);
    }
}
