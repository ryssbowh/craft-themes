<template>
    <div
        ref="modal"
        class="modal elementselectormodal themes-modal-options"
        style="display:none"
    >
        <div
            v-if="editedBlock"
            class="header"
        >
            <h3>{{ t('Edit block {block} options', {block: editedBlock.name}) }}</h3>
        </div>
        <div
            v-if="editedBlock"
            class="body"
        >
            <formfield-lightswitch
                :value="active ? true : false"
                :definition="{label: t('Active', {}, 'app')}"
                :name="'active'"
                @change="active = $event"
            />
            <div v-if="editedBlock.canBeCached || isContentBlock">
                <div class="field">
                    <div class="heading">
                        <label>{{ t('Caching') }}</label>                                    
                    </div>
                    <div class="instructions">
                        {{ strategyDescription }}
                    </div>
                    <div class="input ltr">
                        <div class="select">
                            <select
                                id="type"
                                v-model="cacheStrategy.handle"
                                @change="updateCacheStrategy($event.target.value)"
                            >
                                <option value="">
                                    {{ t('No cache') }}
                                </option>
                                <option
                                    v-for="strategy in cacheStrategies"
                                    :key="strategy.handle"
                                    :value="strategy.handle"
                                >
                                    {{ strategy.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div
                        v-if="isContentBlock && cacheStrategy.handle == 'global'"
                        class="warning"
                    >
                        {{ t('This is really not recommended, all your pages will display the same content') }}
                    </div>
                </div>
                <component
                    :is="formFieldComponent(definition.field)"
                    v-for="definition, name in strategyFieldsDefinitions"
                    :key="name"
                    :name="name"
                    :definition="definition"
                    :value="cacheStrategy.options[name] ?? null"
                    :errors="getCacheStrategyErrors(name)"
                    @change="updateStrategyOption(name, $event)"
                />
            </div>
            <component
                :is="formFieldComponent(definition.field)"
                v-for="definition, name in editedBlock.optionsDefinitions"
                :key="name"
                :name="name"
                :definition="definition"
                :value="options[name] ?? null"
                :errors="getOptionErrors(name)"
                @change="updateOption(name, $event)"
            />
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
                    @click.prevent="save"
                >
                    {{ t('Save', {}, 'app') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import { merge } from 'lodash';

export default {
    data() {
        return {
            modal: null,
            options: {},
            errors: {},
            active: null,
            cacheStrategy: null
        }
    },
    computed: {
        machineName: function () {
            return this.editedBlock.provider + '_' + this.editedBlock.handle;
        },
        isContentBlock: function () {
            return this.machineName == 'system_content';
        },
        strategyDescription: function () {
            let strategy = this.getStrategy(this.cacheStrategy.handle);
            if (!strategy) {
                return '';
            }
            return strategy.description;
        },
        strategyFieldsDefinitions: function () {
            let strategy = this.getStrategy(this.cacheStrategy.handle);
            if (!strategy) {
                return '';
            }
            return strategy.options.definitions;
        },
        ...mapState(['cacheStrategies', 'showOptionsModal', 'editedBlock'])
    },
    watch: {
        showOptionsModal: function () {
            if (this.showOptionsModal) {
                this.options = merge({}, this.editedBlock.options);
                this.active = this.editedBlock.active;
                this.errors = this.editedBlock.errors;
                this.cacheStrategy = this.editedBlock.cacheStrategy;
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
        formFieldComponent (field) {
            return 'formfield-' + field;
        },
        getStrategy(handle) {
            return this.cacheStrategies[handle] ?? null;
        },
        createModal() {
            this.modal = new Garnish.Modal(this.$refs.modal, {
                hideOnEsc: false,
                hideOnShadeClick: false,
                autoShow: false
            });
        },
        closeModal() {
            this.options = {};
            this.errors = {};
            this.active = null;
            this.setShowOptionsModal({show:false})
        },
        updateStrategyOption(name, value) {
            this.cacheStrategy.options[name] = value;
        },
        updateOption(name, value) {
            this.options[name] = value;
        },
        updateCacheStrategy(handle) {
            if (handle) {
                this.cacheStrategy.handle = handle;
                this.cacheStrategy.options = this.getStrategy(handle).options.defaultValues;
            } else {
                this.cacheStrategy.handle = '';
                this.cacheStrategy.options = {};
            }
        },
        getCacheStrategyErrors(name) {
            if (!this.errors.cacheStrategy) {
                return [];
            }
            return this.errors.cacheStrategy[name] ?? [];
        },
        getOptionErrors(name) {
            if (!this.errors.options) {
                return [];
            }
            return this.errors.options[name] ?? [];
        },
        save() {
            let data = {
                blockHandle: this.editedBlock.handle,
                provider: this.editedBlock.provider,
                options: this.options,
                cacheStrategy: this.cacheStrategy
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
                    this.editedBlock.cacheStrategy = this.cacheStrategy;
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
