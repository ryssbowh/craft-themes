<template>
    <component :is="type" :item="display.item" @updateItem="updateItem" @updateMatrixItem="updateMatrixItem" />
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Field from './Field';
import Group from './Group';
import Matrix from './Matrix';

export default {
    computed: {
        type: function () {
            if (this.display.type == 'group') {
                return 'group';
            }
            return this.display.item.type == 'matrix' ? 'matrix' : 'field';
        },
        ...mapState([])
    },
    props: {
        display: Object
    },
    data() {
        return {
        }
    },
    methods: {
        updateMatrixItem: function (data) {
            data.id = this.display.id;
            this.updateMatrixDisplay(data);
        },
        updateItem: function (data) {
            this.updateDisplay({id: this.display.id, data:{item: data}});
        },
        ...mapMutations(['updateDisplay', 'updateMatrixDisplay']),
        ...mapActions([])
    },
    emits: [],
    components: {Field, Matrix, Group}
};
</script>
<style lang="scss" scoped>
</style>