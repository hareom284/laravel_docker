<?php

namespace Src\Common\Domain\Exceptions;

final class EmailAlreadyUsedException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Email is already used! Try with another.');
    }
}