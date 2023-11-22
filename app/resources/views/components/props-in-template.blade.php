<script setup>
defineProps({
    title: {
        type: String,
        required: false,
        default: 'Default title'
    }
})

const subtitle = ref('Default subtitle')
</script>

<h2 v-text="title"></h2>
<h3 v-text="subtitle"></h3>