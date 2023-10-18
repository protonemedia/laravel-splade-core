<script setup>
const form = ref({
    package: 'Splade',
    framework: 'laravel',
    date: '2021-01-01',
})
</script>

<form>
    <input name="package" v-model="form.package" type="text" />
    <x-select name="framework" v-model="form.framework" :options="['laravel', 'vue', 'tailwind']" />
    <x-date-picker name="date" v-model="form.date" />
    <pre v-text="JSON.stringify(form)"></pre>
</form>