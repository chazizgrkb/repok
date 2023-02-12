<?php

namespace rePok {
    class Theme {
        public $id;
        public $name;
        public $logo;
        public $override;

        public function __construct(string $id, string $name, string $logo, $override) {
            $this->id = $id;
            $this->name = $name;
            $this->logo = $logo;
            $this->override = $override;
        }

        public static function availableThemes(): array {
            return [
                new Theme("yt", "YouTube", "img/logo_sm.gif", false),
                new Theme("pt", "PokTube - February 2021", "img/pt_feb/logo_sm.gif", "/img/pt_feb/styles.css"),
            ];
        }

        public static function getCurrentTheme(): Theme {
            $availableThemes = self::availableThemes();
            $themeId = $_COOKIE['rptheme'] ?? "yt";
            foreach ($availableThemes as $theme) {
                if ($theme->id === $themeId) {
                    return $theme;
                }
            }
            return $availableThemes[0];
        }
    }

}