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
                            <input type="text" id="handle" :class="{text: true, fullwidth:true, error: handleError}" :disabled="edit !== null" v-model="handle" maxlength="255" required>
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
import { mapMutations, mapState, mapActions } from 'vuex';
import HandleGenerator from '../../HandleGenerator'

export default {
    computed: {
        editedViewMode: function () {
            if (this.edit === null) {
                return null;
            }
            return this.viewModes[this.edit];
        },
        hasError: function () {
            return this.handleError || this.nameError;
        },
        ...mapState(['viewModes', 'layout'])
    },
    props: {
        showModal: Boolean,
        edit: null
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
                this.popup.show();
            } else {
                this.popup.hide();
            }
        },
        edit: function (newValue) {
            this.updateGenerator();
            if (newValue !== null) {
                this.name = this.editedViewMode.name;
                this.handle = this.editedViewMode.handle;
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
            if (this.editedViewMode === null) {
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
            if (this.editedViewMode !== null) {
                this.editViewMode({index: this.edit, name: this.name});
            } else {
                this.addViewMode({name: this.name, handle: this.handle});
            }
            this.$emit('closeModal');
        },
        ...mapMutations(['editViewMode']),
        ...mapActions(['addViewMode']),
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