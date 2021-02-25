<template>
  <form v-html="block.optionsHtml" class="options-inner">
  </form>
</template>

<script>
import { mapMutations, mapState, mapActions } from 'vuex';
import Mixin from '../../mixin';

export default {
  computed: {
    ...mapState([])
  },
  props: {
    block: Object
  },
  mounted: function() {
    this.setWatchers();
  },
  methods: {
    setWatchers: function () {
      let form = $(this.$el);
      let _this = this;
      $(this.$el).find('input, select, .lightswitch').change(function () {
        _this.validate();
        let block = {..._this.block};
        block.options = form.serializeJSON();
        _this.updateBlock(block);
      });
    },
    validate: function () {
      let _this = this;
      $.each($(this.$el).find('input, select'), function () {
        let errors = [];
        $(this).removeClass('error');
        if ($(this).attr('required') && $(this).val().trim() == '') {
          let label = $(this).closest('.field').find('label');
          let name = label.length ? label.html() : $(this).attr('name');
          $(this).addClass('error');
          errors.push(_this.t(name + ' is required'));
        }
        _this.setBlockErrors({block: _this.block, errors: errors});
      });
    },
    ...mapMutations(['updateBlock', 'setBlockErrors']),
    ...mapActions([])
  },
  mixins: [Mixin],
};
</script>

<style lang="scss" scoped>
  .options-inner {
    padding: 15px;
  }
</style>
