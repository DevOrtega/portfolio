import { ref } from 'vue';
import L from 'leaflet';

/**
 * Composable for bus tracking map functionality
 * Provides utilities for bus icons, bounds checking, and responsive zoom
 */
export function useBusMap() {
  /**
   * Geographic bounds of Gran Canaria
   * Adjusted to keep buses within the island
   */
  const BOUNDS = {
    north: 28.18,   // Norte de Las Palmas
    south: 27.74,   // Sur de Maspalomas
    east: -15.35,   // Este (Telde/Jinámar)
    west: -15.60    // Oeste (hasta Maspalomas/Puerto Rico, evitando el océano)
  };

  /**
   * Map options configuration
   */
  const mapOptions = {
    zoomControl: true,
    attributionControl: true,
    maxBounds: [[27.70, -15.90], [28.20, -15.30]],
    maxBoundsViscosity: 0.8,
    minZoom: 9.5
  };

  /**
   * Calculate responsive zoom level based on window width
   * @returns {number} Zoom level
   */
  const getResponsiveZoom = () => {
    const width = window?.innerWidth ?? 1920;
    if (width < 640) return 9.5;    // mobile
    if (width < 768) return 9.75;   // small tablet
    if (width < 1024) return 10;    // tablet
    if (width < 1280) return 10.25; // laptop
    return 10.5;                     // large desktop
  };

  /**
   * Check if coordinates are within Gran Canaria bounds
   * @param {number} lat - Latitude
   * @param {number} lng - Longitude
   * @returns {boolean}
   */
  const isWithinBounds = (lat, lng) => {
    if (lat < BOUNDS.south || lat > BOUNDS.north) return false;
    if (lng < BOUNDS.west || lng > BOUNDS.east) return false;
    return true;
  };

  /**
   * Create custom bus icon with direction indicator
   * @param {Object} bus - Bus object with company, line, angle and tripDirection properties
   * @returns {L.DivIcon} Leaflet div icon
   */
  const createBusIcon = (bus) => {
    const colors = {
      municipales: { fill: '#FDB913', stroke: '#D49400', text: '#333' },
      global: { fill: '#0066CC', stroke: '#004C99', text: '#FFF' },
      default: { fill: '#9933FF', stroke: '#7722CC', text: '#FFF' }
    };

    // Usar color morado para nocturnas
    const color = bus.type === 'night' ? colors.default : (colors[bus.company] ?? colors.default);
    
    // Indicador de dirección (ida = →, vuelta = ←)
    const directionArrow = bus.tripDirection === 'outbound' ? '→' : '←';
    const arrowColor = bus.tripDirection === 'outbound' ? '#22C55E' : '#F97316'; // Verde ida, naranja vuelta

    const svgIcon = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40" height="40">
        <g>
          <!-- Cuerpo del bus -->
          <rect x="4" y="10" width="28" height="18" rx="3" fill="${color.fill}" stroke="${color.stroke}" stroke-width="1.5"/>
          <!-- Ventanas -->
          <rect x="7" y="13" width="5" height="5" rx="1" fill="#87CEEB" opacity="0.8"/>
          <rect x="14" y="13" width="5" height="5" rx="1" fill="#87CEEB" opacity="0.8"/>
          <rect x="21" y="13" width="5" height="5" rx="1" fill="#87CEEB" opacity="0.8"/>
          <!-- Ruedas -->
          <circle cx="11" cy="28" r="3" fill="#2C2C2C"/>
          <circle cx="11" cy="28" r="1.5" fill="#666"/>
          <circle cx="25" cy="28" r="3" fill="#2C2C2C"/>
          <circle cx="25" cy="28" r="1.5" fill="#666"/>
          <!-- Número de línea -->
          <text x="18" y="24" font-family="Arial, sans-serif" font-size="7" font-weight="bold" fill="${color.text}" text-anchor="middle">${bus.line}</text>
          <!-- Indicador de dirección -->
          <circle cx="34" cy="8" r="5" fill="${arrowColor}" stroke="white" stroke-width="1"/>
          <text x="34" y="11" font-family="Arial, sans-serif" font-size="7" font-weight="bold" fill="white" text-anchor="middle">${directionArrow}</text>
        </g>
      </svg>
    `;

    return L.divIcon({
      html: svgIcon,
      className: 'custom-bus-icon',
      iconSize: [40, 40],
      iconAnchor: [20, 20]
    });
  };

  /**
   * Get company label in Spanish
   * @param {string} company - Company code
   * @returns {string} Company label
   */
  const getCompanyLabel = (company) => {
    const labels = {
      municipales: 'Guaguas Municipales',
      global: 'Global',
      night: 'Línea Nocturna'
    };
    return labels[company] || company;
  };

  /**
   * Get bus type label in Spanish
   * @param {string} type - Bus type
   * @returns {string} Type label
   */
  const getBusTypeLabel = (type) => {
    const types = {
      urban: 'Urbana',
      interurban: 'Interurbana',
      night: 'Nocturna'
    };
    return types[type] || type;
  };

  return {
    BOUNDS,
    mapOptions,
    getResponsiveZoom,
    isWithinBounds,
    createBusIcon,
    getCompanyLabel,
    getBusTypeLabel
  };
}
