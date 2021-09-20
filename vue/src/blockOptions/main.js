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
        </div>`
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
        </div>`
    };

    e.detail['system-entry'] = {
        computed: {
            ...mapState(['theme'])
        },
        props: {
            block: Object
        },
        data: function () {
            return {
                entries: [],
                viewModes: []
            };
        },
        watch: {
            'block.options.type': {
                handler () {
                    this.$emit('updateOptions', {entry: '', viewMode: ''});
                    this.fetchEntries();
                    this.fetchViewModes();
                }
            }
        },
        created() {
            if (this.block.options.type) {
                this.fetchEntries();
                this.fetchViewModes();
            }
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
                axios.post(Craft.getCpUrl('themes/ajax/entries/'+this.block.options.type))
                .then((response) => {
                    this.entries = response.data.entries;
                })
                .catch((err) => {
                    this.handleError(err);
                });
            },
            fetchViewModes: function () {
                axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+this.theme+'/entry/'+this.block.options.type))
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
                <label>{{ t('Entry type') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {type: $event.target.value})" :value="block.options.type">
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
                <label>{{ t('Entry') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {entry: $event.target.value})" :value="block.options.entry">
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
                viewModes: []
            };
        },
        watch: {
            'block.options.group': {
                handler () {
                    this.$emit('updateOptions', {category: '', viewMode: ''});
                    this.fetchCategories();
                    this.fetchViewModes();
                }
            }
        },
        created() {
            if (this.block.options.group) {
                this.fetchCategories();
                this.fetchViewModes();
            }
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
                axios.post(Craft.getCpUrl('themes/ajax/categories/'+this.block.options.group))
                .then((response) => {
                    this.categories = response.data.categories;
                })
                .catch((err) => {
                    this.handleError(err);
                });
            },
            fetchViewModes: function () {
                axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+this.theme+'/category/'+this.block.options.group))
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
                    <select @input="$emit('updateOptions', {group: $event.target.value})" :value="block.options.group">
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
                <label>{{ t('Category') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {category: $event.target.value})" :value="block.options.category">
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
                viewModes: []
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
                <label>{{ t('User') }}</label>
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

    e.detail['system-global'] = {
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
        watch: {
            'block.options.set': {
                handler () {
                    this.fetchViewModes();
                }
            }
        },
        created() {
            if (this.block.options.set) {
                this.fetchViewModes();
            }
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
                axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+this.theme+'/global/'+this.block.options.set))
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
                <label>{{ t('Global set') }}</label>
            </div>
            <div class="input ltr">
                <div class="select">
                    <select @input="$emit('updateOptions', {set: $event.target.value})" :value="block.options.set">
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
});