#!/usr/bin/php5
<?php
   require_once("ConvertDb.php");

   $params = [
      ''    => 'help',
      'd:'  => 'dbname',
      'l::' => 'level',
      'a:'  => 'action'
   ];

   $dbname = '';
   $errors = [];
   $level = -100;
   $action = '';
   $help = "Example:
   ./AddDb.php -a=catalog -d=catalog.backup
   ./AddDb.php -a=plan -l=1 -d=plan_1_10102010.mbtiles\n";
   $options = getopt(implode('', array_keys($params)), $params);
   if (isset($options['action']) || isset($options['a'])) {
      $action = isset($options['action']) ? $options['action'] : $options['a'];
      if (!($action == "catalog" || $action == "plan")) {
         $errors[] = "action should be catalog or plan";
      }
   } else {
      $errors[] = "you must provide action";
   }
   if (isset($options['dbname']) || isset($options['d'])) {
      $dbname = isset($options['dbname']) ? $options['dbname'] : $options['d'];
   } else {
      $errors[] = "you must provide database";
   }
   if (isset($options['level']) || isset($options['l'])) {
      $level = isset($options['level']) ? $options['level'] : $options['l'];
      if ($level == -20) {
         $errors[] = "level value -20 reserved by catalog database";
      }
   }
   if ($action == "plan" && $level == -100) {
      $errors[] = "if you're uploading plan file, you should provide level number";
   }
   if (isset($options['help']) || count($errors)) {
      if ($errors ) {
         $help .= 'Errors:' . PHP_EOL . implode("\n", $errors) . PHP_EOL;
      }
    die($help);
   }


   echo "Welcome!\n";
   echo "It's important! Start this with your's database user!\n";

   switch ($action) {
      case "catalog":
         ConvertDb($dbname);
         break;
      case "plan":
         AddPlan($dbname, $level);
         break;
      default:
         break;
   }
