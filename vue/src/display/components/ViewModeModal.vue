<template>
    <div class="modal elementselectormodal modal-viewmode" style="display:none" ref="modal">
        <div class="header">
            <h3>{{ editedViewMode ? t('Edit View Mode') : t('Add View Mode') }}</h3>
        </div>
        <div class="body">
            <div class="content">
                <form class="main">
                    <div class="field width-100">
                        <div class="heading">
                            <label class="required" for="name">{{ t('Name', {}, 'app') }}</label>
                        </div>
                        <div class="input ltr">
                            <input type="text" id="name" :class="{text: true, fullwidth:true, error: nameError}" v-model="name" maxlength="255" required>
                        </div>
                        <ul class="errors" v-if="nameError">
                            <li>{{ nameError }}</li>
                        </ul>
                    </div>
                    <div class="field width-100">
                        <div class="heading">
                            <label class="required" for="handle">{{ t('Handle', {}, 'app') }}</label>
                        </div>
                        <div class="input ltr">
                            <input type="text" id="handle" :class="{text: true, fullwidth:true, error: handleError}" :disabled="mode == 'edit' && editedViewMode.handle == 'default'" v-model="handle" maxlength="255" required>
                        </div>
                        <ul class="errors" v-if="handleError">
                            <li>{{ handleError }}</li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button type="button" class="btn" @click="closeModal">{{ t('Close', {}, 'app') }}</button>
                <button type="button" class="btn submit" @click.prevent="save">{{ t('Save', {}, 'app') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import HandleGenerator from '../../HandleGenerator'

export default {
    computed: {
        mode: function () {
            return (this.editedViewMode === null ? 'add' : 'edit');
        },
        hasError: function () {
            return this.handleError || this.nameError;
        },
        ...mapState(['viewModes', 'layout'])
    },
    props: {
        showModal: Boolean,
        editedViewMode: null
    },
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
    },
    emits: ['closeModal'],
};
</script>
<style lang="scss" scoped>
input[disabled] {
    background: rgba(230, 230, 230, 0.7);
}
.modal {
    padding-bottom: 62px;
    min-width: 300px;
    min-height: 300px;
    height: 307px;
    width: 300px;
    .body {
        height: calc(100% - 65px);
    }
}
</style>