export default {
    props: {
        definition: Object,
        errors: Array,
        name: String,
        classes: String
    },
    mounted () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
        <div :class="'field ' + classes" :id="'field-' + name">
            <slot name="heading">
                <div class="heading" v-if="definition.label">
                    <label :class="{required: definition.required ?? false}">{{ definition.label }}</label>
                </div>
            </slot>
            <slot name="instructions">
                <div class="instructions" v-if="definition.instructions" v-html="definition.instructions">
                </div>
            </slot>
            <slot name="main">
            </slot>
            <slot name="tip">
                <p v-if="definition.tip" class="notice with-icon" v-html="definition.tip">
                </p>
            </slot>
            <slot name="warning">
                <p v-if="definition.warning" class="warning with-icon" v-html="definition.warning">
                </p>
            </slot>
            <slot name="errors">
                <ul class="errors" v-if="errors">
                    <li class="error" v-for="error, index in errors" v-bind:key="index">
                        {{ error }}
                    </li>
                </ul>
            </slot>
        </div>`
};
