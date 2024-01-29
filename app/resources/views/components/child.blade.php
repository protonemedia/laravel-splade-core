<script setup>
    const childVar = ref('Hello from child component');
</script>

<h2>Child component</h2>
<div>
    --DEFAULT SLOT--
    Hi from {{ 'child' }}
    <h3>Slot <span v-html="childVar" /></h3>
    {{ $slot }}
    --SUBSLOT--
    What's that, Hawaiian Noises?
    {{ $subslot ?? null }}
    --SUBSLOT END--
    Alright
</div>