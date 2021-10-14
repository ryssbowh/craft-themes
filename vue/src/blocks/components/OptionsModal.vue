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
            <component class="strategy" :is="cacheStrategyOptionsComponent" :block="editedBlock" @updateOptions="updateOptions"></component>
            <component :is="optionsComponent" :block="editedBlock" @updateOptions="updateOptions"></component>
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
            active: null
        }
    },
    watch: {
        showOptionsModal: function () {
            if (this.showOptionsModal) {
                this.options = {...this.editedBlock.options};
                this.active = this.editedBlock.active;
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
            this.active = null;
            this.setShowOptionsModal({show:false})
        },
        updateOptions (options) {
            this.changes = merge(this.options, options);
        },
        save() {
            let changes = {active: this.active, options: this.options};
            this.updateBlock(merge(this.editedBlock, changes));
            this.closeModal();
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
    .body {
        height: calc(100% - 65px);
        overflow-y: auto;
    }
}
</style>
