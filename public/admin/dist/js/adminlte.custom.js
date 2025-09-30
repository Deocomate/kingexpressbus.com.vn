$(function () {
    let url = window.location.href;

    $('ul.nav-sidebar a').filter(function () {
        return this.href == url;
    }).addClass('active');

    $('ul.nav-treeview a').filter(function () {
        return url.includes(this.href);
    })
        .parentsUntil(".nav-sidebar > .nav-treeview")
        .addClass('menu-open')
        .prev('a')
        .addClass('active');

    $('ul.nav-treeview a').filter(function () {
        return url.includes(this.href);
    }).addClass('active')
});
