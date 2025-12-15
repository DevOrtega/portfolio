#!/bin/bash
set -e

# Configuration
DATA_DIR="./docker/osrm"
# Using Overpass API to get exact GC bounding box (returns XML .osm)
# BBox: left,bottom,right,top
BBOX="-15.9,27.7,-15.3,28.3"
MAP_URL="https://overpass-api.de/api/map?bbox=$BBOX"
MAP_FILE="gran-canaria.osm" # XML format
OSRM_FILE="gran-canaria.osrm"

echo "=== OSRM Setup ==="

# Ensure Docker is available
if ! command -v docker &> /dev/null; then
    echo "Error: 'docker' command not found."
    exit 1
fi

# 1. Download Map Data
echo "Downloading map data from Overpass API (Gran Canaria area)..."
rm -f "$DATA_DIR/$MAP_FILE"
rm -f "$DATA_DIR/canary-islands-latest.osm.pbf" # Clean up old attempts

curl -L -f -o "$DATA_DIR/$MAP_FILE" "$MAP_URL"

# Verify file size (should be > 5MB for GC)
FILE_SIZE=$(stat -c%s "$DATA_DIR/$MAP_FILE")
if [ "$FILE_SIZE" -lt 100000 ]; then
    echo "Error: File is too small ($FILE_SIZE bytes). Likely an error."
    head -n 5 "$DATA_DIR/$MAP_FILE"
    exit 1
fi

echo "Map downloaded successfully ($FILE_SIZE bytes)."

# 2. Extract (using standard foot profile)
echo "Running osrm-extract (foot profile)..."
# Note: Input file is now .osm (XML), OSRM handles it
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-extract -p /opt/foot.lua /data/$MAP_FILE

# 3. Partition (MLD)
echo "Running osrm-partition..."
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-partition /data/$OSRM_FILE

# 4. Customize (MLD)
echo "Running osrm-customize..."
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-customize /data/$OSRM_FILE

echo "=== OSRM Setup Complete ==="
echo "Updating docker-compose.yml to use the new file name..."
# We need to ensure docker-compose uses gran-canaria.osrm
sed -i 's|canary-islands-latest.osrm|gran-canaria.osrm|g' docker-compose.yml

echo "You can now run 'docker compose up -d osrm'"
