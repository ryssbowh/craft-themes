<template>
    <div class="modal elementselectormodal modal-viewmode" style="display:none" ref="modal">
        <div class="header">
            <h3>{{ edit ? t('Edit View Mode') : t('Add View Mode') }}</h3>
        </div>
        <div class="body">
            <div class="content">
                <form class="main">
                    <div class="field width-100">
                        <div class="heading">
                            <label class="required" for="name">{{ t('Name') }}</label>
                        </div>
                        <div class="input ltr">
                            <input type="text" id="name" :class="{text: true, fullwidth:true, error: handleError}" v-model="name" maxlength="255" required>
                        </div>
                    </div>
                    <div class="field width-100">
                        <div class="heading">
                            <label class="required" for="handle">{{ t('Handle') }}</label>
                        </div>
                        <div class="input ltr">
                            <input type="text" id="handle" :class="{text: true, fullwidth:true, error: handleError}" :disabled="edit !== null" v-model="handle" maxlength="255" required>
                        </div>
                        <ul class="errors" v-if="handleError">
                            <li>{{ t('This handle is already defined') }}</li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button type="button" class="btn" @click="closeModal">{{ t('Close') }}</button>
                <button type="button" class="btn submit" @click="save">{{ t('Save') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

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
            nameError: false,
            handleError: false,
            handleGenerator: null
        }
    },
    watch: {
        showModal: function () {
            if (this.showModal) {
                if (!this.popup) {
                    this.createModal();
                } else {
                    this.popup.show();
                }
            } else {
                this.popup.hide();
            }
        },
        edit: function (newValue) {
            this.updategenerator();
            this.removeErrors();
            if (newValue !== null) {
                this.name = this.editedViewMode.name;
                this.handle = this.editedViewMode.handle;
            } else {
                this.name = '';
                this.handle = '';
            }
        }
    },
    beforeUnmount () {
        this.popup.destroy();
    },
    methods: {
        createModal: function () {
            this.popup = new Modal(this.$refs.modal, {
                hideOnEsc: false,
                hideOnShadeClick: false
            });
            this.handleGenerator = new Craft.HandleGenerator('.modal-viewmode #name', '.modal-viewmode #handle');
            this.updategenerator();
        },
        updategenerator: function () {
            if (this.edit === null) {
                this.handleGenerator.startListening();
            } else {
                this.handleGenerator.stopListening();
            }
        },
        removeErrors() {
            this.nameError = false;
            this.handleError = false;
        },
        closeModal () {
            this.$emit('closeModal');
        },
        validateModal: function () {
            this.removeErrors();
            if (!this.name.trim()) {
                this.nameError = true;
            }
            if (this.edit === null) {
                if (!this.handle.trim()) {
                    this.handleError = true;
                }
                for (let i in this.viewModes) {
                    if (this.viewModes[i].handle == this.handle.trim()) {
                        this.handleError = true;
                    }
                }
            }
        },
        save() {
            this.validateModal();
            if (this.hasError) {
                return;
            }
            if (this.edit !== null) {
                this.editViewMode({index: this.edit, name: this.name});
            } else {
                this.addViewMode({name: this.name, handle: this.handle});
            }
            this.$emit('closeModal');
        },
        ...mapMutations([]),
        ...mapActions(['addViewMode', 'editViewMode']),
    },
    emits: ['closeModal'],
};
</script>
<style lang="scss" scoped>
input[disabled] {
    background: rgba(230, 230, 230, 0.7);
}
</style>