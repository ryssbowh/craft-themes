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
    let i = url.findIndex(e => e == 'blocks');
    if (i === -1) {
        return;
    }
    if (url[i+1] == theme && url[i+2] == layout) {
        return;
    }
    url[i+1] = theme;
    url[i+2] = layout;
    window.history.pushState({}, '', url.join('/'));
}

function sanitizeBlocks(blocks) {
    blocks = cloneDeep(blocks);
    for (let i in blocks) {
        delete blocks[i].index;
        delete blocks[i].optionsHtml;
    }
    return blocks;
}

const store = createStore({
    state () {
        return {
            theme: null,
            themes: [],
            providers: {},
            isSaving: false,
            isCopying: false,
            isFetching: {},
            blocks: [],
            layouts: [],
            layout: {},
            allLayouts: {},
            regions: {},
            cacheStrategies: {},
            showLayoutModal: false,
            showOptionsModal: false,
            editedLayoutUid: null,
            editedBlock: null,
            showFieldHandles: false,
        }
    },
    mutations: {
        setShowFieldHandles(state, value) {
            state.showFieldHandles = value;
        },
        setShowLayoutModal(state, {show, editUid = null}) {
            state.showLayoutModal = show;
            state.editedLayoutUid = editUid;  
        },
        setShowOptionsModal(state, {show, block = null}) {
            state.showOptionsModal = show;
            state.editedBlock = block;  
        },
        setProviders(state, value) {
            state.providers = value;
        },
        setThemes (state, value) {
            state.themes = value;
        },
        setTheme (state, value) {
            state.theme = value;
            state.regions = state.themes[value].regions;
            state.layouts = state.allLayouts[value];
            state.isCopying = false;
        },
        setIsSaving (state, value) {
            state.isSaving = value;
        },
        setIsCopying(state, value) {
            state.isCopying = value;
        },
        setBlocks (state, blocks) {
            for (let i in blocks) {
                blocks[i].index = i; 
            }
            state.blocks = blocks;
        },
        addBlock(state, block) {
            block.index = state.blocks.length;
            state.blocks.push(block);
        },
        updateBlock(state, block) {
            state.blocks = state.blocks.map((block2) => {
                return (block2.index === block.index) ? block : block2;
            });
        },
        removeBlock(state, block) {
            state.blocks.splice(block.index, 1);
            for (let i in state.blocks) {
                state.blocks[i].index = i;
            }
        },
        addLayout(state, layout) {
            state.layouts.push(layout);
        },
        deleteLayout(state, uid) {
            state.layouts = filter(state.layouts, (layout) => layout.uid != uid);
        },
        updateLayout(state, layout) {
            for (let i in state.layouts) {
                if (state.layouts[i].uid == layout.uid) {
                    state.layouts[i] = layout;
                    break;
                }
            }
        },
        updateCustomLayout(state, data) {
            state.layout = merge(state.layout, data);
        },
        setLayouts(state, value) {
            state.layouts = value;
        },
        setLayout(state, id) {
            state.isCopying = false;
            if (typeof id == 'object') {
                state.layout = id;
                return;
            }
            for (let i in state.layouts) {
                if (state.layouts[i].id == id) {
                    state.layout = state.layouts[i];
                    return;
                }
            }
        },
        setAllLayouts(state, value) {
            state.allLayouts = value;
        },
        setIsFetching(state, {key, value}) {
            state.isFetching[key] = value;
        },
        setCacheStrategies(state, value) {
            state.cacheStrategies = value;
        },
    },
    actions: {
        save({commit, state}) {
            commit('setIsSaving', true);
            let data = {blocks: sanitizeBlocks(state.blocks), layout: state.layout.id, theme: state.theme};
            let isNew = false;
            if (state.layout.type == 'custom') {
                data.custom = state.layout;
                isNew = state.layout.id == null;
            }
            axios({
                method: 'post',
                url: Craft.getCpUrl('themes/ajax/blocks/save'),
                data: data,
                headers: {'X-CSRF-Token': Craft.csrfTokenValue}
            })
            .then(res => {
                if (isNew) {
                    commit('addLayout', res.data.layout);
                } else {
                    commit('updateLayout', res.data.layout);
                }
                setWindowUrl(state.theme, res.data.layout.id);
                commit('setBlocks', res.data.blocks);
                commit('setLayout', res.data.layout);
                Craft.cp.displayNotice(res.data.message);
            })
            .catch(err => {
                commit('setBlocks', err.response.data.blocks);
                handleError(err);
            })
            .finally(() => {
                commit('setIsSaving', false);
            })
        },
        resetBlocks({commit}, blocks) {
            for (let i in blocks) {
                blocks[i].id = null;
                blocks[i].layout = null;
                blocks[i].uid = null;
                blocks[i].dateCreated = null;
                blocks[i].dateUpdated = null;
            }
            commit('setBlocks', blocks);
        },
        fetchBlocks({state, commit}) {
            let data = {};
            data[Craft.csrfTokenName] = Craft.csrfTokenValue;
            commit('setIsFetching', {key: 'blocks', value: true});
            return axios.post(Craft.getCpUrl('themes/ajax/blocks/'+state.layout.id), data)
            .then((response) => {
                commit('setBlocks', response.data.blocks);
            })
            .catch((err) => {
                handleError(err);
            })
            .finally(() => {
                commit('setIsFetching', {key: 'blocks', value: false});
            });
        },
        fetchProviders({commit}) {
            let data = {};
            data[Craft.csrfTokenName] = Craft.csrfTokenValue;
            commit('setIsFetching', {key: 'providers', value: true});
            return axios.post(Craft.getCpUrl('themes/ajax/block-providers'), data)
            .catch((err) => {
                handleError(err);
            })
            .then((response) => {
                commit('setProviders', response.data.providers);
            })
            .finally(() => {
                commit('setIsFetching', {key: 'providers', value: false});
            });
        },
        setLayoutAndFetch({dispatch, commit, state}, id) {
            commit('setLayout', id);
            setWindowUrl(state.theme, id);
            dispatch('fetchBlocks');
        },
        setThemeAndFetch({state, commit, dispatch}, theme) {
            commit('setTheme', theme);
            let layout;
            for (let id in state.layouts) {
                layout = state.layouts[id];
                if (layout.type == 'default') {
                    commit('setLayout', layout.id);
                    setWindowUrl(state.theme, layout.id);
                    dispatch('fetchBlocks');
                    return;
                }
            }
        },
        copyLayout({state, commit, dispatch}, layout) {
            commit('setLayout', layout);
            setWindowUrl(state.theme, layout.id);
            commit('setIsCopying', true);
            dispatch('resetBlocks', state.blocks);
        },
        createLayout({state, commit, dispatch}, layout) {
            commit('setLayout', layout);
            commit('setIsCopying', true);
            dispatch('resetBlocks', state.blocks);
        },
        updateCustomLayout({commit, dispatch}, data) {
            commit('updateCustomLayout', data);
        },
        deleteLayout({state, commit, dispatch}) {
            let data = {};
            data[Craft.csrfTokenName] = Craft.csrfTokenValue;
            commit('setIsFetching', {key: 'blocks', value: true});
            let isCustom = state.layout.type == 'custom';
            return axios.post(Craft.getCpUrl('themes/ajax/layouts/delete/'+state.layout.id), data)
            .then((response) => {
                Craft.cp.displayNotice(response.data.message);
                if (isCustom) {
                    commit('deleteLayout', state.layout.uid);
                } else {
                    commit('updateLayout', response.data.layout);
                }
                let layout = filter(state.layouts, (layout) => {return layout.type == 'default'})[0];
                return dispatch('setLayoutAndFetch', layout.id);
            })
            .catch((err) => {
                handleError(err);
            })
        }
    }
});

export {store};