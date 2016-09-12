<?php

namespace Mediacurrent\CiScripts\Command;

trait Theme
{
    /**
     * Theme Style Guide command.
     *
     * theme:style-guide runs the following -
     *
     *  nvm use
     *  npm install
     *  npm run styleguide
     *
     * @param string $pathToTheme Absolute path to the theme directory.
     *
     * @return object Result
     */
    public function themeStyleGuide($pathToTheme = null)
    {
        $this->taskThemeStyleGuide()
          ->themeDirectory($pathToTheme)
          ->nvmUse()
          ->npmInstall()
          ->npmRunStyleGuide()
          ->run();
    }
}
