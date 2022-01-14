<?php

namespace Seedling\Acf\FieldSeedlings;

use Seedling\Acf\FieldSeedlings\Traits\DefaultAttribute;

class WyswiwygFieldSeedling
{
    use DefaultAttribute;
    /**
     * @param $field
     * @return mixed|string
     */
    static function generate($field)
    {
        /*
        * When generating this field it sometimes a has a default value.
        * If this default value is present we return that value instead of returning faker data.
        */
        if (self::default($field)) {
            return self::default($field);
        }

        /*
         *as Faker does not have a tailored solution for wysiwyg fields in wordpress, we return a generic catch-all
         * html example that gives all the possible options in this field, this is useful for frontending all possibilities.
         */

        return "
<h1>Heading 1</h1>
<h1>Heading 2</h1>
<h1>Heading 3</h1>
<h1>Heading 4</h1>
<h1>Heading 5</h1>
<h1>Heading 6</h1>
This is a paragraph text with some <strong>bold </strong>elements and some <em>Italic</em> elements, it is possible to <del>strikethrough</del> text as well. This wysiwiyg field can also have multiple links: <a href=\"#link_1\">link 1</a> <a href=\"#link_2\">link2</a> <a href=\"#link3\">link 3 with some longer title</a>.
<ul>
 	<li>Bulleted list item 1</li>
 	<li>Bulleted list item 2 with some text</li>
 	<li>Bulleted list item 3 with even more text, this should be tested on mobile to see if the new line is nicely indented.</li>
</ul>
<ol>
 	<li>Numbered list item 1</li>
 	<li>Numbered list item 2 with some text</li>
 	<li>Numbered list item 3 with even more text, this should be tested on mobile to see if the new line is nicely indented.</li>
</ol>
A Wysiwyg field can have enters and 'forced' newlines
as demonstrated here.



&nbsp;
<blockquote>Look at me, I am a blockquote.</blockquote>
In a Wysiwyg field, the user has the option to place images as well.
<img src=\"https://via.placeholder.com/150\" alt=\"A generic image\" />";
    }

}
