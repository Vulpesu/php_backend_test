<?php 
namespace Exepti;

interface ProcessSpecificationInterface
{
    public function isCorrespond(\ProcessInterface $Process): bool;
}

interface ProcessFieldsSpecificationInterface
{
    public function areCorrespondFields(\ProcessInterface $Process): bool;
}


class ProcessCorresponding implements ProcessSpecificationInterface
{
    public function isCorrespond(\ProcessInterface $Process): bool
    {
        return true;
    }
}

