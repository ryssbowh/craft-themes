import { mapState } from 'vuex';
import SelectInput from './SelectInput';

document.addEventListener("register-block-option-components", function(e) {
    e.detail['system-template'] = {
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        template: `
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Template Path') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="text fullwidth" :value="options.template" @input="$emit('updateOptions', {template: $event.target.value})">
            </div>
            <ul class="errors" v-if="errors.template">
                <li v-for="error in errors.template">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['forms-search'] = {
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        template: `
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Form action') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="text fullwidth" :value="options.action" @input="$emit('updateOptions', {action: $event.target.value})">
            </div>
            <ul class="errors" v-if="errors.action">
                <li v-for="error in errors.action">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Search term name') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="text fullwidth" :value="options.inputName" @input="$emit('updateOptions', {inputName: $event.target.value})">
            </div>
            <ul class="errors" v-if="errors.inputName">
                <li v-for="error in errors.inputName">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-twig'] = {
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        template: `
        <div class="field">
            <div class="heading">
                <label>{{ t('Twig Code') }}</label>
            </div>
            <div class="input ltr">
                <textarea class="text fullwidth" rows="10" :value="options.twig" @input="$emit('updateOptions', {twig: $event.target.value})">
                </textarea>
            </div>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['forms-login'] = {
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        template: `
        <div class="field">
            <div class="heading">
                <label>{{ t('Show only if the user is not authenticated') }}</label>
            </div>
            <lightswitch :on="options.onlyIfNotAuthenticated" @change="$emit('updateOptions', {onlyIfNotAuthenticated: $event})">
            </lightswitch>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['forms-register'] = {...e.detail['forms-login']};

    e.detail['system-entry'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        data: function () {
            return {
                selector: null,
            };
        },
        watch: {
            'options.entries': function () {
                this.$emit('updateOptions', this.options);
            }
        },
        mounted() {
            this.createSelector();
        },
        methods: {
            createSelector: function () {
                this.selector = new SelectInput({
                    actionUrl: 'themes/cp-ajax/entries-data',
                    id: 'field-entries',
                    elementType: 'craft\\elements\\Entry',
                    sources: '*',
                    viewMode: 'small',
                    theme: this.theme,
                    selectable: 0,
                    createElementCallback: this.createElement,
                    initialIds: Object.keys(this.options.entries).map((i) => {
                        return this.options.entries[i].id;
                    })
                });
                this.selector.on('selectElements', () => {
                    this.updateElements(Craft.BaseElementSelectInput.ADD_FX_DURATION);
                });
                this.selector.on('removeElements', () => {
                    this.updateElements(Craft.BaseElementSelectInput.REMOVE_FX_DURATION);
                });
            },
            updateElements: function (waitTime) {
                //Need to wait on Garnish transition to finish or data will be wrong
                setTimeout(() => {
                    this.options.entries = this.selector.getSelectedElementData();
                }, waitTime + 50);
            },
            createElement: function (entry) {
                return {
                    $element: $(`
                    <div class="element small hasstatus"
                        data-type="craft\\elements\\Entry"
                        data-id="`+entry.id+`"
                        data-label="`+entry.title+`"
                        title="`+entry.title+`"
                    >
                        <span class="status ` + entry.status + `"></span>
                        <div class="label">
                            <span class="title">`+entry.title+`</span>
                        </div>
                    </div>`),
                    id: entry.id,
                    viewModes: entry.viewModes,
                    viewMode: this.options.entries.filter((e) => e.id == entry.id)[0].viewMode ?? null
                };
            },
        },
        template: `
        <div class="field">
            <div class="heading" style="display:flex;justify-content:space-between">
                <label class="required">{{ t('Assets', {}, 'app') }}</label>
                <label class="required">{{ t('View Mode') }}</label>
            </div>
            <div class="input ltr">
                <div id="field-entries" class="elementselect">
                    <div class="elements">
                    </div>
                    <div class="flex">
                        <button type="button" class="btn add icon dashed">{{ t('Add an entry', {}, 'app') }}</button>
                    </div>
                </div>
            </div>
            <ul class="errors" v-if="errors.entries">
                <li v-for="error in errors.entries">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-category'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        data: function () {
            return {
                selector: null
            };
        },
        watch: {
            'options.categories': function () {
                this.$emit('updateOptions', this.options);
            }
        },
        mounted() {
            this.createSelector();
        },
        methods: {
            createSelector: function () {
                this.selector = new SelectInput({
                    actionUrl: 'themes/cp-ajax/categories-data',
                    id: 'field-categories',
                    elementType: 'craft\\elements\\Category',
                    sources: '*',
                    viewMode: 'small',
                    branchLimit: 1,
                    theme: this.theme,
                    selectable: 0,
                    createElementCallback: this.createElement,
                    initialIds: Object.keys(this.options.categories).map((i) => {
                        return this.options.categories[i].id;
                    })
                });
                this.selector.on('selectElements', () => {
                    this.updateElements(Craft.BaseElementSelectInput.ADD_FX_DURATION);
                });
                this.selector.on('removeElements', () => {
                    this.updateElements(Craft.BaseElementSelectInput.REMOVE_FX_DURATION);
                });
            },
            createElement: function (category) {
                return {
                    $element: $(`
                    <div class="element small hasstatus"
                        data-type="craft\\elements\\Category"
                        data-id="`+category.id+`"
                        data-label="`+category.title+`"
                        title="`+category.title+`"
                    >
                        <span class="status ` + category.status + `"></span>
                        <div class="label">
                            <span class="title">`+category.title+`</span>
                        </div>
                    </div>`),
                    id: category.id,
                    viewModes: category.viewModes,
                    viewMode: this.options.categories.filter((e) => e.id == category.id)[0].viewMode ?? null
                };
            },
            updateElements: function (waitTime) {
                //Need to wait on Garnish transition to finish or data will be wrong
                setTimeout(() => {
                    this.options.categories = this.selector.getSelectedElementData();
                }, waitTime + 50);
            },
        },
        template: `
        <div class="field">
            <div class="heading" style="display:flex;justify-content:space-between">
                <label class="required">{{ t('Category', {}, 'app') }}</label>
                <label class="required">{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div id="field-categories" class="categoriesfield">
                    <div class="elements">
                    </div>
                    <div class="flex">
                        <button type="button" class="btn add icon dashed">{{ t('Add a category') }}</button>
                    </div>
                </div>
            </div>
            <ul class="errors" v-if="errors.categories">
                <li v-for="error in errors.categories">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-asset'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        data: function () {
            return {
                selector: null
            };
        },
        watch: {
            'options.assets': function () {
                this.$emit('updateOptions', this.options);
            }
        },
        mounted() {
            this.createSelector();
        },
        methods: {
            createSelector: function () {
                this.selector = new SelectInput({
                    actionUrl: 'themes/cp-ajax/assets-data',
                    id: 'field-assets',
                    elementType: 'craft\\elements\\Asset',
                    sources: '*',
                    viewMode: 'small',
                    theme: this.theme,
                    branchLimit: 1,
                    selectable: 0,
                    createElementCallback: this.createElement,
                    initialIds: Object.keys(this.options.assets).map((i) => {
                        return this.options.assets[i].id;
                    })
                });
                this.selector.on('selectElements', () => {
                    this.updateElements(Craft.BaseElementSelectInput.ADD_FX_DURATION);
                });
                this.selector.on('removeElements', () => {
                    this.updateElements(Craft.BaseElementSelectInput.REMOVE_FX_DURATION);
                });
            },
            updateElements: function (waitTime) {
                //Need to wait on Garnish transition to finish or data will be wrong
                setTimeout(() => {
                    this.options.assets = this.selector.getSelectedElementData();
                }, waitTime + 50);
            },
            createElement: function (asset) {
                return {
                    $element: $(`
                    <div class="element small hasthumb"
                        data-type="craft\\elements\\Asset"
                        data-id="`+asset.id+`"
                        data-label="`+asset.title+`"
                        title="`+asset.title+`"
                    >
                        <div class="elementthumb">
                            <img sizes="34px" srcset="`+asset.srcset+`" alt="">
                        </div>
                        <div class="label">
                            <span class="title">`+asset.title+`</span>
                        </div>
                    </div>`),
                    id: asset.id,
                    viewModes: asset.viewModes,
                    viewMode: this.options.assets.filter((e) => e.id == asset.id)[0].viewMode ?? null
                };
            }
            
        },
        template: `
        <div class="field">
            <div class="heading" style="display:flex;justify-content:space-between">
                <label class="required">{{ t('Assets', {}, 'app') }}</label>
                <label class="required">{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div id="field-assets" class="elementselect">
                    <div class="elements">
                    </div>
                    <div class="flex">
                        <button type="button" class="btn add icon dashed">{{ t('Add an asset', {}, 'app') }}</button>
                    </div>
                </div>
            </div>
            <ul class="errors" v-if="errors.assets">
                <li v-for="error in errors.assets">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-user'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        data: function () {
            return {
                selector: null
            };
        },
        watch: {
            'options.users': function () {
                this.$emit('updateOptions', this.options);
            }
        },
        mounted() {
            this.createSelector();
        },
        methods: {
            createSelector: function () {
                this.selector = new SelectInput({
                    actionUrl: 'themes/cp-ajax/users-data',
                    id: 'field-users',
                    elementType: 'craft\\elements\\User',
                    sources: '*',
                    viewMode: 'small',
                    theme: this.theme,
                    selectable: 0,
                    createElementCallback: this.createElement,
                    initialIds: Object.keys(this.options.users).map((i) => {
                        return this.options.users[i].id;
                    })
                });
                this.selector.on('selectElements', () => {
                    this.updateElements(Craft.BaseElementSelectInput.ADD_FX_DURATION);
                });
                this.selector.on('removeElements', () => {
                    this.updateElements(Craft.BaseElementSelectInput.REMOVE_FX_DURATION);
                });
            },
            updateElements: function (waitTime) {
                //Need to wait on Garnish transition to finish or data will be wrong
                setTimeout(() => {
                    this.options.users = this.selector.getSelectedElementData();
                }, waitTime + 50);
            },
            createElement: function (user) {
                return {
                    $element: $(`
                    <div class="element small hasstatus hasthumb"
                        data-type="craft\\elements\\User"
                        data-id="`+user.id+`"
                        data-status="`+user.status+`"
                        data-label="`+user.name+`"
                        title="`+user.name+`"
                    >
                        <span class="status `+user.status+`"></span>
                        <div class="elementthumb rounded">
                            <img sizes="34px" srcset="`+user.srcset+`" alt="">
                        </div>
                        <div class="label">
                            <span class="title">`+user.name+`</span>
                        </div>
                    </div>`),
                    id: user.id,
                    viewModes: user.viewModes,
                    viewMode: this.options.users.filter((e) => e.id == user.id)[0].viewMode ?? null
                };
            }
        },
        template: `
        <div class="field">
            <div class="heading" style="display:flex;justify-content:space-between">
                <label class="required">{{ t('Users', {}, 'app') }}</label>
                <label class="required">{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div id="field-users" class="elementselect">
                    <div class="elements">
                    </div>
                    <div class="flex">
                        <button type="button" class="btn add icon dashed">{{ t('Add a user', {}, 'app') }}</button>
                    </div>
                </div>
            </div>
            <ul class="errors" v-if="errors.users">
                <li v-for="error in errors.users">{{ error }}</li>
            </ul>
        </div>
        `,
        emits: ['updateOptions']
    };

    e.detail['system-current-user'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        data: function () {
            return {
                viewModes: []
            };
        },
        created() {
            this.fetchViewModes();
        },
        methods: {
            fetchViewModes: function () {
                axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+this.theme+'/user'))
                .then((response) => {
                    this.viewModes = response.data.viewModes;
                })
                .catch((err) => {
                    this.handleError(err);
                });
            }
        },
        template: `
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {viewMode: $event.target.value})" :value="options.viewMode">
                        <option v-for="viewMode in viewModes" :value="viewMode.uid">{{ viewMode.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors.viewMode">
                <li v-for="error in errors.viewMode">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-global'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        data: function () {
            return {
                viewModes: [],
            };
        },
        watch: {
            'options.set': function () {
                this.fetchViewModes();
            },
        },
        created() {
            this.fetchViewModes();
        },
        methods: {
            fetchViewModes: function () {
                axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+this.theme+'/global/'+this.options.set))
                .then((response) => {
                    this.viewModes = response.data.viewModes;
                })
                .catch((err) => {
                    this.handleError(err);
                });
            }
        },
        template: `
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Global Set', {}, 'app') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {set: $event.target.value, viewMode: null})" :value="options.set">
                        <option v-for="set in block.sets" :value="set.uid">{{ set.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors.set">
                <li v-for="error in errors.set">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {viewMode: $event.target.value})" :value="options.viewMode">
                        <option v-for="viewMode in viewModes" :value="viewMode.uid">{{ viewMode.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors.viewMode">
                <li v-for="error in errors.viewMode">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-flash-messages'] = {
        props: {
            block: Object,
            errors: Object,
            options: Object
        },
        template: `
        <div class="field">
            <div class="heading">
                <label>{{ t('Remove messages from session') }}</label>
            </div>
            <lightswitch :on="options.removeMessages" @change="$emit('updateOptions', {removeMessages: $event})">
            </lightswitch>
        </div>`,
        emits: ['updateOptions']
    };
});