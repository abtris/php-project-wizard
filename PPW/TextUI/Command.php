<?php
/**
 * PHP Project Wizard (PPW)
 *
 * Copyright (c) 2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   PPW
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */

/**
 * TextUI frontend for PPW.
 *
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/sebastianbergmann/php-project-wizard/tree
 * @since     Class available since Release 1.0.0
 */
class PPW_TextUI_Command
{
    /**
     * Main method.
     */
    public static function main()
    {
        $input  = new ezcConsoleInput;
        $output = new ezcConsoleOutput;

        $input->registerOption(
          new ezcConsoleOption(
            '',
            'name',
            ezcConsoleInput::TYPE_STRING,
            NULL,
            FALSE,
            '',
            '',
            array(),
            array(),
            TRUE,
            TRUE
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            '',
            'source',
            ezcConsoleInput::TYPE_STRING,
            'src'
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            '',
            'tests',
            ezcConsoleInput::TYPE_STRING,
            'tests'
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            '',
            'bootstrap',
            ezcConsoleInput::TYPE_STRING,
            'tests/autoload.php'
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            '',
            'phpcs',
            ezcConsoleInput::TYPE_STRING,
            'build/phpcs.xml'
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            '',
            'phpmd',
            ezcConsoleInput::TYPE_STRING,
            'build/phpmd.xml'
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            'f',
            'force',
            ezcConsoleInput::TYPE_NONE
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            '',
            'disable-apidoc',
            ezcConsoleInput::TYPE_NONE
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            '',
            'disable-phpab',
            ezcConsoleInput::TYPE_NONE
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            'h',
            'help',
            ezcConsoleInput::TYPE_NONE,
            NULL,
            FALSE,
            '',
            '',
            array(),
            array(),
            FALSE,
            FALSE,
            TRUE
           )
        );

        $input->registerOption(
          new ezcConsoleOption(
            'v',
            'version',
            ezcConsoleInput::TYPE_NONE,
            NULL,
            FALSE,
            '',
            '',
            array(),
            array(),
            FALSE,
            FALSE,
            TRUE
           )
        );

        try {
            $input->process();
        }

        catch (ezcConsoleOptionException $e) {
            self::showHelp();

            print "\n" . $e->getMessage() . "\n";

            exit(1);
        }

        if ($input->getOption('help')->value) {
            self::showHelp();
            exit(0);
        }

        else if ($input->getOption('version')->value) {
            self::printVersionString();
            exit(0);
        }

        $arguments       = $input->getArguments();
        $name            = $input->getOption('name')->value;
        $source          = $input->getOption('source')->value;
        $tests           = $input->getOption('tests')->value;
        $bootstrap       = $input->getOption('bootstrap')->value;
        $phpcs           = $input->getOption('phpcs')->value;
        $phpmd           = $input->getOption('phpmd')->value;
        $force           = $input->getOption('force')->value;
        $disableApiDoc   = $input->getOption('disable-apidoc')->value;
        $disableAutoload = $input->getOption('disable-autoload')->value;

        if (!is_dir($source)) {
            mkdir($source, 0777, TRUE);
        }

        if (!is_dir($tests)) {
            mkdir($tests, 0777, TRUE);
        }

        if (isset($arguments[0])) {
            $target = $arguments[0];
        }

        else if (isset($_ENV['PWD'])) {
            $target = $_ENV['PWD'];
        }

        else {
            self::showHelp();
            exit(1);
        }

        self::printVersionString();
    }

    /**
     * Shows the help.
     */
    protected static function showHelp()
    {
        self::printVersionString();

        print <<<EOT
Usage: ppw [switches] <directory>

  --name <name>           Name of the project

  --source <directory>    Directory with the project's sources (default: src)
  --tests <directory>     Directory with the project's tests (default: tests)
  For multiple directories use a comma separated list

  --bootstrap <script>    PHPUnit bootstrap script (default: tests/autoload.php)
  --phpcs <ruleset>       Ruleset for PHP_CodeSniffer (default: build/phpcs.xml)
  --phpmd <ruleset>       Ruleset(s) for PHPMD (default: build/phpmd.xml)

  --disable-apidoc        Do not include API documentation in the build script
  --disable-phpab         Do not include PHPAB in the build script

  --force                 Overwrite existing files

  --help                  Prints this usage information
  --version               Prints the version and exits

EOT;
    }

    /**
     * Prints the version string.
     */
    protected static function printVersionString()
    {
        print "PHP Project Wizard (PPW) @package_version@ by Sebastian Bergmann.\n";
    }
}
