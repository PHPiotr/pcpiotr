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

  private function getPcpsForContact($contactId) {
    $status = CRM_PCP_BAO_PCP::buildOptions('status_id', 'create');

    $approvedId = CRM_Core_OptionGroup::getValue('pcp_status', 'Approved', 'name');

    $pages = $this->getPages();

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

    $pcpResult = civicrm_api3('Pcp', 'get', array(
      'sequential' => 1,
      'contact_id' => $contactId,
    ));

    foreach ($pcpResult['values'] as $key => $pcp) {

      $class = '';
      if ($pcp['status_id'] != $approvedId || $pcp['is_active'] != 1) {
        $class = 'disabled';
      }

      $pageType = $pcp['page_type'];
      $pageId = (int) $pcp['page_id'];
      $title = $pages[$pageType][$pageId]['title'];
      if ($title == '' || $title == NULL) {
        $title = '(no title found for ' . $pageType . ' id ' . $pageId . ')';
      }

      if ($pcp['page_type'] == 'contribute') {
        $pageUrl = CRM_Utils_System::url('civicrm/' . $pageType . '/transact', 'reset=1&id=' . $pcp['page_id']);
      }
      else {
        $pageUrl = CRM_Utils_System::url('civicrm/' . $pageType . '/register', 'reset=1&id=' . $pcp['page_id']);
      }

      $contributionData = $this->getContributionDataForPcp($pcp['id']);

      $pcps[$pcp['id']] = $pcp;
      $pcps[$pcp['id']]['page_title'] = $pcp['title'];
      $pcps[$pcp['id']]['page_url'] = $pageUrl;
      $pcps[$pcp['id']]['status'] = $status[$pcp['status_id']];
      $pcps[$pcp['id']]['contribution_page_event'] = $title;
      $pcps[$pcp['id']]['no_of_contributions'] = $contributionData['no_of_contributions'];
      $pcps[$pcp['id']]['amount_raised'] = $contributionData['amount_raised'];
      $pcps[$pcp['id']]['target_amount'] = $pcp['goal_amount'];
      $pcps[$pcp['id']]['action'] = CRM_Core_Action::formLink($edit_link, $action_key, array('id' => $pcp['id']), ts('more'), FALSE, 'contributionpage.pcp.list', 'PCP', $pcp['id']);
      $pcps[$pcp['id']]['class'] = $class;
    }

    return $pcps;
  }

  private function getContributionDataForPcp($pcp_id) {
    $data = array('no_of_contributions' => 0, 'amount_raised' => 0.00);

    $result = civicrm_api3('ContributionSoft', 'get', array(
      'sequential' => 1,
      'pcp_id' => $pcp_id,
    ));

    $data['no_of_contributions'] = $result['count'];

    if (!$result['count']) {
      return $data;
    }

    foreach ($result['values'] as $key => $value) {
      $data['amount_raised'] += (float) $value['amount'];
    }

    $data['amount_raised'] = number_format($data['amount_raised'], 2);

    return $data;
  }

  private function getPages() {
    $pages = array();

    $contributionPageResult = civicrm_api3('ContributionPage', 'get');

    foreach ($contributionPageResult['values'] as $contributionPage) {
      $pages['contribute'][$contributionPage['id']]['title'] = $contributionPage['title'];
    }

    $eventResult = civicrm_api3('Event', 'get');

    foreach ($eventResult['values'] as $event) {
      $pages['event'][$event['id']]['title'] = $event['title'];
    }

    return $pages;
  }

}
