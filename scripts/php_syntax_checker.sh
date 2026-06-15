#!/bin/bash

echo "Checking changed PHP files..."


# Detect changed PHP files only
FILES=$(git diff --name-only --diff-filter=ACM origin/main...HEAD | grep '\.php$')

if [ -z "$FILES" ]; then
  echo "No changed PHP files found."
  exit 0
fi

FAILED=0

while IFS= read -r file
do

  # Skip deleted/non-existing files
  if [ ! -f "$file" ]; then
    continue
  fi

  echo "Checking: $file"

  php -l "$file" > /dev/null 2>&1

  if [ $? -ne 0 ]; then

    echo "❌ Syntax error found in $file"

    php -l "$file"

    FAILED=1

  fi

done <<< "$FILES"

if [ "$FAILED" -eq 1 ]; then
  echo "❌ PHP syntax validation failed."
  exit 1
fi

echo "✅ PHP syntax validation passed."