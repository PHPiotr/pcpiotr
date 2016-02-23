<?php

require_once 'CRM/Core/Page.php';

class CRM_Pcpiotr_Page_PersonalCampaignPages extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(ts('Personal Campaign Pages'));
    $contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $displayName = CRM_Contact_BAO_Contact::displayName($contactId);
    $this->assign('displayName', $displayName);
    $this->assign('rows', $this->getPcpsForContact($contactId));
    parent::run();
  }

  public static function getPcpsCountForContact($contactId) {
    $query = "SELECT COUNT(id) pcp_count FROM civicrm_pcp WHERE contact_id = %1";
    $result = CRM_Core_DAO::executeQuery($query, array('1' => array($contactId, 'Integer')));
    $fetchAll = $result->fetchAll();
    $pcpCount = (int) $fetchAll[0]['pcp_count'];

    return $pcpCount;
  }

  private function getPcpsForContact($contactId) {
    $status = CRM_PCP_BAO_PCP::buildOptions('status_id', 'create');

    $approvedId = CRM_Core_OptionGroup::getValue('pcp_status', 'Approved', 'name');

    $query = 'SELECT title FROM civicrm_contribution_page';
    $cpages = CRM_Core_DAO::executeQuery($query);
    while ($cpages->fetch()) {
      $pages['contribute'][$cpages->id]['title'] = $cpages->title;
    }

    $query = 'SELECT title FROM civicrm_event  WHERE is_template IS NULL OR is_template != 1';
    $epages = CRM_Core_DAO::executeQuery($query);
    while ($epages->fetch()) {
      $pages['event'][$epages->id]['title'] = $epages->title;
    }

    $query = "
        SELECT cp.id, cp.contact_id , cp.status_id, cp.title, cp.is_active, cp.page_type, cp.page_id, cp.goal_amount
        FROM civicrm_pcp cp
        WHERE cp.contact_id = %1 ORDER BY cp.status_id";
    $pcp = CRM_Core_DAO::executeQuery($query, array('1' => array($contactId, 'Integer')));

    $action_key = CRM_Core_Action::UPDATE;

    $edit_link = array(
      $action_key => array(
        'name' => ts('Edit'),
        'url' => 'civicrm/pcp/info',
        'qs' => "action=update&reset=1&id=%%id%%&context=contact&cid=$contactId",
        'title' => ts('Edit Personal Campaign Page'),
      ),
    );

    $pcps = array();

    while ($pcp->fetch()) {

      $class = '';
      if ($pcp->status_id != $approvedId || $pcp->is_active != 1) {
        $class = 'disabled';
      }

      $page_type = $pcp->page_type;
      $page_id = (int) $pcp->page_id;
      $title = $pages[$page_type][$page_id]['title'];
      if ($title == '' || $title == NULL) {
        $title = '(no title found for ' . $page_type . ' id ' . $page_id . ')';
      }

      if ($pcp->page_type == 'contribute') {
        $pageUrl = CRM_Utils_System::url('civicrm/' . $page_type . '/transact', 'reset=1&id=' . $pcp->page_id);
      }
      else {
        $pageUrl = CRM_Utils_System::url('civicrm/' . $page_type . '/register', 'reset=1&id=' . $pcp->page_id);
      }

      $contributionData = $this->getContributionDataForPcp($pcp->id);

      $pcps[$pcp->id] = array(
        'id' => $pcp->id,
        'status_id' => $status[$pcp->status_id],
        'target_amount' => $pcp->goal_amount,
        'page_id' => $page_id,
        'page_title' => $title,
        'page_url' => $pageUrl,
        'page_type' => $page_type,
        'action' => CRM_Core_Action::formLink($edit_link, $action_key, array('id' => $pcp->id), ts('more'), FALSE, 'contributionpage.pcp.list', 'PCP', $pcp->id),
        'title' => $pcp->title,
        'class' => $class,
        'amount_raised' => $contributionData[0]['amount_raised'],
        'no_of_contributions' => $contributionData[0]['no_of_contributions']
      );
    }

    return $pcps;
  }

  private function getContributionDataForPcp($pcp_id) {
    $query = "SELECT sum(amount) amount_raised, count(id) no_of_contributions FROM `civicrm_contribution_soft` WHERE pcp_id = %1";
    $result = CRM_Core_DAO::executeQuery($query, array('1' => array($pcp_id, 'Integer')));

    return $result->fetchAll();
  }

}
