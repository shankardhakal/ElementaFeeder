{{--
    This is a custom override to add a "Manage" button to relationship columns.
--}}
@php
    $column['escaped'] = $column['escaped'] ?? true;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['limit'] = $column['limit'] ?? 40;
    $column['attribute'] = $column['attribute'] ?? (new $column['model'])->identifiableAttribute();

    $related_entries = $entry->{$column['name']};

    if ($related_entries instanceof \Illuminate\Support\Collection) {
        $related_entries_count = $related_entries->count();
    } else {
        $related_entries_count = is_array($related_entries) ? count($related_entries) : 0;
    }
@endphp

<span>
    @if($related_entries_count > 0)
        @foreach ($related_entries as $related_entry)
            @php
                $related_entry_text = $column['prefix'].$related_entry->{$column['attribute']}.$column['suffix'];
                $manage_route = route('feed.manage', ['id' => $related_entry->id]);
            @endphp
            <span class="d-inline-flex">
                <a class="btn btn-sm btn-link pr-0" href="{{ backpack_url($column['entity']).'/'.$related_entry->getKey() }}">
                    <i class="la la-search"></i>
                </a>
                <a class="btn btn-sm btn-link pr-0" href="{{ $manage_route }}">
                    <i class="la la-cogs"></i> Manage
                </a>
                <span>{{ Str::limit($related_entry_text, $column['limit'], '[...]') }}</span>
            </span>
            @if(!$loop->last), @endif
        @endforeach
    @else
        -
    @endif
</span>