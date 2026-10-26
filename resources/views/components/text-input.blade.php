@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border-ink/15 bg-white text-ink placeholder:text-ink/40 focus:border-primary focus:ring-primary rounded-lg shadow-sm']) }}>
