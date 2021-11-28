<template>
    <div :class="'modal elementselectormodal options-modal displayer-' + displayer.handle" style="display:none" ref="modal">
        <div class="header">
            <h3>{{ t('Edit displayer options') }}</h3>
        </div>
        <div class="body">
            <div class="content">
                <form class="main" @submit.prevent="save">
                    <component v-for="definition, name in displayer.options.definitions" :name="name" :is="formFieldComponent(definition.field)" :definition="definition" :value="options[name] ?? null" :errors="errors[name] ?? []" @change="updateOption(name, $event)" :key="name"></component>
                </form>
            </div>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button type="button" class="btn" @click="closeModal" v-if="!displayerHasChanged">{{ t('Close', {}, 'app') }}</button>
                <button type="button" class="btn submit" @click="save">{{ t('Save', {}, 'app') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { merge } from 'lodash';

export default {
    computed: {
        
    },
    props: {
        displayer: Object,
        item: Object,
        displayerHasChanged: Boolean
    },
    data() {
        return {
            modal: null,
            options: {},
            errors: {}
        }
    },
    created() {
        //we need then to reset the options to the displayer's defaults if it has changed
        if (this.displayerHasChanged) {
            this.options = merge({}, this.displayer.options.defaultValues);
        } else {
            this.options = merge({}, this.item.options);
        }
        this.errors = this.item.errors ?? {};
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
    },
    emits: ['onSave', 'onHide'],
};
</script>
<style lang="scss" scoped>
.options-modal {
    padding-bottom: 62px;
    min-width: 300px;
    min-height: 300px;
    height: 60vh;
    width: 30%;
    &.displayer-asset_render_file, &.displayer-file_default {
        width: 60%;
        height: 80vh;
    }
    .body {
        height: calc(100% - 65px);
    }
}
</style>