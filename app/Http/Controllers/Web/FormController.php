<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = Form::all();

        return view('forms.index', compact('forms'));
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

        // Store the form
        $form = Form::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        // Store inputs
        foreach ($request->inputs as $input) {
            $form->inputs()->create([
                'label' => $input['label'],
                'type' => $input['type'],
                'options' => $input['type'] === 'select' || $input['type'] === 'checkbox'
                    ? json_encode($input['options'] ?? [])
                    : null,
                'required' => isset($input['required']) && $input['required'] === 'on',
            ]);
        }

        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $form = Form::with('inputs')->findOrFail($id);

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
        //
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
                'form_id' => $form->id,
                'form_input_id' => $response['input_id'],
                'response' => is_array($response['response']) ? json_encode($response['response']) : $response['response'],
            ]);
        }

        return redirect()->route('dashboard');
    }
}
