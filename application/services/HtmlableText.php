<?php

namespace app\services;

class HtmlableText
{
    protected $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function toHtml()
    {
        $text = $this->text;

        // Do not process if the text is not a string
        if (! is_string($text)) {
            return '';
        }

        // Early return
        if (empty($text)) {
            return $text;
        }

        include_once APPPATH . 'third_party/simple_html_dom.php';

        // Remove any not allowed tags
        $allowedTags = array_map(fn ($tag) => "<{$tag}>", array_keys(common_allowed_html_tags()));

        $text = strip_tags($text, implode(', ', $allowedTags));

        // Remove any inline styles
        $html = str_get_html($text);

        foreach ($html->find('*[style]') as $item) {
            $item->style = null;
        }
        foreach ($html->find('a') as $item) {
            $item->setAttribute('target', '_blank');
            $item->setAttribute('rel', 'nofollow');
        }

        $text = $html->save();

        // Escape the entire text first
        $text = e($text, false);

        // Process each allowed tag
        foreach (common_allowed_html_tags() as $tagName => $attributes) {
            // Start tag, capturing attributes
            $text = preg_replace_callback("/&lt;({$tagName})(.*?)&gt;/i", function ($matches) use ($attributes) {
                // Decode the tag
                $attrsString = htmlspecialchars_decode($matches[2]);
                // Filter and rebuild attributes
                $attrsString = preg_replace_callback('/(\w+)=("[^"]*"|\'[^\']*\')/', function ($attrMatches) use ($attributes) {
                    // Check if the attribute is allowed for this tag
                    if (in_array(strtolower($attrMatches[1]), $attributes)) {
                        // Return the original attribute string
                        return $attrMatches[0];
                    }

                    // Exclude the attribute by returning an empty string
                    return '';
                }, $attrsString);

                return "<{$matches[1]}{$attrsString}>";
            }, $text);

            // End tag
            $text = preg_replace("/&lt;\\/{$tagName}&gt;/i", "</{$tagName}>", $text);
        }

        // Convert URLs to clickable links if they are not already in an anchor tag
        $text = preg_replace_callback('/(?<!href=")(?<!href=\')(?<!src=")(?<!src=\')\b(http|https):\/\/[^\s<]+/i', function ($urlMatches) {
            $url = htmlspecialchars_decode($urlMatches[0]);

            return "<a href=\"{$url}\" target=\"_blank\" rel=\"nofollow\">{$url}</a>";
        }, $text);

        return $text;
    }
}
