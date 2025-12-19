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

# 1. Check for Map Data
echo "Checking for map data in $DATA_DIR..."
ls -lh "$DATA_DIR" || mkdir -p "$DATA_DIR"

if [ -f "$DATA_DIR/$MAP_FILE" ] && [ $(stat -c%s "$DATA_DIR/$MAP_FILE") -gt 1000000 ]; then
    echo "✅ Map file $MAP_FILE found and looks valid ($(stat -c%s "$DATA_DIR/$MAP_FILE") bytes)."
else
    echo "⚠️ Map file missing or too small. Attempting emergency download from OSM France..."
    rm -f "$DATA_DIR/$MAP_FILE"
    # URL verificada tras inspección del servidor
    curl -L -f -A "Mozilla/5.0" -o "$DATA_DIR/$MAP_FILE" "https://download.openstreetmap.fr/extracts/africa/spain/canarias-latest.osm.pbf" || true
fi

# Final verification
if [ ! -f "$DATA_DIR/$MAP_FILE" ]; then
    echo "❌ Error: Map file $MAP_FILE not found."
    exit 1
fi

FILE_SIZE=$(stat -c%s "$DATA_DIR/$MAP_FILE")
if [ "$FILE_SIZE" -lt 1000000 ]; then
    echo "❌ Error: Map file is too small ($FILE_SIZE bytes). The server cannot reach the map provider."
    echo "Check server connectivity or ensure the CI runner is sending the file correctly."
    exit 1
fi

echo "🚀 Map ready for processing ($FILE_SIZE bytes)."

# 2. Extract (using standard foot profile)
echo "Running osrm-extract (foot profile)..."
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-extract -p /opt/foot.lua /data/$MAP_FILE

# 3. Partition (MLD)
echo "Running osrm-partition (foot)..."
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-partition /data/$OSRM_FILE

# 4. Customize (MLD)
echo "Running osrm-customize (foot)..."
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-customize /data/$OSRM_FILE

# 5. Extract (using car profile) for Bus Demo
OSRM_CAR_FILE="gran-canaria-car.osrm"
echo "Running osrm-extract (car profile)..."
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-extract -p /opt/car.lua /data/$MAP_FILE

echo "Renaming car profile output to avoid conflict..."
# osrm-extract creates output based on input filename. We need to rename the generated files
# before the next extract overwrote them? No, osrm-extract output name matches input name sans extension?
# Actually osrm-extract creates <input>.osrm.*
# If we run it again on same input, it overwrites.
# So we must rename the FOOT files first, or rename the input for CAR?
# Renaming input is safer.

cp "$DATA_DIR/$MAP_FILE" "$DATA_DIR/gran-canaria-car.osm.pbf"

docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-extract -p /opt/car.lua /data/gran-canaria-car.osm.pbf

# 6. Partition (MLD) Car
echo "Running osrm-partition (car)..."
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-partition /data/$OSRM_CAR_FILE

# 7. Customize (MLD) Car
echo "Running osrm-customize (car)..."
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-customize /data/$OSRM_CAR_FILE

echo "=== OSRM Setup Complete ==="
# Cleanup temp file
rm "$DATA_DIR/gran-canaria-car.osm.pbf"

echo "=== OSRM Setup Complete ==="
echo "Updating docker-compose.yml to use the new file name..."
# We need to ensure docker-compose uses gran-canaria.osrm
sed -i 's|canary-islands-latest.osrm|gran-canaria.osrm|g' docker-compose.yml

echo "You can now run 'docker compose up -d osrm'"
