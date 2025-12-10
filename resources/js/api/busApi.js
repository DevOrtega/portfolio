/**
 * @file busApi.js
 * @description API client for bus data
 * 
 * Provides methods to fetch bus-related data from the backend API.
 * All methods return Promises and handle errors appropriately.
 */

import axios from 'axios';

const API_BASE = '/api/bus';

/**
 * Create an axios instance with default configuration
 */
const apiClient = axios.create({
  baseURL: API_BASE,
  timeout: 10000,
  headers: {
    'Accept': 'application/json',
  }
});

/**
 * Handle API errors consistently
 * @param {Error} error - The error object
 * @throws {Error} Re-throws with a user-friendly message
 */
const handleError = (error) => {
  console.error('API Error Details:', {
    message: error.message,
    status: error.response?.status,
    data: error.response?.data,
    url: error.config?.url,
    baseURL: error.config?.baseURL
  });

  if (error.response) {
    // Server responded with error status
    throw new Error(error.response.data?.message || `Error ${error.response.status}`);
  } else if (error.request) {
    // Request made but no response received
    throw new Error('Network error: Unable to reach the server. Please check if the backend is running and accessible.');
  } else {
    // Error setting up the request
    throw new Error(error.message || 'An unexpected error occurred');
  }
};

/**
 * Fetch all bus data from the API
 * @returns {Promise<Object>} Complete bus data
 */
export const fetchBusData = async () => {
  try {
    const response = await apiClient.get('/data');
    return response.data;
  } catch (error) {
    handleError(error);
    throw error; // Re-throw for caller handling
  }
};

/**
 * Fetch bus routes from the API
 * @returns {Promise<Array>} Array of routes
 */
export const fetchBusRoutes = async () => {
  try {
    const response = await apiClient.get('/routes');
    return response.data.routes;
  } catch (error) {
    handleError(error);
    throw error; // Re-throw for caller handling
  }
};

/**
 * Fetch bus stops from the API
 * @returns {Promise<Object>} Map of stops
 */
export const fetchBusStops = async () => {
  try {
    const response = await apiClient.get('/stops');
    return response.data.stops;
  } catch (error) {
    handleError(error);
    throw error; // Re-throw for caller handling
  }
};

/**
 * Fetch bus companies from the API
 * @returns {Promise<Object>} Company colors
 */
export const fetchBusCompanies = async () => {
  try {
    const response = await apiClient.get('/companies');
    return response.data.companies;
  } catch (error) {
    handleError(error);
    throw error; // Re-throw for caller handling
  }
};

export default {
  fetchBusData,
  fetchBusRoutes,
  fetchBusStops,
  fetchBusCompanies,
};
