import { mapState } from 'vuex';

window.themesBlockStrategyComponents = {};

window.themesBlockStrategyComponents['global'] = {
    props: {
        block: Object,
        options: Object,
    },
    methods: {
        errors: function (field) {
            if (!this.block.errors.options ?? null) {
                return [];
            }
            for (let i in this.block.errors.options) {
                if (this.block.errors.options[i][field] ?? null) {
                    return this.block.errors.options[i][field];
                }
            }
            return [];
        }
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
            $.each($(this.$el).find('.lightswitch'), (i, lightswitch) => {
                $(lightswitch).on('change', (e) => {
                    let options = {};
                    options[$(e.target).data('field')] = $(e.target).hasClass('on');
                    this.$emit('updateOptions', options);
                });
            });
        });
    },
    emits: ['updateOptions'],
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Cache depends on user authentication') }}</label>
            </div>
            <div class="input ltr">                 
                <button type="button" :class="{lightswitch: true, on: block.options.cachePerAuthenticated}" data-field="cachePerAuthenticated">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden">
                </button>
            </div>
            <ul class="errors" v-if="errors('cachePerAuthenticated')">
                <li v-for="error in errors('cachePerAuthenticated')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Cache depends on user') }}</label>
            </div>
            <div class="input ltr">                 
                <button type="button" :class="{lightswitch: true, on: block.options.cachePerUser}" data-field="cachePerUser">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden">
                </button>
            </div>
            <ul class="errors" v-if="errors('cachePerUser')">
                <li v-for="error in errors('cachePerUser')">{{ error }}</li>
            </ul>
        </div>
    </div>`
};

window.themesBlockStrategyComponents['path'] = {...window.themesBlockStrategyComponents['global']};

window.themesBlockStrategyComponents['query'] = {...window.themesBlockStrategyComponents['global']};