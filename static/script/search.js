function toggleSearchBar() {
    var searchBar = document.getElementById("search-bar");
    searchBar.style.display = (searchBar.style.display === "flex") ? "none" : "flex";
    if (searchBar.style.display === "flex") {
        document.getElementById("search-input").focus();
    }
}

function hideSearchBar() {
    document.getElementById("search-bar").style.display = "none";
}
