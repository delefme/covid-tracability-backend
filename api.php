<?php
require_once('core.php');

DAFME\Covid\API::process((string)($_GET['path'] ?? ''));
