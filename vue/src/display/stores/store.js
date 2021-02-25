import { createStore } from 'vuex';
import axios from 'axios';
import { cloneDeep } from 'lodash';

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
            isFetching: {},
            viewModes: [],
            viewMode: 0
        }
    },
    mutations: {
        setTheme (state, value) {
            state.theme = value;
        },
        setLayout (state, value) {
            state.layout = value;
        },
        setIsFetching(state, value) {
            state.isFetching[value.key] = value.value;
        },
        setViewModes(state, value) {
            state.viewModes = value;
        },
        setViewMode(state, index) {
            state.viewMode = index;
        }
    },
    actions: {
        setTheme ({state, commit, dispatch}, theme) {
            commit('setTheme', theme);
            dispatch('setLayout', state.layout);
        },
        setLayout ({state, commit, dispatch}, layout) {
            commit('setLayout', layout);
            dispatch('fetchViewModes');
        },
        setViewMode({state, commit, dispatch}, index) {
            commit('setViewMode', index);
            dispatch('fetchFields');
        },
        fetchViewModes({state, commit, dispatch}) {
            let data = {};
            data[Craft.csrfTokenName] = Craft.csrfTokenValue;
            commit('setIsFetching', {key: 'viewModes', value: true});
            return axios.post(Craft.getCpUrl('themes/ajax/view-modes/'+state.theme+'/'+state.layout), data)
            .then((response) => {
                commit('setViewModes', cloneDeep(response.data.viewModes));
                dispatch('setViewMode', 0);
            })
            .catch((err) => {
                handleError(err);
            })
            .finally(() => {
                commit('setIsFetching', {key: 'viewModes', value: false});
            });
        },
        fetchFields({state, commit}) {

        }
    }
});

export {store};