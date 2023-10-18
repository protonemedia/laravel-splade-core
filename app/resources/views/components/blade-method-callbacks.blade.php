<script setup>
const response = ref('-')

execute.before((data) => {
    response.value = 'waiting...'
})

execute.then((data) => {
    response.value = 'yes!'
})

fail.catch((data) => {
    response.value = 'no!'
})
</script>
<div>
    <button type="button" @click="execute">
        Execute
    </button>

    <button type="button" @click="fail">
        Fail
    </button>

    <p>Response: @{{ response }}</p>
</div>