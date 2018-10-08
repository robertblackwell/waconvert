<?php

use GetOpt\Command;
use GetOpt\GetOpt;
use GetOpt\Operand;

class MoveCommand extends Command
{
    public function __construct()
    {
        parent::__construct('move', [$this, 'handle']);
        
        $this->addOperands([
            Operand::create('file', Operand::REQUIRED)
                ->setValidation('is_readable'),
            Operand::create('destination', Operand::REQUIRED)
                ->setValidation('is_writable')
        ]);
        
    }
    
    public function handle(GetOpt $getOpt)
    {
        copy($getOpt->getOperand('file'), $getOpt->getOperand('destination'));
    } 
}