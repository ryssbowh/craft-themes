document.addEventListener("register-block-strategy-components", function(e) {
    e.detail['global'] = {
        props: {
            block: Object,
            options: Object,
            errors: Object
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
            <span></span>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Cache depends on user authentication') }}</label>
                </div>
                <div class="input ltr">                 
                    <button type="button" :class="{lightswitch: true, on: options.cachePerAuthenticated}" data-field="cachePerAuthenticated">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden">
                    </button>
                </div>
                <ul class="errors" v-if="errors.cachePerAuthenticated">
                    <li v-for="error in errors.cachePerAuthenticated">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Cache depends on user') }}</label>
                </div>
                <div class="input ltr">                 
                    <button type="button" :class="{lightswitch: true, on: options.cachePerUser}" data-field="cachePerUser">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden">
                    </button>
                </div>
                <ul class="errors" v-if="errors.cachePerUser">
                    <li v-for="error in errors.cachePerUser">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Cache depends on view port (mobile, tablet or desktop)') }}</label>
                </div>
                <div class="input ltr">                 
                    <button type="button" :class="{lightswitch: true, on: options.cachePerViewport}" data-field="cachePerViewport">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden">
                    </button>
                </div>
                <ul class="errors" v-if="errors.cachePerViewport">
                    <li v-for="error in errors.cachePerViewport">{{ error }}</li>
                </ul>
            </div>
        </div>`
    };

    e.detail['path'] = {...e.detail['global']};

    e.detail['query'] = {...e.detail['global']};
});