<?php

require_once 'importcontactsactivity.civix.php';
const IMPORTCONTACTSACTIVITY_ACTIVITY_TYPE_LABEL = 'Created by Mass Upload';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function importcontactsactivity_civicrm_config(&$config) {
  _importcontactsactivity_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function importcontactsactivity_civicrm_xmlMenu(&$files) {
  _importcontactsactivity_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function importcontactsactivity_civicrm_install() {
  // Does the activity type already exist?
  $acTypeResult = civicrm_api('ActivityType', 'get', array('version' => 3));
  if (civicrm_error($acTypeResult)) {
    throw new CRM_Extension_Exception("Failed to check existing activity types while installing: {$acTypeResult['error_message']}");
  }

  // If not, create it
  if (!in_array(IMPORTCONTACTSACTIVITY_ACTIVITY_TYPE_LABEL, $acTypeResult['values'])) {
    $acTypeCreate = civicrm_api('ActivityType', 'create', array(
      'version'     => 3,
      'label'       => IMPORTCONTACTSACTIVITY_ACTIVITY_TYPE_LABEL,
      'weight'      => 1,
      'is_active'   => 1,
      'description' => "To signify that this contact was created using the 'Import Contacts' function, eg. from a CSV file or SQL query.",
    ));
    if (civicrm_error($acTypeCreate)) {
      throw new CRM_Extension_Exception("Failed to create '" . IMPORTCONTACTSACTIVITY_ACTIVITY_TYPE_LABEL . "' activity type while installing: {$acTypeCreate['error_message']}");
    }
  }

  return _importcontactsactivity_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function importcontactsactivity_civicrm_uninstall() {
  return _importcontactsactivity_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function importcontactsactivity_civicrm_enable() {
  return _importcontactsactivity_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function importcontactsactivity_civicrm_disable() {
  return _importcontactsactivity_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function importcontactsactivity_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _importcontactsactivity_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function importcontactsactivity_civicrm_managed(&$entities) {
  return _importcontactsactivity_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function importcontactsactivity_civicrm_caseTypes(&$caseTypes) {
  _importcontactsactivity_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function importcontactsactivity_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _importcontactsactivity_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_import
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_import
 */
function importcontactsactivity_civicrm_import($object, $usage, &$objectRef, &$params) {
  if ($object != 'Contact' || $usage != 'process') {
    return;
  }

  // Create an activity on each imported contact.
  $actResult = civicrm_api('Activity', 'create', array(
    'version'           => 3,
    // Can be given instead of activity_type_id
    'activity_label'    => IMPORTCONTACTSACTIVITY_ACTIVITY_TYPE_LABEL,
    // source_contact_id is filled in automatically, as the user running the import
    'target_contact_id' => $params['contactID'],
  ));
  if (civicrm_error($actResult)) {
    throw new CRM_Extension_Exception("Failed to create activity on import: {$actResult['error_message']}");
  }
}
