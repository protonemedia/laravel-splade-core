<script setup>
const response = ref('-')

const executeWithCallback = () => {
    execute(new Date()).then((data) => {
        response.value = data.data.response
    })
}
</script>
<div>
    <p>Blade Method</p>

    <button type="button" @click="execute(new Date())">
        Write time
    </button>

    <button type="button" @click="executeWithCallback">
        Write time with callback
    </button>

    <p>Response: @{{ response }}</p>

    <button type="button" @click="sleep">
        Sleep
    </button>

    <p>Sleeping: @{{ sleep.loading ? 'Yes' : 'No' }}</p>
</div>