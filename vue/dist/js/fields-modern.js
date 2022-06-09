(function(){var e={567:function(e,t,n){"use strict";n.r(t);var i=n(8081),l=n.n(i),o=n(3645),d=n.n(o),r=d()(l());r.push([e.id,"",""]),t["default"]=r},7622:function(e,t,n){"use strict";n.r(t);n(9653);t["default"]={props:{item:Object,display:Object,indentationLevel:Number,classes:{type:String,default:function(){return""}}},methods:{updateMatrixItem:function(e,t,n){e:for(var i in this.item.types){var l=this.item.types[i];if(l.type.id==t)for(var o in l.fields){var d=l.fields[o];if(d.uid==e){for(var r in n)this.item.types[i].fields[o][r]=n[r];break e}}}},sortableGroup:function(e){return"matrix-"+e.type_id}},template:'\n        <div :class="classes + \' line has-sub-fields bg-grey\'">\n            <field :indentation-level="indentationLevel" :classes="\'no-margin\'" :item="item" @updateItem="$emit(\'updateItem\', $event)"></field>\n            <div class="sub-fields" v-for="type, index in item.types" v-bind:key="index">\n                <div :class="\'line no-margin no-padding flex indented-\' + (indentationLevel + 1)">\n                    <div class="block-type-name">\n                        <div class="indented"><i>{{ t(\'Type {type}\', {type: type.type.name}) }}</i></div>\n                    </div>\n                </div>\n                <draggable\n                    item-key="id"\n                    :list="type.fields"\n                    :group="sortableGroup(type)"\n                    handle=".move"\n                    >\n                    <template #item="{element}">\n                        <component :is="fieldComponent(element.type)" :item="element" :indentation-level="indentationLevel + 1" @updateItem="updateMatrixItem(element.uid, type.type_id, $event)"/>\n                    </template>\n                </draggable>\n            </div>\n        </div>'}},3873:function(e,t,n){"use strict";n.r(t);n(9653);t["default"]={props:{item:Object,display:Object,indentationLevel:Number,classes:{type:String,default:function(){return""}}},methods:{updateNeoItem:function(e,t,n){e:for(var i in this.item.types){var l=this.item.types[i];if(l.type.id==t)for(var o in l.fields){var d=l.fields[o];if(d.uid==e){for(var r in n)this.item.types[i].fields[o][r]=n[r];break e}}}},sortableGroup:function(e){return"neo-"+e.type_id}},template:'\n        <div :class="classes + \' line has-sub-fields bg-grey\'">\n            <field :indentation-level="indentationLevel" :classes="\'no-margin\'" :item="item" @updateItem="$emit(\'updateItem\', $event)"></field>\n            <div class="sub-fields" v-for="type, index in item.types" v-bind:key="index">\n                <div :class="\'line no-margin no-padding flex indented-\' + (indentationLevel + 1)">\n                    <div class="block-type-name">\n                        <div class="indented"><i>{{ t(\'Type {type}\', {type: type.type.name}) }}</i></div>\n                    </div>\n                </div>\n                <draggable\n                    item-key="id"\n                    :list="type.fields"\n                    :group="sortableGroup(type)"\n                    handle=".move"\n                    >\n                    <template #item="{element}">\n                        <component :is="fieldComponent(element.type)" :item="element" :classes="\'no-padding\'" :indentation-level="indentationLevel + 1" @updateItem="updateNeoItem(element.uid, type.type_id, $event)"/>\n                    </template>\n                </draggable>\n            </div>\n        </div>'}},9340:function(e,t,n){"use strict";n.r(t);n(9653),n(7941);t["default"]={props:{item:Object,display:Object,indentationLevel:Number},computed:{fields:function(){var e,t=Object.keys(this.item.types);return null!==(e=this.item.types[t[0]].fields)&&void 0!==e?e:[]}},methods:{updateItem:function(e,t){var n,i=Object.keys(this.item.types),l=this.item.types[i[0]];for(var o in l.fields)if(n=l.fields[o],n.uid==e){for(var d in t)this.item.types[i[0]].fields[o][d]=t[d];break}},sortableGroup:function(){return"super-table-"+this.item.id}},template:'\n    <div class="line has-sub-fields bg-grey">\n        <field :item="item" :indentation-level="indentationLevel" @updateItem="$emit(\'updateItem\', $event)"></field>\n        <draggable\n            item-key="id"\n            :list="fields"\n            :group="sortableGroup()"\n            handle=".move"\n            class="sub-fields"\n            >\n            <template #item="{element}">\n                <component :is="fieldComponent(element.type)" :item="element" :indentation-level="indentationLevel + 1" @updateItem="updateItem(element.uid, $event)"/>\n            </template>\n        </draggable>\n    </div>'}},2897:function(e,t,n){"use strict";n.r(t);n(9653);t["default"]={props:{item:Object,display:Object,indentationLevel:Number},methods:{updateTableField:function(e,t){for(var n in t)this.item.fields[e][n]=t[n]}},template:'\n    <div class="line has-sub-fields bg-grey">\n        <field :item="item" :indentation-level="indentationLevel" @updateItem="$emit(\'updateItem\', $event)"></field>\n        <div class="sub-fields">\n            <component v-for="element, key in item.fields" :is="fieldComponent(element.type)" :item="element" :indentation-level="indentationLevel + 1" @updateItem="updateTableField(key, $event)"/>\n        </div>\n    </div>'}},8012:function(e,t,n){"use strict";n.r(t);n(6992),n(8674),n(9601),n(7727),n(2496);var i=n(7622),l=n(2897),o=n(9340),d=n(3873),r=n(6486);window.CraftThemes.fieldComponents["matrix"]={component:i["default"],clone:function(e,t){var n=(0,r.merge)({},e);for(var i in e.types)for(var l in e.types[i].fields)n.types[i].fields[l]=t.config.globalProperties.cloneField(e.types[i].fields[l]);return n}},window.CraftThemes.fieldComponents["table"]={component:l["default"],clone:function(e,t){var n=(0,r.merge)({},e);for(var i in e.fields)newFields.fields[i]=t.config.globalProperties.cloneField(e.fields[i]);return n}},window.CraftThemes.fieldComponents["super-table"]={component:o["default"],clone:function(e,t){var n=(0,r.merge)({},e);for(var i in e.types)for(var l in e.types[i].fields)n.types[i].fields[l]=t.config.globalProperties.cloneField(e.types[i].fields[l]);return n}},window.CraftThemes.fieldComponents["neo"]={component:d["default"],clone:function(e,t){var n=(0,r.merge)({},e);for(var i in e.types)for(var l in e.types[i].fields)n.types[i].fields[l]=t.config.globalProperties.cloneField(e.types[i].fields[l]);return n}}},2496:function(e,t,n){var i=n(567);i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[e.id,i,""]]),i.locals&&(e.exports=i.locals);var l=n(7913)["default"];l("1a8c18f0",i,!0,{sourceMap:!1,shadowMode:!1})}},t={};function n(i){var l=t[i];if(void 0!==l)return l.exports;var o=t[i]={id:i,loaded:!1,exports:{}};return e[i].call(o.exports,o,o.exports,n),o.loaded=!0,o.exports}n.m=e,function(){var e=[];n.O=function(t,i,l,o){if(!i){var d=1/0;for(f=0;f<e.length;f++){i=e[f][0],l=e[f][1],o=e[f][2];for(var r=!0,s=0;s<i.length;s++)(!1&o||d>=o)&&Object.keys(n.O).every((function(e){return n.O[e](i[s])}))?i.splice(s--,1):(r=!1,o<d&&(d=o));if(r){e.splice(f--,1);var a=l();void 0!==a&&(t=a)}}return t}o=o||0;for(var f=e.length;f>0&&e[f-1][2]>o;f--)e[f]=e[f-1];e[f]=[i,l,o]}}(),function(){n.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return n.d(t,{a:t}),t}}(),function(){n.d=function(e,t){for(var i in t)n.o(t,i)&&!n.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:t[i]})}}(),function(){n.g=function(){if("object"===typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"===typeof window)return window}}()}(),function(){n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}}(),function(){n.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}}(),function(){n.nmd=function(e){return e.paths=[],e.children||(e.children=[]),e}}(),function(){var e={93:0};n.O.j=function(t){return 0===e[t]};var t=function(t,i){var l,o,d=i[0],r=i[1],s=i[2],a=0;if(d.some((function(t){return 0!==e[t]}))){for(l in r)n.o(r,l)&&(n.m[l]=r[l]);if(s)var f=s(n)}for(t&&t(i);a<d.length;a++)o=d[a],n.o(e,o)&&e[o]&&e[o][0](),e[o]=0;return n.O(f)},i=self["webpackChunk"]=self["webpackChunk"]||[];i.forEach(t.bind(null,0)),i.push=t.bind(null,i.push.bind(i))}();var i=n.O(void 0,[998],(function(){return n(8012)}));i=n.O(i)})();