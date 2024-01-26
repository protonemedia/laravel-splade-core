<x-layout>
    <script setup>
        const layoutCounter = ref(0)
    </script>

    <p>Layout counter: @{{ layoutCounter }}</p>

    <x-root @incremented="layoutCounter++" />
</x-layout>