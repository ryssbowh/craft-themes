<template>
    <nav id="tabs">
        <ul>
            <li v-for="mode, index in viewModes" v-bind:key="index" :id="'tab-'+index">
                <a :class="{'sel': viewMode.handle === mode.handle}" @click.prevent="">
                    <span @click.prevent="setViewMode(mode)">{{ mode.name }}</span>
                    <span class="icon edit" @click.prevent="editViewMode(mode)" :title="t('Edit View Mode')"></span>
                    <span v-if="mode.handle != 'default'" class="icon delete" @click.prevent="deleteViewMode(mode)"></span>
                </a>
            </li>
            <li>
                <a href="#" class="add-viewmode" @click.prevent="addViewMode" :title="t('Add View Mode')">
                    <span class="icon add"></span>
                </a>
            </li>
        </ul>
        <view-mode-modal :show-modal="showModal" :editedViewMode="edited" @closeModal="onCloseModal"/>
    </nav>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
    computed: {
        ...mapState(['viewModes', 'viewMode'])
    },
    props: {
    },
    data() {
        return {
            showModal: false,
            edited: null
        }
    },
    methods: {
        onCloseModal: function () {
            this.edit = null;
            this.showModal = false;
        },
        addViewMode: function () {
            this.edited = null;
            this.showModal = true;
        },
        editViewMode: function (viewMode) {
            this.edited = viewMode;
            this.showModal = true;
        },
        ...mapActions(['deleteViewMode', 'setViewMode'])
    }
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