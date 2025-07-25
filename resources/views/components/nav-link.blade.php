@props(['active' => false])

<a {{ $attributes->merge([
    'class' => ($active
        ? 'btn btn-active btn-default'
: 'btn btn-ghost text-gray-300 hover:text-white')
]) }}
   aria-current="{{ $active ? 'page' : 'false' }}">
    {{ $slot }}
</a>
