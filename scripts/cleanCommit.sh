#!/bin/bash
#set -x

$baseDir='/home/frmwk/frmwk.crondex.com/';

cp ${baseDir}/config/config.php ${baseDir}/config/config.php.tmp;
cp ${baseDir}/config/config.php.default ${baseDir}/config/config.php
git commit
