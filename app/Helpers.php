<?php

use Illuminate\Database\Query\Builder;

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

/**
 * Titlecase 
 * @param mixed $str
 * @return string
 */
function titleCase($str)
{
    return ucwords(str_replace("_", " ", $str));
}


function formatServiceResponse(
    string $message,
    mixed $payload = null,
    int $statusCode = 200,
    bool $rawResponse = false
): array {

    if ($rawResponse) {
        return ['data' => $payload];
    }

    $response = [
        'data' => [
            'success' => true,
            'message' => $message,
        ],
        'status' => $statusCode,
    ];

    if ($payload !== null) {
        $response['data']['data'] = $payload;
    }

    return $response;
}

function apiResponse(array $serviceResponse)
{
    return response()->json(...$serviceResponse);
}

