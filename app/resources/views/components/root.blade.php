<script setup>
    const rootCounter = ref(0)

    const emit = defineEmits(['incremented'])

    function increment() {
        rootCounter.value++
        emit('incremented')
    }
</script>

<p>This is Root Component, rendering Child Component (Root Counter: @{{ rootCounter }})</p>
<button @click="increment">Increment</button>

<x-child>
    <div style="background: #f3f3f3; padding: 15px;">
        <p>Root Counter from default slot: @{{ rootCounter }}</p>
    </div>

    <x-slot name="subslot">
        <div style="background: #c3c3c3; padding: 15px;">
            <p>Root Counter from sub-slot: @{{ rootCounter }}</p>
        </div>
    </x-slot>
</x-child>