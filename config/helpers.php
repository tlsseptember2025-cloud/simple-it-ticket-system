<?php
function cleanMessage(string $text): string
{
    // Normalize line endings
    $text = str_replace(["\r\n", "\r"], "\n", $text);

    // Remove inline images placeholders
    $text = preg_replace('/\[image:.*?\]/i', '', $text);
    $text = preg_replace('/cid:.*?\)/i', '', $text);

    // Remove quoted replies
    $patterns = [
        '/^On .* wrote:.*$/ims',
        '/^>.*$/m',
        '/^-{2,}.*$/ims',
    ];
    foreach ($patterns as $p) {
        $text = preg_replace($p, '', $text);
    }

    // Remove common signatures
    $signatures = [
        '/best regards.*/is',
        '/kind regards.*/is',
        '/regards.*/is',
        '/thanks.*/is',
        '/thank you.*/is',
        '/sent from my.*/is',
        '/www\..*/is',
        '/\+?\d[\d\s\-()]{7,}/',
    ];
    foreach ($signatures as $s) {
        $text = preg_replace($s, '', $text);
    }

    // Trim & collapse whitespace
    $text = trim(preg_replace("/\n{3,}/", "\n\n", $text));

    return $text;
}
?>