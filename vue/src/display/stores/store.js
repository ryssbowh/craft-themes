import { createStore } from 'vuex';
import { cloneDeep, isEqual, merge, has, filter } from 'lodash';
import { v4 as uuidv4 } from 'uuid';

function handleError(err) {
    if (err.response) {
        Craft.cp.displayError(err.response.data.message);
    } else {
        Craft.cp.displayError(err);
    }
}

function setWindowUrl(theme, layout, viewMode) {
    let url = document.location.pathname.split('/');
    let i = url.findIndex(e => e == 'display');
    if (i === -1) {
        return;
    }
    if (url[i+1] == theme && url[i+2] == layout && url[i+3] == viewMode) {
        return;
    }
    url[i+1] = theme;
    url[i+2] = layout;
    url[i+3] = viewMode;
    window.history.pushState({}, '', url.join('/'));
}

function cloneDisplay(oldDisplay) {
    let display = cloneDeep(oldDisplay);
    display.id = null;
    display.uid = uuidv4();
    display.viewMode_id = null;
    display.item.id = null;
    display.item.uid = null;
    if (display.type == 'group') {
        let groupDisplays = [];
        for (let i in display.item.displays) {
            groupDisplays.push(cloneDisplay(display.item.displays[i]));
        }
        display.item.displays = groupDisplays;
    }
    if (has(window.cloneField, display.item.type)) {
        window.cloneField[display.item.type](oldDisplay, display);
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
            viewModes: [],
            originalViewModes: [],
            viewMode: null,
            showOptionsModal: false,
            showGroupModal: false,
            editedGroupUid: null,
            displayer: {},
            itemOptionsEdited: {},
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
        setViewMode(state, viewMode) {
            state.viewMode = viewMode;
        },
        setViewModes(state, value) {
            state.viewModes = value;
            state.originalViewModes = cloneDeep(value);
            state.hasChanges = false;
        },
        addViewMode(state, viewMode) {
            state.viewModes.push(viewMode);
        },
        editViewMode(state, {originalHandle, name, handle}) {
            for (let i in state.viewModes) {
                if (state.viewModes[i].handle == originalHandle) {
                    state.viewModes[i].name = name;
                    state.viewModes[i].handle = handle;
                    return;
                }
            }
        },
        deleteViewMode(state, viewMode) {
            state.viewModes = filter(state.viewModes, (mode) => mode.handle != viewMode.handle);
        },
        setDisplays(state, displays) {
            state.viewMode.displays = displays;
        },
        setHasChanges(state, value) {
            state.hasChanges = value;
        },
        updateDisplay(state, {uid, data}) {
            let display;
            for (let i in state.viewMode.displays) {
                display = state.viewMode.displays[i];
                if (display.uid != uid) {
                    continue;
                }
                display = merge(display, data);
                break;
            }
        },
        removeDisplay(state, display) {
            let displays = state.viewMode.displays.filter(display2 => display2.uid != display.uid);
            state.viewMode.displays = displays;
        },
        addDisplay(state, display) {
            state.viewMode.displays.push(display);
        },
        removeDisplayFromGroup(state, {display, groupUid}) {
            let group;
            for (let i in state.viewMode.displays) {
                group = state.viewMode.displays[i];
                if (group.uid != groupUid) {
                    continue;
                }
                break;
            }
            group.item.displays = group.item.displays.filter(display2 => display2.uid != display.uid);
        },
        addDisplayToGroup(state, {display, groupUid}) {
            let group;
            for (let i in state.viewMode.displays) {
                group = state.viewMode.displays[i];
                if (group.uid != groupUid) {
                    continue;
                }
                break;
            }
            group.item.displays.push(display);
        },
        setIsSaving(state, value) {
            state.isSaving = value;
        },
        openDisplayerOptions(state, {displayer, item}) {
            state.displayer = displayer;
            state.itemOptionsEdited = item;
            state.showOptionsModal = true;
        },
        resetDisplayerOptions(state) {
            state.displayer = {};
            state.itemOptionsEdited = {};
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
            state.itemOptionsEdited.options = value;
        }
    },
    actions: {
        setLayout({commit, dispatch}, {layoutId, viewModeHandle}) {
            commit('setLayout', layoutId);
            dispatch('fetchViewModes', viewModeHandle);
        },
        setViewModeByHandle({state, dispatch}, handle) {
            for (let i in state.viewModes) {
                if (state.viewModes[i].handle == handle) {
                    dispatch('setViewMode', state.viewModes[i]);
                    break;
                }
            }
        },
        setViewMode({commit, state}, viewMode) {
            commit('setViewMode', viewMode);
            setWindowUrl(state.theme, state.layout.id, viewMode.handle);
        },
        fetchViewModes({state, commit, dispatch}, viewModeHandle) {
            commit('setIsFetching', true);
            return axios.post(Craft.getCpUrl('themes/ajax/view-modes'), {layoutId: state.layout.id})
            .then((response) => {
                commit('setViewModes', response.data.viewModes);
                if (viewModeHandle) {
                    dispatch('setViewModeByHandle', viewModeHandle);
                } else {
                    dispatch('setViewMode', state.viewModes[0]);
                }
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
        editViewMode({state, commit}, {originalHandle, name, handle}) {
            if (originalHandle != handle) {
                setWindowUrl(state.theme, state.layout.id, handle);
            }
            commit('editViewMode', {originalHandle: originalHandle, name: name, handle: handle});
        },
        addViewMode({commit, state, dispatch}, viewMode) {
            viewMode.layout_id = state.layout.id;
            let newDisplays = [];
            let display;
            for (let i in state.viewMode.displays) {
                display = state.viewMode.displays[i];
                newDisplays.push(cloneDisplay(display));
            }
            viewMode.displays = newDisplays;
            commit('addViewMode', viewMode);
            dispatch('setViewMode', viewMode);
        },
        deleteViewMode({commit, state}, viewMode) {
            commit('deleteViewMode', viewMode);
            commit('setViewMode', state.viewModes[0]);
        },
        checkChanges({state, commit}) {
            commit('setHasChanges', !isEqual(state.originalViewModes, state.viewModes));
        },
    }
});

export {store};