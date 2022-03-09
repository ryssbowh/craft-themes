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
        let defaultDisplayer;
        for (let kind in this.definition.mapping) {
            defaultDisplayer = this.definition.mapping[kind].displayers[0];
            if (this.value[kind] ?? null) {
                this.realValue[kind] = this.value[kind];
                
            } else {
                this.realValue[kind] = {};
            }
            if (!this.realValue[kind].options) {
                this.realValue[kind].options = defaultDisplayer.options.defaultValues;
            }
            if (!this.realValue[kind].displayer) {
                this.realValue[kind].displayer = defaultDisplayer.handle;
            }
        }
        this.currentKind = Object.keys(this.definition.mapping)[0] ?? null;
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
            if (!this.definition.mapping[kind]) {
                return null;
            }
            for (let i in this.definition.mapping[kind].displayers) {
                let displayer = this.definition.mapping[kind].displayers[i];
                if (this.realValue[kind].displayer == displayer.handle) {
                    return displayer;
                }
            }
            return null;
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
    emits: ['change'],
    template: `
        <form-field :errors="errors" :definition="definition" :name="name">
            <template #main>
                <div class="displayers-sidebar">
                    <div class="heading">
                        <h5>{{ t('File Kinds') }}</h5>
                    </div>
                    <a :class="{'kind-item': true, sel: currentKind == handle}" v-for="elem, handle in definition.mapping" v-bind:key="handle" @click.prevent="currentKind = handle">
                        <div class="name">
                            <h4>{{ elem.label }} <span class="error" data-icon="alert" aria-label="Error" v-if="hasErrors(handle)"></span></h4>
                            <div class="smalltext light code" v-if="realValue[handle].displayer ?? null">
                                {{ getDisplayerName(handle) }}
                            </div>
                        </div>
                    </a>
                </div>
                <div class="displayers-settings">
                    <div class="settings-container">
                        <div v-for="elem, handle in definition.mapping" v-bind:key="handle">
                            <div class="displayer-settings" v-show="currentKind == handle">
                                <div class="field">
                                    <div class="heading">
                                        <label class="required">{{ t('Displayer') }}</label>
                                    </div>
                                    <div :class="inputClass">
                                        <div class="select">
                                            <select v-model="realValue[handle].displayer" @change="updateDisplayer(handle, $event.target.value)">
                                                <option v-for="displayer, key in elem.displayers" :value="displayer.handle" v-bind:key="key">{{ displayer.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <component v-for="definition, name in getDisplayer(handle).options.definitions" :name="name" :is="formFieldComponent(definition.field)" :definition="definition" :value="realValue[handle].options[name] ?? null" :errors="getErrors(handle)[name] ?? []" @change="updateOption(handle, name, $event)" :key="name"></component>
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
        </form-field>`
};