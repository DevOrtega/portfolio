#!/bin/bash
set -e

# Configuration
DATA_DIR="./docker/osrm"
MAP_FILE="gran-canaria.osm.pbf"
OSRM_FILE="gran-canaria.osrm"

echo "=== OSRM Setup ==="

# Ensure Docker is available
if ! command -v docker &> /dev/null; then
    echo "Error: 'docker' command not found."
    exit 1
fi

# 1. Download Map Data (if not present)
if [ -f "$DATA_DIR/$MAP_FILE" ] && [ $(stat -c%s "$DATA_DIR/$MAP_FILE") -gt 1000000 ]; then
    echo "Map file $MAP_FILE already exists and looks valid, skipping download."
else
    echo "Downloading map data from BBBike (Gran Canaria)..."
    rm -f "$DATA_DIR/$MAP_FILE"
    curl -L -f -o "$DATA_DIR/$MAP_FILE" "https://download.bbbike.org/osm/bbbike/GranCanaria/GranCanaria.osm.pbf"
fi

# Final verification
FILE_SIZE=$(stat -c%s "$DATA_DIR/$MAP_FILE")
if [ "$FILE_SIZE" -lt 1000000 ]; then
    echo "Error: Map file is too small ($FILE_SIZE bytes). Likely corrupt."
    exit 1
fi

echo "Map ready ($FILE_SIZE bytes)."

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
