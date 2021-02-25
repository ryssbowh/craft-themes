<template>
    <nav id="tabs">
        <ul>
            <li v-for="mode, index in viewModes" v-bind:key="index" :id="'tab-'+index">
                <a :class="{'sel': viewMode === index}" @click.prevent="setViewMode(index)">
                    {{ mode.name }}
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';

export default {
  computed: {
    ...mapState(['viewModes', 'viewMode'])
  },
  props: {
    initialTheme: String,
    themes: Object
  },
  created () {
    
  },
  methods: {
    ...mapMutations([]),
    ...mapActions(['setViewMode']),
  },
  mixins: [Mixin],
};
</script>
