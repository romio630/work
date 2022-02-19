$.fn.clickToggle = function(a, b) {
    return this.each(function() {
        var clicked = false;
        $(this).on('click', function() {
            clicked = !clicked;
            if (clicked) {
                return a.apply(this, arguments);
            }
            return b.apply(this, arguments);
        });
    });
};