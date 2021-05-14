import { createStore } from 'vuex';
import { cloneDeep, isEqual, merge } from 'lodash';

function sanitizeDisplays(displays) {
    return displays;
}

function handleError(err) {
    if (err.response) {
        Craft.cp.displayError(err.response.data.message);
    } else {
        Craft.cp.displayError(err);
    }
}

function setWindowUrl(theme, layout) {
    let url = document.location.pathname.split('/');
    let i = url.findIndex(e => e == 'display');
    if (i === -1) {
        return;
    }
    url[i+1] = theme;
    url[i+2] = layout;
    window.history.pushState({}, '', url.join('/'));
}

function cloneDisplay(display, viewMode) {
    display = cloneDeep(display);
    display.viewMode_id = viewMode.handle;
    display.id = null;
    display.item.id = null;
    if (display.item.type == 'matrix') {
        for (let i in display.item.types) {
            for (let j in display.item.types[i].fields) {
                display.item.types[i].fields[j].id = null;
            }
        }
    }
    return display;
}

const store = createStore({
    state () {
        return {
            theme: null,
            layout: {},
            layouts: [],
            allLayouts: {},
            isFetching: {},
            isSaving: false,
            hasChanged: false,
            viewModes: [],
            originalViewModes: [],
            viewMode: {},
            displays: [],
            originalDisplays: [],
            showOptionsModal: false,
            optionsField: {}
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
                    setWindowUrl(state.theme, id);
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
            state.viewMode = state.viewModes[index];
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
            for (let i in state.displays) {
                let display = state.displays[i];
                if (viewMode.id && viewMode.id === display.viewMode_id) {
                    toDelete.push(parseInt(i));
                }
                if (viewMode.handle === display.viewMode_id) {
                    toDelete.push(parseInt(i));   
                }
            }
            state.displays = state.displays.filter((display, index) => !toDelete.includes(index));
            state.viewModes = state.viewModes.filter((viewMode, index2) => index != index2);
            if (state.viewMode.handle == viewMode.handle) {
                state.viewMode = state.viewModes[0];
            }
        },
        setDisplays(state, displays) {
            state.displays = displays;
            state.originalDisplays = cloneDeep(displays);
        },
        addDisplay(state, display) {
            state.displays.push(display);
        },
        updateDisplay(state, {id, data}) {
            let display;
            for (let i in state.displays) {
                display = state.displays[i];
                if (display.id != id) {
                    continue;
                }
                display = merge(display, data);
            }
        },
        updateMatrixDisplay(state, {id, typeId, fieldId, data}) {
            let display, type, field;
            for (let i in state.displays) {
                display = state.displays[i];
                if (display.id != id) {
                    continue;
                }
                for(i in display.item.types) {
                    type = display.item.types[i];
                    if (type.type.id != typeId) {
                        continue;
                    }
                    for (i in type.fields) {
                        field = type.fields[i];
                        if (field.id != fieldId) {
                            continue;
                        }
                        field = merge(field, data);
                    }
                }
            }
        },
        setHasChanged(state, value) {
            state.hasChanged = value;
        },
        removeChanges(state) {
            for (let i in state.viewModes) {
                if (typeof state.viewModes[i].id == 'undefined') {
                    delete state.viewModes[i];
                }
            }
        },
        setIsSaving(state, value) {
            state.isSaving = value;
        },
        setOptionsField(state, value) {
            state.optionsField = value;
            state.showOptionsModal = true;
        },
        setShowOptionsModal(state, value) {
            state.showOptionsModal = value;  
        },
        updateOptions(state, value) {
            state.optionsField.options = value;
        }
    },
    actions: {
        setLayoutAndFetch ({commit, dispatch, state}, id) {
            commit('removeChanges');
            commit('setLayout', id);
            commit('setViewModes', state.layout.viewModes);
            commit('setViewMode', 0);
            dispatch('fetchDisplays');
        },
        fetchDisplays({state, commit}) {
            let data = {
                viewMode: state.viewMode
            };
            commit('setIsFetching', {key: 'displays', value: true});
            return axios.post(Craft.getCpUrl('themes/ajax/displays/'+state.layout.id), data)
            .then((response) => {
                commit('setDisplays', cloneDeep(response.data.displays));
            })
            .catch((err) => {
                handleError(err);
            })
            .finally(() => {
                commit('setIsFetching', {key: 'displays', value: false});
            });
        },
        checkChanges({state, commit}) {
            commit('setHasChanged', !isEqual(state.originalDisplays, state.displays) || !isEqual(state.originalViewModes, state.viewModes));
        },
        save({commit, state}) {
            commit('setIsSaving', true);
            axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/displays/save'),
                data: {displays: sanitizeDisplays(state.displays), layout: state.layout.id, viewModes: state.viewModes},
            })
            .then(res => {
                commit('setDisplays', cloneDeep(res.data.displays));
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
            commit('addViewMode', viewMode);
            for (let i in state.displays) {
                let display = state.displays[i];
                if (display.viewMode_id !== state.viewMode.id && display.viewMode_id !== state.viewMode.handle) {
                    continue;
                }
                display = cloneDisplay(display, viewMode);
                commit('addDisplay', display);
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