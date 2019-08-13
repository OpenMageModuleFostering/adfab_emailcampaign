Promoboost = Class.create();
Promoboost.prototype = {

    fields: [
        'rule_discount_qty',
        'rule_discount_step',
        'rule_apply_to_shipping',
        'rule_simple_free_shipping',
        'rule_discount_max',
        'rule_discount_amount_max',
        'rule_discount_repeat',
        'rule_discount_amount_step',
        'promo_catalog_edit_tabs_gifts_section'
    ],

    configuration: null,
    defaultLabels: null,

    initialize : function(elementId) {
        this.elt = $(elementId);
        if ( typeof PromoboostConfig != 'undefined' && typeof PromoboostConfig[elementId] != 'undefined') {
            this.configuration = PromoboostConfig[elementId];
        }
        this.defaultLabels = {}; 
        try{
            this.toggleDisplay();
        } catch(error){

        }
        if (!this.elt) {
            return;
        }
        this.childLoader = this.onChangeChildLoad.bindAsEventListener(this);
        Event.observe(this.elt, 'change', this.childLoader);
    },

    onChangeChildLoad : function(event) {
        element = Event.element(event);
        this.elementChildLoad(element);
    },

    elementChildLoad : function(element, callback) {
        this.callback = callback || false;
        this.toggleDisplay();
    },

    hideAll: function(){
        for( var i = 0; i < this.fields.length ; i++ ) {
            $(this.fields[i]).up('tr,li').hide();
        }
    },

    toggleDisplay: function(){
	this.hideAll();
        var ruleType = this.elt.value;
        var c = { show:['rule_discount_qty','rule_discount_step','rule_apply_to_shipping','rule_simple_free_shipping'], labels: this.defaultLabels };
        if ( typeof this.configuration[ruleType] != 'undefined' ) {
             c = this.configuration[ruleType];
        }
        if ( ( typeof c != 'undefined' ) && ( typeof c.show != 'undefined' ) ) {
            for ( var i = 0; i < c.show.length ; i++ ) {
                $(c['show'][i]).up('tr,li').show();
            }
        }
        if ( ( typeof c != 'undefined' ) && ( typeof c.labels != 'undefined' ) ) {
            for ( var key in c.labels ) {
                var ls = $$('label[for="'+key+'"]');
                for ( var j = 0; j < ls.length ; j++ ) {
                    if ( this.defaultLabels[key] == 'undefined' ) {
                        this.defaultLabels[key] = ls[j].firstChild.nodeValue;
                    }
                    ls[j].firstChild.nodeValue = c.labels[key];
                }
            }
        }
	$('rule_manual_fieldset').firstDescendant().firstDescendant().firstDescendant().innerHTML = ( ( typeof c != 'undefined' ) && ( typeof c.labels != 'undefined' ) && ( typeof c.labels['rule_manual_fieldset'] != 'undefined' ) ) ? c.labels['rule_manual_fieldset'] : '';
    }
}

Event.observe(window, "load", function(){
    var actionRule = new Promoboost('rule_simple_action');
});
