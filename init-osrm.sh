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

# Cleanup old OSRM files to ensure clean generation
echo "Cleaning up old OSRM data..."
rm -f "$DATA_DIR"/*.osrm*

# 2. FOOT Profile Generation (Hiking)
echo "--- Generating FOOT profile ---"
cp "$DATA_DIR/$MAP_FILE" "$DATA_DIR/temp_foot.osm.pbf"
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-extract -p /opt/foot.lua /data/temp_foot.osm.pbf

echo "Finalizing FOOT profile files..."
for f in "$DATA_DIR"/temp_foot.osrm*; do
    mv "$f" "${f//temp_foot/gran-canaria-foot}"
done

docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-partition /data/gran-canaria-foot.osrm
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-customize /data/gran-canaria-foot.osrm

rm -f "$DATA_DIR/temp_foot.osm.pbf"

# 3. CAR Profile Generation (Bus)
echo "--- Generating CAR profile ---"
# We use a different filename to avoid any overlap during processing
cp "$DATA_DIR/$MAP_FILE" "$DATA_DIR/temp_car.osm.pbf"
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-extract -p /opt/car.lua /data/temp_car.osm.pbf

# Rename files from temp_car.osrm.* to gran-canaria-car.osrm.*
echo "Finalizing CAR profile files..."
for f in "$DATA_DIR"/temp_car.osrm*; do
    mv "$f" "${f//temp_car/gran-canaria-car}"
done

docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-partition /data/gran-canaria-car.osrm
docker run -t --rm -v "${PWD}/docker/osrm:/data" osrm/osrm-backend osrm-customize /data/gran-canaria-car.osrm

# Cleanup
rm -f "$DATA_DIR/temp_car.osm.pbf"

echo "=== OSRM Setup Complete ==="
echo "You can now run 'docker compose up -d osrm'"
