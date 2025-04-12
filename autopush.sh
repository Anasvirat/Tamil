#!/data/data/com.termux/files/usr/bin/bash

while true; do
    git add .

    # Only commit if there are changes
    if ! git diff --cached --quiet; then
        git commit -m "Auto update: $(date +'%Y-%m-%d %H:%M:%S')"
        git push origin main
    else
        echo "No changes to commit."
    fi

    sleep 1
done
