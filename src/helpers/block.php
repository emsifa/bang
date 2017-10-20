<?php

/**
 * Parse block tags
 *
 * @param string $content
 * @param string $identifier
 * @return array [string $content, array $blocks]
 */
function parse_blocks($content) {
    $regex = "/\<\!-- block::([a-z0-9_.-]+) --\>/i";
    $splits = preg_split($regex, $content);
    if (count($splits) == 1) {
        return [$content, []];
    }

    preg_match_all($regex, $content, $matchs);
    $content = array_shift($splits);
    $blocks = [];
    foreach($splits as $i => $block_content) {
        $block_name = $matchs[1][$i];
        $blocks[$block_name] = $block_content;
    }

    return [$content, $blocks];
}

/**
 * Get/set block
 *
 * @param string $block_name
 * @param string $block_content
 * @return mixed
 */
function block($block_name, $block_content = null) {
    static $blocks;
    if (!$blocks) $blocks = [];
    $args = func_get_args();
    if (count($args) == 1) {
        return isset($blocks[$block_name]) ? $blocks[$block_name] : null;
    } else {
        $blocks[$block_name] = $block_content;        
    }
}
