<x-layout>
    <script setup>
        const toggled = ref(false)
        const toggle = () => toggled.value = !toggled.value
    </script>

    <button @click="toggle">Toggle</button>

    <p v-if="toggled">Parent - Blade Slot - toggled</p>
    <p v-if="!toggled">Parent - Blade Slot - not toggled</p>

    <x-nested-root>
        <p v-if="toggled">Nested root - Blade Slot - toggled</p>
        <p v-if="!toggled">Nested root - Blade Slot - not toggled</p>

        <x-nested-child>
            <p v-if="toggled">Nested child - Blade Slot - toggled</p>
            <p v-if="!toggled">Nested child - Blade Slot - not toggled</p>
        </x-nested-child>
    </x-nested-root>
</x-layout>