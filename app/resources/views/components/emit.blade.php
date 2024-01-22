<script setup>
const emit = defineEmits(['trigger'])

const count = ref(0)

const trigger = () => {
    count.value++
    emit('trigger')
}
</script>

<button @click="trigger">Trigger</button>

<p>Times triggered: <span v-html="count"></span></p>