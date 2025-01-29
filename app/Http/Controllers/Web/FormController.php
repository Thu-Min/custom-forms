<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\FormSubmissionMail;
use App\Models\Form;
use App\Models\FormInput;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = Form::where('user_id', auth()->id())->get();

        return view('dashboard', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('forms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'inputs' => 'required|array',
            'inputs.*.label' => 'required|string|max:255',
            'inputs.*.type' => 'required|string|in:text,date,number,select,checkbox',
            'inputs.*.options' => 'nullable|array', // Validate options only if present
            'inputs.*.options.*' => 'required_if:inputs.*.type,select,checkbox|string|max:255', // Options are required for select/checkbox types
        ]);

        $form = Form::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        foreach ($request->inputs as $input) {
            $form->inputs()->create([
                'label' => $input['label'],
                'type' => $input['type'],
                'options' => $input['type'] === 'select' || $input['type'] === 'checkbox'
                    ? json_encode($input['options'] ?? [])
                    : null,
                'required' => isset($input['required']) && $input['required'] === true,
            ]);
        }

        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $form = Form::with('inputs', 'responses')->findOrFail($id);

        return view('forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $form = Form::findOrFail($id);

        $form->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        if ($request->filled('deleted_inputs')) {
            $deletedInputs = explode(',', $request->input('deleted_inputs'));
            FormInput::whereIn('id', $deletedInputs)->delete();
        }

        if ($request->has('inputs')) {
            foreach ($request->input('inputs') as $key => $inputData) {
                if (isset($inputData['label']) && isset($inputData['type'])) {
                    if (is_numeric($key)) {
                        FormInput::where('id', $key)->update([
                            'label' => $inputData['label'],
                            'type' => $inputData['type'],
                        ]);
                    } else {
                        $form->inputs()->create([
                            'label' => $inputData['label'],
                            'type' => $inputData['type'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('forms.show', $form->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $form = Form::findOrFail($id);

        $form->delete();

        return redirect()->route('dashboard');
    }

    public function submit(Request $request, string $id)
    {
        $form = Form::findOrFail($id);

        $request->validate([
            'responses' => 'required|array',
            'responses.*.input_id' => 'required|exists:form_inputs,id',
            'responses.*.response' => 'required',
        ]);

        foreach ($request->responses as $response) {
            FormResponse::create([
                'user_id' => auth()->id(),
                'form_id' => $form->id,
                'form_input_id' => $response['input_id'],
                'response' => is_array($response['response']) ? json_encode($response['response']) : $response['response'],
            ]);
        }

        Mail::to(auth()->user())->send(new FormSubmissionMail());

        return redirect()->route('dashboard');
    }
}
