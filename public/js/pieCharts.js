
$.getJSON('/lead/summary-by-lead-source', function(result) {
    summaryByLeadSourceData = _.map(result, function(leadSourceDataPoint) {
        return {
            value: leadSourceDataPoint.lead_count,
            color: 'hsl(' + (180 * leadSourceDataPoint.lead_count/ result.length) + ', 100%, 50%)',
            label: leadSourceDataPoint.description,
        };
    });
    var byLeadSourceContext = $("#pie-by-lead-source").get(0).getContext("2d");
    var byLeadSourceChart = new Chart(byLeadSourceContext).Pie(summaryByLeadSourceData);
});

$.getJSON('/lead/summary-by-city', function(result) {
    summaryByCityData = _.map(result, function(cityDataPoint) {
        return {
            value: cityDataPoint.lead_count,
            color: 'hsl(' + (180 * cityDataPoint.lead_count/ result.length) + ', 100%, 50%)',
            label: cityDataPoint.city
        };
    });
    var byCityContext = $("#pie-by-city").get(0).getContext("2d");
    var byCityChart = new Chart(byCityContext).Pie(summaryByCityData);
});
