<?php
namespace Application\Role;

class NoPermissionsException extends \Exception
{
    function __construct() {
        parent::__construct('No Permissions !');
    }
}

?>