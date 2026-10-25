{{--
Trường chọn nhiều giá trị: nhập số lượng, hiện đúng bấy nhiêu ô chọn.
Tham số: $field (tên trường, vd 'author_ids'), $label, $options (collection id/name), $selected (mảng id đã chọn)
Gửi lên server dưới dạng mảng $field[]
--}}
@php
    $selected = array_values($selected ?? []);
    $count = max(count($selected), 1);
    $hasError = $errors->has($field) || $errors->has($field . '.*');
@endphp

<div data-select-count="{{ $field }}">
    <label for="{{ $field }}_count" class="form-label">{{ $label }}</label>
    <div class="input-group mb-2">
        <span class="input-group-text">Số lượng</span>
        <input type="number" id="{{ $field }}_count" min="1" max="20" value="{{ $count }}"
               class="form-control" data-count-input>
    </div>

    <div data-rows>
        @for ($i = 0; $i < $count; $i++)
            <select name="{{ $field }}[]" class="form-select mb-2 @if ($hasError) is-invalid @endif">
                <option value="">-- Chọn --</option>
                @foreach ($options as $option)
                    <option value="{{ $option->id }}" @selected(($selected[$i] ?? null) == $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
        @endfor
    </div>

    <template data-row-template>
        <select name="{{ $field }}[]" class="form-select mb-2">
            <option value="">-- Chọn --</option>
            @foreach ($options as $option)
                <option value="{{ $option->id }}">{{ $option->name }}</option>
            @endforeach
        </select>
    </template>

    @if ($hasError)
        <div class="text-danger small">{{ $errors->first($field) ?: $errors->first($field . '.*') }}</div>
    @endif
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-select-count]').forEach((box) => {
                const input = box.querySelector('[data-count-input]');
                const rows = box.querySelector('[data-rows]');
                const template = box.querySelector('[data-row-template]');

                input.addEventListener('input', () => {
                    const target = Math.min(Math.max(parseInt(input.value, 10) || 1, 1), 20);
                    while (rows.children.length < target) {
                        rows.appendChild(template.content.firstElementChild.cloneNode(true));
                    }
                    while (rows.children.length > target) {
                        rows.lastElementChild.remove();
                    }
                });
            });
        });
    </script>
@endonce
