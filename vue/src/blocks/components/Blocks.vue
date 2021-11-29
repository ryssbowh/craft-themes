<template>
    <div class="blocks">
        <div class="spinner-wrapper" v-if="isLoading">
            <div class="spinner"></div>
        </div>
        <div class="blocks-sidebar">
            <div class="heading">
                <h3>Blocks</h3>
            </div>
            <div v-for="provider in providers" v-bind:key="provider.handle">
                <h5 class="sub-heading slide" :class="{icon: true, expand: !slideStates[provider.handle], collapse: slideStates[provider.handle]}" @click="slideStates[provider.handle] = !slideStates[provider.handle]">{{ provider.name }}</h5>
                <transition name="slide">
                    <div>
                        <draggable v-if="slideStates[provider.handle]"
                          item-key="vueid"
                          :list="provider.blocks"
                          :group="{ name: 'blocks', pull: 'clone', put: false }"
                          :sort="false"
                          handle=".block"
                        >
                            <template #item="{element}">
                                <block :block="element" :original="true"/>
                            </template>
                        </draggable>
                    </div>
                </transition>
            </div>
        </div>
        <div class="regions">
            <div class="heading">
                <h3>{{ t('Regions') }}</h3>
            </div>
            <div class="region-list" v-if="layouts.length">
                <region v-for="region in regions" v-bind:key="region.handle" :region="region"/>
                <p v-if="!regions.length">{{ t('No regions are defined for this theme') }}</p>
            </div>
            <div class="region-list" v-if="!layouts.length">
                <p>{{ t('No layouts available, you should reinstall the themes data in the settings') }}</p>
            </div>
        </div>
        <layout-modal/>
        <options-modal/>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import Block from './Block.vue';
import OptionsModal from './OptionsModal.vue';
import Region from './Region.vue';
import Draggable from 'vuedraggable';
import { reduce } from 'lodash';

export default {
    computed: {
        isLoading: function () {
            return reduce(this.isFetching, function (res, elem) {
                return (res || elem);
            }, this.isSaving);
        },
        ...mapState(['blocks', 'regions', 'isFetching', 'isSaving', 'blockOptionId', 'providers', 'layouts'])
    },
    props: {
    },
    watch: {
        providers: { 
            deep: true,
            handler (providers) {
                let first = true;
                for (let i in providers) {
                    this.slideStates[i] = first;
                    first = false;
                }
            }
        }
    },
    data: function () {
        return {
            slideStates: {}
        }
    },
    created: function () {
        this.fetchProviders();
    },
    methods: {
        ...mapActions(['checkChanges', 'fetchProviders'])
    },
    components: {
        Region,
        Block,
        OptionsModal,
        Draggable
    }
};
</script>

<style lang="scss">
#toolbar {
    justify-content: end;
}
</style>

<style lang="scss" scoped>
@import '~craftcms-sass/_mixins';

.blocks {
    position: relative;
    display: flex;
    border-radius: 3px;
    background-color: $grey050;
    overflow: hidden;
    border: 1px solid rgba(96, 125, 159, 0.25);
    box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);
    .spinner-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        .spinner {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
    }
    .regions, .options, .block-sidebar{
        box-shadow: 0 0 0 1px rgba(31, 41, 51, 0.1), 0 2px 5px -2px rgba(31, 41, 51, 0.2);
    }
    &:after {
        position: absolute;
        display: block;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        content: '';
        box-shadow: inset 0 1px 3px -1px #acbed2;
        pointer-events: none;
    }
    .regions {
        width: 60%;
        flex-grow: 1;
        background: white;
        * {
            box-sizing: border-box;
        }
    }
    .region-list {
        padding: 15px 15px 5px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .blocks-sidebar, .regions, .options {
        min-height: calc(100vh - 320px);
        overflow-y: auto;
    }
    .blocks-sidebar {
        width: 30%;
        max-width: 300px;
    }
    h5.sub-heading {
        padding: 7px 15px;
        margin: 0;
        position: relative;
        cursor: pointer;
        border-bottom: 1px solid rgba(96, 125, 159, 0.25);
    }
    .heading {
        padding: 7px 14px 6px;
        border-bottom: 1px solid rgba(96, 125, 159, 0.25);
        background-color: $grey050;
        background-image: linear-gradient(rgba(51, 64, 77, 0), rgba(51, 64, 77, 0.05));
    }
    h5.slide {
        position: relative;
        &:before {
            position: absolute;
            right: 10px;
        }
    }
}
</style>
