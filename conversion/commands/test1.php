<?php

use GetOpt\Command;
use GetOpt\GetOpt;
use GetOpt\Operand;

class Test1Command extends Command
{
    public function __construct()
    {
        parent::__construct('test1', [$this, 'handle']);
        
        $this->addOperands([
            Operand::create('file', Operand::REQUIRED)
                ->setValidation('is_string'),
            Operand::create('destination', Operand::REQUIRED)
                ->setValidation('is_string')
        ]);
        
    }
    
    public function handle(GetOpt $getOpt)
    {
        $file = $getOpt->getOperand('file');
        $dest = $getOpt->getOperand('destination');

        print "{$file}, {$dest} \n";
        // copy($getOpt->getOperand('file'), $getOpt->getOperand('destination'));
    } 
}