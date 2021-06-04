<template>
    <div :class="'modal elementselectormodal options-modal displayer-' + displayer.handle" style="display:none" ref="modal">
        <div class="body">
            <div class="content">
                <form class="main" ref="form">
                    <component :is="optionsComponent" :displayer="displayer" :options="displayer.options" :errors="displayerOptionsErrors" @updateOptions="updateOptions" :key="item.id"></component>
                </form>
            </div>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button type="button" class="btn" @click="setShowOptionsModal(false)">{{ t('Close') }}</button>
                <button type="button" class="btn submit" @click="save">{{ t('Save') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Modal from '../modal';

export default {
    computed: {
        optionsComponent: function () {
            return 'fieldDisplayer-' + this.displayer.handle;
        },
        ...mapState(['showOptionsModal', 'displayer', 'item', 'displayerOptionsErrors'])
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
                id: this.item.id,
                displayer: this.displayer.handle,
                options: options
            };
            axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/field-options/validate'),
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
        ...mapMutations(['setShowOptionsModal', 'setDisplayerOptionsError']),
        ...mapActions(['updateOptions']),
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