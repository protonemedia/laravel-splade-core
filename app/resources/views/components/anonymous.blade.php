<script setup>
const message = ref('Hello Vue!')
const reversed = computed(() => message.value.split('').reverse().join(''))
</script>

<input name="message" type="text" v-model="message" />
<p dusk="reversed" v-text="reversed" />