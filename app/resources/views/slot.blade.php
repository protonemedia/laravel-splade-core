<x-layout>
    <script setup>
        const layoutCounter = ref(0)
    </script>

    <p>This is the base view, rendering Root component (Base View Counter: @{{ layoutCounter }})</p>

    <x-root @incremented="layoutCounter++" />
</x-layout>