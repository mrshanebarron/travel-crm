@props([
    'type' => 'view',
    'href' => null,
    'action' => null,
    'confirm' => null,
    'size' => 'md',
    'method' => 'DELETE',
])

@php
    $icons = [
        'view' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
        'edit' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
        'delete' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />',
        'add' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />',
        'complete' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
    ];

    $labels = [
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'add' => 'Add',
        'complete' => 'Complete',
    ];

    $sizes = [
        'xs' => 'text-xs py-0.5 px-1.5',
        'sm' => 'text-xs py-1 px-2',
        'md' => 'text-sm py-1.5 px-3',
        'lg' => 'text-sm py-2 px-4',
    ];

    $iconSizes = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-3 h-3',
        'md' => 'w-4 h-4',
        'lg' => 'w-4 h-4',
    ];

    $icon = $icons[$type] ?? $icons['view'];
    $label = $labels[$type] ?? ucfirst($type);
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $iconSize = $iconSizes[$size] ?? $iconSizes['md'];

    $isDelete = $type === 'delete';
    $isComplete = $type === 'complete';
    $isPrimary = $isComplete;

    $baseClass = $isPrimary
        ? "btn btn-primary {$sizeClass}"
        : "btn btn-secondary {$sizeClass}";

    if ($isDelete) {
        $baseClass .= ' text-red-600 hover:text-red-700';
    }
@endphp

@if($action)
    {{-- Form-based action (for delete, complete, etc.) --}}
    <form method="POST" action="{{ $action }}" class="inline" @if($confirm) onsubmit="return confirm('{{ $confirm }}')" @endif>
        @csrf
        @method($method)
        <button type="submit" {{ $attributes->merge(['class' => $baseClass]) }}>
            <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
            {{ $label }}
        </button>
    </form>
@elseif($href)
    {{-- Link-based action --}}
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClass]) }}>
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
        {{ $label }}
    </a>
@else
    {{-- Button (for modals, JS actions) --}}
    <button type="button" {{ $attributes->merge(['class' => $baseClass]) }}>
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
        {{ $label }}
    </button>
@endif
