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

$MaxVistors = $vars['entity']->num_display;

if(!$MaxVistors)
$MaxVistors = 5;

$VisitorArray = izapVisitorList();
$VisitorArray = array_slice($VisitorArray, 0, $MaxVistors);
$TotalVisitor = count($VisitorArray);
$online = find_active_users();

foreach ($online as $key => $entity) {
    $onlineUsers[] = $entity->guid;
}

if($TotalVisitor)
{
    foreach($VisitorArray as $VisitorGuid) {
        $VisitorEntity = get_entity($VisitorGuid);
        $icon = elgg_view_entity_icon($VisitorEntity, 'small');

        if(in_array($VisitorGuid, $onlineUsers)) {
            $Visitors .= '<div class="izapWrapperOnline">' . $icon . '</div>';
        } else {
            $Visitors .= '<div class="izapWrapper">' . $icon . '</div>';
        }
    }
} else {
    $Visitors .= '<div align="center"><h3>' . elgg_echo('izapProfileVisitor:NoVisits') . '</h3></div>';
}
?>

<div class="izapMargin"><?php echo $Visitors;?>
<div style="clear:both"></div>
</div>
