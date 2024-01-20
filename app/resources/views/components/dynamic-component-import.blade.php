<script setup>
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from "@headlessui/vue";

const openend = ref(false);

function show() {
    openend.value = true;
}
</script>

<div>
    <button type="button" @click="show">Open Dialog</button>

    <component :is="true ? TransitionRoot : 'div'" :show="openend">
        <component :is="true ? TransitionChild : 'div'">
            <Dialog>
                <DialogPanel>
                    <h2>Dialog</h2>
                </DialogPanel>
            </Dialog>
        </component >
    </component>
</div>