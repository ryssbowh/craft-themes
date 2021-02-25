<template>
  <div class="btngroup">
    <button v-if="theme" type="button" class="btn menubtn" data-icon="brush">{{ themes[theme].name }}</button>
    <div v-if="theme" class="menu">
      <ul class="padded">
        <li v-for="theme2 in themes" v-bind:key="theme2.handle">
            <a :class="{sel: theme == theme2.handle}" href="#" @click.prevent="setTheme(theme2.handle)">{{ theme2.name }}</a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';

export default {
  computed: {
    ...mapState(['theme'])
  },
  props: {
    initialTheme: String,
    themes: Object
  },
  created () {
    if (this.initialTheme) {
        this.setTheme(this.initialTheme);
    }
    window.addEventListener('popstate', () => {
      // const url = document.location.pathname.split('/');
      // let i = url.findIndex(e => e == 'display');
    });
  },
  methods: {
    ...mapMutations(['setTheme']),
    ...mapActions([]),
  },
  mixins: [Mixin],
};
</script>
