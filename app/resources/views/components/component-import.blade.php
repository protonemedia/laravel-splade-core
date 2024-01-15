<script setup>
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from "@headlessui/vue";

const openend = ref(false);

function show() {
    openend.value = true;
}
</script>

<div>
    <button type="button" @click="show">Open Dialog</button>

    <TransitionRoot :show="openend">
        <TransitionChild>
            <Dialog>
                <DialogPanel>
                    <h2>Dialog</h2>
                </DialogPanel>
            </Dialog>
        </TransitionChild>
    </TransitionRoot>
</div>