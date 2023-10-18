<script setup>
</script>

<div>
    <p v-bind="$attrs">Time: {{ now() }}</p>

    <button type="button" @click="refreshComponent">
        {{ $button }}
    </button>

    {{ $slot }}
</div>
