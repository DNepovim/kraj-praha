#!/bin/sh

mango build
rsync -ruv -e 'ssh -p[PORT_NUMBER]' public/assets/ [USERNAME]@[HOST]:[PATH_TO_WEB]/public/assets
git push deploy/prod --force-with-lease


# create bare git repository on your server and add following code to post-receive hook
# (require git and composer)
##!/bin/sh
# WEB_DIRECTORY='[PATH_TO_WEB]'
# GIT_DIRECTORY='[PATH_TO_GIT_REPO]'
# git --work-tree=$WEB_DIRECTORY --git-dir=$GIT_DIRECTORY checkout -f
#
# cd $WEB_DIRECTORY
#
# composer install
#
# rm -rfv temp/cache
#
