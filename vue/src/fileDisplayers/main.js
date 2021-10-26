document.addEventListener("register-file-displayers-components", function(e) {

    e.detail['raw'] = {
        props: {
            displayer: Object,
            kind: String,
            errors: Object
        },
        template: `
        <div class="warning with-icon">
            {{ t("This could be used to run potentially dangerous code on your site, do you trust the data you're going to display ?") }}
        </div>
        `
    };

    e.detail['iframe'] = {
        props: {
            displayer: Object,
            kind: String,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Width') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="displayer.options.width" @input="$emit('updateOptions', {width: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.width">
                    <li v-for="error in errors.width">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Height') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="displayer.options.height" @input="$emit('updateOptions', {height: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.height">
                    <li v-for="error in errors.height">{{ error }}</li>
                </ul>
            </div>
        </div>
        `
    }

    e.detail['html_video'] = {
        props: {
            displayer: Object,
            kind: String,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Width') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="displayer.options.width" @input="$emit('updateOptions', {width: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.width">
                    <li v-for="error in errors.width">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Height') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="displayer.options.height" @input="$emit('updateOptions', {height: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.height">
                    <li v-for="error in errors.height">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Show controls') }}</label>
                </div>
                <lightswitch :on="displayer.options.controls" @change="$emit('updateOptions', {controls: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Muted') }}</label>
                </div>
                <lightswitch :on="displayer.options.muted" @change="$emit('updateOptions', {muted: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Autoplay') }}</label>
                </div>
                <lightswitch :on="displayer.options.autoplay" @change="$emit('updateOptions', {autoplay: $event})">
                </lightswitch>
            </div>
        </div>
        `
    }

    e.detail['html_audio'] = {
        props: {
            displayer: Object,
            kind: String,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Show controls') }}</label>
                </div>
                <lightswitch :on="displayer.options.controls" @change="$emit('updateOptions', {controls: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Muted') }}</label>
                </div>
                <lightswitch :on="displayer.options.muted" @change="$emit('updateOptions', {muted: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Autoplay') }}</label>
                </div>
                <lightswitch :on="displayer.options.autoplay" @change="$emit('updateOptions', {autoplay: $event})">
                </lightswitch>
            </div>
        </div>
        `
    }

    e.detail['image_transform'] = {
        props: {
            displayer: Object,
            kind: String,
            errors: Object
        },
        emits: ['updateOptions'],
        template: `
        <div>
            <div class="field">
                <div class="heading">
                    <label class="required">{{ t('Transform') }}</label>
                </div>
                <div class="input ltr">                    
                    <div class="select">
                        <select v-model="displayer.options.transform" @input="$emit('updateOptions', {transform: $event.target.value})">
                            <option :value="handle" v-for="name, handle in displayer.imageTransforms">{{ name }}</option>
                            <option value="_custom">{{ t('Custom') }}</option>
                        </select>
                    </div>
                </div>
                <ul class="errors" v-if="errors.transform">
                    <li v-for="error in errors.transform">{{ error }}</li>
                </ul>
            </div>
            <div class="field" v-if="displayer.options.transform == '_custom'">
                <div class="heading">
                    <label class="required">{{ t('Custom') }}</label>
                </div>
                <div class="instructions">{{ t('Enter a json list of options to transform the image, example: { "width": 300, "height": 300 }') }}</div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="displayer.options.custom" @input="$emit('updateOptions', {custom: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.custom">
                    <li v-for="error in errors.custom">{{ error }}</li>
                </ul>
            </div>
            <div class="field" v-if="displayer.options.transform == '_custom'">
                <div class="heading">
                    <label>{{ t('Sizes') }}</label>
                </div>
                <div class="instructions">{{ t('Enter a json list of options to generate different sizes (srcset), example: ["1.5x", "2x", "3x"]') }}</div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="displayer.options.sizes" @input="$emit('updateOptions', {sizes: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.sizes">
                    <li v-for="error in errors.sizes">{{ error }}</li>
                </ul>
            </div>
        </div>
        `
    }

    e.detail['link'] = {
        props: {
            displayer: Object,
            kind: String,
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
                        <select v-model="displayer.options.label" @input="$emit('updateOptions', {label: $event.target.value})">
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
            <div class="field" v-if="displayer.options.label == 'custom'">
                <div class="heading">
                    <label class="required">{{ t('Custom') }}</label>
                </div>
                <div class="input ltr">
                    <input type="text" class="fullwidth text" :value="displayer.options.custom" @input="$emit('updateOptions', {custom: $event.target.value})">
                </div>
                <ul class="errors" v-if="errors.custom">
                    <li v-for="error in errors.custom">{{ error }}</li>
                </ul>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Open in new tab') }}</label>
                </div>
                <lightswitch :on="displayer.options.newTab" @change="$emit('updateOptions', {newTab: $event})">
                </lightswitch>
            </div>
            <div class="field">
                <div class="heading">
                    <label>{{ t('Download link') }}</label>
                </div>
                <lightswitch :on="displayer.options.download" @change="$emit('updateOptions', {download: $event})">
                </lightswitch>
            </div>
        </div>`
    };
});