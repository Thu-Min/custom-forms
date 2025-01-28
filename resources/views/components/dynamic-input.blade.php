@props(['input'])

<div class="flex flex-col">
    <label class="mb-2 font-medium">{{ $input->label }}</label>
    @switch($input->type)
        @case('text')
        @case('date')
        @case('number')
        @case('email')
            <input type="{{ $input->type }}" name="responses[{{ $input->id }}][response]" class="p-2 border border-gray-300 rounded" required>
            <input type="hidden" name="responses[{{ $input->id }}][input_id]" value="{{ $input->id }}">
            @break

        @case('textarea')
            <textarea name="responses[{{ $input->id }}][response]" class="p-2 border border-gray-300 rounded" required></textarea>
            <input type="hidden" name="responses[{{ $input->id }}][input_id]" value="{{ $input->id }}">
            @break

        @case('select')
            <select name="responses[{{ $input->id }}][response]" class="p-2 border border-gray-300 rounded" required>
                @foreach (json_decode($input->options) as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            <input type="hidden" name="responses[{{ $input->id }}][input_id]" value="{{ $input->id }}">
            @break

        @case('checkbox')
            @foreach (json_decode($input->options) as $option)
                <label class="inline-flex items-center">
                    <input type="checkbox" name="responses[{{ $input->id }}][response][]" value="{{ $option }}" class="p-2 border border-gray-300 rounded">
                    <span class="ml-2">{{ $option }}</span>
                </label>
            @endforeach
            <input type="hidden" name="responses[{{ $input->id }}][input_id]" value="{{ $input->id }}">
            @break

        @case('radio')
            @foreach (json_decode($input->options) as $option)
                <label class="inline-flex items-center">
                    <input type="radio" name="responses[{{ $input->id }}][response]" value="{{ $option }}" class="p-2 border border-gray-300 rounded">
                    <span class="ml-2">{{ $option }}</span>
                </label>
            @endforeach
            <input type="hidden" name="responses[{{ $input->id }}][input_id]" value="{{ $input->id }}">
            @break
    @endswitch
</div>
