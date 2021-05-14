<template>
    <div class="modal elementselectormodal options-modal" style="display:none" ref="modal">
        <div class="body">
            <div class="content">
                <form class="main" ref="form" v-html="html">
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
import populate from 'populate.js';

export default {
    computed: {
        ...mapState(['showOptionsModal', 'optionsField'])
    },
    data() {
        return {
            popup: null,
            html: null
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
        optionsField: function () {
            this.fetchOptions();
        }
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
        handleError: function (err) {
            if (err.response) {
                Craft.cp.displayError(err.response.data.message);
            } else {
                Craft.cp.displayError(err);
            }
        },
        fetchOptions: function () {
            let _this = this;
            let data = {
                id: this.optionsField.id,
                displayer: this.optionsField.displayerHandle
            };
            return axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/field-options'),
                data: data,
                headers: {'X-CSRF-Token': Craft.csrfTokenValue}
            }).then((response) => {
                _this.html = response.data.html;
                _this.$nextTick(() => {
                    _this.populateForm();
                    Craft.initUiElements(_this.$refs.form);
                });
            })
            .catch((err) => {
                _this.handleError(err);
            });
        },
        populateForm() {
            //Cast all booleans as integers, or populate.js will populate "false" and "true"
            let options = this.optionsField.options;
            for (let i in options) {
                let option = options[i];
                if (typeof option == 'boolean') {
                    options[i] = options[i] ? 1 : 0;
                }
            }
            populate(this.$refs.form, options);
            $.each($(this.$refs.form).find('.lightswitch'), function () {
                if ($(this).find('input').val()) {
                    $(this).addClass('on');
                }
            });
        },
        save () {
            this.hideErrors();
            let _this = this;
            let options = $(this.$refs.form).serializeJSON();
            let data = {
                id: this.optionsField.id,
                displayer: this.optionsField.displayerHandle,
                options: options
            };
            axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/field-options/validate'),
                data: data,
                headers: {'X-CSRF-Token': Craft.csrfTokenValue}
            }).then((response) => {
                if (Object.keys(response.data.errors).length > 0) {
                    _this.showErrors(response.data.errors);
                } else {
                    this.updateOptions(options);
                    this.setShowOptionsModal(false);
                }
            })
            .catch((err) => {
                _this.handleError(err);
            });
            
        },
        hideErrors: function () {
            $(this.$refs.form).find('.errors').hide();
        },
        showErrors: function (errors) {
            let errorsWrapper;
            for (let name in errors) {
                let elem = $(this.$refs.form).find('[name='+name+']').parent();
                if (!elem.next().hasClass('errors')) {
                    errorsWrapper = $('<ul class="errors">');
                    elem.after(errorsWrapper);
                } else {
                    errorsWrapper = elem.next();
                }
                errorsWrapper.empty();
                for (let i in errors[name]) {
                    errorsWrapper.append($('<li>'+errors[name][i]+'</li>'));
                }
            }
        },
        ...mapMutations(['setShowOptionsModal', 'updateOptions']),
        ...mapActions([]),
    },
    emits: [],
};
</script>
<style lang="scss" scoped>
.options-modal .main {
    overflow-y: auto;
}
</style>