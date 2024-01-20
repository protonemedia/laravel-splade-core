<script setup>
const emit = defineEmits(['trigger'])

const trigger = () => {
    emit('trigger')
}
</script>

<button @click="trigger">Trigger</button>