<template>
  <div>
    <LoadingView :loading="loading">
      <Head :title="__('Settings')" />
      <RestoreDefaultsModal
        :show="restoreDefaultsModalOpen"
        @confirm="resetConfigDefaults"
        @close="closeRestoreDefaultsModal"
      >
      </RestoreDefaultsModal>

      <form
        class="space-y-8"
        @submit="submitConfigForm"
        :data-form-unique-id="formUniqueId"
        autocomplete="off"
        ref="form"
      >
        <div class="space-y-4">
          <Heading :level="1">{{ __('Settings') }}</Heading>

          <Card
              class="divide-y divide-gray-100 dark:divide-gray-700"
          >
            <component
              v-for="(field, index) in fields"
              :key="index"
              :index="index"
              :is="`form-${field.component}`"
              :errors="validationErrors"
              :field="field"
              :resource-id="'main'"
              :resource-name="'app_config'"
              :form-unique-id="formUniqueId"
              :mode="'form'"
              :show-help-text="true"
            />
          </Card>
        </div>

        <div
          class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 space-x-3"
        >
          <LoadingButton
            dusk="defaults-button"
            component="DangerButton"
            type="button"
            @click="openRestoreDefaultsModal"
            :disabled="isWorking"
            :loading="loading"
          >
            {{ __('Restore Defaults') }}
          </LoadingButton>

          <LoadingButton
            dusk="save-button"
            type="submit"
            :disabled="isWorking"
            :loading="loading"
          >
            {{ __('Save') }}
          </LoadingButton>
        </div>
      </form>
    </LoadingView>
  </div>
</template>

<script>
import each from 'lodash/each'
import tap from 'lodash/tap'
import merge from 'lodash/merge'
import {
  Errors,
} from 'laravel-nova'
import RestoreDefaultsModal from "./RestoreDefaultsModal"

export default {
  components: {
     RestoreDefaultsModal
  },
  data: () => ({
    apiResourceUrl: '/illuminatech/nova-config/config',
    loading: true,
    isWorking: false,
    restoreDefaultsModalOpen: false,
    fields: {},
    validationErrors: new Errors(),
    formUniqueId: 'illuminatech-nova-config-form'
  }),
  created() {
    this.getFields();
  },
  methods: {
    getFields() {
      Nova.request()
        .get(this.apiResourceUrl)
        .then(response => {
          this.fields = response.data.data;
          this.loading = false;
        })
        .catch(error => console.error(error));
    },
    submitConfigForm(e) {
      e.preventDefault();

      this.isWorking = true;

      if (! this.$refs.form.reportValidity()) {
        this.isWorking = false;

        return;
      }

      let formData = tap(new FormData(), formData => {
        each(this.fields, field => {
          field.fill(formData)
        });
      });

      Nova.request()
        .put(this.apiResourceUrl, this.toRawObject(formData)) // use JSON to avoid problems with names containing dots ('.')
        .then(response => {
          this.fields = merge(this.fields, response.data.data);
          this.validationErrors = new Errors();

          Nova.success(
            this.__('The settings have been updated!')
          )

          this.isWorking = false;
        })
        .catch(error => {
          this.isWorking = false;

          if (error.response.status == 422) {
            this.validationErrors = new Errors(error.response.data.errors);
            Nova.error(this.__('There was a problem submitting the form.'));

            return;
          }

          console.error(error);
        });
    },
    resetConfigDefaults() {
      this.isWorking = true;

      Nova.request()
        .delete(this.apiResourceUrl)
        .then(response => {
          // bypass input values cache :
          let originalFields = this.fields;
          this.fields = {};
          setTimeout(function (component, originalFields) {
            component.fields = merge(originalFields, response.data.data);
          }, 100, this, originalFields);

          Nova.success(
            this.__('The settings have been reset to defaults!')
          );

          this.closeRestoreDefaultsModal();
          this.isWorking = false;
        })
        .catch(error => console.error(error));
    },
    openRestoreDefaultsModal() {
      this.restoreDefaultsModalOpen = true;
    },
    closeRestoreDefaultsModal() {
      this.restoreDefaultsModalOpen = false;
    },
    toRawObject(formData) {
      if (Object.fromEntries) {
        return Object.fromEntries(formData);
      }

      let object = {};
      formData.forEach((value, key) => {object[key] = value});

      return object;
    },
  }
}
</script>
