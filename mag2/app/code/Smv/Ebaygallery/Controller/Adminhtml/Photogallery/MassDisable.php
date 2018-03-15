<?php
namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;

use  Smv\Ebaygallery\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassDelete
 */
class MassDisable extends AbstractMassStatus
{
    /**
     * Field id
     */
    const ID_FIELD = 'photogallery_id';

    /**
     * ResourceModel collection
     *
     * @var string
     */
    protected $collection = 'Smv\Ebaygallery\Model\Resource\Photogallery\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $model = 'Smv\Ebaygallery\Model\Photogallery';

    /**
     * Item status
     *
     * @var bool
     */
    protected $status = 2;
}
