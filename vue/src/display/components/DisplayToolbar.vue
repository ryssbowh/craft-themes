<template>
    <div id="action-buttons" class="flex">
        <button href="#" class="btn submit" :disabled="!canSave" @click.prevent="save">{{ t('Save') }}</button>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import { reduce } from 'lodash';

export default {
    computed: {
        canSave: function () {
            return (!this.isSaving && !this.isLoading && this.hasChanged);
        },
        isLoading: function () {
            return reduce(this.isFetching, function (res, elem) {
                return (res || elem);
            }, false);
        },
        ...mapState(['isSaving', 'hasChanged', 'isFetching'])
    },
    methods: {
        ...mapMutations([]),
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