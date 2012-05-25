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

// set default value
if (!isset($vars['entity']->num_display)) {
        $vars['entity']->num_display = 5;
}

$params = array(
        'name' => 'params[num_display]',
        'value' => $vars['entity']->num_display,
        'options' => array(5, 10, 15, 20),
);
$dropdown = elgg_view('input/dropdown', $params);

?>
<div>
        <?php echo elgg_echo('izapProfileVisitor:NumberOfVisitors'); ?>:
        <?php echo $dropdown; ?>
</div>
