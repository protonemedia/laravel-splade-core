<x-layout>
    <script setup>
        const title = ref('Default title')

        function updateTitle() {
            title.value = 'New title'
        }
    </script>

    <x-props-in-template v-bind:title="title" />

    <button @click="updateTitle">Update title</button>
</x-layout>