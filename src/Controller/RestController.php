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
}
