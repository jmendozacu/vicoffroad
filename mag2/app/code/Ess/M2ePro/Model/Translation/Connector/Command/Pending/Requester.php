<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Translation\Connector\Command\Pending;

abstract class Requester extends \Ess\M2ePro\Model\Connector\Command\Pending\Requester
{
    /**
     * @var \Ess\M2ePro\Model\Account|null
     */
    protected $account = NULL;

    // ########################################

    public function __construct(
        \Ess\M2ePro\Helper\Factory $helperFactory,
        \Ess\M2ePro\Model\Factory $modelFactory,
        \Ess\M2ePro\Model\Account $account,
        array $params
    )
    {
        $this->account = (!is_null($account->getId())) ? $account : NULL;
        parent::__construct($helperFactory, $modelFactory, $params);
    }

    //########################################

    protected function buildRequestInstance()
    {
        $request = parent::buildRequestInstance();

        $requestData = $request->getData();
        if (!is_null($this->account)) {
            $requestData['account'] = $this->account->getChildObject()->getTranslationHash();
        }
        $request->setData($requestData);

        return $request;
    }

    //########################################

    protected function getProcessingParams()
    {
        $params = parent::getProcessingParams();

        if (!is_null($this->account)) {
            $params['account_id'] = $this->account->getId();
        }

        return $params;
    }

    protected function getResponserParams()
    {
        $params = parent::getResponserParams();

        if (!is_null($this->account)) {
            $params['account_id'] = $this->account->getId();
        }

        return $params;
    }

    //########################################
}
