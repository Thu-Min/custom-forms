<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\FormSubmissionMail;
use App\Models\Form;
use App\Models\FormInput;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $forms = Form::where('user_id', auth()->id())->get();
            return view('dashboard', compact('forms'));
        } catch (\Exception $e) {
            Log::error('Error fetching forms: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Unable to fetch forms.');
        }
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
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'inputs' => 'required|array',
                'inputs.*.label' => 'required|string|max:255',
                'inputs.*.type' => 'required|string|in:text,date,number,select,checkbox',
                'inputs.*.options' => 'nullable|array',
                'inputs.*.options.*' => 'required_if:inputs.*.type,select,checkbox|string|max:255',
            ]);

            $form = Form::create([
                'name' => $request->name,
                'description' => $request->description,
                'user_id' => auth()->id(),
            ]);

            $inputs = collect($request->inputs)->map(function ($input) use ($form) {
                return [
                    'form_id' => $form->id,
                    'label' => $input['label'],
                    'type' => $input['type'],
                    'options' => in_array($input['type'], ['select', 'checkbox'])
                        ? json_encode($input['options'] ?? [])
                        : null,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            FormInput::insert($inputs);

            return redirect()->route('dashboard');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error storing form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Unable to store form.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $form = Form::with('inputs', 'responses')->findOrFail($id);
            return view('forms.show', compact('form'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('dashboard')->withErrors('Form not found.');
        } catch (\Exception $e) {
            Log::error('Error displaying form: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Unable to display form.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Implementation here
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
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
                    FormInput::updateOrCreate(
                        ['id' => is_numeric($key) ? $key : null, 'form_id' => $form->id],
                        [
                            'label' => $inputData['label'],
                            'type' => $inputData['type'],
                        ]
                    );
                }
            }

            return redirect()->route('forms.show', $form->id);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('dashboard')->withErrors('Form not found.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Unable to update form.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $form = Form::findOrFail($id);
            $form->delete();
            return redirect()->route('dashboard');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('dashboard')->withErrors('Form not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting form: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Unable to delete form.');
        }
    }

    public function submit(Request $request, string $id)
    {
        try {
            $form = Form::findOrFail($id);

            $request->validate([
                'responses' => 'required|array',
                'responses.*.input_id' => 'required|exists:form_inputs,id',
                'responses.*.response' => 'required',
            ]);

            $responses = collect($request->responses)->map(function ($response) use ($form) {
                return [
                    'user_id' => auth()->id() ?? null,
                    'form_id' => $form->id,
                    'form_input_id' => $response['input_id'],
                    'response' => is_array($response['response']) ? json_encode($response['response']) : $response['response'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            FormResponse::insert($responses);

            if (auth()->user()) {
                Mail::to(auth()->user())->send(new FormSubmissionMail());
            }

            return view('forms.thank');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('dashboard')->withErrors('Form not found.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error submitting form: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Unable to submit form.');
        }
    }
}
