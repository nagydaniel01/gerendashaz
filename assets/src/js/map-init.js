import { Loader } from '@googlemaps/js-api-loader';
import MarkerClusterer from '@googlemaps/markerclustererplus';

let map;
let markers = [];
let originalColumns = []; // Original column divs for re-adding
let markerMap = [];       // Map marker index to marker object
let snazzyStyle = localize.snazzystyle ? JSON.parse(localize.snazzystyle) : null;

// Inline SVG for custom marker
const customMarkerIcon =
  'data:image/svg+xml;base64,' +
  window.btoa(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="#FF6D10" d="M192 0C85.969 0 0 85.969 0 192.001C0 269.408 26.969 291.033 172.281 501.676C181.813 515.441 202.188 515.441 211.719 501.676C357.031 291.033 384 269.408 384 192.001C384 85.969 298.031 0 192 0ZM192 271.998C147.875 271.998 112 236.123 112 191.998S147.875 111.997 192 111.997S272 147.872 272 191.998S236.125 271.998 192 271.998Z"/></svg>
  `);

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('map-list');
  const mapElement = document.getElementById('map');

  if (!container || !mapElement) {
    console.warn('Warning: #map-list or #map element not found in the DOM.');
    return;
  }

  // Save original columns
  const columns = container.querySelectorAll('.card__wrapper');
  columns.forEach(col => originalColumns.push(col.cloneNode(true)));

  // Load Google Maps
  const loader = new Loader({
    apiKey: localize.googlemapsapikey,
    version: 'weekly',
    libraries: ['places']
  });

  loader.load().then(() => initMap(container, mapElement));
});

function initMap(container, mapElement) {
  map = new google.maps.Map(mapElement, {
    zoom: 8,
    center: { lat: 0, lng: 0 },
    styles: snazzyStyle
  });

  const bounds = new google.maps.LatLngBounds();

  // Extend bounds to include all markers
  originalColumns.forEach(col => {
    const card = col.querySelector('.card');
    const lat = parseFloat(card?.getAttribute('data-lat'));
    const lng = parseFloat(card?.getAttribute('data-lng'));
    if (!isNaN(lat) && !isNaN(lng)) bounds.extend({ lat, lng });
  });

  if (!bounds.isEmpty()) map.fitBounds(bounds);

  addLocationMarkers(originalColumns, container);
  addMarkerCluster(markers, map);

  // Listen to map bounds changes and update card visibility
  map.addListener('bounds_changed', debounce(() => updateCardVisibility(container), 100));
  updateCardVisibility(container);
}

function addLocationMarkers(columns, container) {
  const infowindow = new google.maps.InfoWindow();

  columns.forEach((col, index) => {
    const card = col.querySelector('.card');
    if (!card) return;

    const lat = parseFloat(card.getAttribute('data-lat'));
    const lng = parseFloat(card.getAttribute('data-lng'));
    const title = card.getAttribute('data-title') || '';
    const address = card.getAttribute('data-address') || '';
    const link = card.getAttribute('data-link') || '';
    const content = card.getAttribute('data-content') || '';
    const image = card.getAttribute('data-image') || '';

    if (isNaN(lat) || isNaN(lng)) return;

    const marker = new google.maps.Marker({
      position: { lat, lng },
      map,
      icon: {
        url: customMarkerIcon,
        scaledSize: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(20, 40)
      }
    });

    /*
    const infoContent = `
      <div class="card card--map-infowindow">
        <div class="card__inner">
          ${image ? `<div class="card__header"><div class="card__image-wrapper"><img src="${image}" alt="${title}" class="card__image" /></div></div>` : ''}
          <div class="card__content">
          ${
            link
              ? `<a href="${link}"><strong class="card__title">${title}</strong></a>`
              : `<strong class="card__title">${title}</strong>`
          }
            <p>${address}</p>
            ${content ? `<div class="card__lead">${content}</div>` : ''}
          </div>
        </div>
      </div>
    `;
    */

    const infoContent = `
      <div class="infowindow">
        <div class="infowindow__inner">
          <div class="infowindow__content">
            <strong class="infowindow__title">${title}</strong>
            <p>${address}</p>
          </div>
        </div>
      </div>
    `;

    marker.addListener('click', () => {
      infowindow.setContent(infoContent);
      infowindow.open(map, marker);
    });

    markerMap[index] = marker;

    const cardId = `map-card-${index}`;
    const cardElement = col.querySelector(`#${cardId}`);
    if (cardElement) {
      cardElement.addEventListener('click', () => {
        map.setCenter(marker.getPosition());
        map.setZoom(14);
        google.maps.event.trigger(marker, 'click');
      });
    }

    markers.push(marker);
  });
}

function addMarkerCluster(markers, map) {
  if (!markers.length) return;

  function createClusterIcon(count) {
    const svg = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40">
        <circle cx="20" cy="20" r="18" fill="#F5F5F5" stroke="#FF6D10" stroke-width="2"/>
        <text x="50%" y="50%" text-anchor="middle" dominant-baseline="central"
              font-size="16" fill="#FF6D10" font-family="Arial, sans-serif">
          ${count}
        </text>
      </svg>
    `;
    return 'data:image/svg+xml;base64,' + window.btoa(svg);
  }

  new MarkerClusterer(map, markers, {
    styles: [{ url: '', width: 40, height: 40 }],
    clusterClass: 'custom-clustericon',
    calculator: function(markers, numStyles) {
      const count = markers.length;
      return { text: '', index: 1, title: `Cluster of ${count}`, url: createClusterIcon(count) };
    }
  });
}

function updateCardVisibility(container) {
  const bounds = map.getBounds();
  if (!bounds) return;

  container.innerHTML = '';

  markers.forEach((marker, index) => {
    if (bounds.contains(marker.getPosition())) {
      const colClone = originalColumns[index].cloneNode(true);
      container.appendChild(colClone);

      const cardElement = colClone.querySelector(`#map-card-${index}`);
      if (cardElement) {
        cardElement.addEventListener('click', () => {
          map.setCenter(marker.getPosition());
          map.setZoom(14);
          google.maps.event.trigger(marker, 'click');
        });
      }
    }
  });
}

function debounce(fn, delay) {
  let timer;
  return function () {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, arguments), delay);
  };
}
