<script setup>
import CustomVueComponent from '@/CustomVueComponent.vue';
</script>

<x-component-with-slot>
     <CustomVueComponent>
          <div>{{ $slot }}</div>
     </CustomVueComponent>
</x-component-with-slot>