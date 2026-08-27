<?php
// stores.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Our Stores – Kit Group";
$page_desc = "Visit our stores in Gaborone and Jwaneng for all your PPE and workwear needs. Find store addresses, contact information, and opening hours.";

$breadcrumb_items = [
    ['label' => 'Home', 'url' => '/kitgroup/'],
    ['label' => 'Stores']
];

// Store data (you can move this to a database table later)
$stores = [
    [
        'id' => 1,
        'name' => 'Block 3 Store',
        'address' => 'Plot 123, Block 3, Gaborone, Botswana',
        'phone' => '+267 31 234 567',
        'email' => 'info@kitgroup.co.bw',
        'manager' => 'Mr. Thabo Molefe',
        'hours' => 'Mon–Fri: 8:00am – 5:00pm',
        'lat' => -24.6282,
        'lng' => 25.9231,
        'description' => 'Our flagship store in the heart of Gaborone. Fully stocked with the complete Kit Group range.'
    ],
    [
        'id' => 2,
        'name' => 'Commerce Park Store',
        'address' => 'Unit 5, Commerce Park, Gaborone, Botswana',
        'phone' => '+267 31 234 568',
        'email' => 'info@kitgroup.co.bw',
        'manager' => 'Ms. Kelebogile Ntswe',
        'hours' => 'Mon–Fri: 8:00am – 5:00pm',
        'lat' => -24.6482,
        'lng' => 25.9111,
        'description' => 'Conveniently located in Commerce Park, offering easy access for businesses.'
    ],
    [
        'id' => 3,
        'name' => 'Jwaneng Mall Store',
        'address' => 'Shop 12, Jwaneng Mall, Jwaneng, Botswana',
        'phone' => '+267 31 234 569',
        'email' => 'info@kitgroup.co.bw',
        'manager' => 'Mr. Olebile Dikolobe',
        'hours' => 'Mon–Sat: 8:00am – 6:00pm',
        'lat' => -24.5833,
        'lng' => 24.6000,
        'description' => 'Serving the mining community with a full range of PPE and workwear solutions.'
    ]
];

include 'templates/header.php';
?>

<!-- ============================================================
     STORES PAGE HERO
     ============================================================ -->
<section class="stores-hero py-5" style="
    background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 100%);
    color: #ffffff;
    border-bottom: 3px solid #e63946;
">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Our Stores</h1>
                <p class="lead mb-0" style="opacity: 0.9;">
                    Visit us at one of our three convenient locations across Botswana.
                </p>
            </div>
            <div class="col-lg-4 mt-3 mt-lg-0 text-end">
                <span class="badge bg-danger px-4 py-2" style="font-size: 0.9rem; letter-spacing: 2px; text-transform: uppercase; background: #e63946 !important;">
                    <i class="bi bi-geo-alt me-2"></i> 3 Locations
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     STORES WITH MAP
     ============================================================ -->
<section class="py-4">
    <div class="container-fluid px-4">
        <div class="row g-0" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); min-height: 600px;">
            
            <!-- ====== LEFT: STORE LIST ====== -->
            <div class="col-lg-4 bg-white p-4" style="max-height: 600px; overflow-y: auto;">
                <h5 class="fw-bold mb-3" style="color: #1a1a2e;">
                    <i class="bi bi-shop me-2" style="color: #e63946;"></i> Our Locations
                </h5>
                <div class="list-group list-group-flush" id="storeList">
                    <?php foreach ($stores as $index => $store): ?>
                        <a href="#" class="list-group-item list-group-item-action store-item <?= $index === 0 ? 'active' : '' ?>" 
                           data-store-id="<?= $store['id'] ?>"
                           data-lat="<?= $store['lat'] ?>"
                           data-lng="<?= $store['lng'] ?>"
                           style="border: none; padding: 1rem 0.75rem; border-radius: 8px; <?= $index === 0 ? 'background: rgba(230, 57, 70, 0.08); border-left: 3px solid #e63946;' : '' ?>">
                            <div class="d-flex align-items-start gap-3">
                                <div style="background: <?= $index === 0 ? '#e63946' : '#f8f9fa' ?>; color: <?= $index === 0 ? '#fff' : '#1a1a2e' ?>; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; flex-shrink: 0;">
                                    <?= $store['id'] ?>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: <?= $index === 0 ? '#e63946' : '#1a1a2e' ?>;">
                                        <?= htmlspecialchars($store['name']) ?>
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($store['address']) ?>
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($store['phone']) ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- ====== RIGHT: GOOGLE MAP ====== -->
            <div class="col-lg-8">
                <div id="map" style="width: 100%; height: 600px; background: #e9ecef;">
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <i class="bi bi-geo-alt" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #dee2e6;"></i>
                            <p>Loading map...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     GOOGLE MAPS JAVASCRIPT
     ============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store data from PHP
    const stores = <?= json_encode($stores) ?>;
    
    // ============================================================
    // LOAD GOOGLE MAPS API
    // ============================================================
    function initMap() {
        // Default center (Gaborone, Botswana)
        const defaultCenter = { lat: -24.6282, lng: 25.9231 };
        
        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: defaultCenter,
            mapTypeId: 'roadmap',
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });
        
        // Add markers for each store
        const markers = [];
        const infowindows = [];
        
        stores.forEach((store, index) => {
            const position = { lat: store.lat, lng: store.lng };
            
            // Create marker
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: store.name,
                label: {
                    text: (index + 1).toString(),
                    color: '#ffffff',
                    fontSize: '12px',
                    fontWeight: 'bold'
                },
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(40, 40)
                }
            });
            
            markers.push(marker);
            
            // Create infowindow
            const infowindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 8px; max-width: 250px;">
                        <h6 style="font-weight: 700; margin-bottom: 4px; color: #1a1a2e;">${store.name}</h6>
                        <p style="font-size: 0.85rem; margin-bottom: 4px; color: #6c757d;">
                            <i class="bi bi-geo-alt"></i> ${store.address}
                        </p>
                        <p style="font-size: 0.85rem; margin-bottom: 4px; color: #6c757d;">
                            <i class="bi bi-telephone"></i> ${store.phone}
                        </p>
                        <a href="/kitgroup/stores#store-${store.id}" style="color: #e63946; text-decoration: none; font-weight: 600; font-size: 0.85rem;">
                            View Details →
                        </a>
                    </div>
                `
            });
            
            infowindows.push(infowindow);
            
            // Click on marker shows infowindow and highlights store in list
            marker.addListener('click', function() {
                // Close all infowindows
                infowindows.forEach(iw => iw.close());
                // Open this one
                infowindow.open(map, marker);
                // Highlight store in list
                highlightStore(store.id);
            });
            
            // Also open infowindow when store is clicked from list
            const storeItem = document.querySelector(`.store-item[data-store-id="${store.id}"]`);
            if (storeItem) {
                storeItem.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Close all infowindows
                    infowindows.forEach(iw => iw.close());
                    // Open this one
                    infowindow.open(map, marker);
                    // Highlight this marker (bounce animation)
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    setTimeout(() => {
                        marker.setAnimation(null);
                    }, 1000);
                });
            }
        });
        
        // Fit bounds to show all markers
        if (stores.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            markers.forEach(marker => bounds.extend(marker.getPosition()));
            map.fitBounds(bounds);
            // Zoom out slightly if only one marker
            if (stores.length === 1) {
                map.setZoom(14);
            }
        }
    }
    
    // ============================================================
    // HIGHLIGHT STORE IN LIST AND DETAILS
    // ============================================================
    function highlightStore(storeId) {
        // Update store list
        document.querySelectorAll('.store-item').forEach(item => {
            item.classList.remove('active');
            item.style.background = 'transparent';
            item.style.borderLeft = 'none';
            item.style.borderLeftColor = 'transparent';
            const id = item.dataset.storeId;
            if (id == storeId) {
                item.classList.add('active');
                item.style.background = 'rgba(230, 57, 70, 0.08)';
                item.style.borderLeft = '3px solid #e63946';
            }
        });
        
        // Update store details
        document.querySelectorAll('.store-detail').forEach(detail => {
            detail.classList.add('d-none');
            detail.classList.remove('show');
            const id = detail.dataset.storeId;
            if (id == storeId) {
                detail.classList.remove('d-none');
                detail.classList.add('show');
            }
        });
    }
    
    // ============================================================
    // INITIALIZE MAP – ON PAGE LOAD
    // ============================================================
    // Check if Google Maps API is loaded
    if (typeof google !== 'undefined' && google.maps) {
        initMap();
    } else {
        // If not loaded, the API callback will handle it
        window.initMap = initMap;
    }
});
</script>

<!-- Load Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY_HERE&callback=initMap" async defer></script>

<?php include 'templates/footer.php'; ?>