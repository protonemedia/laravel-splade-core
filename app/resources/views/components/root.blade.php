<script setup>
    const count = ref(0)

    function increment() {
        count.value++
    }
</script>

<h2>Parent component</h2>
<p>Count: @{{ count }}</p>

<button @click="increment">Increment</button>

<x-child>
    <p>Count: @{{ count }}</p>
</x-child>