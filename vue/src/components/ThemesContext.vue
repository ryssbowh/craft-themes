<template>
  <div>
    <button v-if="theme" type="button" id="context-btn" class="btn menubtn themesbtn" data-icon="world">{{ theme.name }}</button>
    <div class="menu">
      <ul class="padded">
        <li v-for="theme2 in themes" v-bind:key="theme2.handle"><a :class="{sel: theme && theme.handle == theme2.handle}" href="#" @click.prevent="loadTheme(theme2)">{{ theme2.name }}</a></li>
      </ul>
    </div>
  </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

export default {
  computed: {
    ...mapState([])
  },
  props: {
    theme: Object,
    themes: Object
  },
  created () {
    this.setTheme(this.theme);
    this.setThemes(this.themes);
  },
  methods: {
    ...mapMutations(['setTheme', 'setThemes']),
    ...mapActions(['loadTheme'])
  }
};
</script>
