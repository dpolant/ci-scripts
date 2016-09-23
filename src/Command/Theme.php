<?php

namespace Mediacurrent\CiScripts\Command;

trait Theme
{

    /**
     * Theme Build command.
     *
     * theme:build runs the following -
     *
     *  nvm use
     *  npm install
     *  npm run build
     *
     * @param string $pathToTheme Absolute path to the theme directory.
     *
     * @return object Result
     */
    public function themeBuild($pathToTheme = null)
    {
        $this->taskThemeBuild()
          ->themeDirectory($pathToTheme)
          ->nvmUse()
          ->npmInstall()
          ->npmRunBuild()
          ->run();
    }

    /**
     * Theme Compile command.
     *
     * theme:compile runs the following -
     *
     *  nvm use
     *  npm install
     *  npm run compile
     *
     * @param string $pathToTheme Absolute path to the theme directory.
     *
     * @return object Result
     */
    public function themeCompile($pathToTheme = null)
    {
        $this->taskThemeStyleGuide()
          ->themeDirectory($pathToTheme)
          ->nvmUse()
          ->npmInstall()
          ->npmRunCompile()
          ->run();
    }

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

    /**
     * Theme Watch command.
     *
     * theme:watch runs the following -
     *
     *  nvm use
     *  npm install
     *  npm run watch
     *
     * @param string $pathToTheme Absolute path to the theme directory.
     *
     * @return object Result
     */
    public function themeWatch($pathToTheme = null)
    {
        $this->taskThemeWatch()
          ->themeDirectory($pathToTheme)
          ->nvmUse()
          ->npmInstall()
          ->npmRunWatch()
          ->run();
    }
}
