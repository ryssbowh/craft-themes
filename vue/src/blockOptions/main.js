import { mapState } from 'vuex';

document.addEventListener("register-block-option-components", function(e) {
    e.detail['system-template'] = {
        props: {
            block: Object
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
        template: `
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Template Path') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="text fullwidth" :value="block.options.template" @input="$emit('updateOptions', {template: $event.target.value})">
            </div>
            <ul class="errors" v-if="errors('template')">
                <li v-for="error in errors('template')">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['forms-search'] = {
        props: {
            block: Object
        },
        methods: {
            errors: function (field) {
                for (let i in this.block.errors.options ?? []) {
                    if (this.block.errors.options[i][field] ?? null) {
                        return this.block.errors.options[i][field];
                    }
                }
                return [];
            }
        },
        template: `
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Form action') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="text fullwidth" :value="block.options.action" @input="$emit('updateOptions', {action: $event.target.value})">
            </div>
            <ul class="errors" v-if="errors('action')">
                <li v-for="error in errors('action')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label class="required">{{ t('Search term name') }}</label>
            </div>
            <div class="input ltr">
                <input type="text" class="text fullwidth" :value="block.options.inputName" @input="$emit('updateOptions', {inputName: $event.target.value})">
            </div>
            <ul class="errors" v-if="errors('inputName')">
                <li v-for="error in errors('inputName')">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-twig'] = {
        props: {
            block: Object
        },
        template: `
        <div class="field">
            <div class="heading">
                <label>{{ t('Twig Code') }}</label>
            </div>
            <div class="input ltr">
                <textarea class="text fullwidth" rows="10" :value="block.options.twig" @input="$emit('updateOptions', {twig: $event.target.value})">
                </textarea>
            </div>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['forms-login'] = {
        props: {
            block: Object
        },
        mounted: function () {
            this.$nextTick(() => {
                Craft.initUiElements(this.$el);
                $(this.$el).find('.lightswitch').on('change', (e) => {
                    let options = {
                        onlyIfNotAuthenticated: $(e.target).hasClass('on')
                    };
                    this.$emit('updateOptions', options);
                });
            });
        },
        template: `
        <div class="field">
            <div class="heading">
                <label>{{ t('Show only if the user is not authenticated') }}</label>
            </div>
            <div class="input ltr">                    
                <button type="button" :class="{lightswitch: true, on: block.options.onlyIfNotAuthenticated}">
                    <div class="lightswitch-container">
                        <div class="handle"></div>
                    </div>
                    <input type="hidden" name="onlyIfNotAuthenticated" :value="block.options.onlyIfNotAuthenticated ? 1 : ''">
                </button>
            </div>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['forms-register'] = {...e.detail['forms-login']};

    e.detail['system-entry'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object
        },
        data: function () {
            return {
                type: null,
                entry: null,
                viewMode: null,
                entries: [],
                viewModes: []
            };
        },
        watch: {
            type: function () {
                this.entry = null;
                this.viewMode = null;
                this.$emit('updateOptions', {type: this.type});
                this.fetchEntries();
                this.fetchViewModes();
            },
            entry: function () {
                this.$emit('updateOptions', {entry: this.entry});
            },
            viewMode: function () {
                this.$emit('updateOptions', {viewMode: this.viewMode});
            }
        },
        created() {
            this.type = this.block.options.type;
            this.entry = this.block.options.entry;
            this.viewMode = this.block.options.viewMode;
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
            },
            fetchEntries: function () {
                axios.post(Craft.getCpUrl('themes/ajax/entries/'+this.type))
                .then((response) => {
                    this.entries = response.data.entries;
                })
                .catch((err) => {
                    this.handleError(err);
                });
            },
            fetchViewModes: function () {
                axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+this.theme+'/entry/'+this.type))
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
                <label>{{ t('Entry Type', {}, 'app') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select v-model="type">
                        <option v-for="type in block.entryTypes" :value="type.uid">{{ type.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('type')">
                <li v-for="error in errors('type')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Entry', {}, 'app') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select v-model="entry">
                        <option v-for="entry in entries" :value="entry.uid">{{ entry.title }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('entry')">
                <li v-for="error in errors('entry')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select v-model="viewMode">
                        <option v-for="viewMode in viewModes" :value="viewMode.uid">{{ viewMode.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('viewMode')">
                <li v-for="error in errors('viewMode')">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-category'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object
        },
        data: function () {
            return {
                categories: [],
                viewModes: [],
                group: null,
                viewMode: null,
                category: null
            };
        },
        watch: {
            group: function () {
                this.$emit('updateOptions', {group: this.group});
                this.fetchCategories();
                this.fetchViewModes();
            },
            viewMode: function () {
                this.$emit('updateOptions', {viewMode: this.viewMode});
            },
            category: function () {
                this.$emit('updateOptions', {category: this.category});
            }
        },
        created() {
            this.group = this.block.options.group;
            this.viewMode = this.block.options.viewMode;
            this.category = this.block.options.category;
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
            },
            fetchCategories: function () {
                axios.post(Craft.getCpUrl('themes/ajax/categories/'+this.group))
                .then((response) => {
                    this.categories = response.data.categories;
                })
                .catch((err) => {
                    this.handleError(err);
                });
            },
            fetchViewModes: function () {
                axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+this.theme+'/category/'+this.group))
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
                <label>{{ t('Group') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select v-model="group">
                        <option v-for="group in block.groups" :value="group.uid">{{ group.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('group')">
                <li v-for="error in errors('group')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('Category', {}, 'app') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select v-model="category">
                        <option v-for="category in categories" :value="category.uid">{{ category.title }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('category')">
                <li v-for="error in errors('category')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select v-model="viewMode">
                        <option v-for="viewMode in viewModes" :value="viewMode.uid">{{ viewMode.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('viewMode')">
                <li v-for="error in errors('viewMode')">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-user'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object
        },
        data: function () {
            return {
                users: [],
                viewModes: [],
            };
        },
        created() {
            this.fetchUsers();
            this.fetchViewModes();
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
            },
            fetchUsers: function () {
                axios.post(Craft.getCpUrl('themes/ajax/users'))
                .then((response) => {
                    this.users = response.data.users;
                })
                .catch((err) => {
                    this.handleError(err);
                });
            },
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
                <label>{{ t('User', {}, 'app') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {user: $event.target.value})" :value="block.options.user">
                        <option v-for="user in users" :value="user.uid">{{ user.title }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('user')">
                <li v-for="error in errors('user')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {viewMode: $event.target.value})" :value="block.options.viewMode">
                        <option v-for="viewMode in viewModes" :value="viewMode.uid">{{ viewMode.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('viewMode')">
                <li v-for="error in errors('viewMode')">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-current-user'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object
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
            },
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
                <label>{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select $emit('updateOptions', {viewMode: $event.target.value}) :value="viewMode">
                        <option v-for="viewMode in viewModes" :value="viewMode.uid">{{ viewMode.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('viewMode')">
                <li v-for="error in errors('viewMode')">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };

    e.detail['system-global'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object
        },
        data: function () {
            return {
                viewModes: [],
                set: null,
                viewMode: null
            };
        },
        watch: {
            set: function () {
                this.viewMode = null;
                this.$emit('updateOptions', {set: this.set});
                this.fetchViewModes();
            },
            viewMode: function () {
                this.$emit('updateOptions', {viewMode: this.viewMode});
            },
        },
        created() {
            this.set = this.block.options.set;
            this.viewMode = this.block.options.viewMode;
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
            },
            fetchViewModes: function () {
                axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+this.theme+'/global/'+this.set))
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
                <label>{{ t('Global Set', {}, 'app') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select v-model="set">
                        <option v-for="set in block.sets" :value="set.uid">{{ set.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('set')">
                <li v-for="error in errors('set')">{{ error }}</li>
            </ul>
        </div>
        <div class="field">
            <div class="heading">
                <label>{{ t('View mode') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select v-model="viewMode">
                        <option v-for="viewMode in viewModes" :value="viewMode.uid">{{ viewMode.name }}</option>
                    </select>
                </div>
            </div>
            <ul class="errors" v-if="errors('viewMode')">
                <li v-for="error in errors('viewMode')">{{ error }}</li>
            </ul>
        </div>`,
        emits: ['updateOptions']
    };
});