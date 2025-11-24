import { ref } from 'vue';
import L from 'leaflet';

/**
 * Composable for bus tracking map functionality
 * Provides utilities for bus icons, bounds checking, and responsive zoom
 */
export function useBusMap() {
  /**
   * Geographic bounds of Gran Canaria
   */
  const BOUNDS = {
    north: 28.18,   // Las Palmas (North)
    south: 27.75,   // Maspalomas/Mogán (South)
    east: -15.35,   // Telde (East)
    west: -15.85    // Agaete/La Aldea (West)
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
    if (typeof window === 'undefined') return 10.25;
    const width = window.innerWidth;
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
    return lat >= BOUNDS.south && 
           lat <= BOUNDS.north &&
           lng >= BOUNDS.west && 
           lng <= BOUNDS.east;
  };

  /**
   * Create custom bus icon
   * @param {Object} bus - Bus object with company and line properties
   * @returns {L.DivIcon} Leaflet div icon
   */
  const createBusIcon = (bus) => {
    const colors = {
      municipales: { fill: '#FDB913', stroke: '#D49400', text: '#333' },
      global: { fill: '#0066CC', stroke: '#004C99', text: '#FFF' },
      default: { fill: '#9933FF', stroke: '#7722CC', text: '#FFF' }
    };

    const color = colors[bus.company] || colors.default;

    const svgIcon = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32">
        <g>
          <rect x="4" y="8" width="24" height="16" rx="2" fill="${color.fill}" stroke="${color.stroke}" stroke-width="1.2"/>
          <rect x="6" y="10" width="5" height="6" rx="0.8" fill="#87CEEB" opacity="0.7"/>
          <rect x="13" y="10" width="5" height="6" rx="0.8" fill="#87CEEB" opacity="0.7"/>
          <rect x="20" y="10" width="5" height="6" rx="0.8" fill="#87CEEB" opacity="0.7"/>
          <circle cx="10" cy="24" r="2.5" fill="#2C2C2C"/>
          <circle cx="22" cy="24" r="2.5" fill="#2C2C2C"/>
          <text x="16" y="22" font-family="Arial, sans-serif" font-size="6" font-weight="bold" fill="${color.text}" text-anchor="middle">${bus.line}</text>
        </g>
      </svg>
    `;

    return L.divIcon({
      html: svgIcon,
      className: 'custom-bus-icon',
      iconSize: [32, 32],
      iconAnchor: [16, 16]
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
