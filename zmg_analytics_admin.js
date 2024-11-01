/*
 * $Date: 2010/03/11 11:41:29 $
 * $Revision: 1.0 $
 */

/******************************************************************************/
jQuery(document).ready(init_clickable_checkboxs);

/******************************************************************************/
function init_clickable_checkboxs (id)
{
    var transfer = {
        "zmg-analytics-use_ga"     : "#zmg-analytics-google_analytics",
        "zmg-analytics-use_gostats": "#zmg-analytics-gostats_counter",
        "zmg-analytics-use_custom" : "#zmg-analytics-custom_counter"
    };

    jQuery('.clickable').each(function () {
        if (!jQuery(this).attr('checked')) {
            jQuery(transfer[jQuery(this).attr('id')]).hide();
        }
    });

    jQuery('.clickable').click(function () {
        if (jQuery(this).attr('checked')) {
            jQuery(transfer[jQuery(this).attr('id')]).fadeIn();
        } else {
            jQuery(transfer[jQuery(this).attr('id')]).fadeOut();
        }
    });
}

