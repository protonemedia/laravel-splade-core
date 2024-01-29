<script setup>
    const childVar = ref('Hello from child component');
</script>

<h2>Child component</h2>
<div>
    Hi from {{ 'child' }}
    <h3>Slot <span v-html="childVar" /></h3>
    {{ $slot }}

    {{ $subslot ?? null }}
</div>