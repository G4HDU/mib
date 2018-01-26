<?php

/*
* e107 website system
*
* Copyright (C) 2008-2009 e107 Inc (e107.org)
* blankd under the terms and conditions of the
* GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
*
* e107 blank Plugin
*
* $Source: /cvs_backup/e107_0.8/e107_plugins/blank/admin_config.php,v $
* $Revision$
* $Date$
* $Author$
*
*/

require_once ("../../class2.php");
if (!getperms("P"))
{
    e107::redirect('admin');
    exit;
}


class plugin_mib_admin extends e_admin_dispatcher
{
    /**
     * Format: 'MODE' => array('controller' =>'CONTROLLER_CLASS'[, 'index' => 'list', 'path' => 'CONTROLLER SCRIPT PATH', 'ui' => 'UI CLASS NAME child of e_admin_ui', 'uipath' => 'UI SCRIPT PATH']);
     * Note - default mode/action is autodetected in this order:
     * - $defaultMode/$defaultAction (owned by dispatcher - see below)
     * - $adminMenu (first key if admin menu array is not empty)
     * - $modes (first key == mode, corresponding 'index' key == action)
     * @var array
     */
    protected $modes = array(
        'main' => array(
            'controller' => 'plugin_mib_admin_ui',
            'path' => null,
            'ui' => 'plugin_mib_admin_form_ui',
            'uipath' => null),
        'history' => array(
            'controller' => 'plugin_mib_history_ui',
            'path' => null,
            'ui' => 'plugin_mib_history_form_ui',
            'uipath' => null),
        'action' => array(
            'controller' => 'plugin_mib_action_ui',
            'path' => null,
            'ui' => 'plugin_mib_action_form_ui',
            'uipath' => null),
        'type' => array(
            'controller' => 'plugin_mib_type_ui',
            'path' => null,
            'ui' => 'plugin_mib_type_form_ui',
            'uipath' => null),
        );

    /* Both are optional
    * protected $defaultMode = null;
    * protected $defaultAction = null;
    */

    /**
     * Format: 'MODE/ACTION' => array('caption' => 'Menu link title'[, 'url' => '{e_PLUGIN}blank/admin_config.php', 'perm' => '0']);
     * Additionally, any valid e107::getNav()->admin() key-value pair could be added to the above array
     * @var array
     */
    protected $adminMenu = array(
        'main/list' => array('caption' => 'Manage Locations', 'perm' => '0'),
        'main/create' => array('caption' => LAN_CREATE, 'perm' => '0'),
        'main/prefs' => array('caption' => 'Settings', 'perm' => '0'),
        'other0' => array('divider' => true),
        'history/list' => array('caption' => 'Manage History', 'perm' => '0'),
        'history/create' => array('caption' => 'Create', 'perm' => '0'),
        'other1' => array('divider' => true),
        'action/list' => array('caption' => 'Manage Action', 'perm' => '0'),
        'action/create' => array('caption' => 'Create', 'perm' => '0'),
        'other2' => array('divider' => true),
        'type/list' => array('caption' => 'Manage Type', 'perm' => '0'),
        'type/create' => array('caption' => 'Create', 'perm' => '0'));
    /**
     * Optional, mode/action aliases, related with 'selected' menu CSS class
     * Format: 'MODE/ACTION' => 'MODE ALIAS/ACTION ALIAS';
     * This will mark active main/list menu item, when current page is main/edit
     * @var array
     */
    protected $adminMenuAliases = array('main/edit' => 'main/list');

    /**
     * Navigation menu title
     * @var string
     */
    protected $menuTitle = 'blank Menu';
}


class plugin_mib_admin_ui extends e_admin_ui
{
    // required
    protected $pluginTitle = "MiB";

    /**
     * plugin name or 'core'
     * IMPORTANT: should be 'core' for non-plugin areas because this
     * value defines what CONFIG will be used. However, I think this should be changed
     * very soon (awaiting discussion with Cam)
     * Maybe we need something like $prefs['core'], $prefs['blank'] ... multiple getConfig support?
     *
     * @var string
     */
    protected $pluginName = 'mib';

    /**
     * DB Table, table alias is supported
     * Example: 'r.blank'
     * @var string
     */
    protected $table = "mib_locations";

    /**
     * This is only needed if you need to JOIN tables AND don't wanna use $tableJoin
     * Write your list query without any Order or Limit.
     *
     * @var string [optional]
     */
    protected $listQry = "";
    //

    // optional - required only in case of e.g. tables JOIN. This also could be done with custom model (set it in init())
    //protected $editQry = "SELECT * FROM #blank WHERE mib_id = {ID}";

    // required - if no custom model is set in init() (primary id)
    protected $pid = "mib_locations_id";

    // optional
    protected $perPage = 20;

    protected $batchDelete = true;

    //	protected \$sortField		= 'somefield_order';


    //	protected \$sortParent      = 'somefield_parent';


    //	protected \$treePrefix      = 'somefield_title';


    //TODO change the mib_url type back to URL before blank.
    // required
    /**
     * (use this as starting point for wiki documentation)
     * $fields format  (string) $field_name => (array) $attributes
     *
     * $field_name format:
     * 	'table_alias_or_name.field_name.field_alias' (if JOIN support is needed) OR just 'field_name'
     * NOTE: Keep in mind the count of exploded data can be 1 or 3!!! This means if you wanna give alias
     * on main table field you can't omit the table (first key), alternative is just '.' e.g. '.field_name.field_alias'
     *
     * $attributes format:
     * 	- title (string) Human readable field title, constant name will be accpeted as well (multi-language support
     *
     *  - type (string) null (means system), number, text, dropdown, url, image, icon, datestamp, userclass, userclasses, user[_name|_loginname|_login|_customtitle|_email],
     *    boolean, method, ip
     *  	full/most recent reference list - e_form::renderTableRow(), e_form::renderElement(), e_admin_form_ui::renderBatchFilter()
     *  	for list of possible read/writeParms per type see below
     *
     *  - data (string) Data type, one of the following: int, integer, string, str, float, bool, boolean, model, null
     *    Default is 'str'
     *    Used only if $dataFields is not set
     *  	full/most recent reference list - e_admin_model::sanitize(), db::_getFieldValue()
     *  - dataPath (string) - xpath like path to the model/posted value. Example: 'dataPath' => 'prefix/mykey' will result in $_POST['prefix']['mykey']
     *  - primary (boolean) primary field (obsolete, $pid is now used)
     *
     *  - help (string) edit/create table - inline help, constant name will be accpeted as well, optional
     *  - note (string) edit/create table - text shown below the field title (left column), constant name will be accpeted as well, optional
     *
     *  - validate (boolean|string) any of accepted validation types (see e_validator::$_required_rules), true == 'required'
     *  - rule (string) condition for chosen above validation type (see e_validator::$_required_rules), not required for all types
     *  - error (string) Human readable error message (validation failure), constant name will be accepted as well, optional
     *
     *  - batch (boolean) list table - add current field to batch actions, in use only for boolean, dropdown, datestamp, userclass, method field types
     *    NOTE: batch may accept string values in the future...
     *  	full/most recent reference type list - e_admin_form_ui::renderBatchFilter()
     *
     *  - filter (boolean) list table - add current field to filter actions, rest is same as batch
     *
     *  - forced (boolean) list table - forced fields are always shown in list table
     *  - nolist (boolean) list table - don't show in column choice list
     *  - noedit (boolean) edit table - don't show in edit mode
     *
     *  - width (string) list table - width e.g '10%', 'auto'
     *  - thclass (string) list table header - th element class
     *  - class (string) list table body - td element additional class
     *
     *  - readParms (mixed) parameters used by core routine for showing values of current field. Structure on this attribute
     *    depends on the current field type (see below). readParams are used mainly by list page
     *
     *  - writeParms (mixed) parameters used by core routine for showing control element(s) of current field.
     *    Structure on this attribute depends on the current field type (see below).
     *    writeParams are used mainly by edit page, filter (list page), batch (list page)
     *
     * $attributes['type']->$attributes['read/writeParams'] pairs:
     *
     * - null -> read: n/a
     * 		  -> write: n/a
     *
     * - dropdown -> read: 'pre', 'post', array in format posted_html_name => value
     * 			  -> write: 'pre', 'post', array in format as required by e_form::selectbox()
     *
     * - user -> read: [optional] 'link' => true - create link to user profile, 'idField' => 'author_id' - tells to renderValue() where to search for user id (used when 'link' is true and current field is NOT ID field)
     * 				   'nameField' => 'comment_author_name' - tells to renderValue() where to search for user name (used when 'link' is true and current field is ID field)
     * 		  -> write: [optional] 'nameField' => 'comment_author_name' the name of a 'user_name' field; 'currentInit' - use currrent user if no data provided; 'current' - use always current user(editor); '__options' e_form::userpickup() options
     *
     * - number -> read: (array) [optional] 'point' => '.', [optional] 'sep' => ' ', [optional] 'decimals' => 2, [optional] 'pre' => '&euro; ', [optional] 'post' => 'LAN_CURRENCY'
     * 			-> write: (array) [optional] 'pre' => '&euro; ', [optional] 'post' => 'LAN_CURRENCY', [optional] 'maxlength' => 50, [optional] '__options' => array(...) see e_form class description for __options format
     *
     * - ip		-> read: n/a
     * 			-> write: [optional] element options array (see e_form class description for __options format)
     *
     * - text -> read: (array) [optional] 'htmltruncate' => 100, [optional] 'truncate' => 100, [optional] 'pre' => '', [optional] 'post' => ' px'
     * 		  -> write: (array) [optional] 'pre' => '', [optional] 'post' => ' px', [optional] 'maxlength' => 50 (default - 255), [optional] '__options' => array(...) see e_form class description for __options format
     *
     * - textarea 	-> read: (array) 'noparse' => '1' default 0 (disable toHTML text parsing), [optional] 'bb' => '1' (parse bbcode) default 0,
     * 								[optional] 'parse' => '' modifiers passed to e_parse::toHTML() e.g. 'BODY', [optional] 'htmltruncate' => 100,
     * 								[optional] 'truncate' => 100, [optional] 'expand' => '[more]' title for expand link, empty - no expand
     * 		  		-> write: (array) [optional] 'rows' => '' default 15, [optional] 'cols' => '' default 40, [optional] '__options' => array(...) see e_form class description for __options format
     * 								[optional] 'counter' => 0 number of max characters - has only visual effect, doesn't truncate the value (default - false)
     *
     * - bbarea -> read: same as textarea type
     * 		  	-> write: (array) [optional] 'pre' => '', [optional] 'post' => ' px', [optional] 'maxlength' => 50 (default - 0),
     * 				[optional] 'size' => [optional] - medium, small, large - default is medium,
     * 				[optional] 'counter' => 0 number of max characters - has only visual effect, doesn't truncate the value (default - false)
     *
     * - image -> read: [optional] 'title' => 'SOME_LAN' (default - LAN_PREVIEW), [optional] 'pre' => '{e_PLUGIN}myplug/images/',
     * 				'thumb' => 1 (true) or number width in pixels, 'thumb_urlraw' => 1|0 if true, it's a 'raw' url (no sc path constants),
     * 				'thumb_aw' => if 'thumb' is 1|true, this is used for Adaptive thumb width
     * 		   -> write: (array) [optional] 'label' => '', [optional] '__options' => array(...) see e_form::imagepicker() for allowed options
     *
     * - icon  -> read: [optional] 'class' => 'S16', [optional] 'pre' => '{e_PLUGIN}myplug/images/'
     * 		   -> write: (array) [optional] 'label' => '', [optional] 'ajax' => true/false , [optional] '__options' => array(...) see e_form::iconpicker() for allowed options
     *
     * - datestamp  -> read: [optional] 'mask' => 'long'|'short'|strftime() string, default is 'short'
     * 		   		-> write: (array) [optional] 'label' => '', [optional] 'ajax' => true/false , [optional] '__options' => array(...) see e_form::iconpicker() for allowed options
     *
     * - url	-> read: [optional] 'pre' => '{ePLUGIN}myplug/'|'http://somedomain.com/', 'truncate' => 50 default - no truncate, NOTE:
     * 			-> write:
     *
     * - method -> read: optional, passed to given method (the field name)
     * 			-> write: optional, passed to given method (the field name)
     *
     * - hidden -> read: 'show' => 1|0 - show hidden value, 'empty' => 'something' - what to be shown if value is empty (only id 'show' is 1)
     * 			-> write: same as readParms
     *
     * - upload -> read: n/a
     * 			-> write: Under construction
     *
     * Special attribute types:
     * - method (string) field name should be method from the current e_admin_form_ui class (or its extension).
     * 		Example call: field_name($value, $render_action, $parms) where $value is current value,
     * 		$render_action is on of the following: read|write|batch|filter, parms are currently used paramateres ( value of read/writeParms attribute).
     * 		Return type expected (by render action):
     * 			- read: list table - formatted value only
     * 			- write: edit table - form element (control)
     * 			- batch: either array('title1' => 'value1', 'title2' => 'value2', ..) or array('singleOption' => '<option value="somethig">Title</option>') or rendered option group (string '<optgroup><option>...</option></optgroup>'
     * 			- filter: same as batch
     * @var array
     */
    protected $fields = array(
        'checkboxes' => array(
            'title' => '',
            'type' => null,
            'data' => null,
            'width' => '5%',
            'thclass' => 'center',
            'forced' => true,
            'class' => 'center',
            'toggle' => 'e-multiselect'),
        'mib_locations_id' => array(
            'title' => LAN_ID,
            'type' => 'number',
            'data' => 'int',
            'width' => '5%',
            'thclass' => '',
            'class' => 'center',
            'forced' => true,
            'primary' => true,
            'noedit' => true), //Primary ID is not editable
        'mib_location_type_fk' => array(
            'title' => 'Type of location',
            'type' => 'method',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => '',
            'forced' => true,
            'batch' => true,
            'filter' => true),
        'mib_location_name' => array(
            'title' => 'Location Name',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'forced' => true,
            'thclass' => ''),
        'mib_location_address1' => array(
            'title' => 'Address 1',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'mib_location_address2' => array(
            'title' => 'Address 2',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'mib_location_town' => array(
            'title' => 'Town',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'forced' => true,
            'thclass' => ''),
        'mib_location_county' => array(
            'title' => 'County',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'mib_location_postcode' => array(
            'title' => 'Postcode',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'mib_location_phone' => array(
            'title' => 'Phone',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'forced' => true,
            'thclass' => ''),
        'mib_location_contact1' => array(
            'title' => 'Contact',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'mib_location_contact2' => array(
            'title' => 'Contact',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),


        'mib_location_willing' => array(
            'title' => 'Willing',
            'type' => 'checkbox',
            'data' => 'int',
            'width' => 'auto',
            'thclass' => ''),
        'mib_location_comments' => array(
            'title' => 'Comments',
            'type' => 'textarea',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),

        'options' => array(
            'title' => LAN_OPTIONS,
            'type' => null,
            'data' => null,
            'width' => '10%',
            'thclass' => 'center last',
            'class' => 'center last',
            'forced' => true));

    //required - default column user prefs
    protected $fieldpref = array(
        'checkboxes',
        'mib_id',
        'mib_type',
        'mib_url',
        'mib_compatibility',
        'options');

    // FORMAT field_name=>type - optional if fields 'data' attribute is set or if custom model is set in init()
    /*protected $dataFields = array();*/

    // optional, could be also set directly from $fields array with attributes 'validate' => true|'rule_name', 'rule' => 'condition_name', 'error' => 'Validation Error message'
    /*protected  $validationRules = array(
    * 'mib_url' => array('required', '', 'blank URL', 'Help text', 'not valid error message')
    * );*/

    // optional, if $pluginName == 'core', core prefs will be used, else e107::getPluginConfig($pluginName);
    protected $prefs = array(
        'perpage' => array(
            'title' => 'Per Page',
            'type' => 'number',
            'data' => 'int',
            'validate' => false),
        'viewClass' => array(
            'title' => 'View Class',
            'type' => 'userclass',
            'data' => 'integer'),
        );
} // TODO: format

class plugin_mib_admin_form_ui extends e_admin_form_ui
{

    function mib_location_type_fk($curVal, $mode) // not really necessary since we can use 'dropdown' - but just an example of a custom function.
    {
        $frm = e107::getForm();

        $sql = e107::getDb();
        $sql->select('mib_type', 'mib_type_id,mib_type_name', '', 'nowhere');
        while ($row = $sql->fetch('assoc'))
        {

            $this->titles[$row['mib_type_id']] = $row['mib_type_name'];
        }

        $types = $this->titles;
        if ($mode == 'read')
        {
            return vartrue($types[$curVal]);
        }

        if ($mode == 'batch') // Custom Batch List for mib_location_type_fk
        {
            return $types;
        }

        if ($mode == 'filter') // Custom Filter List for mib_location_type_fk
        {
            return $types;
        }

        return $frm->select('mib_location_type_fk', $types, $curVal);
    }

}

/**
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * */
class plugin_mib_history_ui extends e_admin_ui
{
    // required
    protected $pluginTitle = "MiB";

    /**
     * plugin name or 'core'
     * IMPORTANT: should be 'core' for non-plugin areas because this
     * value defines what CONFIG will be used. However, I think this should be changed
     * very soon (awaiting discussion with Cam)
     * Maybe we need something like $prefs['core'], $prefs['blank'] ... multiple getConfig support?
     *
     * @var string
     */
    protected $pluginName = 'mib';

    /**
     * DB Table, table alias is supported
     * Example: 'r.blank'
     * @var string
     */
    protected $table = "mib_bottles";

    /**
     * This is only needed if you need to JOIN tables AND don't wanna use $tableJoin
     * Write your list query without any Order or Limit.
     *
     * @var string [optional]
     */
    protected $listQry = "";
    //

    // optional - required only in case of e.g. tables JOIN. This also could be done with custom model (set it in init())
    //protected $editQry = "SELECT * FROM #blank WHERE mib_id = {ID}";

    // required - if no custom model is set in init() (primary id)
    protected $pid = "mib_bottles_id";

    // optional
    protected $perPage = 20;

    protected $batchDelete = true;

    protected $fields = array(
        'checkboxes' => array(
            'title' => '',
            'type' => null,
            'data' => null,
            'width' => '5%',
            'thclass' => 'center',
            'forced' => true,
            'class' => 'center',
            'toggle' => 'e-multiselect'),
        'mib_bottles_id' => array(
            'title' => LAN_ID,
            'type' => 'number',
            'data' => 'int',
            'width' => '5%',
            'thclass' => '',
            'class' => 'center',
            'forced' => false,
            'primary' => true,
            'noedit' => true), //Primary ID is not editable
        'mib_bottles_location_fk' => array(
            'title' => 'Location',
            'type' => 'method',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => '',
            'batch' => true,
            'filter' => true),
        'mib_bottles_action_fk' => array(
            'title' => 'Action',
            'type' => 'method',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => '',
            'batch' => true,
            'filter' => true),
        'mib_bottles_date' => array(
            'title' => LAN_DATE,
            'type' => 'datestamp',
            'data' => 'int',
            'width' => 'auto',
            'thclass' => '',
            'readParms' => 'long',
            'writeParms' => 'type=datetime'),
        'mib_bottles_quantity' => array(
            'title' => 'Quantity of bottles',
            'type' => 'number',
            'data' => 'int',
            'width' => 'auto',
            'thclass' => ''),
        'mib_bottles_comments' => array(
            'title' => 'Notes',
            'type' => 'textarea',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'mib_bottles_user' => array(
            'title' => 'Member',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'options' => array(
            'title' => LAN_OPTIONS,
            'type' => null,
            'data' => null,
            'width' => '10%',
            'thclass' => 'center last',
            'class' => 'center last',
            'forced' => true));
}

class plugin_mib_history_form_ui extends e_admin_form_ui
{

    function mib_bottles_location_fk($curVal, $mode) // not really necessary since we can use 'dropdown' - but just an example of a custom function.
    {
        $frm = e107::getForm();

        $sql = e107::getDb();
        $sql->select('mib_locations', 'mib_locations_id,mib_location_name', '', 'nowhere');
        while ($row = $sql->fetch('assoc'))
        {

            $this->titles[$row['mib_locations_id']] = $row['mib_location_name'];
        }

        $types = $this->titles;
        if ($mode == 'read')
        {
            return vartrue($types[$curVal]);
        }

        if ($mode == 'batch') // Custom Batch List for mib_bottles_location_fk
        {
            return $types;
        }

        if ($mode == 'filter') // Custom Filter List for mib_bottles_location_fk
        {
            return $types;
        }

        return $frm->select('mib_bottles_location_fk', $types, $curVal);
    }
    function mib_bottles_action_fk($curVal, $mode) // not really necessary since we can use 'dropdown' - but just an example of a custom function.
    {
        $frm = e107::getForm();

        $sql = e107::getDb();
        $sql->select('mib_action', 'mib_action_id,mib_action_action', '', 'nowhere');
        while ($row = $sql->fetch('assoc'))
        {

            $this->titles[$row['mib_action_id']] = $row['mib_action_action'];
        }

        $types = $this->titles;
        if ($mode == 'read')
        {
            return vartrue($types[$curVal]);
        }

        if ($mode == 'batch') // Custom Batch List for mib_bottles_action_fk
        {
            return $types;
        }

        if ($mode == 'filter') // Custom Filter List for mib_bottles_action_fk
        {
            return $types;
        }

        return $frm->select('mib_bottles_action_fk', $types, $curVal);
    }

}
/**
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * */
class plugin_mib_action_ui extends e_admin_ui
{
    // required
    protected $pluginTitle = "MiB";

    /**
     * plugin name or 'core'
     * IMPORTANT: should be 'core' for non-plugin areas because this
     * value defines what CONFIG will be used. However, I think this should be changed
     * very soon (awaiting discussion with Cam)
     * Maybe we need something like $prefs['core'], $prefs['blank'] ... multiple getConfig support?
     *
     * @var string
     */
    protected $pluginName = 'mib';

    /**
     * DB Table, table alias is supported
     * Example: 'r.blank'
     * @var string
     */
    protected $table = "mib_action";

    /**
     * This is only needed if you need to JOIN tables AND don't wanna use $tableJoin
     * Write your list query without any Order or Limit.
     *
     * @var string [optional]
     */
    protected $listQry = "";
    //

    // optional - required only in case of e.g. tables JOIN. This also could be done with custom model (set it in init())
    //protected $editQry = "SELECT * FROM #blank WHERE mib_id = {ID}";

    // required - if no custom model is set in init() (primary id)
    protected $pid = "mib_action_id";

    // optional
    protected $perPage = 20;

    protected $batchDelete = true;

    //	protected \$sortField		= 'somefield_order';


    //	protected \$sortParent      = 'somefield_parent';


    //	protected \$treePrefix      = 'somefield_title';


    //TODO change the mib_url type back to URL before blank.
    // required
    /**
     * (use this as starting point for wiki documentation)
     * $fields format  (string) $field_name => (array) $attributes
     *
     * $field_name format:
     * 	'table_alias_or_name.field_name.field_alias' (if JOIN support is needed) OR just 'field_name'
     * NOTE: Keep in mind the count of exploded data can be 1 or 3!!! This means if you wanna give alias
     * on main table field you can't omit the table (first key), alternative is just '.' e.g. '.field_name.field_alias'
     *
     * $attributes format:
     * 	- title (string) Human readable field title, constant name will be accpeted as well (multi-language support
     *
     *  - type (string) null (means system), number, text, dropdown, url, image, icon, datestamp, userclass, userclasses, user[_name|_loginname|_login|_customtitle|_email],
     *    boolean, method, ip
     *  	full/most recent reference list - e_form::renderTableRow(), e_form::renderElement(), e_admin_form_ui::renderBatchFilter()
     *  	for list of possible read/writeParms per type see below
     *
     *  - data (string) Data type, one of the following: int, integer, string, str, float, bool, boolean, model, null
     *    Default is 'str'
     *    Used only if $dataFields is not set
     *  	full/most recent reference list - e_admin_model::sanitize(), db::_getFieldValue()
     *  - dataPath (string) - xpath like path to the model/posted value. Example: 'dataPath' => 'prefix/mykey' will result in $_POST['prefix']['mykey']
     *  - primary (boolean) primary field (obsolete, $pid is now used)
     *
     *  - help (string) edit/create table - inline help, constant name will be accpeted as well, optional
     *  - note (string) edit/create table - text shown below the field title (left column), constant name will be accpeted as well, optional
     *
     *  - validate (boolean|string) any of accepted validation types (see e_validator::$_required_rules), true == 'required'
     *  - rule (string) condition for chosen above validation type (see e_validator::$_required_rules), not required for all types
     *  - error (string) Human readable error message (validation failure), constant name will be accepted as well, optional
     *
     *  - batch (boolean) list table - add current field to batch actions, in use only for boolean, dropdown, datestamp, userclass, method field types
     *    NOTE: batch may accept string values in the future...
     *  	full/most recent reference type list - e_admin_form_ui::renderBatchFilter()
     *
     *  - filter (boolean) list table - add current field to filter actions, rest is same as batch
     *
     *  - forced (boolean) list table - forced fields are always shown in list table
     *  - nolist (boolean) list table - don't show in column choice list
     *  - noedit (boolean) edit table - don't show in edit mode
     *
     *  - width (string) list table - width e.g '10%', 'auto'
     *  - thclass (string) list table header - th element class
     *  - class (string) list table body - td element additional class
     *
     *  - readParms (mixed) parameters used by core routine for showing values of current field. Structure on this attribute
     *    depends on the current field type (see below). readParams are used mainly by list page
     *
     *  - writeParms (mixed) parameters used by core routine for showing control element(s) of current field.
     *    Structure on this attribute depends on the current field type (see below).
     *    writeParams are used mainly by edit page, filter (list page), batch (list page)
     *
     * $attributes['type']->$attributes['read/writeParams'] pairs:
     *
     * - null -> read: n/a
     * 		  -> write: n/a
     *
     * - dropdown -> read: 'pre', 'post', array in format posted_html_name => value
     * 			  -> write: 'pre', 'post', array in format as required by e_form::selectbox()
     *
     * - user -> read: [optional] 'link' => true - create link to user profile, 'idField' => 'author_id' - tells to renderValue() where to search for user id (used when 'link' is true and current field is NOT ID field)
     * 				   'nameField' => 'comment_author_name' - tells to renderValue() where to search for user name (used when 'link' is true and current field is ID field)
     * 		  -> write: [optional] 'nameField' => 'comment_author_name' the name of a 'user_name' field; 'currentInit' - use currrent user if no data provided; 'current' - use always current user(editor); '__options' e_form::userpickup() options
     *
     * - number -> read: (array) [optional] 'point' => '.', [optional] 'sep' => ' ', [optional] 'decimals' => 2, [optional] 'pre' => '&euro; ', [optional] 'post' => 'LAN_CURRENCY'
     * 			-> write: (array) [optional] 'pre' => '&euro; ', [optional] 'post' => 'LAN_CURRENCY', [optional] 'maxlength' => 50, [optional] '__options' => array(...) see e_form class description for __options format
     *
     * - ip		-> read: n/a
     * 			-> write: [optional] element options array (see e_form class description for __options format)
     *
     * - text -> read: (array) [optional] 'htmltruncate' => 100, [optional] 'truncate' => 100, [optional] 'pre' => '', [optional] 'post' => ' px'
     * 		  -> write: (array) [optional] 'pre' => '', [optional] 'post' => ' px', [optional] 'maxlength' => 50 (default - 255), [optional] '__options' => array(...) see e_form class description for __options format
     *
     * - textarea 	-> read: (array) 'noparse' => '1' default 0 (disable toHTML text parsing), [optional] 'bb' => '1' (parse bbcode) default 0,
     * 								[optional] 'parse' => '' modifiers passed to e_parse::toHTML() e.g. 'BODY', [optional] 'htmltruncate' => 100,
     * 								[optional] 'truncate' => 100, [optional] 'expand' => '[more]' title for expand link, empty - no expand
     * 		  		-> write: (array) [optional] 'rows' => '' default 15, [optional] 'cols' => '' default 40, [optional] '__options' => array(...) see e_form class description for __options format
     * 								[optional] 'counter' => 0 number of max characters - has only visual effect, doesn't truncate the value (default - false)
     *
     * - bbarea -> read: same as textarea type
     * 		  	-> write: (array) [optional] 'pre' => '', [optional] 'post' => ' px', [optional] 'maxlength' => 50 (default - 0),
     * 				[optional] 'size' => [optional] - medium, small, large - default is medium,
     * 				[optional] 'counter' => 0 number of max characters - has only visual effect, doesn't truncate the value (default - false)
     *
     * - image -> read: [optional] 'title' => 'SOME_LAN' (default - LAN_PREVIEW), [optional] 'pre' => '{e_PLUGIN}myplug/images/',
     * 				'thumb' => 1 (true) or number width in pixels, 'thumb_urlraw' => 1|0 if true, it's a 'raw' url (no sc path constants),
     * 				'thumb_aw' => if 'thumb' is 1|true, this is used for Adaptive thumb width
     * 		   -> write: (array) [optional] 'label' => '', [optional] '__options' => array(...) see e_form::imagepicker() for allowed options
     *
     * - icon  -> read: [optional] 'class' => 'S16', [optional] 'pre' => '{e_PLUGIN}myplug/images/'
     * 		   -> write: (array) [optional] 'label' => '', [optional] 'ajax' => true/false , [optional] '__options' => array(...) see e_form::iconpicker() for allowed options
     *
     * - datestamp  -> read: [optional] 'mask' => 'long'|'short'|strftime() string, default is 'short'
     * 		   		-> write: (array) [optional] 'label' => '', [optional] 'ajax' => true/false , [optional] '__options' => array(...) see e_form::iconpicker() for allowed options
     *
     * - url	-> read: [optional] 'pre' => '{ePLUGIN}myplug/'|'http://somedomain.com/', 'truncate' => 50 default - no truncate, NOTE:
     * 			-> write:
     *
     * - method -> read: optional, passed to given method (the field name)
     * 			-> write: optional, passed to given method (the field name)
     *
     * - hidden -> read: 'show' => 1|0 - show hidden value, 'empty' => 'something' - what to be shown if value is empty (only id 'show' is 1)
     * 			-> write: same as readParms
     *
     * - upload -> read: n/a
     * 			-> write: Under construction
     *
     * Special attribute types:
     * - method (string) field name should be method from the current e_admin_form_ui class (or its extension).
     * 		Example call: field_name($value, $render_action, $parms) where $value is current value,
     * 		$render_action is on of the following: read|write|batch|filter, parms are currently used paramateres ( value of read/writeParms attribute).
     * 		Return type expected (by render action):
     * 			- read: list table - formatted value only
     * 			- write: edit table - form element (control)
     * 			- batch: either array('title1' => 'value1', 'title2' => 'value2', ..) or array('singleOption' => '<option value="somethig">Title</option>') or rendered option group (string '<optgroup><option>...</option></optgroup>'
     * 			- filter: same as batch
     * @var array
     */
    protected $fields = array(
        'checkboxes' => array(
            'title' => '',
            'type' => null,
            'data' => null,
            'width' => '5%',
            'thclass' => 'center',
            'forced' => true,
            'class' => 'center',
            'toggle' => 'e-multiselect'),
        'mib_action_id' => array(
            'title' => LAN_ID,
            'type' => 'number',
            'data' => 'int',
            'width' => '5%',
            'thclass' => '',
            'class' => 'center',
            'forced' => false,
            'primary' => true,
            'noedit' => true), //Primary ID is not editable
        'mib_action_action' => array(
            'title' => 'Action',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'options' => array(
            'title' => LAN_OPTIONS,
            'type' => null,
            'data' => null,
            'width' => '10%',
            'thclass' => 'center last',
            'class' => 'center last',
            'forced' => true));
}

class plugin_mib_action_form_ui extends e_admin_form_ui
{
}
/**
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * */
class plugin_mib_type_ui extends e_admin_ui
{
    // required
    protected $pluginTitle = "MiB";

    /**
     * plugin name or 'core'
     * IMPORTANT: should be 'core' for non-plugin areas because this
     * value defines what CONFIG will be used. However, I think this should be changed
     * very soon (awaiting discussion with Cam)
     * Maybe we need something like $prefs['core'], $prefs['blank'] ... multiple getConfig support?
     *
     * @var string
     */
    protected $pluginName = 'mib';

    /**
     * DB Table, table alias is supported
     * Example: 'r.blank'
     * @var string
     */
    protected $table = "mib_type";

    /**
     * This is only needed if you need to JOIN tables AND don't wanna use $tableJoin
     * Write your list query without any Order or Limit.
     *
     * @var string [optional]
     */
    protected $listQry = "";
    //

    // optional - required only in case of e.g. tables JOIN. This also could be done with custom model (set it in init())
    //protected $editQry = "SELECT * FROM #blank WHERE mib_id = {ID}";

    // required - if no custom model is set in init() (primary id)
    protected $pid = "mib_type_id";

    // optional
    protected $perPage = 20;

    protected $batchDelete = true;

    //	protected \$sortField		= 'somefield_order';


    //	protected \$sortParent      = 'somefield_parent';


    //	protected \$treePrefix      = 'somefield_title';


    //TODO change the mib_url type back to URL before blank.
    // required
    /**
     * (use this as starting point for wiki documentation)
     * $fields format  (string) $field_name => (array) $attributes
     *
     * $field_name format:
     * 	'table_alias_or_name.field_name.field_alias' (if JOIN support is needed) OR just 'field_name'
     * NOTE: Keep in mind the count of exploded data can be 1 or 3!!! This means if you wanna give alias
     * on main table field you can't omit the table (first key), alternative is just '.' e.g. '.field_name.field_alias'
     *
     * $attributes format:
     * 	- title (string) Human readable field title, constant name will be accpeted as well (multi-language support
     *
     *  - type (string) null (means system), number, text, dropdown, url, image, icon, datestamp, userclass, userclasses, user[_name|_loginname|_login|_customtitle|_email],
     *    boolean, method, ip
     *  	full/most recent reference list - e_form::renderTableRow(), e_form::renderElement(), e_admin_form_ui::renderBatchFilter()
     *  	for list of possible read/writeParms per type see below
     *
     *  - data (string) Data type, one of the following: int, integer, string, str, float, bool, boolean, model, null
     *    Default is 'str'
     *    Used only if $dataFields is not set
     *  	full/most recent reference list - e_admin_model::sanitize(), db::_getFieldValue()
     *  - dataPath (string) - xpath like path to the model/posted value. Example: 'dataPath' => 'prefix/mykey' will result in $_POST['prefix']['mykey']
     *  - primary (boolean) primary field (obsolete, $pid is now used)
     *
     *  - help (string) edit/create table - inline help, constant name will be accpeted as well, optional
     *  - note (string) edit/create table - text shown below the field title (left column), constant name will be accpeted as well, optional
     *
     *  - validate (boolean|string) any of accepted validation types (see e_validator::$_required_rules), true == 'required'
     *  - rule (string) condition for chosen above validation type (see e_validator::$_required_rules), not required for all types
     *  - error (string) Human readable error message (validation failure), constant name will be accepted as well, optional
     *
     *  - batch (boolean) list table - add current field to batch actions, in use only for boolean, dropdown, datestamp, userclass, method field types
     *    NOTE: batch may accept string values in the future...
     *  	full/most recent reference type list - e_admin_form_ui::renderBatchFilter()
     *
     *  - filter (boolean) list table - add current field to filter actions, rest is same as batch
     *
     *  - forced (boolean) list table - forced fields are always shown in list table
     *  - nolist (boolean) list table - don't show in column choice list
     *  - noedit (boolean) edit table - don't show in edit mode
     *
     *  - width (string) list table - width e.g '10%', 'auto'
     *  - thclass (string) list table header - th element class
     *  - class (string) list table body - td element additional class
     *
     *  - readParms (mixed) parameters used by core routine for showing values of current field. Structure on this attribute
     *    depends on the current field type (see below). readParams are used mainly by list page
     *
     *  - writeParms (mixed) parameters used by core routine for showing control element(s) of current field.
     *    Structure on this attribute depends on the current field type (see below).
     *    writeParams are used mainly by edit page, filter (list page), batch (list page)
     *
     * $attributes['type']->$attributes['read/writeParams'] pairs:
     *
     * - null -> read: n/a
     * 		  -> write: n/a
     *
     * - dropdown -> read: 'pre', 'post', array in format posted_html_name => value
     * 			  -> write: 'pre', 'post', array in format as required by e_form::selectbox()
     *
     * - user -> read: [optional] 'link' => true - create link to user profile, 'idField' => 'author_id' - tells to renderValue() where to search for user id (used when 'link' is true and current field is NOT ID field)
     * 				   'nameField' => 'comment_author_name' - tells to renderValue() where to search for user name (used when 'link' is true and current field is ID field)
     * 		  -> write: [optional] 'nameField' => 'comment_author_name' the name of a 'user_name' field; 'currentInit' - use currrent user if no data provided; 'current' - use always current user(editor); '__options' e_form::userpickup() options
     *
     * - number -> read: (array) [optional] 'point' => '.', [optional] 'sep' => ' ', [optional] 'decimals' => 2, [optional] 'pre' => '&euro; ', [optional] 'post' => 'LAN_CURRENCY'
     * 			-> write: (array) [optional] 'pre' => '&euro; ', [optional] 'post' => 'LAN_CURRENCY', [optional] 'maxlength' => 50, [optional] '__options' => array(...) see e_form class description for __options format
     *
     * - ip		-> read: n/a
     * 			-> write: [optional] element options array (see e_form class description for __options format)
     *
     * - text -> read: (array) [optional] 'htmltruncate' => 100, [optional] 'truncate' => 100, [optional] 'pre' => '', [optional] 'post' => ' px'
     * 		  -> write: (array) [optional] 'pre' => '', [optional] 'post' => ' px', [optional] 'maxlength' => 50 (default - 255), [optional] '__options' => array(...) see e_form class description for __options format
     *
     * - textarea 	-> read: (array) 'noparse' => '1' default 0 (disable toHTML text parsing), [optional] 'bb' => '1' (parse bbcode) default 0,
     * 								[optional] 'parse' => '' modifiers passed to e_parse::toHTML() e.g. 'BODY', [optional] 'htmltruncate' => 100,
     * 								[optional] 'truncate' => 100, [optional] 'expand' => '[more]' title for expand link, empty - no expand
     * 		  		-> write: (array) [optional] 'rows' => '' default 15, [optional] 'cols' => '' default 40, [optional] '__options' => array(...) see e_form class description for __options format
     * 								[optional] 'counter' => 0 number of max characters - has only visual effect, doesn't truncate the value (default - false)
     *
     * - bbarea -> read: same as textarea type
     * 		  	-> write: (array) [optional] 'pre' => '', [optional] 'post' => ' px', [optional] 'maxlength' => 50 (default - 0),
     * 				[optional] 'size' => [optional] - medium, small, large - default is medium,
     * 				[optional] 'counter' => 0 number of max characters - has only visual effect, doesn't truncate the value (default - false)
     *
     * - image -> read: [optional] 'title' => 'SOME_LAN' (default - LAN_PREVIEW), [optional] 'pre' => '{e_PLUGIN}myplug/images/',
     * 				'thumb' => 1 (true) or number width in pixels, 'thumb_urlraw' => 1|0 if true, it's a 'raw' url (no sc path constants),
     * 				'thumb_aw' => if 'thumb' is 1|true, this is used for Adaptive thumb width
     * 		   -> write: (array) [optional] 'label' => '', [optional] '__options' => array(...) see e_form::imagepicker() for allowed options
     *
     * - icon  -> read: [optional] 'class' => 'S16', [optional] 'pre' => '{e_PLUGIN}myplug/images/'
     * 		   -> write: (array) [optional] 'label' => '', [optional] 'ajax' => true/false , [optional] '__options' => array(...) see e_form::iconpicker() for allowed options
     *
     * - datestamp  -> read: [optional] 'mask' => 'long'|'short'|strftime() string, default is 'short'
     * 		   		-> write: (array) [optional] 'label' => '', [optional] 'ajax' => true/false , [optional] '__options' => array(...) see e_form::iconpicker() for allowed options
     *
     * - url	-> read: [optional] 'pre' => '{ePLUGIN}myplug/'|'http://somedomain.com/', 'truncate' => 50 default - no truncate, NOTE:
     * 			-> write:
     *
     * - method -> read: optional, passed to given method (the field name)
     * 			-> write: optional, passed to given method (the field name)
     *
     * - hidden -> read: 'show' => 1|0 - show hidden value, 'empty' => 'something' - what to be shown if value is empty (only id 'show' is 1)
     * 			-> write: same as readParms
     *
     * - upload -> read: n/a
     * 			-> write: Under construction
     *
     * Special attribute types:
     * - method (string) field name should be method from the current e_admin_form_ui class (or its extension).
     * 		Example call: field_name($value, $render_action, $parms) where $value is current value,
     * 		$render_action is on of the following: read|write|batch|filter, parms are currently used paramateres ( value of read/writeParms attribute).
     * 		Return type expected (by render action):
     * 			- read: list table - formatted value only
     * 			- write: edit table - form element (control)
     * 			- batch: either array('title1' => 'value1', 'title2' => 'value2', ..) or array('singleOption' => '<option value="somethig">Title</option>') or rendered option group (string '<optgroup><option>...</option></optgroup>'
     * 			- filter: same as batch
     * @var array
     */
    protected $fields = array(
        'checkboxes' => array(
            'title' => '',
            'type' => null,
            'data' => null,
            'width' => '5%',
            'thclass' => 'center',
            'forced' => true,
            'class' => 'center',
            'toggle' => 'e-multiselect'),
        'mib_type_id' => array(
            'title' => LAN_ID,
            'type' => 'number',
            'data' => 'int',
            'width' => '5%',
            'thclass' => '',
            'class' => 'center',
            'forced' => false,
            'primary' => true,
            'noedit' => true), //Primary ID is not editable
        'mib_type_name' => array(
            'title' => 'Type',
            'type' => 'text',
            'data' => 'str',
            'width' => 'auto',
            'thclass' => ''),
        'options' => array(
            'title' => LAN_OPTIONS,
            'type' => null,
            'data' => null,
            'width' => '10%',
            'thclass' => 'center last',
            'class' => 'center last',
            'forced' => true));


}

class plugin_mib_type_form_ui extends e_admin_form_ui
{
}
/**
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * */
/*
* After initialization we'll be able to call dispatcher via e107::getAdminUI()
* so this is the first we should do on admin page.
* Global instance variable is not needed.
* NOTE: class is auto-loaded - see class2.php __autoload()
*/
/* $dispatcher = */

new plugin_mib_admin();

/*
* Uncomment the below only if you disable the auto observing above
* Example: $dispatcher = new plugin_mib_admin(null, null, false);
*/
//$dispatcher->runObservers(true);

require_once (e_ADMIN . "auth.php");

/*
* Send page content
*/
e107::getAdminUI()->runPage();

require_once (e_ADMIN . "footer.php");

/* OBSOLETE - see admin_shortcodes::sc_admin_menu()
* function admin_config_adminmenu() 
* {
* //global $rp;
* //$rp->show_options();
* e107::getRegistry('admin/mib_dispatcher')->renderMenu();
* }
*/

/* OBSOLETE - done within header.php
* function headerjs() // needed for the checkboxes - how can we remove the need to duplicate this code?
* {
* return e107::getAdminUI()->getHeader();
* }
*/
