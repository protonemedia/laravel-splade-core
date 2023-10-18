
<x-layout>
    <x-time dusk="parent">
        <x-slot:button>Refresh Parent</x-slot:button>

        <x-time dusk="child">
            <x-slot:button>Refresh Child</x-slot:button>
        </x-time>
    </x-time>
</x-layout>