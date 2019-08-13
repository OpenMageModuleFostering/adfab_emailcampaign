document.observe("dom:loaded", function() {
	var select = $('emailcampaign_code');
	
	$$('#emailcampaign_emailcampaign_usage_form tr').invoke('hide');
	var usage = $('emailcampaign_usage_' + $F(select)).up(1);
	
    $$('#emailcampaign_emailcampaign_warning_form tr').invoke('hide');
	var warning = $('emailcampaign_warning_' + $F(select)).up(1);
	
	usage.show();
	$('emailcampaign_warning_' + $F(select)).setStyle({color:'#FF0000'});
	
	select.observe('change', function(event, element) {
		usage.hide();
		usage = $('emailcampaign_usage_' + $F(select)).up(1);
		usage.show();

        warning.hide();
        warning = $('emailcampaign_warning_' + $F(select)).up(1);
        $('emailcampaign_warning_' + $F(select)).setStyle({color:'#FF0000'});
        warning.show();	
        	
	});
	
	var templateSelect = $('emailcampaign_template_id');
	var a = $$('#emailcampaign_preview > a')[0];
	var href = a.readAttribute('href');
	
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
});