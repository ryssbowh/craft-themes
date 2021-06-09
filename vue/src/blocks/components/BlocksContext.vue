<template>
  <div class="btngroup">
    <button v-if="theme" type="button" class="btn menubtn" data-icon="brush">{{ themes[theme].name }}</button>
    <div v-if="theme" class="menu">
      <ul class="padded">
        <li v-for="theme2 in themes" v-bind:key="theme2.handle"><a :class="{sel: theme == theme2.handle}" href="#" @click.prevent="checkAndSetTheme(theme2.handle)">{{ theme2.name }}</a></li>
      </ul>
    </div>

    <button v-if="layout" type="button" class="btn menubtn" data-icon="section">{{ layout.description }}</button>
    <div class="menu" v-if="layoutsWithBlocks">
      <ul class="padded">
        <li v-for="elem, index in layoutsWithBlocks" v-bind:key="index">
            <a :class="{sel: elem == layout}" href="#" @click.prevent="checkAndSetLayout(elem.id)">{{ elem.description }}</a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';

export default {
  computed: {
    layoutsWithBlocks: function () {
      return this.layouts.filter(layout => layout.hasBlocks);
    },
    ...mapState(['layouts', 'layout', 'theme', 'hasChanged'])
  },
  props: {
    initialTheme: String,
    initialLayout: Number,
    themes: Object,
    availableLayouts: Object,
    allLayouts: Object
  },
  created () {
    this.setThemes(this.themes);
    this.setAllLayouts(this.allLayouts);
    if (this.initialTheme) {
        this.setTheme(this.initialTheme);
    }
    if (this.initialLayout) {
        this.setLayoutAndFetch(this.initialLayout);
    }
    let _this = this;
    window.addEventListener('popstate', () => {
      const url = document.location.pathname.split('/');
      let i = url.findIndex(e => e == 'blocks');
      if (i !== -1) {
        _this.setTheme(url[i+1]);
        _this.setLayoutAndFetch(url[i+2]);
      }
    });
  },
  methods: {
    checkAndSetTheme: function (theme) {
      if (this.hasChanged) {
        if (confirm(this.t('You have unsaved changes, continue anyway ?'))) {
          this.setThemeAndFetch(theme);
        }
      } else {
        this.setThemeAndFetch(theme);
      }
    },
    checkAndSetLayout: function (index) {
      if (this.hasChanged) {
        if (confirm(this.t('You have unsaved changes, continue anyway ?'))) {
          this.setLayoutAndFetch(index);
        }
      } else {
        this.setLayoutAndFetch(index);
      }
    },
    ...mapMutations(['setThemes', 'setAllLayouts', 'setAvailableLayouts', 'setTheme']),
    ...mapActions(['setLayoutById', 'setLayoutAndFetch', 'setThemeAndFetch']),
  }
};
</script>
