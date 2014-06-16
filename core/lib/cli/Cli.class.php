<?php

class Cli {

  static function storeCommand($dir) {
    $argv = $_SERVER['argv'];
    $argv[0] = str_replace('.php', '', $argv[0]);
    LogWriter::str('commands', implode(' ', $argv), $dir);
  }

  static function formatPutFileCommand($cmd, $file, $append = false) {
    return "'( cat << EOF\n$cmd\nEOF\n) ".($append ? '>>' : '>')." $file'";
  }

  static function runCode($server, $code, $includes, $runBasePath = null) {
    $code = self::formatRunCmd($code, $includes, $runBasePath);
    return sys("ssh $server $code", true);
  }

  static function ssh($server, $cmd) {
    return sys("ssh $server '$cmd'");
  }

  static function formatRunCmd($code, $includes, $runBasePath = null) {
    $code = str_replace("'", '"', $code);

    return "'".self::addRunPaths($code, $includes, $runBasePath)."'";
  }

  static function addRunPaths($code, $includes, $runBasePath = null) {
    return 'php '.($runBasePath ? $runBasePath : '~').'/ngn-env/run/run.php "'.$code.'" '.$includes;
  }

  static function rpc($server, $code) {
    $cmd = "ssh $server sudo -u user TERM=dumb 'php /home/user/ngn-env/run/run.php rpc \"$code\"'";
    return json_decode(`$cmd`, true);
  }

  static function shell($cmd, $output = true) {
    if ($output) output($cmd);
    return `$cmd`;
  }

  static function confirm($text) {
    print "$text\nEnter 'y' if agree.\n";
    $fp = fopen('php://stdin', 'r');
    $lastLine = false;
    while (!$lastLine) {
      $nextLine = fgets($fp, 1024);
      return 'y' == lcfirst(trim($nextLine)) ? true : false;
    }
  }

  static function prompt($caption = null) {
    print ($caption ? : "Enter text").":\n";
    $fp = fopen('php://stdin', 'r');
    $nextLine = false;
    while (!$nextLine) {
      $nextLine = fgets($fp, 1024);
      if ($nextLine[strlen($nextLine) - 1] == "\n") break;
    }
    $nextLine = trim($nextLine);
    return $nextLine;
  }

  static function replaceOut($str) {
    $numNewLines = substr_count($str, "\n");
    echo chr(27)."[0G"; // Set cursor to first column
    echo $str;
    echo chr(27)."[".$numNewLines."A"; // Set cursor up x lines
  }

}