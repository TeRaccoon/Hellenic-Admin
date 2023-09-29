function handlePage() {
    console.log("click");
    document.getElementById('sidenav-toggle').addEventListener('click', function() {
        var sidebar = document.querySelector('.sidenav');
        if (sidebar.style.display === 'block') {
            sidebar.style.display = 'none';
        } else {
            sidebar.style.display = 'block';
        }
    });
}