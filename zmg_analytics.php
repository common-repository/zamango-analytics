<?

/*
 * $Date: 2010/03/11 11:41:29 $
 * $Revision: 1.0 $
 */

/*
Plugin Name: Zamango Analytics
Plugin URI: http://www.zamango.com/
Description: This plugin makes it simple to add Google Analytics, GoStats or another analytic counter to your WordPress
Author: Zamango
Version: 1.0
Requires at least: 2.8
Author URI: http://www.zamango.com/
License: GPL
*/

require_once('zmg_admin.php');

/******************************************************************************/
if (!class_exists('zmg_analytics'))
{
    class zmg_analytics extends zmg_admin
    {
        var $hook        = 'zmg-analytics';
        var $version     = '1.0';
        var $page_title  = 'Analytics';
        var $menu_title  = 'Analytics';
        var $filename    = 'zamango-analytics/zmg_analytics.php';
        var $options     = array();

        /**********************************************************************/
        function zmg_analytics()
        {
            require_once('zmg_analytics_defaults.php');

            $this->dir_name    = basename(dirname(__FILE__));
            $this->plugin_url  = WP_PLUGIN_URL . '/' . $this->dir_name;
            $this->plugin_path = WP_PLUGIN_DIR . '/' . $this->dir_name;

            $this->reg_deactivation_hook();

            add_action('init', array($this, 'admin_init'), 3);
            add_action('init', array($this, 'init'));
        }

        /**********************************************************************/
        function deactivate()
        {
            $this->options = get_option($this->hook);

            if ($this->options['clear_options']) delete_option($this->hook);
        }

        /**********************************************************************/
        function init()
        {
            $this->add_js('zmg-analytics-admin-js', $this->plugin_url .
                          '/zmg_analytics_admin.js', true);

            add_action('wp_footer', array($this, 'counters'));
        }

        /**********************************************************************/
        function counters()
        {
            if ($this->options['logged'] && is_user_logged_in()) return;

            echo "\n<!-- Zamango Analytics " . $this->version . " -->\n";
            echo "<div id='zmg_analytics' style='display:none;'>\n";

            if ($this->options['use_ga'] == 1)
            {
                $counter = ($this->options['ga_type'] == "ga") ?
                            $this->options['ga'] :
                            $this->options['urchin'];

                $counter = str_replace("[zmg_analytics:GA_ID]",
                                       $this->options['ga_id'], $counter);

                echo $counter . "\n";
            }

            if ($this->options['use_gostats'] == 1)
            {
                $counter = ($this->options['gostats_type'] == "js") ?
                            $this->options['gostats_js'] :
                            $this->options['gostats_html'];

                $counter = str_replace("[zmg_analytics:GO_brand]",
                                       $this->options['gostats_brand'],
                                       $counter);
                $counter = str_replace("[zmg_analytics:GO_server]",
                                       $this->options['gostats_server'],
                                       $counter);
                $counter = str_replace("[zmg_analytics:GO_ID]",
                                       $this->options['gostats_id'],
                                       $counter);

                echo $counter . "\n";
            }

            if ($this->options['use_custom'] == 1)
            {
                $counter = $this->options['counter'];

                echo "\n" . $counter . "\n";
            }

            echo "</div>";
            echo "\n<!-- Zamango Analytics " . $this->version . " -->\n";
        }

        /**********************************************************************/
        function plugin_option_page_content()
        {

            if (isset($_POST['ZMG_SUBMIT']))
            {
                $this->validate_params();

                if (isset($this->errors))
                {
                    echo $this->disappearing_message(
                        __('Incorrect settings value', $this->hook)
                    );

                    if (isset($this->errors['ga_id']))
                        $this->options['use_ga'] = 2;

                    if (isset($this->errors['gostats_id']) ||
                        isset($this->errors['gostats_server']))
                        $this->options['use_gostats'] = 2;

                    if (isset($this->errors['counter']))
                        $this->options['use_custom'] = 2;
                }
                else
                {
                    preg_match("/gostats\.(\w{2,3})$/",
                               $this->options['gostats_server'], $res);

                    $this->options['gostats_brand'] = "gostats." . $res[1];

                    $this->save_options();

                    echo $this->disappearing_message(
                        __('Settings have been saved', $this->hook)
                    );
                }
            }

            $this->form_begin($this->hidden('ZMG_SUBMIT'));

            $this->postbox($this->hook . '-general_settings',
                           __('General settings', $this->hook),
                           $this->general_settings());
            $this->postbox($this->hook . '-google_analytics',
                           __('Google Analytics', $this->hook),
                           $this->google_analytics());
            $this->postbox($this->hook . '-gostats_counter',
                           __('GoStats counter', $this->hook),
                           $this->gostats_counter());
            $this->postbox($this->hook . '-custom_counter',
                           __('Custom counter', $this->hook),
                           $this->custom_counter());

            $this->form_end();
        }

        /**********************************************************************/
        function general_settings()
        {
            $rows = array();
            $row  = array();
            $html = '';

            $row[] = $this->elem('');

            $html  = $this->checkbox('logged', '1',
                                     $this->options['logged'],
                                     __('Ignore logged in users', $this->hook));

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();
            $html   = '';

            $row[] = $this->elem('');

            $html  = $this->checkbox('clear_options', '1',
                                     $this->options['clear_options'],
                                     __('Delete options when deactivating the ' .
                                        'plugin', $this->hook));

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();

            $html  = __('Google Analytics', $this->hook);

            $row[] = $this->elem($html);

            $html  = $this->checkbox('use_ga', '1',
                                     $this->options['use_ga'],
                                     __('Use Google Analytics counter?',
                                        $this->hook), 'clickable');

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();

            $html  = __('GoStats counter', $this->hook);

            $row[] = $this->elem($html);

            $html  = $this->checkbox('use_gostats', '1',
                                     $this->options['use_gostats'],
                                     __('Use GoStats counter?', $this->hook),
                                     'clickable');

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();

            $html  = __('Custom counter', $this->hook);

            $row[] = $this->elem($html);

            $html  = $this->checkbox('use_custom', '1',
                                     $this->options['use_custom'],
                                     __('Use custom counter?', $this->hook),
                                     'clickable');

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();
            $row[]  = $this->elem('');
            $html   = $this->submit(__('Save', $this->hook));
            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);

            $content .= $this->table($rows);

            return $content;
        }

        /**********************************************************************/
        function google_analytics()
        {
            $content  = '<p>';
            $content .= __('Find the Account ID, starting with UA- in your ' .
                           'account overview', $this->hook);
            $content .= '</p>';
            $content .= '<img src="' . $this->plugin_url .
                        '/img/GA-help.png" />';

            $disc_link = $this->add_description(
                            __('Help on Google Analytics UID', $this->hook),
                            $content, 'google_analytics');

            $rows = array();
            $row  = array();

            $html = __('Google Analytics UID', $this->hook);

            $row[] = $this->elem($html);

            $label  = __('Your Google Analytics UID', $this->hook);
            $label .= ' <a href="#help_ga"';
            $label .= 'onClick="' . $disc_link . '"';
            $label .= 'return false;" title="';
            $label .= __('Google Analytics UID', $this->hook) . '">';
            $label .= __('(where can I find it?)', $this->hook);
            $label .= '</a>';

            $html  = "<span>UA-<span>";
            $html .= $this->text('ga_id', $this->options['ga_id'], $label, '');

            if ($this->errors['ga_id'])
                $html .= $this->error_message(
                    __($this->errors['ga_id'], $this->hook));

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();

            $html = __('Type of Google Analytics counter', $this->hook);

            $row[] = $this->elem($html);

            $ul = array();

            $ul[] = $this->elem(
                        $this->radio('ga_type', 'ga',
                                     $this->options['ga_type'] == 'ga',
                                     __('new (asymmetric) counter code',
                                        $this->hook)));
            $ul[] = $this->elem(
                        $this->radio('ga_type', 'urchin',
                                     $this->options['ga_type'] == 'urchin',
                                     __('old code - Urchin Tracker',
                                        $this->hook)));

            $html  = $this->ul($ul, 'col');

            if ($this->errors['ga_type'])
                $html .= $this->error_message(
                    __($this->errors['ga_type'], $this->hook));

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();
            $row[]  = $this->elem('');
            $html   = $this->submit(__('Save', $this->hook));
            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);

            $content = $this->table($rows);

            return $content;
        }

        /**********************************************************************/
        function gostats_counter()
        {
            $content  = '<p>';
            $content .= __('Open the analytics page for your site and pay ' .
                           'attention to the address line', $this->hook);
            $content .= '</p>';

            $ul = array();

            $ul[] = $this->elem(
                        '<div>' .
                        __('this is your GoStats ID', $this->hook) .
                        '</div><img src="' . $this->plugin_url .
                        '/img/GS-ID-help.png" />'
                    );

            $ul[] = $this->elem(
                        '<div>' .
                        __('this is your GoStats server', $this->hook) .
                        '</div><img src="' . $this->plugin_url .
                        '/img/GS-server-help.png" />'
                    );

            $content .= $this->ul($ul, 'col');

            $disc_link = $this->add_description(__('Help on GoStats counter',
                                                   $this->hook), $content,
                                                'gostats_counter');

            $rows = array();
            $row  = array();

            $html = __('GoStats ID', $this->hook);

            $row[] = $this->elem($html);

            $label  = __('Your GoStats ID', $this->hook);
            $label .= ' <a href="#help_gostats"';
            $label .= 'onClick="' . $disc_link . '"';
            $label .= 'return false;" title="';
            $label .= __('GoStats', $this->hook) . '">';
            $label .= __('(where can I find it?)', $this->hook);
            $label .= '</a>';

            $html  = $this->text('gostats_id', $this->options['gostats_id'],
                                 $label);

            if ($this->errors['gostats_id'])
                $html .= $this->error_message(
                    __($this->errors['gostats_id'], $this->hook));

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();

            $html = __('GoStats server', $this->hook);

            $row[] = $this->elem($html);

            $label  = __('Your GoStats server', $this->hook);
            $label .= ' <a href="#help_gostats"';
            $label .= 'onClick="' . $disc_link . '"';
            $label .= 'return false;" title="';
            $label .= __('GoStats', $this->hook) . '">';
            $label .= __('(where can I find it?)', $this->hook);
            $label .= '</a>';

            $html  = $this->text('gostats_server',
                                 $this->options['gostats_server'],
                                 $label);

            if ($this->errors['gostats_server'])
                $html .= $this->error_message(
                    __($this->errors['gostats_server'], $this->hook));

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();

            $html = __('Type of GoStats counter', $this->hook);

            $row[] = $this->elem($html);

            $ul = array();

            $ul[] = $this->elem(
                        $this->radio('gostats_type', 'js',
                                     $this->options['gostats_type'] == 'js',
                                     __('JavaScript based counter',
                                        $this->hook)));
            $ul[] = $this->elem(
                        $this->radio('gostats_type', 'html',
                                     $this->options['gostats_type'] == 'html',
                                     __('HTML based counter', $this->hook)));

            $html  = $this->ul($ul, 'col');

            if ($this->errors['gostats_type'])
                $html .= $this->error_message(
                    __($this->errors['gostats_type'], $this->hook));

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();
            $row[]  = $this->elem('');
            $html   = $this->submit(__('Save', $this->hook));
            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);

            $content = $this->table($rows);

            return $content;
        }

        /**********************************************************************/
        function custom_counter()
        {
            $rows = array();
            $row  = array();

            $html = __('Your counter code', $this->hook);

            $row[] = $this->elem($html);

            $html = $this->textarea('counter', $this->options['counter'], 13);

            if ($this->errors['counter'])
                $html .= $this->error_message(
                    __($this->errors['counter'], $this->hook));

            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);
            $row    = array();
            $row[]  = $this->elem('');
            $html   = $this->submit(__('Save', $this->hook));
            $row[]  = $this->elem($html);
            $rows[] = $this->elem($row);

            $content = $this->table($rows);

            return $content;
        }
    }

    $zmg_analytics = new zmg_analytics();
}

