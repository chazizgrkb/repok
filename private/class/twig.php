<?php

namespace rePok {

    use Parsedown;
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFilter;
    use Twig\TwigFunction;

    class RepokExtension extends AbstractExtension
    {
        public function getFunctions()
        {
            return [
                new TwigFunction('level', '\RePok\level', ['is_safe' => ['html']]),
                new TwigFunction('userlink', '\RePok\userlink', ['is_safe' => ['html']]),
                new TwigFunction('comments', '\RePok\comments', ['is_safe' => ['html']]),
                new TwigFunction('pagination', '\RePok\pagination', ['is_safe' => ['html']]),
                new TwigFunction('custom_info', '\RePok\customInfo', ['is_safe' => ['html']]),
                new TwigFunction('git_commit', '\RePok\gitCommit'),
                new TwigFunction('custom_header', '\RePok\customHeader'),
            ];
        }

        public function getFilters()
        {
            return [
                new TwigFilter('cat_to_type', 'cat_to_type'),

                // Markdown function for non-inline text, sanitized.
                new TwigFilter('markdown', function ($text) {
                    $markdown = new Parsedown();
                    $markdown->setSafeMode(true);
                    return $markdown->text($text);
                }, ['is_safe' => ['html']]),

                // Markdown function for inline text, sanitized.
                new TwigFilter('markdown_inline', function ($text) {
                    $markdown = new Parsedown();
                    $markdown->setSafeMode(true);
                    return $markdown->line($text);
                }, ['is_safe' => ['html']]),

                // Markdown function for non-inline text. **NOT SANITIZED, DON'T LET IT EVER TOUCH USER INPUT**
                new TwigFilter('markdown_unsafe', function ($text) {
                    $markdown = new Parsedown();
                    return $markdown->text($text);
                }, ['is_safe' => ['html']]),

                new TwigFilter('relative_time', '\RePok\relativeTime'),

                new TwigFilter('cmt_num_to_type', '\RePok\cmtNumToType'),

            ];
        }
    }
}