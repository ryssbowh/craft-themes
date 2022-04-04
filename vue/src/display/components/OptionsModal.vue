<template>
    <div
        ref="modal"
        :class="'modal elementselectormodal themes-modal-options displayer-' + displayer.handle"
        style="display:none"
    >
        <div class="header">
            <h3>{{ t('Edit displayer options') }}</h3>
            <i
                v-if="displayer.description"
                class="description"
            >
                {{ displayer.description }}
            </i>
        </div>
        <div class="body">
            <div class="content">
                <form
                    class="main"
                    @submit.prevent="save"
                >
                    <component
                        :is="formFieldComponent(definition.field)"
                        v-for="definition, name in displayer.options.definitions"
                        :key="name"
                        :name="name"
                        :definition="definition"
                        :value="options[name] ?? null"
                        :errors="errors[name] ?? []"
                        @change="updateOption(name, $event)"
                    />
                </form>
            </div>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button
                    v-if="!displayerHasChanged"
                    type="button"
                    class="btn"
                    @click="closeModal"
                >
                    {{ t('Close', {}, 'app') }}
                </button>
                <button
                    type="button"
                    class="btn submit"
                    @click="save"
                >
                    {{ t('Save', {}, 'app') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { merge } from 'lodash';

export default {
    props: {
        displayer: {
            type: Object,
            default: null
        },
        item: {
            type: Object,
            default: null
        },
        displayerHasChanged: Boolean
    },
    emits: ['onSave', 'onHide'],
    data() {
        return {
            modal: null,
            options: {},
            errors: {}
        }
    },
    computed: {
        
    },
    created() {
        //we need then to reset the options to the displayer's defaults if it has changed
        if (this.displayerHasChanged) {
            this.options = merge({}, this.displayer.options.defaultValues);
        } else {
            this.options = merge({}, this.item.options);
        }
        this.errors = this.item.errors.displayer ?? {};
    },
    beforeUnmount () {
        this.modal.destroy();
    },
    mounted: function () {
        this.modal = new Garnish.Modal(this.$refs.modal, {
            hideOnEsc: false,
            hideOnShadeClick: false,
            onHide: () => {
                this.$emit('onHide');
            }
        });
    },
    methods: {
        formFieldComponent (field) {
            return 'formfield-' + field;
        },
        closeModal () {
            this.options = {};
            this.errors = {};
            this.modal.hide();
        },
        updateOption (name, value) {
            this.options[name] = value;
        },
        save () {
            let data = {
                fieldId: this.item.id,
                displayer: this.displayer.handle,
                options: this.options
            };
            axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/validate-field-options'),
                data: data,
                headers: {'X-CSRF-Token': Craft.csrfTokenValue}
            }).then((response) => {
                if (Object.keys(response.data.errors).length > 0) {
                    this.errors = response.data.errors;
                } else {
                    this.$emit('onSave', this.options);
                    this.closeModal();
                }
            }).catch((err) => {
                this.handleError(err);
            });
        }
    }
};
</script>