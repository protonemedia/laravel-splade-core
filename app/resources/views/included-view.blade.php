<script setup>
    const message = ref('Hello Included view!');
</script>

<div style="padding: 10px; background: #eee;">
    <input v-model="message" />
    <p>The message is: <span v-html="message"></span></p>

    <x-two-way-binding />
</div>