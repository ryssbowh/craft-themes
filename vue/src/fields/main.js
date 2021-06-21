import './main.scss';
import { merge } from 'lodash';

window.themesFields = {};

window.themesFields['matrix'] = {
    props: {
        item: Object
    },
    methods: {
        updateMatrixItem: function (fieldId, typeId, data) {
            for(let i in this.item.types) {
                let type = this.item.types[i];
                if (type.type.id != typeId) {
                    continue;
                }
                for (i in type.fields) {
                    let field = type.fields[i];
                    if (field.id != fieldId) {
                        continue;
                    }
                    field = merge(field, data);
                }
            }
        }
    },
    emits: ['updateItem'],
    template: `
    <div>
        <field :item="item" @updateItem="$emit('updateItem', $event)"></field>
        <div class="matrix-type" v-for="type, index in item.types" v-bind:key="index">
            <div class="matrix-type-name">{{ t('Type {type}', {type: type.type.name}) }}</div>
            <draggable
                item-key="id"
                :list="type.fields"
                group="'matrix-'+type.type_id"
                handle=".move"
                >
                <template #item="{element}">
                    <field :item="element" @updateItem="updateMatrixItem(element.id, type.type_id, $event)"></field>
                </template>
            </draggable>
        </div>
    </div>`
}

window.themesFields['table'] = {
    props: {
        item: Object
    },
    methods: {
        updateTableItem: function (key, data) {
            let item = this.item.fields[key];
            item = merge(item, data);
        }
    },
    emits: ['updateItem'],
    template: `
    <div>
        <field :item="item" @updateItem="$emit('updateItem', $event)"></field>
        <div class="table-type">
            <field :item="element" v-for="element, key in item.fields" :moveable="false" v-bind:key="key" @updateItem="updateTableItem(key, $event)"></field>
        </div>
    </div>`
}