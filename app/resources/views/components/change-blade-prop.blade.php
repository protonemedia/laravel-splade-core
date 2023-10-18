<script setup></script>
<div>
    <p>Message: <span v-text="message"></span></p>

    <button type="button" @click="message = 'Hey, Vue!'">Change Message with Vue</button>
    <button type="button" @click="setMessage('Hey, Blade!')">Change Message with Blade</button>
</div>