<?

/*
 * $Date: 2010/03/11 11:41:29 $
 * $Revision: 1.0 $
 */

    $this->default_options =  array(
        "logged"           => array(
            "default"      => 0,
            "definedornot" => 1
        ),
        "clear_options"    => array(
            "default"      => 0,
            "definedornot" => 1
        ),
        "use_ga"           => array(
            "default"      => 0,
            "definedornot" => 1
        ),
        "use_gostats"      => array(
            "default"      => 0,
            "definedornot" => 1
        ),
        "use_custom"       => array(
            "default"      => 0,
            "definedornot" => 1
        ),
        "ga_id"            => array(
            "default"      => '',
            "regs"         => array('/^\d+-\d$/'),
            "stoper"       => create_function('',
                'return ! isset($_POST[\'use_ga\']);')
        ),
        "ga_type"          => array(
            "default"      => 'ga',
            "regs"         => array('/^(ga|urchin)$/'),
            "stoper"       => create_function('',
                'return ! isset($_POST[\'use_ga\']);')
        ),
        "gostats_id"       => array(
            "default"      => '',
            "regs"         => array('/^\d+$/'),
            "stoper"       => create_function('',
                'return ! isset($_POST[\'use_gostats\']);')
        ),
        "gostats_server"   => array(
            "default"      => '',
            "regs"         => array('/^(?:monster\.|c\d\.)?gostats\.\w{2,3}$/'),
            "stoper"       => create_function('',
                'return ! isset($_POST[\'use_gostats\']);')
        ),
        "gostats_type"     => array(
            "default"      => 'js',
            "regs"         => array('/^(js|html)$/'),
            "stoper"       => create_function('',
                'return ! isset($_POST[\'use_gostats\']);')
        ),
        "counter"          => array(
            "default"      => '',
            "minlen"       => 1,
            "stoper"       => create_function('',
                'return ! isset($_POST[\'use_custom\']);')
        ),
        "gostats_brand"    => array (
            "default"      => ''
        ),
        "ga"               => array (
            "default"      => "
<script type=\"text/javascript\">
    var gaJsHost = ((\"https:\" == document.location.protocol) ?
                    \"https://ssl.\" : \"http://www.\");
    document.write(unescape(\"%3Cscript src='\" + gaJsHost +
                            \"google-analytics.com/ga.js'\" +
                            \" type='text/javascript'%3E%3C/script%3E\"));
</script>
<script type=\"text/javascript\">
    try {
        var pageTracker = _gat._getTracker(\"UA-[zmg_analytics:GA_ID]\");
        pageTracker._trackPageview();
    } catch(err) {}
</script>"
        ),
        "urchin"         => array (
            "default"      => "
<script src=\"http://www.google-analytics.com/urchin.js\"
        type=\"text/javascript\">
</script>
<script type=\"text/javascript\">
    try {
        _uacct = \"UA-[zmg_analytics:GA_ID]\";
        urchinTracker();
    } catch(err) {}
</script>"
        ),
        "gostats_js"     => array (
            "default"      => "
<script type=\"text/javascript\"
        src=\"http://[zmg_analytics:GO_brand]/js/counter.js\">
</script>
<script type=\"text/javascript\">
    _gos='[zmg_analytics:GO_server]';
    _goa=[zmg_analytics:GO_ID];
    _got=5;
    _goi=1;
    _goz=0;
    _gol='log analysis';
    _GoStatsRun();
</script>"
        ),
        "gostats_html"   => array (
            "default"      => "
<a target=\"_blank\" title=\"log analysis\"
   href=\"http://[zmg_analytics:GO_brand]\">
<img alt=\"log analysis\" style=\"border-width:0\"
     src=\"http://[zmg_analytics:GO_server]/bin/count/a_[zmg_analytics:GO_ID]/t_5/i_1/counter.png\" />
</a>"
        )
    );

