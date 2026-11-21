#!/bin/sh
set -eu

if [ "$#" -ne 1 ]; then
    echo "Aufruf: sh deploy/plesk.sh <WordPress-Verzeichnis>" >&2
    exit 1
fi

release_dir=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
theme_source="$release_dir/wp-content/themes/hochschulwatch"
wordpress_dir=$1
theme_target="$wordpress_dir/wp-content/themes/hochschulwatch"

if [ ! -f "$theme_source/style.css" ] || [ ! -d "$theme_source/acf-fields" ]; then
    echo "Das Child-Theme fehlt in der Git-Bereitstellung." >&2
    exit 1
fi

if [ ! -f "$wordpress_dir/wp-includes/version.php" ] ||
   [ ! -d "$wordpress_dir/wp-content/themes" ] ||
   [ -L "$theme_target" ]; then
    echo "Ungültiges WordPress-Verzeichnis oder symbolischer Link am Theme-Ziel." >&2
    exit 1
fi

mkdir -p -- "$theme_target"
rsync -rt --checksum --delay-updates --delete-delay -- "$theme_source/" "$theme_target/"
