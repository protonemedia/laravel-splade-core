<x-layout>
    <script setup>
        const layoutCounter = ref(0)
    </script>

    <p>Layout Counter: @{{ layoutCounter }}</p>

    <x-root @incremented="layoutCounter++" />
</x-layout>