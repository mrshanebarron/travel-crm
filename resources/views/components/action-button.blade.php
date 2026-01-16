@props([
    'type' => 'view',
    'href' => null,
    'action' => null,
    'confirm' => null,
    'size' => 'md',
    'method' => 'DELETE',
    'label' => null,
    'icon' => true,
    'submit' => false,
])

@php
    $icons = [
        'view' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
        'edit' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
        'delete' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />',
        'add' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />',
        'complete' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
        'cancel' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        'save' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />',
        'filter' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />',
        'clear' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        'upload' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />',
        'import' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />',
        'email' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
        'create' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />',
        'adduser' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />',
        'flight' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />',
        'export' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />',
    ];

    $labels = [
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'add' => 'Add',
        'complete' => 'Complete',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'search' => 'Search',
        'filter' => 'Filter',
        'clear' => 'Clear',
        'upload' => 'Upload',
        'import' => 'Import',
        'email' => 'Email',
        'create' => 'Create',
        'adduser' => 'Add Traveler',
        'flight' => 'Add Flight',
        'export' => 'Export',
    ];

    $sizes = [
        'xs' => 'text-xs py-0.5 px-1.5 gap-1',
        'sm' => 'text-xs py-1 px-2 gap-1',
        'md' => 'text-sm py-1.5 px-3 gap-1.5',
        'lg' => 'text-sm py-2 px-4 gap-2',
    ];

    $iconSizes = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-3 h-3',
        'md' => 'w-4 h-4',
        'lg' => 'w-4 h-4',
    ];

    $iconPath = $icons[$type] ?? $icons['view'];
    $buttonLabel = $label ?? ($labels[$type] ?? ucfirst($type));
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $iconSize = $iconSizes[$size] ?? $iconSizes['md'];

    // Primary actions (main CTA)
    $primaryTypes = ['complete', 'save', 'create', 'search', 'upload', 'add'];
    // Secondary actions (neutral)
    $secondaryTypes = ['cancel', 'clear', 'filter', 'import', 'view', 'export'];
    // Danger actions
    $dangerTypes = ['delete'];
    // Info actions
    $infoTypes = ['edit', 'email', 'adduser', 'flight'];

    $baseClass = "inline-flex items-center font-medium rounded border transition-colors {$sizeClass}";

    if (in_array($type, $dangerTypes)) {
        $baseClass .= ' bg-red-600 border-red-600 text-white hover:bg-red-700';
    } elseif (in_array($type, $primaryTypes)) {
        $baseClass .= ' bg-orange-600 border-orange-600 text-white hover:bg-orange-700';
    } elseif (in_array($type, $infoTypes)) {
        $baseClass .= ' bg-blue-600 border-blue-600 text-white hover:bg-blue-700';
    } else {
        $baseClass .= ' bg-slate-600 border-slate-600 text-white hover:bg-slate-700';
    }
@endphp

@if($action)
    <form method="POST" action="{{ $action }}" class="inline" @if($confirm) onsubmit="return confirm('{{ $confirm }}')" @endif>
        @csrf
        @method($method)
        <button type="submit" {{ $attributes->merge(['class' => $baseClass]) }}>
            @if($icon)<svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconPath !!}</svg>@endif
            {{ $buttonLabel }}
        </button>
    </form>
@elseif($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClass]) }}>
        @if($icon)<svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconPath !!}</svg>@endif
        {{ $buttonLabel }}
    </a>
@elseif($submit)
    <button type="submit" {{ $attributes->merge(['class' => $baseClass]) }}>
        @if($icon)<svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconPath !!}</svg>@endif
        {{ $buttonLabel }}
    </button>
@else
    <button type="button" {{ $attributes->merge(['class' => $baseClass]) }}>
        @if($icon)<svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconPath !!}</svg>@endif
        {{ $buttonLabel }}
    </button>
@endif
