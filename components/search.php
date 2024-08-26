<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch"><i class="icon_close"></i></div>
        <form class="search-model-form" onsubmit="return redirectToSearch()">
            <input type="text" id="search-input" placeholder="Search here.....">
        </form>
    </div>
</div>

<script>
    function redirectToSearch(){const e=document.getElementById("search-input").value;if(e){const n=`http://localhost/anime/all-shows.php?query=${encodeURIComponent(e)}`;window.location.href=n}return!1}
</script>