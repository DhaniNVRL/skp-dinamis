<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use App\Services\RawDataExportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RawDataController extends Controller
{
    public function index()
    {
        return view('admin.raw-data.index', [
            'activities' => Activity::query()->orderBy('name')->get(['id', 'name']),
            'groups' => Group::query()->orderBy('name')->get(['id', 'activity_id', 'name']),
        ]);
    }

    public function download(Request $request, RawDataExportService $exporter)
    {
        $validated = $request->validate([
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
            'group_id' => [
                'required',
                'integer',
                Rule::exists('groups', 'id')->where(
                    fn ($query) => $query->where('activity_id', $request->integer('activity_id'))
                ),
            ],
        ]);

        $activity = Activity::query()->findOrFail($validated['activity_id']);
        $group = Group::query()->findOrFail($validated['group_id']);

        return $exporter->download($activity, $group);
    }
}
