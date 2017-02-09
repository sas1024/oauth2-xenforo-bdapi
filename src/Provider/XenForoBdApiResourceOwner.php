<?php
namespace Sas1024\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class XenForoBdApiResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * Raw response
     *
     * @var array
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param array $response
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * Get resource owner id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getValueByKey($this->response, 'user.user_id');
    }

    /**
     * Get resource owner email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'user.user_email');
    }

    /**
     * Get resource owner username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->getValueByKey($this->response, 'user.username');
    }

    /**
     * Get resource owner validation flag
     *
     * @return bool|null
     */
    public function getIsValid()
    {
        return $this->getValueByKey($this->response, 'user.user_is_valid');
    }

    /**
     * Get resource owner verified flag
     *
     * @return bool|null
     */
    public function getIsVerified()
    {
        return $this->getValueByKey($this->response, 'user.user_is_verified');
    }

    /**
     * Get resource owner user groups
     *
     * @return array|null
     */
    public function getUserGroups()
    {
        return $this->getValueByKey($this->response, 'user.user_groups');
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
