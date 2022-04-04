<template>
    <div
        ref="modal"
        class="modal elementselectormodal modal-layout"
        style="display:none"
    >
        <div class="header">
            <h3>{{ editedLayoutUid ? t('Edit layout') : t('Create layout') }}</h3>
        </div>
        <div class="body">
            <div class="content">
                <form class="main">
                    <div class="field">
                        <div class="heading">
                            <label
                                class="required"
                                for="name"
                            >{{ t('Name', {}, 'app') }}</label>
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
                            >{{ t('Handle', {}, 'app') }}</label>
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
import { mapState, mapActions, mapMutations } from 'vuex';
import { filter } from 'lodash';
import { v4 as uuidv4 } from 'uuid';
import { HandleGenerator } from '../../Helpers';

export default {
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
        editedLayout: function () {
            for (let i in this.layouts) {
                if (this.layouts[i].uid == this.editedLayoutUid) {
                    return this.layouts[i];
                }
            }
            return null;
        },
        customLayouts: function () {
            return filter(this.layouts, (layout) => {return layout.type == 'custom'});
        },
        hasError: function () {
            return this.handleError || this.nameError;
        },
        ...mapState(['layouts', 'showLayoutModal', 'editedLayoutUid', 'theme'])
    },
    watch: {
        showLayoutModal: function () {
            if (this.showLayoutModal) {
                this.modal.show();
            } else {
                this.modal.hide();
            }
            this.updategenerator();
            if (this.editedLayoutUid) {
                this.name = this.editedLayout.name;
                this.handle = this.editedLayout.elementUid;
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
            this.handleGenerator = new HandleGenerator('.modal-layout #name', '.modal-layout #handle');
            this.handleGenerator.callback = (value) => {
                this.handle = value;
            };
            this.updategenerator();
        },
        updategenerator: function () {
            if (this.editedLayoutUid === null) {
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
            this.setShowLayoutModal({show: false})
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
            for (let i in this.customLayouts) {
                if (this.customLayouts[i].uid != this.editedLayoutUid && this.customLayouts[i].elementUid == this.handle.trim()) {
                    this.handleError = this.t('This handle is already defined');
                }
            }
        },
        save() {
            this.validateModal();
            if (this.hasError) {
                return;
            }
            if (this.editedLayoutUid !== null) {
                this.updateCustomLayout({name: this.name, elementUid: this.handle});
            } else {
                this.createLayout({
                    id: null,
                    type: 'custom',
                    themeHandle: this.theme,
                    uid: uuidv4(),
                    name: this.name,
                    elementUid: this.handle,
                    hasBlocks: true,
                    description: this.t('Custom : {name}', {name: this.name})
                });
                Craft.cp.displayNotice(this.t('Custom layout is created, make your changes and save'));
            }
            this.closeModal();
        },
        ...mapMutations(['setShowLayoutModal', 'updateCustomLayout']),
        ...mapActions(['createLayout'])
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