<template>
    <div>
        <loading-view :loading="loading">
            <portal
                to="modals"
                v-if="restoreDefaultsModalOpen"
            >
                <restore-defaults-modal
                    @confirm="resetConfigDefaults"
                    @close="closeRestoreDefaultsModal"
                >
                </restore-defaults-modal>
            </portal>

            <form
                @submit="submitConfigForm"
                autocomplete="off"
                ref="form"
            >
                <div class="mb-8">
                    <heading class="mb-6">{{ __('Settings') }}</heading>

                    <card>
                    <component
                        :class="{
                          'remove-bottom-border': index == fields.length - 1,
                        }"
                        v-for="(field, index) in fields"
                        :key="index"
                        :is="`form-${field.component}`"
                        :errors="validationErrors"
                        :field="field"
                    />
                    </card>
                </div>

                <div class="flex items-center">
                    <div class="ml-auto"></div>
                    <button
                        type="button"
                        class="btn btn-default btn-danger inline-flex items-center relative mr-3"
                        dusk="defaults-button"
                        @click="openRestoreDefaultsModal"
                        :disabled="isWorking"
                    >
                        <span class="">{{ __('Restore Defaults') }}</span>
                    </button>

                    <progress-button
                        dusk="save-button"
                        type="submit"
                        :disabled="isWorking"
                    >
                        {{ __('Save') }}
                    </progress-button>
                </div>
            </form>
        </loading-view>
    </div>
</template>

<script>
import {
    Errors,
} from 'laravel-nova'
import RestoreDefaultsModal from "./RestoreDefaultsModal";

export default {
    components: {
        RestoreDefaultsModal
    },
    data: () => ({
        apiResourceUrl: '/illuminatech/nova-config/config',
        loading: true,
        isWorking: false,
        restoreDefaultsModalOpen: false,
        fields: [],
        validationErrors: new Errors(),
    }),
    created() {
        this.getFields();
    },
    methods: {
        getFields() {
            Nova.request()
                .get(this.apiResourceUrl)
                .then(response => {
                    this.fields = response.data;
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

            let formData = _.tap(new FormData(), formData => {
                _.each(this.fields, field => {
                    field.fill(formData)
                });
            });

            Nova.request()
                .put(this.apiResourceUrl, this.toRawObject(formData)) // use JSON to avoid problems with names containing dots ('.')
                .then(response => {
                    this.fields = response.data;
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
                    this.fields = [];
                    setTimeout(function (component) {
                        component.fields = response.data;
                    }, 100, this);

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
