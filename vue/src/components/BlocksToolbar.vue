<template>
  <div class="buttons">
    <div v-if="isLoading" class="spinner"></div>
    <a href="#" class="btn save submit" :disabled="isLoading" @click.prevent="save">{{ t('Save blocks') }}</a>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import Mixin from '../mixin';

export default {
  computed: {
    ...mapState(['isLoading'])
  },
  created () {
  },
  methods: {
    ...mapActions(['save'])
  },
  mixins: [Mixin]
};
</script>
