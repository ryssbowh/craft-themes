<template>
    <div id="action-buttons" class="flex" v-if="layouts.length">
        <button href="#" class="btn" :disabled="!canSave" @click.prevent="setShowGroupModal({show: true})">{{ t('New group') }}</button>
        <button href="#" class="btn submit" :disabled="!canSave" @click.prevent="save">{{ t('Save', {}, 'app') }}</button>
    </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from 'vuex';

export default {
    computed: {
        canSave: function () {
            return (!this.isSaving && !this.isFetching);
        },
        ...mapState(['isSaving', 'isFetching', 'layouts'])
    },
    methods: {
        ...mapMutations(['setShowGroupModal']),
        ...mapActions(['save']),
    }
};
</script>
<style lang="scss">
#toolbar {
    justify-content: end;
}
</style>
<style lang="scss" scoped>
.btn[disabled] {
  opacity: 0.5;
}
</style>