import './main.scss';
import { merge } from 'lodash';

document.addEventListener("register-fields-components", function(e) {

    e.detail['matrix'] = {
        component : {
            props: {
                item: Object,
                display: Object
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
                },
                sortableGroup: function (type) {
                    return 'matrix-'+type.type_id;
                }
            },
            emits: ['updateItem'],
            template: `
            <div class="line line-wrapper">
                <field :item="item" @updateItem="$emit('updateItem', $event)"></field>
                <div class="matrix-type" v-for="type, index in item.types" v-bind:key="index">
                    <div class="matrix-type-name"><i>{{ t('Type {type}', {type: type.type.name}) }}</i></div>
                    <draggable
                        item-key="id"
                        :list="type.fields"
                        :group="sortableGroup(type)"
                        handle=".move"
                        >
                        <template #item="{element}">
                            <field :item="element" @updateItem="updateMatrixItem(element.id, type.type_id, $event)"></field>
                        </template>
                    </draggable>
                </div>
            </div>`
        },
        clone: function (oldDisplay, display) {
            for (let i in display.item.types) {
                for (let j in display.item.types[i].fields) {
                    display.item.types[i].fields[j].id = null;
                    display.item.types[i].fields[j].uid = null;
                }
            }
        }
    }

    e.detail['table'] = {
        component: {
            props: {
                item: Object,
                display: Object
            },
            methods: {
                updateTableItem: function (key, data) {
                    let item = this.item.fields[key];
                    item = merge(item, data);
                }
            },
            emits: ['updateItem'],
            template: `
            <div class="line line-wrapper">
                <field :item="item" @updateItem="$emit('updateItem', $event)"></field>
                <div class="table-type">
                    <field :item="element" v-for="element, key in item.fields" :moveable="false" v-bind:key="key" @updateItem="updateTableItem(key, $event)"></field>
                </div>
            </div>`
        },
        clone: function (oldDisplay, display) {
            for (let field of display.item.fields) {
                field.id = null;
                field.uid = null;
            }
        }
    }
});