import './main.scss';
import { merge } from 'lodash';

document.addEventListener("register-field-displayers-components", function(e) {

    e.detail['file_default'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        methods: {
            getDisplayer: function (kind) {
                for (let i in this.displayer.displayersMapping[kind].displayers) {
                    let displayer = merge({}, this.displayer.displayersMapping[kind].displayers[i]);
                    if (this.options.displayers[kind].displayer == displayer.handle) {
                        displayer.options = merge(displayer.options, this.options.displayers[kind].options);
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
            },
            hasErrors: function (kind) {
                return Object.keys(this.getErrors(kind)).length != 0;
            },
            updateOptions: function (kind, options) {
                this.options.displayers[kind].options = merge(this.options.displayers[kind].options, options);
                this.$emit('updateOptions', this.options);
            },
            updateDisplayer: function (kind, displayer) {
                this.options.displayers[kind] = {
                    displayer: displayer,
                    options: this.getDisplayer(kind).options
                };
                this.$emit('updateOptions', this.options);
            }
        },
        data: function () {
            return {
                currentKind: null,
            };
        },
        created: function () {
            this.currentKind = Object.keys(this.displayer.displayersMapping)[0];
        },
        emits: ['updateOptions'],
        template: `
        <div class="displayers-config">
            <div class="displayers-sidebar">
                <div class="heading">
                    <h5>{{ t('File Kinds') }}</h5>
                </div>
                <div :class="{'kind-item': true, sel: currentKind == handle}" v-for="elem, handle in displayer.displayersMapping" v-bind:key="handle" @click.prevent="currentKind = handle">
                    <div class="name">
                        <h4>{{ elem.label }} <span class="error" data-icon="alert" aria-label="Error" v-if="hasErrors(handle)"></span></h4>
                        <div class="smalltext light code" v-if="options.displayers[handle].displayer ?? null">
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
                                        <select v-model="options.displayers[handle].displayer" @change="updateDisplayer(handle, $event.target.value)">
                                            <option v-for="displayer,key in elem.displayers" :value="displayer.handle" v-bind:key="key">{{ displayer.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <component :is="fileDisplayerComponent(handle)" :displayer="getDisplayer(handle)" :kind="handle" :errors="getErrors(handle)" @updateOptions="updateOptions(handle, $event)"></component>
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
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field" v-for="elem, typeUid in displayer.viewModes">
                <div class="heading">
                    <label class="required">{{ t('View mode for {type}', {type: elem.type}) }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="options.viewModes[typeUid]" @change="$emit('updateOptions', {viewModes: options.viewModes})">
                            <option v-for="label, uid in elem.viewModes" :value="uid">{{ label }}</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errors['viewMode-'+typeUid]">
                    <li v-for="error in errors['viewMode-'+typeUid]">{{ error }}</li>
                </ul>
            </div>
            <div class="field" v-if="displayer.viewModes.length == 0">
                <div class="warning with-icon">
                    {{ t("It seems this field doesn't have any valid source") }}
                </div>
            </div>
        </div>`
    };

    e.detail['category_rendered'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('View mode') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="options.viewModeUid" @input="$emit('updateOptions', {viewModeUid: $event.target.value})">
                            <option v-for="label, uid in displayer.viewModes" :value="uid">{{ label }}</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errors.viewModeUid">
                    <li v-for="error in errors.viewModeUid">{{ error }}</li>
                </ul>
                <div class="warning with-icon" v-if="displayer.viewModes.length == 0">
                    {{ t("It seems this field doesn't have any valid source") }}
                </div>
            </div>
        </div>`
    };

    e.detail['tag_rendered'] = {... e.detail['category_rendered']};

    e.detail['user_rendered'] = {... e.detail['category_rendered']};

    e.detail['asset_rendered'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field" v-for="elem, volumeUid in displayer.viewModes">
                <div class="heading">
                    <label class="required">{{ t('View mode for volume {volume}', {volume: elem.label}) }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="options.viewModes[volumeUid]" @change="$emit('updateOptions', {viewModes: options.viewModes})">
                            <option v-for="label, uid in elem.viewModes" :value="uid">{{ label }}</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errors['viewMode-'+volumeUid]">
                    <li v-for="error in errors['viewMode-'+volumeUid]">{{ error }}</li>
                </ul>
            </div>
            <div class="field" v-if="displayer.viewModes.length == 0">
                <div class="warning with-icon">
                    {{ t("It seems this field doesn't have any valid source") }}
                </div>
            </div>
        </div>`
    };

    e.detail['url_default'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Label', {}, 'app') }}</label>
                </div>
                <div class="instructions">
                    <p>{{ t('Leave blank to use the url itself') }}</p>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="options.label" @input="$emit('updateOptions', {label: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.label">
                    <li v-for="error in errors.label">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Open in new tab') }}</label>
                </div>                    
                <lightswitch :on="options.newTab" @change="$emit('updateOptions', {newTab: $event})">
                </lightswitch>
            </div>
        </div>`
    };

    e.detail['redactor_truncated'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="warning with-icon">
                {{ t("Truncated text will always have html stripped out") }}
            </div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('Character limit') }}</label>
                </div>
                <div class="input ltr">
                    <input type="number" class="fullwidth text" :value="options.truncated" min="1" :placeholder="t('Character limit')" @input="$emit('updateOptions', {truncated: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.truncated">
                    <li v-for="error in errors.truncated">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Ellipsis') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="options.ellipsis" @input="$emit('updateOptions', {ellipsis: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.ellipsis">
                    <li v-for="error in errors.ellipsis">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Link ellipsis to entry') }}</label>
                </div>
                <lightswitch :on="options.linked" @change="$emit('updateOptions', {linked: $event})">
                </lightswitch>
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
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Strip HTML tags') }}</label>
                </div>
                <lightswitch :on="options.stripped" @change="$emit('updateOptions', {stripped: $event})">
                </lightswitch>
            </div>
        </div>`
    };

    e.detail['number_default'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Decimals') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="options.decimals" @input="$emit('updateOptions', {decimals: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.decimals">
                    <li v-for="error in errors.decimals">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Show prefix') }}</label>
                </div>
                <lightswitch :on="options.showPrefix" @change="$emit('updateOptions', {showPrefix: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Show suffix') }}</label>
                </div>
                <lightswitch :on="options.showSuffix" @change="$emit('updateOptions', {showSuffix: $event})">
                </lightswitch>
            </div>
        </div>`
    };

    e.detail['entry_link'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('Label', {}, 'app') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="options.label" @input="$emit('updateOptions', {label: $event.target.value})">
                            <option value="title">{{ t('Entry title') }}</option>
                            <option value="custom">{{ t('Custom') }}</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errors.label">
                    <li v-for="error in errors.label">{{ error }}</li>
                </ul>
            </div>
            <div class="field" v-if="options.label == 'custom'">
                <div class="heading">
                    <label class="required">{{ t('Custom') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="options.custom" @input="$emit('updateOptions', {custom: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.custom">
                    <li v-for="error in errors.custom">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Open in new tab') }}</label>
                </div>
                <lightswitch :on="options.newTab" @change="$emit('updateOptions', {newTab: $event})">
                </lightswitch>
            </div>
        </div>`
    };

    e.detail['date_date'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('Format') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="options.format" @input="$emit('updateOptions', {format: $event.target.value})">
                            <option :value="format" v-for="label, format in displayer.formats">{{ label }}</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errors.format">
                    <li v-for="error in errors.format">{{ error }}</li>
                </ul>
            </div>
            <div class="field" v-if="options.format == 'custom'">
                <div class="heading">
                    <label class="required">{{ t('Custom') }}</label>
                </div>
                <div class="instructions">
                    <p><span v-html="t('View available formats')"></span> <a href="https://www.php.net/manual/en/datetime.format.php" target="_blank">{{ t('here') }}</a></p>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="options.custom" @input="$emit('updateOptions', {custom: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.custom">
                    <li v-for="error in errors.custom">{{ error }}</li>
                </ul>
            </div>
        </div>`
    };

    e.detail['date_datetime'] = {...e.detail['date_date']};

    e.detail['time_time'] = {...e.detail['date_date']};

    e.detail['email_default'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Output as link') }}</label>
                </div>
                <lightswitch :on="options.linked" @change="$emit('updateOptions', {linked: $event})">
                </lightswitch>
            </div>
        </div>`
    };

    e.detail['category_list'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Output as links') }}</label>
                </div>
                <lightswitch :on="options.linked" @change="$emit('updateOptions', {linked: $event})">
                </lightswitch>
            </div>
        </div>`
    };

    e.detail['author_default'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Display first name') }}</label>
                </div>
                <lightswitch :on="options.firstName" @change="$emit('updateOptions', {firstName: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Display last name') }}</label>
                </div>
                <lightswitch :on="options.lastName" @change="$emit('updateOptions', {lastName: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Display email') }}</label>
                </div>
                <lightswitch :on="options.email" @change="$emit('updateOptions', {email: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Link email') }}</label>
                </div>
                <lightswitch :on="options.linkEmail" @change="$emit('updateOptions', {linkEmail: $event})">
                </lightswitch>
            </div>
        </div>`
    };

    e.detail['user-info_default'] = {... e.detail['author_default']}

    e.detail['user_default'] = {... e.detail['author_default']}

    e.detail['asset_link'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('Label', {}, 'app') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="options.label" @input="$emit('updateOptions', {label: $event.target.value})">
                            <option value="title">{{ t('Asset title') }}</option>
                            <option value="filename">{{ t('File name') }}</option>
                            <option value="custom">{{ t('Custom') }}</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errors.label">
                    <li v-for="error in errors.label">{{ error }}</li>
                </ul>
            </div>
            <div class="field" v-if="options.label == 'custom'">
                <div class="heading">
                    <label class="required">{{ t('Custom') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="options.custom" @input="$emit('updateOptions', {custom: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.custom">
                    <li v-for="error in errors.custom">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Open in new tab') }}</label>
                </div>
                <lightswitch :on="options.newTab" @change="$emit('updateOptions', {newTab: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Download link') }}</label>
                </div>
                <lightswitch :on="options.download" @change="$emit('updateOptions', {download: $event})">
                </lightswitch>
            </div>
        </div>`
    };

    e.detail['title_default'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('Tag') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="options.tag" @input="$emit('updateOptions', {tag: $event.target.value})">
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
                <ul class="errors" v-if="errors.tag">
                    <li v-for="error in errors.tag">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Link to Element') }}</label>
                </div>
                <lightswitch :on="options.linked" @change="$emit('updateOptions', {linked: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Open in new tab') }}</label>
                </div>
                <lightswitch :on="options.newTab" @change="$emit('updateOptions', {newTab: $event})">
                </lightswitch>
            </div>
        </div>`
    };


    e.detail['tag-title_default'] = {
        props: {
            displayer: Object,
            options: Object,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('Tag') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="options.tag" @input="$emit('updateOptions', {tag: $event.target.value})">
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
                <ul class="errors" v-if="errors.tag">
                    <li v-for="error in errors.tag">{{ error }}</li>
                </ul>
            </div>
        </div>`
    };
});