<template>
    <div :class="{line: true, display: true, visuallyHidden: display.visuallyHidden}">

    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';
import OptionsModal from './OptionsModal';

export default {
    computed: {
        
        ...mapState([])
    },
    props: {
        display: Object
    },
    data() {
        
    },
    methods: {
        ...mapMutations(['updateDisplay']),
        ...mapActions([]),
    },
    mixins: [Mixin],
    emits: ['changedVisibility'],
};
</script>
<style lang="scss" scoped>

</style>