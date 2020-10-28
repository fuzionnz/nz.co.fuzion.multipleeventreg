<?php

require_once 'multipleeventreg.civix.php';
use CRM_Multipleeventreg_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function multipleeventreg_civicrm_config(&$config) {
  _multipleeventreg_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function multipleeventreg_civicrm_xmlMenu(&$files) {
  _multipleeventreg_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function multipleeventreg_civicrm_install() {
  $eventCG = civicrm_api3('CustomGroup', 'create', [
    'title' => "Multiple Event Registration",
    'extends' => "Event",
    'is_public' => 0,
    'name' => "child_event_ids_cg",
  ]);
  civicrm_api3('CustomField', 'create', [
    'custom_group_id' => $eventCG['id'],
    'label' => "Child Event IDs",
    'name' => "child_event_ids_cf",
    'data_type' => "String",
    'html_type' => "Text",
    'is_searchable' => 1,
    'help_post' => "Enter comma-separated event ids. New participants will also be registered to these events.",
  ]);
  _multipleeventreg_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function multipleeventreg_civicrm_postInstall() {
  _multipleeventreg_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function multipleeventreg_civicrm_uninstall() {
    //delete custom field.
  $fieldID = CRM_Multipleeventreg_Registration::eventCustomField();
  civicrm_api3('CustomField', 'delete', [
    'id' => $fieldID,
  ]);

  //delete custom group.
  $groupID = civicrm_api3('CustomGroup', 'get', [
    'sequential' => 1,
    'name' => "child_event_ids_cg",
  ])['id'] ?? NULL;
  if ($groupID) {
    civicrm_api3('CustomGroup', 'delete', [
      'id' => $groupID,
    ]);
  }
  _multipleeventreg_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function multipleeventreg_civicrm_enable() {
  _multipleeventreg_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function multipleeventreg_civicrm_disable() {
  _multipleeventreg_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function multipleeventreg_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _multipleeventreg_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function multipleeventreg_civicrm_managed(&$entities) {
  _multipleeventreg_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function multipleeventreg_civicrm_caseTypes(&$caseTypes) {
  _multipleeventreg_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function multipleeventreg_civicrm_angularModules(&$angularModules) {
  _multipleeventreg_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function multipleeventreg_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _multipleeventreg_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function multipleeventreg_civicrm_entityTypes(&$entityTypes) {
  _multipleeventreg_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function multipleeventreg_civicrm_themes(&$themes) {
  _multipleeventreg_civix_civicrm_themes($themes);
}

function multipleeventreg_civicrm_pre($op, $objectName, $objectId, &$params) {
  if ($objectName == 'Participant' && !empty($objectId) && $op == 'delete') {
    CRM_Multipleeventreg_Registration::deleteRelatedEventRegistration($objectId);
  }
}

function multipleeventreg_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Participant' && !empty($objectId) && ($op == 'create' || $op == 'edit')) {
    CRM_Multipleeventreg_Registration::createRelatedEventRegistration($objectId, $op);
  }
}


// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function multipleeventreg_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function multipleeventreg_civicrm_navigationMenu(&$menu) {
  _multipleeventreg_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _multipleeventreg_civix_navigationMenu($menu);
} // */
