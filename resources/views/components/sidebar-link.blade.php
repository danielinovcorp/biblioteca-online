@props(['active' => false, 'icon'])

<a {{ $attributes->merge([
    'class' => 'text-3xl group',
    'title' => $attributes['title'] ?? ''
]) }}>
    <i class="{{ $icon }}
        {{ $active
            ? 'bg-black/50 text-white rounded'
            : 'text-gray-400 group-hover:text-white group-hover:bg-black/50 group-hover:rounded' }}
        w-14 h-14 flex items-center justify-center transition-all duration-200">
    </i>
</a>