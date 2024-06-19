function sortGroupsByTitle(groups) {
    return groups.sort(function(a, b) {
        // Assuming 'en' titles and that each group has at least one label
        var titleA = a.labels[0].title.toUpperCase(); // to ensure case-insensitive comparison
        var titleB = b.labels[0].title.toUpperCase(); // to ensure case-insensitive comparison

        if (titleA < titleB) {
            return -1;
        }
        if (titleA > titleB) {
            return 1;
        }

        // Titles must be equal
        return 0;
    });
}

function generateGroupHTML(group) {
    console.log(group.handle);
    
    if (group.handle) {
      warning = false; // Code to execute if the condition is true
      warning_label = false;
      }
    else {
      warning = "list-group-item-warning "; // Code to execute if the condition is false
      warning_label = " title = 'No handle has been assigned to this group yet.' ";
      }
    var html = '<div class="list-group-item ' + warning + 'list-group-item-action flex-column align-items-start"' +warning_label +'>';
    html += '<div class="d-flex w-100 justify-content-between align-items-center">';
    html += '<h5 class="mb-0">' + group.label + '</h5>';
    html += '<div>';

    // Info
    html += '<a href="' + group.links.view[0] + '" class="text-decoration-none me-2">';
    html += '<i class="fas ' + options.view.icon + ' lead" title="View Group Diagram" style="color: ' + options.view.colour + ';"></i></a>';
    
    html += '&nbsp;-&nbsp;&nbsp;';
    
    // Info
    html += '<a href="' + group.links.info[0] + '" class="text-decoration-none me-2">';
    html += '<i class="fas ' + options.info.icon + ' lead" title="Group Information" style="color: ' + options.info.colour + ';"></i></a>';
    
    html += '&nbsp;-&nbsp;&nbsp;';
    
    // Default
    html += '<a href="' + group.links.default[0] + '" class="text-decoration-none me-2">';
    html += '<i class="fas ' + options.default.icon + ' lead" title="Default" style="color: ' + options.default.colour + ';"></i></a>';

    // Simple    
    html += '<a href="' + group.links.simple[0] + '" class="text-decoration-none me-2">';
    html += '<i class="fas ' + options.simple.icon + ' lead" title="Simple" style="color: ' + options.simple.colour + ';"></i></a>';

    // Full
    html += '<a href="' + group.links.full[0] + '" class="text-decoration-none me-2">';
    html += '<i class="fas ' + options.full.icon + ' lead" title="Full" style="color: ' + options.full.colour + ';"></i></a>';

    html += '&nbsp;-&nbsp;&nbsp;';
    
    html += '<a href="' + group.links.default[1] + '" class="text-decoration-none me-2">';
    html += '<i class="fas ' + options.refresh.icon + ' lead" title="Refresh and display Default" style="color: ' + options.default.colour + ';"></i></a>';
    
    html += '<a href="' + group.links.simple[1] + '" class="text-decoration-none me-2">';
    html += '<i class="fas ' + options.refresh.icon + ' lead" title="Refresh and display Simple" style="color: ' + options.simple.colour + ';"></i></a>';
    
    html += '<a href="' + group.links.full[1] + '" class="text-decoration-none me-2">';
    html += '<i class="fas ' + options.refresh.icon + ' lead" title="Refresh and display Full" style="color: ' + options.full.colour + ';"></i></a>';

    html += '</div></div></div>';

    return html;
    }
