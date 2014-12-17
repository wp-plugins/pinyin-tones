<?php
/*
Plugin Name: Pinyin Tones
Plugin URI: http://somemilk.org/pinyin-tones-plugin/
Description: Transforms digital pinyin tone notation into diacritic marks
Version: 1.0.2
Author: Andrey Kravchuk
Author URI: http://somemilk.org/
*/

function transform_pinyin_tones($content)
{
    if(!preg_match_all('`\[pinyin\](.*)\[/pinyin\]`Uis', $content, $r)) return $content;

    $tones = array(
    'a1' => '257',
    'a2' => '225',
    'a3' => '462',
    'a4' => '224',
    'e1' => '275',
    'e2' => '233',
    'e3' => '283',
    'e4' => '232',
    'i1' => '299',
    'i2' => '237',
    'i3' => '464',
    'i4' => '236',
    'o1' => '333',
    'o2' => '243',
    'o3' => '466',
    'o4' => '242',
    'u1' => '363',
    'u2' => '250',
    'u3' => '468',
    'u4' => '249',
    'v1' => '470',
    'v2' => '472',
    'v3' => '474',
    'v4' => '476'
    );

    $vowels = array('a', 'e', 'i', 'o', 'u', 'v');

    foreach($r[0] as $i => $match)
    {
        $digital = $r[1][$i];
        $diacritic = $digital;
        if(!preg_match_all('`([a-z]{1,6})([1-4])`is', $digital, $syllables)) continue;
        foreach($syllables[0] as $k => $syllable)
        {
            $s = $syllables[1][$k];
            $t = $syllables[2][$k];
            if(preg_match('`(a|e)`i', $s, $r2))
            {
                $s = preg_replace('`'.$r2[1].'`i', '&#'.$tones[strtolower($r2[1]).$t].';', $s);
            }
            elseif(preg_match('`ou`', $s, $r2))
            {
                $s = preg_replace('`ou`i', '&#'.$tones['o'.$t].';u', $s);
            }
            else
            {
                for($j=strlen($s)-1;$j;$j--)
                {
                    if(in_array($s[$j], $vowels))
                    {
                        $s = str_replace($s[$j], '&#'.$tones[$s[$j].$t].';', $s);
                        break;
                    }

                }
            }

            $diacritic = str_replace($syllable, $s, $diacritic);
        }

        $content = str_replace($match, $diacritic, $content);
    }
    return $content;
}

add_filter('the_content', 'transform_pinyin_tones');
add_filter('the_title', 'transform_pinyin_tones');
add_filter('single_post_title', 'transform_pinyin_tones');
add_filter('the_excerpt', 'transform_pinyin_tones');
add_filter('comment_text', 'transform_pinyin_tones');

?>
