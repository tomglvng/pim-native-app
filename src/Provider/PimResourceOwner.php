<?php

namespace App\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

/**
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /** @var array  */
    protected $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->getValueByKey($this->response, 'id');
    }

    public function toArray()
    {
        return $this->response;
    }
}
