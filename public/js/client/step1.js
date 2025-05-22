$(document).ready(function() {
    $('.country-select').select2({
        templateResult: formatCountry,
        templateSelection: formatCountry
    });

    function formatCountry(country) {
        if (!country.id) return country.text;
        return $('<span><img src="https://flagcdn.com/w40/' + country.id.toLowerCase() + '.png" class="flag-icon" /> ' + country.text + '</span>');
    }
});

