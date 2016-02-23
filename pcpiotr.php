<?php

require_once 'pcpiotr.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function pcpiotr_civicrm_config(&$config) {
  _pcpiotr_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function pcpiotr_civicrm_xmlMenu(&$files) {
  _pcpiotr_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function pcpiotr_civicrm_install() {
  _pcpiotr_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function pcpiotr_civicrm_uninstall() {
  _pcpiotr_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function pcpiotr_civicrm_enable() {
  _pcpiotr_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function pcpiotr_civicrm_disable() {
  _pcpiotr_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function pcpiotr_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _pcpiotr_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function pcpiotr_civicrm_managed(&$entities) {
  _pcpiotr_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function pcpiotr_civicrm_caseTypes(&$caseTypes) {
  _pcpiotr_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function pcpiotr_civicrm_angularModules(&$angularModules) {
_pcpiotr_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function pcpiotr_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _pcpiotr_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function pcpiotr_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function pcpiotr_civicrm_navigationMenu(&$menu) {
  _pcpiotr_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'pl.nazwa.phpiotr.pcpiotr')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _pcpiotr_civix_navigationMenu($menu);
} // */

function pcpiotr_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ($tabsetName !== 'civicrm/contact/view') {
    return;
  }
  if (empty($context)) {
    return;
  }
  $contactID = (int) $context['contact_id'];
  $url = CRM_Utils_System::url('civicrm/contact/view/personal-campaign-pages', "cid=$contactID&reset=1&force=1");
  $tabs[] = array(
    'id' => 'pcp',
    'title' => ts('Personal Campaign Pages'),
    'url' => $url,
    'valid' => 1,
    'active' => 1,
    'current' => false,
    'class' => 'livePage',
    'count' => CRM_Pcpiotr_Page_PersonalCampaignPages::getPcpsCountForContact($contactID),
  );
}
