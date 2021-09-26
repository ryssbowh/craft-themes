<template>
    <div :class="'modal elementselectormodal options-modal displayer-' + displayer.handle" style="display:none" ref="modal">
        <div class="header">
            <h3>{{ t('Edit displayer options') }}</h3>
        </div>
        <div class="body">
            <div class="content">
                <form class="main" ref="form">
                    <component :is="optionsComponent" :displayer="displayer" :options="options" :errors="displayerOptionsErrors" @updateOptions="updateOptions" :key="itemOptionsEdited.id"></component>
                </form>
            </div>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button type="button" class="btn" @click="setShowOptionsModal(false)">{{ t('Close', {}, 'app') }}</button>
                <button type="button" class="btn submit" @click="save">{{ t('Save', {}, 'app') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';
import Modal from '../modal';
import { merge } from 'lodash';

export default {
    computed: {
        optionsComponent: function () {
            return 'fieldDisplayer-' + this.displayer.handle;
        },
        options: function () {
            return merge(this.displayer.options, this.itemOptionsEdited.options);
        },
        ...mapState(['showOptionsModal', 'displayer', 'itemOptionsEdited', 'displayerOptionsErrors'])
    },
    data() {
        return {
            popup: null
        }
    },
    watch: {
        showOptionsModal: function () {
            if (this.showOptionsModal) {
                this.popup.show();
            } else {
                this.popup.hide();
            }
        },
    },
    beforeUnmount () {
        if (this.popup) {
            this.popup.destroy();
        }
    },
    mounted: function () {
        this.popup = new Modal(this.$refs.modal, {
            hideOnEsc: false,
            hideOnShadeClick: false,
            autoShow: false
        });
    },
    methods: {
        save () {
            let options = $(this.$refs.form).serializeJSON();
            let data = {
                id: this.itemOptionsEdited.id,
                displayer: this.displayer.handle,
                options: options
            };
            axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/validate-field-options'),
                data: data,
                headers: {'X-CSRF-Token': Craft.csrfTokenValue}
            }).then((response) => {
                if (Object.keys(response.data.errors).length > 0) {
                    this.setDisplayerOptionsError(response.data.errors);
                } else {
                    this.updateOptions(options);
                    this.setShowOptionsModal(false);
                    this.setDisplayerOptionsError({});
                }
            }).catch((err) => {
                this.handleError(err);
            });
        },
        ...mapMutations(['setShowOptionsModal', 'setDisplayerOptionsError', 'updateOptions'])
    },
    emits: [],
};
</script>
<style lang="scss" scoped>
.options-modal .body {
    overflow-y: auto;
    max-height: calc(100vh - 65px);
}
</style>