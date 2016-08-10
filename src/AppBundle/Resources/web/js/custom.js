/**
 * Created by klemens on 07/08/16.
 */


$("#geoLocationButton").click(function () {
    $("#geoLocationIcon").removeClass("fa-location-arrow");
    $("#geoLocationIcon").addClass("fa-spinner fa-pulse");
    getLocation();
});

function getLocation() {
    if (navigator.geolocation) {
        var timeoutVal = 10 * 1000 * 1000;
        navigator.geolocation.getCurrentPosition(
            displayPosition,
            displayError,
            {enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 0}
        );
    }
    else {
        alert("Geolocation is not supported by this browser");
    }
}

function displayPosition(position) {
    $("#geoLocationIcon").addClass("fa-location-arrow");
    $("#geoLocationIcon").removeClass("fa-spinner fa-pulse");
    $("#locationTest").text("Latitude: " + position.coords.latitude + ", Longitude: " + position.coords.longitude);
    $("input[name=location]").val(position.coords.latitude + "," + position.coords.longitude);
}

function displayError(error) {
    $("#geoLocationIcon").addClass("fa-location-arrow");
    $("#geoLocationIcon").removeClass("fa-spinner fa-pulse");
    var errors = {
        1: 'Permission denied',
        2: 'Position unavailable',
        3: 'Request timeout'
    };
    $("#locationTest").text("Error: " + errors[error.code]);
}