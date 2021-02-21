import { createStore } from 'vuex';
import axios from 'axios';
import { cloneDeep, isEqual } from 'lodash';

function handleError(err) {
	if (err.response) {
		Craft.cp.displayError(err.response.data.message);
	} else {
		Craft.cp.displayError(err);
	}
}

function setWindowUrl(theme, layout) {
	let url = document.location.pathname.split('/');
	let i = url.findIndex(e => e == 'layouts');
	if (i === -1) {
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
		delete blocks[i].hasOptions;
		delete blocks[i].optionsHtml;
	}
	return blocks;
}

function sanitizeLayout(layout) {
	layout = {...layout};
	delete layout.description;
	return layout;
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
			originalBlocks: [],
			layouts: [],
			layout: {},
			allLayouts: {},
			availableLayouts: {},
			layoutIndex: null,
			regions: {},
			hasChanged: false,
			blockOptionId: null,
			blockErrors: {}
		}
	},
	mutations: {
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
		setOriginals (state, blocks) {
			for (let i in blocks) {
				blocks[i].index = i;
			}
			state.originalBlocks = blocks;
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
            state.allLayouts[state.theme].push(layout);
        },
		setLayouts(state, value) {
			state.layouts = value;
		},
		setLayout(state, index) {
			state.layout = state.layouts[index];
            setWindowUrl(state.theme, state.layout.id);
            state.isCopying = false;
		},
		setLayoutByObject(state, layout) {
			state.layout = layout;
            state.hasChanged = true;
		},
		setAllLayouts(state, value) {
			state.allLayouts = value;
		},
		setAvailableLayouts(state, value) {
			state.availableLayouts = value;
		},
        deleteLayout(state, layoutId) {
            let layouts = state.allLayouts[state.theme].filter(layout => {
                return layout.id != layoutId;
            });
            state.layouts = layouts;
            state.allLayouts[state.theme] = layouts;
        },
		setHasChanged(state, value) {
			state.hasChanged = value;
		},
		setBlockOptions(state, block) {
			state.blockOptionId = block.index;
		},
		setIsFetching(state, {key, value}) {
			state.isFetching[key] = value;
		},
		setBlockErrors(state, {block, errors}) {
			state.blockErrors[block.index] = errors;
		},
		resetErrors(state) {
			state.blockErrors = {};
		}
	},
	actions: {
		save({commit, state}) {
			commit('setIsSaving', true);
			axios({
				method: 'post',
				url: Craft.getCpUrl('themes/ajax/layouts/save'),
				data: {blocks: sanitizeBlocks(state.blocks), layout: sanitizeLayout(state.layout), theme: state.theme},
				headers: {'X-CSRF-Token': Craft.csrfTokenValue}
			})
			.then(res => {
				commit('setOriginals', res.data.blocks);
				commit('setBlocks', cloneDeep(res.data.blocks));
				commit('setHasChanged', false);
                if (state.isCopying) {
                    commit('addLayout', res.data.layout);
                    commit('setLayout', state.layouts.length-1);
                    commit('setIsCopying', false);
                }
				Craft.cp.displayNotice(res.data.message);
			})
			.catch(err => {
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
                commit('setOriginals', response.data.blocks);
				commit('setBlocks', cloneDeep(response.data.blocks));
			})
			.catch((err) => {
				handleError(err);
			})
			.finally(() => {
				commit('setIsFetching', {key: 'blocks', value: false});
			});
		},
		checkChanges({state, commit}) {
			let res = false;
			if (!isEqual(state.originalOptions, state.options) || state.originalBlocks.length !== state.blocks.length) {
				res = true;
			} else {
				outer:
				for (let i in state.originalBlocks) {
					for (let j of Object.keys(state.originalBlocks[i])) {
						switch (typeof state.blocks[i][j]) {
							case 'object':
								res = !isEqual(state.blocks[i][j], state.originalBlocks[i][j]);
								break;
							default:
								res = state.originalBlocks[i][j] !== state.blocks[i][j];
								break;
						}
						if (res) break outer;
					}
				}
			}
			commit('setHasChanged', res);
		},
		fetchProviders({commit}) {
			let data = {};
			data[Craft.csrfTokenName] = Craft.csrfTokenValue;
			commit('setIsFetching', {key: 'providers', value: true});
			return axios.post(Craft.getCpUrl('themes/ajax/providers'), data)
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
		setLayout({dispatch, commit}, layout) {
			commit('setLayout', layout);
			dispatch('fetchBlocks');
		},
        setThemeAndFetch({state, commit, dispatch}, theme) {
            commit('setTheme', theme);
            commit('setLayout', 0);
            dispatch('fetchBlocks');
        },
		setLayoutById({state, commit, dispatch}, layoutId) {
			for (let i in state.layouts) {
				if (state.layouts[i].id == layoutId) {
					dispatch('setLayout', i);
				}
			}
		},
        copy({state, commit, dispatch}, layout) {
            layout.theme = state.theme;
            commit('setLayoutByObject', layout);
            commit('setIsCopying', true);
            dispatch('resetBlocks', state.blocks);
        },
        deleteLayout({state, commit, dispatch}) {
            let data = {};
            data[Craft.csrfTokenName] = Craft.csrfTokenValue;
            commit('setIsFetching', {key: 'blocks', value: true});
            return axios.post(Craft.getCpUrl('themes/ajax/layouts/delete/'+state.layout.id), data)
            .then((response) => {
                Craft.cp.displayNotice(response.data.message);
                commit('deleteLayout', state.layout.id);
                dispatch('setLayout', 0);
            })
            .catch((err) => {
                handleError(err);
            })
        }
	}
});

export {store};