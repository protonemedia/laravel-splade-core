<script setup>
import flatpickr from "flatpickr";

const emit = defineEmits(["update:modelValue"]);

onMounted(() => {
    let instance = flatpickr($refs.date, {
       onChange: (selectedDates, newValue) => {
            emit("update:modelValue", newValue);
        },
    });

    instance.setDate(props.modelValue);
});
</script>

<input ref="date" />