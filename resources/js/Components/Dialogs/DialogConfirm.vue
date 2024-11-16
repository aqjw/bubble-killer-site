<script setup>
import { ref } from 'vue';

const dialog = ref(false);
const title = ref(null);
const text = ref(null);
const icon = ref(null);
const buttons = ref({});
const callback = ref(() => {});

// Dialog handler function
const openDialog = (options, _callback) => {
  ({
    title: title.value,
    text: text.value,
    icon: icon.value,
    buttons: buttons.value,
  } = options);

  callback.value = _callback;
  dialog.value = true;
};

// Action handler
const onActionClick = (confirm) => {
  callback.value(confirm);
  closeDialog();
};

// Close dialog function
const closeDialog = () => {
  dialog.value = false;
};

defineExpose({
  open: openDialog,
  close: closeDialog,
});
</script>

<template>
  <v-dialog v-model="dialog" max-width="300" :opacity="0.6">
    <v-card :prepend-icon="icon" :title="title" :text="text">
      <template v-slot:actions>
        <v-spacer></v-spacer>
        <v-btn
          @click="() => onActionClick(false)"
          class="text-none"
          v-bind="buttons.cancel"
        >
          Cancel
        </v-btn>

        <v-btn
          @click="() => onActionClick(true)"
          class="text-none"
          v-bind="buttons.confirm"
        >
          Confirm
        </v-btn>
      </template>
    </v-card>
  </v-dialog>
</template>
