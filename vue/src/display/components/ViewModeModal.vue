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
                            <input type="text" id="name" class="text fullwidth" name="name" :value="edit ? editedViewMode.name : ''" maxlength="255" required>
                        </div>
                    </div>
                    <div class="field width-100">
                        <div class="heading">
                            <label class="required" for="handle">{{ t('Handle') }}</label>
                        </div>
                        <div class="input ltr">
                            <input type="text" id="handle" class="text fullwidth" name="handle" :disabled="edit !== null" :value="editedViewMode ? editedViewMode.handle : ''" maxlength="255" required>
                        </div>
                        <ul class="errors" v-if="hasError">
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
import Mixin from '../../mixin';
import Modal from '../modal';

export default {
    computed: {
        editedViewMode: function () {
            if (this.edit === null) {
                return null;
            }
            return this.viewModes[this.edit];
        },
        ...mapState(['viewModes'])
    },
    props: {
        showModal: Boolean,
        edit: null
    },
    data() {
        return {
            popup: null,
            hasError: false,
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
        edit: function () {
            this.updategenerator();
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
        closeModal () {
            this.$emit('closeModal');
        },
        validateModal: function () {
            let res = true;
            this.hasError = false;
            let handle = $(this.$refs.modal).find('#handle');
            let name = $(this.$refs.modal).find('#name');
            handle.removeClass('error');
            name.removeClass('error');
            if (!handle.val().trim()) {
                handle.addClass('error');
                res = false;
            }
            if (!name.val().trim()) {
                name.addClass('error');
                res = false;
            }
            if (this.edit === null) {
                for (let i in this.viewModes) {
                    if (this.viewModes[i].handle == handle.val().trim()) {
                        this.hasError = true;
                        res = false;
                    }
                }
            }
            return res;
        },
        save() {
            if (!this.validateModal()) {
                return;
            }
            let handle = $(this.$refs.modal).find('#handle').val();
            let name = $(this.$refs.modal).find('#name').val();
            if (this.edit !== null) {
                this.editViewMode({index: this.edit, name: name});
            } else {
                this.addViewMode({name: name, handle: handle});
            }
            // $(this.$refs.modal).find('#handle').val('');
            // $(this.$refs.modal).find('#name').val('');
            this.$emit('closeModal');
        },
        ...mapMutations([]),
        ...mapActions(['addViewMode', 'editViewMode']),
    },
    emits: ['closeModal'],
    mixins: [Mixin],
};
</script>
<style lang="scss" scoped>
input[disabled] {
    background: rgba(230, 230, 230, 0.7);
}
</style>