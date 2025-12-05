/**
 * Configuration for the Guaguas Tracker demo
 */

/**
 * Main lines shown by default when the map loads
 * Other lines are hidden until user selects them
 */
export const MAIN_LINES = {
  municipales: ['1', '12', '17', '25', '26'],
  global: ['1', '5', '30', '60', '91'],
  night: ['L1', 'L2', 'L3']
};

/**
 * Available bus lines grouped by company
 */
export const BUS_LINES = {
  municipales: [
    '1', '2', '6', '7', '8', '9', '10', '11', '12', '13', 
    '17', '18', '19', '20', '21', '22', '24', '25', '26', 
    '33', '35', '41', '44', '45', '47', '48', '49', '50', 
    '51', '52', '53', '54', '55', '70', '80', '81', '82', '84', '91'
  ],
  global: [
    '1', '5', '10', '11', '12', '15', '18', '21', '25', '30', 
    '32', '38', '41', '45', '50', '55', '60', '66', '80', '90', 
    '91', '105', '204', '205', '206', '210', '211', '213', '214',
    '215', '216', '218', '220', '222', '223', '224', '229', '232',
    '233', '234', '250', '251', '255', '256', '301', '302', '303',
    '305', '306', '307', '308', '311', '313', '316', '317', '318',
    '319', '320', '321', '322', '323', '324', '325', '326', '327',
    '328', '329', '331', '332', '333', '335', '336', '350', '401',
    '402', '403', '404'
  ],
  night: ['L1', 'L2', 'L3', '64', '65']
};

/**
 * Company colors for visual representation
 */
export const COMPANY_COLORS = {
  municipales: {
    primary: '#FDB913',
    secondary: '#D49400',
    text: '#333'
  },
  global: {
    primary: '#0066CC',
    secondary: '#004C99',
    text: '#FFF'
  },
  night: {
    primary: '#9933FF',
    secondary: '#7722CC',
    text: '#FFF'
  }
};

/**
 * Default map configuration
 */
export const MAP_CONFIG = {
  center: [28.050, -15.450],
  bounds: {
    north: 28.18,
    south: 27.74,
    east: -15.35,
    west: -15.60
  },
  maxBounds: [[27.70, -15.90], [28.20, -15.30]],
  minZoom: 9.5,
  maxBoundsViscosity: 0.8
};

/**
 * Simulation configuration
 */
export const SIMULATION_CONFIG = {
  updateInterval: 5000, // ms
  busesPerRoute: {
    min: 1,
    max: 3
  },
  speed: {
    urban: 0.025,
    interurban: 0.035
  },
  delayProbability: 0.15
};

export default {
  MAIN_LINES,
  BUS_LINES,
  COMPANY_COLORS,
  MAP_CONFIG,
  SIMULATION_CONFIG
};
