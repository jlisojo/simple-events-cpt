(function () {
    var toggle = document.querySelector('.se-past__toggle');
    var grid = document.getElementById('se-past-grid');

    if (!toggle || !grid) {
        return;
    }

    toggle.addEventListener('click', function () {
        var expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        if (expanded) {
            grid.setAttribute('hidden', '');
        } else {
            grid.removeAttribute('hidden');
        }
    });
})();
