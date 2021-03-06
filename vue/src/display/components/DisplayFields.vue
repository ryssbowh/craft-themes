<template>
    <div class="display-fields">
        <div class="spinner-wrapper" v-if="isLoading">
          <div class="spinner"></div>
        </div>
        <h2>{{ t('Fields') }}</h2>
        <table class="fullwidth data" v-if="visibleFields.length">
            <thead>
                <tr>
                    <td>{{ t('Title') }}</td>
                    <td>{{ t('Handle') }}</td>
                    <td>{{ t('Type') }}</td>
                    <td>{{ t('Label') }}</td>
                    <td>{{ t('Visibility') }}</td>
                    <td>{{ t('Displayer') }}</td>
                    <td>{{ t('Actions') }}</td>
                </tr>
            </thead>
            <tbody>
                <display-field v-for="field in visibleFields" 
                    :field="field"
                />
            </tbody>
        </table>
        <p v-if="visibleFields.length == 0">
            {{ t('There are no visible fields') }}
        </p>
        <h2>{{ t('Hidden') }}</h2>
        <table class="fullwidth data" v-if="hiddenFields.length">
            <thead>
                <tr>
                    <td>{{ t('Title') }}</td>
                    <td>{{ t('Handle') }}</td>
                    <td>{{ t('Type') }}</td>
                    <td>{{ t('Label') }}</td>
                    <td>{{ t('Visibility') }}</td>
                    <td>{{ t('Displayer') }}</td>
                    <td>{{ t('Actions') }}</td>
                </tr>
            </thead>
            <tbody>
                <display-field v-for="field in hiddenFields" 
                    :field="field"
                />
            </tbody>
        </table>
        <p v-if="hiddenFields.length == 0">
            {{ t('There are no hidden fields') }}
        </p>
    </div>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';
import DisplayField from './DisplayField';
import { reduce } from 'lodash';

export default {
    computed: {
        isLoading: function () {
            return reduce(this.isFetching, function (res, elem) {
                return (res || elem);
            }, this.isSaving);
        },
        currentViewMode: function () {
            return this.viewModes[this.viewMode];
        },
        viewModeFields: function () {
            if (!this.currentViewMode) {
                return [];
            }
            return this.fields.filter(field => field.viewMode === this.currentViewMode.id || field.viewMode === this.currentViewMode.handle);
        },
        hiddenFields: function () {
            return this.viewModeFields.filter(field => field.hidden == 1);
        },
        visibleFields: function () {
            return this.viewModeFields.filter(field => !field.hidden == 1 && field.availableDisplayers.length);
        },
        ...mapState(['fields', 'isSaving', 'isFetching', 'viewModes', 'viewMode'])
    },
    props: {
    },
    created () {
        
    },
    watch: {
        fields: {
            deep: true,
            handler() {
                this.checkChanges();
            }
        }
    },
    methods: {
        ...mapMutations([]),
        ...mapActions(['checkChanges']),
    },
    mixins: [Mixin],
    components: {
        DisplayField
    }
};
</script>

<style lang="scss" scoped>
thead td {
    font-weight: bold
}
.display-fields {
    position: relative;
}
.spinner-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(230, 230, 230, 0.7);
    z-index: 1000;
    .spinner {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
}
</style>