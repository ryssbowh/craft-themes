<template>
    <div class="modal elementselectormodal" style="display:none" ref="modal">
        <div class="body">
            <div class="content">
                <form class="main" ref="form" v-html="html">
                </form>
            </div>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button type="button" class="btn" @click="closeModal">{{ t('Close') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';
import Modal from '../modal';
import populate from 'populate.js';

export default {
    computed: {
        ...mapState([])
    },
    props: {
        field: Object,
        showModal: Boolean
    },
    data() {
        return {
            popup: null,
            html: null
        }
    },
    watch: {
        showModal: function () {
            if (this.showModal) {
                this.popup.show();
            } else {
                this.popup.hide();
            }
        }
    },
    beforeUnmount () {
        this.popup.destroy();
    },
    created: function () {
        this.fetchOptions();
    },
    methods: {
        fetchOptions: function () {
            let _this = this;
            let data = {
                id: this.field.id,
                displayer: this.field.displayerHandle
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
                    _this.popup = new Modal(_this.$refs.modal, {
                        hideOnEsc: false,
                        hideOnShadeClick: false,
                        autoShow: false
                    });
                });
            })
            .catch((err) => {
                // handleError(err);
            });
        },
        populateForm() {
            populate(this.$refs.form, this.field.options);
            $.each($(this.$refs.form).find('.lightswitch'), function () {
                if ($(this).find('input').val()) {
                    $(this).addClass('on');
                }
            });
        },
        closeModal () {
            let options = $(this.$refs.form).serializeJSON();
            this.$emit('closeModal', {id: this.field.id, data: {options: options}});
        },
        ...mapMutations([]),
        ...mapActions([]),
    },
    emits: ['closeModal'],
    mixins: [Mixin],
};
</script>
<style lang="scss" scoped>
</style>