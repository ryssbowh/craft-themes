import { createStore } from 'vuex';
import axios from 'axios';
import { cloneDeep, isEqual } from 'lodash';

function sanitizeFields(fields) {
    return fields;
}

function handleError(err) {
    if (err.response) {
        Craft.cp.displayError(err.response.data.message);
    } else {
        Craft.cp.displayError(err);
    }
}

const store = createStore({
    state () {
        return {
            theme: null,
            layout: 0,
            layouts: [],
            allLayouts: {},
            isFetching: {},
            isSaving: false,
            hasChanged: false,
            viewModes: [],
            originalViewModes: [],
            viewMode: 0,
            fields: [],
            originalFields: [],
            optionsHtml: {}
        }
    },
    mutations: {
        setTheme (state, value) {
            state.theme = value;
            state.layouts = state.allLayouts[value];
        },
        setLayout (state, id) {
            for (let i in state.layouts) {
                if (state.layouts[i].id == id) {
                    state.layout = state.layouts[i];
                    break;
                }
            }
        },
        setAllLayouts (state, value) {
            state.allLayouts = value;
        },
        setIsFetching(state, {key, value}) {
            state.isFetching[key] = value;
        },
        setViewModes(state, value) {
            state.viewModes = value;
            state.originalViewModes = cloneDeep(value);
        },
        setViewMode(state, index) {
            state.viewMode = index;
        },
        addViewMode(state, viewMode) {
            state.viewModes.push(viewMode);
        },
        editViewMode(state, {index, name}) {
            state.viewModes[index].name = name;
        },
        deleteViewMode(state, index) {
            let viewMode = state.viewModes[index];
            let toDelete = [];
            for (let i in state.fields) {
                let field = state.fields[i];
                if (viewMode.id && viewMode.id === field.viewMode) {
                    toDelete.push(i);
                }
                if (viewMode.handle === field.viewMode) {
                    toDelete.push(i);   
                }
            }
            state.fields = state.fields.filter((field, index) => !toDelete.includes(index));
            state.viewModes = state.viewModes.filter((viewMode, index2) => index != index2);
            if (state.viewMode == index) {
                state.viewMode = 0;
            }
        },
        setFields(state, fields) {
            state.fields = fields;
            state.originalFields = cloneDeep(fields);
        },
        addField(state, field) {
            state.fields.push(field);
        },
        updateField(state, {id, data}) {
            for (let i in state.fields) {
                if (state.fields[i].id == id) {
                    for (let j in data) {
                        state.fields[i][j] = data[j];
                    }
                    return;
                }
            }
        },
        setOptionsHtml(state, {id, html}) {
            state.optionsHtml[id] = html;
        },
        setHasChanged(state, value) {
            state.hasChanged = value;
        },
        setIsSaving(state, value) {
            state.isSaving = value;
        }
    },
    actions: {
        setThemeAndFetch ({commit, dispatch}, theme) {
            commit('setTheme', theme);
            dispatch('fetchViewModes');
            dispatch('fetchFields');
        },
        setLayoutAndFetch ({commit, dispatch}, id) {
            commit('setLayout', id);
            dispatch('fetchViewModes');
            dispatch('fetchFields');
        },
        fetchViewModes({state, commit}) {
            let data = {};
            data[Craft.csrfTokenName] = Craft.csrfTokenValue;
            commit('setIsFetching', {key: 'viewModes', value: true});
            return axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+state.layout.id), data)
            .then((response) => {
                commit('setViewModes', response.data.viewModes);
                commit('setViewMode', 0);
            })
            .catch((err) => {
                handleError(err);
            })
            .finally(() => {
                commit('setIsFetching', {key: 'viewModes', value: false});
            });
        },
        fetchFields({state, commit}) {
            let data = {
                viewMode: state.viewModes[state.viewMode]
            };
            data[Craft.csrfTokenName] = Craft.csrfTokenValue;
            commit('setIsFetching', {key: 'fields', value: true});
            return axios.post(Craft.getCpUrl('themes/ajax/fields/'+state.layout.id), data)
            .then((response) => {
                commit('setFields', cloneDeep(response.data.fields));
            })
            .catch((err) => {
                handleError(err);
            })
            .finally(() => {
                commit('setIsFetching', {key: 'fields', value: false});
            });
        },
        checkChanges({state, commit}) {
            commit('setHasChanged', !isEqual(state.originalFields, state.fields) || !isEqual(state.originalViewModes, state.viewModes));
        },
        save({commit, state}) {
            commit('setIsSaving', true);
            axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/fields/save'),
                data: {fields: sanitizeFields(state.fields), layout: state.layout.id, viewModes: state.viewModes},
                headers: {'X-CSRF-Token': Craft.csrfTokenValue}
            })
            .then(res => {
                commit('setFields', cloneDeep(res.data.fields));
                commit('setViewModes', res.data.viewModes);
                commit('setHasChanged', false);
                Craft.cp.displayNotice(res.data.message);
            })
            .catch(err => {
                handleError(err);
            })
            .finally(() => {
                commit('setIsSaving', false);
            })
        },
        addViewMode({commit, state, dispatch}, viewMode) {
            let current = state.viewModes[state.viewMode];
            commit('addViewMode', viewMode);
            for (let i in state.fields) {
                let field = state.fields[i];
                if (field.viewMode !== current.id && field.viewMode !== current.handle) {
                    continue;
                }
                field = cloneDeep(field);
                field.viewMode = viewMode.handle;
                field.id = null;
                commit('addField', field);
            }
            commit('setViewMode', state.viewModes.length - 1);
            dispatch('checkChanges');
        },
        editViewMode({commit, dispatch}, args) {
            commit('editViewMode', args);
            dispatch('checkChanges');
        },
        deleteViewMode({commit, dispatch}, index) {
            commit('deleteViewMode', index);
            dispatch('checkChanges');
        }
    }
});

export {store};