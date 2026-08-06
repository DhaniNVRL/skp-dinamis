<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Answer;
use App\Models\Competitor;
use App\Models\Description;
use App\Models\FormType;
use App\Models\Group;
use App\Models\Question;
use App\Models\SubUnitQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Option;
use Throwable;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return redirect()->route('admin.units', [
            'id' => $id,
            'tab' => 'question',
        ]);
    }

    public function masterdata()
    {
        $forms = Form::all();

        return view('/admin/masterdata/form', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'integer',
                'exists:groups,id',
            ],

            'forms' => [
                'required',
                'array',
                'min:1',
            ],

            'forms.*.no_urut' => [
                'required',
                'integer',
                'min:1',
            ],

            'forms.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'forms.*.formtype_id' => [
                'required',
                'integer',
                'exists:form_types,id',
            ],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['forms'] as $formData) {
                Form::create([
                    'group_id' => $validated['group_id'],
                    'no_urut' => $formData['no_urut'],
                    'name' => $formData['name'],
                    'formtype_id' => $formData['formtype_id'],
                ]);
            }
        });

        return redirect()
            ->route('admin.units', [
                'id' => $validated['group_id'],
                'tab' => 'question',
            ])
            ->with('success', 'Form berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        return redirect()->route('admin.units', [
            'id' => $form->group_id,
            'tab' => 'question',
        ]);
    }

    public function edit($id)
    {
        $form = Form::query()->findOrFail($id);
        $formtypes = FormType::query()->orderBy('name')->get();

        return view('admin.edit.editform', compact('form', 'formtypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'exists:groups,id',
            ],

            'no_urut' => [
                'required',
                'integer',
                'min:1',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'formtype_id' => [
                'required',
                'exists:form_types,id',
            ],
        ]);

        $form = Form::findOrFail($id);

        $form->update([
            'group_id' =>
                $validated['group_id'],

            'no_urut' =>
                $validated['no_urut'],

            'name' =>
                $validated['name'],

            'formtype_id' =>
                $validated['formtype_id'],
        ]);

        return redirect()
            ->route('admin.units', [
                'id' => $form->group_id,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Form berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    

    public function destroy($id)
    {
        $form = Form::findOrFail($id);

        $groupId = $form->group_id;

        try {

            if (Answer::query()->where('form_id', $form->id)->exists()) {
                return redirect()
                    ->route('admin.units', ['id' => $groupId, 'tab' => 'question'])
                    ->with('error', 'Form tidak dapat dihapus karena sudah memiliki jawaban responden.');
            }

            DB::transaction(fn () => $this->deleteFormRelations($form));

            return redirect()
                ->route('admin.units', [
                    'id' => $groupId,
                    'tab' => 'question',
                ])
                ->with(
                    'success',
                    'Form, pertanyaan, dan option berhasil dihapus.'
                );

        } catch (\Throwable $error) {

            report($error);

            return redirect()
                ->route('admin.units', [
                    'id' => $groupId,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Form gagal dihapus karena masih terhubung dengan data lain.'
                );
        }
    }

    public function copy($id)
    {
        $source = Form::query()
            ->with(['questions.options', 'description'])
            ->findOrFail($id);

        $copy = DB::transaction(function () use ($source) {
            $copy = $source->replicate();
            $copy->name = 'Salinan - '.$source->name;
            $copy->no_urut = (int) Form::query()
                ->where('group_id', $source->group_id)
                ->max('no_urut') + 1;
            $copy->save();

            $questionMap = [];

            foreach ($source->questions as $question) {
                $questionCopy = $question->replicate();
                $questionCopy->form_id = $copy->id;
                $questionCopy->save();
                $questionMap[$question->id] = $questionCopy->id;

                foreach ($question->options as $option) {
                    $optionCopy = $option->replicate();
                    $optionCopy->question_id = $questionCopy->id;
                    $optionCopy->save();
                }
            }

            if ($source->description) {
                Description::create([
                    'group_id' => $copy->group_id,
                    'form_id' => $copy->id,
                    'content' => $source->description->content,
                ]);
            }

            Competitor::query()
                ->where('form_id', $source->id)
                ->get()
                ->each(function (Competitor $competitor) use ($copy): void {
                    $competitorCopy = $competitor->replicate();
                    $competitorCopy->form_id = $copy->id;
                    $competitorCopy->save();
                });

            SubUnitQuestion::query()
                ->where('form_id', $source->id)
                ->get()
                ->each(function (SubUnitQuestion $row) use ($copy, $questionMap): void {
                    if (! isset($questionMap[$row->question_id])) {
                        return;
                    }

                    SubUnitQuestion::firstOrCreate([
                        'form_id' => $copy->id,
                        'question_id' => $questionMap[$row->question_id],
                        'subunit_id' => $row->subunit_id,
                    ]);
                });

            return $copy;
        });

        return redirect()->route('admin.units', [
            'id' => $copy->group_id,
            'tab' => 'question',
        ])->with('success', 'Form beserta konfigurasi berhasil disalin.');
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Input Form');
        $sheet->fromArray([
            ['group_id', 'no_urut', 'name', 'formtype_id'],
            ['', '', '', ''],
        ]);
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $master = $spreadsheet->createSheet();
        $master->setTitle('Master Data');
        $master->fromArray([['GROUP ID', 'GROUP', 'FORM TYPE ID', 'FORM TYPE']]);

        $row = 2;
        foreach (Group::query()->orderBy('id')->get(['id', 'name']) as $group) {
            $master->setCellValue("A{$row}", $group->id);
            $master->setCellValue("B{$row}", $group->name);
            $row++;
        }

        $row = 2;
        foreach (FormType::query()->orderBy('id')->get(['id', 'name']) as $type) {
            $master->setCellValue("C{$row}", $type->id);
            $master->setCellValue("D{$row}", $type->name);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            'template_import_forms.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        try {
            $spreadsheet = IOFactory::load($validated['file']->getRealPath());
            $rows = $spreadsheet->getSheet(0)->toArray();
            $headers = array_map(
                fn ($value) => strtolower(trim((string) $value)),
                array_slice($rows[0] ?? [], 0, 4)
            );

            if ($headers !== ['group_id', 'no_urut', 'name', 'formtype_id']) {
                throw ValidationException::withMessages([
                    'file' => 'Judul kolom harus: group_id, no_urut, name, formtype_id.',
                ]);
            }

            $data = [];
            foreach (array_slice($rows, 1) as $index => $row) {
                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $item = [
                    'group_id' => $row[0] ?? null,
                    'no_urut' => $row[1] ?? null,
                    'name' => trim((string) ($row[2] ?? '')),
                    'formtype_id' => $row[3] ?? null,
                ];

                $validator = Validator::make($item, [
                    'group_id' => ['required', 'integer', 'exists:groups,id'],
                    'no_urut' => ['required', 'integer', 'min:1'],
                    'name' => ['required', 'string', 'max:255'],
                    'formtype_id' => ['required', 'integer', 'exists:form_types,id'],
                ]);

                if ($validator->fails()) {
                    throw ValidationException::withMessages([
                        'file' => 'Data form pada baris '.($index + 2).' tidak valid.',
                    ]);
                }

                $data[] = $item;
            }

            if ($data === []) {
                throw ValidationException::withMessages(['file' => 'Tidak ada data form untuk diimport.']);
            }

            DB::transaction(fn () => collect($data)->each(fn ($item) => Form::create($item)));
            $spreadsheet->disconnectWorksheets();

            return back()->with('success', count($data).' form berhasil diimport.');
        } catch (ValidationException $error) {
            throw $error;
        } catch (Throwable $error) {
            report($error);

            return back()->with('error', 'Import form gagal. Periksa kembali format file.');
        }
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:forms,id'],
        ]);

        if (Answer::query()->whereIn('form_id', $validated['ids'])->exists()) {
            return back()->with('error', 'Form terpilih tidak dapat dihapus karena memiliki jawaban responden.');
        }

        DB::transaction(function () use ($validated): void {
            Form::query()
                ->whereIn('id', $validated['ids'])
                ->get()
                ->each(fn (Form $form) => $this->deleteFormRelations($form));
        });

        return back()->with('successdelete', count($validated['ids']).' form berhasil dihapus.');
    }

    private function deleteFormRelations(Form $form): void
    {
        $questionIds = $form->questions()->pluck('id');

        SubUnitQuestion::query()->where('form_id', $form->id)->delete();
        Description::query()->where('form_id', $form->id)->delete();
        Competitor::query()->where('form_id', $form->id)->delete();

        if ($questionIds->isNotEmpty()) {
            Option::query()->whereIn('question_id', $questionIds)->delete();
        }

        Question::query()->where('form_id', $form->id)->delete();
        $form->delete();
    }
}
