<x-layout>
    <script setup>
        const show = ref(false)

        function toggle() {
            show.value = !show.value
        }
    </script>

    <x-emit @trigger="toggle"  />

    <h2 v-if="show">Triggered</h2>
</x-layout>