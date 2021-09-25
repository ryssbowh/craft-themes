<template>
    <div id="action-buttons" class="flex">
        <button href="#" class="btn submit" :disabled="!canSave" @click.prevent="save">{{ t('Save', {}, 'app') }}</button>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
    computed: {
        canSave: function () {
            return (!this.isSaving && !this.isFetching);
        },
        ...mapState(['isSaving', 'isFetching'])
    },
    methods: {
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