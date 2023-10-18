<script setup>
const message = ref('Hello World')
</script>

<input type="name" v-model="message" />
<p>The message is: <span v-text="message" /></p>

