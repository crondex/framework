#!/bin/bash
#set -x

CONFIGDIR='/home/frmwk/frmwk.crondex.com/config';

#copy a blank config over the config
cp ${CONFIGDIR}/config.php.blank ${CONFIGDIR}/config.php

#add blank config to repo
git add ${CONFIGDIR}/config.php

echo "Would you like to 'commit', 'push', 'checkout', or 'merge'?";
read PUSHORGET;

#route based on input
case "$PUSHORGET" in
    commit) git commit
        ;;
    push) git push
        ;;
    checkout) echo "Would you like to create a new branch?"
        read NEWBRANCH
        if [ "$NEWBRANCH" == "yes" ]; then BRANCHFLAG='-b'; fi
        git branch
        echo "Which branch would you like to checkout/create?"
        read BRANCH
        git checkout $BRANCHFLAG $BRANCH
        ;;
    merge) git branch
        echo "With which branch would you like to merge?"
        read BRANCH
        git merge $BRANCH
        ;;
    *) echo "Illegal option: $PUSHORGET"
        ;;
esac

#copy the conifgured config back
cp ${CONFIGDIR}/config.php.configured ${CONFIGDIR}/config.php
