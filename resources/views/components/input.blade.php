@props(['name', 'label', 'type' => 'text'])

<label for="{{ $name }}" class="form-label fw-semibold small">{{ $label }}</label>

@if ($type === 'password')
    <div class="input-group">
        <input type="password" id="{{ $name }}" name="{{ $name }}"
               {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
        <button type="button" class="btn btn-outline-secondary" data-toggle-password="#{{ $name }}"
                aria-label="Tampilkan kata sandi"><i class="bi bi-eye"></i></button>
    </div>
@else
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name) }}"
           {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
@endif

@error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror