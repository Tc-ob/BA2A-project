#!/bin/bash

# Default commit message is "Auto commit" with the current date/time
# You can override this by running: ./push.sh "Your custom message"
COMMIT_MSG=${1:-"Auto commit - $(date +'%Y-%m-%d %H:%M:%S')"}

echo "📦 Staging changes..."
git add .

echo "📝 Committing with message: '$COMMIT_MSG'..."
# Only commit if there are changes to commit
if git diff-index --quiet HEAD --; then
    echo "No changes to commit."
else
    git commit -m "$COMMIT_MSG"
fi

echo "🚀 Pushing to master branch..."
git push origin master

echo "✅ Done!"
