#!/bin/bash
#set -x

CONFIGDIR='/home/frmwk/frmwk.crondex.com/config';

#move the running config to a temp location
mv ${CONFIGDIR}/config.php ${CONFIGDIR}/config.php.tmp;

#copy a blank config in place of the running config
cp ${CONFIGDIR}/config.php.blank ${CONFIGDIR}/config.php

#add blank config  to repo
git add ${CONFIGDIR}/config.php

echo "Would you like to 'commit' or 'push'?";
read PUSHORGET;

#either commit or push based on input
if [ "$PUSHORGET" == "commit" ]
then
    git commit
elif [ "$PUSHORGET" == "push" ]
then
    git push
else
    echo "Illegal option: $PUSHORGET"
fi

#copy the usable (configured) config back
cp ${CONFIGDIR}/config.php.tmp ${CONFIGDIR}/config.php
rm -f ${CONFIGDIR}/config.php.tmp
