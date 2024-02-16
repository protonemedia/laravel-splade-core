<script setup></script>
<template>
    <div dusk="custom-vue-component">
        <p>This Custom Vue Component</p>

        <div style="background: #d3d3d3; padding: 15px">
            <p>Vue Component's slot:</p>
            <slot></slot>
        </div>
    </div>
</template>
