<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template #main>
            <div class="displayers-config">
                <div class="displayers-sidebar">
                    <div class="heading">
                        <h5>{{ t('File Kinds') }}</h5>
                    </div>
                    <div :class="{'kind-item': true, sel: currentKind == handle}" v-for="elem, handle in definition.mapping" v-bind:key="handle" @click.prevent="currentKind = handle">
                        <div class="name">
                            <h4>{{ elem.label }} <span class="error" data-icon="alert" aria-label="Error" v-if="hasErrors(handle)"></span></h4>
                            <div class="smalltext light code" v-if="realValue[handle].displayer ?? null">
                                {{ getDisplayerName(handle) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="displayers-settings">
                    <div class="settings-container">
                        <div v-for="elem, handle in definition.mapping" v-bind:key="handle">
                            <div class="displayer-settings" v-show="currentKind == handle">
                                <div class="field">
                                    <div class="heading">
                                        <label>{{ t('Displayer') }}</label>
                                    </div>
                                    <div :class="inputClass">
                                        <div class="select">
                                            <select v-model="realValue[handle].displayer" @change="updateDisplayer(handle, $event.target.value)">
                                                <option v-for="displayer, key in elem.displayers" :value="displayer.handle" v-bind:key="key">{{ displayer.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="warning with-icon" v-if="realValue[handle].displayer == 'raw'">
                                        {{ t("This could be used to run potentially dangerous code on your site, do you trust the data you're going to display ?") }}
                                    </div>
                                </div>
                                <component v-for="definition, name in getDisplayer(handle).options.definitions" :name="name" :is="formFieldComponent(definition.field)" :definition="definition" :value="realValue[handle].options[name] ?? null" :errors="getErrors(handle)[name] ?? []" @change="updateOption(handle, name, $event)" :key="name"></component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #errors>
            <span></span>
        </template>
        <template #heading>
            <span></span>
        </template>
        <template #instructions>
            <span></span>
        </template>
        <template #warning>
            <span></span>
        </template>
        <template #tip>
            <span></span>
        </template>
    </form-field>
</template>

<script>
import FormField from './Field';

export default {
    computed: {
        inputClass() {
            return 'input ' + Craft.orientation;
        }
    },
    data: function () {
        return {
            realValue: {},
            currentKind: null
        }
    },
    props: {
        value: Object,
        definition: Object,
        errors: Array,
        name: String
    },
    created() {
        this.realValue = this.value;
        this.currentKind = Object.keys(this.definition.mapping)[0];
    },
    watch: {
        realValue: {
            handler: function () {
                this.$emit('change', this.realValue);
            },
            deep: true
        }
    },
    methods: {
        formFieldComponent (field) {
            return 'formfield-' + field;
        },
        getErrors: function (kind) {
            for (let i in this.errors) {
                let keys = Object.keys(this.errors[i]);
                if ((keys[0] ?? null) == kind) {
                    return this.errors[i][kind];
                }
            }
            return {};
        },
        hasErrors: function (kind) {
            return Object.keys(this.getErrors(kind)).length != 0;
        },
        getDisplayer: function (kind) {
            for (let i in this.definition.mapping[kind].displayers) {
                let displayer = this.definition.mapping[kind].displayers[i];
                if (this.realValue[kind].displayer == displayer.handle) {
                    return displayer;
                }
            }
            return '';
        },
        getDisplayerName: function (kind) {
            let displayer = this.getDisplayer(kind);
            return displayer ? displayer.name : '';
        },
        updateDisplayer: function (kind, displayer) {
            this.realValue[kind] = {
                displayer: displayer,
                options: this.getDisplayer(kind).options.defaultValues
            };
        },
        updateOption: function (kind, name, value) {
            this.realValue[kind].options[name] = value;    
        },
    },
    components: {
        'form-field': FormField
    },
    emits: ['change']
};
</script>
<style lang="scss" scoped>
@import '~craftcms-sass/_mixins';

.field[name=displayers] {
    height: 100%;
}

.displayer-file_default.options-modal {
    .body .content .main {
        overflow: hidden;
    }
}

.displayers-config {
    position: relative;
    height: calc(100% - 2px);
    border-radius: 3px;
    border: 1px solid rgba(96, 125, 159, 0.25);
    background-clip: padding-box;
    overflow: hidden;
    &:after {
        display: block;
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        visibility: visible;
        content: '';
        font-size: 0;
        border-radius: 3px;
        box-shadow: inset 0 1px 3px -1px #acbed2;
        user-select: none;
        pointer-events: none;
    }
}

.displayers-settings {
    height: 100%;
    min-width: 300px;
    overflow-y: auto;
    padding-left: 200px;
    background: #fff;
    box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);
    .settings-container {
        padding: 15px;
    }
}

.displayers-sidebar {
    position: absolute;
    background-color: $grey050;
    left: 0;
    width: 205px;
    height: 100%;
    overflow-y: auto;
    .heading {
        padding: 7px 14px 6px;
        border-bottom: 1px solid rgba(51, 64, 77, 0.1);
        background-color: $grey050;
        background-image: linear-gradient(rgba(51, 64, 77, 0), rgba(51, 64, 77, 0.05));
    }
    .kind-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 14px;
        border-bottom: solid $grey200;
        border-width: 1px 0;
        background-color: $grey100;
        &.sel {
            background-color: $grey200
        }
        &:last-child {
            border-bottom: none;
        }
    }
    h4 {
        margin-bottom:5px;
    }
}
</style>