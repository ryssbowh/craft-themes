<template>
    <nav id="tabs">
        <ul>
            <li v-for="mode, index in viewModes" v-bind:key="index" :id="'tab-'+index">
                <a :class="{'sel': viewMode.handle === mode.handle}" @click.prevent="">
                    <span @click.prevent="setViewMode(index)">{{ mode.name }}</span>
                    <span class="icon edit" @click.prevent="editViewMode(index)"></span>
                    <span v-if="mode.handle != 'default'" class="icon delete" @click.prevent="deleteViewMode(index)"></span>
                </a>
            </li>
            <li>
                <a href="#" class="add-viewmode">
                    <span class="icon add" @click="showModal = true"></span>
                </a>
            </li>
        </ul>
        <view-mode-modal :show-modal="showModal" :edit="edit" @closeModal="onCloseModal"/>
    </nav>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import ViewModeModal from './ViewModeModal';

export default {
    computed: {
        ...mapState(['viewModes', 'viewMode', 'hasChanged'])
    },
    props: {
    },
    data() {
        return {
            showModal: false,
            edit: null
        }
    },
    methods: {
        onCloseModal: function () {
            this.edit = null;
            this.showModal = false;
        },
        editViewMode: function (index) {
            this.edit = index;
            this.showModal = true;
        },
        ...mapMutations(['setViewMode']),
        ...mapActions(['deleteViewMode']),
    },
    components: {ViewModeModal}
};
</script>
<style lang="scss" scoped>
.icon.delete, .icon.edit {
    margin-left: 10px;
    cursor: pointer;
}
.add-viewmode:hover {
    background-image: unset !important;
}
</style>