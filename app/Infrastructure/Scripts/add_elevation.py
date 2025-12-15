import sys
import json
import rasterio
from rasterio.warp import transform

def add_elevation(coordinates, tif_path):
    """
    Reads coordinates (lon, lat) and adds elevation from the TIFF file.
    Returns a list of [lon, lat, elevation].
    """
    try:
        with rasterio.open(tif_path) as src:
            # Prepare input coordinates
            lons = [float(c[0]) for c in coordinates]
            lats = [float(c[1]) for c in coordinates]
            
            # Destination CRS is the dataset's CRS
            dst_crs = src.crs
            # Source CRS is WGS84 (Lat/Lon)
            src_crs = 'EPSG:4326'
            
            # Transform coordinates if CRSs are different
            if dst_crs != src_crs:
                # transform expects list of x and list of y
                # Returns tuple of (xs, ys)
                xs, ys = transform(src_crs, dst_crs, lons, lats)
            else:
                xs, ys = lons, lats
            
            # Prepare for sampling
            coords_tuples = list(zip(xs, ys))
            
            # sample returns a generator of numpy arrays
            elevations = src.sample(coords_tuples)
            
            results = []
            for i, val in enumerate(elevations):
                # val is an array of band values
                ele = float(val[0])
                
                # Handle NoData (often very small negative number or specific value)
                # The gdalinfo showed NoData Value=-1e+10
                if ele < -1000: 
                    ele = 0 # Fallback for no data
                
                original = coordinates[i]
                results.append([original[0], original[1], round(ele, 2)])
                
            return results
    except Exception as e:
        sys.stderr.write(f"Error: {str(e)}")
        # Return original with 0 elevation in case of failure
        return [[c[0], c[1], 0] for c in coordinates]

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python add_elevation.py <json_coordinates> <tif_path>")
        sys.exit(1)

    try:
        # Load input JSON
        input_data = sys.argv[1]
        coordinates = json.loads(input_data)
        tif_path = sys.argv[2]
        
        enriched_coords = add_elevation(coordinates, tif_path)
        
        # Output JSON
        print(json.dumps(enriched_coords))
        
    except json.JSONDecodeError:
        sys.stderr.write("Error: Invalid JSON input")
        sys.exit(1)
    except Exception as e:
        sys.stderr.write(f"Unexpected error: {str(e)}")
        sys.exit(1)