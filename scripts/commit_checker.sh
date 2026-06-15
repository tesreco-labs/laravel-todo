#!/bin/bash

commit_message="$1"

# Skip merge commits
if [[ $commit_message == Merge* ]]; then
  echo "Skipping merge commit"
  exit 0
fi

allowed_prefixes="^(feat|fix|docs|chore|test|refactor):"

if [[ $commit_message =~ $allowed_prefixes ]]; then
  echo "Valid commit message"
  exit 0
else
  echo "Invalid commit message: $commit_message"
  echo "Allowed prefixes:"
  echo "  feat:"
  echo "  fix:"
  echo "  docs:"
  echo "  chore:"
  echo "  test:"
  echo "  refactor:"
  exit 1
fi