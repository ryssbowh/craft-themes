window.displayersOptionComponents = {};

window.displayersOptionComponents['entry_rendered'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    created: function () {
        console.log(this.displayer.viewModes);
    },
    template: `
    <div>
        <div class="field" v-for="elem, typeUid in displayer.viewModes">
            <div class="heading">
                <label class="required">{{ t('View mode for {type}', {type: elem.type}) }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select :name="'viewModes['+typeUid+']'" :value="options.viewModes[typeUid]">
                        <option v-for="label, uid in elem.viewModes" :value="uid">{{ label }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('viewMode-'+typeUid)">
                <li v-for="error in errorList('viewMode-'+typeUid)">{{ error }}</li>
            </ul>
        </div>
    </div>`
};

window.displayersOptionComponents['category_rendered'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select name="viewMode" :value="options.viewMode">
                        <option v-for="label, uid in displayer.viewModes" :value="uid">{{ label }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('viewMode')">
                <li v-for="error in errorList('viewMode')">{{ error }}</li>
            </ul>
        </div>
    </div>`
};

window.displayersOptionComponents['asset_rendered'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    template: `
    <div>
        <div class="field" v-for="elem, volumeUid in displayer.viewModes">
            <div class="heading">
                <label class="required">{{ t('View mode for volume {volume}', {volume: elem.label}) }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select :name="'viewModes['+volumeUid+']'" :value="options.viewModes[volumeUid]">
                        <option v-for="label, uid in elem.viewModes" :value="uid">{{ label }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('viewMode-'+volumeUid)">
                <li v-for="error in errorList('viewMode-'+volumeUid)">{{ error }}</li>
            </ul>
        </div>
    </div>`
};

window.displayersOptionComponents['url_default'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Open in new tab') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: options.newTab}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="newTab">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['time_default'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    data: function () {
        return {
            format: 'd/m/Y H:i:s',
            custom: ''
        };
    },
    created: function () {
        format: this.options.format;
        custom: this.options.custom;
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Format') }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select name="format" v-model="format">
                        <option value="H:i:s">{{ t('Full : 13:25:36') }}</option>
                        <option value="H:iY">{{ t('Without seconds : 13:25') }}</option>
                        <option value="custom">{{ t('Custom') }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('format')">
                <li v-for="error in errorList('format')">{{ error }}</li>
            </ul>
        </div>
        <div class="field" v-if="format == 'custom'">
            <div class="heading">
                <label class="required">{{ t('Custom') }}</label>
            </div>
            <div class="instructions">
                <p><span v-html="t('View available formats')"></span> <a href="https://www.php.net/manual/fr/datetime.format.php" target="_blank">{{ t('here') }}</a></p>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" name="custom" v-model="custom">
            </div>
            <ul class="errors" v-if="errorList('custom')">
                <li v-for="error in errorList('custom')">{{ error }}</li>
            </ul>
        </div>
    </div>`
};

window.displayersOptionComponents['redactor_trimmed'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    data: function () {
        return {
            truncated: null,
            ellipsis: null,
        };
    },
    created: function () {
        this.truncated = this.options.truncated;
        this.ellipsis = this.options.ellipsis;
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Truncated') }}</label>
            </div>
            <div class="input ltr">
                <input type="number" class="fullwidth text" name="truncated" v-model="truncated" min="1" :placeholder="t('Character limit')">
            </div>
            <ul class="errors" v-if="errorList('truncated')">
                <li v-for="error in errorList('truncated')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Ellipsis') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" name="ellipsis" v-model="ellipsis">
            </div>
            <ul class="errors" v-if="errorList('ellipsis')">
                <li v-for="error in errorList('ellipsis')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Link ellipsis to entry') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: options.linked}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="linked">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['redactor_full'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Strip HTML tags') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: options.stripped}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="stripped">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['plain_text_trimmed'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    data: function () {
        return {
            truncated: null,
            ellipsis: null,
        };
    },
    created: function () {
        this.truncated = this.options.truncated;
        this.ellipsis = this.options.ellipsis;
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Truncated') }}</label>
            </div>
            <div class="input ltr">
                <input type="number" class="fullwidth text" name="truncated" v-model="truncated" min="1" :placeholder="t('Character limit')">
            </div>
            <ul class="errors" v-if="errorList('truncated')">
                <li v-for="error in errorList('truncated')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Ellipsis') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" name="ellipsis" v-model="ellipsis">
            </div>
            <ul class="errors" v-if="errorList('ellipsis')">
                <li v-for="error in errorList('ellipsis')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Link ellipsis to entry') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: options.linked}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="linked">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['number_default'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    data: function () {
        return {
            decimals: 0,
        };
    },
    created: function () {
        this.decimals = this.options.decimals;
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Decimals') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" name="decimals" v-model="decimals">
            </div>
            <ul class="errors" v-if="errorList('decimals')">
                <li v-for="error in errorList('decimals')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Show prefix') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: options.showPrefix}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="showPrefix">
                </button>
            </div>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Show suffix') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: options.showSuffix}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="showSuffix">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['entry_link'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    data: function () {
        return {
            label: 'title',
            custom: '',
            newTab: false,
        };
    },
    created: function () {
        this.label = this.options.label;
        this.custom = this.options.custom;
        this.newTab = this.options.newTab;
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Label') }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select name="label" v-model="label">
                        <option value="title">{{ t('Entry title') }}</option>
                        <option value="custom">{{ t('Custom') }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('label')">
                <li v-for="error in errorList('label')">{{ error }}</li>
            </ul>
        </div>
        <div class="field" v-if="label == 'custom'">
            <div class="heading">
                <label class="required">{{ t('Custom') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" name="custom" v-model="custom">
            </div>
            <ul class="errors" v-if="errorList('custom')">
                <li v-for="error in errorList('custom')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Open in new tab') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: newTab}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="newTab">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['date_default'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    data: function () {
        return {
            format: 'd/m/Y H:i:s',
            custom: ''
        };
    },
    created: function () {
        format: this.options.format;
        custom: this.options.custom;
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Format') }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select name="format" v-model="format">
                        <option value="d/m/Y H:i:s">{{ t('Full : 31/10/2005 13:25:13') }}</option>
                        <option value="d/m/Y">{{ t('Date : 31/10/2005') }}</option>
                        <option value="H:i">{{ t('Time : 13:25') }}</option>
                        <option value="custom">{{ t('Custom') }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('format')">
                <li v-for="error in errorList('format')">{{ error }}</li>
            </ul>
        </div>
        <div class="field" v-if="format == 'custom'">
            <div class="heading">
                <label class="required">{{ t('Custom') }}</label>
            </div>
            <div class="instructions">
                <p><span v-html="t('View available formats')"></span> <a href="https://www.php.net/manual/fr/datetime.format.php" target="_blank">{{ t('here') }}</a></p>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" name="custom" v-model="custom">
            </div>
            <ul class="errors" v-if="errorList('custom')">
                <li v-for="error in errorList('custom')">{{ error }}</li>
            </ul>
        </div>
    </div>`
};

window.displayersOptionComponents['email_default'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Output as link') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: linked}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="linked">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['category_list'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Output as links') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: linked}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="linked">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['author_default'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Display first name') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: firstName}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="firstName">
                </button>
            </div>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Display last name') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: lastName}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="lastName">
                </button>
            </div>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Display email') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: email}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="email">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['asset_link'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    data: function () {
        return {
            label: 'title',
            custom: '',
            newTab: false,
            download: false
        };
    },
    created: function () {
        this.label = this.options.label;
        this.custom = this.options.custom;
        this.newTab = this.options.newTab;
        this.download = this.options.download;
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Label') }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select name="label" v-model="label">
                        <option value="title">{{ t('Asset title') }}</option>
                        <option value="custom">{{ t('Custom') }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('label')">
                <li v-for="error in errorList('label')">{{ error }}</li>
            </ul>
        </div>
        <div class="field" v-if="label == 'custom'">
            <div class="heading">
                <label class="required">{{ t('Custom') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" name="custom" v-model="custom">
            </div>
            <ul class="errors" v-if="errorList('custom')">
                <li v-for="error in errorList('custom')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Open in new tab') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: newTab}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="newTab">
                </button>
            </div>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Download link') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: download}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="download">
                </button>
            </div>
        </div>
    </div>`
};

window.displayersOptionComponents['title_default'] = {
    props: {
        displayer: Object,
        options: Object,
        errors: Object
    },
    methods: {
        errorList: function (field) {
            return this.errors[field] ?? [];
        }
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
    },
    template: `
    <div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Tag') }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select name="tag" :value="options.tag">
                        <option value="h1">H1</option>
                        <option value="h2">H2</option>
                        <option value="h3">H3</option>
                        <option value="h4">H4</option>
                        <option value="h5">H5</option>
                        <option value="h6">H6</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('tag')">
                <li v-for="error in errorList('tag')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Link to Element') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: options.linked}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="linked">
                </button>
            </div>
        </div>
    </div>`
};