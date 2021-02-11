import { createStore } from 'vuex';
import axios from 'axios';

export const store = createStore({
  state () {
    return {
      theme: null,
      themes: [],
      isLoading: false,
      blocks: [],
      blockCount: 0
    }
  },
  mutations: {
    setTheme (state, value) {
      state.theme = value;
    },
    setThemes (state, value) {
      state.themes = value;
    },
    setIsLoading (state, value) {
      state.isLoading = value;
    },
    setBlocks (state, blocks) {
      for (let i in blocks) {
        blocks[i].index = i;
      }
      state.blocks = blocks;
    },
    incrementBlockCount (state) {
      state.blockCount++
    },
    addBlock(state, block) {
      block.index = state.blocks.length;
      state.blocks.push(block);
    },
    updateBlock(state, block) {
      state.blocks = state.blocks.map((item) => {
        return item.index == block.index ? block : item; 
      });
    },
    removeBlock(state, block) {
      state.blocks.splice(block.index, 1);
    }
  },
  actions: {
    save({commit, state}) {
      commit('setIsLoading', true);
      axios({
        method: 'post',
        url: Craft.getCpUrl('themes/blocks/' + state.theme.handle + '/save'),
        data: {blocks: state.blocks},
        headers: {'X-CSRF-Token': Craft.csrfTokenValue}
      })
      .then(res => {
        commit('setBlocks', res.data.blocks);
        Craft.cp.displayNotice(res.data.message);
      })
      .catch(err => {
        if (err.response) {
          Craft.cp.displayError(err.response.data.message);
        } else {
          Craft.cp.displayError(err);
        }
      })
      .finally(() => {
        commit('setIsLoading', false);
      })
    },
  }
});
