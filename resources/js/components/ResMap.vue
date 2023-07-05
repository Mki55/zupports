<template>
    
    <div class="container-fluid">
        <!-- Search Bar -->
        <div class="input-group mb-3">
    <input v-model="keyword" type="text" class="form-control" placeholder="Search for restaurants..." aria-label="Search for restaurants..." aria-describedby="button-addon2" @keyup.enter="searchRestaurants">
    <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="button" id="button-addon2" @click="searchRestaurants">Search</button>
    </div>
</div>

        <!-- Restaurant List and Map -->
        <div class="row">
            <!-- Restaurant List -->
            <div class="col-lg-4">
                <div class="restaurant-list p-3" style="overflow-y: auto; height: 500px; max-height: 50vh;">
                    <div v-for="restaurant in restaurants" :key="restaurant.id" 
                        class="card mb-3" 
                        :class="{ 'bg-info text-white': restaurant === selectedRestaurant }"  
                        @click="selectRestaurant(restaurant, $event)">

                        <div class="card-body">
                            <h5 class="card-title">{{ restaurant.name }}</h5>
                            <p class="card-text">{{ restaurant.address }}</p>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Map -->
            <div class="col-lg-8">
                <div id="map"></div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            keyword: 'Bang sue', // Default keyword
            restaurants: [],
            map: null,
            markers: [],
            selectedRestaurant: null,
            activeInfoWindow: null
        };
    },
    methods: {
        initMap() {
            this.map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: { lat: 13.736717, lng: 100.523186 }  // Centered at Bang Sue
            });
        },

        // This can delete infowindow but can't delete markers(Red pins).
       /* clearMarkers() {
    for (var i = 0; i < this.markers.length; i++) {
        google.maps.event.clearInstanceListeners(this.markers[i]); // remove event listeners
        if (this.markers[i].infowindow) {
            this.markers[i].infowindow.close();
            this.markers[i].infowindow = null;
        }
        this.markers[i].setMap(null);
    }
    // Now we clear the markers array
    this.markers = [];

}



,*/
searchRestaurants() {   
    this.initMap(); // Re-initialize the map instead of using clearMarkers function
    axios.get(`/restaurants/${this.keyword}`)
        .then(response => {
            this.restaurants = response.data;
            this.$nextTick(() => {
                this.addMarkers();
            });
        })
        .catch(error => {
            console.log(error);
        });
},

        addMarkers() {
            // This will be used to store the location of the first restaurant
            let firstRestaurantLocation = null;
            this.restaurants.forEach((restaurant, index) => {
                console.log(restaurant.geometry.location.lat, restaurant.geometry.location.lng);
                
                const marker = new google.maps.Marker({
                    position: {
                        lat: restaurant.geometry.location.lat,
                        lng: restaurant.geometry.location.lng
                    },
                    map: this.map
                });

                // Create an InfoWindow for this marker
                marker.infowindow = new google.maps.InfoWindow({
                    content: `<div><strong>${restaurant.name}</strong></div>`
                });

                // Add a click listener to this marker for its InfoWindow
                marker.addListener("click", () => {
                    if (this.activeInfoWindow) {
                        this.activeInfoWindow.close();
                    }
                    marker.infowindow.open({
                        anchor: marker,
                        map: this.map,
                        shouldFocus: false,
                    });

                    this.activeInfoWindow = marker.infowindow;
                    this.selectedRestaurant = restaurant;  // Set the selected restaurant when marker is clicked
                });

                // Save the location of the first restaurant
                if (index === 0) {
                    firstRestaurantLocation = marker.position;
                    this.activeInfoWindow = marker.infowindow;
                    this.activeInfoWindow.open({
                        anchor: marker,
                        map: this.map,
                        shouldFocus: false,
                    });
                    this.selectedRestaurant = restaurant;  // Set the selected restaurant to the first restaurant in the list
                }

                // Push the marker to our array of markers.                
                this.markers.push(marker);
                
                
            });
            
            // If there was at least one restaurant, move the map to its location
            if (firstRestaurantLocation) {
                this.map.setCenter(firstRestaurantLocation);
            }
            console.log('#')
        },
        selectRestaurant(restaurant, event) {
            const selectedMarker = this.markers.find(marker => marker.getPosition().lat() === restaurant.geometry.location.lat && marker.getPosition().lng() === restaurant.geometry.location.lng);
            if (selectedMarker) {
                // Center the map on the selected restaurant
                this.map.setCenter(selectedMarker.getPosition());

                // Open the InfoWindow for the selected marker
                if (this.activeInfoWindow) {
                    this.activeInfoWindow.close();
                }
                selectedMarker.infowindow.open({
                    anchor: selectedMarker,
                    map: this.map,
                    shouldFocus: false,
                });
                this.activeInfoWindow = selectedMarker.infowindow;
            }

            this.selectedRestaurant = restaurant;  // Set the selected restaurant
    
        }
    },
    mounted() {
        this.initMap();
        this.searchRestaurants();
    }
};
</script>
