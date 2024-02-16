<script setup>
    const childVar = ref("What's that, Hawaiian Noises?");
</script>

<p>This is Child component</p>

<div style="background: #e3e3e3; padding: 15px;">
    <div>
        <h3 v-html="childVar" />

        <div dusk="slot" style="background: #d3d3d3; padding: 15px;">
            <p>Child's slot:</p>
            {{ $slot }}
            <p>Render ref from child: <span v-html="childVar" /></p>
        </div>

        <div dusk="subslot" style="background: #c3c3c3; padding: 15px;">
            <p>Child's sub-slot:</p>
            {{ $subslot }}
            <p>Render ref from child: <span v-html="childVar" /></p>
        </div>
    </div>
</div>