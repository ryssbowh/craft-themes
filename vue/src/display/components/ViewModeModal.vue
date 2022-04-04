<template>
    <div
        ref="modal"
        class="modal elementselectormodal modal-viewmode"
        style="display:none"
    >
        <div class="header">
            <h3>{{ editedViewMode ? t('Edit view mode') : t('Add view mode') }}</h3>
        </div>
        <div class="body">
            <div class="content">
                <form @submit.prevent="save">
                    <div class="field width-100">
                        <div class="heading">
                            <label
                                class="required"
                                for="name"
                            >
                                {{ t('Name', {}, 'app') }}
                            </label>
                        </div>
                        <div class="input ltr">
                            <input 
                                id="name"
                                v-model="name"
                                :class="{text: true, fullwidth:true, error: nameError}"
                                type="text"
                                maxlength="255"
                                required
                            >
                        </div>
                        <ul
                            v-if="nameError"
                            class="errors"
                        >
                            <li>{{ nameError }}</li>
                        </ul>
                    </div>
                    <div class="field width-100">
                        <div class="heading">
                            <label
                                class="required"
                                for="handle"
                            >
                                {{ t('Handle', {}, 'app') }}
                            </label>
                        </div>
                        <div class="input ltr">
                            <input
                                id="handle"
                                v-model="handle"
                                type="text"
                                :class="{text: true, fullwidth:true, error: handleError}"
                                :disabled="mode == 'edit' && editedViewMode.handle == 'default'"
                                maxlength="255"
                                required
                            >
                        </div>
                        <ul
                            v-if="handleError"
                            class="errors"
                        >
                            <li>{{ handleError }}</li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button 
                    type="button" 
                    class="btn"
                    @click="closeModal"
                >
                    {{ t('Close', {}, 'app') }}
                </button>
                <button 
                    type="button"
                    class="btn submit"
                    @click.prevent="save"
                >
                    {{ t('Save', {}, 'app') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import { HandleGenerator } from '../../Helpers';

export default {
    props: {
        showModal: Boolean,
        editedViewMode: {
            type: Object,
            default: null
        }
    },
    emits: ['closeModal'],
    data() {
        return {
            popup: null,
            name: '',
            handle: '',
            nameError: '',
            handleError: '',
            handleGenerator: null
        }
    },
    computed: {
        mode: function () {
            return (this.editedViewMode === null ? 'add' : 'edit');
        },
        hasError: function () {
            return this.handleError || this.nameError;
        },
        ...mapState(['viewModes', 'layout'])
    },
    watch: {
        showModal: function () {
            if (this.showModal) {
                this.updateGenerator();
                if (this.mode == 'edit') {
                    this.name = this.editedViewMode.name;
                    this.handle = this.editedViewMode.handle;
                }
                this.popup.show();
            } else {
                this.popup.hide();
            }
        }
    },
    mounted() {
        this.createModal();
    },
    beforeUnmount () {
        this.popup.destroy();
    },
    methods: {
        createModal: function () {
            this.popup = new Garnish.Modal(this.$refs.modal, {
                hideOnEsc: false,
                hideOnShadeClick: false,
                autoShow: false
            });
            this.handleGenerator = new HandleGenerator('.modal-viewmode #name', '.modal-viewmode #handle');
            this.handleGenerator.callback = (value) => {
                this.handle = value;
            };
            this.updateGenerator();
        },
        updateGenerator: function () {
            if (this.mode == 'add') {
                this.handleGenerator.startListening();
            } else {
                this.handleGenerator.stopListening();
            }
        },
        removeErrors() {
            this.nameError = '';
            this.handleError = '';
        },
        closeModal () {
            this.$emit('closeModal');
            this.name = '';
            this.handle = '';
            this.removeErrors();
        },
        validateModal: function () {
            this.removeErrors();
            this.name = this.name.trim();
            this.handle = this.handle.trim();
            if (!this.name) {
                this.nameError = this.t('Name is required');
            }
            if (this.editedViewMode === null) {
                if (!this.handle) {
                    this.handleError = this.t('Handle is required');
                }
                for (let i in this.viewModes) {
                    if (this.viewModes[i].handle == this.handle) {
                        this.handleError = this.t('This handle is already defined');
                    }
                }
            }
        },
        save() {
            this.validateModal();
            if (this.hasError) {
                return;
            }
            if (this.mode == 'edit') {
                this.editViewMode({originalHandle: this.editedViewMode.handle, name: this.name, handle: this.handle});
            } else {
                this.addViewMode({name: this.name, handle: this.handle});
            }
            this.closeModal();
        },
        ...mapActions(['addViewMode', 'editViewMode']),
    }
};
</script>
<style lang="scss" scoped>
input[disabled] {
    background: rgba(230, 230, 230, 0.7);
}
.modal {
    padding-bottom: 62px;
    max-height: 307px !important;
    max-width: 300px !important;
    min-height: unset !important;
    min-width: unset !important;
    .body {
        height: calc(100% - 65px);
    }
}
</style>