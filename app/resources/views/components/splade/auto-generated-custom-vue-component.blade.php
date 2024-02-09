<script setup>
    import CustomVueComponent from '@/CustomVueComponent.vue';
</script>

<CustomVueComponent>
    {{ $slot }}
</CustomVueComponent>