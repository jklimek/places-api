/**
 * Created by klemens on 07/08/16.
 */


$("#geoLocationButton").click(function () {
    var geolocationIcon = $("#geoLocationIcon");
    geolocationIcon.removeClass("fa-location-arrow");
    geolocationIcon.addClass("fa-spinner fa-pulse");
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
    var geolocationIcon = $("#geoLocationIcon");
    geolocationIcon.addClass("fa-location-arrow");
    geolocationIcon.removeClass("fa-spinner fa-pulse");
    geolocationIcon.addClass("geo-active");
    //$("#locationTest").text("Latitude: " + position.coords.latitude + ", Longitude: " + position.coords.longitude);
    console.log("Latitude: " + position.coords.latitude + ", Longitude: " + position.coords.longitude);
    $("input[name=location]").val(position.coords.latitude + "," + position.coords.longitude);
    //$("input[name=saddr]").val(position.coords.latitude + "," + position.coords.longitude);
}

function displayError(error) {
    var geolocationIcon = $("#geoLocationIcon");
    geolocationIcon.addClass("fa-location-arrow");
    geolocationIcon.removeClass("fa-spinner fa-pulse");
    var errors = {
        1: 'Permission denied',
        2: 'Position unavailable',
        3: 'Request timeout'
    };
    //$("#locationTest").text("Error: " + errors[error.code]);
    //console.log("Error: " + errors[error.code]);
    alert("Error: " + errors[error.code]);
}

function toggleIdElement(id) {
    var element = $('#' + id);
    if (element.css("display") == "none") {
        element.removeClass('hidden-xs');
    } else {
        element.addClass('hidden-xs');
    }
}