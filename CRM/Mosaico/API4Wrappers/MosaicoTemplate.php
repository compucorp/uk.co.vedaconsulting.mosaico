<?php

// This is only necessary in CiviCRM versions prior to 6.12 (and after about 6.10). See:
// https://lab.civicrm.org/extensions/mosaico/-/issues/699
// and
// https://github.com/civicrm/civicrm-core/pull/34839https://github.com/civicrm/civicrm-core/pull/34839
require_once 'api/Wrapper.php';

class CRM_Mosaico_API4Wrappers_MosaicoTemplate implements API_Wrapper {

  /**
   * Add current domain_id in whereClause for the API request.
   */
  public function fromApiInput($apiRequest) {
    $domainId = CRM_Core_Config::domainID();

    $params = $apiRequest->getParams();
    $apiRequest->addWhere('domain_id', '=', $domainId);
    return $apiRequest;
  }

  public function toApiOutput($apiRequest, $result) {
    return $result;
  }

}
