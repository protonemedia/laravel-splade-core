<script setup>
const status = ref('idle');

refreshComponent.before(() => {
    status.value = 'loading';
});

refreshComponent.then(() => {
    console.log('then refreshed');
});

refreshComponent.finally(() => {
    console.log('finally refreshed');
});
</script>
<div>
    <p dusk="time">Time: {{ now() }}</p>

    <button type="button" @click="refreshComponent">
        Refresh
    </button>

    <p>Is refreshing: @{{ refreshComponent.loading ? 'Yes' : 'No' }}</p>

    <p>Status: @{{ status }}</p>
</div>
