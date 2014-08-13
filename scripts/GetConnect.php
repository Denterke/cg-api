<?php
   function getValue($src) {
      $left = strpos($src, '"');
      $right = strpos($src, '"', $left + 1);
      return mb_substr($src, $left + 1, strlen($src) - $left - 3);
   }

   function getConnection($dbname) {
      echo "Let's create some connection....\n";
      $path = dirname(__FILE__);
      $symfConfig = fopen($path . '/../app/config/config.yml', 'r');
      if (!$symfConfig) {
         die("Problem with paths!\n");
      }
      echo "Symfony config found!\n";
      $str = fgets($symfConfig);
      while (fgets($symfConfig) != "doctrine:\n") { }
      $paramCount = 0;
      $params = [
         "dbname" => "",
         "user" => "",
         "password" => "",
         "port" => "",
         "host" => ""
      ];
      $connection_string = "";
      if ($dbname != '') {
         $params["dbname"] = $dbname;
      }
      while ($paramCount < count($params)) {
         $str = fgets($symfConfig);
         foreach ($params as $paramName => $paramVal) {
            if (strpos($str, $paramName)) {
               $val = $paramVal == "" ? getValue($str) : $paramVal;
               $connection_string .= $paramName . " = " . $val . " ";
               $params[$paramName] = $val;
               $paramCount++;
            }
         }
      }
      echo "Your's connection string:\n".$connection_string . "\n";
      $dbcon = pg_connect($connection_string);
      if (!$dbcon) {
         die('Connection failed!');
      } else {
         echo "Connection succeed!\n";
      }
      // pg_close($dbcon);
      return [$dbcon, $params];
   }

