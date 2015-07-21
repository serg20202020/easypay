<?php
namespace Application\Role;

use Zend\Permissions\Acl\Role\GenericRole as Role;

abstract class BaseRole extends Role
{
    public $name;

    function __construct( $name = '' )
    {
        parent::__construct(get_class($this));
        $this->name = $name;
    }
}

?>