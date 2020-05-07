<?php

namespace Src\Controller;

use Google\CRC32\CRC32;

class RestController 
{
    protected function getCRC32C($payload)
    {
        $crc = CRC32::create(CRC32::CASTAGNOLI);
        $crc->update($payload);
        return $crc->hash();
    }

    protected function optimisticLockFailure($request, $payload)
    {
        $ifMatch = $request->getHeader("If-Match");
        $eTag = $this->getCRC32C($payload);
        return $ifMatch && count($ifMatch) == 1 && $eTag != $ifMatch[0];
    }
}
