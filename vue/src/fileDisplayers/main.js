window.fileDisplayers = {};

window.fileDisplayers['iframe'] = {
    props: {
        displayer: Object,
        kind: String,
        errors: Object
    },
    data: function () {
        return {
            width: 500,
            height: 500,
        };
    },
    created: function () {
        this.width = this.displayer.options.width;
        this.height = this.displayer.options.height;
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
                <label>{{ t('Width') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" :name="'displayers['+kind+'][options][width]'" v-model="width">
            </div>
            <ul class="errors" v-if="errorList('width')">
                <li v-for="error in errorList('width')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Height') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" :name="'displayers['+kind+'][options][height]'" v-model="height">
            </div>
            <ul class="errors" v-if="errorList('height')">
                <li v-for="error in errorList('height')">{{ error }}</li>
            </ul>
        </div>
    </div>
    `
}

window.fileDisplayers['html_video'] = {
    props: {
        displayer: Object,
        kind: String,
        errors: Object
    },
    data: function () {
        return {
            controls: false,
            muted: false,
            autoplay: false,
            width: 500,
            height: 500,
        };
    },
    created: function () {
        this.controls = this.displayer.options.controls;
        this.muted = this.displayer.options.muted;
        this.autoplay = this.displayer.options.autoplay;
        this.width = this.displayer.options.width;
        this.height = this.displayer.options.height;
    },
    mounted: function () {
        this.$nextTick(() => {
            Craft.initUiElements(this.$el);
        });
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
                <label>{{ t('Width') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" :name="'displayers['+kind+'][options][width]'" v-model="width">
            </div>
            <ul class="errors" v-if="errorList('width')">
                <li v-for="error in errorList('width')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Height') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" :name="'displayers['+kind+'][options][height]'" v-model="height">
            </div>
            <ul class="errors" v-if="errorList('height')">
                <li v-for="error in errorList('height')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Show controls') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: controls}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" :name="'displayers['+kind+'][options][controls]'">
                </button>
            </div>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Muted') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: muted}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" :name="'displayers['+kind+'][options][muted]'">
                </button>
            </div>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Autoplay') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: autoplay}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" :name="'displayers['+kind+'][options][autoplay]'">
                </button>
            </div>
        </div>
    </div>
    `
}

window.fileDisplayers['html_audio'] = {
    props: {
        displayer: Object,
        kind: String,
        errors: Object
    },
    data: function () {
        return {
            controls: false,
            muted: false,
            autoplay: false
        };
    },
    created: function () {
        this.controls = this.displayer.options.controls;
        this.muted = this.displayer.options.muted;
        this.autoplay = this.displayer.options.autoplay;
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
                <label>{{ t('Show controls') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: controls}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" :name="'displayers['+kind+'][options][controls]'">
                </button>
            </div>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Muted') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: muted}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" :name="'displayers['+kind+'][options][muted]'">
                </button>
            </div>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Autoplay') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: autoplay}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" :name="'displayers['+kind+'][options][autoplay]'">
                </button>
            </div>
        </div>
    </div>
    `
}

window.fileDisplayers['image_transform'] = {
    props: {
        displayer: Object,
        kind: String,
        errors: Object
    },
    data: function () {
        return {
            transform: '',
            custom: '',
        };
    },
    created: function () {
        this.transform = this.displayer.options.transform;
        this.custom = this.displayer.options.custom;
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
                <label class="required">{{ t('Transform') }}</label>
            </div>
            <div class="input ltr">                    
                <div class="select">
                    <select :name="'displayers['+kind+'][options][transform]'" v-model="transform">
                        <option :value="handle" v-for="name, handle in displayer.imageTransforms">{{ name }}</option>
                        <option value="_custom">{{ t('Custom') }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errorList('transform')">
                <li v-for="error in errorList('transform')">{{ error }}</li>
            </ul>
        </div>
        <div class="field" v-if="transform == '_custom'">
            <div class="heading">
                <label class="required">{{ t('Custom') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="fullwidth text" :name="'displayers['+kind+'][options][custom]'" v-model="custom">
            </div>
            <div class="instructions">{{ t('Enter a json list of options to transform the image, example: { "width": 300, "height": 300 }') }}</div>
            <ul class="errors" v-if="errorList('custom')">
                <li v-for="error in errorList('custom')">{{ error }}</li>
            </ul>
        </div>
    </div>
    `
}

window.fileDisplayers['link'] = {
    props: {
        displayer: Object,
        kind: String,
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
        this.label = this.displayer.options.label;
        this.custom = this.displayer.options.custom;
        this.newTab = this.displayer.options.newTab;
        this.download = this.displayer.options.download;
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
                    <select :name="'displayers['+kind+'][options][label]'" v-model="label">
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
                <input type="text" class="fullwidth text" :name="'displayers['+kind+'][options][custom]'" v-model="custom">
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
                    <input type="hidden" :name="'displayers['+kind+'][options][newTab]'">
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
                    <input type="hidden" :name="'displayers['+kind+'][options][download]'">
                </button>
            </div>
        </div>
    </div>`
};