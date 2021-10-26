document.addEventListener("register-block-strategy-components", function(e) {
    e.detail['global'] = {
        props: {
            block: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <span></span>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Cache depends on user authentication') }}</label>
                </div>
                <lightswitch :on="options.cachePerAuthenticated" @change="$emit('updateOptions', {cachePerAuthenticated: $event})">
                </lightswitch>
                <ul class="errors" v-if="errors.cachePerAuthenticated">
                    <li v-for="error in errors.cachePerAuthenticated">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Cache depends on user') }}</label>
                </div>
                <lightswitch :on="options.cachePerUser" @change="$emit('updateOptions', {cachePerUser: $event})">
                </lightswitch>
                <ul class="errors" v-if="errors.cachePerUser">
                    <li v-for="error in errors.cachePerUser">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Cache depends on view port (mobile, tablet or desktop)') }}</label>
                </div>
                <lightswitch :on="options.cachePerViewport" @change="$emit('updateOptions', {cachePerViewport: $event})">
                </lightswitch>
                <ul class="errors" v-if="errors.cachePerViewport">
                    <li v-for="error in errors.cachePerViewport">{{ error }}</li>
                </ul>
            </div>
        </div>`
    };

    e.detail['path'] = {...e.detail['global']};

    e.detail['query'] = {...e.detail['global']};
});