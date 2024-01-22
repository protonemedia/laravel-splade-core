<x-layout>
    <x-emit @trigger="toggle"  />

    <h2 v-if="show">Triggered</h2>
</x-layout>