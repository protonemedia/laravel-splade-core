<script setup>
    const childVar = ref("What's that, Hawaiian Noises?");
</script>

<h2>Child component</h2>

<div>
    <h3 v-html="childVar" />
    <div dusk="slot">
        {{ $slot }}
        <p v-html="childVar" />
    </div>

    <div dusk="subslot">
        {{ $subslot }}
        <p v-html="childVar" />
    </div>
</div>