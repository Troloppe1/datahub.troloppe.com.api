<?php

function streetDataConstructionDetailMatcher(string $constructionDetail): string | null
{
    return match ($constructionDetail) {
        'onhold' => 'On Hold',
        'completed' => 'Completed',
        'under_construction' =>  'Under Construction',
        default => null
    };
}

/**
 * Replaces file name with new name and retains file extension
 * 
 * @param string $fileName
 * @param string $newFileName
 * @return string
 */
function replaceFileName(string $fileName, string $newFileName): string
{
    return preg_replace("/[a-zA-Z]+(?=\.[a-zA-Z]+)/", $newFileName, $fileName);
}