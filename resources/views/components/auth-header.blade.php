@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center space-x-2">
    <h1 class="text-4xl">
        Ude<span class="font-extrabold text-blue-500">vipo</span>
    </h1>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
