<template>
    <div class="modal elementselectormodal modal-options" style="display:none" ref="modal">
        <div class="header" v-if="editedBlock">
            <h3>{{ t('Edit block {block} options', {block: this.editedBlock.name}) }}</h3>
        </div>
        <div class="body" v-if="editedBlock">
            <div class="field">
                <div class="heading">
                    <label>{{ t('Active') }}</label>
                </div>
                <div class="input ltr">
                    <button type="button" :class="'lightswitch active has-labels' + (editedBlock.active ? ' on' : '')" role="checkbox">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden" name="active" :value="active">
                    </button>
                </div>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Caching') }}</label>                                    
                </div>
                <div class="instructions">{{ strategyDescription }}</div>
                <div class="input ltr">
                    <div class="select">
                        <select id="type" name="cacheStrategy" :value="options.cacheStrategy" @input="updateOptions({cacheStrategy: $event.target.value})">
                            <option value="">{{ t('No cache') }}</option>
                            <option :value="strategy.handle" v-for="strategy in cacheStrategies" v-bind:key="strategy.handle">{{ strategy.name }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <component class="strategy" :is="cacheStrategyOptionsComponent" :block="editedBlock" :options="options.cacheStrategyOptions ?? {}" :errors="errors.cacheStrategy ?? {}" @updateOptions="updateStrategyOptions"></component>
            <component :is="optionsComponent" :block="editedBlock" :errors="errors.options ?? {}" :options="options" @updateOptions="updateOptions"></component>
        </div>
        <div class="footer">
            <div class="buttons right">
                <button type="button" class="btn" @click="closeModal">{{ t('Close', {}, 'app') }}</button>
                <button type="button" class="btn submit" @click="save">{{ t('Save', {}, 'app') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import { merge } from 'lodash';

export default {
    computed: {
        strategyDescription: function () {
            for (let i in this.cacheStrategies) {
                if (this.cacheStrategies[i].handle == this.options.cacheStrategy) {
                    return this.cacheStrategies[i].description;
                }
            }
            return '';
        },
        optionsComponent: function () {
            return this.editedBlock.provider + '-' + this.editedBlock.handle;
        },
        cacheStrategyOptionsComponent: function () {
            return 'strategy-' + this.options.cacheStrategy;
        },
        ...mapState(['cacheStrategies', 'showOptionsModal', 'editedBlock'])
    },
    data() {
        return {
            modal: null,
            options: {},
            errors: {},
            active: null
        }
    },
    watch: {
        showOptionsModal: function () {
            if (this.showOptionsModal) {
                this.options = merge({}, this.editedBlock.options);
                this.active = this.editedBlock.active;
                this.errors = this.editedBlock.errors;
                this.modal.show();
            } else {
                this.modal.hide();
            }
        }
    },
    beforeUnmount () {
        this.modal.destroy();
    },
    mounted () {
        this.createModal();
    },
    methods: {
        createModal () {
            this.modal = new Garnish.Modal(this.$refs.modal, {
                hideOnEsc: false,
                hideOnShadeClick: false,
                autoShow: false,
                onFadeIn: () => {
                    let _this = this;
                    Craft.initUiElements(this.$el);
                    $(this.$el).find('.lightswitch.active').on('change', function () {
                        _this.active = $(this).hasClass('on');
                    });
                }
            });
        },
        closeModal () {
            this.options = {};
            this.errors = {};
            this.active = null;
            this.setShowOptionsModal({show:false})
        },
        updateOptions (options) {
            this.options = {...this.options, ...options};
        },
        updateStrategyOptions (options) {
            this.options.cacheStrategyOptions = {...this.options.cacheStrategyOptions, ...options};
        },
        save () {
            let data = {
                blockHandle: this.editedBlock.handle,
                provider: this.editedBlock.provider,
                options: this.options
            };
            axios({
                method: 'post',
                url: Craft.getActionUrl('themes/cp-blocks-ajax/validate-block-options'),
                data: data,
                headers: {'X-CSRF-Token': Craft.csrfTokenValue}
            }).then((response) => {
                if (Object.keys(response.data.errors).length > 0) {
                    this.errors = response.data.errors;
                } else {
                    this.editedBlock.active = this.active;
                    this.editedBlock.options = this.options;
                    this.updateBlock(this.editedBlock);
                    this.closeModal();
                }
            }).catch((err) => {
                this.handleError(err);
            });
        },
        ...mapMutations(['updateBlock', 'setShowOptionsModal']),
        ...mapActions([])
    },
};
</script>

<style lang="scss" scoped>
.modal {
    padding-bottom: 62px;
    height: auto;
    width: auto;
    min-height: calc(100vh / 2);
    .body {
        height: calc(100% - 65px);
        overflow-y: auto;
    }
}
</style>
