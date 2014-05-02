#!/bin/bash
# set -x

# Written by, Andrew McLaughlin
# Date: March 27, 2014
# This is a basic script to allow for using git commands without including
# sensitive config info in commits, pushes, merges, etc.

CONFIGDIR='/home/frmwk/frmwk.crondex.com/app/config';

function copyConfigs {
    for CONFIGFILE in $(ls -1 $CONFIGDIR | grep -v 'php.\|ini.') #screen out blank and configured copies
    do
        if [ $1 == 'begin' ]
        then
            #stage config
            git add ${CONFIGDIR}/${CONFIGFILE}

            #copy a blank config over the configured config
            cp ${CONFIGDIR}/${CONFIGFILE}.blank ${CONFIGDIR}/${CONFIGFILE}
 
            #add blank config to repo
            git add ${CONFIGDIR}/${CONFIGFILE}

        elif [ $1 == 'end' ]
        then
            #copy back a conifgured config
            cp ${CONFIGDIR}/${CONFIGFILE}.configured ${CONFIGDIR}/${CONFIGFILE}

            #unstage config file from repo
            git rm -r --cached ${CONFIGDIR}/${CONFIGFILE}
        fi
    done
}

#copy blank configs in place of configured
#then stage blank conifg
copyConfigs begin

#prompt user
echo "Would you like to 'commit', 'push', 'checkout', 'merge', or 'ammend'?";
read COMMAND;

#route based on input
case "$COMMAND" in
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
    ammend) git commit --amend
        ;;
    *) echo "Illegal option: $COMMAND"
        ;;
esac

#copy the conifgured config back
copyConfigs end

