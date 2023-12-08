<?php

namespace App\Exceptions;

use Exception;

class ForeignKeyConstraintException extends Exception
{

    protected $message = 'No se puede borrar, hay datos dependientes de él.';
    public function getCustomMessage()
    {
        return $this->message;
    }
}
