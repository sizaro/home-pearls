@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl">Home Pearls</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
