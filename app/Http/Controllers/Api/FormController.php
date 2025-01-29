<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\FormSubmissionMail;
use App\Models\Form;
use App\Models\FormInput;
use App\Models\FormResponse;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Exception;

class FormController extends Controller
{

    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $forms = Form::where('user_id', auth()->id())->get();
            return $this->successResponse($forms, 'Forms retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to fetch forms', 500, $e->getMessage());
        }
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

            return $this->successResponse($form, 'Form created successfully', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation error', 422, $e->errors());
        } catch (Exception $e) {
            return $this->errorResponse('Failed to create form', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $form = Form::with('inputs', 'responses')->findOrFail($id);
            return $this->successResponse($form, 'Form retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Form not found', 404, $e->getMessage());
        }
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
        try {
            $form = Form::findOrFail($id);
            $form->update($request->only(['name', 'description']));

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

            return $this->successResponse($form, 'Form updated successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to update form', 500, $e->getMessage());
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
            return $this->successResponse(null, 'Form deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete form', 500, $e->getMessage());
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

            Mail::to(auth()->user())->send(new FormSubmissionMail());

            return $this->successResponse(null, 'Form submitted successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation error', 422, $e->errors());
        } catch (Exception $e) {
            return $this->errorResponse('Failed to submit form', 500, $e->getMessage());
        }
    }
}
