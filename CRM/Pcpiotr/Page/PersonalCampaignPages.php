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

    try {
      $result = civicrm_api3('Pcp', 'get', array(
        'contact_id' => $contactId,
        'api.ContributionPage.get' => array('id' => "\$value.page_id", 'return' => "title"),
        'api.Event.get' => array('id' => "\$value.page_id", 'return' => "title"),
        'api.ContributionSoft.get' => array('pcp_id' => "\$value.id", 'return' => "amount"),
      ));
    }
    catch (CiviCRM_API3_Exception $e) {
      return array(
        'error' => $e->getMessage(),
        'error_code' => $e->getErrorCode(),
        'error_data' => $e->getExtraParams(),
      );
    }

    $status = CRM_PCP_BAO_PCP::buildOptions('status_id', 'create');

    $approvedId = CRM_Core_OptionGroup::getValue('pcp_status', 'Approved', 'name');

    $action_key = CRM_Core_Action::UPDATE;

    $edit_link = array(
      $action_key => array(
        'name' => ts('Edit'),
        'url' => 'civicrm/pcp/info',
        'qs' => "action=update&reset=1&id=%%id%%&context=contact&cid=%%cid%%",
        'title' => ts('Edit Personal Campaign Page'),
      ),
    );

    $pcps = array();

    foreach ($result['values'] as $pcpId => $pcp) {

      $class = '';
      if ($pcp['status_id'] != $approvedId || $pcp['is_active'] != 1) {
        $class = 'disabled';
      }

      $pageType = $pcp['page_type'];
      $pageId = (int) $pcp['page_id'];

      if ($pageType == 'contribute') {
        $contributionPageEvent = $pcp['api.ContributionPage.get']['values'][0]['title'];
        $pageUrl = CRM_Utils_System::url('civicrm/' . $pageType . '/transact', 'reset=1&id=' . $pageId);
      }
      else {
        $contributionPageEvent = $pcp['api.Event.get']['values'][0]['title'];
        $pageUrl = CRM_Utils_System::url('civicrm/' . $pageType . '/register', 'reset=1&id=' . $pageId);
      }

      if ($contributionPageEvent == '' || $contributionPageEvent == NULL) {
        $contributionPageEvent = '(no title found for ' . $pageType . ' id ' . $pageId . ')';
      }

      $contributionSoft = $this->getContributionSoft($pcp['api.ContributionSoft.get']);

      $pcps[$pcpId]['page_title'] = $pcp['title'];
      $pcps[$pcpId]['page_url'] = $pageUrl;
      $pcps[$pcpId]['status'] = $status[$pcp['status_id']];
      $pcps[$pcpId]['contribution_page_event'] = $contributionPageEvent;
      $pcps[$pcpId]['no_of_contributions'] = $contributionSoft['no_of_contributions'];
      $pcps[$pcpId]['amount_raised'] = $contributionSoft['amount_raised'];
      $pcps[$pcpId]['target_amount'] = $pcp['goal_amount'];
      $pcps[$pcpId]['action'] = CRM_Core_Action::formLink($edit_link, $action_key, array('id' => $pcpId, 'cid' => $contactId), ts('more'), FALSE, 'contributionpage.pcp.list', 'PCP', $pcpId);
      $pcps[$pcpId]['class'] = $class;
    }

    return $pcps;
  }

  private function getContributionSoft($contributionSoftGet) {
    $data = array('no_of_contributions' => 0, 'amount_raised' => 0.00);

    foreach ($contributionSoftGet['values'] as $key => $value) {
      $data['amount_raised'] += (float) $value['amount'];
    }

    $data['no_of_contributions'] = $contributionSoftGet['count'];
    $data['amount_raised'] = number_format($data['amount_raised'], 2);

    return $data;
  }

}
