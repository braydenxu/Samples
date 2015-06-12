<?php
/**
 * Created by PhpStorm.
 * User: Lei
 * Date: 6/12/15
 * Time: 12:39 PM
 */

function anagrams($words = array(), $query) {
    $map = array();

    $qryLen = strlen($query);
    foreach ($words as $word) {
        if (strlen($word) != $qryLen) {
            continue;
        }
        $sortedWord = sortWord($word);
        $map[$sortedWord][] = $word;
    }

    $sortedQuery = sortWord($query);
    echo 'Anagrams of "' . $query . '": <br>';
    if (!empty($sortedQuery) && !empty($map[$sortedQuery])) {
        echo_array($map[$sortedQuery]);
        echo "<br>";
    } else {
        echo "not found<br>";
    }
}

function sortWord($word) {
    if (!empty($word)) {
        $wordArr = str_split($word);
        sort($wordArr);
        return implode('', $wordArr);
    }
}

function echo_array($arr) {
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

$anagrams = array(
    'abets',
    'baste',
    'betas',
    'beast',
    'beats',
    'angel',
    'angle',
    'glean',
    'angle',
    // six letters
    'actors',
    'costar',
    'castor'
);

anagrams($anagrams, 'beast');

anagrams($anagrams, 'actors');

anagrams($anagrams, 'xxxxxx');