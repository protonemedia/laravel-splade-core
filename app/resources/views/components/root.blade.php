<script setup>
    const rootCounter = ref(0)

    const emit = defineEmits(['incremented'])

    function increment() {
        rootCounter.value++
        emit('incremented')
    }
</script>

<h2>Parent component</h2>
<p>Root Counter: @{{ rootCounter }}</p>

<button @click="increment">Increment</button>

<x-child>
    <p>Root Counter from default slot: @{{ rootCounter }}</p>

    <x-slot name="subslot">
        <p>Root Counter from sub-slot: @{{ rootCounter }}</p>
    </x-slot>
</x-child>