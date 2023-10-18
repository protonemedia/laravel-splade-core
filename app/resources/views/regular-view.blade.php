<x-layout>
    <script setup>
        const message = ref('Hello World!');
    </script>

    <input v-model="message" />
    <p>The message is: <span v-html="message"></span></p>

    <x-two-way-binding />
</x-layout>