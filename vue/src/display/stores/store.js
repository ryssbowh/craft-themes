import { createStore } from 'vuex';
import { cloneDeep, isEqual, merge, filter } from 'lodash';

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

function cloneGroup(group, viewMode) {
    let display = cloneDisplay(group, viewMode);
    return axios({
        method: 'post',
        url: Craft.getCpUrl('themes/ajax/uid'),
        headers: {'X-CSRF-Token': Craft.csrfTokenValue}
    }).then((response) => {
        display.uid = response.data.uid;
        return display;
    });
}

function cloneDisplay(display, viewMode, groupMapping) {
    let oldGroup = display.group_id;
    display = cloneDeep(display);
    if (oldGroup) {
        display.group_id = groupMapping[oldGroup];
    }
    display.id = null;
    display.uid = null;
    display.viewMode_id = null;
    display.item.id = null;
    display.item.uid = null;
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
            isFetching: false,
            isSaving: false,
            hasChanges: false,
            viewMode: {},
            viewModes: [],
            originalViewModes: [],
            viewModeIndex: null,
            displays: [],
            showOptionsModal: false,
            showGroupModal: false,
            editedGroupUid: null,
            displayer: {},
            item: {},
            displayerOptionsErrors: {}
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
        setIsFetching(state, value) {
            state.isFetching = value;
        },
        setViewMode(state, index) {
            state.viewModeIndex = index;
            state.viewMode = state.viewModes[index];
            state.displays = state.viewMode.displays ?? {};
        },
        setViewModes(state, value) {
            state.viewModes = value;
            state.originalViewModes = cloneDeep(value);
            state.hasChanges = false;
        },
        addViewMode(state, viewMode) {
            state.viewModes.push(viewMode);
        },
        addDisplay(state, display) {
            state.viewModes[state.viewModeIndex].displays.push(display);
        },
        editViewMode(state, {index, name}) {
            state.viewModes[index].name = name;
        },
        deleteViewMode(state, index) {
            state.viewModes.splice(index, 1);
        },
        setDisplays(state, displays) {
            state.viewModes[state.viewModeIndex].displays = displays;
            state.displays = displays;
        },
        setHasChanges(state, value) {
            state.hasChanges = value;
        },
        updateDisplay(state, {id, data}) {
            let display;
            for (let i in state.displays) {
                display = state.displays[i];
                if (display.id != id) {
                    continue;
                }
                display = merge(display, data);
                break;
            }
        },
        addDisplayToGroup(state, {display, groupUid}) {
            for (let i in state.displays) {
                display = state.displays[i];
                if (display.uid != groupUid) {
                    continue;
                }
                display.item.displays.push(displays);
                break;
            }
        },
        removeDisplay(state, id) {
            state.displays = state.displays.filter(display => display.id != id);
        },
        setIsSaving(state, value) {
            state.isSaving = value;
        },
        openDisplayerOptions(state, {displayer, item}) {
            state.displayer = displayer;
            state.item = item;
            state.showOptionsModal = true;
        },
        setShowOptionsModal(state, value) {
            state.showOptionsModal = value;  
        },
        setShowGroupModal(state, {show, editUid = null}) {
            state.showGroupModal = show;
            state.editedGroupUid = editUid;  
        },
        setDisplayerOptionsError(state, value) {
            state.displayerOptionsErrors = value;
        },
        updateOptions(state, value) {
            state.item.options = value;
        }
    },
    actions: {
        setLayout({commit, dispatch, state}, id) {
            commit('setLayout', id);
            dispatch('fetchViewModes');
        },
        setViewMode({commit, dispatch, state}, id) {
            commit('setViewMode', id);
        },
        fetchViewModes({state, commit}) {
            commit('setIsFetching', true);
            return axios.post(Craft.getCpUrl('themes/ajax/view-modes'), {layoutId: state.layout.id})
            .then((response) => {
                commit('setViewModes', response.data.viewModes);
                commit('setViewMode', 0);
            })
            .catch((err) => {
                handleError(err);
            })
            .finally(() => {
                commit('setIsFetching', false);
            });
        },
        save({commit, state}) {
            commit('setIsSaving', true);
            axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/view-modes/save'),
                data: {
                    layoutId: state.layout.id,
                    viewModes: state.viewModes
                },
            })
            .then(res => {
                commit('setViewModes', res.data.viewModes);
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
            viewMode.layout_id = state.layout.id;
            let displays = filter(state.displays, (d) => d.viewMode_id === state.viewMode.id || d.viewMode_id === state.viewMode.handle);
            let groups = filter(displays, (d) => d.type == 'group');
            let fields = filter(displays, (d) => d.type == 'field');
            let newDisplays = [];
            let promises = [];
            let groupMapping = {};
            //Cloning all groups first, each cloning will be a promise. 
            //Builds a mapping between old groups and new groups so that new fields are in the right groups
            for (let i in groups) {
                promises.push(cloneGroup(groups[i], viewMode).then((display) => {
                    newDisplays.push(display);
                    groupMapping[groups[i].id ?? groups[i].uid] = display.uid;
                }));
            }
            //When all groups are cloned, clone all fields
            return new Promise((resolve, reject) => {
                Promise.all(promises).then(() => {
                    for (let i in fields) {
                        newDisplays.push(cloneDisplay(fields[i], viewMode, groupMapping));
                    }
                    viewMode.displays = newDisplays;
                    commit('addViewMode', viewMode);
                    commit('setViewMode', state.viewModes.length - 1);
                    resolve();
                });
            });
        },
        editViewMode({commit, dispatch}, args) {
            commit('editViewMode', args);
        },
        deleteViewMode({commit, dispatch}, index) {
            commit('deleteViewMode', index);
            commit('setViewMode', 0);
        },
        updateOptions({commit, dispatch}, value) {
            commit('updateOptions', value);
        },
        checkChanges({state, commit}) {
            commit('setHasChanges', !isEqual(state.originalViewModes, state.viewModes));
        },
    }
});

export {store};