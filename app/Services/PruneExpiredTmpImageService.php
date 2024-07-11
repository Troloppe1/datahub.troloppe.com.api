<?php

namespace App\Services;

use Carbon\Carbon;

class PruneExpiredTmpImageService
{
    /**
     * 
     * @param int $minutes
     * @return string
     */
    public function deleteExpiredImages(int $minutes = 30)
    {
        $count = 0;
        foreach (\Storage::allFiles('public/tmp') as $file) {
            $fileCreationTimeStamp = \Storage::lastModified($file);
            $fileCreationDate = Carbon::createFromTimestamp($fileCreationTimeStamp);
            $currentTime = Carbon::now();
            $timeDiff = $fileCreationDate->diffInMinutes($currentTime);

            if ($timeDiff > $minutes) {
                $count++;
                \Storage::delete($file);
            }
        }
        return match($count){
            0 => 'No tmp file was pruned.',
            1 => 'A tmp file was pruned.',
            default => "{$count} tmp file was pruned."
        };
    }
}