// @requires Ngn.frm.DdItemSelectDepending
Ngn.Form.El.DdItemSelectDepending = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    var data = this.eRow.getElement('.data');
    c('all ok');
    Ngn.frm.ConsecutiveSelect.factory(Ngn.frm.DdItemSelectDepending, this, {
      strName: data.get('data-strName'),
      parentTagFieldName: data.get('data-parentTagFieldName'),
      fieldName: data.get('data-fieldName'),
      itemsSort: data.get('data-itemsSort')
    });
  }

});