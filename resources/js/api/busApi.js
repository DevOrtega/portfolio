/**
 * @file busApi.js
 * @description API client for bus data
 */

import axios from 'axios';

const API_BASE = '/api/bus';

/**
 * Fetch all bus data from the API
 * @returns {Promise<Object>} Complete bus data
 */
export const fetchBusData = async () => {
  const response = await axios.get(`${API_BASE}/data`);
  return response.data;
};

/**
 * Fetch bus routes from the API
 * @returns {Promise<Array>} Array of routes
 */
export const fetchBusRoutes = async () => {
  const response = await axios.get(`${API_BASE}/routes`);
  return response.data.routes;
};

/**
 * Fetch bus stops from the API
 * @returns {Promise<Object>} Map of stops
 */
export const fetchBusStops = async () => {
  const response = await axios.get(`${API_BASE}/stops`);
  return response.data.stops;
};

/**
 * Fetch bus companies from the API
 * @returns {Promise<Object>} Company colors
 */
export const fetchBusCompanies = async () => {
  const response = await axios.get(`${API_BASE}/companies`);
  return response.data.companies;
};

export default {
  fetchBusData,
  fetchBusRoutes,
  fetchBusStops,
  fetchBusCompanies,
};
