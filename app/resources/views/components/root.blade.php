<script setup>
    const count = ref(0)

    const emit = defineEmits(['incremented'])

    function increment() {
        count.value++
        emit('incremented')
    }
</script>

<h2>Parent component</h2>
<p>Count: @{{ count }}</p>

<button @click="increment">Increment</button>

<x-child>
    Child Slot from Parent Component (inside <-x-child>)
    Hi {{ 'nerd' }}
    <p>Count: @{{ count }}</p>

    <x-slot name="subslot">
        Hi again from Parent
        <p>Count: @{{ count }}</p>
    </x-slot>
</x-child>