jQuery(window).load(function () {
	jQuery(".trigger_popup_fricc").click(function(){
		jQuery(this).next('.hover_bkgr_fricc').toggleClass('hide');
	})
});
jQuery('#demolist li').on('click', function(){
    jQuery('#roksearch_search_str').val(jQuery(this).text());
	jQuery("#dropdown-button").removeClass("toogleDropDown");
	jQuery("#menuIcon").removeClass("changeMenuIcon");
	jQuery("#caretIcon").removeClass("changeCaretIcon");
});
jQuery('#dropdown-button').on('click', function(){
	jQuery("#dropdown-button").toggleClass("toogleDropDown");
	jQuery("#menuIcon").toggleClass("changeMenuIcon");
	jQuery("#caretIcon").toggleClass("changeCaretIcon");
});
jQuery(window).click(function(e) {
	jQuery("#dropdown-button").removeClass("toogleDropDown");
	jQuery("#menuIcon").removeClass("changeMenuIcon");
	jQuery("#caretIcon").removeClass("changeCaretIcon");
});

jQuery(window).scroll(function(){
    if (jQuery(window).scrollTop() >= 120) {
        jQuery('header').addClass('fixed-header');
    }
    else {
        jQuery('header').removeClass('fixed-header');
    }
});

function openNav() {
    document.getElementById("mySidenav").style.width = "100%";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

jQuery('.bulk_buy_submit').prop("disabled", false);