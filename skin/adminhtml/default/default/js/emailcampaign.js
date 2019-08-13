document.observe("dom:loaded", function() {

	var loadFormAjaxUrl = $('emailcampaign_loadFormAjaxUrl');
	
	function observe()
	{
		var select = $('emailcampaign_code');
		var tabs = $('page:left');
		
		$$('#emailcampaign_emailcampaign_usage_form tr').invoke('hide');
		var usage = $('emailcampaign_usage_' + $F(select)).up(1);
		
	    $$('#emailcampaign_emailcampaign_warning_form tr').invoke('hide');
		var warning = $('emailcampaign_warning_' + $F(select)).up(1);
		
		usage.show();
		$('emailcampaign_warning_' + $F(select)).setStyle({color:'#FF0000'});
		
		var templateSelect = $('emailcampaign_template_id');
		var a = $$('#emailcampaign_preview > a')[0];
		var href = a.readAttribute('href');
		
		select.observe('change', function(event, element) {
			usage.hide();
			usage = $('emailcampaign_usage_' + $F(select)).up(1);
			usage.show();

	        warning.hide();
	        warning = $('emailcampaign_warning_' + $F(select)).up(1);
	        $('emailcampaign_warning_' + $F(select)).setStyle({color:'#FF0000'});
	        warning.show();	
		});
		
		select.observe('change', function(event, element) {
			new Ajax.Request($F(loadFormAjaxUrl), {
				method:'get',
				parameters: $('edit_form').serialize(),
			    onSuccess: function(response) {
			    	var data = response.responseText.evalJSON();
			    	var i = 0;
			    	
			    	$$('#edit_form > div').each(function (form) {
			    		if (i++ > 0) {
			    			form.remove();
			    		}
			    	})
			    	tabs.update(data.tabs);
			    	tabs.childElements('script', function (script) {
			    		eval(script.innerHTML);
			    	});
			    	observe();
			    }
			});
		});
		
		templateSelect.observe('change', function(event, element) {
		    var templateData = $F(templateSelect);
		    // I check if the template id is an integer. If not, it's a default template I have to load. Cf http://support.adfab.fr/support/browse/ECAMPAIGN-49
		    if (!isNaN(templateData) && parseInt(Number(templateData)) == templateData && (templateData + "").replace(/ /g,'') !== ""){
		        a.href = href.replace(/\/id\/[0-9]*\//g, '/id/'+templateData+'/');
		    }else{
		        // change it to a form
		        a.href = href.replace(/\/id\/[0-9]*\//g, '/id/'+templateData+'/');
		    }
		});
		setTimeout(function(){
			$('emailcampaign_tabs_form_section_info').click();
		}, 20);
	}
	
	observe();
	
});