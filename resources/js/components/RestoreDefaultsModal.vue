<template>
  <Modal data-testid="config-restore-defaults-modal"
    :show="show"
    role="alertdialog"
    size="sm"
  >
    <form
      @submit.prevent="handleConfirm"
      class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
    >
      <slot>
        <ModalHeader v-text="__('Restore Defaults')" />
        <ModalContent>
          <p class="leading-normal">
            {{ __('Are you sure you want to restore default values?') }}
          </p>
        </ModalContent>
      </slot>

      <ModalFooter>
        <div class="ml-auto">
          <LinkButton
            type="button"
            data-testid="cancel-button"
            dusk="cancel-delete-button"
            @click.prevent="handleClose"
            class="mr-3"
          >
            {{ __('Cancel') }}
          </LinkButton>

          <LoadingButton
            ref="confirmButton"
            dusk="confirm-delete-button"
            :processing="working"
            :disabled="working"
            component="DangerButton"
            type="submit"
          >
            {{ __('Restore Defaults') }}
          </LoadingButton>
        </div>
      </ModalFooter>
    </form>
  </Modal>
</template>

<script>
export default {
  emits: ['confirm', 'close'],

  props: {
    show: { type: Boolean, default: false },
  },

  data: () => ({
    working: false,
  }),

  methods: {
    handleClose() {
      this.$emit('close')
      this.working = false
    },

    handleConfirm() {
      this.$emit('confirm')
      this.working = false
    },
  },

  /**
   * Mount the component.
   */
  mounted() {
    //this.$refs.confirmButton.focus()
  },
}
</script>
