<?php
/**
 * iZAP izap profile visitor
 *
 * @license GNU Public License version 3
 * @author iZAP Team "<support@izap.in>"
 * @link http://www.izap.in/
 *
 * iionly; Version 1.8:
 * compatibility elgg-1.8
 */

include dirname(__FILE__) . "/includes/lib.php";

function izapProfileVisitors()
{
    elgg_register_widget_type('izapProfileVisitors', elgg_echo('izapProfileVisitor:Widget'), elgg_echo('izapProfileVisitor:WidgetDescription'));

    elgg_extend_view('css/elgg', 'izapprofilevisitor/css');
    elgg_extend_view('profile/details', 'izapprofilevisitor/userdetails', 1);
}

// registering the pluing
elgg_register_event_handler('init', 'system', 'izapProfileVisitors', 10000);
