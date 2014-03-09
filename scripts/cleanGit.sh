#!/bin/bash
#set -x

CONFIGDIR='/home/frmwk/frmwk.crondex.com/config';

#move the running config to a temp location
mv ${CONFIGDIR}/config.php ${CONFIGDIR}/config.php.tmp;

#copy a blank config in place of the running config
cp ${CONFIGDIR}/config.php.blank ${CONFIGDIR}/config.php

#add blank config  to repo
git add ${CONFIGDIR}/config.php

echo "Would you like to 'commit', 'push', 'checkout', or 'merge'?";
read PUSHORGET;

#route based on input
case "$PUSHORGET" in
    commit) git commit
        ;;
    push) git push
        ;;
    checkout) git branch
        echo "Which branch would you like to checkout?"
        read BRANCH
        git checkout $BRANCH
        ;;
    merge) git branch
        echo "With which branch would you like to merge?"
        read BRANCH
        git merge $BRANCH
        ;;
    *) echo "Illegal option: $PUSHORGET"
        ;;
esac

#copy the usable (configured) config back
cp ${CONFIGDIR}/config.php.tmp ${CONFIGDIR}/config.php
rm -f ${CONFIGDIR}/config.php.tmp
