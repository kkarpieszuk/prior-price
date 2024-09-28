#!/usr/bin/env bash

set -e

function replace_version_number() {

  # Function to traverse directories and replace {VERSION} with the real version number
  traverse_and_replace() {
    for file in "$1"/*; do
      if [ -d "$file" ]; then
        # Skip /vendor and /node_modules directories
        if [[ "$file" == */vendor ]] || [[ "$file" == */node_modules ]]; then
          continue
        fi
        # Recursively traverse directories
        traverse_and_replace "$file"
      elif [ -f "$file" ]; then
        # Replace {VERSION} with the real version number in files
        sed -i "s/{VERSION}/$VERSION/g" "$file"
      fi
    done
  }

  # Start traversal from the current working directory
  traverse_and_replace "."

  echo "Replaced {VERSION} with $VERSION in all files."
}

# take the branch name from the command line argument. If not specified, default to develop.
BRANCH=${1:-develop}

# Prompt to confirm the branch name before continuing.
read -r -p "$(echo -e '\e[1;31mBuild will be made from branch: '"$BRANCH"'. Continue? \e[0m [y/N]')" response
case "$response" in
    [yY][eE][sS]|[yY])
        echo "Continuing..."
        ;;
    *)
        exit 1
        ;;
esac

git stash
git checkout $BRANCH
git pull

# Get the plugin version from the readme.txt file.
VERSION=$(grep -oP '(?<=Stable tag: ).*' readme.txt)

# Prompt to confirm the version number before continuing.
read -r -p "$(echo -e '\e[1;31mPlugin version: '"$VERSION"'. Continue? \e[0m [y/N]')" response
case "$response" in
    [yY][eE][sS]|[yY])
        echo "Continuing..."
        ;;
    *)
        exit 1
        ;;
esac

# make build directory and copy all files to it.
rm -rf build
mkdir build
mkdir build/gitversion
rsync -av --exclude='build' . build/gitversion/

# Go to the build directory.
cd build/gitversion

# Run composer install.
rm -rf vendor
composer install --no-dev

# make pot file.
wp i18n make-pot . languages/wc-price-history.pot

# Remove not needed files.
rm -rf .git .github .husky .scripts node_modules \
 tests .gitignore .phpunit.result.cache composer.* \
 package-lock.json package.json phpunit.xml \
 phpstan.neon phpstan.neon.dist phpstan-custom-rules \
 README.md screenshot-1.png

replace_version_number


# create a zip file.
zip -r ../wc-price-history.zip .

cd ..

# Checkout svn repository
svn checkout https://plugins.svn.wordpress.org/wc-price-history/ svn-checkout

# Copy files to svn repository
cp -r gitversion/* svn-checkout/trunk/

cd svn-checkout

svn status

# for each file in svn status marked with ? add it to svn
for file in $(svn status | grep '?' | awk '{print $2}'); do
  echo "Adding $file to svn"
  svn add $file
done

svn status

# wait for user input
read -r -p "$(echo -e '\e[1;31mCommit changes to WordPress.org? \e[0m [y/N]')" response
case "$response" in
    [yY][eE][sS]|[yY])
        echo "Continuing..."
        ;;
    *)
        exit 1
        ;;
esac

svn ci -m "Pushing $VERSION to the trunk"

svn cp trunk tags/$VERSION

svn ci -m "Tagging and releasing $VERSION"

