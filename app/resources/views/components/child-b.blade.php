<script setup>
import CustomVueComponent from '@/CustomVueComponent.vue';
</script>

<div dusk="child-b">
    <p>This is Child B, rendering a Custom Vue Component</p>

    <div style="background: #f3f3f3; padding: 15px;">
        <p>Child B's slot:</p>

        <CustomVueComponent>
            {{ $slot }}
        </CustomVueComponent>
    </div>
</div>