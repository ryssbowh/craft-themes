import { createStore as createVuexStore } from 'vuex';
import { filter } from 'lodash';

function handleError(err) {
    if (err.response) {
        Craft.cp.displayError(err.response.data.message);
    } else {
        Craft.cp.displayError(err);
    }
}

/**
 * Empty items options will come as an array, converting it to object
 */
function sanitizeOptions(viewModes) {
    for (let v in viewModes) {
        for (let d in viewModes[v].displays) {
            let options = viewModes[v].displays[d].item.options;
            if (Array.isArray(options)) {
                viewModes[v].displays[d].item.options = {};
            }
        }
    }
    return viewModes;
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

export const createStore = (app) => {
    return createVuexStore({
        state () {
            return {
                theme: null,
                themes: {},
                layout: {},
                layouts: [],
                allLayouts: {},
                isFetching: false,
                isSaving: false,
                viewModes: [],
                viewMode: null,
                showGroupModal: false,
                editedGroupUid: null,
                showFieldHandles: false,
                itemsVisibility: false,
                labelsVisibility: false,
                switchItemsVisibility: 0,
                switchLabelsVisibility: 0
            }
        },
        mutations: {
            setShowFieldHandles(state, value) {
                state.showFieldHandles = value;
            },
            setTheme (state, value) {
                state.theme = value;
                state.layouts = state.allLayouts[value];
            },
            setThemes (state, value) {
                state.themes = value;
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
            updateDisplay(state, {uid, data}) {
                let display;
                for (let v in state.viewModes) {
                    for (let d in state.viewModes[v].displays) {
                        display = state.viewModes[v].displays[d];
                        if (display.uid != uid) {
                            continue;
                        }
                        for (let index in data) {
                            display[index] = data[index];
                        }
                        break;
                    }
                }
            },
            updateItem(state, {displayUid, data}) {
                let display;
                for (let v in state.viewModes) {
                    for (let d in state.viewModes[v].displays) {
                        display = state.viewModes[v].displays[d];
                        if (display.uid != displayUid) {
                            continue;
                        }
                        let item = display.item;
                        for (let index in data) {
                            item[index] = data[index];
                        }
                        break;
                    }
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
            setShowGroupModal(state, {show, editUid = null}) {
                state.showGroupModal = show;
                state.editedGroupUid = editUid;  
            },
            setItemsVisibility(state, value) {
                state.itemsVisibility = value;
                state.switchItemsVisibility = Date.now();
            },
            setLabelsVisibility(state, value) {
                state.labelsVisibility = value;
                state.switchLabelsVisibility = Date.now();
            }
        },
        actions: {
            cloneDisplay(arg, display) {
                return app.config.globalProperties.cloneDisplay(display);
            },
            setLayout({commit, dispatch}, {layoutId, viewModeHandle}) {
                commit('setLayout', layoutId);
                dispatch('fetchViewModes', viewModeHandle);
            },
            setViewModeByHandle({state, dispatch}, {handle, setUrl = true}) {
                for (let i in state.viewModes) {
                    if (state.viewModes[i].handle == handle) {
                        dispatch('setViewMode', {viewMode: state.viewModes[i], setUrl: setUrl});
                        break;
                    }
                }
            },
            setViewMode({commit, state}, {viewMode, setUrl = true}) {
                commit('setViewMode', viewMode);
                if (setUrl) {
                    setWindowUrl(state.theme, state.layout.id, viewMode.handle);
                }
            },
            fetchViewModes({state, commit, dispatch}, viewModeHandle) {
                commit('setIsFetching', true);
                return axios.post(Craft.getCpUrl('themes/ajax/view-modes'), {layoutId: state.layout.id})
                .then((response) => {
                    commit('setViewModes', sanitizeOptions(response.data.viewModes));
                    let viewModeExists = (viewModeHandle && state.viewModes.filter((v) => v.handle == viewModeHandle).length);
                    if (viewModeExists) {
                        dispatch('setViewModeByHandle', {handle: viewModeHandle});
                    } else {
                        dispatch('setViewMode', {viewMode: state.viewModes[0]});
                    }
                })
                .catch((err) => {
                    handleError(err);
                })
                .finally(() => {
                    commit('setIsFetching', false);
                });
            },
            save({commit, state, dispatch}) {
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
                    commit('setViewModes', sanitizeOptions(res.data.viewModes));
                    dispatch('setViewModeByHandle', {handle: state.viewMode.handle, setUrl: false});
                    Craft.cp.displayNotice(res.data.message);
                })
                .catch(err => {
                    handleError(err);
                    if (err.response.data.viewModes) {
                        commit('setViewModes', sanitizeOptions(err.response.data.viewModes));
                        dispatch('setViewModeByHandle', {handle: state.viewMode.handle, setUrl: false});
                    }
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
                let promises = [];
                for (let i in state.viewMode.displays) {
                    promises.push(dispatch('cloneDisplay', state.viewMode.displays[i]).then((display) => {
                        newDisplays.push(display);
                    }));
                }
                Promise.all(promises).then(() => {
                    viewMode.displays = newDisplays;
                    commit('addViewMode', viewMode);
                    dispatch('setViewMode', {viewMode: viewMode});
                });
            },
            deleteViewMode({commit, state, dispatch}, viewMode) {
                commit('deleteViewMode', viewMode);
                dispatch('setViewMode', {viewMode: state.viewModes[0]});
            }
        }
    });
};