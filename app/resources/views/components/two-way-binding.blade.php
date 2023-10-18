<script setup>
const message = ref('Hello Vue!')
const uppercase = computed(() => message.value.toUpperCase())
</script>

<input name="message" type="text" v-model="message" />
<p dusk="uppercase" v-text="uppercase" />