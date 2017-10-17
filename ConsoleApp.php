<?php

namespace SoftBricks\CLI;

/**
 * Class ConsoleApp
 * Helps creating CLI scripts
 * @package SoftBricks\CLI
 */
abstract class ConsoleApp {

    // define read pointer
    protected $inputHandle;

    // stack of all arguments that application got on call
    protected $analyzedARGS = array();
    protected $rawARGS = array();

    /**
     * Opens handle for the input stream & renders console arguments into array stack
     */
    function __construct(){

        // open input stream
        $this->inputHandle = fopen("php://stdin","r");

        // set the raw arguments
        $this->rawARGS = $GLOBALS['argv'];
        unset($this->rawARGS[0]);
        $this->rawARGS = array_values($this->rawARGS);

        // parse all arguments we've got
        parse_str(implode('&', array_slice($GLOBALS['argv'], 1)), $this->analyzedARGS);

        // run this console application
        $this->run();
    }

    /**
     * We always close the input stream... (safety first!)
     */
    function __destruct(){

        // close input stream
        fclose($this->inputHandle);
    }


    /**
     * @param string $bufferSize optional
     * @return string the users console input
     */
    protected function readString ($bufferSize='255') {

        // read from input stream
        return trim(
            fgets($this->inputHandle, $bufferSize)
        );
    }

    /**
     * @return int the users console input as string
     */
    protected function readInt () {

        // initialize integer that should be returned
        $number = null;

        // read number from input stream
        fscanf($this->inputHandle, "%d\n", $number);

        // return the parsed number
        return $number;
    }

    /**
     * Creates a colored text output string for unix shells
     * @param $string
     * @param null $foreground_color
     * @param null $background_color
     * @return string
     */
    private function colorizeString($string, $foreground_color = null, $background_color = null) {
        $colored_string = "";

        // Check if given foreground color found
        if ($foreground_color !== null) {
            $colored_string .= "\033[" .$foreground_color . "m";
        }
        // Check if given background color found
        if ($background_color !== null) {
            $colored_string .= "\033[" . $background_color . "m";
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }

    /**
     * writes some text into the console and ends the line with a linebreak
     * @param $txt
     */
    protected  function println($txt, $colorForeground = null, $colorBackground = null) {
        echo $this->colorizeString($txt, $colorForeground, $colorBackground)."\n";
    }

    /**
     * writes some text into the console (no linebreak is attached)
     * @param $txt
     * @param null $colorForeground
     * @param null $colorBackground
     */
    protected function _print($txt, $colorForeground = null, $colorBackground = null){
        echo $this->colorizeString($txt, $colorForeground, $colorBackground);
    }

    /**
     * printes amount $n (standard 1) line breaks into the console
     * @param int $n
     */
    protected function lineBreak($n = 1) {
        for($i = 1; $i<=$n; $i++) echo "\n";
    }

    /**
     * checks if $arg was set at the callup of this script or not
     * @param $arg
     * @return bool
     */
    protected function isArgAvailable($arg) {
        return isset($this->analyzedARGS[$arg]);
    }

    /**
     * @param $arg
     * @return mixed|null the requested argument or NULL if not available
     */
    protected function getArg($arg) {
        if ($this->isArgAvailable($arg)) {
            return $this->analyzedARGS[$arg];
        } else {
            return null;
        }
    }

    /**
     * @return void
     */
    abstract protected function run();
}