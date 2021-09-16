import './main.scss';

document.addEventListener("register-field-displayers-components", function(e) {

    e.detail['file_default'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        methods: {
            errorList: function (field) {
                return this.errors[field] ?? [];
            },
            getDisplayer: function (kind) {
                for (let i in this.displayer.displayersMapping[kind].displayers) {
                    let displayer = this.displayer.displayersMapping[kind].displayers[i];
                    if (this.displayers[kind] && this.displayers[kind].displayer == displayer.handle) {
                        return displayer;
                    }
                }
                return '';
            },
            getDisplayerName: function (kind) {
                let displayer = this.getDisplayer(kind);
                return displayer ? displayer.name : '';
            },
            fileDisplayerComponent: function (kind) {
                let displayer = this.getDisplayer(kind);
                return displayer ? 'fileDisplayer-' + displayer.handle : null;
            },
            getErrors: function (kind) {
                return this.errors[kind] ? this.errors[kind][0] ?? {} : {};
            }
        },
        data: function () {
            return {
                displayers: [],
                currentKind: null,
            };
        },
        created: function () {
            this.displayers = this.options.displayers;
            this.currentKind = Object.keys(this.displayer.displayersMapping)[0];
            for (let i in this.displayer.displayersMapping) {
                if (typeof this.displayers[i] == 'undefined') {
                    this.displayers[i] = {
                        displayer: '',
                        options: {}
                    };
                }
            }
        },
        template: `
        <div class="displayers-config">
            <div class="displayers-sidebar">
                <div class="heading">
                    <h5>{{ t('File Kinds') }}</h5>
                </div>
                <div :class="{'kind-item': true, sel: currentKind == handle}" v-for="elem, handle in displayer.displayersMapping" v-bind:key="handle" @click.prevent="currentKind = handle">
                    <div class="name">
                        <h4>{{ elem.label }}</h4>
                        <div class="smalltext light code" v-if="displayers[handle]">
                            {{ getDisplayerName(handle) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="displayers-settings">
                <div class="settings-container">
                    <div v-for="elem, handle in displayer.displayersMapping" v-bind:key="handle">
                        <div class="displayer-settings" v-show="currentKind == handle">
                            <div class="field">
                                <div class="heading">
                                    <label>{{ t('Displayer') }}</label>
                                </div>
                                <div class="input ltr">
                                    <div class="select">
                                        <select :name="'displayers['+handle+'][displayer]'" v-model="displayers[handle].displayer">
                                            <option v-for="displayer,key in elem.displayers" :value="displayer.handle" v-bind:key="key">{{ displayer.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <component v-if="getDisplayer(handle).hasOptions" :is="fileDisplayerComponent(handle)" :displayer="getDisplayer(handle)" :kind="handle" :errors="getErrors(handle)"></component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `
    };

    e.detail['asset_render_file'] = {...e.detail['file_default']}

    e.detail['entry_rendered'] = {
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
        data: function () {
            return {
                viewModes: {}
            }
        },
        created: function () {
            this.viewModes = this.options.viewModes;
        },
        template: `
        <div>
            <div class="field" v-for="elem, typeUid in displayer.viewModes">
                <div class="heading">
                    <label class="required">{{ t('View mode for {type}', {type: elem.type}) }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select :name="'viewModes['+typeUid+']'" v-model="viewModes[typeUid]">
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

    e.detail['category_rendered'] = {
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
        data: function () {
            return {
                viewMode: null
            }
        },
        created: function () {
            this.viewMode = this.options.viewMode;
        },
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('View mode') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select name="viewMode" v-model="viewMode">
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

    e.detail['tag_rendered'] = {... e.detail['category_rendered']};

    e.detail['asset_rendered'] = {
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
        data: function () {
            return {
                viewModes: {}
            }
        },
        created: function () {
            this.viewModes = this.options.viewModes;
        },
        template: `
        <div>
            <div class="field" v-for="elem, volumeUid in displayer.viewModes">
                <div class="heading">
                    <label class="required">{{ t('View mode for volume {volume}', {volume: elem.label}) }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select :name="'viewModes['+volumeUid+']'" v-model="viewModes[volumeUid]">
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

    e.detail['url_default'] = {
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
                        <input type="hidden" name="newTab" :value="options.newTab ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['time_default'] = {
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
            this.format = this.options.format;
            this.custom = this.options.custom;
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

    e.detail['redactor_truncated'] = {
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
                    <label class="required">{{ t('Character limit') }}</label>
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
                        <input type="hidden" name="linked" :value="options.linked ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['plain_text_truncated'] = {...e.detail['redactor_truncated']};

    e.detail['redactor_full'] = {
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
                        <input type="hidden" name="stripped" :value="options.stripped ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['number_default'] = {
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
                        <input type="hidden" name="showPrefix" :value="options.showPrefix ? 1 : ''">
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
                        <input type="hidden" name="showSuffix" :value="options.showSuffix ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['entry_link'] = {
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
                        <input type="hidden" name="newTab" :value="newTab ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['date_default'] = {
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
            this.format = this.options.format;
            this.custom = this.options.custom;
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

    e.detail['email_default'] = {
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
                    <button type="button" :class="{lightswitch: true, on: options.linked}">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden" name="linked" :value="options.linked ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['category_list'] = {
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
                    <button type="button" :class="{lightswitch: true, on: options.linked}">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden" name="linked" :value="options.linked ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['author_default'] = {
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
                    <button type="button" :class="{lightswitch: true, on: options.firstName}">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden" name="firstName" :value="options.firstName ? 1 : ''">
                    </button>
                </div>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Display last name') }}</label>
                </div>
                <div class="input ltr">                    
                    <button type="button" :class="{lightswitch: true, on: options.lastName}">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden" name="lastName" :value="options.lastName ? 1 : ''">
                    </button>
                </div>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Display email') }}</label>
                </div>
                <div class="input ltr">                    
                    <button type="button" :class="{lightswitch: true, on: options.email}">
                        <div class="lightswitch-container">
                            <div class="handle"></div>
                        </div>
                        <input type="hidden" name="email" :value="options.email ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['user-info_default'] = {... e.detail['author_default']}

    e.detail['asset_link'] = {
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
                            <option value="filename">{{ t('File name') }}</option>
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
                        <input type="hidden" name="newTab" :value="newTab ? 1 : ''">
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
                        <input type="hidden" name="download" :value="download ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };

    e.detail['title_default'] = {
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
        data: function () {
            return {
                tag: 'h1',
            };
        },
        created: function () {
            this.tag = this.options.tag;
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
                        <select name="tag" v:model="tag">
                            <option value="h1">H1</option>
                            <option value="h2">H2</option>
                            <option value="h3">H3</option>
                            <option value="h4">H4</option>
                            <option value="h5">H5</option>
                            <option value="h6">H6</option>
                            <option value="p">p</option>
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
                        <input type="hidden" name="linked" :value="options.linked ? 1 : ''">
                    </button>
                </div>
            </div>
        </div>`
    };


    e.detail['tag-title_default'] = {
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
        data: function () {
            return {
                tag: 'h1',
            };
        },
        created: function () {
            this.tag = this.options.tag;
        },
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('Tag') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select name="tag" v:model="tag">
                            <option value="h1">H1</option>
                            <option value="h2">H2</option>
                            <option value="h3">H3</option>
                            <option value="h4">H4</option>
                            <option value="h5">H5</option>
                            <option value="h6">H6</option>
                            <option value="p">p</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errorList('tag')">
                    <li v-for="error in errorList('tag')">{{ error }}</li>
                </ul>
            </div>
        </div>`
    };
});