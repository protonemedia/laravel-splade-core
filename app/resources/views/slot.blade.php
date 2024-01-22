<x-layout>
    <script setup>
        const layoutCounter = ref(0)

        function onIncremented () {
            layoutCounter.value++
        }
    </script>

    <h2>Layout counter: <span v-html="layoutCounter" /></h2>

    <x-root @incremented="onIncremented" />
</x-layout>