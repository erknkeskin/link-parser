<?php

require 'lib/LinkParser.php';
$file = 'index.html';

$linkParser = new LinkParser($file);
$linkParser->parse();
