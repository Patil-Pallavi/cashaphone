/*
 *Creates dynamically view visfield appearance
 *according to selected field type and subtype

 *for Joomla 2.5
*/

var editValue = null;

if (window.addEventListener)
{
	window.addEventListener("load", initPage, false);
} 
else if (window.attachEvent)
{ 
	var r = window.attachEvent("onload", initPage); 
} 
else 
{
	window.alert("Problem to add EventListener to Window Object !");  
}

function initPage() 
{	
	typeFieldInit();
}

//hide parameters from "defaultvalue" for all field types
function hiddenProperties() 
{
    document.getElementById('visf_text').style.display = "none";
    document.getElementById('visf_email').style.display = "none";
    document.getElementById('visf_date').style.display = "none";
    document.getElementById('visf_url').style.display = "none";
    document.getElementById('visf_number').style.display = "none";
    document.getElementById('visf_password').style.display = "none";
    document.getElementById('visf_hidden').style.display = "none";
    document.getElementById('visf_textarea').style.display = "none";
    document.getElementById('visf_checkbox').style.display = "none";
    document.getElementById('visf_multicheckbox').style.display = "none";
    document.getElementById('visf_radio').style.display = "none";
    document.getElementById('visf_select').style.display = "none";
    document.getElementById('visf_file').style.display = "none";
    document.getElementById('visf_image').style.display = "none";
    document.getElementById('visf_reset').style.display = "none";
    document.getElementById('visf_submit').style.display = "none";
    document.getElementById('visf_fieldsep').style.display = "none";
    document.getElementById('visf_pagebreak').style.display = "none";
    document.getElementById('visf_calculation').style.display = "none";
    document.getElementById('visf_location').style.display = "none";
    document.getElementById('visf_signature').style.display = "none";
}

//initialise field, display parameters for selected field type 
function typeFieldInit() 
{	
	hiddenProperties();
    var ffield = 'visf_' + getSelectedFieldType();
	
    //no type set yet
    //or sumit or reset which have not hidden properties and so nothing to display
	if ((ffield != 'visf_0') && (ffield != 'visf_submit') && (ffield != 'visf_reset'))
	{
        document.getElementById(ffield).style.display = "";
	}
    setRequiredAsterix ();
    editOnlyFieldChange();
}

//perform actions which are necessary when the type of a field is changed
function typeFieldChange() 
{
	hiddenProperties();
    var ffield = 'visf_' + getSelectedFieldType();
	
    //no type set yet
    //or sumit or reset which have not hidden properties and so nothing to display
	if ((ffield != 'visf_0') && (ffield != 'visf_submit') && (ffield != 'visf_reset'))
	{
        document.getElementById(ffield).style.display = "";
	}
    //Insert an asterix for required options
    setRequiredAsterix ()
}

function formatFieldDateChange(o, ffield, text, useNewCalendar)
{
    if (text)
    {
        resetSelectValue(o, ffield);
        alert(text);
        return false;
    }
    else {
        var calendarsToChange = ['tdate_calender', 'tdate_calender_min', 'tdate_calender_max'];
        var n = calendarsToChange.length;
        for (var i = 0; i < n; i++) {
            // set selected in dateformat select list to new value
            formatFieldDateChangeSelected();

            // setup calendar with correct dateformat
            formatDateCalendarChange(useNewCalendar, calendarsToChange[i]);
        }
    }
}

function formatDateChangeInputValue (id)
{
    var el = document.getElementById('jform_defaultvalue_' + id)
    var date = el.value;
	
	// if there is a date value set, change date format acording to selected listbox value
	if (! date == "") 
	{	
		// find date delimiter
		var date_delimiter = date.match(/\/|-|\./);
		var date_parts = date.split(date_delimiter[0]);

		// get date parts. Each date_delimiter represents a defined date format and a fix position of date parts
		switch (date_delimiter[0]) {
			case "/" :
				var month = date_parts[0];
				var day = date_parts[1];
				var year = date_parts[2];
				break;
			case "-" :
				var year = date_parts[0];
				var month = date_parts[1];
				var day = date_parts[2];
				break;
			case "." :
				var day = date_parts[0];
				var month = date_parts[1];
				var year = date_parts[2];
				break;
		}

		// get new date output format
        var d_format = document.getElementById('jform_defaultvalue_tdateformat_row').value;
	
		//find date format delimiter
		var d_format_delimiter = d_format.match(/\/|-|\./);
		
		// construct the formated date string. Each date format delimiter represents a defined date format and a fix position on date parts
		switch (d_format_delimiter[0]) 
		{
			case '/' :
				var formatted_date = month + d_format_delimiter + day + d_format_delimiter + year;
				break;
			case '-' :
				var formatted_date = year + d_format_delimiter + month + d_format_delimiter + day;
				break;
			case '.' :
				var formatted_date = day + d_format_delimiter + month + d_format_delimiter + year;
				break;
		}
        el.setAttribute('data-alt-value', formatted_date);
        el.value = formatted_date;
	}
}

function formatFieldDateChangeSelected () 
{
    for(i=document.getElementById('jform_defaultvalue_tdateformat_row').options.length-1;i>=0;i--) {
        if(document.getElementById('jform_defaultvalue_tdateformat_row').options[i].getAttribute('selected')) {
            document.getElementById('jform_defaultvalue_tdateformat_row').options[i].removeAttribute('selected');
        }
        if(document.getElementById('jform_defaultvalue_tdateformat_row').options[i].selected) {
            document.getElementById('jform_defaultvalue_tdateformat_row').options[i].setAttribute('selected', 'selected');
        }
    }
}

function formatDateCalendarChange (useNewCalendar, id)
{
	// get new date output format
    var d_format = document.getElementById('jform_defaultvalue_tdateformat_row').value;
    var btn = (document).getElementById('jform_defaultvalue_' + id + '_btn');
	
	// get dateformat for php and for javascript
	d_format = d_format.split(';');

	if(!useNewCalendar) {
        Calendar.setup({
            // Id of the input field
            inputField: "jform_defaultvalue_" + id,
            // Format of the input field
            ifFormat: d_format[1], //"%d.%m.%Y",
            // Trigger for the calendar (button ID)
            button: "jform_defaultvalue_" + id  + "_img",
            // Alignment (defaults to "Bl")
            align: "Tl",
            singleClick: true,
            firstDay: 0
        });
        formatDateChangeInputValue (id);
    }
    else
    {
        formatDateChangeInputValue (id);
        var calendar = btn.parentNode.parentNode.parentNode.querySelectorAll('.field-calendar')[0];
        var instance = calendar._joomlaCalendar;
        if (instance)
        {
            instance.params.dateFormat =  d_format[1];
        }
    }
}

//we need to restict some actions for fields which are restrictors and give an error message
function fieldUsed(o, ffield, msg)
{
    if (o.id.indexOf('editonlyfield') > 0)
    {
        var idx = o.selectedIndex;
        var selected = o[idx].value;
        var selectedValue = selected[0].value;
        if (selectedValue == "0")
        {
            return true;
        }
    }
    resetSelectValue(o, ffield);
    window.alert(msg);
    return false;
}

//set asterix in labels for parameters which are required
//we cannot use Joomla! form field attribute required because we get an error when a hidden parameter which is required is not set and we try to save the visforms field
function setRequiredAsterix ()
{
    var sel = getSelectedFieldType();
    switch (sel)
    { 
        case 'checkbox' :
            var el = [document.getElementById('jform_defaultvalue_f_checkbox_attribute_value-lbl')];
            break;
        case 'image':
            var el = [document.getElementById('jform_defaultvalue_f_image_attribute_alt-lbl')];
            el.push (document.getElementById('jform_defaultvalue_f_image_attribute_src-lbl')); 
            break;
        case 'multicheckbox' :
            var el = [document.getElementById('jform_defaultvalue_f_multicheckbox_list_hidden-lbl')];
            break;
        case 'select' :
            var el = [document.getElementById('jform_defaultvalue_f_select_list_hidden-lbl')];
            break;
        case 'radio' :
            var el = [document.getElementById('jform_defaultvalue_f_radio_list_hidden-lbl')];
            break;
        case 'location' :
            var el = [document.getElementById('jform_defaultvalue_f_location_defaultMapCenter_lat-lbl')];
            el.push (document.getElementById('jform_defaultvalue_f_location_defaultMapCenter_lng-lbl'));
            break;
        default :
            break;
    }
    if (el)
    {
    el.each (changeLabel);
    }
}


//insert asterix in label
function changeLabel (el, index, arr)
{
     var label = el.get('text') + '<span class="star"> *</span>';
     el.set('html', label);
}

//we use jQuery here
function editOnlyFieldChange()
{
    //remove all options exept the default option from the field list in parameter equalTo
    var fieldtype = getSelectedFieldType();
    var editonly = document.getElementById('jform_' + 'editonlyfield');
    if (editonly)
    {
        var equalToList = document.getElementById('jform_defaultvalue_f_' + fieldtype + '_validate_equalTo');
        var showWhenList = document.getElementById('jform_defaultvalue_f_' + fieldtype + '_showWhen');
        var uncheckedValue = document.getElementById('jform_defaultvalue_f_' + fieldtype + '_unchecked_value');
        var idx = editonly.selectedIndex;
        if (editonly.options[idx].value === "1")
        {
            //hide equalto and conditional fields
            if (equalToList)
            {
                equalToList.parentNode.parentNode.style.display = "none";
            }
            if (showWhenList)
            {
                showWhenList.parentNode.parentNode.style.display = "none";
            }
            if (uncheckedValue)
            {
                uncheckedValue.parentNode.parentNode.style.display = "none";
            }
        }
        else
        {
            //show equalto and conditional fields
            if (equalToList)
            {
                equalToList.parentNode.parentNode.style.display = "";
            }
            if (showWhenList)
            {
                showWhenList.parentNode.parentNode.style.display = "";
            }
            if (uncheckedValue)
            {
                uncheckedValue.parentNode.parentNode.style.display = "";
            }
        }
    }
}

function resetSelectValue(o, value)
{
    var selectbox = document.getElementById(o.id);
    var optlength = selectbox.options.length;
    for (var i = 0; i < optlength; i++)
    {
        if (selectbox.options[i].value == value) {
            selectbox.options[i].selected = true;
            jQuery('#' + o.id).trigger('liszt:updated');
        }
    }
}

function getSelectedFieldType() {
    var ft = document.getElementById('jform_typefield');
    var idx = ft.selectedIndex;
    return ft[idx].value;
}
