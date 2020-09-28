<?php
require_once(__DIR__.'/core.php');

if (isset($conf['gitHubRepo']))
  header('Location: '.$conf['gitHubRepo']);
