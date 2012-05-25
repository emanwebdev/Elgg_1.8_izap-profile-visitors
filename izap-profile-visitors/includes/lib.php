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

/**
 *funtion to mark the visitor
 */
function izapMarkVisitor()
{
    $ProfileEntity =  elgg_get_page_owner_entity();
    $ProfileName = $ProfileEntity->name;
    $ProfileGuid = $ProfileEntity->guid;
    $ProfileOwner = $ProfileEntity->owner_guid;
    $ProfileAccess = $ProfileEntity->access_id;

    $VisitorEntity = elgg_get_logged_in_user_entity();
    $VisitorName = $VisitorEntity->name;
    $VisitorGuid = $VisitorEntity->guid;

    $VisitorsArray = array();

    if(($VisitorGuid != $ProfileGuid) && $VisitorEntity && $ProfileEntity) {

        $md = elgg_get_metadata(array('guid' => $ProfileGuid, 'metadata_name' => 'izapProfileVisitor', 'limit' => 0));
        $Metadata = array();
        if ($md && count($md) == 1) {
          $Metadata = $md[0];
        } else {
          $Metadata = $md;
        }

        if($Metadata) {
            $Id = $Metadata->id;
            $VisitorsArray = unserialize($Metadata->value);
            array_unshift($VisitorsArray, $VisitorGuid);
            $InsertArray = array_slice(array_unique($VisitorsArray), 0, 10);
            $InsertArray = serialize($InsertArray);
            izap_update_metadata($Id, 'izapProfileVisitor', $InsertArray, 'text', $ProfileOwner, ACCESS_PUBLIC);
        } else {
            array_unshift($VisitorsArray, $VisitorGuid);
            $InsertArray = serialize($VisitorsArray);
            create_metadata($ProfileGuid, 'izapProfileVisitor', $InsertArray, 'text', $ProfileOwner, ACCESS_PUBLIC);
        }
    }
}

/**
*function to update the metadata
*same as the update_metadata, only made metadata editable
*/
function izap_update_metadata($id, $name, $value, $value_type, $owner_guid, $access_id)
{
    global $CONFIG;

    $id = (int)$id;

    if (!$md = elgg_get_metadata_from_id($id)) {
        return false;
    }

    // If memcached then we invalidate the cache for this entry
    static $metabyname_memcache;
    if ((!$metabyname_memcache) && (is_memcache_available())) {
        $metabyname_memcache = new ElggMemcache('metabyname_memcache');
    }
    if ($metabyname_memcache) $metabyname_memcache->delete("{$md->entity_guid}:{$md->name_id}");

    //$name = sanitise_string(trim($name));
    //$value = sanitise_string(trim($value));
    $value_type = detect_extender_valuetype($value, sanitise_string(trim($value_type)));

    $owner_guid = (int)$owner_guid;
    if ($owner_guid==0) {
        $owner_guid = elgg_get_logged_in_user_guid();
    }

    $access_id = (int)$access_id;

    $access = get_access_sql_suffix();

    // Support boolean types (as integers)
    if (is_bool($value)) {
        if ($value) {
            $value = 1;
        } else {
            $value = 0;
        }
    }

    // Add the metastring
    $value = add_metastring($value);
    if (!$value) {
        return false;
    }

    $name = add_metastring($name);
    if (!$name) {
        return false;
    }

    // If ok then add it
    $result = update_data("UPDATE {$CONFIG->dbprefix}metadata set value_id='$value', value_type='$value_type', access_id=$access_id, owner_guid=$owner_guid where id=$id and name_id='$name'");
    if ($result!==false) {
        $obj = elgg_get_metadata_from_id($id);
        if (elgg_trigger_event('update', 'metadata', $obj)) {
            return true;
        } else {
        elgg_delete_metadata(array('metadata_id' => $id));
        }
    }

    return $result;
}

/**
*function to return array of visitors
*/
function izapVisitorList()
{
    global $CONFIG;
    $ProfileEntity = elgg_get_page_owner_entity();

    if(!$ProfileEntity) {
        $ProfileEntity = elgg_get_logged_in_user_entity();
    }

    $ProfileName = $ProfileEntity->name;
    $ProfileGuid = $ProfileEntity->guid;
    $ProfileOwner = $ProfileEntity->owner_guid;
    $ProfileAccess = $ProfileEntity->access_id;
    $meta_name = add_metastring('izapProfileVisitor');
    $Metadata = get_data("SELECT m.*, n.string as name, v.string as value from {$CONFIG->dbprefix}metadata m JOIN {$CONFIG->dbprefix}entities e ON e.guid = m.entity_guid JOIN {$CONFIG->dbprefix}metastrings v on m.value_id = v.id JOIN {$CONFIG->dbprefix}metastrings n on m.name_id = n.id where m.entity_guid={$ProfileGuid} and m.name_id={$meta_name}", "row_to_elggmetadata");
    if($Metadata) {
        return unserialize($Metadata[0]->value);
    }
}
