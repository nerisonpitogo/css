<div>
    <label>{{ $label }}</label>
    <select wire:model.live="{{ $model }}">
        <option value="">-- Select an option --</option>
        @foreach ($options as $option)
            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
        @endforeach
    </select>
</div>
