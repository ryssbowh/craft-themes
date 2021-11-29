<template>
    <form-field :errors="errors" :definition="definition" :name="name">
        <template v-slot:heading>
            <div class="heading" v-if="definition.label">
                <label :class="{required: definition.required ?? false}">{{ definition.label }}</label>
                <label :class="{required: definition.required ?? false}">{{ t('View mode') }}</label>
            </div>
        </template>
        <template v-slot:main>
            <div :class="inputClass">
                <div :id="'field-' + name + '-elements'" :class="options.class">
                    <div class="elements">
                    </div>
                    <div class="flex">
                        <button type="button" class="btn add icon dashed">{{ definition.addElementLabel }}</button>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:errors>
            <ul class="errors" v-if="mainErrors">
                <li class="error" v-for="error, index in mainErrors" v-bind:key="index">
                    {{ error }}
                </li>
            </ul>
        </template>
    </form-field>
</template>

<script>
import FormField from './Field';
import { mapState } from 'vuex';
import SelectInput from '../SelectInput';

export default {
    data: function () {
        return {
            realValue: {}
        }
    },
    computed: {
        inputClass: function () {
            return 'input ' + Craft.orientation;
        },
        mainErrors: function () {
            let main = [];
            for (let i in this.errors) {
                if (typeof this.errors[i] == 'string') {
                    main.push(this.errors[i]);
                }
            }
            return main;
        },
        options: function () {
            switch (this.definition.elementType) {
                case 'assets':
                    return {
                        elementType: 'craft\\elements\\Asset',
                        id: 'field-assets',
                        class: 'elementselect',
                        ajaxUrl: 'assets-data',
                        elementClass: 'element small hasthumb'
                    }
                case 'users':
                    return {
                        elementType: 'craft\\elements\\User',
                        id: 'field-users',
                        class: 'elementselect',
                        ajaxUrl: 'users-data',
                        elementClass: 'element small hasstatus hasthumb'
                    }
                case 'categories':
                    return {
                        elementType: 'craft\\elements\\Category',
                        id: 'field-categories',
                        class: 'categoriesfield',
                        ajaxUrl: 'categories-data',
                        elementClass: 'element small hasstatus'
                    }
                case 'entries':
                    return {
                        elementType: 'craft\\elements\\Entry',
                        id: 'field-entries',
                        class: 'elementselect',
                        ajaxUrl: 'entries-data',
                        elementClass: 'element small hasstatus'
                    }
                default:
                    return {};
            }
        },
        ...mapState(['theme'])
    },
    props: {
        value: Object,
        definition: Object,
        errors: Array,
        name: String
    },
    created() {
        this.realValue = this.value;
        if (this.realValue === null) {
            this.realValue = [];
        }
    },
    mounted() {
        this.createSelector();
    },
    methods: {
        createSelector: function () {
            this.selector = new SelectInput({
                actionUrl: 'themes/cp-ajax/' + this.options.ajaxUrl,
                id: 'field-' + this.name + '-elements',
                elementType: this.options.elementType,
                name: 'field-' + this.name,
                sources: '*',
                viewMode: 'small',
                branchLimit: 1,
                theme: this.theme,
                selectable: 0,
                createElementCallback: this.createElement,
                errors: this.getElementsErrors(),
                initialIds: Object.keys(this.realValue).map((i) => {
                    return this.realValue[i].id;
                })
            });
            this.selector.on('viewModesChanged', () => {
                this.realValue = this.selector.getSelectedElementData();
            });
            this.selector.on('removeElements', () => {
                this.realValue = this.selector.getSelectedElementData();
            });
            this.selector.on('orderChanged', () => {
                this.realValue = this.selector.getSelectedElementData();
            });
        },
        createElement: function (element) {
            let inner;
            switch (this.definition.elementType) {
                case 'assets':
                    inner = `<div class="elementthumb">
                            <img sizes="34px" srcset="`+element.srcset+`" alt="">
                        </div>
                        <div class="label">
                            <span class="title">`+element.title+`</span>
                        </div>`
                    break;
                case 'users':
                    inner = `<span class="status `+element.status+`"></span>
                        <div class="elementthumb rounded">
                            <img sizes="34px" srcset="`+element.srcset+`" alt="">
                        </div>
                        <div class="label">
                            <span class="title">`+element.name+`</span>
                        </div>`;
                    break;
                case 'categories':
                    inner = `<span class="status ` + element.status + `"></span>
                        <div class="label">
                            <span class="title">`+element.title+`</span>
                        </div>`;
                    break;
                case 'entries':
                    inner = `<span class="status ` + element.status + `"></span>
                        <div class="label">
                            <span class="title">`+element.title+`</span>
                        </div>`;
                    break;
            }
            return {
                $element: $(`
                <div class="` + this.options.elementClass + `"
                    data-type="` + this.options.elementType + `"
                    data-id="`+ element.id +`"
                    data-label="`+ element.title + `"
                    title="` + element.title + `"
                >` + inner + `
                </div>`),
                id: element.id,
                viewModes: element.viewModes,
                viewMode: this.realValue.filter((e) => e.id == element.id)[0].viewMode ?? null
            };
        },
        getElementsErrors: function () {
            let errors = {};
            for (let i in this.errors) {
                if (typeof this.errors[i] == 'string') {
                    continue;
                }
                let keys = Object.keys(this.errors[i]);
                errors[keys[0]] = this.errors[i][keys[0]];
            }
            return errors;
        }
    },
    watch: {
        realValue: {
            handler: function () {
                this.$emit('change', this.realValue);
            },
            deep: true
        },
        errors: {
            handler: function () {
                if (this.selector) {
                    this.selector.errors = this.getElementsErrors();
                    this.selector.updateErrors();
                }
            },
            deep: true
        }
    },
    components: {
        'form-field': FormField
    },
    emits: ['change']
};
</script>
<style lang="scss" scoped>
.heading {
    display:flex;
    justify-content:space-between;
}
</style>
