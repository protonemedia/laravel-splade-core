<script setup></script>

<div dusk="child-a">
     <p>This is Child A, rendering Child B:</p>

     <x-child-b>
          <div style="background: #e3e3e3; padding: 15px;">
               <p>Child A's slot:</p>
               {{ $slot }}
          </div>
     </x-child-b>
</div>