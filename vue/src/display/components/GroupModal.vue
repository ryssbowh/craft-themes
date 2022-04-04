<template>
    <div
        ref="modal"
        class="modal elementselectormodal modal-group"
        style="display:none"
    >
        <div class="header">
            <h3>{{ editedGroupUid ? t('Edit group') : t('Add group') }}</h3>
        </div>
        <div class="body">
            <div class="content">
                <form @submit.prevent="save">
                    <div class="field">
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
                                type="text"
                                :class="{text: true, fullwidth:true, error: nameError}"
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
                    <div class="field">
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
                    @click="save"
                >
                    {{ t('Save', {}, 'app') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';
import { v4 as uuidv4 } from 'uuid';
import { HandleGenerator } from '../../Helpers';

export default {
    emits: ['closeModal'],
    data() {
        return {
            modal: null,
            name: '',
            handle: '',
            nameError: false,
            handleError: false,
            handleGenerator: null
        }
    },
    computed: {
        editedGroup: function () {
            for (let i in this.groups) {
                if (this.groups[i].uid == this.editedGroupUid) {
                    return this.groups[i];
                }
            }
            return null;
        },
        displays: function () {
            return this.viewMode.displays;
        },
        groups: function () {
            return this.displays.filter((d) => d.type == 'group');
        },
        hasError: function () {
            return this.handleError || this.nameError;
        },
        maxOrder: function () {
            return this.displays[this.displays.length - 1].order ?? 0;
        },
        ...mapState(['layout', 'showGroupModal', 'editedGroupUid', 'viewMode'])
    },
    watch: {
        showGroupModal: function () {
            if (this.showGroupModal) {
                this.modal.show();
            } else {
                this.modal.hide();
            }
            this.updategenerator();
            if (this.editedGroupUid) {
                this.name = this.editedGroup.item.name;
                this.handle = this.editedGroup.item.handle;
            }
        }
    },
    mounted() {
        this.createModal();
    },
    beforeUnmount () {
        this.modal.destroy();
    },
    methods: {
        createModal: function () {
            this.modal = new Garnish.Modal(this.$refs.modal, {
                hideOnEsc: false,
                hideOnShadeClick: false,
                autoShow: false
            });
            this.handleGenerator = new HandleGenerator('.modal-group #name', '.modal-group #handle');
            this.handleGenerator.callback = (value) => {
                this.handle = value;
            };
            this.updategenerator();
        },
        updategenerator: function () {
            if (this.editedGroupUid === null) {
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
            this.name = '';
            this.handle = '';
            this.removeErrors();
        },
        validateModal: function () {
            this.removeErrors();
            if (!this.name.trim()) {
                this.nameError = this.t('Name is required');
            }
            if (!this.handle.trim()) {
                this.handleError = this.t('Handle is required');
            }
            for (let i in this.displays) {
                if (this.displays[i].uid != this.editedGroupUid && this.displays[i].item.handle == this.handle.trim()) {
                    this.handleError = this.t('This handle is already defined');
                    return;
                }
                if (this.displays[i].type == 'group') {
                    for (let j in this.displays[i].item.displays) {
                        if (this.displays[i].item.displays[j].item.handle == this.handle.trim()) {
                            this.handleError = this.t('This handle is already defined');
                            return;           
                        }
                    }
                }
            }
        },
        save() {
            this.validateModal();
            if (this.hasError) {
                return;
            }
            if (this.editedGroupUid !== null) {
                this.updateItem({displayUid: this.editedGroup.uid, data: {name: this.name, handle: this.handle}});
            } else {
                this.addDisplay({
                    id: null,
                    type: 'group',
                    uid: uuidv4(),
                    order: this.maxOrder + 1,
                    item: {
                        id: null,
                        uid: uuidv4(),
                        name: this.name, 
                        handle: this.handle,
                        visuallyHidden: false,
                        hidden: false,
                        labelHidden: false,
                        labelVisuallyHidden: false,
                        displays: []
                    }
                });
            }
            this.closeModal();
        },
        ...mapMutations(['updateItem', 'addDisplay'])
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