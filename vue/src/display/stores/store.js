import { createStore } from 'vuex';
import { cloneDeep, isEqual, merge, has } from 'lodash';
import { v4 as uuidv4 } from 'uuid';

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
            viewModeIndex: null,
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
        },
        setViewModes(state, value) {
            state.viewModes = value;
            state.originalViewModes = cloneDeep(value);
            state.hasChanges = false;
        },
        addViewMode(state, viewMode) {
            state.viewModes.push(viewMode);
        },
        editViewMode(state, {index, name}) {
            state.viewModes[index].name = name;
        },
        deleteViewMode(state, index) {
            state.viewModes.splice(index, 1);
        },
        setDisplays(state, displays) {
            state.viewModes[state.viewModeIndex].displays = displays;
        },
        setHasChanges(state, value) {
            state.hasChanges = value;
        },
        updateDisplay(state, {uid, data}) {
            let display;
            for (let i in state.viewModes[state.viewModeIndex].displays) {
                display = state.viewModes[state.viewModeIndex].displays[i];
                if (display.uid != uid) {
                    continue;
                }
                display = merge(display, data);
                break;
            }
        },
        removeDisplay(state, display) {
            let displays = state.viewModes[state.viewModeIndex].displays.filter(display2 => display2.uid != display.uid);
            state.viewModes[state.viewModeIndex].displays = displays;
        },
        addDisplay(state, display) {
            state.viewModes[state.viewModeIndex].displays.push(display);
        },
        removeDisplayFromGroup(state, {display, groupUid}) {
            let group;
            for (let i in state.viewModes[state.viewModeIndex].displays) {
                group = state.viewModes[state.viewModeIndex].displays[i];
                if (group.uid != groupUid) {
                    continue;
                }
                break;
            }
            group.item.displays = group.item.displays.filter(display2 => display2.uid != display.uid);
        },
        addDisplayToGroup(state, {display, groupUid}) {
            let group;
            for (let i in state.viewModes[state.viewModeIndex].displays) {
                group = state.viewModes[state.viewModeIndex].displays[i];
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
        setLayout({commit, dispatch}, id) {
            commit('setLayout', id);
            dispatch('fetchViewModes');
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
        addViewMode({commit, state}, viewMode) {
            viewMode.layout_id = state.layout.id;
            let newDisplays = [];
            let display;
            for (let i in state.viewModes[state.viewModeIndex].displays) {
                display = state.viewModes[state.viewModeIndex].displays[i];
                newDisplays.push(cloneDisplay(display));
            }
            viewMode.displays = newDisplays;
            commit('addViewMode', viewMode);
            commit('setViewMode', state.viewModes.length - 1);
        },
        deleteViewMode({commit}, index) {
            commit('deleteViewMode', index);
            commit('setViewMode', 0);
        },
        checkChanges({state, commit}) {
            commit('setHasChanges', !isEqual(state.originalViewModes, state.viewModes));
        },
    }
});

export {store};