let price = document.querySelector('.price');
let houseType = document.querySelector('.housetype');
const PATHS = {
    house: '/sajilo-rent/user-panel/back_end/',
    student: '/sajilo-rent/studentsection/backend/',
    defaultProfile: '/sajilo-rent/resources/profile-related/default-profile.png'
};

class Map {
    constructor(center, zoom, tileLayer, attribution) {
        this.center = center;
        this.zoom = zoom;
        this.tileLayer = tileLayer;
        this.attribution = attribution;
    }

    createMap() {
        const mapInstance = L.map('map', {
            center: this.center,
            zoom: this.zoom
        });

        L.tileLayer(this.tileLayer, {
            attribution: this.attribution
        }).addTo(mapInstance);

        return mapInstance;
    }
}

class RoutingControl {
    constructor(mapInstance, markerMaker, center) {
        this.mapInstance = mapInstance;
        this.markerMaker = markerMaker;
        this.center = center;
        this.currentRoutingControl = null;
        this.removedMarkers = null;
        this.lat = null;
        this.lng = null;
    }

    addRoutingControl(lat, lng) {
        if (this.currentRoutingControl) {
            this.removeExistingRouting();
        }
        this.lat = lat;
        this.lng = lng;

        this.currentRoutingControl = L.Routing.control({
            waypoints: [
                L.latLng(lat, lng),
                L.latLng(this.center[0], this.center[1])
            ],
            draggableWaypoints: false
        }).addTo(this.mapInstance);

        this.markerMaker.removeMarker(lat, lng);
        this.displayCloseRouting();
    }

    removeExistingRouting() {
        if (this.currentRoutingControl) {
            this.mapInstance.removeControl(this.currentRoutingControl);
            const routingContainer = document.querySelector('.leaflet-routing-container');
            if (routingContainer) {
                routingContainer.style.display = 'none';
            }
            this.currentRoutingControl = null;
            this.markerMaker.addRemovedMarkers(this.lat, this.lng);
        }
    }

    displayCloseRouting() {
        const closeRouting = document.querySelector('.closeRouting');
        if (this.currentRoutingControl) {
            closeRouting.style.display = 'block';

            closeRouting.addEventListener('click', () => {
                this.removeExistingRouting();
                closeRouting.style.display = 'none';
            });
        } else {
            closeRouting.style.display = 'none';
        }
    }
}

class MarkerMaker {
    constructor(mapInstance) {
        this.mapInstance = mapInstance;
        this.myIcon = L.icon({
            iconUrl: '/sajilo-rent/resources/marker.svg',
            iconSize: [30, 30]
        });
        this.markerList = [];
    }

    addMarkers(latlngData) {
        latlngData.forEach(location => {

            const content = `
                <div class="top-div" style = "background-image: url(${PATHS.house + location.image1})"></div>
                <div class="bottom-div">
                    <div class="left-div "style = "background-image: url(${PATHS.house + location.image2})"></div>
                    <div class="right-div "style = "background-image: url(${PATHS.house + location.image3})"></div>
                </div>
                <div class="infocontainer">
                    <div class="quickinfo">
                        <div class="housetype">Rooms: <span class="bold">${location.no_of_rooms}</span></div>
                        <div class="price">Price: <span class="bold">NRP ${location.price}</span></div>
                        <div class="contact">Owner: <span class="bold">${location.username}</span></div>
                    </div>
                    <div class="button">
                    <button class = "bookButton">
                        Book for rent
                    </button>
                        <button class="directionButton">
                            Directions
                        </button>
                    </div>  
                </div>
            `;
            const popup = L.popup({
                maxWidth: 'auto',
                minWidth: 0
            }).setContent(content);

            const marker = L.marker([parseFloat(location.latitude), parseFloat(location.longitude)], {
                icon: this.myIcon
            });
            marker.bindPopup(popup);
            marker.addTo(this.mapInstance);
            this.markerList.push(marker);
        })};
    

    addRemovedMarkers(removedLat, removedLng) {
        const removedMarker = this.markerList.find(marker => {
            const { lat: latToBeChecked, lng: LngToBeChecked } = marker.getLatLng();
            return removedLat == latToBeChecked && removedLng == LngToBeChecked;
        });
        if (removedMarker) {
            removedMarker.addTo(this.mapInstance);
        }
    }

    removeMarker(lat, lng) {
        const marker = this.markerList.find(element => {
            const { lat: markerLat, lng: markerLng } = element.getLatLng();
            return markerLat === lat && markerLng === lng;
        });
        if (marker) {
            this.mapInstance.removeLayer(marker);
        } else {
            console.warn(`Marker not found at coordinates: ${lat}, ${lng}`);
        }
    }
}

class SelectRanges {
    constructor(mapInstance, markerMaker) {
        this.mapInstance = mapInstance;
        this.markerMaker = markerMaker;
        this.removedMarkers = [];
        this.priceValue = Infinity; 
        this.roomValue = null;      
    }

    setPrice(value) {
        this.priceValue = value;
        this.applyFilters();
    }

    setRooms(value) {
        this.roomValue = value;
        this.applyFilters();
    }

    resetFilters() {

        this.removedMarkers.forEach(marker => {
            marker.addTo(this.mapInstance);
        });
        

        this.removedMarkers = [];
        

        this.priceValue = Infinity;
        this.roomValue = null;
        
    }

    applyFilters() {
        this.removedMarkers.forEach(marker => {
            marker.addTo(this.mapInstance);
        });
        this.removedMarkers = []; // Clear our tracking array
        
        const visibleMarkers = this.markerMaker.markerList.filter(marker => {
            const popupContent = marker.getPopup().getContent();
            
            // Extract price information
            const priceText = popupContent.match(/NRP\s+(\d+(?:,\d+)*)/);
            const price = priceText && priceText[1] ? parseInt(priceText[1].replace(/,/g, '')) : Infinity;
            
            // Extract room information
            const houseText = popupContent.match(/Rooms:\s*<span class="bold">(\d+)<\/span>/);
            const rooms = houseText && houseText[1] ? parseInt(houseText[1]) : 0;
            
            // Apply both filters
            const priceMatches = price <= this.priceValue;
            const roomMatches = this.roomValue === null || rooms === this.roomValue;
            
            return priceMatches && roomMatches;
        });
        
        // Remove markers that don't match both filters
        this.markerMaker.markerList.forEach(marker => {
            if(!visibleMarkers.includes(marker)) {
                this.mapInstance.removeLayer(marker);
                this.removedMarkers.push(marker);
            }
        });   
    }
}

export { Map, RoutingControl, MarkerMaker, SelectRanges };
